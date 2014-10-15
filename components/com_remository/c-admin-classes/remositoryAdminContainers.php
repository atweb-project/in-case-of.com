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

class remositoryAdminContainers extends remositoryAdminControllers {

	public function __construct (&$admin) {
		parent::__construct ($admin);
		$this->parentid = remositoryRepository::getParam($_REQUEST, 'parentid', 0);
	    $_REQUEST['act'] = 'containers';
	}

	public function listTask () {
		// Get the search string that will constrain the list of containers displayed
		$search = trim( strtolower( remositoryRepository::getParam( $_POST, 'search', '' ) ) );
		// Get the flag that tells us whether to continue to nested containers right down to the bottom
		$descendants = intval(remositoryRepository::getParam($_POST, 'descendants', 0));
		// Create the container above our present position - might be degenerate
		$container = new remositoryContainer($this->parentid);
		// Get all the containers that are to be displayed
		if ($descendants) {
			$folders = $container->getDescendants($this->admin->limitstart, $this->admin->limit, $search);
			$total = $container->countDescendants($search);
		}
		else {
			$folders = $container->getSelectedChildren($this->admin->limitstart, $this->admin->limit, $search);
			$total = $container->countChildren($search);
		}
		clearstatcache();
		foreach ($folders as $key=>$folder) {
			if ($folder->filepath) {
				if ('/' != $folder->filepath[0] AND !preg_match('#^[a-z]:/#i', $folder->filepath)) $pathstatus = '<span class="remositoryred">'._DOWN_NOT_ABSOLUTE.'</span>';
				elseif (file_exists($folder->filepath)) {
					if (is_writeable($folder->filepath)) $pathstatus = _DOWN_FILE_SYSTEM_OK;
					else $pathstatus = '<span class="remositoryred">'._DOWN_NOT_WRITEABLE.'</span>';
				}
				else $pathstatus = '<span class="remositoryred">'._DOWN_DIRECTORY_NON_EXISTENT.'</span>';
			}
			else $pathstatus = _DOWN_DATABASE;
			$folders[$key]->pathstatus = $pathstatus;
		}
		// Generate a container list for user to select where to be
		$manager = remositoryContainerManager::getInstance();
		if ($manager->count() < 100) $clist = $container->getSelectList('parentid', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', $this->remUser);
		else {
			$clist = _DOWN_MANY_CONTAINERS;
			$clist .= <<<HIDDEN_PARENT

				<input type="hidden" name="parentid" value="$container->id" />

HIDDEN_PARENT;

		}
		// Create and activate a View object
		$view = $this->admin->newHTMLClassCheck ('listContainersHTML', $this, $total, $clist);
		$view->view($container, $folders, $descendants, $search);
	}

	public function addTask () {
		// This is the parent container for our current location to generate clist
		$currparent = remositoryRepository::getParam($_REQUEST, 'currparent', 0);
		$container = new remositoryContainer($currparent);
		$interface = remositoryInterface::getInstance();
		$subsinfo = $interface->triggerMambots('querySubscriptionCount', array('com_remository', 0, 0));
		// Generate a container list so the user can change the parent
		$clist = $container->getSelectList('parentid', 'class="inputbox"', $this->remUser);
		// Now create empty container
		$container = new remositoryContainer();
		// Create and activate a View object
		$view = $this->admin->newHTMLClassCheck ('editContainersHTML', $this, 0, $clist);
		foreach (remositoryContainer::$actions as $action) $selector[$action] = $this->getRoleSelect(null, $action);
		$view->view($container, $selector, count($subsinfo));
	}

	public function editTask () {
		// Create a container object that will be filled with data from the DB using currid as key
		$container = new remositoryContainer($this->admin->currid);
		$parent = new remositoryContainer($container->parentid);
		if (0 == $container->id) {

		}
		$interface = remositoryInterface::getInstance();
		$subsinfo = $interface->triggerMambots('querySubscriptionCount', array('com_remository', 0, 0));
		// Generate a container list so the user can change the parent
		$clist = $parent->getSelectList('parentid', 'class="inputbox"', $this->remUser, $container->id);
		// Create and activate a View object
		$view = $this->admin->newHTMLClassCheck ('editContainersHTML', $this, 0, $clist);
		foreach (remositoryContainer::$actions as $action) $selector[$action] = $this->getRoleSelect($container, $action);
		$view->view($container, $selector, count($subsinfo));
	}

	// This is a private function
	private function getRoleSelect ($container, $action) {
		$defaults = array(
		'upload' => 'Registered',
		'download' => 'Public',
		'edit' => 'Nobody'
		);
		$defaults['selfApprove'] = $this->repository->Enable_User_Autoapp ? 'Registered' : 'Nobody';
		$authoriser = aliroAuthorisationAdmin::getInstance();
		$roles = $authoriser->getAllRoles(true);
		if ($container) $selected = $authoriser->permittedRoles ($action, 'remosFolder', $container->id);
		elseif (isset($defaults[$action])) $selected = array($defaults[$action] => 1);
		else $selected = array();
		foreach ($roles as $role=>$translated) $selector[] = $this->repository->makeOption($role, $translated);
		if (isset($selector)) return $this->repository->selectList ($selector, 'permit_'.$action.'[]', 'multiple="multiple"', array_keys($selected));
		else return 'This is the role selector';
	}

	public function saveTask () {
		$container = $this->commonSave();
		// Next we locate ourselves where this container has finished up and list containers
		$this->parentid = $container->parentid;
		$this->listTask();
	}

	public function applyTask () {
		$this->commonSave();
		$this->editTask();
	}

	private function commonSave () {
		// Create a container object that will be filled with data from the DB using currid as key
	    $container = new remositoryContainer($this->admin->currid);
	    // Clear tick box fields as nothing will be received if they are unticked
	    $container->published = $container->plaintext = 0;
	    // Add the new information from the form just submitted
	    $container->addPostData();
	    if ($container->plaintext) $container->filepath = '';
	    // By default, a new container is automatically published
	    if ($this->admin->currid == 0) $container->published = 1;
	    // Check for anomalies in the file path specified, if any
	    $container->checkFilePath();
	    // If fields are to be inherited by descendants, do it
	    $inheritpath = empty($_POST['inheritpath']) ? false : true;
	    if ($inheritpath) $container->makeDescendantsInherit();
	    $inherit = empty($_POST['inherit']) ? false : true;
	    // Save the new information about the container to the database
	    $container->saveValues ();
	    // Handle the permissions
	    $this->savePermissions($container, $inherit);
	    // Update the memorandum fields held in any files within this container
	    remositoryFile::storeMemoFields($container, $inheritpath);
	    // Move files as necessary
	    $this->relocateFilesCorrectly();
	    // Remove any orphan entries in the blob table
		remositoryRepository::doSQL("DELETE LOW_PRIORITY #__downloads_blob FROM #__downloads_blob LEFT JOIN #__downloads_files ON #__downloads_blob.fileid = #__downloads_files.id WHERE #__downloads_files.id IS NULL");
	    // The changes may well have altered the file/folder counts, so recalculate
		$this->repository->resetCounts();
		return $container;
	}

	// Private function for tidiness
	private function savePermissions ($container, $inherit) {
		$authoriser = aliroAuthorisationAdmin::getInstance();
		$interface = remositoryInterface::getInstance();
		foreach (remositoryContainer::$actions as $action) {
			$this->dropPermissions($authoriser, $action, $container, $inherit);
			$roles = remositoryRepository::getParam($_POST, 'permit_'.$action, array());
			if (in_array('Public', $roles)) continue;
			if (in_array('Registered', $roles)) {
				$this->grantPermissions($authoriser, 'Registered', $action, $container, $inherit);
				continue;
			}
			$extra = remositoryRepository::getParam($_POST, 'new_role_'.$action);
			if ($extra) $roles[] = $extra;
			foreach ($roles as $role) {
				$role = $interface->getEscaped($role);
				if ('none' != $role) $this->grantPermissions($authoriser, $role, $action, $container, $inherit);
			}
		}
	}
	
	private function dropPermissions ($authoriser, $action, $container, $inherit) {
		$authoriser->dropPermissions($action, 'remosFolder', $container->id);
		if ($inherit) {
			$descendants = $container->getDescendants();
			foreach ($descendants as $descendant) $authoriser->dropPermissions($action, 'remosFolder', $descendant->id);
		}
	}
	
	private function grantPermissions ($authoriser, $role, $action, $container, $inherit) {
		$authoriser->permit ($role, 2, $action, 'remosFolder', $container->id);
		if ($inherit) {
			$descendants = $container->getDescendants();
			foreach ($descendants as $descendant) $authoriser->permit ($role, 2, $action, 'remosFolder', $descendant->id);
		}
	}

	public function deleteTask (){
		// In case the Javascript cannot do the check, ensure at least one item selected
		$this->admin->check_selection(_DOWN_SEL_FILE_DEL);
		// For each selected container, create an object then delete (will delete from DB)
		$authoriser = aliroAuthorisationAdmin::getInstance();
		foreach ($this->admin->cfid as $id) {
			$container = new remositoryContainer($id);
			foreach (remositoryContainer::$actions as $action) $this->dropPermissions($authoriser, $action, $container, false);
			$this->parentid = $container->parentid;
			$container->deleteAll();
		}
		// The file/folder counts will have been upset, so recalculate
		$this->repository->resetCounts();
		// Now show the list of containers again
		$this->listTask();
	}

	public function publishTask () {
		$this->publishToggle(1);
	}

	public function unpublishTask () {
		$this->publishToggle(0);
	}

	private function publishToggle ($publish) {
		// Check that one or more items have been selected (Javascript may not have run)
		$this->admin->check_selection(_DOWN_PUB_PROMPT.($publish ? 'publish' : 'unpublish'));
	    remositoryContainer::togglePublished($this->admin->cfid,$publish);
	    // The file/folder counts only include published items, so recalculate
		$this->repository->resetCounts();
		// List out the containers again
		if (isset($this->admin->cfid[0])) {
		    $container = new remositoryContainer($this->admin->cfid[0]);
		    $this->parentid = $container->parentid;
		}
		$this->listTask();
	}

}
