<?php

// this isn't used?
// '([^']*?)\\([^/])([^']*?)'     . ... .
include_once('retidy.php');
include_once('OM.php');
// linux uses foreslashes in paths whereas windows uses backslashes
// which files have paths in them?
$contents = file_get_contents('retidy.php');
$arrayOStrings = OM::getAllOStrings($contents, "'", "'", 0); // do we have to handle an escaped character? \'
foreach($arrayOStrings as $index => $OString) { // simplistic but sufficient
	$string_to_change = $OString[0];
	$changed_string = str_replace('\\', '/', $string_to_change);
	print('$string_to_change, $changed_string: ');var_dump($string_to_change, $changed_string);
	$contents = str_replace($string_to_change, $changed_string, $contents);
}
exit(0);
file_put_contents('retidy.php', $contents);

?>