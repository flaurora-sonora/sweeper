<?php

class OM {

	public function getMediaRule($string, $opening_string, $closing_string, $offset) {
		$media_rule_length = 0;
		$depth = 0;
		if(($first_opening_position = strpos($string, $opening_string, $offset)) !== false) {
			$media_rule_length += $first_opening_position;
			$working_string = substr($string, $first_opening_position + 1);
			$depth++;
			while($depth > 0) {
				$position_of_opening_string = strpos($working_string, $opening_string);
				$position_of_closing_string = strpos($working_string, $closing_string);
				if($position_of_opening_string === false) {
					$media_rule_length += $position_of_closing_string + 1;
					$working_string = substr($working_string, $position_of_closing_string + 1);
					$depth--;
					// pick a sufficiently large number to approximate infinity for our purposes
					//$position_of_opening_string = 1048576; // 2^20 (I think)
				} elseif($position_of_opening_string < $position_of_closing_string) {
					$media_rule_length += $position_of_opening_string + 1;
					$working_string = substr($working_string, $position_of_opening_string + 1);
					$depth++;
				} elseif($position_of_opening_string > $position_of_closing_string) {
					$media_rule_length += $position_of_closing_string + 1;
					$working_string = substr($working_string, $position_of_closing_string + 1);
					$depth--;
				} else {
					print("<h1 style=\"color:red;\">should never get here 0984530495</h1>");
				}
			}
			$length_closing_string = strlen($closing_string);
			$string = substr($string, 0, $media_rule_length + $length_closing_string);
		} else {
			return false;
		}
		return $string;
	}
	
	public function reverseGetOString($string, $opening_string, $closing_string, $offset = 0) {
		$offset = $offset + strlen($closing_string) - 1;
		return OM::getOString(strrev($string), strrev($closing_string), strrev($opening_string), strlen($string) - $offset - 1);
	}
	
	public function getOString($string, $opening_string, $closing_string, $offset = 0) {
		// notice that this does not really handle the HTML short-hand of self-closing tags. (2011-08-10; see getAllTags)
		//print('$string, $opening_string, $closing_string, $offset: ');var_dump($string, $opening_string, $closing_string, $offset);
		$first_opening_string_pos = strpos($string, $opening_string, $offset);
		if($first_opening_string_pos === false) {
			return false;
		}
		$offset = $first_opening_string_pos + strlen($opening_string);
		$object_string = $opening_string;
		$depth = 1;
		while($offset < strlen($string) && $depth > 0) {
			if(substr($string, $offset, strlen($opening_string)) === $opening_string) {
				$depth++;
				$object_string .= $opening_string;
				$offset += strlen($opening_string);
			} elseif(substr($string, $offset, strlen($closing_string)) === $closing_string) {
				$depth--;
				$object_string .= $closing_string;
				$offset += strlen($closing_string);
			} else {
				$object_string .= $string[$offset];
				$offset++;
			}
		}
		return $object_string;
	}
	
	public function getOStringContents($string, $opening_string, $closing_string, $offset = 0) {
		// notice that this does not really handle the HTML short-hand of self-closing tags. (2011-08-10; see getAllTags)
		//print('$string, $opening_string, $closing_string, $offset: ');var_dump($string, $opening_string, $closing_string, $offset);
		$first_opening_string_pos = strpos($string, $opening_string, $offset);
		if($first_opening_string_pos === false) {
			return false;
		}
		$offset = $first_opening_string_pos + strlen($opening_string);
		$object_string = '';
		$depth = 1;
		while($offset < strlen($string) && $depth > 0) {
			if(substr($string, $offset, strlen($opening_string)) === $opening_string) {
				$depth++;
				$object_string .= $opening_string;
				$offset += strlen($opening_string);
			} elseif(substr($string, $offset, strlen($closing_string)) === $closing_string) {
				$depth--;
				$object_string .= $closing_string;
				$offset += strlen($closing_string);
			} else {
				$object_string .= $string[$offset];
				$offset++;
			}
		}
		return substr($object_string, 0, strlen($object_string) - strlen($closing_string));
	}
	
	public function getParentTag($string, $offset) {
		return OM::getContainingTag($string, $offset);
	}
	
	public function getContainingTag($string, $offset) {
		$substr = substr($string, 0, $offset);
		preg_match('/[ >](\w+)</is', strrev($substr), $open_matches, PREG_OFFSET_CAPTURE);
		$tag = strrev($open_matches[1][0]);
		$tag_offset = $open_matches[0][1] + strlen($tag) + 2;
		$tagString = OM::getOString(substr($string, $offset - $tag_offset), '<' . $tag, '</' . $tag . '>');
		return $tagString;
	}
	
	public function getContainingBlock($string, $offset, $blocks = false) {
		return OM::getContainingBlockString($string, $offset, $blocks);
	}	
	
	public function getContainingBlockString($string, $offset, $blocks = false) {
		//print('here0305-<br>');
		if($blocks === false) {
			//print('here0306-<br>');
			$blocks = DTD::getBlock();
		}
		//print('blocks: ');var_dump($blocks);
		foreach(explode("|", $blocks) as $index => $value) {
			$rev_blocks .= "|" . strrev($value);
		}
		$rev_blocks = substr($rev_blocks, 1);
		// dummy initialization for the while loop
		$blockString = "XXOXX";
		$offset_contained_string_in_block = 7;
		$failed_offset_contained_string_in_block = -1;
		while($offset_contained_string_in_block >= strlen($blockString)) {
			$substr = substr($string, 0, $offset - $failed_offset_contained_string_in_block + 1);
			preg_match('/[ >](' . $rev_blocks . ')</is', strrev($substr), $open_matches, PREG_OFFSET_CAPTURE);
			$block = strrev($open_matches[1][0]);
			$block_offset = $open_matches[0][1] + strlen($block) + 2;
			// we may be off by 2 somewhere and it may not matter (because tag openings have at least two characters).
			$containing_block_offset = $offset - $failed_offset_contained_string_in_block - $block_offset + 1; // offset of the block in the string
			$blockString = OM::getOString(substr($string, $containing_block_offset), '<' . $block, '</' . $block . '>');
			if($blockString === false) {
				break;
			}
			$offset_contained_string_in_block = $offset - $containing_block_offset; // offset of the contained string in the the block
			$failed_offset_contained_string_in_block = $offset_contained_string_in_block;
		}
		if($blockString !== false) {
			return array($blockString, $offset_contained_string_in_block, $containing_block_offset);
		} else {
			return false;
		}
	}
	
	public function getContainingBlockForCombineInline($string, $offset, $blocks = false) {
		$blockString = false;
		if($blocks === false) {
			$blocks = DTD::getBlock();
		}
		$blocksArray = explode("|", $blocks);
		foreach($blocksArray as $index => $value) {
			$rev_blocks .= "|" . strrev($value);
		}
		$rev_blocks = substr($rev_blocks, 1);
		$substr = substr($string, 0, $offset);
		preg_match('/>([^>]*)(' . $rev_blocks . ')(\/)?</is', strrev($substr), $open_matches, PREG_OFFSET_CAPTURE);
		$block_offset = $open_matches[0][1];
		$containing_block_offset = $offset - $block_offset; // offset of the block in the string
		preg_match('/<(\/)?(' . $blocks . ')[ >]/is', substr($string, $containing_block_offset), $end_of_block_matches, PREG_OFFSET_CAPTURE);
		$blockString = substr($string, $containing_block_offset, $end_of_block_matches[0][1]);
		$offset_contained_string_in_block = $offset - $containing_block_offset; // offset of the contained string in the the block
		if($blockString !== false) {
			return array($blockString, $offset_contained_string_in_block, $containing_block_offset);
		} else {
			return false;
		}
	}
	
	public function getOffsetWithinBlock($string, $substr, $offset) {
		$strlen = strlen($substr);
		$pos = 0;
		while($pos < $strlen) {
			$pos2 = 0;
			while($string[$offset + $pos2] === $substr[$pos + $pos2]) {
				if($pos + $pos2 + 1 === $strlen) {
					return $pos;
				}
				$pos2++;
			}
			$pos++;
		}
		return false;
	}	
	
	public function getAllTagParts($string) {
		$tag_parts = array();
		//print('$string: ');var_dump($string);
		$tagPieces = OM::getAllTagPieces($string);
		//print('$tagPieces: ');var_dump($tagPieces);
		// if the first tag is not at offset 0, then add the pre tag stuff
		$first_offset = $tagPieces[0][1];
		if($first_offset !== 0) {
			$tag_parts[] = array(substr($string, 0, $first_offset), 0, 0);
		}
		$size_minus_one = sizeof($tagPieces) - 1;
		foreach($tagPieces as $index => $value) {
			$piece = $value[0];
			$offset = $value[1];
			$tag_parts[] = array($piece, $offset, 1);
			$next_part_offset = $offset + strlen($piece);
			$next_piece_offset = $tagPieces[$index + 1][1];
			if($next_piece_offset !== $next_part_offset) { // then it is not a tag piece
				if($index === $size_minus_one) {
					if(substr($string, $next_part_offset) !== false) {
						$tag_parts[] = array(substr($string, $next_part_offset), $next_part_offset, 0);
					}
				} else {
					$tag_parts[] = array(substr($string, $next_part_offset, $next_piece_offset - $next_part_offset), $next_part_offset, 0);
				}
			}
		}
		return $tag_parts;
	}
	
	public function getLastOfDepth($depth, $tempTagParts) {
		//print('$tempTagParts: ');var_dump($tempTagParts);
		foreach(array_reverse($tempTagParts) as $index => $value) {
			// we only want tags; not text or self-closing tags
			if($value[5] === 0 || $value[5] === 3 || ReTidy::is_self_closing_for_tidyer(ReTidy::tag_name_from_tag_string($value[0]))) {
				continue;
			}
			if($value[3] === $depth) {
				return $value;
			}
		}
		return false;
	}
	
	public function getLastOfName($name, $tempTagParts) {
		foreach(array_reverse($tempTagParts) as $index => $value) {
			if($value[4] === $name) {
				return $value;
			}
		}
		return false;
	}

	public function getStringFromPartsArray($partsArray, $begin_index = 0, $end_index = false) {
		$string = '';
		if($end_index === false) {
			$end_index = sizeof($partsArray) - 1;
		}
		while($begin_index <= $end_index) {
			$string .= $partsArray[$begin_index][0];
			$begin_index++;
		}
		return $string;
	}
	
	public function is_properly_nested($var) {
		if(is_string($var)) {
			$tagParts = OM::getAllTagParts($var);
		} elseif(is_array($var)) {
			$tagParts = $var;
		} else {
			print("unexpected variable type was passed to is_properly_nested: ");
			var_dump($var);
			exit(0);
		}
		//print('$tagParts: ');var_dump($tagParts);
		$depth = 0;
		// as a preliminary check, see if the sum of opening and closing tags is an even number.
		foreach($tagParts as $index => $value) {
			$part = $value[0];
			if($value[2] === 1) { // then it is a tag piece
				if(ReTidy::is_self_closing_for_tidyer(ReTidy::tag_name_from_tag_string($part))) {
					//print('is_self_closing_for_tidyer: ');var_dump($part);exit(0);
				} elseif($part[1] == "!") { // then it is a comment (or doctype)
					
				} elseif($part[1] == "?") { // then it is a processing instruction
					
				} elseif($part[strlen($part) - 2] == "/") { // then it is a self-closing tag
					
				} elseif($part[1] == "/") { // then it is a closing piece
					$depth--;
				} else { // then it is an opening piece
					if($part[strlen($part) - 2] == "/") { // it is a self-closing tag

					} else {
						$depth++;
					}
				}
			}
		}
		if($depth === 0) {
		
		} else {
			// we could try to clean it but hopefully we only get here when cleaning a piece so that this is only used for cleaning a piece 
			// in a string with an even sum of opening and closing tags.
			//print('here486970808068957<br>');
			return false;
		}
		//print('here99054-56-<br>');
		$depth = 0;		
		$tempTagParts = array();
		foreach($tagParts as $index => $value) {
			$part = $value[0];
			$offset = $value[1];
			if($value[2] === 1) { // then it is a tag piece
				preg_match('/<(\/)?(\w+)/is', $part, $name_matches);
				$tagName = $name_matches[2];
				if($part[1] == "!") { // then it is a comment (or doctype)
					$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 3);
				} elseif($part[1] == "?") { // then it is a processing instruction
					$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 3);
				} elseif($part[1] == "/") { // then it is a closing piece
					$entryOfDepth = OM::getLastOfDepth($depth, $tempTagParts);
					//print('$entryOfDepth: ');var_dump($entryOfDepth);
					if($tagName !== $entryOfDepth[4]) { // then we have a problem
						//print('here486970808068958<br>');
						return false;
					}
					$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 2);
					$depth--;
				} else { // then it is an opening piece
					if($part[strlen($part) - 2] == "/" || ReTidy::is_self_closing_for_tidyer(ReTidy::tag_name_from_tag_string($part))) { // it is a self-closing tag
						$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 3);
					} else {
						$depth++;
						$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 1);
					}
				}
				if($depth === 0) {
					$tempTagParts = array();
				}
			} else { // text content
				$tempTagParts[] = array($part, $offset, $value[2], $depth, "#text", 0);
			}
		}
		return true;
	}

	public function try_tag_shifting_combinations_with_style_agnosticism($problems_array, $tempTagParts, $tempTagParts2, $recursion_counter = 0) {
		$opening_index = $problems_array[$recursion_counter][0];
		$closing_index = $problems_array[$recursion_counter][1];
		$opening_left_counter = $problems_array[$recursion_counter][2];
		$opening_right_counter = $problems_array[$recursion_counter][3];
		$closing_left_counter = $problems_array[$recursion_counter][4];
		$closing_right_counter = $problems_array[$recursion_counter][5];
		
	//	print("opening_index, closing_index, opening_left_counter, opening_right_counter, closing_left_counter, closing_right_counter<br>\r\n");
	//	var_dump($opening_index, $closing_index, $opening_left_counter, $opening_right_counter, $closing_left_counter, $closing_right_counter);print("<br>\r\n");
		
		$closing_counter = 0 - $closing_left_counter;
		while($closing_counter < $closing_right_counter + 1) {
			$opening_counter = 0 - $opening_left_counter;
			while($opening_counter < $opening_right_counter + 1) {
				// switch then if the substring is properly nested, then recurse (with the whole string)

				$temp_closing = $tempTagParts2[$closing_index];
				$temp_other = $tempTagParts2[$closing_index + $closing_counter];
				$tempTagParts2[$closing_index] = $temp_other;
				$tempTagParts2[$closing_index + $closing_counter] = $temp_closing;
				
				$temp_opening = $tempTagParts2[$opening_index];
				$temp_other = $tempTagParts2[$opening_index + $opening_counter];
				$tempTagParts2[$opening_index] = $temp_other;
				$tempTagParts2[$opening_index + $opening_counter] = $temp_opening;
				
				$object_string = OM::getStringFromPartsArray($tempTagParts2);
				
				if($recursion_counter + 1 < sizeof($problems_array)) {
					$object_string = OM::try_tag_shifting_combinations($problems_array, $tempTagParts2, $tempTagParts2, $recursion_counter + 1);
				}
				if(OM::is_properly_nested($object_string)) { // problem solved maybe
					// if it is properly nested, check that we have not changed the wrappers on the various pieces of text.
					$good_solution = true;
					foreach($tempTagParts2 as $index => $value) {
						if($value[5] === 1) {
							$existing_tagless = $value[6];
							$sub_object_string = OM::getObjectStringFromTagParts($tempTagParts2, $index);
							$tagless = ReTidy::trimStyleAgnostic(ReTidy::tagless($sub_object_string));
							if($existing_tagless !== $tagless) {
								$good_solution = false;
								break;
							}
						}
					}
					if($good_solution) {
						break 2;
					} else {
						$tempTagParts2 = $tempTagParts;
					}
				} else {
					$tempTagParts2 = $tempTagParts;
				}
				$opening_counter++;
			}
			$closing_counter++;
		}
		// notice that there is currently no allowance for failure of this function to provide a properly nested string.
		return $object_string;
	}	
	
	public function array_slide($array, $from_index, $to_index) {
		$moving_element = $array[$from_index];
		if($from_index < $to_index) {
			while($from_index < $to_index) {
				$array[$from_index] = $array[$from_index + 1];
				$from_index++;
			}
		} elseif($from_index > $to_index) {
			while($from_index > $to_index) {
				$array[$from_index] = $array[$from_index - 1];
				$from_index--;
			}
		} else {
			// then using this function is wasteful but does not cause errors.
			return $array;
		}
		$array[$to_index] = $moving_element;
		return $array;
	}
	
	public function is_good_tag_shifting_solution($tempTagParts2) {
		if(OM::is_properly_nested($tempTagParts2)) { // problem solved maybe		
			// if it is properly nested, check that we have not changed the wrappers on the various pieces of text.
			$good_solution = true;
			foreach($tempTagParts2 as $index => $value) {
				if($value[5] === 1) {
					$existing_tagless = $value[6];
					$sub_object_string = OM::getObjectStringFromTagParts($tempTagParts2, $index);
					$tagless = ReTidy::trim_nbsp(ReTidy::tagless($sub_object_string));
					if($existing_tagless !== $tagless) {
						$good_solution = false;
						break;
					}
				}
			}
			if($good_solution) {
				return true;
			}
		}
		return false;
	}
	
	public function getArrayPossibleClosesByDepth($tempTagParts2, $opening_index, $closing_index, $closing_left_counter, $closing_right_counter) {
		//print("tempTagParts2, opening_index, closing_index, closing_left_counter, closing_right_counter: <br>\r\n");
		//var_dump($tempTagParts2, $opening_index, $closing_index, $closing_left_counter, $closing_right_counter);
		$array_possible_closes_by_depth = array();
		$depth = 0;
		$index = $opening_index;
		while($index <= $closing_index + $closing_right_counter) {
			$value = $tempTagParts2[$index];
			$part = $value[0];
			if($value[2] === 1) { // then it is a tag piece
				if($part[1] == "!") { // then it is a comment (or doctype)
					
				} elseif($part[1] == "?") { // then it is a processing instruction
					
				} elseif($part[1] == "/") { // then it is a closing piece
					$depth--;
				} else { // then it is an opening piece
					if($part[strlen($part) - 2] == "/") { // it is a self-closing tag

					} else {
						$depth++;
					}
				}
			}
			if($depth === 1 && $index + 1 <= $closing_index + $closing_right_counter && $index + 1 >= $closing_index - $closing_left_counter) {
				//print("depth, index, closing_index, closing_right_counter, closing_left_counter at possible: <br>\r\n");
				//var_dump($depth, $index, $closing_index, $closing_right_counter, $closing_left_counter);
				$array_possible_closes_by_depth[] = $index + 1 - $closing_index;
			}
			$index++;
		}
		return $array_possible_closes_by_depth;
	}
	
	public function try_tag_shifting_combinations($problems_array, $tempTagParts, $tempTagParts2 = false, $recursion_counter = 0) {
		if((time() - $this->ensure_proper_nesting_initial_time) > 1) { // 1 seconds
			print("combine_inline failed on: <br>\r\n");
			print(htmlentities(OM::getObjectStringFromTagParts($tempTagParts)) . "<br>\r\n");
			print("because proper nesting could not be ensured in a reasonable amount of time.<br>\r\n");
			print("problems array: ");var_dump($problems_array);
			exit(0);
			return false;
		}
		if($tempTagParts2 === false) {
			$tempTagParts2 = $tempTagParts;
		}
		//print('$problems_array: ');var_dump($problems_array);exit(0);
		
		// try without any shifting at all
		$object_string = OM::getStringFromPartsArray($tempTagParts2);
		if($object_string == false) {
			return false;
		}
		if(OM::is_good_tag_shifting_solution($tempTagParts2)) {
			return $object_string;
		}
		if($recursion_counter + 1 < sizeof($problems_array)) {
			$object_string = OM::try_tag_shifting_combinations($problems_array, $tempTagParts2, $tempTagParts2, $recursion_counter + 1);
			if($object_string == false) {
				return false;
			}
			if(OM::is_good_tag_shifting_solution($tempTagParts2)) {
				return $object_string;
			}
		}
		$tempTagParts2 = $tempTagParts;
		
		$opening_index = $problems_array[$recursion_counter][0];
		$closing_index = $problems_array[$recursion_counter][1];
		$opening_left_counter = $problems_array[$recursion_counter][2];
		$opening_right_counter = $problems_array[$recursion_counter][3];
		$closing_left_counter = $problems_array[$recursion_counter][4];
		$closing_right_counter = $problems_array[$recursion_counter][5];
		
		//print("opening_index, closing_index, opening_left_counter, opening_right_counter, closing_left_counter, closing_right_counter<br>\r\n");
		//var_dump($opening_index, $closing_index, $opening_left_counter, $opening_right_counter, $closing_left_counter, $closing_right_counter);print("<br>\r\n");
	
		//print("at opening_index, at closing_index<br>\r\n");
		//var_dump($tempTagParts[$opening_index], $tempTagParts[$closing_index]);print("<br>\r\n");
		
		
		// first, try without shifting the opening.
		//$closing_counter = 0 - $closing_left_counter;
		//while($closing_counter < $closing_right_counter + 1) {
		$closing_counter = $closing_right_counter;
	//	$array_possible_closes_by_depth = OM::getArrayPossibleClosesByDepth($tempTagParts2, $opening_index, $closing_index, $closing_left_counter, $closing_right_counter);
		//print("array_possible_closes_by_depth: ");var_dump($array_possible_closes_by_depth);print("<br>\r\n");exit(0);
	//	foreach($array_possible_closes_by_depth as $closing_counter) {
		while($closing_counter > 0 - ($closing_left_counter + 1)) {
			// switch
			//$temp_closing = $tempTagParts2[$closing_index];
			//$temp_other = $tempTagParts2[$closing_index + $closing_counter];
			//$tempTagParts2[$closing_index] = $temp_other;
			//$tempTagParts2[$closing_index + $closing_counter] = $temp_closing;
			//print("temptagparts1: ");var_dump($tempTagParts2);
			//print("tempTagParts2, closing_index, closing_index + closing_counter: ");
			//var_dump($tempTagParts2, $closing_index, $closing_index + $closing_counter);
			$tempTagParts2 = OM::array_slide($tempTagParts2, $closing_index, $closing_index + $closing_counter);
			
			$object_string = OM::getStringFromPartsArray($tempTagParts2);
			//print("from parts1: ");var_dump($object_string);print("<br>\r\n");//exit(0);
			//if($object_string === '<a name="_Toc150751263" id="_Toc150751263">') {
			//	print("temptagparts2: ");var_dump($tempTagParts2);exit(0);
			//}
			if($object_string == false) {
				return false;
			}
			//if(OM::is_good_tag_shifting_solution($object_string, $tempTagParts2)) {
			if(OM::is_good_tag_shifting_solution($tempTagParts2)) {
				//print("good solution48948942509");var_dump($object_string);print("<br>\r\n");exit(0);
				return $object_string;
			}
			//if($recursion_counter + 1 < sizeof($problems_array)) {
			//	$object_string = OM::try_tag_shifting_combinations($problems_array, $tempTagParts2, $tempTagParts2, $recursion_counter + 1);
			//	if($object_string == false) {
			//		return false;
			//	}
			//	//if(OM::is_good_tag_shifting_solution($object_string, $tempTagParts2)) {
			//	if(OM::is_good_tag_shifting_solution($tempTagParts2)) {
			//		return $object_string;
			//	}
			//}
			$tempTagParts2 = $tempTagParts;
			//$closing_counter++;
			$closing_counter--;
		}
		
		// try without shifting the closing.
		$opening_counter = 0 - $opening_left_counter;
		while($opening_counter < $opening_right_counter + 1) {
			// switch
			//$temp_opening = $tempTagParts2[$opening_index];
			//$temp_other = $tempTagParts2[$opening_index + $opening_counter];
			//$tempTagParts2[$opening_index] = $temp_other;
			//$tempTagParts2[$opening_index + $opening_counter] = $temp_opening;
			
			$tempTagParts2 = OM::array_slide($tempTagParts2, $opening_index, $opening_index + $opening_counter);
			
			$object_string = OM::getStringFromPartsArray($tempTagParts2);
		//	print("from parts2: ");var_dump($object_string);print("<br>\r\n");
			if($object_string == false) {
				return false;
			}
			//if(OM::is_good_tag_shifting_solution($object_string, $tempTagParts2)) {
			if(OM::is_good_tag_shifting_solution($tempTagParts2)) {
				return $object_string;
			}
			//if($recursion_counter + 1 < sizeof($problems_array)) {
			//	$object_string = OM::try_tag_shifting_combinations($problems_array, $tempTagParts2, $tempTagParts2, $recursion_counter + 1);
			//	if($object_string == false) {
			//		return false;
			//	}
			//	//if(OM::is_good_tag_shifting_solution($object_string, $tempTagParts2)) {
			//	if(OM::is_good_tag_shifting_solution($tempTagParts2)) {
			//		return $object_string;
			//	}
			//}
			$tempTagParts2 = $tempTagParts;
			$opening_counter++;
		}		
		
		

		// try without recursion but all the problems (should work if the nesting problems are only from one badly nested tag?)
		// since there is only one problem using ensure_proper_nesting_for_combine_inline?
		foreach($problems_array as $problems_index => $problems_value) {
			//print("problems_value: ");var_dump($problems_value);print("<br>\r\n");
			
			$opening_index = $problems_array[$problems_index][0];
			$closing_index = $problems_array[$problems_index][1];
			$opening_left_counter = $problems_array[$problems_index][2];
			$opening_right_counter = $problems_array[$problems_index][3];
			$closing_left_counter = $problems_array[$problems_index][4];
			$closing_right_counter = $problems_array[$problems_index][5];
			
			//print("at opening_index, at closing_index<br>\r\n");
			//var_dump($tempTagParts[$opening_index], $tempTagParts[$closing_index]);print("<br>\r\n");
			
			$opening_counter = 0 - $opening_left_counter;
			while($opening_counter < $opening_right_counter + 1) {
				//print("main loop<br>\r\n");
				// since it is done above:
				if($opening_counter === 0) {
					$opening_counter++;
					continue;
				}
			//	$tempTagParts2 = OM::array_slide($tempTagParts2, $opening_index, $opening_index + $opening_counter);
			//	$array_possible_closes_by_depth = OM::getArrayPossibleClosesByDepth($tempTagParts2, $opening_index + $opening_counter, $closing_index, $closing_left_counter, $closing_right_counter);
				//$closing_counter = 0 - $closing_left_counter;
				//while($closing_counter < $closing_right_counter + 1) {
				$closing_counter = $closing_right_counter;
				while($closing_counter > 0 - ($closing_left_counter + 1)) {
				//print("array_possible_closes_by_depth: ");var_dump($array_possible_closes_by_depth);print("<br>\r\n");
			//	foreach($array_possible_closes_by_depth as $closing_counter) {
					// since it is done above:
					if($closing_counter === 0) {
						$closing_counter--;
						continue;
					}
					// switch
					//$temp_closing = $tempTagParts2[$closing_index];
					//$temp_other = $tempTagParts2[$closing_index + $closing_counter];
					//$tempTagParts2[$closing_index] = $temp_other;
					//$tempTagParts2[$closing_index + $closing_counter] = $temp_closing;
					
					$tempTagParts2 = OM::array_slide($tempTagParts2, $closing_index, $closing_index + $closing_counter);
					
					//$temp_opening = $tempTagParts2[$opening_index];
					//$temp_other = $tempTagParts2[$opening_index + $opening_counter];
					//$tempTagParts2[$opening_index] = $temp_other;
					//$tempTagParts2[$opening_index + $opening_counter] = $temp_opening;
					
					$tempTagParts2 = OM::array_slide($tempTagParts2, $opening_index, $opening_index + $opening_counter);
					
					$object_string = OM::getStringFromPartsArray($tempTagParts2);
				//	print("from parts3: ");var_dump($object_string);print("<br>\r\n");
					if($object_string == false) {
						return false;
					}
					//if(OM::is_good_tag_shifting_solution($object_string, $tempTagParts2)) {
					if(OM::is_good_tag_shifting_solution($tempTagParts2)) {
						return $object_string;
					}
					//$tempTagParts2 = OM::array_slide($tempTagParts2, $opening_index + $opening_counter, $opening_index);
				//	$tempTagParts2 = OM::array_slide($tempTagParts2, $closing_index + $closing_counter, $closing_index);
					$tempTagParts2 = $tempTagParts;
					//$closing_counter++;
					$closing_counter--;
				}
				//$tempTagParts2 = $tempTagParts;
				$opening_counter++;
			}
		}		
		
		
		
		/*
		$opening_index = $problems_array[$recursion_counter][0];
		$closing_index = $problems_array[$recursion_counter][1];
		$opening_left_counter = $problems_array[$recursion_counter][2];
		$opening_right_counter = $problems_array[$recursion_counter][3];
		$closing_left_counter = $problems_array[$recursion_counter][4];
		$closing_right_counter = $problems_array[$recursion_counter][5];
		
		// try without recursion
		$closing_counter = 0 - $closing_left_counter;
		while($closing_counter < $closing_right_counter + 1) {
		//$closing_counter = $closing_right_counter;
		//while($closing_counter > 0 - ($closing_left_counter + 1)) {
			$opening_counter = 0 - $opening_left_counter;
			while($opening_counter < $opening_right_counter + 1) {
				// switch then if the substring is properly nested, then recurse (with the whole string)

				$temp_closing = $tempTagParts2[$closing_index];
				$temp_other = $tempTagParts2[$closing_index + $closing_counter];
				$tempTagParts2[$closing_index] = $temp_other;
				$tempTagParts2[$closing_index + $closing_counter] = $temp_closing;
				
				$temp_opening = $tempTagParts2[$opening_index];
				$temp_other = $tempTagParts2[$opening_index + $opening_counter];
				$tempTagParts2[$opening_index] = $temp_other;
				$tempTagParts2[$opening_index + $opening_counter] = $temp_opening;
				
				$object_string = OM::getStringFromPartsArray($tempTagParts2);
				print("from parts: ");var_dump($object_string);print("<br>\r\n");
				//if($recursion_counter + 1 < sizeof($problems_array)) {
				//	$object_string = OM::try_tag_shifting_combinations($problems_array, $tempTagParts2, $tempTagParts2, $recursion_counter + 1);
				//	//print("try: ");var_dump($object_string);print("<br>\r\n");
				//	if($object_string == false) {
				//		//if((time() - $this->ensure_proper_nesting_initial_time) > 1) { // 1 seconds
				//		//	if($recursion_counter === 0) {
				//		//		print("combine_inline failed on: <br>\r\n");
				//		//		print(htmlentities(OM::getObjectStringFromTagParts($tempTagParts)) . "<br>\r\n");
				//		//		print("because proper nesting could not be ensured in a reasonable amount of time.<br>\r\n");
				//		//		print("problems array: ");var_dump($problems_array);
				//		//	}
				//		//}
				//		//print("recursion at false return: ");var_dump($recursion_counter);print("<br>\r\n");
				//		return false;
				//	}
				//}
				if(OM::is_properly_nested($object_string)) { // problem solved maybe
					//print("properly_nested object string: ");var_dump($object_string);print("<br>\r\n");
					// if it is properly nested, check that we have not changed the wrappers on the various pieces of text.
					$good_solution = true;
					foreach($tempTagParts2 as $index => $value) {
						if($value[5] === 1) {
							$existing_tagless = $value[6];
							$sub_object_string = OM::getObjectStringFromTagParts($tempTagParts2, $index);
							$tagless = ReTidy::trim_nbsp(ReTidy::tagless($sub_object_string));
							if($existing_tagless !== $tagless) {
								$good_solution = false;
								break;
							}
						}
					}
					if($good_solution) {
						//if($recursion_counter === 0) {
							//print("object_string: ");var_dump($object_string);print("<br>\r\n");
						//}
						//print("recursion: ");var_dump($recursion_counter);print("<br>\r\n");
						return $object_string;
						//break 2;
					} else {
						$tempTagParts2 = $tempTagParts;
					}
				} else {
					$tempTagParts2 = $tempTagParts;
				}
				$opening_counter++;
			}
			$closing_counter++;
			//$closing_counter--;
		}*/
	
		
		/*
		//$closing_counter = 0 - $closing_left_counter;
		//while($closing_counter < $closing_right_counter + 1) {
		$closing_counter = $closing_right_counter;
		while($closing_counter > 0 - ($closing_left_counter + 1)) {
			$opening_counter = 0 - $opening_left_counter;
			while($opening_counter < $opening_right_counter + 1) {
				// switch then if the substring is properly nested, then recurse (with the whole string)

				$temp_closing = $tempTagParts2[$closing_index];
				$temp_other = $tempTagParts2[$closing_index + $closing_counter];
				$tempTagParts2[$closing_index] = $temp_other;
				$tempTagParts2[$closing_index + $closing_counter] = $temp_closing;
				
				$temp_opening = $tempTagParts2[$opening_index];
				$temp_other = $tempTagParts2[$opening_index + $opening_counter];
				$tempTagParts2[$opening_index] = $temp_other;
				$tempTagParts2[$opening_index + $opening_counter] = $temp_opening;
				
				$object_string = OM::getStringFromPartsArray($tempTagParts2);
				print("from parts: ");var_dump($object_string);print("<br>\r\n");
				if($recursion_counter + 1 < sizeof($problems_array)) {
					$object_string = OM::try_tag_shifting_combinations($problems_array, $tempTagParts2, $tempTagParts2, $recursion_counter + 1);
					//print("try: ");var_dump($object_string);print("<br>\r\n");
					if($object_string == false) {
						//if((time() - $this->ensure_proper_nesting_initial_time) > 1) { // 1 seconds
						//	if($recursion_counter === 0) {
						//		print("combine_inline failed on: <br>\r\n");
						//		print(htmlentities(OM::getObjectStringFromTagParts($tempTagParts)) . "<br>\r\n");
						//		print("because proper nesting could not be ensured in a reasonable amount of time.<br>\r\n");
						//		print("problems array: ");var_dump($problems_array);
						//	}
						//}
						//print("recursion at false return: ");var_dump($recursion_counter);print("<br>\r\n");
						return false;
					}
				}
				if(OM::is_properly_nested($object_string)) { // problem solved maybe
					//print("properly_nested object string: ");var_dump($object_string);print("<br>\r\n");
					// if it is properly nested, check that we have not changed the wrappers on the various pieces of text.
					$good_solution = true;
					foreach($tempTagParts2 as $index => $value) {
						if($value[5] === 1) {
							$existing_tagless = $value[6];
							$sub_object_string = OM::getObjectStringFromTagParts($tempTagParts2, $index);
							$tagless = ReTidy::trim_nbsp(ReTidy::tagless($sub_object_string));
							if($existing_tagless !== $tagless) {
								$good_solution = false;
								break;
							}
						}
					}
					if($good_solution) {
						//if($recursion_counter === 0) {
							//print("object_string: ");var_dump($object_string);print("<br>\r\n");
						//}
						//print("recursion: ");var_dump($recursion_counter);print("<br>\r\n");
						return $object_string;
						//break 2;
					} else {
						$tempTagParts2 = $tempTagParts;
					}
				} else {
					$tempTagParts2 = $tempTagParts;
				}
				$opening_counter++;
			}
			//$closing_counter++;
			$closing_counter--;
		}
		//if($recursion_counter === 0) {
		//	print("object_string: ");var_dump($object_string);print("<br>\r\n");
		//}
		*/
		
		//return $object_string;
	//	print("possibly interspanning combine strings<br>\r\n");
		return false; 
		//print("<h1>MEGA ERROR 32832890-=2mm2=2-m2m</h1>");exit(0);
	}
	
	public function getIndexOfOpeningByName($name, $tempTagParts) {
		$depth = -1;
		foreach(array_reverse($tempTagParts) as $index => $value) {
			if($value[4] === $name) {
				if($value[5] === 1) {
					$depth++;
				}
				if($value[5] === 2) {
					$depth--;
				}
				if($depth === 0) {
					// since the array is reversed.
					return sizeof($tempTagParts) - $index - 1;
				}
			}
		}
		return false;
	}
	
	public function getObjectStringFromTagParts($tempTagParts, $index = 0) {
		$counter = 1;
		$depth = 1;
		$tagName = $tempTagParts[$index][4];
		while($index + $counter < sizeof($tempTagParts)) {
			if($tempTagParts[$index + $counter][4] === $tagName) {
				if($tempTagParts[$index + $counter][5] === 1) {
					$depth++;
				}
				if($tempTagParts[$index + $counter][5] === 2) {
					$depth--;
				}
				if($depth === 0) {
					return OM::getStringFromPartsArray($tempTagParts, $index, $index + $counter);
				}
			}			
			$counter++;
		}
		return ""; // instead of false because functions depending on this one expect a string.
	}
	
	public function addTagless($tempTagParts) {
		foreach($tempTagParts as $index => $value) {
			if($value[5] === 1) {
				$object_string = OM::getObjectStringFromTagParts($tempTagParts, $index);
				$tempTagParts[$index][6] = ReTidy::tagless($object_string);
			} else {
				$tempTagParts[$index][6] = "";
			}
		}
		return $tempTagParts;
	}
	
	public function addTrimmedTagless($tempTagParts) {
		foreach($tempTagParts as $index => $value) {
			if($value[5] === 1) {
				$object_string = OM::getObjectStringFromTagParts($tempTagParts, $index);
				$tempTagParts[$index][6] = ReTidy::trim_nbsp(ReTidy::tagless($object_string));
			} else {
				$tempTagParts[$index][6] = "";
			}
		}
		return $tempTagParts;
	}
	
	public function addStyleAgnosticTrimmedTagless($tempTagParts) {
		foreach($tempTagParts as $index => $value) {
			if($value[5] === 1) {
				$object_string = OM::getObjectStringFromTagParts($tempTagParts, $index);
				$tempTagParts[$index][6] = ReTidy::trimStyleAgnostic(ReTidy::tagless($object_string));
			} else {
				$tempTagParts[$index][6] = "";
			}
		}
		return $tempTagParts;
	}
	
	public function ensure_proper_nesting_for_combine_inline($string, $entity) {
		// the main difference with this _for_combine_inline version is that we assume any nesting errors are due to 
		// combine_inline so we should be able to move the opening or closing tag for that which was combined.
		$tagParts = OM::getAllTagParts($string);
		if(OM::is_properly_nested($tagParts)) {
			return $string;
		}
		$this->ensure_proper_nesting_initial_time = time();
		$inlineTags = explode("|", $entity . preg_replace('/\|([^\|]+)/is', '|XXX9o9${1}9o9XXX', "|" . $entity));
		$depth = 0;
		$problems_array = array();
		$tempTagParts = array();
		foreach($tagParts as $index => $value) {
			$part = $value[0];
			$offset = $value[1];
			if($value[2] === 1) { // then it is a tag piece
				preg_match('/<(\/)?(\w+)/is', $part, $name_matches);
				$tagName = $name_matches[2];
				if($part[1] == "!") { // then it is a comment (or doctype)
					$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 3); // should these be type 3???
					$working_string .= $part;
				} elseif($part[1] == "?") { // then it is a processing instruction
					$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 3); // should these be type 3???
					$working_string .= $part;
				} elseif($part[1] == "/") { // then it is a closing piece
					if(strpos($tagName, "XXX9o9") === 0) {
						$closing_index = sizeof($tempTagParts);
					}
					$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 2);
					$working_string .= $part;
					$depth--;
				} else { // then it is an opening piece
					if($part[strlen($part) - 2] == "/") { // it is a self-closing tag
						$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 3);
						$working_string .= $part;
					} else {
						if(strpos($tagName, "XXX9o9") === 0) {
							$opening_index = sizeof($tempTagParts);
						}
						$depth++;
						$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 1);
						$working_string .= $part;
					}
				}
				if($depth === 0) {
					// check if the opening tag can be moved.
					// check if it can be moved to the right
					$opening_right_counter = 0;
					while($opening_index !== false) {
						if($tempTagParts[$opening_index + $opening_right_counter + 1][2] === 0) { // text
							if(ReTidy::isSpace($tempTagParts[$opening_index + $opening_right_counter + 1][0])) {
								$opening_right_counter++;
								continue;
							} else {
								break;
							}
						}							
						if($tempTagParts[$opening_index + $opening_right_counter + 1][2] === 1) { // tag
							$tagName5 = $tempTagParts[$opening_index + $opening_right_counter + 1][4];
							foreach($inlineTags as $index5 => $value5) {
								if($tagName5 === $value5) {
									$opening_right_counter++;
									continue 2;
								}
							}
							break;
						} else {
							break;
						}
					}
					// the tag can be moved to the right by the amount $opening_right_counter
					// check if it can be moved to the left
					$opening_left_counter = 0;
					while($opening_index !== false) {
						if($tempTagParts[$opening_index - $opening_left_counter - 1][2] === 0) { // text
							if(ReTidy::isSpace($tempTagParts[$opening_index - $opening_left_counter - 1][0])) {
								$opening_left_counter++;
								continue;
							} else {
								break;
							}
						}							
						if($tempTagParts[$opening_index - $opening_left_counter - 1][2] === 1) { // tag
							$tagName5 = $tempTagParts[$opening_index - $opening_left_counter - 1][4];
							foreach($inlineTags as $index5 => $value5) {
								if($tagName5 === $value5) {
									$opening_left_counter++;
									continue 2;
								}
							}
							break;
						} else {
							break;
						}
					}
					// the tag can be moved to the left by the amount $opening_left_counter
						
					// check if it can be moved to the right
					$closing_right_counter = 0;
					while(true) {
						if($tempTagParts[$closing_index + $closing_right_counter + 1][2] === 0) { // text							
							if(ReTidy::isSpace($tempTagParts[$closing_index + $closing_right_counter + 1][0])) {
								$closing_right_counter++;
								continue;
							} else {
								break;
							}
						}
						if($tempTagParts[$closing_index + $closing_right_counter + 1][2] === 1) { // tag							
							$tagName5 = $tempTagParts[$closing_index + $closing_right_counter + 1][4];
							foreach($inlineTags as $index5 => $value5) {
								if($tagName5 === $value5) {
									$closing_right_counter++;
									continue 2;
								}
							}
							break;
						} else {
							break;
						}
					}
					// the tag can be moved to the right by the amount $closing_right_counter
					// check if it can be moved to the left
					$closing_left_counter = 0;
					while(true) {
						if($tempTagParts[$closing_index - $closing_left_counter - 1][2] === 0) { // text
							if(ReTidy::isSpace($tempTagParts[$closing_index - $closing_left_counter - 1][0])) {
								$closing_left_counter++;
								continue;
							} else {
								break;
							}
						}							
						if($tempTagParts[$closing_index - $closing_left_counter - 1][2] === 1) { // tag
							$tagName5 = $tempTagParts[$closing_index - $closing_left_counter - 1][4];
							foreach($inlineTags as $index5 => $value5) {
								if($tagName5 === $value5) {
									$closing_left_counter++;
									continue 2;
								}
							}
							break;
						} else {
							break;
						}							
					}
					// the tag can be moved to the left by the amount $closing_left_counter							
					
					$tempTagParts = OM::addTrimmedTagless($tempTagParts);
					$problems_array[] = array($opening_index, $closing_index, $opening_left_counter, $opening_right_counter, $closing_left_counter, $closing_right_counter);
					$properly_nested = OM::try_tag_shifting_combinations($problems_array, $tempTagParts);
					if($properly_nested === false) {
						return false;
					}
					$return_string .= $properly_nested;
					$working_string = "";
					$tempTagParts = array();
					$problems_array = array();
				}
			} else { // text content
				$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 0);
				$working_string .= $part;
			}
		}
		if($tempTagParts[0][0] !== false) { // that is, if there is text at the end of the string we are interested in that needs to be added:
			$return_string .= $tempTagParts[0][0];
		}
		return $return_string;
	}
	
	public function ensure_proper_nesting($string, $entity) {
		//print('$entity: ');var_dump($entity);exit(0);
		if(OM::is_properly_nested($string)) {
			return $string;
		}
		$this->ensure_proper_nesting_initial_time = time();
		$inlineTags = explode("|", $entity . preg_replace('/\|([^\|]+)/is', '|XXX9o9${1}9o9XXX', "|" . $entity));
		//print('$inlineTags: ');var_dump($inlineTags);exit(0);
		$tagParts = OM::getAllTagParts($string);
		$depth = 0;
		$problems_array = array();
		$tempTagParts = array();
		foreach($tagParts as $index => $value) {
			$part = $value[0];
			$offset = $value[1];
			if($value[2] === 1) { // then it is a tag piece
				preg_match('/<(\/)?(\w+)/is', $part, $name_matches);
				$tagName = $name_matches[2];
				if($part[1] == "!") { // then it is a comment (or doctype)
					$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 3); // should these be type 3???
					$working_string .= $part;
				} elseif($part[1] == "?") { // then it is a processing instruction
					$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 3); // should these be type 3???
					$working_string .= $part;
				} elseif($part[1] == "/") { // then it is a closing piece
					$entryOfDepth = OM::getLastOfDepth($depth, $tempTagParts);
					if($tagName !== $entryOfDepth[4]) { // then we have a problem
						//print('problem in ensure_proper_nesting: <br>');
						//print('$tagName: ' . $tagName . '<br>');
						//print('$working_string: ' . $working_string . '<br>');
						// this part should be moved or the entryOfDepth should be moved.
						// check if this part can be moved.
						// check if it can be moved to the right
						$right_counter = 0;
						while(true) {
							if($tagParts[$index + $right_counter + 1][2] === 0) { // text
								if(ReTidy::isSpaceOrTag($tagParts[$index + $right_counter + 1][0])) {
									$right_counter++;
									continue;
								} else {
									break;
								}
							}
							if($tagParts[$index + $right_counter + 1][2] === 1) { // tag
								preg_match('/<(\/)?(\w+)/is', $tagParts[$index + $right_counter + 1][0], $name_matches5);
								$tagName5 = $name_matches5[2];
								foreach($inlineTags as $index5 => $value5) {
									if($tagName5 === $value5) {
										$right_counter++;
										continue 2;
									}
								}
								break;
							} else {
								break;
							}
						}
						// the tag can be moved to the right by the amount $right_counter
						// check if it can be moved to the left
						$left_counter = 0;
						while(true) {
							if($tagParts[$index - $left_counter - 1][2] === 0) { // text
								if(ReTidy::isSpaceOrTag($tagParts[$index - $left_counter - 1][0])) {
									$left_counter++;
									continue;
								} else {
									break;
								}
							}							
							if($tagParts[$index - $left_counter - 1][2] === 1) { // tag
								preg_match('/<(\/)?(\w+)/is', $tagParts[$index - $left_counter - 1][0], $name_matches5);
								$tagName5 = $name_matches5[2];
								foreach($inlineTags as $index5 => $value5) {
									if($tagName5 === $value5) {
										$left_counter++;
										continue 2;
									}
								}
								break;
							} else {
								break;
							}							
						}
						// the tag can be moved to the left by the amount $left_counter
						$opening_index = OM::getIndexOfOpeningByName($tagName, $tempTagParts);
						// check if the opening tag can be moved.
						// check if it can be moved to the right
						$opening_right_counter = 0;
						while($opening_index !== false) {
							if($tempTagParts[$opening_index + $opening_right_counter + 1][2] === 0) { // text
								if(ReTidy::isSpaceOrTag($tempTagParts[$opening_index + $opening_right_counter + 1][0])) {
									$opening_right_counter++;
									continue;
								} else {
									break;
								}
							}							
							if($tempTagParts[$opening_index + $opening_right_counter + 1][2] === 1) { // tag
								preg_match('/<(\/)?(\w+)/is', $tempTagParts[$opening_index + $opening_right_counter + 1][0], $name_matches5);
								$tagName5 = $name_matches5[2];
								foreach($inlineTags as $index5 => $value5) {
									if($tagName5 === $value5) {
										$opening_right_counter++;
										continue 2;
									}
								}
								break;
							} else {
								break;
							}
						}
						// the tag can be moved to the right by the amount $opening_right_counter
						// check if it can be moved to the left
						$opening_left_counter = 0;
						while($opening_index !== false) {
							if($tempTagParts[$opening_index - $opening_left_counter - 1][2] === 0) { // text
								if(ReTidy::isSpaceOrTag($tempTagParts[$opening_index - $opening_left_counter - 1][0])) {
									$opening_left_counter++;
									continue;
								} else {
									break;
								}
							}							
							if($tempTagParts[$opening_index - $opening_left_counter - 1][2] === 1) { // tag
								preg_match('/<(\/)?(\w+)/is', $tempTagParts[$opening_index - $opening_left_counter - 1][0], $name_matches5);
								$tagName5 = $name_matches5[2];
								foreach($inlineTags as $index5 => $value5) {
									if($tagName5 === $value5) {
										$opening_left_counter++;
										continue 2;
									}
								}
								break;
							} else {
								break;
							}
						}
						// the tag can be moved to the left by the amount $opening_left_counter
						$problems_array[] = array($opening_index, sizeof($tempTagParts), $opening_left_counter, $opening_right_counter, $left_counter, $right_counter);
					}
					$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 2);
					$working_string .= $part;
					$depth--;
				} else { // then it is an opening piece
					if($part[strlen($part) - 2] == "/") { // it is a self-closing tag
						$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 3);
						$working_string .= $part;
					} else {
						$depth++;
						$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 1);
						$working_string .= $part;
					}
				}
				//print('string: ');var_dump($string);
				if($depth === 0) {
					if(sizeof($problems_array) === 0) {
						//print('$return_string: ');var_dump($return_string);
						$return_string .= $working_string;
						$working_string = "";
						$tempTagParts = array();
					} else {
						//print('$working_string: ' . $working_string);
						$tempTagParts = OM::addTrimmedTagless($tempTagParts);
						// notice that this is brute force and the recursion is n-fold where n is the number of nesting problems.
						$properly_nested = OM::try_tag_shifting_combinations($problems_array, $tempTagParts);
						if($properly_nested === false) {
							return false;
						}
						$return_string .= $properly_nested;
						$working_string = "";
						$tempTagParts = array();
						$problems_array = array();
					}
				}
			} else { // text content
				$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 0);
				$working_string .= $part;
			}
		}
		return $return_string;
	}
	
	public function ensure_proper_nesting_with_style_agnoticism($string, $entity) {
		$inlineTags = explode("|", $entity . preg_replace('/\|([^\|]+)/is', '|XXX9o9${1}9o9XXX', "|" . $entity));
		$tagParts = OM::getAllTagParts($string);
		$depth = 0;
		$problems_array = array();
		$tempTagParts = array();
		foreach($tagParts as $index => $value) {
			$part = $value[0];
			$offset = $value[1];
			if($value[2] === 1) { // then it is a tag piece
				preg_match('/<(\/)?(\w+)/is', $part, $name_matches);
				$tagName = $name_matches[2];
				if($part[1] == "!") { // then it is a comment (or doctype)
					$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 3); // should these be type 3???
					$working_string .= $part;
				} elseif($part[1] == "?") { // then it is a processing instruction
					$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 3); // should these be type 3???
					$working_string .= $part;
				} elseif($part[1] == "/") { // then it is a closing piece
					$entryOfDepth = OM::getLastOfDepth($depth, $tempTagParts);
					if($tagName !== $entryOfDepth[4]) { // then we have a problem
						// this part should be moved or the entryOfDepth should be moved.
						// check if this part can be moved.
						// check if it can be moved to the right
						$right_counter = 0;
						while(true) {
							if($tagParts[$index + $right_counter + 1][2] === 0) { // text
								if(ReTidy::isStyleAgnostic($tagParts[$index + $right_counter + 1][0])) {
									$right_counter++;
									continue;
								} else {
									break;
								}
							}
							if($tagParts[$index + $right_counter + 1][2] === 1) { // tag
								preg_match('/<(\/)?(\w+)/is', $tagParts[$index + $right_counter + 1][0], $name_matches5);
								$tagName5 = $name_matches5[2];
								foreach($inlineTags as $index5 => $value5) {
									if($tagName5 === $value5) {
										$right_counter++;
										continue 2;
									}
								}
								break;
							} else {
								break;
							}
						}
						// the tag can be moved to the right by the amount $right_counter
						// check if it can be moved to the left
						$left_counter = 0;
						while(true) {
							if($tagParts[$index - $left_counter - 1][2] === 0) { // text
								if(ReTidy::isStyleAgnostic($tagParts[$index - $left_counter - 1][0])) {
									$left_counter++;
									continue;
								} else {
									break;
								}
							}							
							if($tagParts[$index - $left_counter - 1][2] === 1) { // tag
								preg_match('/<(\/)?(\w+)/is', $tagParts[$index - $left_counter - 1][0], $name_matches5);
								$tagName5 = $name_matches5[2];
								foreach($inlineTags as $index5 => $value5) {
									if($tagName5 === $value5) {
										$left_counter++;
										continue 2;
									}
								}
								break;
							} else {
								break;
							}							
						}
						// the tag can be moved to the left by the amount $left_counter
						$opening_index = OM::getIndexOfOpeningByName($tagName, $tempTagParts);
						// check if the opening tag can be moved.
						// check if it can be moved to the right
						$opening_right_counter = 0;
						while($opening_index !== false) {
							if($tempTagParts[$opening_index + $opening_right_counter + 1][2] === 0) { // text
								if(ReTidy::isStyleAgnostic($tempTagParts[$opening_index + $opening_right_counter + 1][0])) {
									$opening_right_counter++;
									continue;
								} else {
									break;
								}
							}							
							if($tempTagParts[$opening_index + $opening_right_counter + 1][2] === 1) { // tag
								preg_match('/<(\/)?(\w+)/is', $tempTagParts[$opening_index + $opening_right_counter + 1][0], $name_matches5);
								$tagName5 = $name_matches5[2];
								foreach($inlineTags as $index5 => $value5) {
									if($tagName5 === $value5) {
										$opening_right_counter++;
										continue 2;
									}
								}
								break;
							} else {
								break;
							}
						}
						// the tag can be moved to the right by the amount $opening_right_counter
						// check if it can be moved to the left
						$opening_left_counter = 0;
						while($opening_index !== false) {
							if($tempTagParts[$opening_index - $opening_left_counter - 1][2] === 0) { // text
								if(ReTidy::isStyleAgnostic($tempTagParts[$opening_index - $opening_left_counter - 1][0])) {
									$opening_left_counter++;
									continue;
								} else {
									break;
								}
							}							
							if($tempTagParts[$opening_index - $opening_left_counter - 1][2] === 1) { // tag
								preg_match('/<(\/)?(\w+)/is', $tempTagParts[$opening_index - $opening_left_counter - 1][0], $name_matches5);
								$tagName5 = $name_matches5[2];
								foreach($inlineTags as $index5 => $value5) {
									if($tagName5 === $value5) {
										$opening_left_counter++;
										continue 2;
									}
								}
								break;
							} else {
								break;
							}
						}
						// the tag can be moved to the left by the amount $opening_left_counter
						$problems_array[] = array($opening_index, sizeof($tempTagParts), $opening_left_counter, $opening_right_counter, $left_counter, $right_counter);
					}
					$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 2);
					$working_string .= $part;
					$depth--;
				} else { // then it is an opening piece
					if($part[strlen($part) - 2] == "/") { // it is a self-closing tag
						$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 3);
						$working_string .= $part;
					} else {
						$depth++;
						$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 1);
						$working_string .= $part;
					}
				}
				if($depth === 0) {
					if(sizeof($problems_array) === 0) {
						$return_string .= $working_string;
						$working_string = "";
						$tempTagParts = array();
					} else {
						$tempTagParts = OM::addStyleAgnosticTrimmedTagless($tempTagParts);
						// notice that this is brute force and the recursion is n-fold where n is the number of nesting problems.
						$properly_nested = OM::try_tag_shifting_combinations_with_style_agnoticism($problems_array, $tempTagParts, $tempTagParts);
						$return_string .= $properly_nested;
						$working_string = "";
						$tempTagParts = array();
						$problems_array = array();
					}
				}
			} else { // text content
				$tempTagParts[] = array($part, $offset, $value[2], $depth, $tagName, 0);
				$working_string .= $part;
			}
		}
		return $return_string;
	}
	
	public function getTOCString2($string, $offset = 0, $pure = false) {
		// this function revises getTOCString (2011-07-19)
		// instead of going by allowed tags; we'll try to stick to as close to a definition as there is for a table contents;
		// that is; that it is indexical rather than explicit content...
		// so, once explicit content is hit, then call it the end of the table of contents.
		// this one ensures proper nesting on the fly.
		// Notice that the offset variable in this function is defined as the beginning of the table of contents even though it has a default value of 0
		$blockString = 'p|h1|h2|h3|h4|h5|h6|li|a';
		$TOCOStringsArray = OM::getTOCOStringsArray($string, $offset, $pure);
		$TOCString = substr(
		$string, 
		$TOCOStringsArray[0][1], 
		$TOCOStringsArray[sizeof($TOCOStringsArray) - 1][1] - $TOCOStringsArray[0][1]
		) . $TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0];
		return $TOCString;
	}
	
	public function getTOCString($string, $offset, $ignore_expression, $allowed_tags) {
		// this one ensures proper nesting on the fly.
		$TOCString = '';
		$marker = $offset;
		while(true) {
			$next_opening_position = strpos($string, "<", $marker);
			if($next_opening_position === false) {
				break;
			}
			preg_match('/<(\w+)/is', $string, $matches4577, PREG_OFFSET_CAPTURE, $marker);
			$tagName = $matches4577[1][0];
			$tag_allowed = false;
			foreach($allowed_tags as $allowed_tag) {
				if($allowed_tag === $tagName) {
					$tag_allowed = true;
					break;
				}
			}
			if(!$tag_allowed) {
				break;
			}
			// notice that when we first get in to this loop the next_opening_position should be zero.
			if($next_opening_position === $marker) {
			
			} else {
				$potentially_to_combine = substr($string, $marker, $next_opening_position - $marker);
				preg_match('/' . $ignore_expression . '/is', $potentially_to_combine, $matches39);
				if($matches39[0] === $potentially_to_combine) {
					$marker += strlen($potentially_to_combine);
					$TOCString .= $potentially_to_combine;
				} else {
					break;
				}
			}
			$OString = OM::getOString($string, '<' . $tagName, '</' . $tagName . '>', $marker);
			$marker += strlen($OString);
			$TOCString .= $OString;
		}
		// the $combine_string is here potentially improperly nested, so:
		/*while(true) {
			if(strpos(substr($combine_string, 1), $opening_string) !== false) { // if there is anything to combine
				$replaceString = $combine_string;
				// getAllTagStrings with an optional attributes parameter would be useful here. "getTagString" should be generalized to work with this (2009-08-18).
				$arrayOStrings = OM::getAllOStrings($replaceString, '<' . $tagName, $closing_string);
				foreach($arrayOStrings as $index => $value) {
					$OString = $value[0];
					if(strpos($OString, $opening_string) === 0) {
						$combinedOString = substr($OString, strlen($opening_string), strlen($OString) - strlen($opening_string) - strlen($closing_string));
						$replaceString = str_replace($OString, $combinedOString, $replaceString);
					}
				}
				$replaceString = str_replace('<' . $tagName, '<XXX9o9' . $tagName . '9o9XXX', $opening_string) . $replaceString . '</XXX9o9' . $tagName . '9o9XXX>';
				$new_string = $string;
				$new_string = str_replace($combine_string, $replaceString, $new_string);
				$new_string = OM::ensure_proper_nesting_for_combine_inline($new_string, $entity);
				if($new_string === false) {
					// cut back what we are trying to combine.
					unset($array_combine_strings[sizeof($array_combine_strings) - 1]);
					$combine_string = $array_combine_strings[sizeof($array_combine_strings) - 1];
					continue;
				}
				$new_string = str_replace('XXX9o9' . $tagName . '9o9XXX', $tagName, $new_string);
				break;
				
			} else {
				return false;
			}
		}*/
		return $TOCString;
	}
	
	public function getCombineString($string, $opening_string, $closing_string, $offset, $ignore_expression, $entity, $tagName) {
		// this one ensures proper nesting on the fly.
		$combine_string = '';
		$marker = $offset;
		$array_combine_strings = array();
		while(true) {
			$next_opening_position = strpos($string, $opening_string, $marker);
			if($next_opening_position === false) {
				//print('here03405056<br>');
				break;
			}
			// notice that when we first get in to this loop the next_opening_position should be zero.
			if($next_opening_position === $marker) {
			
			} else {
				$potentially_to_combine = substr($string, $marker, $next_opening_position - $marker);
				preg_match('/' . $ignore_expression . '/is', $potentially_to_combine, $matches39);
				if($matches39[0] === $potentially_to_combine) {
					$marker += strlen($potentially_to_combine);
					$combine_string .= $potentially_to_combine;
				} else {
					//print('here03405057<br>');
					break;
				}
			}
			$OString = OM::getOString($string, '<' . $tagName, $closing_string, $marker);
			$marker += strlen($OString);
			$combine_string .= $OString;
			$array_combine_strings[] = $combine_string;
		}
		// the $combine_string is here potentially improperly nested, so:
		while(true) {
			if(strpos(substr($combine_string, 1), $opening_string) !== false) { // if there is anything to combine
				$replaceString = $combine_string;
				// getAllTagStrings with an optional attributes parameter would be useful here. "getTagString" should be generalized to work with this (2009-08-18).
				$arrayOStrings = OM::getAllOStrings($replaceString, '<' . $tagName, $closing_string);
				foreach($arrayOStrings as $index => $value) {
					$OString = $value[0];
					if(strpos($OString, $opening_string) === 0) {
						$combinedOString = substr($OString, strlen($opening_string), strlen($OString) - strlen($opening_string) - strlen($closing_string));
						$replaceString = str_replace($OString, $combinedOString, $replaceString);
					}
				}
				$replaceString = str_replace('<' . $tagName, '<XXX9o9' . $tagName . '9o9XXX', $opening_string) . $replaceString . '</XXX9o9' . $tagName . '9o9XXX>';
				$new_string = $string;
				$new_string = str_replace($combine_string, $replaceString, $new_string);
				//print('$new_string 1: ');var_dump($new_string);
				$new_string = OM::ensure_proper_nesting_for_combine_inline($new_string, $entity);
				//print('$new_string 2: ');var_dump($new_string);
				if($new_string === false) {
					// cut back what we are trying to combine.
					unset($array_combine_strings[sizeof($array_combine_strings) - 1]);
					$combine_string = $array_combine_strings[sizeof($array_combine_strings) - 1];
					continue;
				}
				$new_string = str_replace('XXX9o9' . $tagName . '9o9XXX', $tagName, $new_string);
				break;
			} else {
				return false;
			}
		}
		return $new_string;
	}
	
	public function getTagString($string, $tagName, $offset = 0) {
		// first check for a self-closing tag
		preg_match('/<' . $tagName . '([^<>]*)\/>/s', $string, $matches, PREG_OFFSET_CAPTURE, $offset);
		$pos_first_open_tag = strpos($string, "<" . $tagName, $offset);
		$pos_self_closing = $matches[0][1];
		if($pos_self_closing === $pos_first_open_tag) { // then it is self-closing
			return $matches[0][0];
		} else {
			return OM::getOString($string, "<" . $tagName, "</" . $tagName . ">", $offset);
		}
	}	
	
	public function getSortedAllNonBlockContainingBlockOStrings_for_structure($string, $offset = 0) {
		$nonBlockContainingBlockOStrings = OM::getAllNonBlockContainingBlockOStrings_for_structure($string, $offset = 0);
		$preSortedNonBlockContainingBlockOStrings = array();
		//
		//$smallest_offset = 1000000000000000;
		foreach($nonBlockContainingBlockOStrings as $index => $value) {
			$preSortedNonBlockContainingBlockOStrings[$value[1]] = $value;
		}
		ksort($preSortedNonBlockContainingBlockOStrings);
		$sortedNonBlockContainingBlockOStrings = array();
		foreach($preSortedNonBlockContainingBlockOStrings as $index => $value) {
			$sortedNonBlockContainingBlockOStrings[] = $value;
		}
		return $sortedNonBlockContainingBlockOStrings;
	}
	
	public function getAllNonBlockContainingBlockOStrings_for_structure($string, $offset = 0) {
		$blockOStrings = OM::getAllBlockOStrings_for_structure($string, $offset);
		$nonBlockContainingBlockOStrings = array();
		foreach($blockOStrings as $index => $value) {
			$code = $value[0];
			$offset = $value[1];
			$subBlockOStrings = OM::getAllBlockOStrings_for_structure($code);
			if(sizeof($subBlockOStrings) > 1) { // exclude it
			
			} else {
				$nonBlockContainingBlockOStrings[] = array($code, $offset);
			}
		}
		return $nonBlockContainingBlockOStrings;
	}
	
	public function getAllBlockOStrings_for_structure($string, $offset = 0) {
		//$string = substr($string, $offset); // bad
		$arrayOStrings = array();
		//if(DTD::getDTDfile() === 'DTD/html5.dtd') { // HTML5
		//	$blockString = DTD::getAllElements();
		//} else {
		//	$blockString = DTD::getBlock();
		//}
		$blockString = 'table|blockquote|p|h1|h2|h3|h4|h5|h6|pre'; // notice that these are now sorted so that each element cannot be contained by those to the right of it (roughly; pre is tough...)
		$blockNames = explode("|", $blockString);
		//print('blockNames: ');var_dump($blockNames);exit(0);
		foreach($blockNames as $blockName) {
			$opening_string = "<" . $blockName;
			$closing_string = "</" . $blockName . ">";
			preg_match_all('/' . $opening_string . '/', $string, $matches, PREG_OFFSET_CAPTURE, $offset);
			foreach($matches[0] as $index => $value) {
				$match = $value[0];
				$offset2 = $value[1];			
				$object_string = OM::getOString($string, $opening_string, $closing_string, $offset2);
				if($object_string !== false) {
					// disallow nesting in the page structure, such as <p>s in <table>s
					$contained_in_another_structuring_element = false;
					foreach($arrayOStrings as $index2 => $value2) {
						//print('$offset2: ');var_dump($offset2);
						//print('$value2[1]: ');var_dump($value2[1]);
						//print('$value2[1] + strlen($value2[0]): ');var_dump($value2[1] + strlen($value2[0]));
						if($offset2 > $value2[1] && $offset2 < $value2[1] + strlen($value2[0])) {
							//print('break<br>');
							$contained_in_another_structuring_element = true;
							break;
						}
					}
					if(!$contained_in_another_structuring_element) {
						$arrayOStrings[] = array($object_string, $offset2);
					}
				}
			}
		}
		$preSortedArrayOStrings = array();
		//
		//$smallest_offset = 1000000000000000;
		foreach($arrayOStrings as $index => $value) {
			$preSortedArrayOStrings[$value[1]] = $value;
		}
		ksort($preSortedArrayOStrings);
		$sortedArrayOStrings = array();
		foreach($preSortedArrayOStrings as $index => $value) {
			$sortedArrayOStrings[] = $value;
		}
		//print('$sortedArrayOStrings: ');var_dump($sortedArrayOStrings);
		return $sortedArrayOStrings;
	}
	
	public function getAllBlockOStrings($string, $offset = 0) {
		//$string = substr($string, $offset); // bad
		$arrayOStrings = array();
		if(DTD::getDTDfile() === 'DTD/html5.dtd') { // HTML5
			$blockString = DTD::getAllElements();
		} else {
			$blockString = DTD::getBlock() . '|li|td|th|caption';
		}
		$blockNames = explode("|", $blockString);
		//print('blockNames: ');var_dump($blockNames);
		foreach($blockNames as $blockName) {
			$opening_string = "<" . $blockName;
			$closing_string = "</" . $blockName . ">";
			preg_match_all('/' . $opening_string . '/', $string, $matches, PREG_OFFSET_CAPTURE, $offset);
			foreach($matches[0] as $index => $value) {
				$match = $value[0];
				$offset2 = $value[1];			
				$object_string = OM::getOString($string, $opening_string, $closing_string, $offset2);
				if($object_string !== false) {
					$arrayOStrings[] = array($object_string, $offset2);
				}
			}
		}
		return $arrayOStrings;
	}
	
	public function getAllBlockOStrings_for_normalize_indexical_content($string, $offset = 0) {
		//$string = substr($string, $offset); // bad
		$arrayOStrings = array();
		if(DTD::getDTDfile() === 'DTD/html5.dtd') { // HTML5
			$blockString = DTD::getAllElements();
		} else {
			$blockString = DTD::getBlock() . '|li|td|th|caption|a';
		}
		$blockNames = explode("|", $blockString);
		//print('blockNames: ');var_dump($blockNames);
		foreach($blockNames as $blockName) {
			$opening_string = "<" . $blockName;
			$closing_string = "</" . $blockName . ">";
			preg_match_all('/' . $opening_string . '/', $string, $matches, PREG_OFFSET_CAPTURE, $offset);
			foreach($matches[0] as $index => $value) {
				$match = $value[0];
				$offset2 = $value[1];			
				$object_string = OM::getOString($string, $opening_string, $closing_string, $offset2);
				if($object_string !== false) {
					$arrayOStrings[] = array($object_string, $offset2);
				}
			}
		}
		return $arrayOStrings;
	}
	
	public function getAllSpecificOStrings($string, $Os, $offset = 0) {
		$arrayOStrings = array();
		$Names = explode("|", $Os);
		foreach($Names as $Name) {
			$opening_string = "<" . $Name;
			$closing_string = "</" . $Name . ">";
			preg_match_all('/' . $opening_string . '/', $string, $matches, PREG_OFFSET_CAPTURE, $offset);
			foreach($matches[0] as $index => $value) {
				$match = $value[0];
				$offset2 = $value[1];			
				$object_string = OM::getOString($string, $opening_string, $closing_string, $offset2);
				if($object_string !== false) {
					$arrayOStrings[] = array($object_string, $offset2);
				}
			}
		}
		return $arrayOStrings;
	}
	
	public function getNonParentSpecificOStrings($string, $blockString, $offset = 0) {
		$arrayOStrings = array();
		preg_match_all('/<(' . $blockString . ')/', $string, $matches, PREG_OFFSET_CAPTURE, $offset);
		if(sizeof($matches[0]) === 0) {
			return false;
		}
		foreach($matches[0] as $index => $value) {
			$opening_string = $matches[0][$index][0];
			$closing_string = "</" . $matches[1][$index][0] . ">";
			$offset2 = $matches[0][$index][1];
			$object_string = OM::getOString($string, $opening_string, $closing_string, $offset2);
			if($object_string !== false) {
				preg_match_all('/<(' . $blockString . ')/', $object_string, $matches66, PREG_OFFSET_CAPTURE, 1);
				if(sizeof($matches66[0]) > 0) { // then skip it
					continue;
				}
				$arrayOStrings[] = array($object_string, $offset2);
			}
		}
		return $arrayOStrings;
	}
	
	public function blockify_list_items_with_subitems($string) {
		$open_comment = '<!-- XXX9o9blockifyListItemOpen9o9XXX -->';
		$close_comment = '<!-- XXX9o9blockifyListItemClose9o9XXX -->';
		$strlen_open_comment = strlen($open_comment);
		$strlen_close_comment = strlen($close_comment);
		//print('pre list blockify: ' . $string);
		$do_some_work = true;
		while($do_some_work) {
			$do_some_work = false;
			$lis = OM::getAllOStrings($string, '<li', '</li>');
			//print('lis: ');var_dump($lis);exit(0);
			$counter = sizeof($lis) - 1;
			while($counter > -1) {
				$code = $lis[$counter][0];
				$offset = $lis[$counter][1];
				$strpos_close_li = strpos($code, '</li>');
				$strpos_open_ul = strpos($code, '<ul');
				if(is_numeric($strpos_close_li) && is_numeric($strpos_open_ul) && $strpos_open_ul < $strpos_close_li) {
					$blockified_list_item = $code;
					$blockified_list_item = substr($blockified_list_item, 0, strlen($blockified_list_item) - 5) . $close_comment; // swap the closing li for the closing comment
					$blockified_list_item = substr($blockified_list_item, 0, $strpos_open_ul) . '</li>' . $open_comment . substr($blockified_list_item, $strpos_open_ul);
					$string = substr($string, 0, $offset) . $blockified_list_item . substr($string, $offset + strlen($code));
					$do_some_work = true;
					break;
				}
				$counter--;
			}
		}
		//print('post list blockify: ' . $string);exit(0);
		return $string;
	}
	
	public function deblockify_list_items_with_subitems($string) {
		$string = str_replace('</li><!-- XXX9o9blockifyListItemOpen9o9XXX -->', '', $string);
		$string = str_replace('<!-- XXX9o9blockifyListItemClose9o9XXX -->', '</li>', $string);
		return $string;
	}
	
	public function getTOCOStringsArray($string, $offset = 0, $pure = false) {
		$this->finding_TOC = true;
		//$string = OM::blockify_list_items_with_subitems($string);
		$blockString = DTD::getBlock() . '|li|td|th|caption';
		$blockString = str_replace('|ul', '', $blockString);
		$blockString = str_replace('|ol', '', $blockString);
		$blockString = str_replace('|dl', '', $blockString);
		$position_after_TOC = $offset;
		//while(preg_match('/<(' . $blockString . ')([^<>]*)>(\s*<[^<>]*>){0,}\s*((TABLE OF CONTENTS)|(Table des mati)|(TABLE DES MATI)|(INDEX))/is', $string, $matches, PREG_OFFSET_CAPTURE, $position_after_TOC)) {
		while(preg_match('/<(' . $blockString . ')([^<>]*)>(\s*<[^<>]*>){0,}\s*((TABLE OF CONTENTS)|(Table des mati)|(TABLE DES MATI))/is', $string, $matches, PREG_OFFSET_CAPTURE, $position_after_TOC)) { // index removed because of a single instance (2012-01-08)
			$position_of_TOC = $matches[4][1];
			$ancestryArray = OM::getAncestryArray($string, $position_of_TOC);
			//print('$ancestryArray: ');var_dump($ancestryArray);
			foreach($ancestryArray as $index => $value) {
				$tag_piece = $value[1];
				if(strpos($tag_piece, ' href=') !== false) {
					//print('false TOC pre<br>');
					$position_after_TOC = $position_of_TOC + strlen($matches[0][0]);
					continue 2;
				}
			}
			break;
		}
		//print('matches: ');var_dump($matches);
		if(strlen($matches[0][0]) === 0) {
			$this->foundTOC = false;
			return false;
		}
		//foreach($matches[0] as $index => $value) {
			//$position_of_TOC = $matches[4][$index][1];
			//$position_of_TOC = $matches[4][1];
			//print('from TOC: ');var_dump(substr($string, $position_of_TOC));exit(0);
			$blockArray = OM::getContainingBlock($string, $position_of_TOC, $blockString);
			//print('TOC block array: ');var_dump($blockArray);
			$begin_position = $position_of_TOC - $blockArray[1];
			$heading_string = $blockArray[0];
			$TOCBlockString = 'p|h1|h2|h3|h4|h5|h6|li|a';
			$TOCOStringsArray = OM::getNonParentSpecificOStringsForTOC($string, $TOCBlockString, $begin_position, $pure);
		//}
		//print("TOCOStringsArray 0.5: ");var_dump($TOCOStringsArray);exit(0);
		//print("TOCOStringsArray 1: ");var_dump($TOCOStringsArray);exit(0);
		// ignore page number "header"
		if(isset($TOCOStringsArray[1][0]) && strtolower(ReTidy::tagless($TOCOStringsArray[1][0])) === "page") {
			//$TOCOStringsArray = ReTidy::array_delete($TOCOStringsArray, 1, true);
			// Do not delete it; instead, change its level
			$TOCOStringsArray[1][2] = "?";
		}
		
		//print("TOCOStringsArray 2: ");var_dump($TOCOStringsArray);exit(0);
		// here try (and it may be quite important) to eliminate stuff that is the start of the content
		$look_for_extra_stuff = true;
		while($look_for_extra_stuff) {
			$reversedTOCOStringsArray = array_reverse($TOCOStringsArray);
			//print('$reversedTOCOStringsArray: ');var_dump($reversedTOCOStringsArray);
			foreach($reversedTOCOStringsArray as $reversed_index => $reversed_value) {
				// while we have <h1>s at the bottom of the table of contents; remove them
				if(strpos($reversed_value[0], "<h1") !== false) { // should we also do other headings?
					$TOCOStringsArray = ReTidy::array_delete($TOCOStringsArray, sizeof($TOCOStringsArray) - 1 - $reversed_index, true);
					$look_for_extra_stuff = true;
					continue 2;
				}
				$item_text = ReTidy::trim_nbsp(ReTidy::tagless($reversed_value[0]));
				if(strlen($item_text) < 4) {
					// we say this is too short to be a heading and maybe a footnote in the first paragraph.
					$TOCOStringsArray = ReTidy::array_delete($TOCOStringsArray, sizeof($TOCOStringsArray) - 1 - $reversed_index, true);
					$look_for_extra_stuff = true;
					continue 2;
				}
				$look_for_extra_stuff = false;
				break;
			}
		}
		//print("TOCOStringsArray 3: ");var_dump($TOCOStringsArray);exit(0);
		// if the last TOC item equals the first TOC item, we say that we are falsely catching the first <h2> as part of the table of contents.
		//if(ReTidy::indexical_match($TOCOStringsArray[1][0], $TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0])) {
		//	$TOCOStringsArray = ReTidy::array_delete($TOCOStringsArray, sizeof($TOCOStringsArray) - 1);
		//}
		// not quite good enough; it is unlikely, though possible to have subheadings as the first items in the first main heading... 
		// so, get the last match of the first TOC item and if every subsequent item from it matches every subsequent item from the first TOC item, then remove them.
		$counter4 = 0;
		//$first_item = $TOCOStringsArray[$counter4][0];
		// the last item could somehow allow for intra-TOC linking I suppose; maybe something like level = TOC1, TOC2, etc...
		//print('here045-<br>');
		while(isset($TOCOStringsArray[$counter4]) && ($TOCOStringsArray[$counter4][2] === "pre" || $TOCOStringsArray[$counter4][2] === "?" || stripos(ReTidy::trim_nbsp(ReTidy::tagless($TOCOStringsArray[$counter4][0])), "list") === 0)) {
			$counter4++;
			//$first_item = $TOCOStringsArray[$counter4][0];
		}
		//print('counter4 1: ');var_dump($counter4);exit(0);
		$counter5 = 1;
		$size = sizeof($TOCOStringsArray);
		$counter = $size - 1;
		while($counter >= $counter4 + $counter5 && !ReTidy::indexical_match($TOCOStringsArray[$counter4][0], $TOCOStringsArray[$counter4 + $counter5][0])) {
			if($counter === $counter4 + $counter5) {
				$counter5 = 1;
				$counter4++;
				continue;
			}
			$counter5++;
		}
		// counter4 has the index of the first item with an inter-TOC indexical match
		$item = $TOCOStringsArray[$size - 1][0];
		while($counter > $counter4 && !ReTidy::indexical_match($TOCOStringsArray[$counter4][0], $item)) {
			//print('here38589509<br>');
			$counter--;
			$item = $TOCOStringsArray[$counter][0];
		}
		//print('here38589510<br>');
		//print('counter: ');var_dump($counter);
		//print('counter4 2: ');var_dump($counter4);
		if($counter !== $counter4) {
			//print('here38589511<br>');
			$initial_difference = $difference = $size - $counter;
			$item1 = $TOCOStringsArray[$counter4 + $difference - 1][0];
			$item2 = $TOCOStringsArray[$counter + $difference - 1][0];
			while($difference > 0 && ReTidy::indexical_match($item1, $item2)) {
				//print('here38589512<br>');
				$difference--;
				$item1 = $TOCOStringsArray[$counter4 + $difference - 1][0];
				$item2 = $TOCOStringsArray[$counter + $difference - 1][0];
			}
			// only curtail to TOC if we did not find any discrepancies between what with think are the parts of the beginning content and the beginning TOC items
			//print('here38589513<br>');
			if($difference === 0) {
				//print('here38589514<br>');
				$new_array = array();
				$key_to_stop_at = $size - $initial_difference;
				$counter3 = 0;
				while($counter3 < $key_to_stop_at) {
					//print('here38589515<br>');
					$new_array[] = $TOCOStringsArray[$counter3];
					$counter3++;
				}
				$TOCOStringsArray = $new_array;
			}
		}
		//print("TOCOStringsArray 5: ");var_dump($TOCOStringsArray);exit(0);
		// TOC "sub-headings"...
		$size = sizeof($TOCOStringsArray);
		$counter = 0;
		$found_first_with_order_indicator = false;
		while($counter < $size) {
			$order_indicator_array = ReTidy::get_order_indicator_type(ReTidy::tagless($TOCOStringsArray[$counter][0]));
			if(!$found_first_with_order_indicator && ($order_indicator_array[0] === "number" || $order_indicator_array[0] === "complex number")) {
				$found_first_with_order_indicator = true;
			}
			if($found_first_with_order_indicator) {
				if(strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "list of tables" ||
				strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "liste des tableaux" ||
				strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "list of figures" ||
				strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "liste des figures" ||
				strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "list of acronyms" ||
				strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "liste des acronymes"
				//strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "list of appendices" || 
				//strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "liste des annexes" ||
				//strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "appendices" || 
				//strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "annexes"
				) {
					$TOCOStringsArray[$counter][2] = "?";
				}
			}
			$counter++;
		}
		//print("TOCOStringsArray 5.5: ");var_dump($TOCOStringsArray);exit(0);
		$counter = 0;
		// we may have to explode on things other than periods (probably styleAgnostic characters)
		while($counter < $size) {
			//print('TOC item: ');var_dump($TOCOStringsArray[$counter][0]);print('<br>');
			//var_dump($TOCOStringsArray[$counter][1]);print('<br>');
			//var_dump($TOCOStringsArray[$counter][2]);print('<br>');
			if($TOCOStringsArray[$counter][2] === "?" &&
			(strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "list of tables" ||
			strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "liste des tableaux")
			) {
				//print('here39495065-<br>');
				$counter++;
				while($counter < $size && $TOCOStringsArray[$counter][2] !== "?") {
					if(preg_match('/Table(au)*\s*([^\s:&]+)/is', ReTidy::tagless($TOCOStringsArray[$counter][0]), $table_indicator_matches)) {
						$level = "tab";
						$numbers_from_indicator = explode(".", $table_indicator_matches[2]);
						foreach($numbers_from_indicator as $number) {
							if(strlen($number) > 0) {
								$level .= "_". $number;
							}
						}
						//$level = substr($level, 0, strlen($level) - 1);
						$TOCOStringsArray[$counter][2] = $level;
					} else {
						$TOCOStringsArray[$counter][2] = "?";
					}
					$counter++;
				}
				continue;
			}
			if($TOCOStringsArray[$counter][2] === "?" &&
			(strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "list of figures" ||
			strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "liste des figures")
			) {
				//print('here39495066-<br>');
				$counter++;
				while($counter < $size && $TOCOStringsArray[$counter][2] !== "?") {
					if(preg_match('/Figure\s*([^\s:]+)/is', ReTidy::tagless($TOCOStringsArray[$counter][0]), $table_indicator_matches)) {
						$level = "fig";
						$numbers_from_indicator = explode(".", $table_indicator_matches[1]);
						foreach($numbers_from_indicator as $number) {
							if(strlen($number) > 0) {
								$level .= "_". $number;
							}
						}
						//$level = substr($level, 0, strlen($level) - 1);
						$TOCOStringsArray[$counter][2] = $level;
					} else {
						$TOCOStringsArray[$counter][2] = "?";
					}
					$counter++;
				}
				continue;
			}
			/*
			print('here304---4----4-----4-<br>');
			if(strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "list of appendices" || 
			strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "liste des annexes" ||
			strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "appendices" || 
			strtolower(ReTidy::tagless($TOCOStringsArray[$counter][0])) === "annexes"
			) {
				print('here304---4----4-----5-<br>');
				$counter++;
				while($counter < $size && $TOCOStringsArray[$counter][2] !== "?") {
					print('here304---4----4-----6-<br>');
					if(preg_match('/(appendix|annexe)\s*([^\s:]+)/is', ReTidy::tagless($TOCOStringsArray[$counter][0]), $table_indicator_matches)) {
						$level = "a";
						$numbers_from_indicator = explode(".", $table_indicator_matches[2]);
						foreach($numbers_from_indicator as $number) {
							if(strlen($number) > 0) {
								$level .= "_" . $number;
							}
						}
						//$level = substr($level, 0, strlen($level) - 1);
						$TOCOStringsArray[$counter][2] = $level;
					} else {
						$TOCOStringsArray[$counter][2] = "?";
					}
					$counter++;
				}
				continue;
			}*/
			$counter++;
		}
		//print("TOCOStringsArray 6: ");var_dump($TOCOStringsArray);exit(0);
		// for the case when there is document navigation right after the TOC (such as when the TOC is the whole page and document navigation exists)
		while(
		mb_strtolower(ReTidy::tagless($TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0])) === "table of contents" ||
		mb_strtolower(ReTidy::tagless($TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0])) === "previous" ||
		mb_strtolower(ReTidy::tagless($TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0])) === "next" ||
		mb_strtolower(ReTidy::tagless($TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0])) === "table des matières" ||
		mb_strtolower(ReTidy::tagless($TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0])) === "table des mati&egrave;res" ||
		mb_strtolower(ReTidy::tagless($TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0])) === "précédente" ||
		mb_strtolower(ReTidy::tagless($TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0])) === "pr&eacute;c&eacute;dente" ||
		mb_strtolower(ReTidy::tagless($TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0])) === "précédent" ||
		mb_strtolower(ReTidy::tagless($TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0])) === "pr&eacute;c&eacute;dent" ||
		mb_strtolower(ReTidy::tagless($TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0])) === "prochaine"
		) {
			$TOCOStringsArray = ReTidy::array_delete($TOCOStringsArray, sizeof($TOCOStringsArray) - 1, true);
			//print('$TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0]: ');var_dump($TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0]);
			//print('mb_strtolower(ReTidy::tagless($TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0])): ');var_dump(mb_strtolower(ReTidy::tagless($TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0])));
			//print('mb_strtolower(ReTidy::tagless($TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0])) == "précédente": ');var_dump(mb_strtolower(ReTidy::tagless($TOCOStringsArray[sizeof($TOCOStringsArray) - 1][0])) == "précédente");
		}
		//print("TOCOStringsArray 7: ");var_dump($TOCOStringsArray);exit(0);
		//$string = OM::deblockify_list_items_with_subitems($string);
		$this->foundTOC = true;
		$this->finding_TOC = false;
		return $TOCOStringsArray;
	}
	
	public function deblockForTOC($string) {
		$blockArray = array("p", "h1", "h2", "h3", "h4", "h5", "h6", "li", "a");
		foreach($blockArray as $block) {
			$string = preg_replace('/<' . $block . '[^<>]*?>(.*?)<\/' . $block . '>/is', '$1', $string);
		}
		return $string;
	}
	
	public function getAncestryArray($string, $offset = false) {
		if($offset === false) {
			$offset = strlen($string);
		}
		preg_match_all('/<(\/){0,1}(\w+)[^<>]*?(\/){0,1}>/is', substr($string, 0, $offset), $matches, PREG_OFFSET_CAPTURE);
		$ancestryArray = array();
		$size = sizeof($matches[0]);
		foreach($matches[0] as $index => $value) {
			$tag_piece = $matches[0][$index][0];
			$rev_tag_piece = strrev($tag_piece);
			//var_dump($tag_piece);exit(0);
			if($tag_piece[1] === "!" || $rev_tag_piece[1] === "-") { // then it is a comment (or doctype)
				continue;
			}
			if($tag_piece[1] === "?" || $rev_tag_piece[1] === "?") { // then it is a processing instruction
				continue;
			}
			if($matches[3][$index][0] === "/") { // it is self-closing
				continue;
			}
			if($matches[1][$index][0] === "/") { // it is a closing tag
				continue;
			}
			$level = 0;
			$tagName = $matches[2][$index][0];
			$counter = $index + 1;
			while($counter < $size) {
				if($matches[3][$counter][0] === "/") { // it is self-closing
					$counter++;
					continue;
				}
				if($matches[1][$counter][0] === "/") { // it is a closing tag
					$tagName2 = $matches[2][$counter][0];
					if($tagName === $tagName2) { 
						if($level === 0) { // then we found the matching closing tag
							continue 2;
						} else {
							$level--;
							$counter++;
							continue;
						}
					}
				}
				$tagName2 = $matches[2][$counter][0];
				if($tagName === $tagName2) { // then we found another opening tag with the same tagName
					$level++;
				}
				$counter++;
			}
			// if we get to here, then we have not found the closing tag that matches the opening tag we are working on
			// so add it to the ancestry array
			$ancestryArray[] = array($tagName, $tag_piece, $matches[0][$index][1]);
		}
		return $ancestryArray;
	}
	
	public function getReverseAncestryArray($string, $offset = 0) {
		preg_match_all('/<(\/){0,1}(\w+)[^<>]*?(\/){0,1}>/is', substr($string, $offset), $matches, PREG_OFFSET_CAPTURE);
		$reverseAncestryArray = array();
		foreach($matches[0] as $index => $value) {
			$tag_piece = $matches[0][$index][0];
			$rev_tag_piece = strrev($tag_piece);
			if($tag_piece[1] === "!" || $rev_tag_piece[1] === "-") { // then it is a comment (or doctype)
				continue;
			}
			if($tag_piece[1] === "?" || $rev_tag_piece[1] === "?") { // then it is a processing instruction
				continue;
			}
			if($matches[3][$index][0] === "/") { // it is self-closing
				continue;
			}
			if($matches[1][$index][0] !== "/") { // it is an opening tag
				continue;
			}
			$level = 0;
			$tagName = $matches[2][$index][0];
			$counter = $index - 1;
			while($counter > -1) {
				if($matches[3][$counter][0] === "/") { // it is self-closing
					$counter--;
					continue;
				}
				if($matches[1][$counter][0] !== "/") { // it is an opening tag
					$tagName2 = $matches[2][$counter][0];
					if($tagName === $tagName2) { 
						if($level === 0) { // then we found the matching opening tag
							continue 2;
						} else {
							$level++;
							$counter--;
							continue;
						}
					}
				}
				$tagName2 = $matches[2][$counter][0];
				if($tagName === $tagName2) { // then we found another opening tag with the same tagName
					$level--;
				}
				$counter--;
			}
			// if we get to here, then we have not found the opening tag that matches the closing tag we are working on
			// so add it to the ancestry array
			$reverseAncestryArray[] = array($tagName, $tag_piece, $matches[0][$index][1]);
		}
		return $reverseAncestryArray;
	}
	
	public function cleanTOCOString($string) {
		if(stripos($string, ' href="') === false) {
			$string = preg_replace('/<(\/){0,1}li[^<>]*?>/is', '', $string);
			$string = str_replace('<ol', '', $string);
			$string = str_replace('<ul', '', $string);
		}
		$string = trim($string);
		return $string;
	}
	
	public function addOStringToArray($object_string, &$arrayOStrings, $offset2, $level) {
		$object_string = ReTidy::acronyms_and_abbr_retaining_offsets_decode($object_string);
		$object_string = OM::cleanTOCOString($object_string);
		$arrayOStrings[] = array($object_string, $offset2, $level);
	}
	
	public function getNonParentSpecificOStringsForTOC($string, $blockString, $offset = 0, $pure = false) {
		//print('here690568956<br>');exit(0);
		// definately another hack; thanks to the HTML people
		// make <acronym>s and <abbr> not interfere with the link <a> object string finder... (not altering the offsets of anything)
		$string = ReTidy::acronyms_and_abbr_encode_retaining_offsets($string);
		//var_dump($blockString);
		$subheadings_counter = 0;
		$arrayOStrings = array();
		//print('string: ' . $string);exit(0);
		preg_match_all('/<(' . $blockString . ')(\s|>)/is', $string, $matches, PREG_OFFSET_CAPTURE, $offset);
		//print('matches: ');var_dump($matches);
		if(sizeof($matches[0]) === 0) {
			return false;
		}
		$preed_first = false;
		$did_first_after_pre = false;
		$found_bold = false;
		//$offset2 = $offset;
		foreach($matches[0] as $index => $value) {
			//print('$value: ');var_dump($value);
			//print('here3894894509<br>');
			$opening_string = $matches[0][$index][0];
			$offset2 = $matches[0][$index][1];
			if(strpos($opening_string, '<li') === 0) { // we have to allow list items with sub-lists to be indexical
				$substr = substr($string, $offset2);
				$strpos_close_li = strpos($substr, '</li>');
				$strpos_open_ol = strpos($substr, '<ol');
				$strpos_open_ul = strpos($substr, '<ul');
				$strpos_open_dl = strpos($substr, '<dl');
				$array_possible_closes = array();
				if($strpos_close_li !== false) {
					$array_possible_closes[$strpos_close_li] = 'close li';
				}
				if($strpos_open_ol !== false) {
					$array_possible_closes[$strpos_open_ol] = 'open ol';
				}
				if($strpos_open_ul !== false) {
					$array_possible_closes[$strpos_open_ul] = 'open ul';
				}
				if($strpos_open_dl !== false) {
					$array_possible_closes[$strpos_open_dl] = 'open dl';
				}
				//print('$array_possible_closes 1:');var_dump($array_possible_closes);
				ksort($array_possible_closes);
				//print('$array_possible_closes 2:');var_dump($array_possible_closes);
				foreach($array_possible_closes as $list_nesting_index => $list_nesting_value) {
					if($list_nesting_value == 'open ol') { // the use the open ol as the closing tag
						//print('open ol<br>');
						$closing_string = '<ol';
					} elseif($list_nesting_value == 'open ul') { // the use the open ul as the closing tag
						//print('open ul<br>');
						$closing_string = '<ul';
					} elseif($list_nesting_value == 'open dl') { // the use the open dl as the closing tag
						//print('open dl<br>');
						$closing_string = '<dl';
					} else { // the use the close li as the closing tag
						//print('close li<br>');
						$closing_string = '</li>';
					}
					break; // get the first one
				}
			} else {
				$closing_string = "</" . $matches[1][$index][0] . ">";
			}
			//print('$opening_string: ');var_dump($opening_string);
			//print('$closing_string: ');var_dump($closing_string);
			//print('substr47596: ');var_dump(substr($string, $offset2, $list_nesting_index - $offset2));
			//print('substr47597: ');var_dump(substr($string, $offset2));
			//print('substr47598: ');var_dump(substr($string, $offset2, $list_nesting_index));
			$object_string = OM::getOString($string, $opening_string, $closing_string, $offset2);
			//print('object string__: ');var_dump(substr($string, $offset2, strlen($object_string)));
			//print('object string: ');var_dump($object_string);
			if($object_string !== false) {
				if(!$pure && (strpos($object_string, '#chp') !== false || strpos($object_string, 'page-') !== false)) { // CED-style TOC
					//print("heer20202-1<br>\r\n");
					$order_indicator = "";
					preg_match_all('/<div class="numero-list">(.*?)<\/div>/is', substr($string, 0, $offset2), $CED_matches, PREG_OFFSET_CAPTURE);
					//print('$CED_matches: ');var_dump($CED_matches);
					// ensure that this belongs to the same TOC item
					$string3 = $CED_matches[0][sizeof($CED_matches[0]) - 1][0];
					$string4 = $CED_matches[1][sizeof($CED_matches[0]) - 1][0];
					$offset3 = $CED_matches[0][sizeof($CED_matches[0]) - 1][1];
					$substring_after_order_indicator = substr($string, $offset3 + strlen($string3), $offset2 - $offset3 - strlen($string3));
					//print('$substring_after_order_indicator: ');var_dump($substring_after_order_indicator);
					if(strlen($string4) > 0 && strpos($substring_after_order_indicator, '</div>') === false) {
						//print("heer20202-1-1<br>\r\n");
						$order_indicator = ReTidy::trim_nbsp(ReTidy::tagless($string4));
						//print('order_indicator: ');var_dump($order_indicator);
						if(strlen($order_indicator) > 0) {
							$pos_end_opening_tag = strpos($object_string, ">");
							$object_string = substr($object_string, 0, $pos_end_opening_tag + 1) . $order_indicator . " " . substr($object_string, $pos_end_opening_tag + 1);
						}
						// now get the level
						ReTidy::preg_match_last('/<div class="row-list indent([0-9])">/is', substr($string, 0, $offset2), $CED_level_matches);
						//$object_string = ReTidy::acronyms_and_abbr_retaining_offsets_decode($object_string);
						//$arrayOStrings[] = array($object_string, $offset2, $CED_level_matches[1] + 1);
						OM::addOStringToArray($object_string, $arrayOStrings, $offset2, $CED_level_matches[1] + 1);
					} else {
						//print("heer20202-1-2<br>\r\n");
						// just keep the object string as it is?
						//$object_string = ReTidy::acronyms_and_abbr_retaining_offsets_decode($object_string);
						//$arrayOStrings[] = array($object_string, $offset2, 2);
						OM::addOStringToArray($object_string, $arrayOStrings, $offset2, 2);
					}
					continue;
				} else { 
					//print("heer20202-2<br>\r\n");
					preg_match_all('/<(' . $blockString . ')/', $object_string, $matches66, PREG_OFFSET_CAPTURE, 1);
					$size = sizeof($matches66[0]);
					$subheadings_counter += $size;
					if($size > 0) { // then skip it
						//print("heer20202-4<br>\r\n");
						$subheadings_counter = 0;
						continue;
					}
				}
				//print('here3894894510<br>');
				if(strlen(ReTidy::trim_nbsp(ReTidy::tagless($object_string))) === 0) {
					// ignore stuff like empty tags
					continue;
				}
				if(!ReTidy::isIndexical($object_string)) {
					//print("heer20202-3<br>\r\n");
					break;
				}
				//print('here3894894511<br>');
				if(!$preed_first) {
					$preed_first = true;
					//if($offset2 !== $offset) {
					/*	preg_match('/<(\w+)[^<>]*?>(.*?)<\/\1>/is', substr($string, $offset, $offset2 - $offset), $first_tag_matches);
						$first_tag = $first_tag_matches[0];
						$arrayOStrings[] = array($first_tag, $offset, "pre");
					} else {*/
						//$object_string = ReTidy::acronyms_and_abbr_retaining_offsets_decode($object_string);
						//$arrayOStrings[] = array($object_string, $offset2, "pre");
						OM::addOStringToArray($object_string, $arrayOStrings, $offset2, "pre");
						continue;
					//}
				}
				//print('here3894894512<br>');
				if(!$did_first_after_pre) {
					$firsts_list_count = 0;
					$ancestryArray = OM::getAncestryArray($string, $offset2);
					foreach($ancestryArray as $ancestor_index => $ancestor_value) {
						if($ancestryArray[$ancestor_index][0] === "ol" || $ancestryArray[$ancestor_index][0] === "ul") {
							$firsts_list_count++;
						}
					}
					$did_first_after_pre = true;
				}
				//print('here3894894513<br>');
				$determined_heading_level = ReTidy::determine_heading_level($object_string);
				if($determined_heading_level !== false) {
					//$object_string = ReTidy::acronyms_and_abbr_retaining_offsets_decode($object_string);
					//$arrayOStrings[] = array($object_string, $offset2, $determined_heading_level);
					OM::addOStringToArray($object_string, $arrayOStrings, $offset2, $determined_heading_level);
					continue;
				}
				//print('here3894894514<br>');
				$beginning_of_TOC_item = OM::getToBeginningOfTOCItem($string, $offset2);
				if(strpos($beginning_of_TOC_item, "margin-left") !== false) { // there is a div that indents this content, then call it a subheading.
					//print('here3894894515<br>');
					//$object_string = ReTidy::acronyms_and_abbr_retaining_offsets_decode($object_string);
					//$arrayOStrings[] = array($object_string, $offset2, 3); // we guess this is a subheading
					OM::addOStringToArray($object_string, $arrayOStrings, $offset2, 3);
				} else {
					//print('here3894894516<br>');
					$list_count = 0;
					$ancestryArray = OM::getAncestryArray($string, $offset2);
					foreach($ancestryArray as $ancestor_index => $ancestor_value) {
						if($ancestryArray[$ancestor_index][0] === "ol" || $ancestryArray[$ancestor_index][0] === "ul") {
							$list_count++;
						}
					}
					if($list_count > 0) {
						// then it's in a nested list
						//$object_string = ReTidy::acronyms_and_abbr_retaining_offsets_decode($object_string);
						//$arrayOStrings[] = array($object_string, $offset2, $list_count - $firsts_list_count + 2); // we guess this is a subheading
						OM::addOStringToArray($object_string, $arrayOStrings, $offset2, $list_count - $firsts_list_count + 2);
						continue;
					} else {
						if($subheadings_counter > 0) {
							//$object_string = ReTidy::acronyms_and_abbr_retaining_offsets_decode($object_string);
							//$arrayOStrings[] = array($object_string, $offset2, 3); // we guess this is a subheading
							OM::addOStringToArray($object_string, $arrayOStrings, $offset2, 3);
							$subheadings_counter--;
							continue;
						} else {
							if(strpos($beginning_of_TOC_item, "bold") !== false || strpos($beginning_of_TOC_item, "<strong") !== false) {
								$found_bold = true;
								//$object_string = ReTidy::acronyms_and_abbr_retaining_offsets_decode($object_string);
								//$arrayOStrings[] = array($object_string, $offset2, 2); // we guess this is a main section heading
								OM::addOStringToArray($object_string, $arrayOStrings, $offset2, 2);
								continue;
							}
							if($found_bold) {
								//$object_string = ReTidy::acronyms_and_abbr_retaining_offsets_decode($object_string);
								//$arrayOStrings[] = array($object_string, $offset2, 3); // we guess this is a subheading
								OM::addOStringToArray($object_string, $arrayOStrings, $offset2, 3);
							} else {
								//$object_string = ReTidy::acronyms_and_abbr_retaining_offsets_decode($object_string);
								//$arrayOStrings[] = array($object_string, $offset2, 2); // we guess this is a main section heading
								OM::addOStringToArray($object_string, $arrayOStrings, $offset2, 2);
							}
						}
					}
				}
			}
		}
		//print('arrayOStrings: ');var_dump($arrayOStrings);exit(0);
		return $arrayOStrings;
	}
	
	public function getToBeginningOfTOCItem($string, $offset) {
		ReTidy::preg_match_last('/(\s*<[^<>]+>\s*){1,}/is', substr($string, 0, $offset), $matches);
		$beginning_of_TOC_item = $matches[0];
		return $beginning_of_TOC_item;
	}
	
	public function getAllOStrings($string, $opening_string, $closing_string, $offset = 0) {
		$arrayOStrings = array();
		$escaped_opening_string = ReTidy::preg_escape($opening_string);
		preg_match_all('/' . $escaped_opening_string . '/', $string, $matches, PREG_OFFSET_CAPTURE, $offset);
		foreach($matches[0] as $index => $value) {
			$offset2 = $value[1];			
			$object_string = OM::getOString($string, $opening_string, $closing_string, $offset2);
			if($object_string !== false) {
				// notice that offset2 is the offset inside the substring.
				$arrayOStrings[] = array($object_string, $offset2);
			}
		}
		return $arrayOStrings;
	}
	
	public function getAllTagPieces($string, $offset = 0) {
		//$string = substr($string, $offset); // bad
		//print('$string: ');var_dump($string);exit(0);
		$arrayTagPieces = array();
		preg_match_all('/<[^<>]+>/is', $string, $matches, PREG_OFFSET_CAPTURE, $offset);
		foreach($matches[0] as $index => $value) {
			$match = $value[0];
			$offset2 = $value[1];
			$arrayTagPieces[] = array($match, $offset2);
		}
		//print('$arrayTagPieces: ');var_dump($arrayTagPieces);exit(0);
		return $arrayTagPieces;
	}

	public function getAllTags($string, $offset = 0) {
		//$string = substr($string, $offset); // bad
		$arrayOStrings = array();
		// it should be mentioned that "tags" such as CDATA regions will not be caught since we are only looking for words rather than non-spaces
		preg_match_all('/<(\w+)[\s>]/is', $string, $matches, PREG_OFFSET_CAPTURE, $offset);
		foreach($matches[0] as $index => $value) {
			$match = $value[0];
			$offset2 = $value[1];
			$tagName = $matches[1][$index][0];
			$object_string = OM::getTagString($string, $tagName, $offset2);
			if($object_string !== false) {
				$arrayOStrings[] = array($object_string, $offset2);
			}
		}
		return $arrayOStrings;
	}
	
	public function getAllTagsAtThisLevel($string, $offset = 0) {
		//$string = substr($string, $offset); // bad
		$arrayOStrings = array();
		// it should be mentioned that "tags" such as CDATA regions will not be caught since we are only looking for words rather than non-spaces
		preg_match_all('/<(\w+)[\s>]/is', $string, $matches, PREG_OFFSET_CAPTURE, $offset);
		foreach($matches[0] as $index => $value) {
			$match = $value[0];
			$offset2 = $value[1];
			// only add it if it is not contained in the previous tag
			if($offset2 > ($previous_offset + strlen($previous_object_string) - 1)) {
				$tagName = $matches[1][$index][0];
				$object_string = OM::getTagString($string, $tagName, $offset2);
				if($object_string !== false) {
					$arrayOStrings[] = array($object_string, $offset2);
					$previous_object_string = $object_string;
					$previous_offset = $offset2;
				}
			}
		}
		return $arrayOStrings;
	}
	
	public function explode_non_nested($explode_on, $string, $opening_string, $closing_string) {
		// notice that this will not include empty strings (such as if $explode_on occurs at the beginning or the end of the string)
		// which is different from explode.
		$array_ploded = array();
		$last_pos = 0 - strlen($explode_on);
		$strpos_non_nested = OM::strpos_non_nested($string, $explode_on, $opening_string, $closing_string);
		while($strpos_non_nested !== false) {
			$array_ploded[] = substr($string, $last_pos + strlen($explode_on), $strpos_non_nested - $last_pos - strlen($explode_on));
			$last_pos = $strpos_non_nested;
			if(strlen($string) > $strpos_non_nested + 1) {
				$strpos_non_nested = OM::strpos_non_nested($string, $explode_on, $opening_string, $closing_string, $strpos_non_nested + 1);
			} else {
				break;
			}
		}
		if(strlen($string) > $last_pos + strlen($explode_on)) {
			$array_ploded[] = substr($string, $last_pos + strlen($explode_on));
		}
		return $array_ploded;
	}
	
	public function strpos_non_nested($haystack, $needle, $opening_string, $closing_string, $offset = 0) {
		$strpos = strpos($haystack, $needle, $offset);
		while(OM::isNested($haystack, $needle, $strpos, $opening_string, $closing_string, $offset)) {
			$strpos = strpos($haystack, $needle, $strpos + 1);
		}
		return $strpos;
	}
	
	public function isNested($haystack, $needle, $needle_pos, $opening_string, $closing_string, $offset = 0) {
		$pos1 = strpos($haystack, $opening_string, $offset);
		if($pos1 > $needle_pos) {
			return false;
		}
		$OString = OM::getOString($haystack, $opening_string, $closing_string, $offset);
		if($needle_pos > $pos1 && $needle_pos < $pos1 + strlen($OString)) {
			return true;
		}
		return false;
	}
	
	public function buildAttributesArrayFromString($string) {
		// we assume double quotes as the delimiters for simplicity
		$attributesArray = array();
		preg_match_all('/ (\w*)="([^"]*?)"/is', $string, $matches);
		foreach($matches[0] as $index => $value) {
			$attributesArray[$matches[1][$index]] = $matches[2][$index];
		}
		return $attributesArray;
	}
	
	public function combineAttributesStringsWithoutOverwrite($string1, $string2) {
		// notice that this function is expecting attributes strings rather than full tag strings.
		$attributesArray1 = OM::buildAttributesArrayFromString($string1);
		$attributesArray2 = OM::buildAttributesArrayFromString($string2);
		$new_attributes_string = "";
		foreach($attributesArray1 as $attribute_name1 => $attribute_value1) {
			foreach($attributesArray2 as $attribute_name2 => $attribute_value2) {
				if($attribute_name1 === $attribute_name2) {
					if($attribute_name1 === "style") {
						$attribute_value1 = trim($attribute_value1);
						$attribute_value1 = ReTidy::ensureStyleInformationEndsProperly($attribute_value1);
						$attribute_value1 = $attribute_value1 . $attribute_value2;
					} elseif($attribute_name1 === "class" || $attribute_name1 === "headers") {
						$attribute_value1 = $attribute_value1 . " " . $attribute_value2;
					} elseif($attribute_name1 === "lang" || $attribute_name1 === "xml:lang") {
						// overwrite it.
						$attribute_value1 = $attribute_value2;
					} elseif($attribute_name1 === "id") { 
						// do nothing.
					} elseif($attribute_name1 === "align") { 
						// do nothing.
					} elseif($attribute_value1 === $attribute_value2) { 
						// do nothing.
					} else {
						// here we assume that other than class and style, there are no attributes that can be added...
						// this works for (the only case considered) align="" but something smarter would be better.
						// that is; something that refers to the DTD... I do not know if tidy can do this... (2009-02-03)
						// (2009-08-20) do nothing does not work for me... at the least assume that if this function is called, then 
						// it wants attributes to be applied...
						print("!!!<br>");
						print("Trying to combine two attribute strings but how to merge for this one is not accounted for437:<br>");
						print($string1);print("to: <br>");
						print($string2);print("<br>");
						print("!!!<br>");
					}
				}
			}
			$new_attributes_string .= ' ' . $attribute_name1 . '="' . $attribute_value1 . '"';
		}
		foreach($attributesArray2 as $attribute_name2 => $attribute_value2) {
			$skip_it = false;
			foreach($attributesArray1 as $attribute_name1 => $attribute_value1) {
				if($attribute_name1 === $attribute_name2) {
					// we have already done it so skip it.
					$skip_it = true;
					break;
				}
			}
			if(!$skip_it) {
				$new_attributes_string .= ' ' . $attribute_name2 . '="' . $attribute_value2 . '"';
			}
		}
		return $new_attributes_string;
	}

}

?>