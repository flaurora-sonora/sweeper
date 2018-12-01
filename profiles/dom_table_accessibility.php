<?php

return array(

	// execution macro: this defines the methods you want to call, each and every one of these "cleans" something
	// call the methods how many times you want, change the order as needed
	
	'macro' => array(
	
	//'tidy_code', // macro automatically tidies at the start

	'dom_init',
	'DOM_table_accessibility',
	'dom_save',
	
	'post_dom',
	'unencode_entities_in_attributes', // tidy should take care of this...
	//'undoublyencodeentities',
	
	'tidy_code',

	),
	
	'table_headers_id_start_count' => 1,
	'table_headers_string' => 'header',
	'table_type' => 'simple', // simple, complex
	'trust_ths' => true,
	'english_existing_caption_summary_pre_text' => 'Details of ',
	'french_existing_caption_summary_pre_text' => 'Détails de ',
	
	'use_local_DTD' => true,
	'local_DTD' => 'DTD' . DIRECTORY_SEPARATOR . 'xhtml1-strict.dtd',
	
	//'HTML5' => true, // false, true // if not specified, whether each file is HTML5 should be detected by sweeper automatically 
	
	'character_entity_encoding_type' => 'hexadecimal', // we might like to use 'named' but DOM which is currently (2015-06-09) being used doesn't like named entities
	
);

?>
