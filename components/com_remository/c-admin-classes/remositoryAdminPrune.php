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

class remositoryAdminPrune extends remositoryAdminControllers {

	function __construct ($admin) {
		parent::__construct ($admin);
	    $_REQUEST['act'] = 'prune';
	}

	function listTask (){
	    $d90 = 90*24*3600;
	    $default = time() - $d90;
	    $default_date = date('Y-m-d', $default);
		$view = $this->admin->newHTMLClassCheck ('listPruneHTML', $this, 0, '');
		if ($view AND $this->admin->checkCallable($view, 'view')) $view->view($default_date);
	}

	function saveTask () {
	    $startdate = remositoryRepository::GetParam ($_REQUEST, 'startdate', '');
	    $ymd = explode('-', $startdate);
	    $d90 = 90*24*3600;
	    $default = time() - $d90;
	    if (count($ymd) == 3) $time = mktime(0,1,0,$ymd[1],$ymd[2],$ymd[0]);
		else $time = $default;
		$startdate = date('Y-m-d H:i:s', $time);
		$sql = "DELETE FROM #__downloads_log WHERE date<'$startdate'";
		remositoryRepository::doSQL($sql);
		$this->backTask( sprintf(_DOWN_OLD_LOG_REMOVED, $startdate) );
	}

}
