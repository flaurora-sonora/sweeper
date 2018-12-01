<meta charset="utf-8" />
<form action="character_generator.php">
Start: <input type="text" name="start" /><br />
End: <input type="text" name="end" /><br />
<input type="submit" />
</form>

<?php

if ($_REQUEST["start"] == "") {
	$start = 230;
} else {
	$start = $_REQUEST["start"];
}

if ($_REQUEST["end"] == "") {
	$end = 235;
} else {
	$end = $_REQUEST["end"];
}

$entities_file = "dtd/xhtml1-strict.dtd";
//$entities_file = "dtd/many_entities.dtd";

$entities_code = file_get_contents($entities_file);
$array_entities = explode("\r\n", $entities_code);

print("entities file: " . $entities_file . "<br>\r\n");

?>

<table cellpadding="4" cellspacing="0" border="1">
<tr>
<th>Raw</th>
<th>Entity</th>
<th>Decimal-encoded</th>
<th>Hexadecimal-encoded</th>
<th>Letter-encoded</th>
<th>URL-encoded</th>
</tr>

<?php

while($start < $end + 1) {
	print("<tr>\r\n");
	print("<td>");
	$raw = html_entity_decode("&#" . $start . ";");
	print($raw);
	print("</td>\r\n");
	print("<td>");				
	print("&#" . $start . ";");
	print("</td>\r\n");
	print("<td>");				
	print(htmlentities("&#" . $start . ";"));
	print("</td>\r\n");
	$hex = dechex($start);
	print("<td>");				
	print(htmlentities("&#x" . $hex . ";"));
	print("</td>\r\n");
	print("<td>");
	// we use something like a .ent lookup for this...
	foreach($array_entities as $line) {
		if(preg_match('/<!ENTITY\s*(\w*)\s*"&#' . $start . ';"\s*>/is', $line, $entities_match)) {
			print(htmlentities("&" . $entities_match[1] . ";"));
			break;
		} elseif(preg_match('/<!ENTITY\s*(\w*)\s*"&#x[0]*' . $hex . ';"\s*>/is', $line, $entities_match)) {
			print(htmlentities("&" . $entities_match[1] . ";"));
			break;
		}
	}
	print("</td>\r\n");
	print("<td>");
	if(strlen($raw) === 1) {
		print(urlencode($raw));
	}
	print("</td>\r\n");
	//print("<td>");
	//print(ord($raw) . "\r\n");
	//print("</td>\r\n");
	print("</tr>\r\n");
	$start++;
}

?>

</table>