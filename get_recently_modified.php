<?php

$source = 'source';
$target = 'not-swept';

recursive_list($source, $target);

function recursive_list($source, $target) {
	if(is_dir($source)) {
		if(!is_dir($target)) {
			mkdir($target);
		}
		$d = dir($source);
		while(FALSE !== ($entry = $d->read())) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			$Entry = $source . '/' . $entry;
			if(is_dir($Entry)) {
				$target_Entry = str_replace($source, $target, $Entry);
				recursive_list($Entry, $target_Entry);
			} else {
				if(is_recently_modifed($Entry)) {
					copy($Entry, str_replace($source, $target, $Entry));
					print($Entry . ' was recently modified.<br>');
				}
			}
		}
		$d->close();
	} else {
		if(is_recently_modifed($source)) {
			copy($source, $target);
			print($source . ' was recently modified.<br>');
		}
	}
}

function is_recently_modifed($filename) {
	$modified_string = date("F d Y H:i:s.", filemtime($filename));
	//print('$modified_string: ');var_dump($modified_string);
	preg_match('/([^\s]+) ([0-9]{2}) ([0-9]{4}) /is', $modified_string, $modified_matches);
	//print('$modified_matches: ');var_dump($modified_matches);
	$month = $modified_matches[1];
	$day = $modified_matches[2];
	$year = $modified_matches[3];
	//print('$month, $day, $year: ');var_dump($month, $day, $year);
	//print('$month === September, $day > 25, $year >= 2016: ');var_dump($month === 'September', $day > 25, $year >= 2016);
	//return $month === 'October' && $day > 2 && $year >= 2016;
	//return $month === 'February' && $day > 2 && $year >= 2017;
	//return $month === 'June' && $day > 27 && $year >= 2017;
	return ($month === 'March' && $day > 7 || $month === 'April') && $year >= 2018;
	//return $year >= 2018;
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