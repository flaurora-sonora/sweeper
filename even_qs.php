<?php

$eq = new even_qs_class();
$source = "not-swept";
$target = "swept";
$eq->even_qs($source, $target);
file_put_contents("report.txt", $eq->get_report_contents());

class even_qs_class {

function __contruct() {
	$this->report_contents = "";
}

function get_report_contents() {
	return $this->report_contents;
}

function even_qs($source, $target) {
	if(is_dir($source)) {
		@mkdir($target);
		$d = dir($source);
		while(FALSE !== ($entry = $d->read())) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			$Entry = $source . '/' . $entry;           
			if(is_dir($Entry)) {
				if($entry == '_notes') {
					// don't copy it
					//var_dump($entry);
				} else {
					even_qs_class::even_qs($Entry, $target . '/' . $entry);
				}
				continue;
			} else {
				if($entry == 'thumbs.db') {
					
				} else {
					if(strpos($Entry, ".html") || strpos($Entry, ".htm") || strpos($Entry, ".asp") || strpos($Entry, ".xml") || strpos($Entry, ".php")) {
						$code = file_get_contents($Entry);
						$opens = substr_count($code, "<q");
						$closes = substr_count($code, "</q>");
						if($opens !== $closes) {
							//print("crap!");
							//print($Entry);
							$this->report_contents .= $Entry . '
';
							var_dump($this->report_contents);
							//exit(0);
						}
						//copy($Entry, $target . '/' . $entry);
					}
				}
			}
		}
		$d->close();
	}/* else {
		copy($source, $target);
	}*/
}

}

?>