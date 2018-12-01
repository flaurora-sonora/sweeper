<?php

include('mappings' . DIRECTORY_SEPARATOR . 'CLF2.php');
include('basic' . DIRECTORY_SEPARATOR . 'typical-mod.php');
include('basic' . DIRECTORY_SEPARATOR . 'typical-rxp-mod.php');
include('basic' . DIRECTORY_SEPARATOR . 'wingding.php');
include('basic' . DIRECTORY_SEPARATOR . 'language.php');

return array(

	// execution macro: this defines the methods you want to call, each and every one of these "cleans" something
	// call the methods how many times you want, change the order as needed
	'macro' => array(
	
	//'tidy_code',
	
	'basictypical',
	'basictypicalrxp', 
	'basicwingding',	
	
	'dom_init', 
	'DOM_clean_redundant_tags',
	'DOM_delete_empty_attributes',
	'DOM_alt_text',
	'DOM_non_breaking',
	'DOM_false_footnotes',
	'DOM_finish_footnotes',
	'dom_save',
	
//	'undoublyencodeentities', // possibly unnecessary now (2012-01-16) but kept here for insurance (which is low risk since we have never done a document whose content was about HTML character entities)
	'post_dom', //'post_dom_stripme',
	
	'delete_empty_tags',
	
	//'mark_TOC', // needs to be rethought
	'combine_inline', // was disabled since in using brute force to ensure proper nesting this could take an extremely long time to run
	//'unmark_TOC', // needs to be rethought
	
	//'extra_space', // this is currently (2009-07-10) too aggressive for vanilla sweeper
	// do clean inline instead of some of these?
	'footnotes',
	//'heading_anchors',
	//'fix_inline',	// risky although I have not seen it be destructive; also its orange message could scare somebody.
	'non_breaking',
	'dekern',
	'citation',
	
	'tidy_code',
	
	'basiclanguage',
	'remove_default_attributes',
	
	),

	'non_breaking_type' => 'nbsp', // nbsp, noWrap
	
	'use_local_DTD' => true,
	'local_DTD' => 'DTD' . DIRECTORY_SEPARATOR . 'xhtml1-strict.dtd',
	
	'basictypical' => $basicTypicalArray,	
	'basictypicalrxp' => $basicTypicalArrayRxp,
	'basicwingding' => $basicwingding,
	'basicEnglishArray' => $basicEnglishArray,
	'basicFrenchArray' => $basicFrenchArray,
	'basicFrenchRxpArray' => $basicFrenchRxpArray,

	'CLF2_replace' => $CLF2Array,	
	'CLF2_regex' => $CLF2RxpArray,
	
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

	'character_entity_encoding_type' => 'hexadecimal', // we might like to use 'named' but DOM which is currently (2015-06-09) being used doesn't like named entities
);

?>
