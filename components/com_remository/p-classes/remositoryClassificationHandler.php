<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

class booleanSearchParser {
	protected $tokens = array('');
	protected $tokentext = array(false);
	protected $track = array();
	protected $tid = 0;
	protected $next = 0;

	public function parse ($text) {
		$inquote = false;
		while ($text != ($text = str_replace('  ', ' ', $text)));
		for ($i=0; $i<strlen($text); $i++) {
			$char = $text[$i];
			if ('"' == $char) {
				if ($inquote) $this->tid = array_pop($this->track);
				else $this->startNext(true);
				$inquote = !$inquote;
			}
			elseif (!$inquote AND '(' == $char) $this->startNext(false);
			elseif (!$inquote AND ')' == $char) {
				if (count($this->track)) $this->tid = array_pop($this->track);
			}
			else $this->tokens[$this->tid] .= $char;
		}
		$this->addAnds();
		$this->addBrackets();
		foreach ($this->tokens as $i=>$token) if (!$this->tokentext[$i]) $this->tokens[$i] = $this->convertLogic($token);
		$text = $this->recombine();
		$text = $this->finishOff($text);
		return $text;
	}

	private function startNext ($isText) {
		$this->next++;
		$this->tokentext[$this->next] = $isText;
		$this->tokens[$this->tid] .= "#$this->next#";
		array_push($this->track,$this->tid);
		$this->tid = $this->next;
		$this->tokens[$this->tid] = '';
	}

	private function addAnds () {
		$logic = array('NOT', 'OR', 'AND');
		foreach ($this->tokens as $i=>$token) {
			if ($this->tokentext[$i]) continue;
			$anded = array();
			$words = explode (' ',$token);
			foreach ($words as $sub=>$word) {
				if (!in_array($word, $logic) AND $sub > 0 AND !in_array($words[$sub-1], $logic)) array_push($anded, 'AND');
				array_push($anded, $word);
			}
			$this->tokens[$i] = implode(' ', $anded);
		}
	}

	private function addBrackets () {
		$priority = 'OR';
		$nonpriority = 'AND';
		foreach ($this->tokens as $i=>$token) {
			$pmode = false;
			$nmode = false;
			$opened = false;
			$words = explode(' ', $token);
			foreach ($words as $j=>$word) {
				if ($priority == $word) {
					if ($nmode AND $j>0) {
						$words[$j-1] = '('.$words[$j-1];
						$opened = true;
					}
					$pmode = true;
					$nmode = false;
				}
				if ($nonpriority == $word) {
					if ($pmode AND $j>0) $words[$j-1] = $words[$j-1].')';
					if (!$opened) $words[0] = '('.$words[0];
					$pmode = false;
					$nmode = true;
				}
			}
			if ($pmode AND $opened) $words[j] .= ')';
			$this->tokens[$i] = implode(' ', $words);
		}
	}

	private function convertLogic ($text) {
		$regexes = array(
			'/\bOR\b/',
			'/\bAND\b/',
			'/\bNOT\b/'
		);
		$replace = array(
			' ',
			'+',
			'-'
		);

		$text = preg_replace($regexes, $replace, $text);

		$newword = true;
		$parsed = $nonblank = '';
		$depth = 0;
		for ($i=0; $i<strlen($text); $i++) {
			$char = $text[$i];
			if (' ' != $char) $nonblank .= $char;
			if ('(' == $char) {
				$newword = true;
				$start[$depth] = strlen($parsed);
				$parsed .= ' (';
				$depth++;
			}
			elseif (')' == $char) {
				$newword = true;
				$parsed .= $char;
				if (isset($start[$depth])) unset($start[$depth]);
				$depth--;
			}
			elseif (' ' == $char) {
				$newword = true;
				$parsed .= $char;
			}
			elseif ('+' == $char) {
				$newword = true;
				if (isset($start[$depth])) $parsed[$start[$depth]] = '+';
				$parsed .= '+';
			}
			else {
				if ($newword) {
					$start[$depth] = strlen($parsed);
					$parsed .= ' '.$char;
				}
				else $parsed .= $char;
				$newword = false;
			}
		}
		return $parsed;
	}

	private function recombine () {
		$text = isset($this->tokens[0]) ? $this->tokens[0] : '';
		for ($i=1; $i<count($this->tokens); $i++) {
			if ($this->tokentext[$i]) $text = str_replace("#$i#", '"'.$this->tokens[$i].'"', $text);
			else $text = str_replace("#$i#", '('.$this->tokens[$i].')', $text);
		}
		return $text;
	}

	private function finishOff ($parsed) {
		$mess = array('  ', '+ +', '++', '( ', ' )', '+ ', '- ');
		$tidy = array(' ', '+', '+', '(', ')', '+', '-');
		while ($parsed != ($parsed = str_replace($mess, $tidy, $parsed)));
		return $parsed;
	}

}

class remositoryClassificationHandler {
	// Search parameters
	protected $userstuff = array();
	protected $matchtype = 'all';
	protected $allclassify = array();
	protected $allvisible = array();
	protected $typelink = array();

	// Constructor
	private function __construct () {
		$sql = "SELECT * FROM #__downloads_classify WHERE published = 1 ORDER BY type";
		$result = remositoryRepository::doSQLget($sql, 'remositoryClassification');
		if ($result) foreach ($result as $item) {
			$this->allclassify[$item->type][$item->id] = $item;
			if (!$item->hidden) $this->allvisible[$item->type][$item->id] = $item->id;
			$this->typelink[$item->id] = $item->type;
		}
		$this->setNewUserStuff();
		$this->userstuff['pagecount'] = (int) _ITEMS_PER_PAGE;
	}

	// private function to reset the user stuff
	private function setNewUserStuff () {
		$this->userstuff['searchtext'] = '';
		$this->userstuff['categories'] = array();
		$this->userstuff['submitter'] = 0;
		$this->userstuff['newsearch'] = true;
		$this->userstuff['clstack'] = array();
		$this->userstuff['onlyfree'] = false;
	}

	// public static function
	public static function getInstance ($new=false) {
		static $instance;
		if (empty($instance)) {
			$instance = new remositoryClassificationHandler();
			$instance->loadCookie();
		}
		if ($new) $instance->setNewUserStuff();
		return $instance;
	}

	private function loadCookie () {
		$this->userstuff = array();
		if (!empty($_COOKIE['remositoryClassification'])) {
			$cookiestuff = $_COOKIE['remositoryClassification'];
			if (strlen($cookiestuff) > 8) {
				$datastuff = substr($cookiestuff,8);
				if (substr($cookiestuff,0,8) == substr(md5($datastuff),12,8)) {
					$this->userstuff = @unserialize(base64_decode($datastuff));
				}
			}
		}
		if (0 == count($this->userstuff)) {
			$this->setNewUserStuff();
			$this->setAllCategories();
		}
	}

	// Appears not to be used
	function isNew () {
		return $this->userstuff['newsearch'];
	}

	// public function save - write this object to a cookie
	public function save () {
		$this->userstuff['newsearch'] = false;
		$cookietext = base64_encode(serialize($this->userstuff));
		$validator = substr(md5($cookietext),12,8);
		setcookie('remositoryClassification', $validator.$cookietext, 0, '/');
	}

	// public function to save the search text
	public function setSearchText ($text) {
		$this->userstuff['searchtext'] = $text;
	}

	public function getSearchText () {
		return $this->userstuff['searchtext'];
	}

	// public function - returns array of as yet unselected classifications
	public function unselected () {
		$result = array();
		foreach ($this->getUnselectedClassify() as $type=>$group) {
			$result[$type][0] = "No additional $type filters are available";
			foreach ($group as $id=>$item) {
				if (!in_array($id, $this->userstuff['clstack'])) $result[$type][$id] = $item;
			}
		}
		return $result;
	}

	// public function - add classification to active list
	public function addClassify ($id) {
		if (('*' == $id OR !is_null($this->getType(intval($id)))) AND !in_array($id, $this->userstuff['clstack'])) {
			array_push($this->userstuff['clstack'], $id);
			if ('*' != $id) remositoryRepository::doSQL("UPDATE #__downloads_classify SET frequency=frequency+1 WHERE id=$id");
		}
	}

	// public function - remove classification from active list
	public function removeClassify ($id) {
		$key = array_search($id, $this->userstuff['clstack']);
		if (false !== $key AND null !== $key) unset($this->userstuff['clstack'][$key]);
	}

	// public function - set "only free" flag to true or false
	public function setOnlyFree ($bool) {
		$this->userstuff['onlyfree'] = $bool ? true : false;
	}

	// public function - get "only free" flag
	public function getOnlyFree () {
		return $this->userstuff['onlyfree'];
	}

	// public function - get classifications stack
	public function getClassifyStack () {
		return $this->userstuff['clstack'];
	}

	// public function - get a single classification given ID
	public function getClassify ($id) {
		$type = $this->getType($id);
		if (!is_null($type)) return $this->allclassify[$type][$id];
		return null;
	}

	// public function - get all classifications for a type
	public function getClassifyByType ($type) {
		if (isset($this->allvisible[$type]) AND count($this->allvisible[$type])) {
			foreach ($this->allvisible[$type] as $id) $result[] = $this->allclassify[$type][$id];
			return $result;
		}
		else return array();
	}

	// private function - get the type from a classification ID
	private function getType ($id) {
		return isset($this->typelink[$id]) ? $this->typelink[$id] : null;
	}

	// public function - set categories to give array of category IDs
	public function setCategories ($cats) {
		if (is_array($cats)) $this->userstuff['categories'] = $cats;
		$this->save();
	}

	// public function - set all available categories
	public function setAllCategories () {
		$cmanager = remositoryContainerManager::getInstance();
		$this->userstuff['categories'] = $cmanager->getCategoryIDs();
	}

	public function areAllCategoriesSet () {
		$cmanager = remositoryContainerManager::getInstance();
		$stored = $cmanager->getCategoryIDs();
		if (count(array_diff($stored, $this->userstuff['categories']))) return false;
		else return true;
	}

	// public function - set HTML tag if category is active
	public function checkCategory ($id) {
		if (in_array($id, $this->userstuff['categories'])) return 'checked="checked"';
		else return '';
	}

	// public function - return number of active categories
	public function countCategories () {
		return count($this->userstuff['categories']);
	}

	// public function - get sole category - if there is only one selected
	public function getSoleCategory () {
		if (1 == count($this->userstuff['categories'])) {
			$cats = array_values($this->userstuff['categories']);
			return new remositoryContainer($cats[0]);
		}
		else return null;
	}

	// public function - get current count of items per page
	public function getPageCount () {
		return empty($this->userstuff['pagecount']) ? (int) _ITEMS_PER_PAGE : $this->userstuff['pagecount'];
	}

	// public function - set current count of items per page
	public function setPageCount ($n) {
		$this->userstuff['pagecount'] = intval($n);
	}

	// public function - get the array of files that fits the current criteria
	public function &getFileArray ($remUser, $orderby, $func, &$pagecontrol) {
		if ($this->countCategories()) {
			$sql = $this->getSelectedSQL(true);
			$interface = remositoryInterface::getInstance();
			$database = $interface->getDB();
			$database->setQuery($sql);
			$total = intval($database->loadResult());
			$page = remositoryRepository::getParam($_REQUEST, 'page', 1);
			$pagecount = remositoryRepository::getParam($_REQUEST, 'pagecount', 0);
			if ($pagecount AND $pagecount != $this->getPageCount()) {
				$page = 1;
				$this->setPageCount($pagecount);
			}
			// Fourth parameter should be _ITEMS_PER_PAGE
			$pagecontrol = new remositoryPage ($total, $remUser, $this->getPageCount(), $page, "&func=$func&orderby=$orderby" );
			$sql = $this->getSelectedSQL(false, $orderby, $pagecontrol->startItem(), $pagecontrol->itemsperpage);
			$file_array = remositoryRepository::doSQLget($sql, 'remositoryFile');
		}
		else {
			$file_array = array();
			$pagecontrol = null;
		}
		return $file_array;
	}

	// private function - gets the search text clause for SQL
	private function searchCondition ($text) {
		if (get_magic_quotes_gpc()) $text = stripslashes($text);
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$parser = new booleanSearchParser;
		$parsed = $parser->parse($text);
		$parsed = $database->getEscaped($parsed);
		// Don't need text files for Nucleus Reseach
	    	// return "\n (MATCH (t.filetext) AGAINST ('$parsed' IN BOOLEAN MODE) OR MATCH (f.filetitle,f.description,f.smalldesc,f.fileauthor,f.custom_1) AGAINST ('$parsed' IN BOOLEAN MODE))";
	    	return "\n (MATCH (f.filetitle,f.description,f.smalldesc,f.fileauthor,f.custom_1) AGAINST ('$parsed' IN BOOLEAN MODE))";
	}

	// public function - return the SQL to do a search (for either count of results or actual files)
	public function getSelectedSQL ($count=true, $orderby=_REM_DEFAULT_ORDERING, $limitstart=0, $limit=0) {
		$sorter = array ('', ' ORDER BY id', ' ORDER BY filetitle', ' ORDER BY downloads DESC', ' ORDER BY custom_5 DESC', ' ORDER BY u.username');
		if (!isset($sorter[$orderby]) OR $orderby == 0) $orderby = _REM_DEFAULT_ORDERING;
		if ($count) $results = 'count(f.id)';
		// Suppress ratings - not needed by Nucleus Research
		// else $results = 'f.*, AVG(l.value) AS vote_value, COUNT(l.value) AS vote_count';
		else $results = 'f.*';
		if ($this->userstuff['submitter']) $results .= ', u.username';
		// $sql = "SELECT $results ".$this->detailedSQL(!$count, ($this->userstuff['submitter'] OR (5 == $orderby)), false);
		// Remove possibility of ratings - not needed for Nucleus Research
		$sql = "SELECT $results ".$this->detailedSQL(false, ($this->userstuff['submitter'] OR (5 == $orderby)), false);
		if (!$count) {
			$sql .= ' GROUP BY f.id';
			$sql .= $sorter[$orderby];
		}
		if ($limit) $sql .= " LIMIT $limitstart,$limit";
		return $sql;
	}

	// private function - get all classifications that are linked to current search set
	private function getUnselectedClassify () {
		$sql = "SELECT cl.* ".$this->detailedSQL(false, false, true);
		$sql .= ' GROUP BY cl.id ';
		$result = remositoryRepository::doSQLget($sql, 'remositoryClassification');
		$unselected = array();
		if ($result) foreach ($result as $item) {
			if (!$item->hidden) $unselected[$item->type][$item->id] = $item;
		}
		return $unselected;
	}

	// private function - work out tables and conditions for SQL statement
	private function detailedSQL ($addRatings, $addSubmitters, $joinClassify) {
		$interface = remositoryInterface::getInstance();
		$cmanager = remositoryContainerManager::getInstance();
		foreach ($this->userstuff['categories'] as $id) {
			$list = $cmanager->listGivenAndDescendants($id);
			if ($list) {
				$sql = "FROM #__downloads_files AS f";
				$partlist[] = "f.containerid IN($list)";
			}
		}
		if (isset($partlist)) $where[] = '('.implode(' OR ', $partlist).')';
		if (empty($sql)) {
			$sql = "FROM #__downloads_files AS f ";
			$where[] = "f.metatype = 0";
		}
		// Change for multiple repositories
		// $where[] = 'f.repnum = '.max(1,remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		$stack = $this->userstuff['clstack'];
		$sub = array_search('*', $stack);
		if (false !== $sub) unset($stack[$sub]);
		if ($classify = implode(',', $stack)) {
			$classcount = count(explode(',', $classify));
			$sql .= " INNER JOIN (SELECT file_id, COUNT(file_id) AS num FROM #__downloads_file_classify "
			." WHERE classify_id IN ($classify) GROUP BY file_id) AS fc ON f.id = fc.file_id ";
			$where[] = "fc.num >= $classcount";
		}
		if ($joinClassify) {
			$sql .= " INNER JOIN #__downloads_file_classify AS fcl ON fcl.file_id = f.id ";
			$sql .= " INNER JOIN #__downloads_classify AS cl ON cl.id = fcl.classify_id AND cl.published = 1";
		}
		if ($this->userstuff['submitter']) $where[] = "f.submittedby = $this->userstuff['submitter']";
		if ($this->userstuff['onlyfree']) $where[] = "f.custom_4 != 0";
		if ($addRatings) $sql .= ' LEFT JOIN #__downloads_log AS l ON l.type=3 AND l.fileid=f.id ';
		if ($addSubmitters) $sql .= ' LEFT JOIN #__users AS u ON u.id=f.submittedby';
		if ($this->userstuff['searchtext']) {
			// Don't need to match against text files for Nucleus Research
			// $sql .= ' LEFT JOIN #__downloads_text AS t ON t.fileid = f.id';
			$searchtext = $interface->getEscaped($this->userstuff['searchtext']);
			$where[] = $this->searchCondition($searchtext);
		}
		if (isset($where)) $sql .= ' WHERE '.implode(' AND ',$where);
		$user = $interface->getUser();
		$visibility = remositoryAbstract::visibilitySQL ($user);
		if ($visibility) $sql .= ' AND '.$visibility;
		return $sql;
	}

}
