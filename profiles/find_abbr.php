<?php

return array(
	
	// execution macro: this defines the methods you want to call, each and every one of these "cleans" something
	// call the methods how many times you want, change the order as needed
	
	'macro' => array(
	
	// notice that while acronyms and abbr are handled separately in sweeper due to them having been distinguished in HTML4, the user of sweeper should be unaware of this distinction since the prominence of HTML5
	'find_abbr',
	'find_acronyms',
	
	),

	'simple_acronym_detection' => true,
	'near_for_acronyms_finding' => 1000,
	
);

?>
