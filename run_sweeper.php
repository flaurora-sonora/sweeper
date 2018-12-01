<meta charset="utf-8" />
Run sweeper.php Script
<form method="POST" action="sweeper.php" style="margin-top:0;">
<br>
Profile: <br>
<select style="WIDTH: 350px;" name="profile">

<?php

$directory = "profiles";
$handle = opendir($directory);

$profiles_array = array();
$file = "string_not_null";
while($file != "") {
	$file = readdir($handle);
	if($file != "." && $file != ".." && $file != "" && !is_dir($directory . '/' . $file)) {
		//print("<!--$file-->\r\n");
		$profiles_array[] = substr($file, 0, strpos($file, "."));
	}
}
closedir($handle);
sort($profiles_array, SORT_NATURAL | SORT_FLAG_CASE); // for linux
foreach($profiles_array as $profile) {
	print("<option value=\"" . $profile . "\">" . $profile . "</option>\r\n");
}

?>

</select><br><br>
<div id="EngDepDiv">
Path: <input type="text" name="acronym_path" size="70"> (in the abbr folder)<br>
</div>
<br>
<div style="float: left;">
English Template: <br>
<select style="WIDTH: 350px;" name="EngTemplate">
<option value=""></option>
<option value="none">none</option>
<?php

$directory = "Templates";

print_template_options($directory);

closedir($handle);

function print_template_options($source) {
	if(is_dir($source)) {
		$d = dir($source);
		while(FALSE !== ($entry = $d->read())) {
			if($entry == '.' || $entry == '..') {
				continue;
			}
			$Entry = $source . '/' . $entry;           
			if(is_dir($Entry)) {
				if($entry != 'Templates') {
					print_template_options($Entry);
				}
				continue;
			}
			if(strpos($Entry, ".html") || strpos($Entry, ".htm") || strpos($Entry, ".asp") || strpos($Entry, ".xml")) {
				print("<option value=\"" . $Entry . "\">" . $Entry . "</option>\r\n");
			}
		}
		$d->close();
	}
	else {
		print("<option value=\"" . $Entry . "\">" . $Entry . "</option>\r\n");
	}
}

?>
</select>
</div>

<div style="float: left;margin-left: 10px;">
French Template: <br>
<select style="WIDTH: 350px;" name="FraTemplate">
<option value=""></option>
<option value="none">none</option>
<?php

$directory = "Templates";

print_template_options($directory);

closedir($handle);

?>
</select>
</div><br><br><br>

Source: <br><input type="text" name="source" value="not-swept" size="70"><br>
Target: <br><input type="text" name="target" value="swept" size="70"><br>

<br>
<input type="submit">
</form>
