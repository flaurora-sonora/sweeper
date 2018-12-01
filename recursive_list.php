<?php

$source = 'not-swept';
//$source = 'P:/Program Files/wget-1-11-4/bin/crtc.gc.ca';
$put_contents = '';
$put_contents = recursive_list($source, $put_contents);
file_put_contents('list.txt', $put_contents);

function recursive_list($source, $put_contents) {
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
				$put_contents = recursive_list($Entry, $put_contents);
				//continue;
			} else {
				//print("here394950561<br>\r\n");exit(0);
				//if(strpos(strtolower($Entry), '.asp') !== false) {
				//if(file_extension_is($Entry, ".html") || file_extension_is($Entry, ".htm") || file_extension_is($Entry, ".asp") || file_extension_is($Entry, ".aspx") || file_extension_is($Entry, ".pdf") || file_extension_is($Entry, ".xml")) {			
				if(file_extension_is($Entry, ".html") || file_extension_is($Entry, ".htm") || file_extension_is($Entry, ".asp") || file_extension_is($Entry, ".aspx")) {
		//			$contents = file_get_contents($Entry);
		//			preg_match('/<title>(.*?)<\/title>/is', $contents, $title_matches);
					//$Entry = str_replace('not-swept/', 'http://sirc.gc.ca/', $Entry);
					$Entry = str_replace('P:/Program Files/wget-1-11-4/bin/crtc.gc.ca/', 'http://crtc.gc.ca/', $Entry);
					$put_contents .= $Entry . '
';
					print($Entry . '
');
					/*if(strpos($Entry, '-fra') === false) {
						print($Entry . '	' . $title_matches[1] . '
');
					}*/
				}
			}
		}
		$d->close();
	} else {
		//print("here394950561<br>\r\n");exit(0);
		if(strpos(strtolower($source), '.asp') !== false) {
			print($source . "<br>\r\n");
		}
	}
	return $put_contents;
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

?>