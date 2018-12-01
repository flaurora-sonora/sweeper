<?php

return array(

	// execution macro: this defines the methods you want to call, each and every one of these "cleans" something
	// call the methods how many times you want, change the order as needed
	
	//'macro' => array(
	'pre_internal_templating_macro' => array(
	
	// notice that while acronyms and abbr are handled separately in sweeper due to them having been distinguished in HTML4, the user of sweeper should be unaware of this distinction since the prominence of HTML5
	'remove_acronyms_and_abbr',
	//'abbr',
	'acronyms',
	'remove_tags_intra_tags',
	
	),

	'HTML5' => true,
	
	'apply_acronym_if_near_definition' => false,
	'near_for_acronyms_finding' => 1000,
	'near_for_acronyms_application' => 100,
	'apply_combined_acronyms' => false,
	
	'apply_combined_abbr' => true,
	
	'abbr_instead_of_acronym' => true,
	
	//'character_entity_encoding_type' => 'named',
	
);

?>
