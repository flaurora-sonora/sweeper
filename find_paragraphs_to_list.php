<?php

$source = 'not-swept';

find_paragraphs_to_list($source);

function find_paragraphs_to_list($source) {
	if(is_dir($source)) {
		//print("here394950560<br>\r\n");exit(0);
		$d = dir($source);
		while(FALSE !== ($entry = $d->read())) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			$Entry = $source . '/' . $entry;
			//print("***" . $Entry . "<br>\r\n");
			if(is_dir($Entry)) {
				//print("here394950562<br>\r\n");exit(0);
				find_paragraphs_to_list($Entry);
				//continue;
			} else {
				//print("here394950561<br>\r\n");exit(0);
				if(found_paragraph_to_list(file_get_contents($Entry))) {
					print($Entry . "<br>\r\n");
				}
			}
		}
		$d->close();
	} else {
		//print("here394950561<br>\r\n");exit(0);
		if(found_paragraph_to_list(file_get_contents($Entry))) {
			print($source . "<br>\r\n");
		}
	}
}

function found_paragraph_to_list($code) {
	$found_paragraph_to_list = false;
	preg_match_all('/<p[^<>]*?>(.*?)<\/p>/is', $code, $paragraph_matches);
	foreach($paragraph_matches[0] as $index => $value) {
		preg_match_all('/<\/a>\s*<br/is', $paragraph_matches[1][$index], $link_break_matches);
		if(sizeof($link_break_matches[0]) > 1) {
			//print('size: ');var_dump(sizeof($link_break_matches[0]));
			$found_paragraph_to_list = true;
			break;
		}
	}
	//print('found_paragraph_to_list: ');var_dump($found_paragraph_to_list);
	return $found_paragraph_to_list;
}

?>