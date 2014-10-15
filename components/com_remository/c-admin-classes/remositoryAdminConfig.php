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



class remositoryAdminConfig extends remositoryAdminControllers {

    function __construct ($admin) {
	parent::__construct ($admin);
	$_REQUEST['act'] = 'config';
    }

    function listTask (){
	$customnames = $this->repository->custom_names ? unserialize(base64_decode($this->repository->custom_names)) : array();

	$view = $this->admin->newHTMLClassCheck ('listConfigurationHTML', $this, 0, '');
	if ($view AND $this->admin->checkCallable($view, 'view')) $view->view($customnames);
    }

    function saveTask () {
	$oldwithid = $this->repository->Real_With_ID;
	$this->repository->addPostData();
	if ($oldwithid AND 0 == $this->repository->Real_With_ID) {
	    $database = $this->interface->getDB();
	    $database->setQuery("SELECT MAX( f.occur ) FROM (SELECT COUNT( * ) AS occur FROM jos_downloads_files WHERE islocal !=0 GROUP BY realname) AS f");
	    if (1 < $database->loadResult()) {
		$message = _DOWN_POSSIBLE_DUPLICATE_NAMES;
		$this->repository->Real_With_ID = 1;
	    }
	}
	$customobj = new remositoryCustomizer();
	$fields = $customobj->getFileListFields();
	foreach ($fields as $key=>$farr) {
	    $values['A'][$key] = empty($_POST['afield'][$key]) ? 0 : 1;
	    $values['B'][$key] = empty($_POST['bfield'][$key]) ? 0 : 1;
	    $values['C'][$key] = empty($_POST['cfield'][$key]) ? 0 : 1;
	    $values['D'][$key] = empty($_POST['dfield'][$key]) ? 0 : 1;
	    $values['E'][$key] = empty($_POST['efield'][$key]) ? 0 : 1;
	    $values['S'][$key] = empty($_POST['sequence'][$key]) ? 5 : (int) $_POST['sequence'][$key];
	}
	/*
	foreach ($values['S'] as $key=>$sequence) $reseq[$sequence][] = $key;
	$sequence = 10;
	if (isset($reseq)) foreach ($reseq as $kset) foreach ($kset as $key) {
		$values['S'][$key] = $sequence;
		$sequence += 10;
	}
	*/
	unset ($_POST['afield'], $_POST['bfield'], $_POST['cfield'], $_POST['dfield'], $_POST['efield'], $_POST['sequence']);
	$custom_names = remositoryRepository::getParam($_POST, 'custom_name', array());
	$custom_titles = remositoryRepository::getParam($_POST, 'custom_title', array());
	$custom_values = remositoryRepository::getParam($_POST, 'custom_values', array());
	$custom_uploads = remositoryRepository::getParam($_POST, 'custom_upload', array());
	$custom_lists = remositoryRepository::getParam($_POST, 'custom_list', array());
	$custom_infos = remositoryRepository::getParam($_POST, 'custom_info', array());
	foreach ($custom_names as $sub=>$name) if ($name AND $custom_titles[$sub]) {
	    $customfields[$name]['title'] = $custom_titles[$sub];
	    $customfields[$name]['values'] = $custom_values[$sub];
	    $customfields[$name]['upload'] = $custom_uploads[$sub];
	    $customfields[$name]['list'] = $custom_lists[$sub];
	    $customfields[$name]['info'] = $custom_infos[$sub];
	}
	$this->repository->custom_names = isset($customfields) ? base64_encode(serialize($customfields)) : '';
	
	$customobj->saveCustomSpec($values);
	if (0 == $this->repository->Use_Database) {
	    $diskpath = $this->repository->Down_Path;
	    if ('/' != substr($diskpath,-1)) $diskpath .= '/';
	    remositoryRepository::doSQL("UPDATE #__downloads_containers SET filepath = '$diskpath' WHERE filepath = ''");
	}
	aliroAuthorisationAdmin::getInstance()->clearCache();
	// Move files as necessary
	$this->relocateFilesCorrectly();
	$this->backTask( empty($message) ? _DOWN_CONFIG_COMP : $message );
    }

}