<?php
/*
Copyright (C) 2006-2007 Mihai Şucan
http://www.robodesign.ro/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

class ReTidy
{
	// work started: August 2006
	public static $version = 1.11;
	public static $build = 20070702;

	// the default profile
	public $config_profile = 'maximum';
	public $config = false;

	// the timers for cleanCode()
	public $timer_start = false;
	public $timer_end = false;
	public $timer_total = false;

	// this is the verbose/debug output
	// use getMessages() to read this value
	protected $messages = '';

	// the configuration used for cleaning a document is named a profile.
	// when you construct a new ReTidy('whatever') object, the profile is loaded from a file using prefix+whatever+suffix.
	// in this case ../profiles/whatever.php
	protected $profile_prefix = '../profiles/';
	protected $profile_suffix = '.php';

	// when you configure a profile to use the external htmltidy binary you must have a folder where the PHP script is allowed to save the generated htmltidy configuration file (which is based on your profile configuration)
	protected $htmltidy_config_dir = '/tmp';

	protected $code = false; // use getCode() and setCode($code)
	protected $dom = false;
	protected $xpath = false;
	protected static $xhtmlns = 'http://www.w3.org/1999/xhtml';

	protected static $lang = array(
		'__toString' => 'ReTidy version %1$s build %2$s by www.robodesign.ro.',
		'loadProfile_done' => 'ReTidy configuration profile has been loaded',
		'loadProfile_notfound' => 'Profile configuration file was not found: %s',
		'loadProfile_error' => 'Profile configuration file did not return an array!',
		'cleanCode_noinit' => 'No code, or no configuration found! You cannot run cleanCode() now.',
		'dom_init_error' => 'Fatal error trying to initialize the DOMDocument!',
		'dom_xpath_warning' => 'The XPath DOM interface could not be initialized!',
		'dom_save_error' => 'Cannot save the DOM code: no DOM found!',
	);

	/***********
	 * TO DO:
	 *
	 * - better page
	 * - better documentation
	 * - GUI for uploading a file to be cleaned and picker for profile
	 * - GUI for editing a profile
	 * - general purpose methods: dom_strip_tag(DOMElementNode $elem), dom_rename_tag (DOMElementNode $elem, string $new_tag_name)
	 * - more code loops, repeat the cleanup until nothing to do remains
	 * - config option: output body only
	 * - fix the new line duplication in <pre> tags
	 * - in dom_merge_parent_attr do proper merging for the style attribute
	 * - configure htmltidy to generate style attributes instead of CSS classes for the purpose of using the style information to generate headings based on font-size, color and other styling information
	 * - generate headings based on case detection, style information and other information
	 *
	 ***********/

	function __construct ($profile = false)
	{
		$this->profile_prefix = dirname(__FILE__) . '/' . $this->profile_prefix;

		$this->loadProfile($profile);
	}

	function __destruct ()
	{
		$this->free_mem();
		if($this->htmltidy_config_file)
			unlink($this->htmltidy_config_file);
	}

	function __toString ()
	{
		return sprintf(self::$lang['__toString'], self::$version, self::$build);
	}

	public function loadProfile ($profile = false)
	{
		if($profile == $this->config_profile && is_array($this->config))
			return true;

		if($profile)
			$this->config_profile = $profile;

		if(!$this->config_profile)
			return false;

		$config_include = $this->profile_prefix . $this->config_profile . $this->profile_suffix;
		if(!file_exists($config_include))
		{
			$this->logMsg(sprintf(self::$lang['loadProfile_notfound'], $config_include));
			return false;
		}

		$this->config = include($config_include);
		if(!is_array($this->config))
		{
			$this->config = false;
			$this->logMsg(self::$lang['loadProfile_error']);
			return false;
		}

		$this->logMsg(self::$lang['loadProfile_done'] . ': ' . $this->config_profile);

		return true;
	}

	public function setCode ($code)
	{
		$this->code = $code;
	}

	public function getCode ()
	{
		return $this->code;
	}

	public function getMessages ()
	{
		return $this->messages;
	}

	public function free_mem ()
	{
		$this->code = false;
		$this->messages = '';
		$this->toc_ids = false;
	}

	public function cleanCode ()
	{
		if(!$this->code || !$this->config)
		{
			$this->logMsg(self::$lang['cleanCode_noinit']);
			return false;
		}

		$this->timer_start = self::getmicrotime();
		$this->logMsg((string)$this);

		if(is_callable('mb_internal_encoding'))
		{
			$old_iencoding = mb_internal_encoding();
			mb_internal_encoding($this->config['encoding']);
		}

		if(count($this->config['strip_tags']) > 1 || (count($this->config['strip_tags']) == 1 && !isset($this->config['strip_tags']['*'])))
			$this->config['strip_child_tags'] = true;
		else
			$this->config['strip_child_tags'] = false;

		if($this->config['add_toc'])
			$this->config['fix_headings'] = true;

		foreach($this->config['macro'] as $method)
		{
			$l = 0; // method loop count

			while($l < $this->config['loop_max'])
			{
				$res = call_user_func(array($this, $method));
				if(!$res && in_array($method, $this->config['important_methods']))
					return false;

				if($res && in_array($method, $this->config['loop_methods']))
					$l++;
				else
					break;
			}
		}

		if($this->config['retidy_mark'])
			$this->code = str_replace('</head>', '<meta name="generator" content="' . (string)$this . "\" />\n</head>", $this->code);

		if(isset($old_iencoding) && is_callable('mb_internal_encoding'))
			mb_internal_encoding($old_iencoding);

		$this->timer_end = self::getmicrotime();
		$this->timer_total = $this->timer_end - $this->timer_start;

		return true;
	}

	protected function logMsg ($msg)
	{
		$prefix = '';
		if($this->timer_start)
		{
			$time = round(self::getmicrotime() - $this->timer_start, 2);
			$prefix = "[$time] ";
		}

		$msg = $prefix . $msg . "\n";

		$this->messages .= $msg;

		return $msg;
	}

	private static function getmicrotime()
	{
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

	protected function dom_init ()
	{
		if($this->dom)
			return $this->xpath_init();

		if($this->xpath)
			$this->xpath = false;

		$this->dom = new DOMDocument('1.0', $this->config['encoding']);
		if(!$this->dom)
		{
			$this->logMsg(self::$lang['dom_init_error']);
			return false;
		}

		//$this->dom->preserveWhiteSpace = false;
		if(!$this->dom->loadXML($this->code))
			$this->dom->loadHTML($this->code);

		//$this->dom->formatOutput = true;
		$this->dom->encoding = $this->config['encoding'];

		$this->logMsg('dom_init = true');

		return $this->xpath_init();
	}

	protected function xpath_init ()
	{
		if(!$this->dom)
			return false;

		if($this->xpath)
			return true;

		$this->xpath = new DOMXPath($this->dom);
		if(!$this->xpath)
		{
			$this->logMsg(self::$lang['dom_xpath_warning']);
			return false;
		} else
			$this->xpath->registerNamespace('h', self::$xhtmlns);

		$this->logMsg('xpath_init = true');

		return true;
	}

	protected function dom_save ()
	{
		if(!$this->dom)
		{
			$this->logMsg(self::$lang['dom_save_error']);
			return false;
		}

		$this->code = $this->dom->saveXML();
		$this->dom = false;

		$this->logMsg("=== DOM saved === ");

		$this->post_dom_strip_xmlns();

		return true;
	}

	protected function post_dom_strip_xmlns ()
	{
		$c = 0;
		$this->code = preg_replace('/\s(xmlns|xmlns:default)\s*=\s*("|\')(.*?)\2/i', ' ', $this->code, -1, $c);
		$this->logMsg("post_dom_strip_xmlns $c ");

		return true;
	}

	protected function post_dom_stripme ()
	{
		$c = 0;
		$regex = '/<([a-z0-9]+)[^>]*\sstripme\s*=\s*("|\')y\2[^>]*>(.*?)<\/\1>/is';
		$this->code = preg_replace($regex, ' $3 ', $this->code, -1, $c);
		$this->logMsg("post_dom_stripme $c ");

		return true;
	}

	protected function post_dom_renametag ()
	{
		$c = 0;
		$regex = '/<([a-z0-9]+)([^>]*)\srenametag\s*=\s*("|\')([a-z0-9]+)\3([^>]*)>(.*?)<\/\1>/is';
		$this->code = preg_replace($regex, '<$4 $2 $5>$6</$4>', $this->code, -1, $c);
		$this->logMsg("post_dom_renametag $c ");

		return true;
	}

	protected function tidy_code ()
	{
		if($this->config['htmltidy_app'] == 'php')
			return $this->htmltidy_php();
		else
			return $this->htmltidy_app();
	}

	private $htmltidy_config_file = false;

	private function htmltidy_gen_config ()
	{
		// cache the result
		if($this->htmltidy_config_file)
			return $this->htmltidy_config_file;

		$this->logMsg('htmltidy_gen_config() started');

		$tmp = tempnam($this->htmltidy_config_dir, 'retidy-htmltidy-config-');
		if(!$tmp)
		{
			$this->logMsg('tempnam() failed!');
			return false;
		}

		$config = '# ' . (string)$this . "\r\n# Profile: " . $this->config_profile . "\r\n# " . date('c') . "\r\n\r\n";

		foreach($this->config['htmltidy'] as $k => $v)
		{
			$config .= "$k: $v\r\n";
		}

		$handle = fopen($tmp, 'wb');
		if(!$handle)
		{
			$this->logMsg('fopen(' . $tmp .') failed!');
			return false;
		}

		if(!fwrite($handle, $config))
		{
			$this->logMsg('fwrite(' . $tmp . ') failed!');
			return false;
		}

		fclose($handle);
		unset($config);

		$this->htmltidy_config_file = $tmp;

		return $tmp;
	}

	private function htmltidy_app ()
	{
		$this->logMsg('=== htmltidy_app ===');

		$descriptorspec = array(
			0 => array('pipe', 'r'), // stdin
			1 => array('pipe', 'w'), // stdout
			2 => array('pipe', 'w')  // stderr
		);

		$pipes = array();

		$cmd = $this->config['htmltidy_app'];
		$config = $this->htmltidy_gen_config();
		if($config)
			$cmd .= ' -config ' . escapeshellarg($config);
		else
			return false;

		$this->logMsg('Will execute: ' . $cmd);

		$process = proc_open($cmd, $descriptorspec, $pipes);

		if(!is_resource($process))
		{
			$this->logMsg('Error when trying to open the process!');
			return false;
		}

		if(!fwrite($pipes[0], $this->code))
		{
			$this->logMsg('Error when trying to fwrite() the code to HTML Tidy!');
			return false;
		}
		fclose($pipes[0]);

		$this->code = stream_get_contents($pipes[1]);
		if(!$this->code)
		{
			$this->logMsg('Warning! HTML Tidy output is empty.');

			fclose($pipes[1]);
			fclose($pipes[2]);
			proc_close($process);

			return false;
		}

		fclose($pipes[1]);

		$errors = stream_get_contents($pipes[2]);
		fclose($pipes[2]);

		$ret_val = proc_close($process);

		$this->logMsg('Status: ' . $ret_val);

		if($this->config['show_tidy_errors'])
			$this->logMsg("--- Error buffer --- \n" . $errors . "\n--- Error buffer end ---");
		else
		{
			$matches = array();
			if(preg_match('/\n(\d+)\s+warnings?\,\s+(\d+)\s+errors?/i', $errors, $matches))
				$this->logMsg('Errors ' . $matches[2] . "\nWarnings: " . $matches[1]);
		}

		return true;
	}

	private function htmltidy_php ()
	{
		$this->logMsg("=== htmltidy_php ===");

		if(!is_callable('tidy_parse_string'))
		{
			$this->logMsg('tidy_parse_string() is not callable! Maybe the php5-tidy extension is not installed.');
			return false;
		}

		$tidy = tidy_parse_string($this->code, $this->config['htmltidy'], $this->config['htmltidy']['input-encoding']);
		if(!$tidy)
		{
			$this->logMsg('tidy_parse_string() failed!');
			return false;
		}

		if(!$tidy->cleanRepair())
		{
			$this->logMsg('$tidy->cleanRepair() failed!');
			return false;
		}

		$this->logMsg('Status: '. $tidy->getStatus() . "\nErrors: " . tidy_error_count($tidy) . "\nWarnings: " . tidy_warning_count($tidy));

		if($this->config['show_tidy_errors'])
			$this->logMsg("--- Error buffer --- \n" . $tidy->errorBuffer . "\n --- Error buffer end ---");

		$this->code = tidy_get_output($tidy);
		if(!$this->code)
			$this->logMsg('Warning! HTML Tidy output is empty.');

		return true;
	}

	protected function pre_tidy_regex ()
	{
		if(!is_array($this->config['pre_tidy_regex']))
			return false;

		$c = $ct = 0;
		foreach($this->config['pre_tidy_regex'] as $key => $val)
		{
			$this->code = preg_replace($val[0], $val[1], $this->code, -1, $c);
			$this->logMsg("pre_tidy_regex[$key] $c ");
			$ct += $c;
		}

		if($ct > 0)
			$this->logMsg("pre_tidy_regex $ct \n");

		return true;
	}

	protected function remove_nodes ()
	{
		if(!is_array($this->config['remove_nodes']))
			return false;

		$c = 0;
		$regex = '(' . implode('|', $this->config['remove_nodes']) . ')';
		$regex = '/<' . $regex . '[\s>].*?<\/\1>/is';
		$this->code = preg_replace($regex, ' ', $this->code, -1, $c);
		$this->logMsg("remove_nodes $c ");

		return true;
	}

	protected function my_strip_tags ()
	{
		if(!is_array($this->config['strip_tags']) || !isset($this->config['strip_tags']['*']))
			return false;

		$c = 0;
		$regex = '/<\/?(' . implode('|', $this->config['strip_tags']['*']) . ')[^>]*>/i';
		$this->code = preg_replace($regex, ' ', $this->code, -1, $c);
		$this->logMsg("strip_tags[*] $c ");

		return true;
	}

	protected function strip_lang ()
	{
		if ($this->config['strip_lang'])
		{
			$c = 0;
			$this->code = preg_replace('/\slang\s*=\s*("|\')(.*?)\1/is', '', $this->code, -1, $c);
			$this->logMsg("strip_lang $c ");
		}

		if ($this->config['strip_xmllang'])
		{
			$c = 0;
			$this->code = preg_replace('/\sxml:lang\s*=\s*("|\')(.*?)\1/is', '', $this->code, -1, $c);
			$this->logMsg("strip_xmllang $c ");
		}

		return true;
	}

	protected function strip_br_dupes ()
	{
		if (!$this->config['strip_br_dupes'])
			return false;

		$c = 0;
		$this->code = preg_replace('/(\s*<br\s*\/?>\s*)+/i', ' <br /> ', $this->code, -1, $c);
		$this->logMsg("strip_br_dupes $c ");

		return true;
	}

	protected function trim_br_tags ()
	{
		if(!is_array($this->config['trim_br_tags']))
			return false;

		$ct = $c = 0;
		$regex = '(' . implode('|', $this->config['trim_br_tags']) . ')';

		$this->code = preg_replace('/(\s*<br\s*\/?>\s*)+<(\/?)' . $regex . '([^>]*)>/is', '<\2\3\4> ', $this->code, -1, $c);
		$ct += $c;

		$this->code = preg_replace('/<(\/?)' . $regex . '([^>]*)>(\s*<br\s*\/?>\s*)+/is', '<\1\2\3> ', $this->code, -1, $c);
		$ct += $c;

		$this->logMsg("trim_br_tags $c ");

		return true;
	}

	protected function replace_tags ()
	{
		if(!is_array($this->config['replace_tags']))
			return false;

		$this->logMsg("=== replace_tags === ");

		$c = $ct = 0;
		foreach($this->config['replace_tags'] as $from => $to)
		{
			$this->code = preg_replace('/<(\/?)' . $from . '([\s>])/i', '<\1' . $to . '\2', $this->code, -1, $c);
			$ct += $c;
			$this->logMsg("$from = $to : $c ");
		}
		$this->logMsg("Changes made: $ct ");

		return true;
	}

	protected function dom_regenerate_tables ()
	{
		if (!$this->config['regenerate_tables'] || !$this->dom)
			return false;

		$tables = $this->dom->getElementsByTagName('table');
		$nrtbl = $tables->length;
		$this->logMsg("=== regenerate_tables === \nDOM tables: $nrtbl ");

		$remtables = array();
		for($i=0; $i < $nrtbl; $i++)
			$remtables[] = $tables->item($i);

		foreach($remtables as $i => $tbl)
		{
			$trs = $tbl->getElementsByTagName('tr');
			$nrtrs = $trs->length;
			$restable = array();
			$row = 0;
			for($z=0; $z<$nrtrs; $z++)
			{
				$tr = $trs->item($z);
				$tds = $tr->getElementsByTagName('td');
				$nrtds = $tds->length;
				$maxrow = 0;
				for($y=0; $y<$nrtds; $y++)
				{
					$td = $tds->item($y);
					$currow = 0;
					foreach($td->childNodes as $elem)
					{
						$clone = $elem->cloneNode(true);
						if($elem->nodeType == XML_ELEMENT_NODE && in_array($elem->nodeName, $this->config['regenerate_tables_tr']))
							$currow++;
						$r = $currow+$row;
						if(!isset($restable[$r]))
							$restable[$r] = array();
						if(!isset($restable[$r][$y]))
							$restable[$r][$y] = array();
						$restable[$r][$y][] = $clone;
					}
					if($currow > $maxrow)
						$maxrow = $currow;
				}
				$row += $maxrow;
			}

			$newtable = $this->dom->createElementNS(self::$xhtmlns, 'table');
			foreach($restable as $row)
			{
				$newtr = $this->dom->createElementNS(self::$xhtmlns, 'tr');
				foreach($row as $cell)
				{
					$newtd = $this->dom->createElementNS(self::$xhtmlns, 'td');
					foreach($cell as $elem)
					{
						$newtd->appendChild($elem);
					}
					$newtr->appendChild($newtd);
				}
				$newtable->appendChild($newtr);
			}
			$tbl->parentNode->insertBefore($newtable, $tbl);
		}
		reset($remtables);
		foreach($remtables as $i => $tbl)
			$tbl->parentNode->removeChild($tbl);

		return true;
	}

	protected function dom_fix_text_tags ()
	{
		if (!$this->dom || !$this->xpath || !is_array($this->config['fix_text_tags']))
			return false;

		$query = '//h:' . implode('/text() | //h:', $this->config['fix_text_tags']) . '/text()';
		$nodes = $this->xpath->query($query);

		$this->logMsg("=== fix_text_tags === \nDOM nodes: " . $nodes->length);

		$ct = $c = 0;
		$regex_fq = '\"|\'|&quot;|&#034;|&apos;|&#039;|&lsquo;|&rsquo;';

		foreach($nodes as $child)
		{
			$child->nodeValue = preg_replace(
				array(
					// ,, = &quot;
					'/,,/', 

					// test2 , test = test2, test
					'/([^\.])\s*(,|\.|\?|\!|:)\s*([^\.0-9])/i',

					// test , test2 = test, test2
					'/([^\.0-9])\s*(,|\.|\?|\!|:)\s*([^\.])/i',

					// a ( b = a (b
					'/\s*\(\s*/i',

					// a ) . b = a). b
					'/\s*\)\s*(,|\.|\?|\!|:|;)*\s*/i',

					// 2 - 3 = 2-3
					'/(\d)\s*-\s*(\d)/i',

					// ( &quot; test " ) . = (&quot;test").
					'/\s*(\()?\s*('.$regex_fq.')(.+?)('.$regex_fq.')\s*(,|\.|\?|\!|;|\))*\s*/i',

					// a  / b = a/b
					'/\s*\/\s*/',

					// "2 % , " = "2%,"
					'/(\d)\s*%\s*(,|\.|;|:\?|\!)*\s*/',
				),
				array(
					'"',
					'\1\2 \3',
					'\1\2 \3',
					' (',
					')\1 ',
					'\1-\2',
					' \1\2\3\4\5 ',
					'/',
					'\1%\2 ',
				), $child->nodeValue, -1, $c);

			$ct += $c;
		}

		$this->logMsg("Changes made: $ct ");

		return true;
	}

	protected function dom_fix_headings ()
	{
		if (!$this->dom || !$this->xpath || !$this->config['fix_headings'])
			return false;

		$tags = $this->xpath->query('//h:h1 | //h:h2 | //h:h3 | //h:h4 | //h:h5 | //h:h6');

		$this->logMsg("=== fix_headings === \nDOM nodes: " . $tags->length);

		$c = 0;
		$heading = false;

		foreach($tags as $tag)
		{
			$nodeName = $tag->nodeName;

			if($heading && $heading < $nodeName{1} && ($heading+1) != $nodeName{1})
			{
				$tag->setAttribute('renametag', 'h' . ($heading+1));
				$c++;
			} else
				$heading = $nodeName{1};
		}

		$this->logMsg("Changes made: $c ");

		return true;
	}

	protected function dom_strip_child_tags ()
	{
		if (!$this->dom || !$this->xpath || !$this->config['strip_child_tags'])
			return false;

		$query = '';
		foreach($this->config['strip_tags'] as $parent => $childs)
		{
			if($parent == '*')
				continue;

			$query .= ' | //h:' . $parent . '/h:' . implode(' | //h:' . $parent . '/h:', $childs);
		}
		$query{1} = ' ';
		$query = trim($query);

		$tags = $this->xpath->query($query);

		$this->logMsg("=== strip_child_tags (strip_tags[any_tag]) === \nDOM nodes: " . $tags->length);

		$c = array();

		foreach($tags as $tag)
		{
			$nodeName = $tag->nodeName;
			$pName = $tag->parentNode->nodeName;

			$tag->setAttribute('stripme', 'y');

			if(!isset($c[$pName]))
				$c[$pName] = array();

			if(!isset($c[$pName][$nodeName]))
				$c[$pName][$nodeName] = 0;

			$c[$pName][$nodeName]++;
		}

		foreach($c as $pName => $c2)
		{
			foreach($c2 as $nodeName => $n)
			{
				$this->logMsg("$pName > $nodeName : $n ");
			}
		}

		return true;
	}

	protected function dom_strip_attrs ()
	{
		if (!$this->dom || !$this->xpath || !is_array($this->config['strip_attrs']))
			return false;

		$query = '';
		foreach($this->config['strip_attrs'] as $tag => $attrs)
		{
			if($tag != '*')
			{
				$tag = 'h:' . $tag;
				$query .= ' | //' . $tag . '/@' . implode(' | //' . $tag . '/@', $attrs);
			} else
				$query .= ' | //@' . implode(' | //@', $attrs);
		}

		$query{1} = ' ';
		$query = trim($query);

		$attrs = $this->xpath->query($query);

		foreach($attrs as $attr)
		{
			$elem = $attr->ownerElement;
			$elem->removeAttributeNode($attr);
		}

		$this->logMsg('strip_attrs ' . $attrs->length);

		return true;
	}

	protected function dom_strip_only_child ()
	{
		if (!$this->dom || !$this->xpath || !is_array($this->config['strip_only_child']))
			return false;

		$query = '';

		foreach($this->config['strip_only_child'] as $parent => $childs)
		{
			$query .= ' | //h:' . $parent . '/h:' . implode('[count(..)=1] | //h:' . $parent . '/h:', $childs) . '[count(..)=1]';
		}

		$query{1} = ' ';
		$query = trim($query);

		$tags = $this->xpath->query($query);
		if(!$tags || !$tags->length)
			return false;

		$this->logMsg("=== strip_only_child === \nDOM nodes: " . $tags->length);

		$c = array();

		foreach($tags as $tag)
		{
			$nodeName = $tag->nodeName;
			$pName = $tag->parentNode->nodeName;

			$tag->setAttribute('stripme', 'y');

			if(!isset($c[$pName]))
				$c[$pName] = array();

			if(!isset($c[$pName][$nodeName]))
				$c[$pName][$nodeName] = 0;

			$c[$pName][$nodeName]++;
		}

		foreach($c as $pName => $c2)
		{
			foreach($c2 as $nodeName => $n)
			{
				$this->logMsg("$pName > $nodeName : $n ");
			}
		}

		return true;
	}

	protected function dom_strip_parent_only_child ()
	{
		if (!$this->dom || !$this->xpath || !is_array($this->config['strip_parent_only_child']))
			return false;

		$query = '';

		foreach($this->config['strip_parent_only_child'] as $parent => $childs)
		{
			$query .= ' | //h:' . $parent . '/h:' . implode('[count(..)=1] | //h:' . $parent . '/h:', $childs) . '[count(..)=1]';
		}

		$query{1} = ' ';
		$query = trim($query);

		$tags = $this->xpath->query($query);
		if(!$tags || !$tags->length)
			return false;

		$this->logMsg("=== strip_parent_only_child === \nDOM nodes: " . $tags->length);

		$c = array();

		foreach($tags as $tag)
		{
			$nodeName = $tag->nodeName;
			$pName = $tag->parentNode->nodeName;

			$tag->parentNode->setAttribute('stripme', 'y');

			if(!isset($c[$pName]))
				$c[$pName] = array();

			if(!isset($c[$pName][$nodeName]))
				$c[$pName][$nodeName] = 0;

			$c[$pName][$nodeName]++;
		}

		foreach($c as $pName => $c2)
		{
			foreach($c2 as $nodeName => $n)
			{
				$this->logMsg("$pName > $nodeName : $n ");
			}
		}

		return true;
	}

	protected function dom_merge_parent_attr ()
	{
		if (!$this->dom || !$this->xpath || !is_array($this->config['merge_parent_attr']))
			return false;

		$query = '';

		foreach($this->config['merge_parent_attr'] as $tag)
		{
			$query .= ' | //h:' . $tag . '/h:' . $tag . '[count(..)=1 and @*]';
		}

		$query{1} = ' ';
		$query = trim($query);

		$tags = $this->xpath->query($query);
		if(!$tags || !$tags->length)
			return false;

		$this->logMsg("=== merge_parent_attr === \nDOM nodes: " . $tags->length);

		$c = array();

		foreach($tags as $tag)
		{
			$nodeName = $tag->nodeName;
			$pNode = $tag->parentNode;

			foreach($tag->attributes as $attr)
			{
				$pNode->setAttribute($attr->name, $attr->value);
			}
			$tag->setAttribute('stripme', 'y');

			if(!isset($c[$nodeName]))
				$c[$nodeName] = 0;

			$c[$nodeName]++;
		}

		foreach($c as $nodeName => $c2)
		{
			$this->logMsg("$nodeName : $c2 ");
		}

		return true;
	}

	protected function dom_strip_no_attr ()
	{
		if (!$this->dom || !$this->xpath || !is_array($this->config['strip_no_attr']))
			return false;

		$query = '';

		foreach($this->config['strip_no_attr'] as $tag)
		{
			$query .= ' | //h:' . $tag . '[not(@*)]';
		}

		$query{1} = ' ';
		$query = trim($query);

		$tags = $this->xpath->query($query);
		if(!$tags || !$tags->length)
			return false;

		$this->logMsg("=== strip_no_attr === \nDOM nodes: " . $tags->length);

		$c = array();

		foreach($tags as $tag)
		{
			$nodeName = $tag->nodeName;

			$tag->setAttribute('stripme', 'y');

			if(!isset($c[$nodeName]))
				$c[$nodeName] = 0;

			$c[$nodeName]++;
		}

		foreach($c as $nodeName => $c2)
		{
			$this->logMsg("$nodeName : $c2 ");
		}

		return true;
	}

	protected function strip_empty_tags ()
	{
		if(!is_array($this->config['strip_empty_tags']))
			return false;

		$regex = implode('|', $this->config['strip_empty_tags']);
		$regex = '/<(' . $regex . ')[^>]*>(\s*|<br\s*\/?>)*<\/\1>/i';
		$c = 1;
		$ct = 0;
		while($c > 0)
		{
			$this->code = preg_replace($regex, ' ', $this->code, -1, $c);
			$ct += $c;
		}
		$this->logMsg("strip_empty_tags $ct ");

		return true;
	}

	protected function combine_inline ()
	{
		if (!is_array($this->config['combine_inline']))
			return false;

		$nr = count($this->config['combine_inline'])-1;
		$f = false;
		$c = -1;
		$ct = 0;
		for ($i = 0; $i <= $nr; $i++)
		{
			$regex = '/<\/' . $this->config['combine_inline'][$i] . '>' . $this->config['combine_inline_chars'] . '<' . $this->config['combine_inline'][$i] . '>/i';
			$this->code = preg_replace($regex, '$1', $this->code, -1, $c);
			$ct += $c;
			if ($c > 0)
				$f = true;

			if ($i == $nr && $f)
			{
				$i = -1;
				$f = false;
			}
		}

		$this->logMsg("combine_inline $ct ");

		return true;
	}

	protected function reorder_tags ()
	{
		if (!is_array($this->config['reorder_tags']))
			return false;

		$c = 0;
		$regex = '(' . implode('|', $this->config['reorder_tags']) . ')';
		$regex = '/<' . $regex . '>([^<>]*)<' . $regex . '>([^<>]*)<\/\3>([^<>]*)<\/\1>([^<>]*)<\3>([^<>]*)<\/\3>/i';
		$this->code = preg_replace($regex, '<$3>$2<$1>$4</$1>$5$6$7</$3>', $this->code, -1, $c);
		$this->logMsg("reorder_tags $c ");

		return true;
	}

	protected function combine_br_tags ()
	{
		if (!is_array($this->config['combine_br_tags']))
			return false;

		$c = 0;
		$regex = '/<\/(' . implode('|', $this->config['combine_br_tags']) . ')>(\s+|(\s*<br\s*\/?>\s*)+)?<\1[^>]*>/i';
		$this->code = preg_replace($regex, ' ', $this->code, -1, $c);
		$this->logMsg("combine_br_tags $c ");

		return true;
	}

	protected function fix_img_pos ()
	{
		if (!$this->config['fix_img_pos'])
			return false;

		$c = 1;
		$ct = 0;
		while($c > 0)
		{
			$this->code = preg_replace('/>([^<]+)<img\s+([^>]+)>/i', '><img \2>\1', $this->code, -1, $c);
			$ct += $c;
		}
		$this->logMsg("fix_img_pos $ct ");

		return true;
	}

	protected function extend_quotes ()
	{
		if(!$this->config['extend_quotes'])
			return false;

		$ct = 0;
		$c = 1;
		$regex = '(\"|\'|&quot;|&#034;|&apos;|&#039;|&lsquo;|&rsquo;)';
		while($c > 0)
		{
			$this->code = preg_replace('/'.$regex.'\s*<([a-z0-9]+)([^>]*)>/i', '<\2\3>\1', $this->code, -1, $c);
			$ct += $c;
		}
		$c = 1;
		while($c > 0)
		{
			$this->code = preg_replace('/<\/([a-z0-9]+)>([\s!\.\?]*)'.$regex.'/i', '\2\3</\1>', $this->code, -1, $c);
			$ct += $c;
		}
		$this->logMsg("extend_quotes $ct ");

		return true;
	}

	protected function combine_broken_tags ()
	{
		if(!is_array($this->config['combine_broken_tags']))
			return false;

		$c = 0;
		$regex = '(' . implode('|', $this->config['combine_broken_tags']) . ')';
		$regex = '/([\p{Ll}a-z\d\p{N}])\s*<\/(' . $regex . ')>\s*<\2(\s+[^>]+)?>\s*([\p{Ll}a-z])/u';
		$this->code = preg_replace($regex, '\1 \5', $this->code, -1, $c);
		$this->logMsg("combine_broken_tags $c ");

		return true;
	}

	protected function hruler ()
	{
		if(!is_array($this->config['hruler']))
			return false;

		$c = 0;
		$regex = '(' . implode('|', $this->config['hruler']) . ')';
		$regex = '/<' . $regex . '[^>]*>(\s*[\p{P}\p{S}\p{Z}]\s*){3,}<\/\1>/isu';
		$this->code = preg_replace($regex, '<hr />', $this->code, -1, $c);
		$this->logMsg("hruler $c");

		return true;
	}

	protected function dom_parse_lists ()
	{
		if(!$this->config['parse_lists'])
			return false;

		$this->logMsg("=== Parsing lists === ");

		if(!$this->dom || !$this->xpath)
			$this->dom_init();

		if(!$this->dom || !$this->xpath)
			return false;

		$query = '//h:' . implode(' | //h:', $this->config['parse_lists_container_tags']);
		$tags = $this->xpath->query($query);

		$this->logMsg("Containers: " . $tags->length);

		$allowed_chars = '((\-|\*|\.|\)|_|\x{2660}|\x{2663}|\x{2665}|\x{2666}|\x{25D0}|\x{25E6}|\x{F0AB}|\x{F076}){1,2})';

		$lists_array = array();

		foreach($tags as $tag)
		{
			$tmp2 = $fuzzy_list_type = $fuzzy_list_started = $list_type = false;
			$list_chars = '';
			$list_elems = array();
			foreach ($tag->childNodes as $child)
			{
				if($child->nodeType != XML_ELEMENT_NODE)
					continue;

				$item_found = $item_nr = $item_type = false;
				$item_chars = '';

				if(in_array($child->nodeName, $this->config['parse_lists_tags']))
				{
					$content = $child->firstChild->nodeValue;
					$content2 = $child->textContent;
					$table_tabs = 0;
					if($this->config['parse_table_tabs'])
						$table_tabs = substr_count($content2, "\t");

					$matches = array();
					if(preg_match('/^\s*([\d\p{N}]{1,2})\s*' . $allowed_chars . '/iu', $content, $matches))
					{
						$item_type = 'ol-1';
						$item_nr = $matches[1];
						$item_chars = $matches[2];
					} else if(preg_match('/^\s*([a-z\p{L}])\s*' . $allowed_chars . '\s+/iu', $content, $matches))
					{
						$item_type = 'ol-a';
						$item_nr = $matches[1];
						$item_chars = $matches[2];
					} else if(preg_match('/^\s*' . $allowed_chars . '/iu', $content, $matches))
					{
						$item_type = 'ul';
						$item_nr = false;
						$item_chars = $matches[1];
					} else if($table_tabs > 0)
					{
						$item_type = 'table-tabs';
						$item_nr = false;
						$item_chars = $table_tabs;
					} else if($this->config['parse_fuzzy_lists'] && preg_match('/:\s*$/', $content2))
					{
						$fuzzy_list_started = true;
						$item_chars = ':';
					} else if($fuzzy_list_started && preg_match('/(;|\.)\s*$/iu', $content2, $matches))
					{
						$tmp = preg_match('/^[\s\d\p{N}\p{P}]*[a-z\p{Ll}]/u', $content);

						if(!$fuzzy_list_type)
						{
							if($tmp)
								$fuzzy_list_type = 'a';
							else
								$fuzzy_list_type = 'A';
						}

						if($tmp2)
						{
							$tmp2 = false;
							if($matches[1] == '.')
								$fuzzy_list_type = $fuzzy_list_started = false;
						}

						if($fuzzy_list_started && $matches[1] == '.' && !$tmp)
						{
							if($fuzzy_list_type == 'a')
								$fuzzy_list_type = $fuzzy_list_started = false;
							else
								$tmp = 2;
						}

						if($fuzzy_list_started)
						{
							$item_type = 'fuzzy-ul';
							$item_nr = false;
							$item_chars = ';';
						}

						if($tmp == 2)
						{
							if(!isset($list_elems[1]))
								$tmp2 = true;
							else
								$fuzzy_list_type = $fuzzy_list_started = false;
						}

					}
				}

				if($item_nr && $list_type && isset($list_elems[$item_nr]))
					$item_found = true;

				if(!$list_type && $item_type)
				{
					$list_type = $item_type;
					$list_chars = $item_chars;
				}

				if($item_type && $list_type && $item_type == $list_type && $item_chars == $list_chars)
				{
					if($item_type == 'ul' || $item_type == 'fuzzy-ul' || $item_type == 'table-tabs')
						$list_elems[] = $child;
					else
						$list_elems[$item_nr] = $child;
				}

				if($list_type && $item_type && ($item_type != $list_type || $item_chars != $list_chars || $item_found))
				{
					if(count($list_elems) > 1 || (count($list_elems) == 1 && $list_type == 'ul' && $this->config['parse_ulist_1li']))
					{
						$lists_array[] = array('type' => $list_type, 'elems' => $list_elems, 'chars' => $list_chars);
					}

					$list_type = $item_type;
					$list_chars = $item_chars;
					$list_elems = array();
					$list_started = false;
					if($item_chars != ':')
						$fuzzy_list_started = $fuzzy_list_type = false;
					if($item_type == 'ul' || $item_type == 'fuzzy-ul' || $item_type == 'table-tabs')
						$list_elems[] = $child;
					else
						$list_elems[$item_nr] = $child;
				}
				if($list_type && !$item_type)
				{
					if(count($list_elems) > 1 || (count($list_elems) == 1 && $list_type == 'ul' && $this->config['parse_ulist_1li'] === true))
					{
						$lists_array[] = array('type' => $list_type, 'elems' => $list_elems, 'chars' => $list_chars);
					}

					$list_started = $list_type = false;
					$list_chars = '';
					$list_elems = array();
					if($item_chars != ':')
						$fuzzy_list_started = $fuzzy_list_type = false;
				}
			}
		}

		$c_li = 0;

		foreach($lists_array as $i => $list)
		{

			// this is used for ltrimming any LI (to eliminate the LI char)
			$regex = false;

			if($list['type'] == 'ul' || $list['type'] == 'fuzzy-ul')
			{
				$container = $this->dom->createElementNS(self::$xhtmlns, 'ul');
				if($list['type'] == 'ul')
					$regex = '/^\s*'.$allowed_chars.'\s*/iu';
			} else if($list['type'] == 'ol-a' || $list['type'] == 'ol-1')
			{
				$container = $this->dom->createElementNS(self::$xhtmlns, 'ol');
				if($list['type'] == 'ol-a') {
					$container->setAttribute('type', 'a');
					$regex = '/^\s*([a-z\p{L}])\s*'.$allowed_chars.'\s+/iu';
				} else
					$regex = '/^\s*([\d\p{N}]{1,2})\s*'.$allowed_chars.'\s*/iu';
			} else if($list['type'] == 'table-tabs')
				$container = $this->dom->createElementNS(self::$xhtmlns, 'table');
			else
				continue;

			// $regex_prev is used for matching "inline list items" in paragraphs that are previousSiblings of ULs
			$regex_prev = '/(:)\s+'.preg_quote($list['chars']).'\s+/u';

			$regex_li = false;
			if($list['chars'] == '-' || $list['chars'] == '*')
				$regex_li = '/(;)\s+'.preg_quote($list['chars']).'\s+/u';
			else
				$regex_li = '/\s+'.preg_quote($list['chars']).'\s+/u';

			// we will only use the above regular expressions *if* $parse_ulist_inline is true, and current $list['type'] is 'ul', and the $list['chars'] is NOT '.'
			if($list['chars'] == '.' || $list['type'] != 'ul' || !$this->config['parse_ulist_inline'])
				$regex_prev = $regex_li = false;

			if($regex_li)
				$regex_li_str = "\\1\x01";

			if($list['type'] == 'table-tabs')
			{
				$regex_li = '/\t+/';
				$regex_li_str = "\x05";
				$li_elem_name = 'td';
			} else
				$li_elem_name = 'li';

			$pNode = false;

			foreach($list['elems'] as $elem)
			{
				$c_li++;
				$li = $this->dom->createElementNS(self::$xhtmlns, $li_elem_name);
				if($regex)
					$elem->firstChild->nodeValue = preg_replace($regex, '', $elem->firstChild->nodeValue);

				foreach($elem->childNodes as $child)
				{
					$clone = $child->cloneNode(true);
					if($regex_li)
					{
						if($clone->hasChildNodes())
							$tmp = $clone->firstChild;
						else
							$tmp = $clone;

						$tmp->nodeValue = preg_replace($regex_li, $regex_li_str, $tmp->nodeValue);
					}
					$li->appendChild($clone);
				}

				if(!$pNode)
				{
					$pNode = $elem->parentNode;
					$pNode->insertBefore($container, $elem);
				}

				$pNode->removeChild($elem);

				if($list['type'] == 'table-tabs')
				{
					$tmp_tr = $this->dom->createElementNS(self::$xhtmlns, 'tr');
					$tmp_tr->appendChild($li);
					$container->appendChild($tmp_tr);
				} else
					$container->appendChild($li);
			}

			$prev = $container->previousSibling;
			if($prev != null && $prev->nodeType != XML_ELEMENT_NODE)
				$prev = $prev->previousSibling;

			if($regex_prev && $prev != null && $prev->nodeName == 'p')
			{
				foreach($prev->childNodes as $child)
				{
					if($child->hasChildNodes())
						$tmp = $child->firstChild;
					else
						$tmp = $child;

					$tmp->nodeValue = preg_replace($regex_prev, "\\1\x03", $tmp->nodeValue, -1, $c);
					if($c > 0)
						break;
				}
			}
		}

		$this->logMsg("parse_lists DOM " . count($lists_array) .  " lists $c_li LIs ");

		if(!$this->dom_save())
			return false;

		if($this->config['parse_table_tabs'])
		{
			$this->code = str_replace("\x05", '</td><td>', $this->code, $c);
			$this->logMsg("parse_table_tabs $c TDs ");
		}

		if($this->config['parse_ulist_inline'])
		{
			$this->code = str_replace("\x01", '</li><li>', $this->code, $c);
			$this->code = preg_replace('/\x03(.+?)<\/p>\s*<ul>/is', '</p><ul><li>\1</li>', $this->code, -1, $ct);
			$this->logMsg("parse_ulist_inline $c LIs $ct ULs ");
		}

		if($this->config['parse_lists_combine_br_tags'])
			$this->combine_br_tags();

		if($this->config['tidy_after_parse_lists'])
			$this->tidy_code();

		$this->logMsg("=== Lists parsed === ");

		return true;
	}

	private $toc_ids = false;
	protected function dom_toc_add ()
	{
		if(!$this->config['add_toc'])
			return false;

		$this->logMsg("=== TOC generation === ");

		if(!$this->dom || !$this->xpath)
			$this->dom_init();

		if(!$this->dom || !$this->xpath)
			return false;

		$tags = $this->xpath->query('//h:h1 | //h:h2 | //h:h3 | //h:h3 | //h:h4 | //h:h5 | //h:h6');

		$this->logMsg("Heading nodes: " . $tags->length);

		$plvl = false;

		$levels = array(
			0 => $this->dom->createElementNS(self::$xhtmlns, 'ul'),
		);

		$this->toc_ids = array();
		$c = 0;

		foreach($tags as $tag)
		{
			$nodeName = $tag->nodeName;
			$lvl = $nodeName{1};

			if($nodeName{0} != 'h' || !is_numeric($lvl))
				continue;

			$uplvl = $lvl-1;
			if($plvl && $lvl < $plvl)
			{
				for($y = $lvl; $y < $plvl; $y++)
					unset($levels[$y]);
			}

			if(!$plvl && $lvl > 1)
			{
				for($y = 1; $y < $lvl; $y++)
				{
					$levels[$y] = $this->dom->createElementNS(self::$xhtmlns, 'ul');
					$li2 = $this->dom->createElementNS(self::$xhtmlns, 'li');
					$li2->appendChild($this->dom->createTextNode('Heading '.$y));
					$li2->appendChild($levels[$y]);
					$levels[($y-1)]->appendChild($li2);
				}
			}

			$id = $tag->getAttribute('id');

			if($this->config['toc_autolink'] && (!$id || $id == null))
			{
				$id = $this->toc_genid($tag->textContent, $c);
				$tag->setAttribute('id', $id);
			}

			$li = $this->dom->createElementNS(self::$xhtmlns, 'li');
			$txt = $this->dom->createTextNode($tag->textContent);
			if(!$txt || $txt == null || $txt == '')
				$txt = 'item '.$i;

			if($this->config['toc_autolink'])
			{
				$a = $this->dom->createElementNS(self::$xhtmlns, 'a');
				$a->setAttribute('href', '#'.$id);
				$a->appendChild($txt);
				$li->appendChild($a);
			} else
				$li->appendChild($txt);

			if(!isset($levels[$uplvl]))
			{
				for($y = min($uplvl, count($levels)); $y < $lvl; $y++)
				{
					$levels[$y] = $this->dom->createElementNS(self::$xhtmlns, 'ul');
					if($levels[($y-1)]->lastChild == null)
					{
						$li2 = $this->dom->createElementNS(self::$xhtmlns, 'li');
						$li2->appendChild($this->dom->createTextNode('Heading '.$y));
						$li2->appendChild($levels[$y]);
						$levels[($y-1)]->appendChild($li2);
					} else
						$levels[($y-1)]->lastChild->appendChild($levels[$y]);
				}
			}
			$levels[$uplvl]->appendChild($li);

			$plvl = $lvl;
			$c++;
		}

		$body = $this->dom->getElementsByTagName('body');
		$body = $body->item(0);
		$body->insertBefore($levels[0], $body->firstChild);

		$this->dom_save();

		$this->logMsg("TOC ".count($this->toc_ids)." IDs $c LIs ");

		if($this->config['tidy_after_toc'])
			$this->tidy_code();

		$this->logMsg("=== TOC generated === ");

		return true;
	}

	private function toc_genid ($txt, $n)
	{
		if($this->config['toc_autolink'] != '#text')
			return $this->config['toc_autolink'] . $n;

		if(!isset($this->toc_ids) || !is_array($this->toc_ids))
			return 'toc_auto_' . $n;

		if(is_callable('mb_strtolower'))
			$id = mb_strtolower($txt);
		else
			$id = strtolower($txt);

		$id = preg_replace('/[\p{P}]/u', '-', $id);
		$id = str_replace(array(' ', '_'), '-', $id);
		if(is_callable('mb_convert_encoding'))
			$id = mb_convert_encoding($id, 'ascii');
		$id = preg_replace('/[^a-z0-9\-]/', '-', $id);
		$id = preg_replace('/\-+/', '-', $id);
		$id = trim($id, '-');
		$id = preg_replace('/^\d+/', '', $id);
		$id = trim($id, '-');

		if(!isset($id{1}))
			$id = 'hid-' . $n;

		if(in_array($id, $this->toc_ids))
			$id .= '-'.$n;

		$this->toc_ids[] = $id;

		return $id;
	}

	protected function final_regex ()
	{
		if(!is_array($this->config['final_regex']))
			return false;

		$c = $ct = 0;
		foreach($this->config['final_regex'] as $key => $val)
		{
			$this->code = preg_replace($val[0], $val[1], $this->code, -1, $c);
			$this->logMsg("final_regex[$key] $c ");
			$ct += $c;
		}

		$this->logMsg("final_regex $ct ");

		return true;
	}
}

?>
