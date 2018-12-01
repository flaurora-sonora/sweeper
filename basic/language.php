<?php

//If language = "english" Then

//' *** this is for automatic or user-selected English	  
$basicEnglishArray = array(
//' *** remove spaces in english only
' :' => ':',
' ;' => ';',
' !' => '!',
' ?' => '?',
' %<sup>o</sup>C' => '&nbsp;<sup>o</sup>C',
'1 %' => '1%',
'2 %' => '2%',
'3 %' => '3%',
'4 %' => '4%',
'5 %' => '5%',
'6 %' => '6%',
'7 %' => '7%',
'8 %' => '8%',
'9 %' => '9%',
'0 %' => '0%',
'$ 1' => '$1',
'$ 2' => '$2',
'$ 3' => '$3',
'$ 4' => '$4',
'$ 5' => '$5',
'$ 6' => '$6',
'$ 7' => '$7',
'$ 8' => '$8',
'$ 9' => '$9',
'$ 0' => '$0',

);

//End If

//If language = "french" Then

//' *** this is for automatic or user-selected French
$basicFrenchArray = array(
//' *** spaces to non-breaking spaces for french only
' :' => '&nbsp;:',
' ;' => '&nbsp;;',
' !' => '&nbsp;!',
' ?' => '&nbsp;?',
' %' => '&nbsp;%',
' %<sup>o</sup>C' => '&nbsp;<sup>o</sup>C',

//' *** for .asp code
// sweeper is no longer ASP.. ha
//'&nbsp;" + chr(37) + ">' => ' " + chr(37) + ">',
'&nbsp;%>' => ' %>',

' $' => '&nbsp;$',
' k$' => '&nbsp;k$',
' M$' => '&nbsp;M$',
'&nbsp;$ CAN' => '&nbsp;$&nbsp;CAN',
'&nbsp;k$ CAN' => '&nbsp;k$&nbsp;CAN',
'&nbsp;M$ CAN' => '&nbsp;M$&nbsp;CAN',

);

$basicEnglishRxpArray = array(
//' *** in english it is ex. $25k (or "$25 k"?) so can't be fixed because "k" 
//''([0-9]{1,})\s(k|M)\s' => '$1$2 ',
//' *** could be on its own and not necessarily after a number?
);

$basicFrenchRxpArray = array(
//' *** pourcent
'([0-9])\s(p\.)\s(100|cent)' => '$1&nbsp;$2&nbsp;$3',
//' *** page et par example
'(p\.)\s([0-9]|ex\.)' => '$1&nbsp;$2',
//' *** the "|nbsp;" is from the h (hour) unit
'([0-9])(\s|&nbsp;)h\s([0-5])' => '$1&nbsp;h&nbsp;$3',
//' *** for the time with h with a space after the hour number
'([0-9])\sh([0-5])([0-9])' => '$1&nbsp;h$2$3',
//' *** for the time with h without a space after the hour number
'([0-9])h\s([0-5])' => '$1h&nbsp;$2',
//' *** removing spaces around é and É that were caused by editing in dreamweaver
//''>&eacute; ' => '>&eacute;',  
//''>&Eacute; ' => '>&Eacute;', 
//''>&eacute; ' => '>&eacute;',  
//''>&Eacute; ' => '>&Eacute;',
//' *** this section causes problems with acronyms

//' ### turning english footnotes into french footnotes
'<sup><a href="#footnote" name="note" title="Link to footnote ">' => '<sup><a href="#footnote" name="note" title="Lien vers référence ">',
'<a href="#note" name="footnote" title="Link to note ">' => '<a href="#note" name="footnote" title="Lien vers note ">',    

);

//End If

?>