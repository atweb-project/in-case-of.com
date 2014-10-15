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

class remositoryAdminAddstructure extends remositoryAdminControllers {

	public function listTask () {
	    $containerID = 0;
		$clist = $this->repository->getSelectList(false,$containerID,'cfid','class="inputbox"',$this->remUser);
		$view = $this->admin->newHTMLClassCheck ('listAddstructureHTML', $this, 0, $clist);
		if ($view AND $this->admin->checkCallable($view, 'view')) $view->view();
	}

	private function setFileCommonData () {
		$filedata['license'] = remositoryRepository::getParam($_POST, 'license', '');
		$filedata['licenseagree'] = remositoryRepository::getParam($_POST, 'licenseagree', '');
		$filedata['fileversion'] = remositoryRepository::getParam($_POST, 'fileversion', $this->repository->Default_Version);
		$filedata['fileauthor'] = remositoryRepository::getParam($_POST, 'fileauthor', '');
		$filedata['filehomepage'] = remositoryRepository::getParam($_POST, 'filehomepage', '');
		$filedata['icon'] = remositoryRepository::getParam($_POST, 'icon', '');
		return $filedata;
	}

	public function saveTask () {
	    $basedir = str_replace("'", '', remositoryRepository::getParam ($_REQUEST, 'basedir', ''));
	    $interface = remositoryInterface::getInstance();
	    $basedir = $interface->getEscaped($basedir);
	    $recurse = remositoryRepository::getParam($_REQUEST, 'recurse', 0);
	    $extensionlist = remositoryRepository::getParam($_REQUEST, 'extensionlist', '');
		$extensiontitle = remositoryRepository::getParam($_POST, 'extensiontitle', '');
		$filedata = $this->setFileCommonData();
		$defaults = array(
		'upload' => 'Registered',
		'edit' => 'Nobody'
		);
		$manager = new remositoryAdditionsManager();
		$message = '';
		$badfiles = array();
		$submitter = $this->remUser->id;
		$result = $manager->saveNewFiles ($basedir, $this->admin->currid, $recurse, $extensionlist, $extensiontitle, $this->remUser->id, $filedata, $defaults, $message, $badfiles);
		if (!$result) {
			if (count($badfiles)) {
				$view = $this->admin->newHTMLClassCheck ('listAddstructureHTML', $this, 0, '');
				if ($view AND $this->admin->checkCallable($view, 'badfiles')) $view->badfiles($badfiles);
				else die('Bad file extensions');
				return true;
			}
			$this->interface->redirect( "index2.php?option=com_remository&act=addstructure", $message);
		}
		$_SESSION['remositoryResetCounts'] = 1;
		$this->backTask( _DOWN_STRUCTURE_ADDED );
	}
	
	// Private function for tidiness
	function savePermissions ($container) {
		$defaults = array(
		'upload' => 'Registered',
		'edit' => 'Nobody'
		);
		$authoriser = aliroAuthorisationAdmin::getInstance();
		foreach ($defaults as $action=>$role) {
			$authoriser->permit ($role, 2, $action, 'remosFolder', $container->id);
		}
	}

}