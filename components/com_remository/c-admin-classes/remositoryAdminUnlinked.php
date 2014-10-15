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

class remositoryAdminUnlinked extends remositoryAdminControllers {

	function __construct ($admin) {
		parent::__construct ($admin);
	    $_REQUEST['act'] = 'unlinked';
	}

	function getOrphanDownloads () {
		$manager = remositoryContainerManager::getInstance();
		$paths = $manager->getFilePathData();
		$OrphanDownloads = array();
		foreach ($paths as $dir_path=>$containers) {
			$directory = new remositoryDirectory($dir_path);
			$neworphans = $directory->getOrphans();
			if (count($neworphans)) $OrphanDownloads = array_merge($OrphanDownloads, $neworphans);
		}
		return $OrphanDownloads;
	}

	function getOrphanUploads () {
		$upload_path = $this->repository->Up_Path.'/';
		$upload_dir = new remositoryDirectory($upload_path);
		$uporphans = $upload_dir->getOrphans(true);
		return $uporphans;
	}

	function listTask () {
		$OrphanDownloads = $this->getOrphanDownloads();
		sort($OrphanDownloads);
		$OrphanUploads = $this->getOrphanUploads();
		sort($OrphanUploads);
		$interface = remositoryInterface::getInstance();
		$link = $interface->getCfg('admin_site')."/{$interface->indexFileName()}?option=com_remository&amp;repnum=$this->repnum&amp;act=files&amp;task=addfile&amp;orphanpath=";
		$view = $this->admin->newHTMLClassCheck ('listUnlinkedHTML', $this, 0, '');
		if ($view AND $this->admin->checkCallable($view, 'view')) $view->view($OrphanDownloads, $OrphanUploads, $link);
	}

	function deleteTask () {
		$this->admin->check_selection(_DOWN_SEL_FILE_DEL);
		$OrphanDownloads = $this->getOrphanDownloads();
		$OrphanUploads = $this->getOrphanUploads();
		$cfid = remositoryRepository::getParam($_REQUEST, 'cfid', array());
	    foreach ($cfid as $file64) {
			$file = base64_decode($file64);
			if (in_array($file, $OrphanDownloads) OR in_array($file, $OrphanUploads)) @unlink($file);
	    }
	    $this->listTask();
	}

}
