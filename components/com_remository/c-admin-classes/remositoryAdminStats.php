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

class remositoryAdminStats extends remositoryAdminControllers {

	function __construct ($admin) {
		parent::__construct ($admin);
	    $_REQUEST['act'] = 'stats';
	}

	function listTask (){
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();

		// General information
		$sql="SELECT COUNT(*) from #__downloads_files";
		$database->setQuery( $sql );
		$filecount = $database->loadResult();
		
		$sql="SELECT COUNT(*) from #__downloads_containers";
		$database->setQuery( $sql );
		$foldercount = $database->loadResult();
		
		// Top 5 Downloads
		// Change for multiple repositories
		// $sql="SELECT downloads, filetitle from #__downloads_files WHERE repnum = $this->repnum ORDER BY downloads DESC LIMIT 5";
		$sql="SELECT downloads, filetitle from #__downloads_files ORDER BY downloads DESC LIMIT 5";
		$database->setQuery( $sql );
		$downloads = $database->loadObjectList();

		// Top 5 Rated
		$logtype = _REM_VOTE_USER_GENERAL;
		// Change for multiple repositories
		// $sql="SELECT CONCAT(filetitle,',',ROUND(AVG(value),1)), AVG(value) as average FROM #__downloads_log as l INNER JOIN #__downloads_files as f ON f.id = l.fileid WHERE l.type=$logtype AND f.repnum = $this->repnum GROUP BY f.id ORDER BY average DESC LIMIT 5";
		$sql="SELECT CONCAT(filetitle,',',ROUND(AVG(value),1)), AVG(value) as average FROM #__downloads_log as l INNER JOIN #__downloads_files as f ON f.id = l.fileid WHERE l.type=$logtype GROUP BY f.id ORDER BY average DESC LIMIT 5";
		$database->setQuery( $sql );
		$ratings = $database->loadResultArray();

		// Top 5 Voted
		// Change for multiple repositories
		// $sql="SELECT CONCAT(filetitle,',',COUNT(l.id)), COUNT(l.id) as counter FROM #__downloads_log as l INNER JOIN #__downloads_files as f ON f.id = l.fileid WHERE l.type=$logtype AND repnum = $this->repnum GROUP BY f.id ORDER BY counter DESC LIMIT 5";
		$sql="SELECT CONCAT(filetitle,',',COUNT(l.id)), COUNT(l.id) as counter FROM #__downloads_log as l INNER JOIN #__downloads_files as f ON f.id = l.fileid WHERE l.type=$logtype GROUP BY f.id ORDER BY counter DESC LIMIT 5";
		$database->setQuery( $sql );
		$votes = $database->loadResultArray();

		$view = $this->admin->newHTMLClassCheck ('listStatsHTML', $this, 0, '');
		if ($view AND $this->admin->checkCallable($view, 'view')) $view->view($downloads, $ratings, $votes, $filecount, $foldercount);
	}

}