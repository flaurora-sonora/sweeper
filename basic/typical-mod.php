<?php  

$basicTypicalArray = array(	

' align="justify"' => '', 
// *** removing align="left" on headings and paragraphs in table headers is not desired since 
// *** the default alignment for these is center rather than left.

// ### redacted text
//'redacted text' => 'redacted&nbsp;text', 
/*'[ redacted' => '<span class="redacted_text">[&nbsp;redacted', 
'redacted ]' => 'redacted&nbsp;]</span>', 
'text ]' => 'text&nbsp;]</span>', 
//'texte r&eacute;dact&eacute;' => 'texte&nbsp;r&eacute;dact&eacute;', 
'[ r&eacute;dact&eacute;' => '<span class="redacted_text">[&nbsp;r&eacute;dact&eacute;', 
'texte ]' => 'texte&nbsp;\]</span>', 
'r&eacute;dact&eacute; ]' => 'r&eacute;dact&eacute;&nbsp;]</span>',*/


//' *** for cells that were created by inserting rows or columns in dreamweaver
'<!--DWLayoutEmptyCell-->' => '',		

//' ### removing page numbers and dots in table of contents
//' *** this first step is done for a .pdf that was broken up
'.Error! Bookmark not defined.' => '0', 

//''</strong><sup><a href="#footnote" name="note" title="Link to footnote "><strong>' => '<sup><a href="#footnote" name="note" title="Link to footnote ">', 
//''</strong></a></sup><strong>' => '</a></sup>', 
//''</em><sup><a href="#footnote" name="note" title="Link to footnote "><em>' => '<sup><a href="#footnote" name="note" title="Link to footnote ">', 
//''</em></a></sup><em>' => '</a></sup>', 
//' *** nice try, but we must change the heredity of the tags for this to work. (see DOM_combine_inline)

//' ### eliminate any empty align attributes
' align=""' => '',

// this presumably comes out of converter.
'<table  align="center"width="' => '<table width="', 

//' ### for missing links
// tidy seems to delete either <a> or <a></a> so that this line is not used...
// disabled 2009-09-14
//'<a>' => '<a><br>---------------------------------------<br>-- Link missing, fix manually        --<br>---------------------------------------<br>',

// tags from PDF
//'<LBody>' => '',
// tidy cleans unknown tags, such as those from PDF.

);
?>
