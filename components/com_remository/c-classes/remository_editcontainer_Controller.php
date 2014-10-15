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
*
* --Written by Gebus
*/

class remository_editcontainer_Controller extends remositoryUserControllers {
	
    function editcontainer ($func) {

	if (! isset($this->idparm)) {
	    $viewer = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $this);
	    $viewer->uploadError(_DOWN_BAD_POST);
	    return;
	}
		
	//we check that the create container option is enabled
	if(! $this->repository->Allow_Container_Edit){
	    $viewer = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $this);
	    $viewer->uploadError(_DOWN_NOT_VALID_BR);
	    return;
	}
	
	$container = remositoryContainerManager::getInstance()->getContainer($this->idparm);

	if(! $container->updatePermitted($this->remUser)){
	    $viewer = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $this);
	    $viewer->uploadError(_DOWN_CONTAINER_UPDATE_INVALID_GROUP);
	    return;
	}
	//we need a user confirmation to update the folder
	else if(isset($_POST['cancel'])){
	    $url = $this->repository->RemositoryBasicFunctionURL('', $container->parentid);
	    header('Location: ' . $url); 
	}
	else if(isset($_POST['rename'])){
	    $container->name = $_POST['name'];
	    $container->saveValues();
	    $url = $this->repository->RemositoryBasicFunctionURL('', $container->parentid);
	    header('Location: ' . $url);
	}
	else {
	    $view = remositoryUserHTML::viewMaker('EditContainerHTML', $this);
	    $view->editContainerHTML($container);
	}
    }
}