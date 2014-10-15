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

class remositoryCustomizer {
	private $fileListFields = array();
	private $fieldsByName = array();
	private $values = array();
	private $repository = null;
	
	public function __construct () {
		$this->fileListFields = array(
		array ('smalldesc', _DOWN_DESC_SMALL, 1, 1),
		array ('submittedby', _DOWN_SUB_BY, 1, 0),
		array ('submitdate', _DOWN_SUBMIT_DATE, 1, 0),
		array ('filesize', _DOWN_FILE_SIZE, 1, 0),
		array ('downloads', _DOWN_DOWNLOADS, 1, 0),
		array ('vote_value', _DOWN_RATING, 1, 0),
		array ('license', _DOWN_LICENSE, 0, 1),
		array ('fileversion', _DOWN_FILE_VER, 0, 1),
		array ('fileauthor', _DOWN_FILE_AUTHOR, 0, 1),
		array ('filehomepage', _DOWN_FILE_HOMEPAGE, 0, 1),
		array ('filedate', _DOWN_FILE_DATE, 0, 0),
		array ('description', _DOWN_DESCRIPTION, 0, 1),
		array ('icon', _DOWN_ICON, 0, 1),
		array ('price', _DOWN_PRICE_LABEL, 0, 0)
		);
		foreach ($this->fileListFields as $key=>$info) $this->fieldsByName[$info[0]] = $key;
		$this->repository = remositoryRepository::getInstance();
		$this->checkCustomizer();
	}
	
	public function getFileListFields () {
		return $this->fileListFields;
	}

	public function getFieldByName ($name) {
		return isset($this->fieldsByName[$name]) ? $this->fieldsByName[$name] : false;
	}
	
	public function getCustomSpec () {
		return $this->values;
	}

	public function getUserCustomSpec () {
		$special = remositoryInterface::getInstance()->triggerMambots('onRemositoryCreateCustomizer', array($this));
		return empty($special) ? $this->values : $special[0];
	}

	public function saveCustomSpec ($values) {
		$this->values = $values;
		$this->repository->customizer = serialize($this->values);
		$this->repository->saveValues();
	}

	private function checkCustomizer () {
		if ($this->repository->customizer) $this->values = unserialize($this->repository->customizer);
		else $this->values = array();
		$fields = $this->getFileListFields();
		if (!isset($this->values['S']) OR in_array(0, $this->values['S'])) {
			foreach ($fields as $key=>$farr) $this->values['S'][$key] = 10 + 10*$key;
			$changed = true;
		}
		if (!isset($this->values['A'])) {
			foreach ($fields as $key=>$farr) $this->values['A'][$key] = $farr[2];
			$changed = true;
		}
		if (!isset($this->values['B'])) {
			foreach ($fields as $key=>$farr) $this->values['B'][$key] = $farr[2];
			$changed = true;
		}
		if (!isset($this->values['C'])) {
			foreach ($fields as $key=>$farr) $this->values['C'][$key] = $farr[2];
			$changed = true;
		}
		if (!isset($this->values['D'])) {
			foreach ($fields as $key=>$farr) $this->values['D'][$key] = 1;
			$changed = true;
		}
		if (!isset($this->values['E'])) {
			foreach ($fields as $key=>$farr) $this->values['E'][$key] = $farr[3];
			$changed = true;
		}
		if (!empty($changed)) $this->saveCustomSpec($this->values);
	}
    
}