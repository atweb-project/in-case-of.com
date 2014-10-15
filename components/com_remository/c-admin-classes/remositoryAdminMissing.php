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

class remositoryAdminMissing extends remositoryAdminControllers {

	public function __construct ($admin) {
		parent::__construct ($admin);
	    $_REQUEST['act'] = 'missing';
	}

	private function getMissingFiles(){
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		// Change for multiple repositories
		// $database->setQuery('SELECT id, metatype, islocal, isblob, plaintext, filepath, realname, realwithid, chunkcount, url FROM #__downloads_files WHERE repnum = '.$this->repnum);
		$database->setQuery('SELECT id, metatype, islocal, isblob, plaintext, filepath, realname, realwithid, chunkcount, url FROM #__downloads_files');
		$files = $database->loadObjectList();
		$missingFiles = array();
		if ($files) foreach ($files as $sub=>$file) {
			if ($file->islocal) {
				if ($file->isblob) {
					$files[$sub]->location = 'xxx_downloads_blob';
					$database->setQuery("SELECT COUNT(chunkid) FROM #__downloads_blob WHERE fileid=$file->id");
					$chunks = $database->loadResult();
					if ($chunks == 0) {
						$missingFiles[]=$file;
					}
					elseif ($file->chunkcount == 0) {
						$database->setQuery("UPDATE #__downloads_files SET chunkcount=$chunks WHERE id=$file->id");
						$database->query();
					}
					elseif ($file->chunkcount != $chunks) {
						$missingFiles[]=$file;
					}
				}
				elseif ($file->plaintext) {
					$files[$sub]->location = 'xxx_downloads_text';
					$database->setQuery("SELECT COUNT(id) FROM #__downloads_text WHERE fileid=$file->id");
					$texts = $database->loadResult();
					if ($texts != 1) {
						$missingFiles[]=$file;
					}
				}
				else {
					if (0 < $file->metatype) $files[$sub]->location = $this->repository->Up_Path.'/';
					else $files[$sub]->location = $file->filepath;
				    $physical = new remositoryPhysicalFile();
				    $physical->setData($files[$sub]->location.$file->realname, $file->id, 0, 0, $file->realwithid);
					if (!$physical->exists()) $missingFiles[]=$file;
				}
			}
		}
		if ($files) foreach ($files as $file) {
			if (!$file->islocal) {
				$url = $file->url;
				if (!$url) $url = _DOWN_LOCAL_NO_URL;
				if (!preg_match(_REMOSITORY_REGEXP_URL,$url) AND !preg_match(_REMOSITORY_REGEXP_IP,$url)) {
						$missingFiles[]=$file;
				}
			}
		}
		$user = $interface->getUser();
		foreach ($missingFiles as $sub=>$missing) {
		    $fileobject = new remositoryFile($missing->id);
		    $fileobject->getValues($user);
		    $fileobject->location = isset($missing->location) ? $missing->location : '';
		    $missingFiles[$sub] = $fileobject;
		}
		return $missingFiles;
	}

	public function listTask () {
		$MissingFiles = $this->getMissingFiles();
		$view = $this->admin->newHTMLClassCheck ('listMissingFilesHTML', $this, 0, '');
		$view->view($MissingFiles);
				}


	public function editTask () {
		// Make a file object, with an ID from the form submitted
		$file = new remositoryFile($this->admin->currid);
		// Fill the file object with data from the database
		$file->getValues($this->remUser);
		// Generate a list of possible containers in which the file could be located
		$clist = $file->getEditSelectList('containerid','class="inputbox"',$this->remUser);
		// Find custom field information
		$customnames = $this->repository->custom_names ? unserialize(base64_decode($this->repository->custom_names)) : array();
		// Create and activate a View object
		$view = $this->admin->newHTMLClassCheck ('editFilesHTML', $this, 0, $clist);
		$view->view($file, $customnames );
	}

	public function deleteTask () {
		$this->admin->check_selection(_DOWN_SEL_FILE_DEL);
		foreach ($this->admin->cfid as $id) {
			$file = new remositoryFile ($id);
			$file->getValues($this->remUser);
			$file->deleteFile();
		}
	    // The changes may well have altered the file/folder counts, so recalculate
		$this->repository->resetCounts();
		$this->listTask();
	}

}