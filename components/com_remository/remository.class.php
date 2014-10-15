<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006-12 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

// Don't allow direct linking
if (!defined( '_VALID_MOS' ) AND !defined('_JEXEC')) die( 'Direct Access to this location is not allowed.' );


abstract class remositoryHTML {
	protected $interface = null;

	public function __construct () {
		$this->interface = remositoryInterface::getInstance();
	}

	protected function show ($string) {
		return PHP_VERSION_ID < 50203 ? htmlspecialchars($string, ENT_QUOTES, _CMSAPI_CHARSET) : htmlspecialchars($string, ENT_QUOTES, _CMSAPI_CHARSET, false);
	}
	
	protected function showHTML ($string) {
		$ampencode = '/(&(?!(#[0-9]{1,5};))(?!([0-9a-zA-Z]{1,10};)))/';
		return preg_replace($ampencode, '&amp;', $string);
	}
	
}

/**
* Abstract class for Remository classes that involve straightforward database tables
* Requires child classes to implement: tableName(), notSQL().
* tableName() must return the name of the database table, using #__ in the usual Mambo way
* notSQL() must return an array of strings, where each string is the name of a
* 	variable that is NOT in the database table, or is not written explicitly,
*   e.g. the auto-increment key.  If this is the ONLY non-SQL field, then the
*   child class need not implement it, as that it is already in the abstract class.
* Child classes may optionally implement: forcebools().
*/

abstract class remositoryAbstract {
	/** @var int ID for file record in database */
	public $id=0;
	/** @var int Sequencing number for records */
	public $sequence=0;
	/** @var string Window Title */
	public $windowtitle='';
	/** @var string Keywords */
	public $keywords='';

	function addPostData () {
		$interface = remositoryInterface::getInstance();
		foreach (get_class_vars(get_class($this)) as $field=>$value) {
			if ($field!='id' AND isset($_POST[$field]) AND !is_array($_POST[$field])) {
				$this->$field = trim($_POST[$field]);
				if ($this->$field AND !is_numeric($this->$field)) $this->$field = $interface->purify($this->$field);
			}
		}
		$this->forceBools();
  	}

	function forceBools () {
	}

	function updateObjectDB () {
		remositoryRepository::doSQL($this->updateSQL());
	}

	function timeStampField () {
		return '';
	}

	function prepareValues () {
		$interface = remositoryInterface::getInstance();
		foreach (get_class_vars(get_class($this)) as $field=>$value) {
			if (!is_numeric($this->$field)) $this->$field = $interface->getEscaped($this->$field);
		}
	}

	function updateSQL () {
		$interface = remositoryInterface::getInstance();
		$tabname = $this->tableName();
		$sql = "UPDATE $tabname SET ";
		$exclude = $this->notSQL();
		foreach (get_class_vars(get_class($this)) as $field=>$value) {
			if (!in_array($field,$exclude)) {
				$data = is_numeric($this->$field) ? $this->$field : $interface->getEscaped($this->$field);
				$item[] = $field."='".$data."'";
			}
		}
		if ($this->timeStampField()) $item[] = $this->timeStampField()."='".date('Y-m-d H:i:s')."'";
		if (isset($item)) {
			$sql .= implode (', ', $item);
		}
		return $sql.' WHERE id='.$this->id;
	}

	function notSQL () {
		return array ('id');
	}

	function insertSQL () {
		$interface = remositoryInterface::getInstance();
		$tabname = $this->tableName();
		$exclude = $this->notSQL();
		foreach (get_class_vars(get_class($this)) as $field=>$value) {
			if (!in_array($field,$exclude)) {
				$column[] = $field;
				$data = is_numeric($this->$field) ? $this->$field : $interface->getEscaped($this->$field);
				$item[] = "'".$data."'";
			}
		}
		$timestamp = $this->timeStampField();
		if ($timestamp) {
			$column[] = $timestamp;
			$item[] = "'".date('Y-m-d H:i:s')."'";
		}
		$columns = implode(',', $column);
		$datafields = implode(',', $item);
		return "INSERT INTO $tabname ($columns) VALUES($datafields)";
	}

	function setValues ($anObject) {
		foreach (get_class_vars(get_class($this)) as $field=>$value) {
			if ($field != 'id' AND isset($anObject->$field)) $this->$field = $anObject->$field;
		}
	}

	function readDataBase($sql) {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$database->setQuery( $sql );
		$results = $database->loadObjectList();
		$result = empty($results) ? null : $results[0];
		if (is_object($result)) $this->setValues($result);
		else $this->id = 0;
		return $result ? true : false;
	}

	function lastPart ($field, $separator, $lowercase=true) {
		$parts = explode($separator, $field);
		$last = end($parts);
		return $lowercase ? strtolower($last) : $last;
	}

	function allButLast ($field, $separator) {
		$last = remositoryAbstract::lastPart($field,$separator);
		return substr($field,0,strlen($field)-strlen($last)-1);
	}
	
	public static function visibilitySQL ($user) {
		$cvisibility = remositoryAbstract::containerVisibilitySQL($user);
		if (!$cvisibility) return '';
		$fvisibility = remositoryAbstract::fileVisibilitySQL($user);
		return $fvisibility ? "($cvisibility OR $fvisibility)" : $cvisibility;
	}

	private static function containerVisibilitySQL ($user) {
		$repository = remositoryRepository::getInstance();
		if (!$user->isAdmin() AND !$repository->See_Files_no_download) {
			$refuseSQL = aliroAuthoriser::getInstance()->getRefusedListSQL ('aUser', $user->id, 'remosFolder', 'download,edit', 'f.containerid');
		}
		return empty($refuseSQL) ? '' : $refuseSQL;
	}

	private static function fileVisibilitySQL ($user) {
		$repository = remositoryRepository::getInstance();
		if (!$user->isAdmin() AND !$repository->See_Files_no_download) {
			$refuseSQL = aliroAuthoriser::getInstance()->getRefusedListSQL ('aUser', $user->id, 'remosFile', 'download,edit', 'f.id');
		}
		return empty($refuseSQL) ? '' : $refuseSQL;
	}

}
