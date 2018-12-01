<?php

// should be good enough for names in fernch bibliographies although it doesn't do stop words
// ex. AUSTRALIAN GOVERNMENT DEPARTMENT OF HEALTH AND AGEING => Australian Government Department Of Health And Ageing

$file = 'C:\wamp\www\sweeper\not-swept\im-014-fr.html';
$contents = file_get_contents($file);

preg_match_all('/<span style="text-transform:uppercase">(.*?)<\/span>/is', $contents, $upperclass_span_matches, PREG_OFFSET_CAPTURE);
$counter = sizeof($upperclass_span_matches[0]) - 1;
print('<table>');
while($counter > -1) {
	$span_content = $upperclass_span_matches[1][$counter][0];
	$span_offset = $upperclass_span_matches[0][$counter][1];
	print('<tr>
<th align="left">' . $span_content . '</th>
<td>');
	$counter2 = 0;
	$parsing_word = false;
	$parsing_characer_entity = false;
	$parsing_mac = false;
	$possibly_parsing_mac = false;
	$new_span_content = '';
	while($counter2 < strlen($span_content)) {
		if($parsing_characer_entity) {
			if($span_content[$counter2] === ';') {
				$parsing_characer_entity = false;
			}
			$new_span_content .= strtolower($span_content[$counter2]); // notice that the intention is for character entities to also be converted to lower class by this
		} else {
			if($span_content[$counter2] === '&') {
				$parsing_characer_entity = true;
				$new_span_content .= $span_content[$counter2];
			} else {
				 if(!$parsing_word) {
					if($span_content[$counter2] === 'M' || $span_content[$counter2] === 'm') {
						$possibly_parsing_mac = true;
						$parsing_word = true;
						$new_span_content .= strtoupper($span_content[$counter2]);
					} elseif(preg_match('/[A-Z]/is', $span_content[$counter2])) {
						$parsing_word = true;
						$new_span_content .= strtoupper($span_content[$counter2]);
					} else {
						$new_span_content .= strtolower($span_content[$counter2]);
					}
				} else {
					if($possibly_parsing_mac) {
						if($span_content[$counter2] === 'C' || $span_content[$counter2] === 'c') {
							$parsing_mac = true;
						} elseif(!preg_match('/[A-Z]/is', $span_content[$counter2])) {
							$parsing_word = false;
						}
						$new_span_content .= strtolower($span_content[$counter2]);
						$possibly_parsing_mac = false;
					} elseif($parsing_mac) {
						$new_span_content .= strtoupper($span_content[$counter2]);
						$parsing_mac = false;
					} else {
						if($span_content[$counter2] === 'C' || $span_content[$counter2] === 'c') {
							if($possibly_parsing_mac) {
								$parsing_mac = true;
								$possibly_parsing_mac = false;
							}
						} elseif(!preg_match('/[A-Z]/is', $span_content[$counter2])) {
							$parsing_word = false;
						}
						$new_span_content .= strtolower($span_content[$counter2]);
					}
				}
			}
		}
		$counter2++;
	}
	print($new_span_content . '</td>
</tr>
');
	$contents = substr($contents, 0, $span_offset) . '<span style="text-transform:uppercase">' . $new_span_content . '</span>' . substr($contents, $span_offset + strlen($upperclass_span_matches[0][$counter][0]));
	$counter--;
}
print('</table>');

file_put_contents($file, $contents);

?>