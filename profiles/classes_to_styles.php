<?php

return array(

	// execution macro: this defines the methods you want to call, each and every one of these "cleans" something
	// call the methods how many times you want, change the order as needed
	
	'macro' => array(
	
	//'tidy_code', // macro automatically tidies at the start
	'style_cdata',
	
	'dom_init',
	'DOM_stylesheets_to_styles',
	'dom_save',
	
	'post_dom',

	'tidy_code', 
	
	),
	
	'use_local_DTD' => true,
	'local_DTD' => 'DTD' . DIRECTORY_SEPARATOR . 'xhtml1-strict.dtd',

	
);

?>
