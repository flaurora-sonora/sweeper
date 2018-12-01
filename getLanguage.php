<?php

//include('retidy.php');

function getLanguage($source) {
	$language = "unknown";
	if(strpos($source, "<body") !== false) {	
		$contents = $source;
	} else {
		$sourceIsAFilename = true;
		$array_non_filename_characters = array(/*'/', '\\', only disallowed for file and folder names; not paths */ ':', '*', '?', '"', '<', '>', '|');
		foreach($array_non_filename_characters as $non_filename_character) {
			if(strpos($source, $non_filename_character) !== false) {
				$contents = $source;
				$sourceIsAFilename = false;
				break;
			}
		}
		if($sourceIsAFilename) {
			$contents = file_get_contents($source);	
		}
		//  we could do something like the following if necessary
		//if(strlen($source) > 260) { // file and folder names shouldn't be longer than roughly 256 characters
		//		$slash_count = substr_count($source, '/');
	}
	//print('here374595969790870<br>');
	// method 1 to find the language of a file (look at the file extension) 
	// (notice that we are not limiting the search to the end of the filename)
	if($sourceIsAFilename === true) {
		// these are separate from the below since they are higher priority
		if (
		strpos(strtolower($source), "/eng/") != false || 
		strpos(strtolower($source), "/english/") != false
		) {
			return $language = "english";
		}
		if (
		strpos(strtolower($source), "/fra/") != false || 
		strpos(strtolower($source), "/french/") != false
		) {
			return $language = "french";
		}
		
		if (
		
		//strpos(strtolower($source), "e.htm") != false ||

		//strpos(strtolower($source), "_e.html") != false || 
		//strpos(strtolower($source), "-e.html") != false || 
		//strpos(strtolower($source), "-en.html") != false || 
		//strpos(strtolower($source), "_en.html") != false ||  
		//strpos(strtolower($source), "-eng.html") != false || 
		//strpos(strtolower($source), "_eng.html") != false ||  // redundant
		strpos(strtolower($source), "_e.htm") != false || 
		strpos(strtolower($source), "-e.htm") != false || 
		strpos(strtolower($source), "-en.htm") != false || 
		strpos(strtolower($source), "_en.htm") != false ||  
		strpos(strtolower($source), "-eng.htm") != false || 
		strpos(strtolower($source), "_eng.htm") != false || 
		strpos(strtolower($source), " eng.htm") != false || 		
		strpos(strtolower($source), "_e.xml") != false || 
		strpos(strtolower($source), "-e.xml") != false || 
		strpos(strtolower($source), "-en.xml") != false || 
		strpos(strtolower($source), "_en.xml") != false ||  
		strpos(strtolower($source), "-eng.xml") != false || 
		strpos(strtolower($source), "_eng.xml") != false || 
		strpos(strtolower($source), " eng.xml") != false || 
		strpos(strtolower($source), "_e.php") != false || 
		strpos(strtolower($source), "-e.php") != false || 
		strpos(strtolower($source), "-en.php") != false || 
		strpos(strtolower($source), "_en.php") != false ||  
		strpos(strtolower($source), "-eng.php") != false || 
		strpos(strtolower($source), "_eng.php") != false || 
		strpos(strtolower($source), " eng.php") != false || 
		strpos(strtolower($source), "_e.asp") != false || 
		strpos(strtolower($source), "-e.asp") != false || 		
		strpos(strtolower($source), "-en.asp") != false || 
		strpos(strtolower($source), "_en.asp") != false ||  
		strpos(strtolower($source), "-eng.asp") != false || 
		strpos(strtolower($source), "_eng.asp") != false ||
		strpos(strtolower($source), " eng.asp") != false ||
		strpos(strtolower($source), "_e.aspx") != false || 
		strpos(strtolower($source), "-e.aspx") != false || 		
		strpos(strtolower($source), "-en.aspx") != false || 
		strpos(strtolower($source), "_en.aspx") != false ||  
		strpos(strtolower($source), "-eng.aspx") != false || 
		strpos(strtolower($source), "_eng.aspx") != false ||
		strpos(strtolower($source), " eng.aspx") != false	
		) {
			return $language = "english";
		}
		if (
		
		//strpos(strtolower($source), "f.htm") != false ||
		
		//strpos(strtolower($source), "_f.html") != false || 
		//strpos(strtolower($source), "-f.html") != false || 
		//strpos(strtolower($source), "-fr.html") != false || 
		//strpos(strtolower($source), "_fr.html") != false ||  
		//strpos(strtolower($source), "-fra.html") != false || 
		//strpos(strtolower($source), "_fra.html") != false ||  // redundant
		strpos(strtolower($source), "_f.htm") != false || 
		strpos(strtolower($source), "-f.htm") != false || 
		strpos(strtolower($source), "-fr.htm") != false || 
		strpos(strtolower($source), "_fr.htm") != false ||  
		strpos(strtolower($source), "-fra.htm") != false || 
		strpos(strtolower($source), "_fra.htm") != false || 
		strpos(strtolower($source), " fre.htm") != false || 
		strpos(strtolower($source), "_f.xml") != false || 
		strpos(strtolower($source), "-f.xml") != false || 
		strpos(strtolower($source), "-fr.xml") != false || 
		strpos(strtolower($source), "_fr.xml") != false ||  
		strpos(strtolower($source), "-fra.xml") != false || 
		strpos(strtolower($source), "_fra.xml") != false || 
		strpos(strtolower($source), " fre.xml") != false || 
		strpos(strtolower($source), "_f.php") != false || 
		strpos(strtolower($source), "-f.php") != false || 
		strpos(strtolower($source), "-fr.php") != false || 
		strpos(strtolower($source), "_fr.php") != false ||  
		strpos(strtolower($source), "-fra.php") != false || 
		strpos(strtolower($source), "_fra.php") != false || 
		strpos(strtolower($source), " fre.php") != false || 
		strpos(strtolower($source), "_f.asp") != false || 
		strpos(strtolower($source), "-f.asp") != false || 
		strpos(strtolower($source), "-fr.asp") != false || 
		strpos(strtolower($source), "_fr.asp") != false ||  		
		strpos(strtolower($source), "-fra.asp") != false || 
		strpos(strtolower($source), "_fra.asp") != false ||
		strpos(strtolower($source), " fre.asp") != false ||
		strpos(strtolower($source), "_f.aspx") != false || 
		strpos(strtolower($source), "-f.aspx") != false || 
		strpos(strtolower($source), "-fr.aspx") != false || 
		strpos(strtolower($source), "_fr.aspx") != false ||  		
		strpos(strtolower($source), "-fra.aspx") != false || 
		strpos(strtolower($source), "_fra.aspx") != false ||
		strpos(strtolower($source), " fre.aspx") != false	
		) {
			return $language = "french";
		}
		if (
		strpos(strtolower($source), "-bil.html") != false || 
		strpos(strtolower($source), "-english_and_french.html") != false ||
		strpos(strtolower($source), "_bil.html") != false || 
		strpos(strtolower($source), "_english_and_french.html") != false		
		) {
			return $language = "english_and_french";
		}		
	}
	//print('here374595969790871<br>');
	// method 2 to find the language of a file (look for lang attributes on the <html> tag)
	preg_match('/<html[^<>]*?>/is', $contents, $html_tag_matches);
	preg_match_all('/lang="([^"]*?)"/is', $html_tag_matches[0], $lang_matches);
	foreach($lang_matches[1] as $lang_index => $lang_value) {
		if($lang_value === "fr") {
			return $language = "french";
		}
		if($lang_value === "en") {
			return $language = "english";
		}
	}
	
	// I suppose we could also look for lang attributes in the content and call the language the opposite of what they declare (with the assumption that these attributes would only be used when some piece of content is in the 
	// language opposite to the whole of the document)
	//print('here374595969790872<br>');
	// method 3 to find the language of a file (look for french é density)
	if($language === "unknown") {
		$strlen = strlen($contents);
		if ($strlen > 5000) {
			$code_divisions = bcdiv($strlen, 1000, 0);
			$division_count = 0;
			while($division_count < $code_divisions && $language === "unknown") {
				$substr = substr($contents, bcmul($division_count, 1000), bcmul($division_count + 1, 1000));
				preg_match_all('/((é)|(&eacute;)|(&#233;)|(&#xE9;))/is', $substr, $matches);
				if (sizeof($matches[1]) > 50) {
					return $language = "french";
				}				
				$division_count++;
			}
		}
		elseif ($strlen > 1000) {
			$substr = substr($contents, 300, 700);
			preg_match_all('/((é)|(&eacute;)|(&#233;)|(&#xE9;))/is', $substr, $matches);
			if (sizeof($matches[1]) > 10) {
				return $language = "french";			
			}
		}
	}
	//print('here374595969790873<br>');
	// method 4 to find the language of a file (look for french characters)
	if($language === "unknown") {
		if (
		(
		strpos($contents, "&eacute;") != false && 
		strpos($contents, "&Eacute;") != false &&
		strpos($contents, "&agrave;") != false &&
		strpos($contents, "&Agrave;") != false &&
		strpos($contents, "&egrave;") != false &&
		strpos($contents, "&ocirc;") != false
		)
		||
		(
		strpos($contents, "é") != false && 
		strpos($contents, "É") != false &&
		strpos($contents, "à") != false &&
		strpos($contents, "À") != false &&
		strpos($contents, "è") != false &&
		strpos($contents, "ô") != false
		)
		) {
			return $language = "french";
		}
	}
	//print('here374595969790874<br>');
	// method 5 uses a dictionary search
	$body_code = ReTidy::getBodyCode($contents);
	if($body_code !== false) {
		$body_code = ReTidy::tagless($body_code); // (2009-08-24)
		// choose a number of words to check...
		$number_of_words = 200;
		$word_count = 0;
		$body_length = strlen($body_code);
		$minimum_number_of_characters = 100;
		if($body_length < $minimum_number_of_characters) {
			return $language;
		}
		if($body_length < $number_of_words) {
			return $language; // otherwise we'll get an infinite loop since the step-size will be less than 1
		}
		$step_size = bcdiv($body_length, $number_of_words, 0);
		$position = 0;
		$arrayMatches = array();
		$english_words_file = "abbr/eng/words.txt";
		$ArrayEnglishWords = explode("\r\n", file_get_contents($english_words_file));
		$count_of_english_matches = 0;
		while($position < $body_length) {
			preg_match('/ ([a-z]{1,}) /i', $body_code, $matches, PREG_OFFSET_CAPTURE, $position);
			$arrayMatches = array_merge($arrayMatches, array($matches[1][0]));
			$position += $step_size;		
		}
		$arrayMatches = array_unique($arrayMatches);
		$arraySize = sizeof($arrayMatches);
		if($arraySize > 0) {
			foreach($arrayMatches as $index => $match) {
				$lowered = strtolower(html_entity_decode($match));
				foreach($ArrayEnglishWords as $englishWord) {
					if($englishWord === $lowered) {
						$count_of_english_matches++;
						break;
					}
				}
			}
			print("Number of words found: " . $arraySize . "<br>\r\n");
			print("Number of english words found: " . $count_of_english_matches . "<br>\r\n");
			if(bcdiv($count_of_english_matches, $arraySize, 2) > 0.90) {
				return $language = "english";
			}
		}
		
		$position = 0;
		//$arrayFrenchMatches = array();
		$french_words_file = "abbr/fra/mots.txt";
		$ArrayFrenchWords = explode("\n", file_get_contents($french_words_file));
		$count_of_french_matches = 0;
		//while($position < $body_length) {
		//	preg_match('/ ([a-z]{1,}) /i', $body_code, $matches, PREG_OFFSET_CAPTURE, $position);
		//	$arrayFrenchMatches = array_merge($arrayFrenchMatches, array($matches[1][0]));
		//	$position += $step_size;		
		//}
		//$arrayFrenchMatches = array_unique($arrayFrenchMatches);
		//$frenchArraySize = sizeof($arrayFrenchMatches);
		//if($frenchArraySize > 0) {
		if($arraySize > 0) {		
			//foreach($arrayFrenchMatches as $index => $match) {
			foreach($arrayMatches as $index => $match) {			
				$lowered = strtolower(html_entity_decode($match));
				foreach($ArrayFrenchWords as $frenchWord) {
					if($frenchWord === $lowered) {
						$count_of_french_matches++;
						break;
					}
				}
			}
			//print("Number of words found: " . $frenchArraySize . "<br>\r\n");
			//print("Number of words found: " . $arraySize . "<br>\r\n");
			print("Number of french words found: " . $count_of_french_matches . "<br>\r\n");
			//if(bcdiv($count_of_french_matches, $frenchArraySize, 2) > 0.90) {
			if(bcdiv($count_of_french_matches, $arraySize, 2) > 0.90) {			
				return $language = "french";
			}
		}
		//if($englishArraySize > 0 && $frenchArraySize > 0) {
		if($arraySize > 0) {
			//if(bcdiv($count_of_english_matches, $englishArraySize, 2) < 0.55 &&
			//bcdiv($count_of_english_matches, $englishArraySize, 2) > 0.45 &&
			//bcdiv($count_of_french_matches, $frenchArraySize, 2) < 0.55 &&
			//bcdiv($count_of_french_matches, $frenchArraySize, 2) > 0.45	
			if(bcdiv($count_of_english_matches, $arraySize, 2) < 0.55 &&
			bcdiv($count_of_english_matches, $arraySize, 2) > 0.45 &&
			bcdiv($count_of_french_matches, $arraySize, 2) < 0.55 &&
			bcdiv($count_of_french_matches, $arraySize, 2) > 0.45				
			) {
				return $language = "english_and_french";
			}
		}
	}
	return $language;
}

?>