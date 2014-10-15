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

class remository_savemanyfiles_Controller extends remositoryUserControllers {
	
	function savemanyfiles ($func) {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		//Process the variables
		$container = new remositoryContainer(remositoryRepository::getParam($_POST, 'containerid', 0));
		if (!$container->id) die('Invalid container given');
		$this->remUser->allowUploadCheck($container, $this);
		$autoapp = $this->remUser->hasAutoApprove($container);
		if (!$autoapp) die('Bulk upload not permitted except with auto-approve');
		$physicalFiles = array();
		$newfiles = array();
		$filetitles='';
		for ($i=0; $i<30; $i++) {
			$uploadinfo = $this->interface->triggerMambots('onRemositoryRequestUpload', $this->interface);
			$message = implode(' ', $uploadinfo);
			if (trim($message)) {
				$viewer = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $this);
				$viewer->uploadError($message);
				return;
			}
			$physicalFiles[$i] = new remositoryPhysicalFile();
			$physicalFiles[$i]->handleUpload($i);
			$newfiles[$i] = new remositoryFile();
			$newfiles[$i]->addPostData();
			$newfiles[$i]->checkLicenseagree();
			$newfiles[$i]->memoContainer($container);
			$newfiles[$i]->submittedby = $this->remUser->id;
			$newfiles[$i]->published = 1;
			$newfiles[$i]->autoShort = 1;
			$newfiles[$i]->filedate = remositoryRepository::getParam($_POST, 'filedate');
			$newfiles[$i]->id = 0;
			$newfiles[$i]->filetitle = remositoryRepository::getParam($_POST, 'filetitle'.$i);
			if (!$newfiles[$i]->filetitle) $newfiles[$i]->filetitle = $physicalFiles[$i]->proper_name;
		    if ($_FILES['userfile'.$i]['name'] <>'') {
				if ($newfiles[$i]->storePhysicalFile ($physicalFiles[$i])) $filetitles .=  "\n" . $newfiles[$i]->filetitle; 
		    }
	    }
		//Send Admin notice
		if ($this->repository->Send_Sub_Mail) $this->repository->sendAdminMail($this->remUser->fullname.' ('.$this->remUser->name.')', $filetitles, $container->id);
	    $this->repository->resetCounts(array());
		$view = remositoryUserHTML::viewMaker('AddManyFilesDoneHTML', $this);
		$view->addManyFilesDoneHTML ($newfiles);
	}
	
}
