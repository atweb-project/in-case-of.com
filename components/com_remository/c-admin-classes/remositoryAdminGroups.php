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

class remositoryAdminGroups extends remositoryAdminControllers {

	function __construct (&$admin) {
		parent::__construct ($admin);
		$_REQUEST['act'] = 'groups';
	}

	function listTask () {
		$this->clearSelection();
	    $rsearch = strtolower(remositoryRepository::GetParam($_POST, 'rsearch'));
	    $authoriser = aliroAuthoriser::getInstance();
	    $roles = $authoriser->getAllRoles();
	    if ($rsearch) foreach ($roles as $key=>$role) {
	    	if (false === strpos(strtolower($role), $rsearch)) unset($roles[$key]);
	    }
		// Create and activate a View object
		$view = $this->admin->newHTMLClassCheck ('listGroupsHTML', $this, count($roles), '');
		$view->view($roles, $rsearch);
	}

	function addTask () {
		$this->userlist('', true);
	}

	function editTask () {
		$role = remositoryRepository::GetParam($_REQUEST, 'role');
		$getrole = remositoryRepository::GetParam($_GET, 'role');
		$listype = remositoryRepository::GetParam($_POST, 'listype');
		if ('roles' == $listype) {
			$cfid = remositoryRepository::GetParam($_POST, 'cfid', array());
			if (count($cfid)) $this->userlist($cfid[0], false);
			return;
		}
		if ($getrole) $this->userlist($getrole, false);
		elseif ($role) $this->userlist($role, false);
		else $this->listTask();
	}

	function addmembersTask () {
		$role = remositoryRepository::GetParam($_REQUEST, 'role');
		$this->userlist($role, true);
	}
	
	function trackSelectedUsers () {
		$selection = isset($_SESSION['remositoryGroupUsers']) ? $_SESSION['remositoryGroupUsers'] : array();
		$cfid = remositoryRepository::getParam($_POST, 'cfid', array());
		$cfall = remositoryRepository::getParam($_POST, 'cfall', array());
		if (!empty($cfall)) {
			$selection = array_merge($selection, $cfid);
			$selection = array_diff($selection, array_diff($cfall,$cfid));
			$_SESSION['remositoryGroupUsers'] = $selection;
		}
		return $selection;
	}
	
	function clearSelection () {
		unset($_SESSION['remositoryGroupUsers']);
	}

	function saveTask () {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$authoriser = aliroAuthorisationAdmin::getInstance();
		$role = remositoryRepository::getParam($_POST, 'role');
		if ($role) {
			$drole =  $database->getEscaped($role);
			$selection = $this->trackSelectedUsers();
			foreach ($selection as $id) {
				$authoriser->assign ($drole, 'aUser', $id);
			}
			$this->clearSelection();
			$authoriser->clearCache();
			$message = _DOWN_GROUP_SAVED;
	    	$this->interface->redirect("index2.php?option=com_remository&act=groups&task=edit&role=".$role, $message);
		}
		else $message = _DOWN_GROUP_NO_NAME;
    	$this->interface->redirect("index2.php?option=com_remository&act=groups", $message);

	}

	function deleteTask () {
	    $authoriser = aliroAuthorisationAdmin::getInstance();
		$listype = remositoryRepository::GetParam($_POST, 'listype');
		if ('roles' == $listype) {
		    if (!is_array($this->admin->cfid) OR count($this->admin->cfid) < 1) {
		        echo "<script> alert('Please select at least one item to delete'); window.history.go(-1);</script>\n";
		        exit;
		    }
			$roles = remositoryRepository::GetParam($_POST, 'cfid', array());
			$interface = remositoryInterface::getInstance();
			$database = $interface->getDB();
			foreach ($roles as $role) {
				$role = $database->getEscaped($role);
				$authoriser->dropRole($role);
			}
			$this->listTask();
		}
		else {
			$selection = $this->trackSelectedUsers();
		    if (0 == count($selection)) {
		        echo "<script> alert('Please select at least one item to delete'); window.history.go(-1);</script>\n";
		        exit;
		    }
		    $role = remositoryRepository::GetParam($_POST, 'role');
		    if ($role) {
			    foreach ($selection as $userid) $authoriser->unassign($role, 'aUser', $userid);
		    }
		    $this->clearSelection();
			$this->interface->redirect("index2.php?option=com_remository&act=groups&task=edit&role=".$role);
		}
	}

	function userlist ($role, $usersToAdd=false) {
		if (!isset($_SESSION['remositoryGroupUsersAdd']) OR $usersToAdd != $_SESSION['remositoryGroupUsersAdd']) {
			$_SESSION['remositoryGroupUsersAdd'] = $usersToAdd;
			$this->clearSelection();
		}
		$selected = $this->trackSelectedUsers();
		$interface = remositoryInterface::getInstance();
		$search = $interface->getUserStateFromRequest( "searchRemosRoles", 'search', '' );
		$authoriser = aliroAuthoriser::getInstance();
		$userids = $authoriser->listAccessors ('aUser', $role);
	    // list of criteria filters
		$filterby[] = remositoryRepository::makeOption( 'all', '- All fields -');
		$filterby[] = remositoryRepository::makeOption( 'name', 'Name');
		$filterby[] = remositoryRepository::makeOption( 'username', 'userID');
		$filterby[] = remositoryRepository::makeOption( 'email', 'e-mail');
		$lists['filter_by'] = remositoryRepository::selectList($filterby, 'filter_by', 'class="inputbox" size="1"');
		$rows = $this->findUsers ($userids, $search, $total, $usersToAdd);
		// Create and activate a View object
		$view = $this->admin->newHTMLClassCheck ('editGroupsHTML', $this, $total, '');
		$view->view($rows, $selected, $search, $lists, $role, $usersToAdd, $this->admin->task);
	}

	function findUsers ($userids, $search, &$total, $usersToAdd=false) {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$where = array();
		if (count($userids)) {
			if ($usersToAdd) $where[] = 'id NOT IN ('.implode(',', $userids).')';
			else $where[] = 'id IN ('.implode(',', $userids).')';
		}
		elseif (!$usersToAdd) return array();
		if ($search) {
			$filter_by = $interface->getUserStateFromRequest( "filter_by", 'filter_by', 'all');
			$search = $database->getEscaped($search);
			if (!in_array($filter_by, array ('all', 'name', 'username', 'email'))) $filter_by = 'all';
			if ('all' == $filter_by) $where[] = "(u.username LIKE '%$search%' OR u.email LIKE '%$search%' OR u.name LIKE '%$search%')";
			else $where[] = "(u.$filter_by LIKE '%$search%')";
		}
		if ($conditions = implode(' AND ', $where )) $conditions = ' WHERE '.$conditions;
		$query = "SELECT %s FROM #__users AS u";
		$database->setQuery(sprintf($query,'COUNT(*)').$conditions);
		$total = $database->loadResult();
		$this->makePageNav($this->admin, $total);
		if ($total) {
			if ($this->admin->limitstart > $total-1) $this->admin->limitstart = 0; 
			$limiter = " LIMIT {$this->admin->limitstart}, {$this->admin->limit}";
			$database->setQuery(sprintf($query,'u.*, u.usertype as groupname').$conditions.$limiter);
			$rows = $database->loadObjectList();
		}
		else $rows = array();
		return $rows;
	}

}
