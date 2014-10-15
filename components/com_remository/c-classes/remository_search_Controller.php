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

class remository_search_Controller extends remositoryUserControllers {
	private $containerid = 0;

	// For each item, make an entry with name used in HTML pointing to field name in file table
	private $searchable = array (
	'search_filetitle' => 'filetitle',
	'search_filedesc' => 'description'
 	);

	function search ($func) {

	    $containerid = $this->idparm;  //currently not used
	    $interface = remositoryInterface::getInstance();
	    $interface->SetPageTitle(_DOWN_SEARCH_FILES);

	    $abovetop = new remositoryContainer();
	    $categories = $abovetop->getVisibleChildren($this->remUser);
	    if (1 == count($categories) AND 0 == $categories[0]->getFileCount(false)) {
		$subcategories = $categories[0]->getVisibleChildren($this->remUser);
		if (!empty($subcategories)) $categories = $subcategories;
	    }

	    $searchsubmit = remositoryRepository::getParam($_REQUEST,'submit');
	    $actiontype = $searchsubmit ? 'onRemositorySearchRequest' : 'onRemositorySearchStart';
	    $plugs = $interface->triggerMambots($actiontype, array($this, $categories));
	    if (!empty($plugs)) return;

	    if ($searchsubmit) {
		$search_words = urldecode(remositoryRepository::getParam($_GET,'search_text'));
		if (!$search_words) $search_words = remositoryRepository::getParam($_POST,'search_text');
		$search_text = $interface->getEscaped($search_words);
		$catselector = remositoryRepository::getParam($_REQUEST, 'catsearch', array());
		if (!is_array($catselector)) $catselector = array();
		$seek_fields = array();
		$querystring = "&func=search&submit=yes";

		foreach ($this->searchable as $HTMLname=>$fieldname) {
		    $value = remositoryRepository::getParam($_REQUEST, $HTMLname, 0);
		    if ($value) {
			$seek_fields[] = $fieldname;
			$querystring .= "&$HTMLname=".(string) $value;
		    }
		}
		if ($search_text) {
		    foreach (array_keys($catselector) as $cat) {
			$catid = intval($cat);
			$containers[] = $catid;
			$querystring .= "&catsearch[$catid]=1";
		    }
		    if (empty($containers)) $containers = array();
		    $total = $this->repository->searchRepository($search_text, $seek_fields, $this->remUser, null, $containers, true);
		    $page = remositoryRepository::getParam($_REQUEST, 'page', 1);
		    $querystring .= '&search_text='.urlencode($search_text);
		    $pagecontrol = new remositoryPage ( $total, $this->remUser, _ITEMS_PER_PAGE, $page, $querystring );
		    $file_array = $this->repository->searchRepository($search_text, $seek_fields, $this->remUser, $pagecontrol, $containers);
		}
		else {
		    $file_array = array();
		    $pagecontrol = null;
		}
		$view = remositoryUserHTML::viewMaker('SearchResultsHTML', $this);
		$view->searchResultsHTML($file_array, $search_words, $categories, $catselector, $pagecontrol);
	    }
	    else {
		$view = remositoryUserHTML::viewMaker('SearchBoxHTML', $this);
		$view->searchBoxHTML($categories, array());
	    }
	}
	
	function getContainerID(){
	    return $this->containerid;
	}
}
