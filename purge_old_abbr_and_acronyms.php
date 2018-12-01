<?php

include("retidy.php");

$cleaner = new ReTidy();
$cleaner->purgeOldAcronymsFiles("acronyms");
$cleaner->purgeOldAcronymsFiles("abbr");

?>