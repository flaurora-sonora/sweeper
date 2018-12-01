<?php

$source = 'not-swept';

find_empty_ths($source);

function find_empty_ths($source) {
	if(is_dir($source)) {
		$d = dir($source);
		while(FALSE !== ($entry = $d->read())) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			$Entry = $source . '/' . $entry;
			if(is_dir($Entry)) {
				find_empty_ths($Entry);
			} else {
				if(found_empty_th(file_get_contents($Entry))) {
					print($Entry . "<br>\r\n");
				}// else {
				//	print('Did not find!' . $Entry . "<br>\r\n");
				//}
			}
		}
		$d->close();
	} else {
		if(found_empty_th(file_get_contents($Entry))) {
			print($source . "<br>\r\n");
		}
	}
}

function found_empty_th($code) {
	$found_empty_th = false;
	preg_match_all('/<th(>|\s[^<>]*?>)(.*?)<\/th>/is', $code, $th_matches);
	foreach($th_matches[0] as $index => $value) {
		$content = $th_matches[2][$index];
		$content = preg_replace('/(&#160;|&#xa0;|&nbsp;)/is', ' ', $content);
		preg_match('/\s*/is', $content, $space_matches);
		//print('$content: ');var_dump($content);
		//print('strlen($space_matches[0]): ');var_dump(strlen($space_matches[0]));
		//print('strlen($content): ');var_dump(strlen($content));
		if(strlen($space_matches[0]) === strlen($content)) {
			$found_empty_th = true;
		}
	}
	return $found_empty_th;
}

?>