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

class remository_deletecontainer_Controller extends remositoryUserControllers {
	
    function deletecontainer ($func) {
	
	//we check the entry parameter
	if (! isset($this->idparm)) {
	    $viewer = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $this);
	    $viewer->uploadError(_DOWN_BAD_POST);
	    return;
	}
	
	//we check that the create container option is enabled
	if(! $this->repository->Allow_Container_Delete){
	    $viewer = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $this);
	    $viewer->uploadError(_DOWN_NOT_VALID_BR);
	    return;
	}
		
	$container = new remositoryContainer($this->idparm);
	$nbChildren = $container->countDescendants();

	if(! $container->deletePermitted($this->remUser)){
	    $viewer = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $this);
	    $viewer->uploadError(_DOWN_CONTAINER_DELETE_INVALID_GROUP);
	    return;
	}
	//we need a user confirmation to delete the folder
	else if(isset($_POST['cancel'])){
	    $url = $this->repository->RemositoryBasicFunctionURL('', $container->parentid);
	    header('Location: ' . $url); 
	}
	else if(isset($_POST['confirm'])){
	    
	    $this->parentid = $container->parentid;
	    
	    //we delete the folder and its descendants
	    $authoriser = aliroAuthorisationAdmin::getInstance();
	    foreach (remositoryContainer::$actions as $action) {
		$this->dropPermissions($authoriser, $action, $container, false);
	    }
	    $container->deleteAll(true);
	    // The file/folder counts will have been upset, so recalculate
	    $this->repository->resetCounts();	    
	    
	    $url = $this->repository->RemositoryBasicFunctionURL('', $container->parentid);
	    header('Location: ' . $url); 
	}
	else {
		$view = remositoryUserHTML::viewMaker('DeleteContainerHTML', $this);
		$view->waitForConfirmationHTML($container->id, $nbChildren == 0);
	}
    }

    private function dropPermissions ($authoriser, $action, $container, $inherit) {
	$authoriser->dropPermissions($action, 'remosFolder', $container->id);
	if ($inherit) {
	    $descendants = $container->getDescendants();
	    foreach ($descendants as $descendant) $authoriser->dropPermissions($action, 'remosFolder', $descendant->id);
	}
    }
}