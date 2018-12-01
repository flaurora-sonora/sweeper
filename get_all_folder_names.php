<?php

print('<meta charset="utf-8" />
');
$folders = array();
$source = 'not-swept';
$folders = recursive_list($source, $folders);
print('$folders: ');var_dump($folders);

function recursive_list($source, $folders) {
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
				$folder = $Entry;
				$folder = str_replace('not-swept/', '', $folder);
				$folder = substr($folder, strpos($folder, '/', strpos_last($folder, '/') - strpos($folder, '/')));
				$folders[$folder] = true;
				$folders = recursive_list($Entry, $folders);
				//continue;
			}
		}
		$d->close();
	}
	return $folders;
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