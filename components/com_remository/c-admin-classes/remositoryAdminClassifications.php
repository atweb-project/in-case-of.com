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

class remositoryAdminClassifications extends remositoryAdminControllers {

	function __construct (&$admin) {
		parent::__construct ($admin);
		$this->parentid = remositoryRepository::getParam($_REQUEST, 'parentid', 0);
	    $_REQUEST['act'] = 'Classifications';
	}

	function listTask () {
		// Get the search string that will constrain the list of containers displayed
		$search = strtolower( remositoryRepository::getParam($_POST, 'search', ''));
		$type = remositoryRepository::getParam($_POST, 'type', '');
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$query = "FROM #__downloads_classify";
		if ($search) {
			$search = $interface->getEscaped($search);
			$where[] = "(name LIKE ('%$search%') OR description LIKE ('%$search%'))";
		}
		if ($type) {
			$type = $interface->getEscaped($type);
			$where[] = "type='$type'";
		}
		if (isset($where)) $query .= ' WHERE '.implode (' AND ', $where);
		$database->setQuery('SELECT COUNT(*) '.$query);
		$total = $database->loadResult();
		$query .= " ORDER BY type, name";
		$query .= " LIMIT {$this->admin->limitstart},{$this->admin->limit}";
		$database->setQuery('SELECT * '.$query);
		$classifications = $database->loadObjectList();
		if (!$classifications) $classifications = array();
		$database->setQuery("SELECT type FROM #__downloads_classify GROUP BY type ORDER BY type");
		$types = $database->loadResultArray();
		if (!$types) $types = array();
		$view = $this->admin->newHTMLClassCheck ('listClassificationsHTML', $this, $total, '');
		$view->view($classifications, $search, $type, $types);
	}

	function addTask () {
		$classification = new remositoryClassification();
		// Create and activate a View object
		$view = $this->admin->newHTMLClassCheck ('editClassificationsHTML', $this, 0, '');
		$view->view($classification);
	}

	function editTask () {
		// Create a classification object that will be filled with data from the DB using currid as key
		$classification = new remositoryClassification($this->admin->currid);
		// Create and activate a View object
		$view = $this->admin->newHTMLClassCheck ('editClassificationsHTML', $this, 0, '');
		$view->view($classification);
	}

	function saveTask () {
		// Create a classification object that will be filled with data from the DB using currid as key
	    $classification = new remositoryClassification($this->admin->currid);
	    // Give tick box fields defaults as nothing will be received if they are unticked
	    $classification->published = 0;
	    $classification->hidden = 1;
	    // Add the new information from the form just submitted
	    $classification->addPostData();
	    // By default, a new classification is automatically published
	    if ($this->admin->currid == 0) $classification->published = 1;
	    // Save the new information about the container to the database
	    $classification->saveValues ();
		// Next we go back to listing the classifications
		$this->listTask();
	}

	function deleteTask () {
		// In case the Javascript cannot do the check, ensure at least one item selected
		$this->admin->check_selection(_DOWN_SEL_FILE_DEL);
		$inlist = implode(',', $this->admin->cfid);
		$sql = "DELETE FROM #__downloads_classify WHERE id IN ($inlist)";
		remositoryRepository::doSQL($sql);
		$sql = "DELETE FROM #__downloads_file_classify WHERE classify_id IN ($inlist)";
		remositoryRepository::doSQL($sql);
		$this->listTask();
	}

	function publishTask () {
		$this->publishToggle(1);
	}

	function unpublishTask () {
		$this->publishToggle(0);
	}

	function publishToggle ($publish) {
		// Check that one or more items have been selected (Javascript may not have run)
		$this->admin->check_selection(_DOWN_PUB_PROMPT.($publish ? 'publish' : 'unpublish'));
		$inlist = implode(',', $this->admin->cfid);
		$sql = "UPDATE #__downloads_classify SET published=$publish WHERE id IN($inlist)";
		remositoryRepository::doSQL($sql);
		$this->listTask();
	}

}

