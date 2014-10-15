<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006-10 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

class remository_userupdate_Controller extends remositoryUserControllers {
	
	function userupdate ($func) {
		$file = $this->createFile ();
		if (!$file->updatePermitted($this->remUser)) {
			$this->error_popup (_DOWN_NOT_AUTH);
			return;
		}
		//Check for Upload amt limit
		$container = $file->getContainer();
		// Does not return if not permitted
		$this->remUser->allowUploadCheck($container, $this);
		$clist = $this->repository->getSelectList(false,$file->containerid,'containerid','class="inputbox"',$this->remUser,true);
		$view = remositoryUserHTML::viewMaker('AddFileHTML', $this);
		$view->addfileHTML($clist, $file);
	}
	
}
