<meta charset="utf-8" />
<?php

$initial_time = time();
$code = $_REQUEST['code'];
//print('$code: ');var_dump($code);
if($code == '') {
	$cleaned_code = '<p>paste code here</p>';
} else {
	include("retidy.php");
	include("getLanguage.php");
	$acronym_path = $_REQUEST["acronym_path"];

	if($_REQUEST["profile"] == "") {
		$profile_by_request = "basic";
	} else {
		$profile_by_request = $_REQUEST["profile"];
	}
	
	if($_REQUEST["language"] == "") {
		//$language = "english";
	} else {
		$language = $_REQUEST["language"];
	}

	if($_REQUEST["EngDep"] == "") {
		$EngDepAcro = "";
	} else {
		$EngDepAcro = substr($_REQUEST["EngDep"], 0, strpos($_REQUEST["EngDep"], "	"));
	}

	$grand_total_changes = 0;
	$cleaner = new ReTidy($profile_by_request, 'profiles' . DIRECTORY_SEPARATOR);
	//$cleaner->setFile($sourceFile);
	
	$cleaned_code = str_replace('&amp;', '&', $cleaned_code);
	$code = '<html>
<head>
<title>paste sweep</title>
</head>
<body>
' . $code . '
</body>
</html>';	
	$cleaner->setCode($code);

	if($_REQUEST["language"] == "") {
		if($profile === 'clean_feeds') {
		
		} elseif($profile === 'clean_CSS') {
		
		} else {
			$language = getLanguage($code);
		}
	}
	
	/*if($profile_by_request === 'clean_feeds') {

	} elseif($profile_by_request === 'clean_CSS') {

	} elseif($template !== 'none') {
		$cleaner->setTemplate($template);
	}*/

	if($profile_by_request === 'clean_feeds') {

	} elseif($profile_by_request === 'clean_CSS') {

	} else {
		$cleaner->setLanguage($language);
	}

	if(strlen($EngDepAcro) > 0) {
		$cleaner->setDepartment($EngDepAcro);
	}
	if(strlen($acronym_path) > 0) {
		$cleaner->setAcronymPath($acronym_path);
	}

	$cleaner->cleanCode();
	$cleaned_code = $cleaner->getCode();
	//print('$cleaned_code after getCode: ');var_dump($cleaned_code);
	$cleaned_code = substr($cleaned_code, strpos($cleaned_code, '<body>') + strlen('<body>'));
	$cleaned_code = substr($cleaned_code, 0, strpos($cleaned_code, '</body>'));
	$cleaned_code = str_replace('&', '&amp;', $cleaned_code);
	$messages = $cleaner->getMessages();
	echo $messages;
	$grand_total_changes += $cleaner->getChanges();	

	// If we wanted to count the total number of changes or time taken to sweep (for example)
	// this is the place to do it although number of changes per file would have to passed back or recorded somewhere
	print("Total number of changes made by sweeper: " . $grand_total_changes . "<br>\r\n");
	$sweeping_time = time() - $initial_time;
	print("Total sweeping time: " . $sweeping_time . " seconds");
}


?>
<form method="POST" action="paste_sweep.php" style="margin-top: 0;">
<textarea rows="30" cols="100" name="code">
<?php print($cleaned_code); ?>
</textarea>
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
	if($profile_by_request === $profile) {
		print('<option value="' . $profile . '" selected>' . $profile . '</option>');
	} else {
		print("<option value=\"" . $profile . "\">" . $profile . "</option>\r\n");
	}
}

?>

</select><br><br>
<div id="EngDepDiv">
Path: <input type="text" name="acronym_path" size="70"> (in the abbr folder)<br>
</div><br>
Language: <br>
<select style="WIDTH: 350px;" name="language">

<?php

if($language === 'english') {
	print('<option value=""></option>
<option value="english" selected>english</option>
<option value="french">french</option>');
} elseif($language === 'french') {
	print('<option value=""></option>
<option value="english">english</option>
<option value="french" selected>french</option>');
} else {
	print('<option value=""></option>
<option value="english">english</option>
<option value="french">french</option>');
}

?>

</select><br>
<br>
<input type="submit">
</form>
