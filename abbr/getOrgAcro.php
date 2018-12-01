<?php

// this was used to get canadian government organization names and their acronyms
// as well as contact information using the found acronyms from government electronic directory services.

$OrganizationListPage = file_get_contents("http://direct.srv.gc.ca/cgi-bin/direct500/");
$OrganizationArray = findOrgsInURLs($OrganizationListPage);
$OrganizationNameArray = findOrgNamesInURLs($OrganizationListPage);

foreach($OrganizationArray as $index => $acro) {
	preg_match("/([^\-]*)-/", $acro, $matches);
	$match = $matches[1];
	$ToWrite .= $match . "	" . $OrganizationNameArray[$index] . "\r\n";	
}
$file = "eng/GOC/acronyms.txt";
$fp = fopen($file, 'w');
fwrite($fp, $ToWrite);
fclose($fp);

$FraOrganizationListPage = file_get_contents("http://direct.srv.gc.ca/cgi-bin/direct500/XFo%3dGC%2cc%3dCA");
$FraOrganizationArray = FrafindOrgsInURLs($FraOrganizationListPage);
$FraOrganizationNameArray = FrafindOrgNamesInURLs($FraOrganizationListPage);

foreach($FraOrganizationArray as $Fraindex => $Fraacro) {
	preg_match("/-([^\-]*)/", $Fraacro, $Framatches);
	$Framatch = $Framatches[1];
	$FraToWrite .= $Framatch . "	" . $FraOrganizationNameArray[$Fraindex] . "\r\n";	
}
$Frafile = "fra/GDC/acronyms.txt";
$Frafp = fopen($Frafile, 'w');
fwrite($Frafp, $FraToWrite);
fclose($Frafp);

exit(0);

// from here we are getting contact information
$URLArray = array();
foreach($OrganizationArray as $OrgAcro) {
	$PageWithPeople = file_get_contents("http://direct.srv.gc.ca/cgi-bin/direct500/SEou%3d$OrgAcro%2co%3dGC%2cc%3dCA?SV=web&SF=Title&ST=contains&x=31&y=20");
	$URLArray = array_merge($URLArray, findURLs($PageWithPeople));
}

$rxpArray = array(
	// Name
	'/(<h2>)([\s]{0,10})([^\r\n]*)([\s]{0,10})(<a)/is' => '$3',
	// Title
	'/(<!-- Display detailed information -->)([\s]{0,10})(<div class="*text"*>)([\s]{0,10})([^<]*)(<br>)(<br>)([\s]{0,15})(<!-- title of person -->)/is' => '$5',
	// Organization 1
	'/(<!-- title of person -->)([\s]{0,10})([\w ,\-\(\)é]*)([\s]{0,10})(<br>)([\s]{0,10})(<!-- top level OU -->)/is' => '${3}',
	// Organization 2
	'/(<!-- top level OU -->)([\s]{0,10})([\w ,\-\(\)é]*)([\s]{0,10})*(<br>)([\s]{0,10})(<!-- immediate OU -->)/is' => '$3',
	// Address
	'/(<!-- Address - PO Box - Mail stop - City - Province - Contry - Postal code -->)([\s]{0,10})(<br>)([^<]*)(<br>)/is' => '$4',
	// City, Province
	'/(ITEM=\[\]\$-->)([\s]{0,10})(<br>)([\w ,\-\(\)é]*)([\s]{0,10})(<br>)/is' => '$4',
	// Country
	'/(<br>)([\w]*)(<br>)([\s]{0,10})([\w \-\(\)é]*)([\s]{0,10})(<!-- Telephone - Alternate telephone - Secure telephone - Fax - Secure Fax - TDD -->)/is' => '$2',
	// Postal Code
	'/(<br>)([\s]{0,10})([\w ]*)([\s]{0,10})(<!-- Telephone - Alternate telephone - Secure telephone - Fax - Secure Fax - TDD -->)/is' => '$3',
	// Telephone 1
	'/(<!-- Telephone - Alternate telephone - Secure telephone - Fax - Secure Fax - TDD -->)([\s]{0,10})(<dl>)([\s]{0,10})(<dt>Telephone:<\/dt><dd>)([^<]*)(<\/dd>)/is' => '$6',
	// Telephone 2
	'/(<dd>)([\w\(\)\- ]*)(<\/dd>)([\s]{0,10})(<dt>Fax:<\/dt><dd>)([^<]*)(<\/dd>)([\s]{0,10})(<\/dl>)([\s]{0,10})(<!-- X400 address -->)/is' => '$2',		
	// Fax
	'/(<dt>Fax:<\/dt><dd>)([^<]*)(<\/dd>)([\s]{0,10})(<\/dl>)([\s]{0,10})(<!-- X400 address -->)/is' => '$2',
	);
	
// these spaces are tabs (since we want a tab-separated spreadsheet out of this).
$record = ("Name" . "	");
$record .= ("Title" . "	");
$record .= ("Organization 1" . "	");
$record .= ("Organization 2" . "	");
$record .= ("Address" . "	");
$record .= ("City, Province" . "	");
$record .= ("Country" . "	");
$record .= ("Postal Code" . "	");
$record .= ("Telephone 1" . "	");
$record .= ("Telephone 2" . "	");
$record .= ("Fax" . "\r\n");

foreach ($URLArray as $file) {
	$fileContents = file_get_contents($file);
	$WhatToAdd = FindStuff($fileContents, $rxpArray);
	if ("											\r\n" != $WhatToAdd) {
		$record .= $WhatToAdd;
	}
}

WriteFile("GEDS-record.txt", $record);

function WriteFile($strTargetx, $tpx) {
	// permission must be modified so that this file can be written to.
	$handle = fopen($strTargetx, 'w');
	fwrite($handle, $tpx);
	fclose($handle);
}

function FindStuff($strToFindOn, $rxpArray) {
	$newRecordToAppend = "";
	foreach ($rxpArray as $rxp => $replacement) {
		preg_match($rxp, $strToFindOn, $matches);
		// this space is a tab (since we want a tab-separated spreadsheet out of this).
		$newRecordToAppend .= (ReplaceStuff($matches[0], $rxp, $replacement) . "	");
	}
	$newRecordToAppend .= "\r\n";
	return($newRecordToAppend);
}

function FindURLs($strToFindOn) {
	preg_match_all("/<li><a\shref=\"([^\"]*)\"/is", $strToFindOn, $matches, PREG_PATTERN_ORDER);
	return($matches[1]);
}

function FindOrgsInURLs($strToFindOn) {
	preg_match_all("/<li><a\shref=\"http:\/\/direct\.srv\.gc\.ca\/cgi\-bin\/direct500\/XEou%3d([^\"]*)%2co%3dGC%2cc%3dCA\"/is", $strToFindOn, $matches, PREG_PATTERN_ORDER);
	return($matches[1]);
}

function FraFindOrgsInURLs($strToFindOn) {
	preg_match_all("/<li><a\shref=\"http:\/\/direct\.srv\.gc\.ca\/cgi\-bin\/direct500\/XFou%3d([^\"]*)%2co%3dGC%2cc%3dCA\"/is", $strToFindOn, $matches, PREG_PATTERN_ORDER);
	return($matches[1]);
}

function FindOrgNamesInURLs($strToFindOn) {
	preg_match_all("/<li><a\shref=\"http:\/\/direct\.srv\.gc\.ca\/cgi\-bin\/direct500\/XEou%3d([^\"]*)%2co%3dGC%2cc%3dCA\">([^<]*)/is", $strToFindOn, $matches, PREG_PATTERN_ORDER);
	return($matches[2]);
}

function FraFindOrgNamesInURLs($strToFindOn) {
	preg_match_all("/<li><a\shref=\"http:\/\/direct\.srv\.gc\.ca\/cgi\-bin\/direct500\/XFou%3d([^\"]*)%2co%3dGC%2cc%3dCA\">([^<]*)/is", $strToFindOn, $matches, PREG_PATTERN_ORDER);
	return($matches[2]);
}

function ReplaceStuff($strToReplaceOn, $rxp, $replacement) {
	$strToReplaceOn = preg_replace($rxp, $replacement, $strToReplaceOn);
	return($strToReplaceOn);
}

?>