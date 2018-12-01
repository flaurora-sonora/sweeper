<?php

$CLF2Array = array(
	'align="right"' => 'class="alignRight"',
	'align="center"' => 'class="alignCenter"',
	'align="left"' => 'class="alignLeft"',
	'valign="top"' => 'class="alignTop"',
	'valign="bottom"' => 'class="alignBottom"',
	'target="_blank"' => 'rel="x"', // this may not be a CLF2 standard
	'width="95%"' => 'class="width95"',
	'width="90%"' => 'class="width90"',
	'width="85%"' => 'class="width85"',
	'width="80%"' => 'class="width80"',
	'width="75%"' => 'class="width75"',
	'width="70%"' => 'class="width70"',
	'width="65%"' => 'class="width65"',
	'width="60%"' => 'class="width60"',
	'width="55%"' => 'class="width55"',
	'width="50%"' => 'class="width50"',
	'width="45%"' => 'class="width45"',
	'width="40%"' => 'class="width40"',
	'width="35%"' => 'class="width35"',
	'width="30%"' => 'class="width30"',
	'width="25%"' => 'class="width25"',
	'width="20%"' => 'class="width20"',
	'width="15%"' => 'class="width15"',
	'width="10%"' => 'class="width10"',
	'width="5%"' => 'class="width5"',
	'bgcolor="#000000"' => 'class="blackBG"',
	'bgcolor="#FFFFFF"' => 'class="whiteBG"',
	'bgcolor="#FF0000"' => 'class="redBG"',
	'bgcolor="#0000FF"' => 'class="blueBG"',
	'bgcolor="#009933"' => 'class="greenBG"',
	'bgcolor="#CCCCCC"' => 'class="lightgreyBG"',
	'bgcolor="#FFCC33"' => 'class="deepyellowBG"',	
	'nowrap="nowrap"' => 'class="noWrap"',
	//'nowrap' => 'class="noWrap"', // honestly...
	'<nobr>' => '<span class="noWrap">',
	'</nobr>' => '</span>',	
	'noshade' => '',
	//'<br>' => '<br />', // tidy should take care of this.
	'<u>' => '<span style="text-decoration:underline;">',
	'</u>' => '</span>',
	'onClick="' => 'onclick="',
	'onMouseOver="' => 'onmouseover="',
	'onMouseOut="' => 'onmouseout="',
	'</b>' => '</strong>',
	'</i>' => '</em>',
	'<b>' => '<strong>',
	'<i>' => '<em>',
);

$CLF2RxpArray = array(
	
	//'(<table[^>]*)border="*([0-9])"*' => '$1borXXX9o9TableBorder9o9XXXder="$2"',
	//'border=[\s]*"([^\"]*)"' => 'style="border:${1}px solid;"',
	//'border=([0-9]{1,})' => 'style="border:${1}px solid;"',
	
	// maybe only parks canada
	'<(\w+)([^<]*) class="([^"]*)backTop([^"]*)"' => '<$1$2 class="$3alignRight$4"',
	
	// fill empty table cells
	'<td([^<>]*)><\/td>' => '<td$1>&nbsp;</td>',
	'<th([^<>]*)><\/th>' => '<th$1>&nbsp;</th>',
	'<td([^<>]*)\/>' => '<td$1>&nbsp;</td>',
	'<th([^<>]*)\/>' => '<th$1>&nbsp;</th>',	
	
	'target=[\s]*"[^\"]*"' => 'rel="x"',	
	'vspace=[\s]*(")?[^\"]*(")?' => '',
	'hr[^>]*size=[\s]*(")?[^\"]*(")?' => 'hr ',
	'hr[^>]*size=([0-9]{1,})' => 'hr ',
	
	//'ul([^>]*)type=(")?(disc|square|circle)(")?' => 'ul${1}class="${3}"',
	'ul([^>]*)type=(")?(disc|square|circle)(")?' => 'ul${1}',
	'ol([^>]*)type=(")?a(")?' => 'ol${1}class="lower-alpha"',
	'ol([^>]*)type=(")?A(")?' => 'ol${1}class="upper-alpha"',
	'ol([^>]*)type=(")?i(")?' => 'ol${1}class="lower-roman"',
	'ol([^>]*)type=(")?I(")?' => 'ol${1}class="upper-roman"',
	'<\/li>\s*<li style="list-style: none; display: inline(;?)">' => '',

	'<table\s*width="(450|600|100%)"' => '<table class="widthFull"',	
	'<table([^<>]*) width="(450|600|100%)"([^<>]*)>' => '<table$1 class="widthFull"$3>',
	
	//'(<table[^>]*)width="*([^"]*)"*' => '$1widXXX9o9TableWidth9o9XXXth="$2"',
	//'(<img[^>]*)width="*([^"]*)"*' => '$1widXXX9o9ImageWidth9o9XXXth="$2"',
	//'(<img[^>]*)height="*([^"]*)"*' => '$1heiXXX9o9ImageHeight9o9XXXght="$2"',		
	//'(height|width)=[\s]*"([^\"]*)%"' => 'style="${1}:${2}%;"',
	//'(height|width)=[\s]*"([^\"]*)"' => 'style="${1}:${2}px;"',
	//'(height|width)=([0-9]{1,}%)' => 'style="${1}:${2}%;"',
	//'(height|width)=([0-9]{1,})' => 'style="${1}:${2}px;"',
	'nowrap[\s]*=[\s]*"nowrap"' => 'class="noWrap"',
	'nowrap[\s]*=[\s]*nowrap' => 'class="noWrap"',	
	' style="([^"]*)white\-space\s*:\s*nowrap(\s*;\s*)([^"]*)"' => ' style="$1$3" class="noWrap"',
	' style="([^"]*)color\s*:\s*black(\s*;\s*)*?([^"]*)"' => ' style="$1$3"', // we are here saying that nobody will ever have a tag that is styled to be black coloured and subconflationary with a tag that is styled with some other colour
	' style="([^"]*)color\s*:\s*windowtext(\s*;\s*)*?([^"]*)"' => ' style="$1$3"',
	'br([^>]*)clear[\s]*=[\s]*"[^\"]*"' => 'br${1}',
	'br([^>]*)clear=all' => 'br${1}',
	'<ul[^>]*>[\s]*<\/ul>' => '',
	'noshade[\s]*=[\s]*"noshade"' => '',
	'noshade[\s]*=[\s]*noshade' => '',	
	//'<a[\s]*name="([0-9]{1,})([^\"]*)"[\s]*id="[0-9]{1,}[^\"]*"' => '<a name="${1}${2}"',
	'img([^>]*)name=[\s]*"[^\"]*"' => 'img${1}',	
	'<a name="content">[\s]*<\/a>[\s]*<a name="content">[\s]*<\/a>' => '<a name="content"></a>',
	//'<a([^>]*)name=[\s]*"([^a-zA-Z_]){1}([^\"]*)"([^>]*)>' => '<a${1}name="${3}"${4}>',
	//'<a([^>]*)id=[\s]*"([^a-zA-Z_]){1}([^\"]*)"([^>]*)>' => '<a${1}id="${3}"${4}>',
	'href[\s]*=[\s]*"#(top|debut)"' => 'href="#tphp"',
	//'<input([^>]*)[\s]*\/[\s]*>' => '<div><input${1} /></div>',
	//'<input([^>]*)>' => '<div><input${1}></div>',
	//'<td[^>]*width="(130|131|132)"[^>]*>' => '<td style="display:none;">',
	' style="?vertical\-align:\s*top;?"?' => ' class="alignTop"',
	' style="?vertical\-align:\s*bottom;?"?' => ' class="alignBottom"',
	'valign[\s]*=[\s]*(\")?top(\")?' => 'class="alignTop"',
	'valign[\s]*=[\s]*(\")?bottom(\")?' => 'class="alignBottom"',
	'valign[\s]*=[\s]*(\")?middle(\")?' => 'class="alignBottomLeft"',
	'align[\s]*=[\s]*(\")?top(\")?' => 'class="alignTop"',
	'align[\s]*=[\s]*(\")?bottom(\")?' => 'class="alignBottom"',
	'align[\s]*=[\s]*(\")?right(\")?' => 'class="alignRight"',
	'align[\s]*=[\s]*(\")?center(\")?' => 'class="alignCenter"',
	'align[\s]*=[\s]*(\")?middle(\")?' => 'class="alignCenter"',
	'align[\s]*=[\s]*(\")?left(\")?' => 'class="alignLeft"',
	'align[\s]*=[\s]*(\")?absbottom(\")?' => 'class="alignBottomLeft"',
	'align[\s]*=[\s]*(\")?absmiddle(\")?' => 'class="alignBottomLeft"',
	'style="([^"]*?)text-align\s*:\s*left;?([^"]*?)"' => 'style="$1$2" class="alignLeft"',
	'style="([^"]*?)text-align\s*:\s*center;?([^"]*?)"' => 'style="$1$2" class="alignCenter"',
	'style="([^"]*?)text-align\s*:\s*right;?([^"]*?)"' => 'style="$1$2" class="alignRight"',
	'<(img|meta|hr)([^>]*)([^\/])>' => '<$1$2$3 />',
//	'(<\/div>\s*<(h[1-6][^>]*|p|hr))([^>]*(\/|)>)' => '$1 style="margin-top:1em;"$3',
	//'(<[^>]* style=")([^;]*;)([^"]*)\2("[^>]*>)' => '$1$3$2$4',
	//'(<[^>]* style=")([^"]*;)([^;]*;)([^"]*;)\3([^"]*(;|))("[^>]*>)' => '$1$3$2$4',	
	/// ...
	//'<img([^>]*)>' => '<img alt=""$1>',
	//'<img([^>]*)( alt="[^>]+")([^>]*) alt=""([^>]*)>' => '<img$1$2$3$4>',
	//'<img([^>]*) alt=""([^>]*)( alt="[^>]+")([^>]*)>' => '<img$1$2$3$4>',
	
	//'<a name="([0-9]{1,3})">' => '<a name="section$1" id="section$1">',
	//'<a name="([0-9]{1,3})" id="\1">' => '<a name="section$1" id="section$1">',
	//'<a name="([0-9]{1,3}_[0-9a-z]{1,3})">' => '<a name="section$1" id="section$1">',
	//'<a name="([0-9]{1,3}_[0-9a-z]{1,3})" id="\1">' => '<a name="section$1" id="section$1">',
	//'<a name="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})">' => '<a name="section$1" id="section$1">',
	//'<a name="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})" id="\1">' => '<a name="section$1" id="section$1">',
	//'<a name="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})">' => '<a name="section$1" id="section$1">',
	//'<a name="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})" id="\1">' => '<a name="section$1" id="section$1">',	
	//'<a name="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})">' => '<a name="section$1" id="section$1">',
	//'<a name="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})" id="\1">' => '<a name="section$1" id="section$1">',		
	//'<a name="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})">' => '<a name="section$1" id="section$1">',
	//'<a name="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})" id="\1">' => '<a name="section$1" id="section$1">',
	
	//'<a id="([0-9]{1,3})">' => '<a id="section$1" name="section$1">',
	//'<a id="([0-9]{1,3})" name="\1">' => '<a id="section$1" name="section$1">',
	//'<a id="([0-9]{1,3}_[0-9a-z]{1,3})">' => '<a id="section$1" name="section$1">',
	//'<a id="([0-9]{1,3}_[0-9a-z]{1,3})" name="\1">' => '<a id="section$1" name="section$1">',
	//'<a id="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})">' => '<a id="section$1" name="section$1">',
	//'<a id="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})" name="\1">' => '<a id="section$1" name="section$1">',
	//'<a id="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})">' => '<a id="section$1" name="section$1">',
	//'<a id="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})" name="\1">' => '<a id="section$1" name="section$1">',	
	//'<a id="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})">' => '<a id="section$1" name="section$1">',
	//'<a id="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})" name="\1">' => '<a id="section$1" name="section$1">',		
	//'<a id="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})">' => '<a id="section$1" name="section$1">',
	//'<a id="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})" name="\1">' => '<a id="section$1" name="section$1">',
	
	// (2011-06-27) the name attribute is deprecated
	// (2012-05-04) this should be left to structure function
	/*
	'<a([^>]*?) name="([0-9]{1,3})"([^>]*?)>' => '<a$1 id="section$2"$3>',
	'<a([^>]*?) name="([0-9]{1,3})" id="\1"([^>]*?)>' => '<a$1 id="section$2"$3>',
	'<a([^>]*?) name="([0-9]{1,3}_[0-9a-z]{1,3})"([^>]*?)>' => '<a$1 id="section$2"$3>',
	'<a([^>]*?) name="([0-9]{1,3}_[0-9a-z]{1,3})" id="\1"([^>]*?)>' => '<a$1 id="section$2"$3>',
	'<a([^>]*?) name="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})"([^>]*?)>' => '<a$1 id="section$2"$3>',
	'<a([^>]*?) name="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})" id="\1"([^>]*?)>' => '<a$1 id="section$2"$3>',
	'<a([^>]*?) name="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})"([^>]*?)>' => '<a$1 id="section$2"$3>',
	'<a([^>]*?) name="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})" id="\1"([^>]*?)>' => '<a$1 id="section$2"$3>',
	'<a([^>]*?) name="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})"([^>]*?)>' => '<a$1 id="section$2"$3>',
	'<a([^>]*?) name="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})" id="\1"([^>]*?)>' => '<a$1 id="section$2"$3>',
	'<a([^>]*?) name="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})"([^>]*?)>' => '<a$1 id="section$2"$3>',
	'<a([^>]*?) name="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})" id="\1"([^>]*?)>' => '<a$1 id="section$2"$3>',
	
	' id="([0-9]{1,3})"' => ' id="section$1"',
	' id="([0-9]{1,3})" name="\1"' => ' id="section$1"',
	' id="([0-9]{1,3}_[0-9a-z]{1,3})"' => ' id="section$1"',
	' id="([0-9]{1,3}_[0-9a-z]{1,3})" name="\1"' => ' id="section$1"',
	' id="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})"' => ' id="section$1"',
	' id="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})" name="\1"' => ' id="section$1"',
	' id="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})"' => ' id="section$1"',
	' id="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})" name="\1"' => ' id="section$1"',	
	' id="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})"' => ' id="section$1"',
	' id="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})" name="\1"' => ' id="section$1"',		
	' id="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})"' => ' id="section$1"',
	' id="([0-9]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3}_[0-9a-z]{1,3})" name="\1"' => ' id="section$1"',
	
	'(<a href="[^"#]*#)([0-9][^"]*">)' => '$1section$2',
	*/
	
	//'<span class="([^"]*)align(Top|Bottom|Center|Right|Left|TopCenter|BottomCenter|TopRight|BottomRight|TopLeft|BottomLeft)([^"]*)">' => '<span class="$1$3">',
	// putting these in a better order:
	'<span class="([^"]*)align(TopCenter|BottomCenter|TopRight|BottomRight|TopLeft|BottomLeft|Top|Bottom|Center|Right|Left)([^"]*)">' => '<span class="$1$3">',	
	// this should be done for all inline tags... this is probably good enough. it should finally end up in DOM_CLF2
	'<strong class="([^"]*)align(TopCenter|BottomCenter|TopRight|BottomRight|TopLeft|BottomLeft|Top|Bottom|Center|Right|Left)([^"]*)">' => '<strong class="$1$3">',	
	'<em class="([^"]*)align(TopCenter|BottomCenter|TopRight|BottomRight|TopLeft|BottomLeft|Top|Bottom|Center|Right|Left)([^"]*)">' => '<em class="$1$3">',	
	'<sup class="([^"]*)align(TopCenter|BottomCenter|TopRight|BottomRight|TopLeft|BottomLeft|Top|Bottom|Center|Right|Left)([^"]*)">' => '<sup class="$1$3">',	
	'<acronym class="([^"]*)align(TopCenter|BottomCenter|TopRight|BottomRight|TopLeft|BottomLeft|Top|Bottom|Center|Right|Left)([^"]*)">' => '<acronym class="$1$3">',	
	'<a class="([^"]*)align(TopCenter|BottomCenter|TopRight|BottomRight|TopLeft|BottomLeft|Top|Bottom|Center|Right|Left)([^"]*)">' => '<a class="$1$3">',	
	'<span class="\s*">(.*?)<\/span>' => '$1',
	'<span class="center" style="\s*width:600px;\s*text-align:left;\s*">(.*?)<\/span>' => '$1',
	
	
	'class="([^"]*)align([^" ]*)Center([^"]*) alignCenter([^"]*)"' => 'class="$1align$2Center$3$4"',
	'class="([^"]*)align([^" ]*)Right([^"]*) alignRight([^"]*)"' => 'class="$1align$2Right$3$4"',
	'class="([^"]*)align([^" ]*)Left([^"]*) alignLeft([^"]*)"' => 'class="$1align$2Left$3$4"',
	
	'class="([^"]*)align([^" ]+?)(Center){2,}([ "])' => 'class="$1align$2Center$4',
	'class="([^"]*)align([^" ]+?)(Right){2,}([ "])' => 'class="$1align$2Right$4',
	'class="([^"]*)align([^" ]+?)(Left){2,}([ "])' => 'class="$1align$2Left$4',	
	
	'class="([^"]*)alignTop([^"]*) alignCenter([^"]*)"' => 'class="$1alignTopCenter$2$3"',
	'class="([^"]*)alignBottom([^"]*) alignCenter([^"]*)"' => 'class="$1alignBottomCenter$2$3"',
	'class="([^"]*)alignTop([^"]*) alignRight([^"]*)"' => 'class="$1alignTopRight$2$3"',
	'class="([^"]*)alignBottom([^"]*) alignRight([^"]*)"' => 'class="$1alignBottomRight$2$3"',
	'class="([^"]*)alignTop([^"]*) alignLeft([^"]*)"' => 'class="$1alignTopLeft$2$3"',
	'class="([^"]*)alignBottom([^"]*) alignLeft([^"]*)"' => 'class="$1alignBottomLeft$2$3"',
	'class="([^"]*)alignCenter([^"]*) alignTop([^"]*)"' => 'class="$1alignTopCenter$2$3"',
	'class="([^"]*)alignCenter([^"]*) alignBottom([^"]*)"' => 'class="$1alignBottomCenter$2$3"',
	'class="([^"]*)alignRight([^"]*) alignTop([^"]*)"' => 'class="$1alignTopRight$2$3"',
	'class="([^"]*)alignRight([^"]*) alignBottom([^"]*)"' => 'class="$1alignBottomRight$2$3"',
	'class="([^"]*)alignLeft([^"]*) alignTop([^"]*)"' => 'class="$1alignTopLeft$2$3"',
	'class="([^"]*)alignLeft([^"]*) alignBottom([^"]*)"' => 'class="$1alignBottomLeft$2$3"',		
	
	'(<[^>]+? class=")([^"]*)("[^>]*?) class="([^"]*)"([^>]*?>)' => '$1$2 $4$3$5',
	'(<[^>]+? class=")([^"]*)("[^>]*?) class="([^"]*)"([^>]*?>)' => '$1$2 $4$3$5',
	'(<[^>]+? class=")([^"]*)("[^>]*?) class="([^"]*)"([^>]*?>)' => '$1$2 $4$3$5',
	'(<[^>]+? class=")([^"]*)("[^>]*?) class="([^"]*)"([^>]*?>)' => '$1$2 $4$3$5',

	'(<[^>]+? style=")([^"]*)("[^>]*?) style="([^"]*)"([^>]*?>)' => '$1$2 $4$3$5',
	'(<[^>]+? style=")([^"]*)("[^>]*?) style="([^"]*)"([^>]*?>)' => '$1$2 $4$3$5',
	'(<[^>]+? style=")([^"]*)("[^>]*?) style="([^"]*)"([^>]*?>)' => '$1$2 $4$3$5',
	'(<[^>]+? style=")([^"]*)("[^>]*?) style="([^"]*)"([^>]*?>)' => '$1$2 $4$3$5',

	'(<acronym title="[^"]*">)\1([^<]*)<\/acronym><\/acronym>' => '$1$2</acronym>',
	
	'<b([^\w][^<>]*?)>' => '<strong$1>',
	'<i([^\w][^<>]*?)>' => '<em$1>',

	);

?>