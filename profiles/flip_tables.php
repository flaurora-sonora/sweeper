<?php

return array(

	// execution macro: this defines the methods you want to call, each and every one of these "cleans" something
	// call the methods how many times you want, change the order as needed
	'macro' => array(

	'flip_tables',
	
	'post_DOM',

	),
	
	'use_local_DTD' => true,
	'local_DTD' => 'DTD' . DIRECTORY_SEPARATOR . 'xhtml1-strict.dtd',
	
);

?>