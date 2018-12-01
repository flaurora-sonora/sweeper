<?php

return array(

	// execution macro: this defines the methods you want to call, each and every one of these "cleans" something
	// call the methods how many times you want, change the order as needed
	
	'macro' => array(
	
	'structure',
	
	),
	
	'use_local_DTD' => true,
	'local_DTD' => 'DTD' . DIRECTORY_SEPARATOR . 'xhtml1-strict.dtd',
	
	'TOC_sub' => 'lists', // lists, indent, CED
	'trust_headings' => true,  // false, true, if_they_seem_sufficient
	'HTML5' => false,  // false, true
	'anchor_text' => 'section', // the text to put before anchor numbers that is required since ids beginning with a number do not constitute valid HTML
	'normalize_heading_text' => 'false', // headings, all_indexical_content, false
	'generate_TOC' => false, // false, if_non_existent, true
	'lowest_generated_TOC_level' => 4, // 2, 3, 4, 5, 6
	
	'character_entity_encoding_type' => 'named',
	
);

?>
