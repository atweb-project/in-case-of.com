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

class remositoryAdminCpanel extends remositoryAdminControllers {

	function __construct ($admin) {
		parent::__construct ($admin);
	    $_REQUEST['act'] = 'cpanel';
	}

	function listTask () {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();

		// Obtain all repositories
		$sql="SELECT id, name, alias FROM #__downloads_repository ORDER BY id";
		$database->setQuery( $sql );
		$repositories = $database->loadObjectList();

		$view = $this->admin->newHTMLClassCheck ('listCpanelHTML', $this, 0, '');
		if ($view AND $this->admin->checkCallable($view, 'view')) $view->view($repositories, $this->repnum);
	}
	
	function cpanelTask () {
		$this->listTask();
	}
	
	function newrepTask () {
		$this->repository->id = 0;
		$this->repository->name = 'New Repository';
		$this->repository->alias = '';
		$this->repository->saveValues(true);
		$this->backTask();
	}

}