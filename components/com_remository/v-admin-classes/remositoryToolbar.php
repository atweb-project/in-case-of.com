<?php

class remositoryToolbar {
	private $act;
	private $task;
	// Create an instance, get the controlling parameters from the request
	function __construct () {
		if ($this->act = remositoryRepository::GetParam ($_REQUEST, 'act', 'about'));
		else $this->act = 'about';
		if ($this->task = remositoryRepository::GetParam($_REQUEST, 'task', 'list'));
		else $this->task = 'list';
		$this->makeBar();
	}
	// create a toolbar based on the parameters found in $_REQUEST
	function makeBar () {
		$this->start();
		$act = $this->act;
		if (method_exists($this,$act)) $this->$act();
		$this->finish();
	}
	// Any initial actions
	function start () {
		remosMenuBar::startTable();
		if ('cpanel' != $this->act) remosMenuBar::custom ('cpanel', 'back.png', 'back_f2.png', _DOWN_CPANEL_RETURN, false );
	}
	// The following methods correspond exactly to the possible values
	// of 'act' in the request.  They in turn correspond to all the
	// possible options in the admin side drop down menu for Remository.
	function containers () {
		if ($this->task == 'add') $this->addMenu(_DOWN_CONTAINER);
		elseif ($this->task == 'edit' OR $this->task == 'apply') $this->editMenu(_DOWN_CONTAINER);
		else $this->listMenu('');
	}

	function classifications () {
		if ($this->task == 'add') $this->addMenu(_DOWN_CLASSIFN);
		elseif ($this->task == 'edit') $this->editMenu(_DOWN_CLASSIFN);
		else $this->listMenu('');
	}

	function files () {
		if (in_array($this->task, array('add','addfile','addurl'))) $this->addMenu(_DOWN_FILE);
		elseif ('dcomment' == $this->task OR 'edit' == $this->task) $this->editMenu(_DOWN_FILE);
		else $this->fileListMenu(_DOWN_FILE);
	}

	function groups () {
		if ('add' == $this->task) $this->addGroupMenu();
		elseif ('addmembers' == $this->task) $this->addmembersGroupMenu();
		elseif ($this->task == 'edit') $this->editGroupMenu();
		elseif (!remositoryRepository::getInstance()->Use_CMS_Groups) $this->groupMenu();
	}

	function ftp () {
		remosMenuBar::publish( 'upload', _DOWN_PUBLISH_FILES );
	}

	function uploads () {
		if ($this->task == 'edit') {
			remosMenuBar::custom ('approve1', 'apply.png', 'apply_f2.png', _DOWN_APPROVE_PUB, false);
			$this->cancel_Button();
		}
		else {
			remosMenuBar::custom( 'approvep', 'publish.png', 'publish_f2.png', _DOWN_APPROVE_PUB, false );
			remosMenuBar::editList( 'edit', _DOWN_EDIT_APPROVAL );
			remosMenuBar::deleteList( '', 'delete', _DOWN_DELETE_SUBMISSION );
		}
	}

	function counts () {
		$this->containers();
	}

	function downloads () {
		$this->files();
	}

	function unlinked () {
		if ($this->task == 'add') $this->addMenu(_DOWN_ORPHAN);
		else {
			remosMenuBar::deleteList( '', 'delete', _DOWN_DELETE_ORPHANS );
			$this->cancel_Button();
		}
	}

	function missing () {
		if ($this->task == 'add') $this->addMenu(_DOWN_MISSING);
		else {
			remosMenuBar::deleteList( '', 'delete', _DOWN_DELETE_MISSING );
			$this->cancel_Button();
		}
	}

	function addstructure () {
		remosMenuBar::save( 'save', _DOWN_SAVE.' '._DOWN_FILE_STRUCTURE );
	}

	function config () {
		remosMenuBar::save( 'save', _DOWN_SAVE.' '._DOWN_ADMIN_ACT_CONFIG );
	}

	function prune () {
		remosMenuBar::save( 'save', _DOWN_SAVE.' '._DOWN_PRUNE );
	}

	// The cancel option is always formed the same way
	function cancel_Button () {
		remosMenuBar::custom( 'list', 'cancel.png', 'cancel_f2.png', _CANCEL, false );
	}

	function back_Button () {
		remosMenuBar::custom( 'back', 'back.png', 'back_f2.png', _DOWN_ABOUT, false );
	}
	// The menu for adding something is always the same apart from the text
	function addMenu ($entity) {
		remosMenuBar::save( 'save', _DOWN_SAVE.' '.$entity );
		if ('Container' == $entity) remosMenuBar::save( 'apply', _DOWN_APPLY.' '.$entity );
		$this->cancel_Button();
	}
	// The menu for editing something is always the same apart from the text
	function editMenu ($entity) {
		remosMenuBar::save( 'save', _DOWN_SAVE.' '.$entity );
		if ('Container' == $entity) remosMenuBar::save( 'apply', _DOWN_APPLY.' '.$entity );
		$this->cancel_Button();
	}
	// The menu for a list of items is always the same apart from the text
	function listMenu ($entity) {
		remosMenuBar::publishList( 'publish', _DOWN_PUBLISH.' '.$entity );
		remosMenuBar::unpublishList( 'unpublish', _DOWN_UNPUBLISH.' '.$entity );
		remosMenuBar::addNew( 'add', _DOWN_ADD.' '.$entity );
		remosMenuBar::editList( 'edit', _DOWN_EDIT.' '.$entity );
		remosMenuBar::deleteList( '', 'delete', _DOWN_DELETE.' '.$entity );
	}
	// The menu for a list of files has two different "add" options
	function fileListMenu ($entity) {
		remosMenuBar::custom( 'localise', 'move.png', 'move_f2.png', _DOWN_LOCALISE_REMOTE_FILE, false );
		remosMenuBar::publishList( 'publish', _DOWN_PUBLISH.' '._DOWN_FILE );
		remosMenuBar::unpublishList( 'unpublish', _DOWN_UNPUBLISH.' '._DOWN_FILE );
		remosMenuBar::addNew( 'addfile', _DOWN_ADD_LOCAL );
		remosMenuBar::apply( 'addurl', _DOWN_ADD_REMOTE );
		remosMenuBar::editList( 'edit', _DOWN_EDIT.' '._DOWN_FILE );
		remosMenuBar::deleteList( '', 'delete', _DOWN_DELETE.' '._DOWN_FILE );
	}
    function editGroupMenu ()
    {
        remosMenuBar::deleteList('', 'delete', _DOWN_DELETE);
		$this->cancel_Button();
        remosMenuBar::spacer();
        remosMenuBar::addNew('addmembers', _DOWN_ADD_MEMBERS);
    }

    function groupMenu ()
    {
        remosMenuBar::addNew('add', _DOWN_ADD);
        remosMenuBar::editList();
		remosMenuBar::deleteList( '', 'delete', _DOWN_DELETE );
    }

    function addGroupMenu ()
    {
        remosMenuBar::save('save', _DOWN_SAVE);
		$this->cancel_Button();
    } 	// Any concluding actions

    function addmembersGroupMenu ()
    {
        remosMenuBar::save('save', _DOWN_SAVE);
		$this->cancel_Button();
    } 	// Any concluding actions

	function finish () {
		remosMenuBar::spacer();
		remosMenuBar::endTable();
	}

}