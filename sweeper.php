<?php

//header('Content-type: text/html; charset=iso-8859-1');
print('<meta charset="utf-8" />
');

include("retidy.php");
include("getLanguage.php");
//include("detectWET.php");

$page_id_counter = 0;

$initial_time = time();

if($_REQUEST["source"] == "") {
	$source = "not-swept";
} else {
	$source = $_REQUEST["source"];
}

if($_REQUEST["target"] == "") {
	$target = "swept";
} else {
	$target = $_REQUEST["target"];
}

$acronym_path = $_REQUEST["acronym_path"];

if($_REQUEST["profile"] == "") {
	$profile = "basic";
} else {
	$profile = $_REQUEST["profile"];
}

// A drop-down box is generated using the text files with 
// the department acronyms and department names.
if($_REQUEST["EngDep"] == "") {
	$EngDepAcro = "";
} else {
	$EngDepAcro = substr($_REQUEST["EngDep"], 0, strpos($_REQUEST["EngDep"], "	"));
}

// try to find the templates
$look_for_english_template = false;
$look_for_french_template = false;
if($_REQUEST["EngTemplate"] == "") {
	$look_for_english_template = true;
	$english_template = "none";
} else {
	$english_template = $_REQUEST["EngTemplate"];
}
if($_REQUEST["FraTemplate"] == "") {
	$look_for_french_template = true;
	$french_template = "none";
} else {
	$french_template = $_REQUEST["FraTemplate"];
}

// first look for the templates in the source folder
if($look_for_english_template) {
	if(is_dir($source)) {
		$d = dir($source);
		while(FALSE !== ($entry = $d->read())) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			if($entry == 'Templates') {
				$e = dir($source . '/Templates');
				// notice that this will take the last file with the required condition
				while(FALSE !== ($fntry = $e->read())) {
					if($fntry == '.' || $fntry == '..') {
						continue;
					}
					if(strpos($fntry, "-eng.") !== false) {
						$look_for_english_template = false;
						$english_template = $source . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . $fntry;
					}				
				}
				break;
			}
		}
		$d->close();
	}
}

if($look_for_french_template) {
	if(is_dir($source)) {
		$d = dir($source);
		while(FALSE !== ($entry = $d->read())) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			if($entry == 'Templates') {
				$e = dir($source . '/Templates');
				// notice that this will take the last file with the required condition
				while(FALSE !== ($fntry = $e->read())) {
					if($fntry == '.' || $fntry == '..') {
						continue;
					}
					if(strpos($fntry, "-fra.") !== false) {
						$look_for_french_template = false;
						$french_template = $source . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR . $fntry;
					}				
				}
				break;
			}
		}
		$d->close();
	}
}

// then look for templates in sweeper's templates folder
if($look_for_english_template) {
	if(is_dir('Templates' . DIRECTORY_SEPARATOR . $EngDepAcro)) {
		$d = dir('Templates' . DIRECTORY_SEPARATOR . $EngDepAcro);
		// notice that this will take the last file with the required condition
		while(FALSE !== ($entry = $d->read())) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			if(strpos($entry, "-eng.") !== false) {
				$look_for_english_template = false;
				$english_template = 'Templates' . DIRECTORY_SEPARATOR . $EngDepAcro . DIRECTORY_SEPARATOR . $entry;
			}				
		}
		$d->close();
	}
}

if($look_for_french_template) {
	if(is_dir('Templates' . DIRECTORY_SEPARATOR . $EngDepAcro)) {
		$d = dir('Templates' . DIRECTORY_SEPARATOR . $EngDepAcro);
		// notice that this will take the last file with the required condition
		while(FALSE !== ($entry = $d->read())) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			if(strpos($entry, "-fra.") !== false) {
				$look_for_french_template = false;
				$french_template = 'Templates' . DIRECTORY_SEPARATOR . $EngDepAcro . DIRECTORY_SEPARATOR . $entry;
			}				
		}
		$d->close();
	}
}

/*if($look_for_english_template) {
	$english_template = 'Templates' . DIRECTORY_SEPARATOR . 'XHTML-blank-css.html';
}

if($look_for_french_template) {
	$french_template = 'Templates' . DIRECTORY_SEPARATOR . 'XHTML-blank-css.html';
}*/

$grand_total_changes = 0;

full_directory_sweep($source, $target, $profile);

// If we wanted to count the total number of changes or time taken to sweep (for example)
// this is the place to do it although number of changes per file would have to passed back or recorded somewhere
print("Total number of changes made by sweeper: " . $grand_total_changes . "<br>\r\n");
$sweeping_time = time() - $initial_time;
print("Total sweeping time: " . $sweeping_time . " seconds");

function merge_files($source, $target, $profile, $EngDepAcro) {
	if (is_dir($source)) {
		$have_english_file_to_add_to = false;
		$have_french_file_to_add_to = false;		
		$d = dir($source);
		while (FALSE !== ($entry = $d->read())) {
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			$Entry = $source . '/' . $entry;
			if (is_dir($Entry)) {
				merge_files($source, $target, $profile, $EngDepAcro);
				continue;
			}
			//if (strpos($Entry, ".html") || strpos($Entry, ".htm") || strpos($Entry, ".asp")) {
			if (file_extension_is($Entry, ".html") || file_extension_is($Entry, ".htm") || file_extension_is($Entry, ".asp")) {			
				$entry_contents = file_get_contents($Entry);
				$open_body_string = '<body>
<div class="center" style="width:600px;text-align:left;">';
				$close_body_string = '</div>
</body>';
				$close_body_pos = strpos($entry_contents, $close_body_string);
				$close_body_strlen = strlen($close_body_string);
				$entry_code = substr($entry_contents, $open_body_pos+$open_body_strlen, $close_body_pos-($open_body_pos+$open_body_strlen));
				if(strpos($Entry, "-eng")) {
					if($have_english_file_to_add_to) {						
						$english_working_contents = str_replace('</div>
</body>', $entry_code . '</div>
</body>', $english_working_contents);
					} else {
						$english_working_contents = file_get_contents($Entry);
						$have_english_file_to_add_to = true;
					}
				}
				if(strpos($Entry, "-fra")) {
					if($have_french_file_to_add_to) {
						$french_working_contents = str_replace('</div>
</body>', $entry_code . '</div>
</body>', $french_working_contents);
					} else {
						$french_working_contents = file_get_contents($Entry);
						$have_french_file_to_add_to = true;
					}
				}
			}
		}
		$d->close();
		file_put_contents($source . '/' . "merged-eng.html", $english_working_contents);
		file_put_contents($source . '/' . "merged-fra.html", $french_working_contents);
		SweepFile($source . '/' . "merged-eng.html", $target . '/' . "merged-eng.html", $profile, $EngDepAcro);
		SweepFile($source . '/' . "merged-fra.html", $target . '/' . "merged-fra.html", $profile, $EngDepAcro);
	}
}

function full_directory_sweep($source, $target, $profile) {
	if(is_dir($source)) {
		@mkdir($target);
		$d = dir($source);
		while(FALSE !== ($entry = $d->read())) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			$Entry = $source . '/' . $entry;           
			if(is_dir($Entry)) {
				if($entry != 'Templates' && $entry != '_notes') {
					full_directory_sweep($Entry, $target . '/' . $entry, $profile);
				}
				continue;
			}
			/*if($profile === 'clean_PDF' && strpos($Entry, ".xml")) {
				SweepFile($Entry, str_replace('.xml', '.html', $target . '/' . $entry));
			} else*/if($profile === 'clean_excel' && strpos($Entry, ".xml")) {
				SweepFile($Entry, str_replace('.xml', '.html', $target . '/' . $entry));
			} elseif($profile === 'clean_feeds' && strpos($Entry, ".xml")) {
				SweepFile($Entry, $target . '/' . $entry);
			} elseif($profile === 'clean_CSS' && strpos($Entry, ".css")) {
				SweepFile($Entry, $target . '/' . $entry);
			//} elseif(strpos($Entry, ".html") || strpos($Entry, ".htm") || strpos($Entry, ".asp") || strpos($Entry, ".aspx") || strpos($Entry, ".php")) {
			} elseif(file_extension_is($Entry, ".html") || file_extension_is($Entry, ".htm") || file_extension_is($Entry, ".asp") || file_extension_is($Entry, ".aspx") || file_extension_is($Entry, ".php")) {			
				SweepFile($Entry, $target . '/' . $entry);
			}
		}
		$d->close();
	} else {
		SweepFile($source, $target);
	}
}

function file_extension_is($filename, $extension) {
	$found_extension = substr($filename, strpos_last($filename, '.'));
	if($found_extension === $extension) {
		return true;
	}
	return false;
}

function strpos_last($haystack, $needle) {
	//print('$haystack, $needle: ');var_dump($haystack, $needle);
	if(strlen($needle) === 0) {
		return false;
	}
	$len_haystack = strlen($haystack);
	$len_needle = strlen($needle);		
	$pos = strpos(strrev($haystack), strrev($needle));
	return $len_haystack - $pos - $len_needle;
}

function SweepFile($sourceFile, $destFile) {
	global $profile, $EngDepAcro, $english_template, $french_template, $acronym_path, $grand_total_changes;

	// if the file contains two languages; split it then sweep each file:
	if (false) { // CED1162
		$fileContentsForSplit = file_get_contents($sourceFile);
		preg_match('/<body>(.*?)(<p align="center"><strong>Profil socio&eacute;conomique.*?)<\/body>/is', $fileContentsForSplit, $matches);
		$englishContent = $matches[1];
		$frenchContent = $matches[2];		
		$englishContent = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
<title></title>
</head>
<body>" . $englishContent . "</body>
</html>";

		$frenchContent = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
<title></title>
</head>
<body>" . $frenchContent . "</body>
</html>";		
		$engdestFile = str_replace('.html', '-eng.html', $destFile);
		$fradestFile = str_replace('.html', '-fra.html', $destFile);		
		$arraySourceContentsDest = array(
		$englishContent => $engdestFile, 
		$frenchContent => $fradestFile, 		
		);
	} else {
		$filecontents = file_get_contents($sourceFile);
		$arraySourceContentsDest = array($filecontents => $destFile);
	}
	foreach($arraySourceContentsDest as $filecontents => $destFile) {
		$ProfileDetected = true;
		if ($profile == "" || $profile == "auto-detect") {
			// then we must detect it
			$ProfileDetected = false;
		}
		if($ProfileDetected === false) {
			// default
			$profile = 'basic';
		}
		if($EngDepAcro == "" || $EngDepAcro == "auto-detect") {
			// then we must detect it
			$EngDepAcroDetected = false;
			// Try to detect it, for example by looking in the title.
		}
		if($EngDepAcroDetected === false) {
			// default
			// not every job shall be for a government client...
		}

		if($profile === 'clean_feeds') {
		
		} elseif($profile === 'clean_CSS') {
		
		} else {
			$language = getLanguage($sourceFile);
			if($language === "unknown") {
				// useful at least for files with two languages. (which are rare)
				$language = getLanguage($filecontents);
			}
			if($language === "french") {
				$template = $french_template;
			} else {
				$template = $english_template;
			}
		}

		//$temp_var = $page_id_counter;
		
		//echo("<strong>" . htmlentities($destFile) . "</strong><br>\r\n");
		$destFile_to_string = iconv('windows-1252', 'utf-8//TRANSLIT', $destFile);
		print('<strong>' . $destFile_to_string . '</strong><br>
');
		
		$cleaner = new ReTidy($profile, 'profiles' . DIRECTORY_SEPARATOR);
		$cleaner->setFile($sourceFile);
		$cleaner->setCode($filecontents);
		//var_dump($cleaner->config['macro']);exit(0);
		//foreach($cleaner->config['macro'] as $index => $function) {
		//	if($function === "templateCode") {
		if($profile === 'clean_feeds') {
		
		} elseif($profile === 'clean_CSS') {
		
		} elseif($template !== 'none') {
			$cleaner->setTemplate($template);
		}
		//		break;
		//	}
		//}
		if($profile === 'clean_feeds') {
		
		} elseif($profile === 'clean_CSS') {
		
		} else {
			$cleaner->setLanguage($language);
		}
		/*
		// it is also conceivable that we might not want to display that "WET" has been recognized but very much of the work will be WET
		if($cleaner->config['WET'] === 'WET') {
			// no need to detect it
			$WET = 'WET';
		} else {
			$WET = detectWET($sourceFile);
		}
		$cleaner->setWET($WET);*/
		if(strlen($EngDepAcro) > 0) {
			$cleaner->setDepartment($EngDepAcro);
		}
		if(strlen($acronym_path) > 0) {
			$cleaner->setAcronymPath($acronym_path);
		}
		$cleaner->cleanCode();
		$cleaned_code = $cleaner->getCode();
	
		// For INAC and CED... maybe this is a bigger character encoding problem.
		$cleaned_code = str_replace('&#13;', '', $cleaned_code);
		// what about splitting files?
		
		//var_dump($cleaner->config['encoding']);
		//var_dump($cleaner->config);exit(0);
		$messages = $cleaner->getMessages();
		echo $messages;
		//$this->messages = iconv($cleaner->config['encoding'], "UTF-8", $cleaner->getMessages()); // browsers probably default to UTF-8
		$grand_total_changes += $cleaner->getChanges();	
		
		if(isset($cleaner->config["split_files"]) && $cleaner->config["split_files"]) {
			// split files (CED)
			$template_code = file_get_contents($cleaner->config["template"]);
			preg_match_all('/<body[^<>]*>(.*?)<\/body>/is', $cleaned_code, $body_matches);
			$body_code = $body_matches[1][0];
			// get the strpos of the last <h2>:
			$last_section_code = substr($body_code, rstrpos($body_code, "<h2"));
			preg_match_all('/(.*?)<h2/is', $body_code, $section_matches);
			foreach($section_matches[1] as $index => $section_code) {
				$destOfThisPart = preg_replace('/\/[^ \/]*\.[^ \/]*/is', '/' . $cleaner->config["split_files_names"][$index], $destFile);
				$codeOfThisPart = str_replace('{content}', '<h2' . $section_code, $template_code);
				if($index === 0) {
					// the first section also needs to be treated specially because of the way I have done this.
					$codeOfThisPart = str_replace('<h2', '', $codeOfThisPart);
				}
				file_put_contents($destOfThisPart, $codeOfThisPart);
			}
			// do the last part of the file.
			$index++;
			$destOfLastSection = preg_replace('/\/[^ \/]*\.[^ \/]*/is', '/' . $cleaner->config["split_files_names"][$index], $destFile);
			$codeOfLastSection = str_replace('{content}', $last_section_code, $template_code);
			file_put_contents($destOfLastSection, $codeOfLastSection);
		} elseif(isset($cleaner->config["split_files_because_of_size"]) && $cleaner->config["split_files_because_of_size"]) {
			$filesize = filesize($sourceFile);
			$chunksize = 100000; // bytes
			$chunksize = 6000; // bytes	
			$chunksize = 200; // characters
			$chunksize = 1; // characters			
			$pos_open_body = strpos($cleaned_code, "<body");
			$pos_close_body = strpos($cleaned_code, "</body>");
			$body_code = substr($cleaned_code, $pos_open_body, ($pos_close_body-$pos_open_body+7));
			$body_code = str_replace('</body>', '', $body_code);
			$body_code = preg_replace('/<body[^<>]*>/is', '', $body_code);			
			// since preg_match gives up for a long string length (which is a reason to split the file!)...
			$length_body_code = strlen($body_code);
			$arrayPosTagsToSplitOn = array();
			$body_code_left = $body_code;
			$marker_code = "<p";
			while(($pos_end_part = strpos($body_code, $marker_code, $chunksize)) !== false) {
				var_dump(substr($body_code_left, 0, $pos_end_part-strlen($marker_code)));
				$body_code_left = substr($body_code_left, $pos_end_part-strlen($marker_code));
			}
			exit(0);
			var_dump($arrayPosTagsToSplitOn);print("<br>\r\n");exit(0);
			$template_code = file_get_contents($cleaner->config["template"]);
			$piece_count = 0;
			foreach($arrayPosTagsToSplitOn as $index => $posToSplitOn) {
				$piece_count++;
				if($index > 0) {
					$code_of_part = substr($body_code, $arrayPosTagsToSplitOn[$index - 1], $posToSplitOn - $arrayPosTagsToSplitOn[$index - 1]);
					$templated_code = str_replace('{content}', $code_of_part, $template_code);
				} else {
					$code_of_part = substr($body_code, 0, $posToSplitOn);
					$templated_code = str_replace('{content}', $code_of_part, $template_code);
				}
				file_put_contents(str_replace('.', '-' . $piece_count . '.', $destFile), $templated_code);				
			}
		} else {
			//$cleaner->var_dump_full($filecontents, $cleaned_code);
			// only put the file if there is a difference in it?
			if($filecontents != $cleaned_code) {
				file_put_contents($destFile, $cleaned_code);
			}
		}
		unset($cleaner);
	}
}

function rstrpos ($haystack, $needle){
	$len = strlen ($haystack);
	$needle_len = strlen($needle);
	$pos = strpos (strrev($haystack), strrev($needle));
	if ($pos === false) return false;
	return $len - ($pos + $needle_len);
}


function RxpReplaceCaseInsensitive($pattern, $replacement, $code) {
	$code = preg_replace("/" . $pattern . "/is", $replacement, $code);
	return $code;
}

?>