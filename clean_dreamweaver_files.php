<?php

$source = "not-swept";

$delete_array = array();
$dir_remove_array = array();

clean_design_notes($source, $delete_array, $dir_remove_array);

print("Files deleted:<br>\r\n<br>\r\n");

if(sizeof($delete_array) > 0) {
	foreach($delete_array as $index => $value) {
		print($index + 1 . ". " . $value . "<br>\r\n");
		unlink($value);
	}
} else {
	print("none");
}

print("<br>\r\n<br>\r\nDirectories removed:<br>\r\n<br>\r\n");

if(sizeof($dir_remove_array) > 0) {
	foreach($dir_remove_array as $index => $value) {
		print($index + 1 . ". " . $value . "<br>\r\n");
		rmdir($value);
	}
} else {
	print("none");
}

function clean_design_notes($source, &$delete_array, &$dir_remove_array) {
	if(is_dir($source)) {
		$d = dir($source);
		while(FALSE !== ($entry = $d->read())) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			$Entry = $source . '/' . $entry;           
			if(is_dir($Entry)) {
				if(strtolower($entry) == '_notes') {
					// recursively add files in this folder to the appropriate array
					delete_files($Entry, $delete_array, $dir_remove_array);
					$dir_remove_array[] = $Entry;
				} else {
					clean_design_notes($Entry, $delete_array, $dir_remove_array);
				}
				continue;
			} else {
				if(strtolower($entry) == 'thumbs.db') {
					$delete_array[] = $Entry;
				}
			}
		}
		$d->close();
	}
}

function delete_files($source, &$delete_array, &$dir_remove_array) {
	if(is_dir($source)) {
		$d = dir($source);
		while(FALSE !== ($entry = $d->read())) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			$Entry = $source . '/' . $entry;           
			if(is_dir($Entry)) {
				delete_files($Entry, $delete_array, $dir_remove_array);
				$dir_remove_array[] = $Entry;
				continue;
			} else {
				$delete_array[] = $Entry;
			}
		}
		$d->close();
	}
}

?>