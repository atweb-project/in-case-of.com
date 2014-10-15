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



class remository_createcontainer_Controller extends remositoryUserControllers {
	
	function createcontainer ($func) {
		//we check the entry parameter
		if (! isset($this->idparm)) {
		    $viewer = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $this);
		    $viewer->uploadError(_DOWN_BAD_POST);
		    return;
		}
		
		//we check that the create container option is enabled
		if(! $this->repository->Allow_Container_Add){
		    $viewer = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $this);
		    $viewer->uploadError(_DOWN_NOT_VALID_BR);
		    return;
		}
		
		$parentContainer = remositoryContainerManager::getInstance()->getContainer($this->idparm);
		
		//we check that the parent container folder is correct
		if(! is_dir($parentContainer->filepath)){
		    $view = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $this);
		    $view->genericError($parentContainer, _DOWN_SUBCONTAINER_CREATE_FOLDER_ERR01, _DOWN_SUBCONTAINER_CREATE_ERR_TITLE);
		    return;
		}
		if(! is_writable($parentContainer->filepath)){
		    $view = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $this);
		    $view->genericError($parentContainer, _DOWN_SUBCONTAINER_CREATE_FOLDER_ERR02, _DOWN_SUBCONTAINER_CREATE_ERR_TITLE);
		    return;
		}
                
		//we check the remository rights
		if(! $parentContainer->createPermitted($this->remUser)){
		    $view = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $this);
		    $view->genericError($parentContainer, _DOWN_SUBCONTAINER_CREATE_INVALID_GROUP, _DOWN_SUBCONTAINER_CREATE_ERR_TITLE);
		    return;
		}
		
                //cancel operation action
		//we need a user confirmation to create the folder
		if(isset($_POST['cancel'])){
			$url = $this->repository->RemositoryBasicFunctionURL('', $container->parentid);
			header('Location: ' . $url); 
		}
                //create action
		else if(isset($_POST['create'])){
		    //we create the new container   
		    try{
			$container = $parentContainer->makeCopyAsChild($_POST['name'], false);
		    }
		    catch (InvalidFilenameException $e){
			//wrong title parameter : same player shoot again...
			$view = remositoryUserHTML::viewMaker('CreateContainerHTML', $this);
			$view->createContainerHTML($parentContainer, '<h2 class="remositorywarning">'. _DOWN_SUBCONTAINER_CREATE_ERR_TITLE . '</h2><p class="remositorywarning">' .$e->getMessage() . '</p>');
			return;
		    }
		    catch (Exception $e){
			$view = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $this);
			$view->genericError($parentContainer, $e->getMessage(), _DOWN_SUBCONTAINER_CREATE_ERR_TITLE);
			return;
		    }
		    $url = $this->repository->RemositoryBasicFunctionURL('', $parentContainer->id); //TODO: admin config panel to choose if we go back to parent container or new one
		    header('Location: ' . $url);
		}
		else {
			$view = remositoryUserHTML::viewMaker('CreateContainerHTML', $this);
			$view->createContainerHTML($parentContainer, null);
		}
	}
        

}