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

class remository_addfile_Controller extends remositoryUserControllers {
	
	function addfile ($func) {
		$uploadinfo = $this->interface->triggerMambots('onRemositoryRequestUpload', $this->interface);
		$message = implode(' ', $uploadinfo);
		if (trim($message)) {
			$viewer = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $this);
			$viewer->uploadError($message);
			return;
		}
	    $file = new remositoryFile();
		$file->fileversion = $this->repository->Default_Version;
		$file->filedate = date('Y-m-d H:i:s', time());
		$file->fileauthor = $this->remUser->fullname();
		$file->autoshort = 1;
		if ($this->idparm) {
			$container = new remositoryContainer($this->idparm);
			$clist = $container->getPartialSelectList('containerid', 'class="inputbox"', $this->remUser);
		}
		else $clist = $this->repository->getSelectList(false, 0, 'containerid', 'class="inputbox"', $this->remUser, true);
		$view = remositoryUserHTML::viewMaker('AddFileHTML', $this);
		$view->addfileHTML($clist, $file);
	}
	
}