<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2009 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

class remositoryMiniFile {
	var $id = 0;
	var $realname = '';
}

class remositoryAdditionsManager {

	private function stdpath ($path) {
		if (substr($path,-1) != '/') $path .= '/';
		$filepath=str_replace("\\","/",$path);
		$filepath=str_replace("\\","/",$path);
		return $filepath;
	}

	private function addOneLevel ($path, $container, &$extensions, $extensiontitle, $delete, $recurse, $submitter, $filedata, $permissions) {
		if (substr($path,-1) != '/') $path .= '/';
		$directory = new remositoryDirectory($path);
		$files = $directory->listFiles();
		$newfile = new remositoryFile ();
		$existingfiles = array();
		if (count($files)) {
			$existing = remositoryRepository::doSQLget("SELECT id, realname FROM #__downloads_files WHERE filepath = '$path' OR containerid = $container->id", 'remositoryMiniFile');
			foreach ($existing as $file) {
				$existingfiles[] = $file->realname;
				$existingfiles[] = remositoryPhysicalFile::basicNameWithID($file->id, $file->realname);
			}
			unset($existing);
			$newfile->containerid = $container->id;
			$newfile->memoContainer($container);
			$newfile->published = 1;
			$newfile->submittedby = $submitter;
			foreach ($filedata as $key=>$data) $newfile->$key = $data;
			$chosenicon = $newfile->icon;
			// Extensions are prechecked
			$newfile->validate(false);
		}
		foreach ($files as $file) {
			@set_time_limit(25);
			$ext = remositoryAbstract::lastPart($file,'.');
			if ($extensions != '*' AND !in_array($ext, $extensions)) continue;
			if (in_array($file, $existingfiles)) continue;
			$filepath = $path.$file;
			$physical = new remositoryPhysicalFile ();
			$physical->setData($filepath, 0, 0, 0, false);
			$newfile->id = 0;
			$newfile->filetitle = '';
			$newfile->icon = $chosenicon;
			$newfile->storePhysicalFile ($physical, $extensiontitle, false);
			if ($delete) @unlink($filepath);
		}
		unset($existingfiles, $files, $newfile);
		if ($recurse) {
			$directories = $directory->listFiles('','dir');
			foreach ($directories as $newdir) {
				$dirpath = $path.$newdir;
				$children = $container->getChildren(false);
				foreach ($children as $child) {
					if ($newdir == $child->name OR $newdir == basename($child->filepath)) {
						$folder =& $child;
						break;
					}
				}
				if (!isset($folder)) {
					$folder = new RemositoryContainer ();
					$folder->parentid = $container->id;
					$folder->name = $newdir;
					$folder->plaintext = $container->plaintext;
					if ($container->filepath) $folder->filepath = $container->filepath.$newdir.'/';
					$folder->published = 1;
					$folder->saveValues();
					$this->savePermissions($folder, $permissions);
				}
				// Create new directory if necessary, including for existing, to remedy any missing directories if possible
				if ($folder->filepath AND !file_exists($folder->filepath))$newdir = new remositoryDirectory ($folder->filepath, true);
				if (1 < $recurse) $this->addOneLevel ($dirpath, $folder, $extensions, $extensiontitle, $delete, $recurse, $submitter, $filedata, $permissions);
				unset($folder);
			}
		}
	}

	public function saveNewFiles ($basedir, $containerid, $recurse, $extensionlist, $extensiontitle, $submitter, $filedata, $permissions, &$message, &$badfiles) {
	    $interface = remositoryInterface::getInstance();
		$dir = new remositoryDirectory($basedir);
		// Change for multiple repositories
		// if (!$dir->isDirectory()) $this->interface->redirect( "index2.php?option=com_remository&act=addstructure&repnum=".$this->repnum, _DOWN_STRUCT_NO_DIR );
		if (!$dir->isDirectory()) {
			$message = _DOWN_STRUCT_NO_DIR;
			return false;
		}
		$extensionlist = trim($extensionlist);
	    if (!$extensionlist OR '*' == $extensionlist) {
			$extensions = '*';
			$badfiles = $dir->findBadExtension($recurse);
			if (count($badfiles)) return false;
		}
	    else {
			$extensions = explode(',', $extensionlist);
	    	$extensions = array_map('trim', $extensions);
	    }
	    $container = new remositoryContainer($containerid);
		if ($container->filepath AND !file_exists($container->filepath)) new remositoryDirectory($container->filepath, true);
	    if ($basedir AND $containerid) {
			$this->addOneLevel ($basedir, $container, $extensions, $extensiontitle, False, $recurse, $submitter, $filedata, $permissions);
			remositoryRepository::getInstance()->resetCounts();
		}
		return true;
	}

	// Private function for tidiness
	private function savePermissions ($container, $defaults) {
		$authoriser = aliroAuthorisationAdmin::getInstance();
		foreach ($defaults as $action=>$role) {
			$authoriser->permit ($role, 2, $action, 'remosFolder', $container->id);
		}
	}

}