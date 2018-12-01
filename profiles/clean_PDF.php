<?php

return array(

	// execution macro: this defines the methods you want to call, each and every one of these "cleans" something
	// call the methods how many times you want, change the order as needed
	
	'pre_internal_templating_macro' => array(

	'pre_PDF',
	'pre_word', // mostly handling microsoft word comments
	
	'tidy_code',
	

	
	'dom_init',
	'DOM_stylesheets_to_styles',
	'DOM_double_quotes_in_attributes', // is this necessary? they seem to become encoded as &quot;
	'DOM_save',
	
	'post_dom',
	
	'clean_PDF',
	
	),
	
	'macro' => array(
	
	'dom_init',
	'DOM_word_table_clean',
	'DOM_clean_tables', // before clean_word since it depends on the empty paragraphs that are cleaned thatby
	'DOM_word_font_size',
	'DOM_block_in_cell', // separate from the above because it would set to be stripped the "caption" in the table that is not put outside before the DOM is saved.
	'dom_save',	
	
	'post_dom',
	
	'clean_word', // moved 2011-09-06
	
	'generate_tidyer_DOM',
	'word_lists', // Warning: mb_strpos(): Empty delimiter in C:\wamp\www\sweeper\retidy.php on line 10750
	'generate_code_from_tidyer_DOM',
	
	'clean_inline',
	
	'dom_init',
	'DOM_clean_extraneous_inline_in_block', // we would probably not want to do this generally.
	'DOM_clean_extraneous_inline',
	'DOM_clean_extraneous_block', // again, we would probably not want to do this generally.
	'dom_save', // join?	
	
	'post_dom',
	
	'extra_styles', // notice that we do not want to use this when converting from one set of stylesheets to another.
	
	'post_dom',
	
	'dom_init',
	'DOM_clean_word',
	'dom_save',
	
	'post_dom',		
	
	'basic',
	'footnotes',
	'remove_default_attributes',
	
	'dom_init',
	'DOM_clean_extraneous_inline_in_block',
	'DOM_clean_extraneous_inline',
	'DOM_clean_extraneous_block', // doing this after templating disrupts the CLF2 <div>s
	'DOM_table_accessibility', // seems to want a dom_save // doesn't apply <th>s... // still not quite complete
	'DOM_primalize_anchors', // this may not be effective may because node cloning takes place upon saving the DOM so that anchors that are not within the document are not primalized?
	'DOM_redundant_classes',
	'dom_save',	
	
	'post_dom',
	
	'delete_all_empty_styles',
	
	'remove_word_styles', // very hacky

	'exhaustive_DOM_styles_to_classes_full', // doesn't seem to work
	
	'dom_init',
	'DOM_make_new_classes',
	'dom_save',
	
	'post_dom',

	// for this style in the stylesheets (unlikely).

	'extra_space', //--
	'clean_ineffective_inline',
	'quotation_macro',
	'basic',
	'quality_assurance',
	'tidyer_code_named_entities',
	
	),
	
	'strict_accessibility' => true,
	'table_headers_id_start_count' => 1,
	'table_headers_string' => 'header',
	'table_type' => 'complex',
	'trust_ths' => false,
	
	'make_new_classes' => true,
	'new_classes' => 'embedded', // embedded or new_stylesheet
	'new_class_name' => 'new_class',	
	
	//'turn_captions_into' => 'paragraphs', // paragraphs, captions
	'table_note_size' => '80percent', // normal, 80percent
	'non_breaking_type' => 'nbsp', // nbsp, noWrap
	
	'TOC_sub' => 'lists', // lists, indent, CED
	'trust_headings' => false,  // false, true, if_they_seem_sufficient
	'HTML5' => false,  // false, true
	'anchor_text' => 'section', // the text to put before anchor numbers that is required since ids beginning with a number do not constitute valid HTML
	'normalize_heading_text' => 'all_indexical_content', // headings, all_indexical_content, false
	'generate_TOC' => false, // false, if_non_existent, true
	'lowest_generated_TOC_level' => 4, // 2, 3, 4, 5, 6

	'use_local_DTD' => true,
	'local_DTD' => 'DTD' . DIRECTORY_SEPARATOR . 'xhtml1-strict.dtd',

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
	
	//'character_entity_encoding_type' => 'hexadecimal', // we might like to use 'named' but DOM which is currently (2015-06-09) being used doesn't like named entities
	
);

?>
