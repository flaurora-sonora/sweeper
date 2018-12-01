<?php

return array(

	// execution macro: this defines the methods you want to call, each and every one of these "cleans" something
	// call the methods how many times you want, change the order as needed
	'macro' => array(
	
	'quotation_macro',
	
	'quality_assurance',
	
	),

	// uncomment the desired quotes style
	//'quotes_style' => 'omit_characters', // this quotes style removes and does not insert quotation characters inside existing or added <q> tags
	//'quotes_style' => 'use_quote_chars_inside_quote_tags', // this quotes style applies quotation characters inside existing or added <q> tags
	'quotes_style' => 'use_quote_chars_outside_quote_tags', // this quotes style applies quotation characters outside existing or added <q> tags
	
	// execute the following search and replace for CLF2
	'CLF2_replace' => $CLF2Array,	
	// execute the following search and replace for CLF2
	'CLF2_regex' => $CLF2RxpArray,
	'normalize_th' => false, // TBS stylesheets normalize them by default, otherwise you may want to turn this on.
	
	'use_local_DTD' => true,
	'local_DTD' => 'DTD' . DIRECTORY_SEPARATOR . 'xhtml1-strict.dtd',

	
);

?>
