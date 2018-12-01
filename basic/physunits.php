<?php	



	//If request("physunits") = "on" then

$basicPhysUnitsArray = array(	


//' ### removing false footnotes



' m<sup><a href="#footnote" name="note" title="Link to footnote ">2</a></sup>' => '&nbsp;m<sup>2</sup>',
' m<sup><a href="#footnote" name="note" title="Link to footnote ">3</a></sup>' => '&nbsp;m<sup>3</sup>',  
'/m<sup><a href="#footnote" name="note" title="Link to footnote ">2</a></sup>' => '/m<sup>2</sup>',
'/m<sup><a href="#footnote" name="note" title="Link to footnote ">3</a></sup>' => '/m<sup>3</sup>',  
'(m<sup><a href="#footnote" name="note" title="Link to footnote ">2</a></sup>' => '(m<sup>2</sup>',
'(m<sup><a href="#footnote" name="note" title="Link to footnote ">3</a></sup>' => '(m<sup>3</sup>',  
' s<sup><a href="#footnote" name="note" title="Link to footnote ">2</a></sup>' => '&nbsp;s<sup>2</sup>',
' s<sup><a href="#footnote" name="note" title="Link to footnote ">3</a></sup>' => '&nbsp;s<sup>3</sup>',  
'/s<sup><a href="#footnote" name="note" title="Link to footnote ">2</a></sup>' => '/s<sup>2</sup>',
'/s<sup><a href="#footnote" name="note" title="Link to footnote ">3</a></sup>' => '/s<sup>3</sup>',
'(s<sup><a href="#footnote" name="note" title="Link to footnote ">2</a></sup>' => '(s<sup>2</sup>',
'(s<sup><a href="#footnote" name="note" title="Link to footnote ">3</a></sup>' => '(s<sup>3</sup>',  
' km<sup><a href="#footnote" name="note" title="Link to footnote ">2</a></sup>' => '&nbsp;km<sup>2</sup>',
' km<sup><a href="#footnote" name="note" title="Link to footnote ">3</a></sup>' => '&nbsp;km<sup>3</sup>', 
'/km<sup><a href="#footnote" name="note" title="Link to footnote ">2</a></sup>' => '/km<sup>2</sup>',
'/km<sup><a href="#footnote" name="note" title="Link to footnote ">3</a></sup>' => '/km<sup>3</sup>', 
'(km<sup><a href="#footnote" name="note" title="Link to footnote ">2</a></sup>' => '(km<sup>2</sup>',
'(km<sup><a href="#footnote" name="note" title="Link to footnote ">3</a></sup>' => '(km<sup>3</sup>', 
' cm<sup><a href="#footnote" name="note" title="Link to footnote ">2</a></sup>' => '&nbsp;cm<sup>2</sup>',
' cm<sup><a href="#footnote" name="note" title="Link to footnote ">3</a></sup>' => '&nbsp;cm<sup>3</sup>',  
'/cm<sup><a href="#footnote" name="note" title="Link to footnote ">2</a></sup>' => '/cm<sup>2</sup>',
'/cm<sup><a href="#footnote" name="note" title="Link to footnote ">3</a></sup>' => '/cm<sup>3</sup>',     
'(cm<sup><a href="#footnote" name="note" title="Link to footnote ">2</a></sup>' => '(cm<sup>2</sup>',
'(cm<sup><a href="#footnote" name="note" title="Link to footnote ">3</a></sup>' => '(cm<sup>3</sup>',     
' mm<sup><a href="#footnote" name="note" title="Link to footnote ">2</a></sup>' => '&nbsp;mm<sup>2</sup>',
' mm<sup><a href="#footnote" name="note" title="Link to footnote ">3</a></sup>' => '&nbsp;mm<sup>3</sup>',  
'/mm<sup><a href="#footnote" name="note" title="Link to footnote ">2</a></sup>' => '/mm<sup>2</sup>',
'/mm<sup><a href="#footnote" name="note" title="Link to footnote ">3</a></sup>' => '/mm<sup>3</sup>',   
'(mm<sup><a href="#footnote" name="note" title="Link to footnote ">2</a></sup>' => '(mm<sup>2</sup>',
'(mm<sup><a href="#footnote" name="note" title="Link to footnote ">3</a></sup>' => '(mm<sup>3</sup>',   



' ft<sup><a href="#footnote" name="note" title="Link to footnote ">2</a></sup>' => '&nbsp;ft<sup>2</sup>',
' ft<sup><a href="#footnote" name="note" title="Link to footnote ">3</a></sup>' => '&nbsp;ft<sup>3</sup>', 
'/ft<sup><a href="#footnote" name="note" title="Link to footnote ">2</a></sup>' => '/ft<sup>2</sup>',
'/ft<sup><a href="#footnote" name="note" title="Link to footnote ">3</a></sup>' => '/ft<sup>3</sup>', 
'm<sup><a href="#footnote" name="note" title="Link to footnote ">-1</a></sup>' => 'm<sup>-1</sup>',
'm<sup><a href="#footnote" name="note" title="Link to footnote ">-2</a></sup>' => 'm<sup>-2</sup>',
'm<sup><a href="#footnote" name="note" title="Link to footnote ">-3</a></sup>' => 'm<sup>-3</sup>',
's<sup><a href="#footnote" name="note" title="Link to footnote ">-1</a></sup>' => 's<sup>-1</sup>',
's<sup><a href="#footnote" name="note" title="Link to footnote ">-2</a></sup>' => 's<sup>-2</sup>',
'h<sup><a href="#footnote" name="note" title="Link to footnote ">-1</a></sup>' => 'h<sup>-1</sup>', 







//' *** the following section sometimes misses units that have 



//' *** letters that could form the beginning of a word. (m,s,mi,h,g,l,gal,L,N,Pa,ha)



//' *** because they don't necessarily have spaces after them



//' ### replacing with non-breaking space



' m ' => '&nbsp;m ', //' ****



' m/' => '&nbsp;m/',		  
' km' => '&nbsp;km',
' cm' => '&nbsp;cm',
' mm' => '&nbsp;mm',
' &micro;m' => '&nbsp;&micro;m',
' nm' => '&nbsp;nm',
' ft' => '&nbsp;ft',
' s ' => '&nbsp;s ', //' ****



' s/' => '&nbsp;s/',
' mi ' => '&nbsp;mi ', //' ****



' mi/' => '&nbsp;mi/',
' mi.' => '&nbsp;mi.',
' ha ' => '&nbsp;ha ', //' ****



' kph' => '&nbsp;kph',
' kmph' => '&nbsp;kmph',
' sq. m' => '&nbsp;sq.&nbsp;m',
' sq. km' => '&nbsp;sq.&nbsp;km',
' cu. m' => '&nbsp;cu.&nbsp;m',
' cu. km' => '&nbsp;cu.&nbsp;km',
' sq. ft' => '&nbsp;sq.&nbsp;ft',
' sq. mi' => '&nbsp;sq.&nbsp;mi',
' cu. ft' => '&nbsp;cu.&nbsp;ft',
' cu. mi' => '&nbsp;cu.&nbsp;mi.',
' hrs' => '&nbsp;hrs',
' yrs' => '&nbsp;yrs',
' h ' => '&nbsp;h ', //' ****



//' *** the preceding line will affect times in French, ex. 8 h 30



' h/' => '&nbsp;h/', 
' lb.' => '&nbsp;lb.',
' lb' => '&nbsp;lb',
' g ' => '&nbsp;g ', //' ****



' g/' => '&nbsp;g/',
' kg' => '&nbsp;kg',
' &micro;g' => '&nbsp;&micro;g',
' mg' => '&nbsp;mg',
' l ' => '&nbsp;l ', //' ****



' l/' => '&nbsp;l/',
' gal ' => '&nbsp;gal ', //' ****



' gal/' => '&nbsp;gal/',
' L ' => '&nbsp;L ', //' ****



' L/' => '&nbsp;L/',
' N ' => '&nbsp;N ', //' ****



' N/' => '&nbsp;N/',
' kPa' => '&nbsp;kPa',
' Pa ' => '&nbsp;Pa ', //' ****



' Pa/' => '&nbsp;Pa/',
' yd' => '&nbsp;yd',
' ppm' => '&nbsp;ppm',
' ppb' => '&nbsp;ppb',
' ppt' => '&nbsp;ppt',
' t ' => '&nbsp;t ',

);

$basicPhysUnitsRxpArray = array(	
'([0-9]*10)<sup><a href="#footnote" name="note" title="Link to footnote ">([0-9]{1,3})<\/a><\/sup>' => '$1<sup>$2</sup>',
);

//End If



?>