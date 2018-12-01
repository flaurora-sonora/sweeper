<?php

class DTD {

	public function setDTDfile() {
		$config = ReTidy::getConfig();
		$code = ReTidy::getCode();
		if(strpos($code, '<!DOCTYPE html>') !== false) { // HTML5
			$this->DTDfile = 'DTD' . DIRECTORY_SEPARATOR . 'html5.dtd';
		} elseif($config['use_local_DTD']) {
			if(isset($config['local_DTD'])) {
				$this->DTDfile = $config['local_DTD'];
			} else { // default
				$this->DTDfile = 'DTD' . DIRECTORY_SEPARATOR . 'xhtml1-strict.dtd';
			}
		} else { // default is to take take the declared DTD of the page (although it may need to be downloaded in this case).
			preg_match('/(<!DOCTYPE\s*html\s*PUBLIC\s*"[^"]*"\s*")([^"]*)(">)/is', $code, $matches);
			$this->DTDfile = $matches[2];
			print('<strong style="color:red;">DTD downloaded!</strong><br>');
		}
	}

	public function getDTDfile() {
		if(!$this->DTDfile) {
			DTD::setDTDfile();
		}
		return $this->DTDfile;
	}
	
	public function setDTDcode() {
		if(!$this->DTDfile) {
			DTD::setDTDfile();
		}
		$this->DTDcode = file_get_contents($this->DTDfile);
	}
	
	public function getDTDcode() {
		if(!$this->DTDcode) {
			DTD::setDTDcode();
		}
		return $this->DTDcode;
	}	
	
	public function getEntity($entity_name) {
		if(!$this->DTDcode) {
			$this->DTDcode = DTD::getDTDcode();
		}
		if(preg_match('/<!ENTITY\s+%\s*' . $entity_name . '\s+"([^"]*)"\s*>/is', $this->DTDcode, $entity_matches)) {
			$entity = $entity_matches[1];
			if(strpos($entity, "%") !== false) {
				$entity = DTD::expandEntity($entity);
			}
			return DTD::cleanEntity($entity);
		}
		return false;
	}
	
	public function getAttlist($element_name) {
		if(!$this->DTDcode) {
			$this->DTDcode = DTD::getDTDcode();
		}
		if(ReTidy::isNode($element_name)) {
			$element_name = $element_name->nodeName;
		}
		return DTD::getAttributesForElement($element_name);
	}	
	
	public function getEntities($filename) {
		if(!is_file($filename)) {
			print('<h1 style="color:red;">KABOOM!!!</h1>');exit(0);
		}
		$file_contents = file_get_contents($filename);
		$array_entities = array();
		if(preg_match_all('/<!ENTITY\s*([^\s]*)\s*"([^"]*)"\s*>/is', $file_contents, $entity_matches)) {
			foreach($entity_matches[0] as $index => $value) {
				if(strpos($entity_matches[2][$index], "%") !== false) {
					$entity_matches[2][$index] = DTD::expandEntity($match[2]);
				}
				$array_entities[] = array($entity_matches[1][$index], $entity_matches[2][$index]);
			}
			return $array_entities;
		}
		return false;
	}	
	
	public function getBlock() {
		$block = DTD::getEntity("block");
		if($block === false) { // probably HTML5, then use a standard one
			return "";
		} else {
			return $block;
		}
	}
	
	public function getBlockXPath() {
		$blockString = DTD::getBlock();
		$blockArray = explode("|", $blockString);
		foreach($blockArray as $index => $value) {
			$blockArray[$index] = '//' . ReTidy::get_html_namespace() . $value;
		}
		$blockString = implode("|", $blockArray);
		return $blockString;
	}
	
	public function getInline() {
		return DTD::getEntity("inline");
	}

	public function getInlineXPath() {
		$inlineString = DTD::getInline();
		$inlineArray = explode("|", $inlineString);
		foreach($inlineArray as $index => $value) {
			$inlineArray[$index] = '//' . ReTidy::get_html_namespace(). $value;
		}
		$inlineString = implode("|", $inlineArray);
		return $inlineString;
	}	

	public function getAttributesForElement($element_name) {
		return DTD::getAttributesForElementByType($element_name);
	}
	
	public function getAttributesForElementByType($element_name, $attribute_type = false) {
		if(!$this->DTDcode) {
			$this->DTDcode = DTD::getDTDcode();
		}
		$array_attributes = array();
		if(preg_match('/<!ATTLIST\s+' . $element_name . '\s+([^<>]*)\s*>/is', $this->DTDcode, $attlist_matches)) {
			$attlist = $attlist_matches[1];
			if(strpos($attlist, "%") !== false) {
				$attlist = trim(DTD::expandEntity($attlist));
			}
			preg_match_all('/([^\s]*)\s*([^\s]*)\s*(#IMPLIED|#REQUIRED|#VALUE\s+"[^"]*")/is', $attlist, $att_list_item_matches);
			if($attribute_type === false) {
				foreach($att_list_item_matches[1] as $index => $attribute) {
					if(strlen($attribute) > 0) {
						$array_attributes[] = $attribute;
					}
				}
			} else {
				foreach($att_list_item_matches[1] as $index => $attribute) {
					if(strlen($attribute) > 0 && $att_list_item_matches[3][$index] === $attribute_type) {
						$array_attributes[] = $attribute;
					}
				}
			}
			return $array_attributes;
		}
		return false;
	}	
	
	public function getElementsByContent($content) {
		if(!$this->DTDcode) {
			$this->DTDcode = DTD::getDTDcode();
		}
		if(preg_match_all('/<!ELEMENT\s*([^\s]*)\s*' . $content . '\s*>/is', $this->DTDcode, $element_matches)) {
			return $element_matches[1];
		}
		return false;
	}
	
	public function getAllElements() {
		// this should create the same sort of output as getBlock and getInline, although a smarter function for HTML5 is also needed
		if(!$this->DTDcode) {
			$this->DTDcode = DTD::getDTDcode();
		}
		if(preg_match_all('/<!ELEMENT\s*([^\s]*)\s*([^>]*)\s*>/is', $this->DTDcode, $element_matches)) {
			return implode('|', $element_matches[1]);
		}
		return false;
	}	
	
	public function expandEntity($entity) {
		preg_match_all('/%\s*([^%;]*);/is', $entity, $entity_matches2);
		foreach($entity_matches2[0] as $index2 => $entity2) {
			$entity = str_replace($entity2, DTD::getEntity($entity_matches2[1][$index2]), $entity);
		}
		return $entity;
	}
	
	public function cleanEntity($entity) {
		$entity = preg_replace('/\s*\|\s*/is', '|', $entity);
		return $entity;
	}	

}

?>