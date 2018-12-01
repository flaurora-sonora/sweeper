<?php

print('code for this was not finished.');exit(0);
$dirs = array('abbr', 'acronyms');
$languages = array('eng', 'fra');
foreach($dirs as $dir) {
	$handle = opendir($dir);
	$path = $dir . '\\';
	while(($entry = readdir($handle)) != false) {
		if($entry === '.' || $entry === '..') {
			
		} else {
			//print($entry . '<br>');
			foreach($languages as $language) {
				$language_handle = opendir($language);
				$language_path = $path . '\\' . $language . '\\';
				while(($language_entry = readdir($language_handle)) != false) {
					if($language_entry === '.' || $language_entry === '..') {
						
					} elseif($language_entry === 'GOC' || $language_entry === 'GDC') {
						
					}
				}
				closedir($language_handle);
			}
		}
	}
	closedir($handle);
	exit(0);
}

?>