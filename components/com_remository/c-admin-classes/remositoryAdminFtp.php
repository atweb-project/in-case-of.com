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

// Don't allow direct linking
if (!defined( '_VALID_MOS' ) AND !defined('_JEXEC')) die( 'Direct Access to this location is not allowed.' );

class remositoryAdminFtp extends remositoryAdminAddstructure {

	function __construct ($admin) {
		parent::__construct ($admin);
		$this->containerid = remositoryRepository::getParam($_REQUEST, 'containerid', 0);
	    $_REQUEST['act'] = 'ftp';
	}

	function listTask () {
	    $containerID = 0;
		$clist = $this->repository->getSelectList(false,$containerID,'cfid','class="inputbox"',$this->remUser,true);
		$view = $this->admin->newHTMLClassCheck ('listFtpHTML', $this, 0, $clist);
		if ($view AND $this->admin->checkCallable($view, 'view')) $view->view();
	}

	function uploadTask () {
		$upload_path = $this->repository->Up_Path.'/';
	    $extensionlist = remositoryRepository::getParam($_REQUEST, 'extensionlist', '');
	    if (trim($extensionlist) == '*') $extensions = '*';
	    else {
			$extensions = explode(',', $extensionlist);
	    	$extensions = array_map('trim', $extensions);
	    }
		$extensiontitle = remositoryRepository::getParam($_POST, 'extensiontitle', '');
	    $container = new remositoryContainer($this->admin->currid);
	    if ($upload_path AND $this->admin->currid) $this->addOneLevel ($upload_path, $container, $extensions, $extensiontitle,true);
		$this->backTask( _DOWN_STRUCTURE_ADDED );
	}
}