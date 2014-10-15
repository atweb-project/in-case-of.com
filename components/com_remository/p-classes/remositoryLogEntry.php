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

class remositoryLogEntry {
	public $id=0;
	public $type=0;
	public $date='';
	public $userid=0;
	public $fileid=0;
	public $value=0;
	public $price=0;
	public $ipaddress='';

	public function __construct ($type, $userid, $fileid, $value, $pricepaid=0) {
		$this->type = $type;
		$this->userid = $userid;
		$this->fileid = $fileid;
		$this->value = $value;
		$this->price = $pricepaid;
		$this->date = date('Y-m-d H:i:s');
		$this->ipaddress = remositoryInterface::getInstance()->getIP();
	}

	public function insertEntry () {
		$sql = "INSERT INTO #__downloads_log (type, date, userid, fileid, value, price, ipaddress) VALUES ('$this->type', '$this->date', '$this->userid', '$this->fileid', '$this->value', '$this->price', '$this->ipaddress')";
		remositoryRepository::doSQL($sql);
	}
	
	public static function deleteEntries ($fileid) {
		$sql = "DELETE FROM #__downloads_log WHERE fileid=$fileid";
		remositoryRepository::doSQL($sql);
	}
}