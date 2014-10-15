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

class remositoryAdminFiles extends remositoryAdminControllers {
	protected $containerid = 0;

	public function __construct ($admin) {
		parent::__construct ($admin);
		$this->containerid = remositoryRepository::getParam($_REQUEST, 'containerid', 0);
	    $_REQUEST['act'] = 'files';
	}

	private function combinePostData ($name) {
		$combined = '';
		foreach (array_keys($_POST) as $key) {
			$split = explode('_',$key);
			if ($name == $split[0] AND isset($split[1])) {
				if ($combined) $combined .= ','.$split[1];
				else $combined = $split[1];
			}
		}
		$_POST[$name] = $combined;
	}

	public function listTask () {
		// Get the search string that will constrain the list of containers displayed
		$search = trim( strtolower( remositoryRepository::getParam( $_POST, 'search', '' ) ) );
		// Get the flag that tells us whether to show files in nested containers down to the bottom
		$descendants = remositoryRepository::getParam($_POST, 'descendants', 0);
		// Get the container whose files we are listing
		$container = new remositoryContainer($this->containerid);
		// Get the files in the container (and descendants if appropriate), then their total count
		$files = $container->getFiles(false, 2, $search, $this->admin->limitstart, $this->admin->limit, $descendants);
		$total = $container->getFilesCount($search, $this->remUser, $descendants);
		// Generate a container selector for choosing where to look for files
		$clist = $this->repository->getSelectList(true, $this->containerid,'containerid','class="inputbox" size="1" onchange="document.adminForm.submit();"',$this->remUser);
		// Create and activate a View object
		$view = $this->admin->newHTMLClassCheck ('listFilesHTML', $this, $total, $clist);
		$view->view($files, $descendants, $search);
	}

	private function addCommon ($file, $usable) {
		// Give it the default version, if any, and today's date
		$file->fileversion = $this->repository->Default_Version;
		$file->filedate = date('Y-m-d H:i:s',time());
		$orphanpath = base64_decode(remositoryRepository::getParam($_REQUEST, 'orphanpath', ''));
		if ($orphanpath) {
			$physical = new remositoryPhysicalFile();
			$physical->setData($orphanpath);
			$file->getPhysicalData($physical);
		}
		// Generate a list of possible containers to locate the file
		$containers = remositoryRepository::getParam($_REQUEST, 'containers');
		if ($containers) {
			$manager = remositoryContainerManager::getInstance();
			$clist = $manager->makeSelectedList($containers, 'containerid', 'class="inputbox"');
		}
		else $clist = $this->repository->getSelectList(false,$this->containerid,'containerid','class="inputbox"',$this->remUser, $usable);
		// Find custom field information
		$customnames = $this->repository->custom_names ? unserialize(base64_decode($this->repository->custom_names)) : array();
		// Create and activate a View object
		$view = $this->admin->newHTMLClassCheck ('editFilesHTML', $this, 0, $clist);
		$view->view($file, $customnames);
	}

	public function addfileTask () {
		// Make a new, empty file object
		$file = new remositoryFile();
		$file->islocal = 1;
		$this->addCommon($file, true);
	}

	public function addurlTask () {
		// Make a new, empty file object
		$file = new remositoryFile();
		$file->islocal = 0;
		$this->addCommon($file, false);
	}

	public function editTask () {
		if (1 < count($this->admin->cfid)) {
			$file = new remositoryFile();
			$viewclass = 'editMultiFilesHTML';
			$comments = array();
		}
		else {
			// Make a file object, with an ID from the form submitted
			$file = new remositoryFile($this->admin->currid);
			$file->addSubmitterEmail();
			// Fill the file object with data from the database
			$file->getValues($this->remUser);
			$viewclass = 'editFilesHTML';
			$comments = remositoryComment::getComments($file->id);
		}
		// Generate a list of possible containers in which the file(s) could be located
		$clist = $file->getEditSelectList('containerid','class="inputbox"',$this->remUser);
		// Find custom field information
		$customnames = $this->repository->custom_names ? unserialize(base64_decode($this->repository->custom_names)) : array();
		// Create and activate a View object
		$view = $this->admin->newHTMLClassCheck ($viewclass, $this, 0, $clist);
		$view->view($file, $customnames, $comments);
	}

	public function saveTask () {
		// Create a file object for the file to be saved
		$file = new remositoryFile($this->admin->currid);
		// If the file already has an ID, we are updating so we get values from the DB first
		if ($file->id) {
			$file->getValues($this->remUser);
			$oldphysical = $file->obtainPhysical();
		}
		$file->repnum = $this->repnum;
		// The file's previous location, if it was in the file system
		$oldpath = remositoryRepository::getParam($_REQUEST, 'oldpath', '');

		// Combine tick box data if present
		$this->combinePostData('opsystem');
		$this->combinePostData('bugbears');
		// Clear tick box fields as nothing will be received if unticked
		$file->published = $file->autoshort = $file->licenseagree = 0;
		$this->interface->triggerMambots('remositoryFileSaveData', array($file));
		// Now we can add in the information from the submitted form
		$file->addPostData(true);
		// Look for any custom fields and store them, serialized
		$customnames = unserialize(base64_decode($this->repository->custom_names));
		if (!empty($customnames)) {
			foreach ($customnames as $name=>$info) {
				$data = remositoryRepository::getParam($_POST, $name);
				if (!empty($info['values'])) {
					$optionarray = explode(',', $info['values']);
					$data = isset($optionarray[$data]) ? trim($optionarray[$data]) : null;
				}
				$values[$name] = $data;
			}
			$file->custom_values = isset($values) ? base64_encode(serialize($values)) : '';
		}
		// Using the ID we make a container object and set the memo fields in the file object
		$newContainer = new remositoryContainer($file->containerid);
		$file->memoContainer($newContainer);
		// Do some validation to fields and add submitter if not there already
		$file->validate(false);
		if (!$file->submittedby) $file->submittedby = $this->remUser->id;
		//Handle a file upload if present
		if ($file->islocal) {
			$upload = new remositoryPhysicalFile();
			$orphanpath = base64_decode(remositoryRepository::getParam($_REQUEST, 'orphanpath', ''));
			if ($orphanpath AND realpath($orphanpath)) {
				$upload->setData($orphanpath, 0, 0, 0, false);
			}
			else $upload->handleUpload();
			// This case has no new upload, but the files metadata or location may be changed
			if ($upload->error_message == _ERR1 AND $file->id) {
				// Must NOT save twice, as it causes double escaping of quotes, etc.
				// $file->saveFile();
				$upload = null;
				//move file from Old cantainer to new one
				if (!$oldphysical->moveTo($file->filepath.$file->realname, $file->id, $file->isblob, $file->plaintext, $file->realwithid)) {
					$this->error_popup(_DOWN_MOVE_FILE_FAILED);
					$this->listTask();
					return;
				}
			}
			// In this case, there is simply an error with the file upload
			elseif ($upload->error_message) {
				$this->error_popup($upload->error_message);
				$upload = null;
				$this->listTask();
				return;
			}
			// And this is the case where there is an uploaded file
			// If there was an old file in the file system, then it should be deleted
			else {
				$file->storePhysicalFile($upload);
				// Extension was already checked in storePhysicalFile
				$file->validate(false);
				if ($file->id AND !$file->plaintext AND !$file->isblob AND $file->filepath != $oldpath AND file_exists($file->filepath.$file->realname)) @unlink($oldpath.$file->realname);
				if ($orphanpath AND ($file->plaintext OR $file->isblob)) @unlink($orphanpath);
			}
		}
		elseif (!preg_match(_REMOSITORY_REGEXP_URL, $file->url) AND !preg_match(_REMOSITORY_REGEXP_IP,$file->url)) {
			$this->error_popup(_DOWN_LOCAL_NO_URL);
				$this->listTask();
				return;
		}
		// Save the file information to the database, store classifications, then recalculate the counts
		$file->saveFile();
		// Get classification(s)
		$classifications = remositoryRepository::getParam($_POST, 'classification', array());
		$sql = "DELETE FROM #__downloads_file_classify WHERE file_id = $file->id";
		remositoryRepository::doSQL($sql);
		if (count($classifications)) {
			foreach ($classifications as $classification) if (0 != $classification) $classfile[] = "($file->id, $classification)";
			if (isset($classfile)) {
				$sql = "INSERT INTO #__downloads_file_classify VALUES ";
				$sql .= implode (',', $classfile)." ON DUPLICATE KEY UPDATE file_id = file_id";
				remositoryRepository::doSQL($sql);
			}
		}
		if ($file->islocal) {
			$physical = $file->obtainPhysical();
			$this->savethumb($file, $physical);
		}
		$comments = remositoryRepository::getParam($_REQUEST, 'comment', array());
		foreach ($comments as $id=>$comment) {
			$cobject = new remositoryComment($id);
			$cobject->comment = strip_tags($comment);
			$cobject->saveComment($file);
		}
		$this->repository->resetCounts();
		$this->interface->triggerMambots('remositoryUploadSaved', array($file, $this->admin->currid, $this->remUser));
		// List files again - the container used will be the one for the file just saved
		$this->listTask();
	}

	private function savethumb ($file, $upload) {
		if (remositoryRepository::getInstance()->Make_Auto_Thumbnail) {
			$thumbnails = new remositoryThumbnails($file);
			$thumbnails->addAutoThumbnail($file, $upload);
		}
	}
	
	public function localiseTask () {
		foreach ($this->admin->cfid as $id) {
		// Create a file object for the file to be saved
			$file = new remositoryFile($id);
			// Ignore the ID if it is zero
			if ($file->id) {
				$file->getValues($this->remUser);
				$oldpath = $this->absolute_path.substr($file->url,strlen($this->live_site));
				if (!$file->islocal AND 0 === strpos($file->url,$this->live_site) AND file_exists($oldpath)) {
					$physical = new remositoryPhysicalFile();
					$physical->setData($oldpath, $file->id, false, false, false);
					// Using the ID we make a container object and set the memo fields in the file object
					$newContainer = new remositoryContainer($file->containerid);
					// Give up on this one if did not find a container
					if (!is_object($newContainer)) continue;
					$file->memoContainer($newContainer);
					$file->storePhysicalFile($physical);
					// Extension was already checked in storePhysicalFile
					$file->validate(false);
					$file->saveFile();
				}
				// Go to editing the file if only a single item
				if (1 == count($this->admin->cfid))remositoryInterface::getInstance()->redirect("index2.php?option=com_remository&act=files&task=edit&cfid=$file->id&containerid=$file->containerid");
			}
		}
		// List files again - no valid file ID was obtained
		$this->listTask();
	}

	public function deleteTask () {
		// Check that at least one file has been selected, in case Javascript did not
		$this->admin->check_selection(_DOWN_SEL_FILE_DEL);
		$this->interface->triggerMambots('onRemositoryAboutToDelete', $this->admin->cfid);
		// For each selected file, create a file object then delete from the DB and wherever it is stored
		foreach ($this->admin->cfid as $id) {
			$file = new remositoryFile ($id);
			$file->getValues($this->remUser);
			$file->deleteFile();
		}
		// The file/folder counts need to be recalculated
		$this->repository->resetCounts();
		// Related classifications need to be removed
		$inlist = implode(',', $this->admin->cfid);
		$sql = "DELETE FROM #__downloads_file_classify WHERE file_id IN($inlist)";
		remositoryRepository::doSQL($sql);
		// Afterewards, we list out the remaining files
		$this->listTask();
	}

	public function dcommentTask () {
		$commentid = remositoryRepository::getParam($_REQUEST, 'commentid', 0);
		remositoryComment::deleteComment($commentid);
		$this->editTask();
	}

	public function publishTask () {
		$this->publishToggle(1);
	}

	public function unpublishTask () {
		$this->publishToggle(0);
	}

	private function publishToggle ($publish) {
		$trigger = $publish ? 'onRemositoryPublishFiles' : 'onRemositoryUnpublishFiles';
		$this->interface->triggerMambots($trigger, array($this->admin->cfid));
		$this->admin->check_selection(_DOWN_PUB_PROMPT.($publish ? 'publish' : 'unpublish'));
	    remositoryFile::togglePublished($this->admin->cfid,$publish);
		$this->repository->resetCounts(array());
		$this->listTask();
	}

}