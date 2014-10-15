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

class remository_mydownload_Controller extends remositoryUserControllers {

	function mydownload ($func) {
		$interface = remositoryInterface::getInstance();
		if (!$this->remUser->id) {
			echo 'Please login to find out about the files that you have downloaded, we cannot provide information on public downloads';
			return;
		}
		$database = $interface->getDB();
		// Change for multiple repositories
		// ." WHERE l1.userid = {$this->remUser->id} AND l1.type = 1 AND l2.value IS NULL AND f.repnum = $this->repnum GROUP BY fileid ";
		$sql = "SELECT f.*, AVG(l3.value) AS vote_value, COUNT(l3.value) AS vote_count FROM `jos_downloads_log` AS l1"
		." INNER JOIN `#__downloads_files` AS f ON l1.fileid = f.id"
		." LEFT JOIN `jos_downloads_log` AS l2 ON l2.userid = l1.userid AND l2.fileid = l1.fileid AND l2.type = 3"
		." INNER JOIN `jos_downloads_log` AS l3 ON l3.fileid = l1.fileid AND l3.type = 3"
		." WHERE l1.userid = {$this->remUser->id} AND l1.type = 1 AND l2.value IS NULL GROUP BY fileid ";
		$unratedfiles = remositoryRepository::doSQLget($sql, 'remositoryFile');
		// Change for multiple repositories
		// ." WHERE l1.userid = {$this->remUser->id} AND l1.type = 1 AND f.repnum = $this->repnum GROUP BY fileid ";
		$sql = "SELECT f.filetitle, f.icon, l1.fileid, l2.value AS mine, AVG( l3.value ) AS general FROM `jos_downloads_log` AS l1"
		." INNER JOIN `#__downloads_files` AS f ON l1.fileid = f.id"
		." INNER JOIN `jos_downloads_log` AS l2 ON l2.userid = l1.userid AND l2.fileid = l1.fileid AND l2.type = 3"
		." INNER JOIN `jos_downloads_log` AS l3 ON l3.fileid = l1.fileid AND l3.type = 3"
		." WHERE l1.userid = {$this->remUser->id} AND l1.type = 1 GROUP BY fileid ";
		$ratedfiles = remositoryRepository::doSQLget($sql, 'remositoryFile');
		$view = new remositorMyDownloadsHTML($this);
		$view->myDownloadHTML($unratedfiles, $ratedfiles);
	}

}