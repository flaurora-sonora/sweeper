<?php

$LayoutTableArray = array(

'<table([^>]*)>' => '<div$1>',
'<tr([^>]*)>' => '<div$1 style="clear:both;">',
'<\/tr>' => '</div>',
'<td([^>]*)>' => '<div$1 style="float:left;">',
'<\/td>' => '</div>',
'<th([^>]*)>' => '<div$1 style="float:left;">',
'<\/th>' => '</div>',
'<\/table>' => '</div>',
'<div([^>]*) colspan="[^"]*"([^>]*)>' => '<div$1$2>',
'<div([^>]*) rowspan="[^"]*"([^>]*)>' => '<div$1$2>',
'<div([^>]*) scope="[^"]*"([^>]*)>' => '<div$1$2>',
'<div([^>]*) width="600"([^>]*)>' => '<div$1 style="width:100%;"$2>',
'<div([^>]*) border="0"([^>]*)>' => '<div$1$2>',
'<div([^>]*) cellspacing="[^"]*"([^>]*)>' => '<div$1$2>',
'<div([^>]*) cellpadding="[^"]*"([^>]*)>' => '<div$1$2>',
  
);

?>