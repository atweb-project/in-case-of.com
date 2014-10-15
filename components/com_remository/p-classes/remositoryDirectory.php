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

class remositoryDirectory {
	private $path='';

	public function __construct ($path, $make=false) {
		$this->path = ('/' == substr($path,-1)) ? $path : $path.'/';
		if ($make AND !is_dir($path)) $this->makePhysical();
	}
	
	function getPath () {
		return $this->path;
	}

	function makePhysical () {
		if (@mkdir($this->path, 0755)) return $this->setPerms();
		else return false;
	}

	function setPerms () {
		$interface = remositoryInterface::getInstance();
   		$origmask = @umask(0);
		if ($interface->getCfg('dirperms')) {
	    	$mode = octdec($interface->getCfg('dirperms'));
			$result = @chmod($this->path, $mode);
		}
		else $result = @chmod($this->path,0755);
		@umask($origmask);
		return $result;
	}
	
	function isDirectory () {
		return is_dir($this->path);
	}

	function listFiles ($pattern='', $type='file', $recurse=false) {
		$results = array();
	  	if ($this->isDirectory() AND $dir = @opendir($this->path)) {
			while (false !== ($file = readdir($dir))) {
	      		if (($file == 'index.html') OR (substr($file,0,1) == '.')) continue;
	      		if (is_dir($this->path.$file)) {
					if ($recurse) {
						$nextdir = new remositoryDirectory($this->path.$file);
						$results = array_merge($results,$nextdir->listFiles($pattern, $type, true));
					}
				  	if ($type == 'file') continue;
				}
	      		elseif ($type == 'dir') continue;
				if ($pattern) {
					if (substr($file,0,strlen($pattern)) == $pattern) $results[] = $file;
				}
				else $results[] = $file;
	    	}
		  	closedir($dir);
  		}
  		return $results;
  	}
	
	function findBadExtension ($recurse) {
		$result = array();
	  	if ($this->isDirectory() AND $dir = @opendir($this->path)) {
			$repository = remositoryRepository::getInstance();
			while (false !== ($file = readdir($dir))) {
				if (substr($file,0,1) == '.') continue;
	      		if (is_dir($this->path.$file)) {
					if ($recurse) {
						$nextdir = new remositoryDirectory($this->path.$file);
						$result = array_merge($result, $nextdir->findBadExtension(true));
					}
				}
				else {
					if (!$repository->isExtensionOK($file)) $result[] = $this->path.$file;
				}
	    	}
		  	closedir($dir);
  		}
  		return $result;
	}

  	function &getOrphans ($uploads=false) {
  		$interface = remositoryInterface::getInstance();
  		$database = $interface->getDB();
		$dbpath = $database->getEscaped($this->path);
  		if ($uploads) $sql = "SELECT id, realname, realwithid FROM #__downloads_files WHERE containerid < 0";
  		else $sql = "SELECT id, realname, realwithid FROM #__downloads_files WHERE filepath = '$dbpath' AND containerid > 0";
		$repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		// Change for multiple repositories
		// $sql .= " AND repnum = $repnum";
  		$database->setQuery($sql);
  		$files = $database->loadObjectList();
  		if (!$files) $files = array();
  		$checknames = array();
  		foreach ($files as $file) {
  			if ($file->realwithid) $file->realname = remositoryPhysicalFile::basicNameWithID($file->id, $file->realname);
  			$checknames[] = $file->realname;
  		}
		$filenames = $this->listFiles();
		$orphans = array();
		foreach ($filenames as $filename) {
      		if (($filename != 'index.html') AND (substr($filename,0,1) != '.') AND (!is_dir($this->path.$filename)) AND !in_array($filename, $checknames)) {
				$orphans[] = $this->path.$filename;
			}
		}
		return $orphans;
	}

  	function getSize () {
  		$totalsize = 0;
  		$files = $this->listFiles();
  		foreach ($files as $file) $totalsize += filesize($this->path.$file);
  		return $totalsize;
  	}

    function deleteAll () {
        if (!file_exists($this->path)) return;
        $subdirs = $this->listFiles ('', 'dir');
        foreach ($subdirs as $subdir) {
            $subdirectory = new remositoryDirectory($this->path.$subdir);
            $subdirectory->deleteAll();
        }
        $files = $this->listFiles ();
        foreach ($files as $file) {
            @chmod($this->path.$file, 0644);
            @unlink($this->path.$file);
        }
        @chmod($this->path, 0755);
        @rmdir($this->path);
    }

}