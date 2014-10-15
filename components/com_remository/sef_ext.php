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

/** ensure this file is being included by a parent file */
if (!defined( '_VALID_MOS' ) AND !defined('_JEXEC')) die( sprintf ('Direct Access to %s is not allowed.', __FILE__ ));

if (!defined('_REMOS_SHOW_EXTENSION')) define('_REMOS_SHOW_EXTENSION', 0);

$remository_dir = str_replace('\\','/',dirname(__FILE__));

require_once($remository_dir.'/sef.custom.php');
require_once($remository_dir.'/remository.interface.php');

class sef_remository_data {
	private static $instance = null;
	private $container_names = array();
	private $c_names_by_parent = array();
	private $container_parents = array();
	private $file_names = array();
	private $old_file_names = array();
	private $file_containers = array();
	private $tag_names = array();
	private $tag_types = array();
	private $sef = null;
	private $database = null;

	private function __construct () {
		$interface = remositoryInterface::getInstance();
		$this->database = $interface->getDB();
		if (defined ('_ALIRO_IS_PRESENT') OR class_exists('aliroSEF')) $this->sef = aliroSEF::getInstance();
		$results = remositoryContainerManager::getInstance()->getAll();
		if ($results) foreach ($results as $result) {
			$this->container_names[$result->id] = $this->nameForURL($result->name, $result->alias);
			$this->c_names_by_parent[$result->parentid][$result->id] = $this->container_names[$result->id];
			$this->container_parents[$result->id] = $result->parentid;
		}
		/*
		$this->database->setQuery("SELECT id, type, name FROM #__downloads_classify");
		$results = $this->database->loadObjectList();
		if ($results) foreach ($results as $result) {
			$this->tag_names[$result->id] = $this->nameForURL($result->name);
			$this->tag_types[$result->id] = $this->nameForURL($result->type);
		}
		*/
	}

	public static function getInstance () {
		return is_object(self::$instance) ? self::$instance : self::$instance = new self();
	}

	public function getContainerName ($id, $lowercase) {
		$name = isset($this->container_names[$id]) ? $this->container_names[$id] : '';
		return $lowercase ? strtolower($name) : $name;
	}

	public function getContainerParent ($id) {
		return isset($this->container_parents[$id]) ? $this->container_parents[$id] : 0;
	}

	function getFileName ($id, $lowercase=false) {
		$id = intval($id);
		if (!isset($this->file_names[$id])) $this->setFileInfo("SELECT f.id, f.containerid, f.filetitle, f.realname FROM #__downloads_files AS f INNER JOIN #__downloads_files AS f2 ON f.containerid = f2.containerid WHERE f2.id = $id");
		$name = isset($this->file_names[$id]) ? $this->file_names[$id] : '';
		return $lowercase ? strtolower($name) : $name;
	}
	
	function setFileInfo ($sql) {
		$this->database->setQuery($sql);
		$results = $this->database->loadObjectList();
		if ($results) foreach ($results as $result) {
			$this->file_names[$result->id] = _REMOS_SHOW_EXTENSION ? $this->nameForURL($result->realname) : $this->nameForURL($result->filetitle);
			$this->old_file_names[$result->id] = $this->nameForURL($result->filetitle);
			$this->file_containers[$result->id] = $result->containerid;
		}
	}

	public function getFileContainer ($id) {
		// Make sure this ID has been loaded
		$this->getFileName($id);
		return isset($this->file_containers[$id]) ? $this->file_containers[$id] : '';
	}

	public function getTagType ($id) {
		return isset($this->tag_types[$id]) ? $this->tag_types[$id] : '';
	}

	public function getTagName ($id) {
		return isset($this->tag_names[$id]) ? $this->tag_names[$id] : '';
	}

	public function nameForURL ($name, $alias='') {
		if ($alias) $name = $alias;
		if ($this->sef) $name = $this->sef->nameForURL($name);
		elseif (function_exists('sefencode')) $name = sefencode($name);
		else {
			global $_SEF_SPACE;
			$before = array ('&', '?', '!', ':', '$', '"', "'", ',', '/');
			$after = array(' and ', '', '', '', '', '', '', '', ' ');
			$name = str_replace($before, $after, $name);
			$space_replace = $_SEF_SPACE ? $_SEF_SPACE : '-';
			$name = str_replace(' ', $space_replace, $name);
		}
		return $name;
	}

	public function findContainer ($name, $parentid, $numeric, &$string) {
		if (isset($this->c_names_by_parent[$parentid])) {
			foreach ($this->c_names_by_parent[$parentid] as $key=>$cname) if (0 == strcasecmp($cname, $name)) return $key;
		}
		// Make sure this parent ID has been loaded
		if ($numeric) {
			$id = intval(end(explode('-', $name)));
			if ($id) {
				$string = str_replace('select','fileinfo',$string);
				$_GET['func'] = $_REQUEST['func'] = 'fileinfo';
				return $id;
			}
		}
		$this->setFileInfo("SELECT f.id, f.containerid, f.filetitle, f.realname FROM #__downloads_files AS f WHERE f.containerid = $parentid"); 
		$id = $this->findFile($name, $parentid, $this->file_names, $string);
		if ($id) return $id;
		$id = $this->findFile($name, $parentid, $this->old_file_names, $string);
		if ($id) return $id;
		return $parentid;
	}
	
	private function findFile ($name, $parentid, $filenames, &$string) {
		$encname = urlencode($name);
		foreach ($filenames as $id=>$filename) {
			if ($parentid != $this->file_containers[$id]) continue;
			if (0 == strcasecmp($name, $filename) OR 0 == strcasecmp($name.'/', $filename) OR 0 == strcasecmp($encname, $filename) OR 0 == strcasecmp($encname.'/', $filename)) {
				$string = str_replace('select','fileinfo',$string);
				$_GET['func'] = $_REQUEST['func'] = 'fileinfo';
				return $id;
			}
		}
		return false;
	}

}

class sef_remository {

	function tags () {
		return array (
			'func-startdown',
			'func-showdown',
			'func-finishdown',
			'func-filelist',
			'func-classify',
			'func-addfile',
			'func-addmanyfiles',
			'func-download',
			'func-rss',
			'func-search'
		);
	}

	/**
	* Creates the SEF advance URL out of the Mambo request
	* Input: $string, string, The request URL (index.php?option=com_example&Itemid=$Itemid)
	* Output: $sefstring, string, SEF advance URL ($var1/$var2/)
	**/
	public static function create ($string, $lowercase=_REMOSITORY_SEF_LOWER_CASE, $numeric=_REMOSITORY_SEF_UNIQUE_ID, $maptags=array()) {
		// $string == "index.php?option=com_example&Itemid=$Itemid&var1=$var1&var2=$var2"
		$sefstring = "";
		$isContainer = $isTag = $isSearch = false;
		$string = strtolower(str_replace( '&amp;', '&', $string ));
		if (strpos($string, 'index.php?') === 0) $string = substr($string,10);
		parse_str($string,$params);
		if (isset($params['option'])) unset($params['option']);
		if (isset($params['itemid'])) unset($params['itemid']);
		if (isset($params['func'])) {
			if (substr($params['func'], 0, 5) == 'func,') $params['func'] = substr($params['func'], 5);
			if ($params['func'] == 'select') {
				$isContainer = true;
				$isFile = false;
				// $sefstring .= _REMOSITORY_SELECT_FROM_CONTAINER.'/';
			}
			elseif ($params['func'] == 'fileinfo') {
				$isContainer = true;
				$isFile = true;
				// $sefstring .= _REMOSITORY_SELECT_FROM_CONTAINER.'/';
			}
			elseif ('classify' == $params['func'] AND isset($params['id'])) {
				$isTag = true;
				$sefstring .= 'classify/';
			}
			elseif ('search' == $params['func']) {
				$isSearch = true;
				$sefstring .= 'search/';
			}
			else {
				$funcparm = 'func-'.$params['func'];
				if (isset($maptags[$funcparm])) $funcparm = $maptags[$funcparm];
				$sefstring .= $funcparm."/";
			}
			unset($params['func']);
		}
		if (isset($params['os'])) {
			$sefstring .= $params['os']."/";
			unset($params['os']);
		}
		if (isset($params['id'])) {
			if ($isTag OR $isSearch) {
				$id = intval($params['id']);
				$absid = abs($id);
				$info = sef_remository_data::getInstance();
				// $database->setQuery("SELECT type, name FROM #__downloads_classify WHERE id=$absid");
				// Do not use the following!! Avoid loadObject because of Joomla 1.6 incompatibility
				// $name = $database->loadObject($classify);
				$name = $info->getTagName($absid);
				$type = $info->getTagType($absid);
			}
			if ($isContainer) {
				if ($isFile) {
					$info = sef_remository_data::getInstance();
					$filename = $info->getFileName($params['id'], $lowercase);
					$containerID = $info->getFileContainer($params['id']);
				}
				else $containerID = $params['id'];
				$sefstring .= sef_remository::containerName($containerID, $lowercase);
				$sefstring .= '/';
				if ($isFile) {
					$sefstring .= $filename;
					if ($numeric) $sefstring .= '-'.$params['id'];
					$sefstring .= '/';
				}
			}
			elseif ($isTag) {
				$sefstring .= ($type ? ($lowercase ? strtolower($type) : $type).'/' : '');
				$sefstring .= ($name ? ($lowercase ? strtolower($name) : $name) : $id).'/';
			}
			elseif ($isSearch) {
				if ($id > 0) $extratext = "?add-$type=$name";
				else $extratext = "?delete-$type=$name";
				$sefstring .= $lowercase ? strtolower($extratext) : $extratext;
			}
			else $sefstring .= $params['id']."/";
			unset($params['id']);
		}
		foreach ($params as $key=>$param) $sefstring .= "$key,$param/";
		return $sefstring;
	}

	protected static function containerName ($id, $lowercase) {
		$info = sef_remository_data::getInstance();
		$name = $info->getContainerName($id, $lowercase);
		$parent = $info->getContainerParent($id);
		if ($parent) return sef_remository::containerName($parent, $lowercase).'/'.$name;
		else return $name;
	}

	/**
	* Reverts to the Mambo query string out of the SEF advance URL
	* Input:
	*    $url_array, array, The SEF advance URL split in arrays (first custom virtual directory beginning at $pos+1)
	*    $pos, int, The position of the first virtual directory (component)
	* Output: $QUERY_STRING, string, Mambo query string (var1=$var1&var2=$var2)
	*    Note that this will be added to already defined first part (option=com_example&Itemid=$Itemid)
	**/
	public static function revert ($url_array, $pos, $numeric=_REMOSITORY_SEF_UNIQUE_ID, $maptags=array()) {
		// define all variables you pass as globals - not required for Remository - uses super globals
 		// Examine the SEF advance URL and extract the variables building the query string
		// (class_exists('aliroSEF')) return false;
		$QUERY_STRING = "";
		$parentid = 0;
		$not404 = true;
		$opsystems = array('All', 'Linux', 'Windows', 'Mac', 'Palm', 'Other');
		if (!empty($url_array[$pos+2])) {
			// component/example/$var1/
			$legalfunc = 'addfile/addmanyfiles/download/rss/savefile/savemanyfiles/savethumb/search/classify/startdown/thumbupdate/tree/userupdate';

			$func = $url_array[$pos+2];
			$key = array_search($func, $maptags);
			if ($key) $func = $key;
			$func5 = substr($func,0,5);
			if ('func,' == $func5 OR 'func-' == $func5) {
				$func = substr($func,5);
			}
			else {
				$func = (false !== stripos($legalfunc, $func)) ? $func : 'select';
				$pos--;
			}
			//if ($func == _REMOSITORY_SELECT_FROM_CONTAINER) $func = 'select';
			$_REQUEST['func'] = $_GET['func'] = $func;
			$QUERY_STRING .= "&func=$func";
		}
		for ($i=$pos+3; $i<count($url_array); $i++) {
			$parm = $url_array[$i];
			$posparm = explode(',',$parm);
			if (count($posparm) > 1) {
				$subs = $posparm[0];
				$_GET[$subs] = $_REQUEST[$subs] = $posparm[1];
				$QUERY_STRING .= "&$subs=$posparm[1]";
			}
			elseif (is_numeric($parm) OR '*' == $parm) {
				$_REQUEST['id'] = $_GET['id'] = $id = $parm;
				$QUERY_STRING .= "&id=$id";
			}
			elseif (in_array($parm,$opsystems)) {
				$_REQUEST['os'] = $_GET['os'] = $os = $parm;
				$QUERY_STRING .= "&os=$os";
			}
			elseif ($parm) {
				$info = sef_remository_data::getInstance();
				$newparentid = $info->findContainer($parm,$parentid,$numeric,$QUERY_STRING);
				if ($newparentid == $parentid) {
					if (defined('_ALIRO_IS_PRESENT')) {
						$request = aliroRequest::getInstance();
						$request->set404();
					}
				}
				else $parentid = $newparentid;
				if ('select' == $func AND 0 == $parentid) return '';
			}
		}
		if ($parentid) {
			$_REQUEST['id'] = $_GET['id'] = $parentid;
			$QUERY_STRING .= "&id=$parentid";
		}
		if (isset($func) AND $func == 'rss' AND !isset($_REQUEST['no_html'])) {
				$_REQUEST['no_html'] = $_GET['no_html'] = 1;
				$QUERY_STRING .= "&no_html=1";
		}

		return $QUERY_STRING;
	}

}
