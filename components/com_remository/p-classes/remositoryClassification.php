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

class remositoryClassification extends remositoryAbstract  {
	public $name = '';
	public $type = '';
	public $published = 0;
	public $hidden = 0;
	public $frequency = 0;
	public $description = '';

	public function __construct ($id=0) {
		if ($id) {
			$this->id = $id;
			$this->getValues();
		}
	}

	protected function tableName () {
		return '#__downloads_classify';
	}

	public function getValues () {
		$sql = "SELECT * FROM #__downloads_classify WHERE id = $this->id";
		$this->readDataBase($sql);
	}

	public function saveValues () {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		if ($this->id == 0) {
			$sql = $this->insertSQL();
			remositoryRepository::doSQL ($sql);
			$this->id = $database->insertid();
		}
		else {
			$sql = $this->updateSQL();
			remositoryRepository::doSQL ($sql);
		}
	}

	public static function classfnList ($fileid) {
		$fileid = intval($fileid);
		$html = array();
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$sql = "SELECT c.id, c.name, c.type, fc.file_id FROM #__downloads_classify AS c "
		." LEFT JOIN #__downloads_file_classify AS fc ON c.id = fc.classify_id AND fc.file_id = $fileid "
		." WHERE c.published = 1 ORDER BY c.type, c.name";
		$database->setQuery($sql);
		$objectlist = $database->loadObjectList();
		if ($objectlist) foreach ($objectlist as $object) $linker[$object->type][] = $object;
		$nulloption = new stdClass();
		$nulloption->id = 0;
		$nulloption->name = _DOWN_ROLE_NONE_THESE;
		$nulloption->selected = false;
		if (isset($linker)) foreach ($linker as $type=>$sublist) {
			foreach ($sublist as $object) if (!is_null($object->file_id) AND $object->file_id == $fileid) $selected[] = $object->id;
			if (!isset($selected)) $selected = array(0);
			$sublist[] = $nulloption;
			$html[$type] = remositoryRepository::getInstance()->selectList($sublist, "classification[]", 'multiple="multiple"', $selected, 'id', 'name');
			unset($selected);
		}
		return $html;
	}

}