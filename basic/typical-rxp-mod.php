<?php  

$basicTypicalArrayRxp = array(	

// not sure what this is for (2009-04-23)
//'(<TOCI_Leader>)([0-9a-zA-Z\s\.&;#]{1,10})(<\/TOCI_Leader>)' => '',

// page references
// this is not put into the table of contents section because page references could occur outside of it.
/*'(\.{4,})(\s*)([0-9ivx]{1,6})' => '',
'((&hellip;){2,})(\s*)([0-9ivx]{1,6})' => '',
'((&#8230;){2,})(\s*)([0-9ivx]{1,6})' => '',
// (spaced-out dots)
'(( \.){4,})(\s*)([0-9ivx]{1,6})' => '',*/ // disabled (2011-12-07); consider trim_page_reference() in ReTidy which is better

// sometimes we want the LI_Label (due to the way converter and SaveAsXML work).
//'(<LI_Label>)([0-9a-zA-Z\s\.&;#]{1,10})(<\/LI_Label>)' => '',

);

?>
