<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2008 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

class remository_userdelete_Controller extends remositoryUserControllers {
	
	function userdelete ($func) {
		$file = $this->createFile ();
		if (!$file->deletePermitted($this->remUser)) {
			$this->error_popup (_DOWN_NOT_AUTH);
			return;
		}
		$this->interface->triggerMambots('onRemositoryAboutToDelete', $file->id);
		$containerid = $file->containerid;
		$file->deleteFile();
		$logentry = new remositoryLogEntry(_REM_LOG_DELETE, $this->remUser->id, $file->id, 0);
		$logentry->insertEntry();
		$interface = remositoryInterface::getInstance();
		$interface->redirect($this->repository->RemositoryBasicFunctionURL('select', $containerid));
	}
	
}
