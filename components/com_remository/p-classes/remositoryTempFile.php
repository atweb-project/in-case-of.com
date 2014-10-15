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

class remositoryTempFile extends remositoryFile {

	// Parameters are only declared for compatibility
	function getValues ($user=null, $onlypublished=true) {
		$sql = "SELECT * FROM #__downloads_files WHERE id = $this->id";
		$this->readDataBase($sql);
		$this->containerid = abs($this->containerid);
	}

	function obtainPhysical () {
		$physical = new remositoryPhysicalFile();
		$repository = remositoryRepository::getInstance();
		$physical->setData($repository->Up_Path.'/'.$this->realname, $this->id, $this->isblob, $this->plaintext, true);
		return $physical;
	}

	function filePath () {
		if (1 == $this->metatype) {
			$repository = remositoryRepository::getInstance();
			return $repository->Up_Path.'/'.$this->nameWithID();
		}
		else return parent::filePath();
	}

	function saveFile () {
		$this->containerid = -$this->containerid;
		//$sql = $this->insertSQL();
		//remositoryRepository::doSQL($sql);
		if ($this->id == 0) $this->insertFileDB();
	}

}