<?php

//print('...');

$contents = file_get_contents('not-swept/DEC_pages_list.txt');
$array = explode("\r\n", $contents);
//var_dump($array);exit(0);
$filtered_array = array();
foreach($array as $index => $value) {
	preg_match('/[^\?]*/is', $value, $url_matches);
	/*print($url_matches[0] . '
');*/
	$add_it = true;
	$clipped_url = $url_matches[0];
	foreach($filtered_array as $index2 => $value2) {
		if($clipped_url === $value2[1]) {
			$add_it = false;
		}
	}
	if($add_it) {
		$whole_url = $value;
		$filtered_array[] = array($whole_url, $clipped_url);
	}
}
$filtered_contents = '';
foreach($filtered_array as $index3 => $value3) {
	$filtered_contents .= $value3[0] . '
';
}
file_put_contents('not-swept/DEC_pages_list_filtered.txt', $filtered_contents)

?>