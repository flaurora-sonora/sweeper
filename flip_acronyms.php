<?php

// flip acronyms

$url = $_REQUEST["url"];
$contents = file_get_contents($url);

$contents = preg_replace('/<(abbr|acronym) title="([^"]*?)">([^<>]*?)<\/\1>/is', '<$1 title="$3">$2</$1>', $contents);
print($contents);

?>