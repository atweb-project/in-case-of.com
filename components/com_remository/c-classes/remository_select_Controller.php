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

class remository_select_Controller extends remositoryUserControllers {
    private $container = null;

	public function select($func) {
		if ('directlist' == $func) {
			$directlink = true;
			$func = 'select';
		}
		else $directlink = false;
		$interface = remositoryInterface::getInstance();
	    if ('select' == $func AND $this->idparm) {
	        $container = $this->createContainer ();
	        if (!$this->repository->See_Containers_no_download AND !$container->isDownloadable($this->remUser)) {
    			echo "<span class='remositorymessage'>"._DOWN_RESTRICTED_WARN.'</span>';
    	   		return;
	        }
	    }
	    else $container = new remositoryContainer();
		$page = remositoryRepository::getParam($_REQUEST, 'page', 1);
		$this->selectWork ($interface, $func, $container, $this->remUser, $this->idparm, $this->orderby, $page, $directlink);
	}
	
	private function selectWork ($interface, $func, $container, $user, $id, $orderby, $page, $directlink) {
		$manager = remositoryContainerManager::getInstance();
		$categories = $manager->getVisibleCategories($this->remUser);
		if (0 == $manager->count()) {
			$view = remositoryUserHTML::viewMaker('FileListHTML', $this);
			$view->emptyHTML();
			return;
		}
		if ('select' == $func) $subfolders = $container->getVisibleChildren($user);
		else $subfolders = array();
		if ('select' == $func AND 0 == $id AND 0 == $user->id AND count($subfolders) == 0) {
			echo "<span class='remositorymessage'>"._DOWN_NO_VISITOR_CATS.'</span>';
			return;
		}
		if (!$id AND 1 == count($subfolders)) {
			$container = $subfolders[0];
			$interface->setSingleCategory();
			$this->idparm = $container->id;
			$subfolders = $container->getVisibleChildren($user);
		}
	    if ($container->windowtitle) $interface->SetPageTitle($container->windowtitle);
		elseif ($container->name) $interface->SetPageTitle($container->name);
		$this->container = $container;
		$querystring = "&func=$func&id=$container->id&orderby=$orderby";
		$pagecontrol = new remositoryPage ( $this->getFileCount($func), $user, _ITEMS_PER_PAGE, $page, $querystring );
		if (1 == $page AND 0 < $this->repository->Featured_Number) {
			$featured = $container->getFeaturedFiles(true);
			$fcount = count($featured);
		}
		else $fcount = 0;
		if ('select' == $func AND $container->id AND $container->areFilesVisible($user)) $files = $container->getFiles(!$user->isAdmin(), $orderby, null, $pagecontrol->startItem(), $pagecontrol->itemsperpage - $fcount);
		elseif ('filelist' == $func) {
		    if ($id) {
		        $sql = remositoryFile::getFilesSQL(!$user->isAdmin(), false, 0, true, 5, '', $pagecontrol->startItem(), $pagecontrol->itemsperpage - $fcount, $this->idparm);
		        $files = remositoryRepository::doSQLget($sql, 'remositoryFile');
		    }
		    else $files = array();
		}
		else $files = array();
		if ($fcount) $files = array_merge($featured, $files);
		$view = remositoryUserHTML::viewMaker('FileListHTML', $this);
		$view->fileListHTML($id, $container, $subfolders, $files, $pagecontrol, $func, $directlink);
	}
	
	private function getFileCount ($func) {
	    if ('select' == $func) {
	        $container = $this->container;
	        return $container->getFilesCount('', $this->remUser);
	    }
	    if ('filelist' == $func AND $this->idparm) {
    		$interface = remositoryInterface::getInstance();
    		$database = $interface->getDB();
    		$sql = remositoryFile::getFilesSQL(!$this->remUser->isAdmin(), true, 0, true, 5, '', 0, 0, $this->idparm);
    		$database->setQuery($sql);
    		return $database->loadResult();
	    }
	    return 0;
	}
	
	public function getContainerID () {
		return is_object($this->container) ? $this->container->id : 0;
	}

}