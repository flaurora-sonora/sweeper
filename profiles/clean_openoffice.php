<?php

include('mappings' . DIRECTORY_SEPARATOR . 'CLF2.php');

return array(

	// execution macro: this defines the methods you want to call, each and every one of these "cleans" something
	// call the methods how many times you want, change the order as needed
	
	'pre_internal_templating_macro' => array(

	'tidy_code',

	),
	
	'macro' => array(
	
	'clean_openoffice',
	'post_dom',
	
	'detected_acronyms',
	'remove_tags_intra_tags',
	
	/*'dom_init',
	'DOM_table_accessibility',
	//'DOM_primalize_anchors',	// this may not be effective may because node cloning takes place upon saving the DOM so that anchors that are not within the document are not primalized?
	//'DOM_redundant_classes',
	'dom_save',	
	
	'post_dom',*/

	'tidy_code',
	
	),
	
	'HTML5' => true,
	
	'apply_acronym_if_near_definition' => true,
	'apply_combined_acronyms' => false,
	
	'apply_combined_abbr' => true,
	
	'abbr_instead_of_acronym' => true,

	'strict_accessibility' => true,
	'table_headers_id_start_count' => 1,
	'table_headers_string' => 'header',
	'table_type' => 'complex',
	'trust_ths' => true,
	
	'make_new_classes' => true,
	'new_classes' => 'embedded', // embedded or new_stylesheet
	'new_class_name' => 'new_class',	
	
	//'turn_captions_into' => 'paragraphs', // paragraphs, captions
	'table_note_size' => '80percent', // normal, 80percent
	'non_breaking_type' => 'nbsp', // nbsp, noWrap
	
	'TOC_sub' => 'lists', // lists, indent, CED
	'trust_headings' => false,  // false, true, if_they_seem_sufficient
	//'HTML5' => false,  // false, true
	'anchor_text' => 'section', // the text to put before anchor numbers that is required since ids beginning with a number do not constitute valid HTML
	'normalize_heading_text' => 'all_indexical_content', // headings, all_indexical_content, false
	'generate_TOC' => false, // false, if_non_existent, true
	'lowest_generated_TOC_level' => 4, // 2, 3, 4, 5, 6

	'use_local_DTD' => true,
	'local_DTD' => 'DTD' . DIRECTORY_SEPARATOR . 'xhtml1-strict.dtd',

	// execute the following search and replace for CLF2
	'CLF2_replace' => $CLF2Array,	
	// execute the following search and replace for CLF2
	'CLF2_regex' => $CLF2RxpArray,
	'normalize_th' => true,
	
	// uncomment the desired quotes style
	//'quotes_style' => 'omit_characters', // this quotes style removes and does not insert quotation characters inside existing or added <q> tags
	//'quotes_style' => 'use_quote_chars_inside_quote_tags', // this quotes style applies quotation characters inside existing or added <q> tags
	'quotes_style' => 'use_quote_chars_outside_quote_tags', // this quotes style applies quotation characters outside existing or added <q> tags
	
	'WET' => 'WET',
	
	'french_footnote_reference_anchor_text' => 'Lien à la note ',
	'french_footnote_anchor_text' => 'Lien &agrave; la r&eacute;f&eacute;rence de la note ',
	'english_footnote_reference_anchor_text' => 'Link to note ',
	'english_footnote_anchor_text' => 'Link to note reference ',
	'footnote_anchor_name' => 'note',
	'footnote_reference_anchor_name' => 'noteref',
	
	'french_endnote_reference_anchor_text' => 'Lien à la note de bas ',
	'french_endnote_anchor_text' => 'Lien &agrave; la r&eacute;f&eacute;rence de la note de bas ',
	'english_endnote_reference_anchor_text' => 'Link to endnote ',
	'english_endnote_anchor_text' => 'Link to endnote reference ',
	'endnote_anchor_name' => 'nnote',
	'endnote_reference_anchor_name' => 'nnoteref',
	
	'strict_accessibility_level' => 0, // 0 = document retains all relevant styles, 1 = color information is lost, 2 = table header normalization is lost
	
);

?>
