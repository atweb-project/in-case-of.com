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

class remositoryPhysicalFile {
	private $interface = '';
	private $database = '';
	public $error_message = '';
	public $file_path = '';
	public $proper_name = '';
	public $date = '';
	public $size = '';
	public $isblob = false;
	public $plaintext = false;
	public $fileid = 0;
	public $uploaded = false;
	public $withid = true;

	public function __construct () {
		$this->interface = remositoryInterface::getInstance();
		$this->database = $this->interface->getDB();
	}

	public function isUpload () {
		return $this->uploaded;
	}

	public function setData ($filepath, $fileid=0, $isblob=0, $plaintext=0, $withid=true) {
		$this->fileid = $fileid;
		$this->isblob = $isblob;
		$this->plaintext = $plaintext;
		$this->withid = $withid;
		$this->proper_name = preg_replace('/^.+[\\\\\\/]/', '', $filepath);
		$this->file_path = $filepath;
		if (!$isblob AND !$plaintext AND file_exists($filepath)) {
			$this->date = $this->fileDate($filepath);
			$filesize = filesize($filepath)/1024;
			$this->size = ($filesize > 1024) ? number_format($filesize/1024, 1).' MB' : number_format($filesize, 1).' KB';
		}
	}
	
	private function fileDate ($filepath) {
		$unixtime = @filemtime($filepath);
		if (0 == $unixtime) $unixtime = time();
		return date('Y-m-d H:i:s', $unixtime);
	}

	// Appears not used
	function setFileID ($id) {
		$this->fileid = $id;
	}

	public function exists () {
		$path = $this->realFilePath();
		clearstatcache();
	    return file_exists($path);
	}

	public function setPerms () {
		$interface = remositoryInterface::getInstance();
		if (!$this->uploaded AND $this->file_path) {
   			$origmask = @umask(0);
			if ($interface->getCfg('fileperms')) {
	    		$mode = octdec($interface->getCfg('fileperms'));
	    		$result = @chmod($this->file_path, $mode);
			}
			else $result = @chmod($this->file_path,0644);
			@umask($origmask);
		}
		else $result = true;
		return $result;
	}

	public function delete () {
		if ($this->isblob) $sql = "DELETE FROM #__downloads_blob WHERE fileid = $this->fileid";
		elseif ($this->plaintext) $sql = "DELETE FROM #__downloads_text WHERE fileid = $this->fileid";
		elseif ($this->file_path) {
			$path = $this->realFilePath();
			@unlink($path);
		}
		if (isset($sql)) {
			remositoryRepository::doSQL($sql);
		}
	}

	public function handleUpload ($suffix='') {
		$key = 'userfile'.$suffix;
		if (!isset($_FILES[$key]) OR $_FILES[$key]['tmp_name']=='none' OR $_FILES[$key]['tmp_name']==''){
			$this->error_message =_ERR1;
			return;
		}
		if ($_FILES[$key]['error']) {
		    $this->error_message = _ERR11;
			return;
		}
		$this->proper_name = remositoryRepository::getParam($_FILES[$key], 'name');
		if ($_FILES[$key]['size'] == 0) {
		    $this->error_message = _ERR3;
		    return;
		}
		$this->size = remositoryRepository::getParam($_FILES[$key], 'size', 1024)/1024;
		$repository = remositoryRepository::getInstance();
		if($this->size > $repository->MaxSize) {
	    	$this->error_message =  _ERR5.$repository->MaxSize.' Kb';
	    	return;
	    }
	    $this->size = number_format($this->size,2).' Kb';
		if (!is_uploaded_file($_FILES[$key]['tmp_name'])) {
		    $this->error_message = _ERR2;
		    return;
	    }
	    else $this->file_path = $_FILES[$key]['tmp_name'];
		if (ini_get('safe_mode')) $this->date = date('Y-m-d H:i:s');
		else $this->date = $this->fileDate($this->file_path);
		$this->uploaded = true;
		$this->withid = false;
	}

	// Not sure this is used anywhere
	function getExtension () {
		return end(explode('.', $this->proper_name));
	}

	public function antiLeech () {
		$repository = remositoryRepository::getInstance();
	    if ($repository->Anti_Leach){
	    	$this->proper_name = substr(md5($this->interface->getCfg('absolute_path')),0,8).$this->proper_name;
	    }
	}

	public function makeUploadSafe () {
		if ($this->uploaded AND ini_get('safe_mode')) {
			$repository = remositoryRepository::getInstance();
			$newfile = $repository->Up_Path.'/'.time().$this->proper_name;
			move_uploaded_file($this->file_path, $this->makeNameWithID($newfile));
			$this->file_path = $newfile;
			$this->withid = true;
			$this->uploaded = false;
		}
	}

	public function realFilePath () {
		return $this->withid ? $this->makeNameWithID($this->file_path) : $this->file_path;
	}

	// Must be public for the sake of custom plugin made for clients
	public function makeNameWithID ($filename) {
		return remositoryPhysicalFile::basicNameWithID($this->fileid, $filename);
	}

	public static function basicNameWithID ($id, $name) {
		$elements = explode ('.', $name);
		if (1 < count($elements)) $extension = array_pop($elements);
		else $extension = '';
		array_push ($elements, (string) $id);
		if ($extension) array_push ($elements, $extension);
		return implode('.', $elements);
	}

	public function moveTo ($filepath, $fileid, $isblob, $plaintext, $withid) {
		if ($this->isblob) {
			if ($isblob) return $this->blobToblob($fileid);
			elseif ($plaintext) return $this->blobToText($fileid);
			else {
				$repository = remositoryRepository::getInstance();
				if (!$repository->isExtensionOK($filepath)) return false;
				$this->file_path = $filepath;
				return $this->blobToFile($filepath, $fileid, $withid);
			}
		}
		elseif ($this->plaintext) {
			if ($isblob) return $this->textToBlob($fileid);
			elseif ($plaintext) return $this->textToText($fileid);
			else {
				$repository = remositoryRepository::getInstance();
				if (!$repository->isExtensionOK($filepath)) return false;
				$this->file_path = $filepath;
				return $this->textToFile($filepath, $fileid, $withid);
			}
		}
		else {
			if ($isblob) return $this->fileToBlob($fileid);
			elseif ($plaintext) return $this->fileToText($fileid);
			else return $this->fileToFile($filepath, $fileid, $withid);
		}
	}

	public function copyToFileSystem ($filepath) {

	    if ($this->isblob) return $this->blobToFile($filepath, 0, false, false);
	    elseif ($this->plaintext) return $this->textToFile($filepath, 0, false, false);
	    else return $this->fileToFile ($filepath, 0, false, false);
	}

	public function fileToFile ($filepath, $fileid, $withid, $delete=true) {
		$destination = $withid ? remositoryPhysicalFile::basicNameWithID($fileid, $filepath) : $filepath;
		$result = false;
		if ($this->uploaded) {
			if (move_uploaded_file($this->file_path, $destination)) {
				$this->uploaded = false;
				$result = true;
			}
		}
		else {
			$source = $this->realFilePath();
			if ($source != $destination) {
				clearstatcache();
				if (!file_exists($source)) {
					// If no source, but destination exists, assume all is ok
					if (file_exists($destination)) return true;
					return false;
				}
				if ($delete) $result = rename ($source, $destination);
				if (empty($result)) $result = copy($source, $destination);
				if ($delete AND $result) @unlink($source);
			}
			// Or nothing to do
			else $result = true;
		}
		if ($result) {
			if ($delete) {
				$this->file_path = $filepath;
				$this->withid = $withid;
				$this->fileid = $fileid;
			}
			$this->setPerms();
		}
		return $result;
	}


	private function fileToBlob ($fileid) {
		$this->makeUploadSafe();
		$source = $this->realFilePath();
		if ($fileid AND $f = fopen($source,'rb')) {
			$sql = "DELETE FROM #__downloads_blob WHERE fileid=$fileid";
			remositoryRepository::doSQL($sql);
			$chunkid = 0;
			$sql = "INSERT INTO #__downloads_blob (fileid, chunkid, datachunk, bloblength) VALUES ($fileid, ";
			while($f AND !feof($f)) {
				$chunk = fread($f, 60000);
				$chunk = $this->interface->getEscaped($chunk);
				remositoryRepository::doSQL($sql."$chunkid, '$chunk', LENGTH(datachunk))");
				$chunkid++;
			}
			fclose($f);
			$sql = "UPDATE #__downloads_files SET chunkcount=$chunkid WHERE id=$fileid";
			remositoryRepository::doSQL($sql);
			@unlink($source);
			$this->fileid = $fileid;
			return true;
		}
		else {
			return false;
		}
	}

	private function fileToText ($fileid) {
		$this->makeUploadSafe();
		$source = $this->realFilePath();
		if ($fileid AND $f = @fopen($source,'rb')) {
			$sql = "DELETE FROM #__downloads_text WHERE fileid=$fileid";
			remositoryRepository::doSQL($sql);
			$sql = "INSERT INTO #__downloads_text (fileid, filetext) VALUES ($fileid, '";
			while($f AND !feof($f)) {
				$chunk = fread($f, 65535);
				$chunk = $this->interface->getEscaped($chunk);
				$sql .= $chunk;
			}
			fclose($f);
			remositoryRepository::doSQL($sql."')");
			@unlink($source);
			$this->fileid = $fileid;
			return true;
		}
		else return false;
	}
	
	private function blobToBlob ($fileid) {
		if ($fileid AND $this->fileid AND $fileid != $this->fileid) {
			remositoryRepository::doSQL("UPDATE #__downloads_blob SET fileid = $fileid WHERE fileid = $this->fileid");
			$this->fileid = $fileid;
		}
		return true;
	}

	private function blobToFile ($filepath, $fileid, $withid, $delete=true) {
		$result = false;
		$destination = $withid ? remositoryPhysicalFile::basicNameWithID($fileid, $filepath) : $filepath;
		if (!file_exists($destination) AND $f = @fopen($destination, 'wb')) {
			$sql = "SELECT chunkid FROM #__downloads_blob WHERE fileid=$this->fileid ORDER BY chunkid";
			$this->database->setQuery($sql);
			$chunks = $this->database->loadResultArray();
			if ($chunks) foreach ($chunks as $chunkid) {
				$sql = "SELECT datachunk FROM #__downloads_blob WHERE fileid=$this->fileid AND chunkid=$chunkid";
				$this->database->setQuery($sql);
				$datachunk = $this->database->loadResult();
				if (fwrite ($f, $datachunk)) $result = true;
			}
			fclose($f);
			if ($result) {
				if ($delete) remositoryRepository::doSQL("DELETE FROM #__downloads_blob WHERE fileid=$this->fileid");
				$this->setPerms ();
				$this->fileid = $fileid;
			}
		}
		return $result;
	}

	private function textToText ($fileid) {
		if ($fileid AND $this->fileid AND $fileid != $this->fileid) {
			remositoryRepository::doSQL("UPDATE #__downloads_text SET fileid = $fileid WHERE fileid = $this->fileid");
			$this->fileid = $fileid;
		}
		return true;
	}

	private function textToFile ($filepath, $fileid, $withid, $delete=true) {
		$result = false;
		$destination = $withid ? remositoryPhysicalFile::basicNameWithID($fileid, $filepath) : $filepath;
		if (!file_exists($destination) AND $f = @fopen($destination,'wb')) {
			$sql = "SELECT filetext FROM #__downloads_text WHERE fileid=$this->fileid";
			$this->database->setQuery($sql);
			$text = $this->database->loadResult();
			if ($text AND fwrite ($f, $text)) $result = true;
			fclose($f);
			if ($result) {
				if ($delete) remositoryRepository::doSQL("DELETE FROM #__downloads_text WHERE fileid=$this->fileid");
				$this->setPerms ();
				$this->fileid = $fileid;
			}
		}
		return $result;
	}

	private function blobToText ($fileid) {
		remositoryRepository::doSQL("DELETE FROM #__downloads_text WHERE fileid=$fileid");
		$sql = "SELECT chunkid FROM #__downloads_blob WHERE fileid=$this->fileid ORDER BY chunkid";
		$this->database->setQuery($sql);
		$chunks = $this->database->loadResultArray();
		$isql = "INSERT INTO #__downloads_text (fileid, filetext) VALUES ($fileid, '";
		if ($chunks) foreach ($chunks as $chunkid) {
			$sql = "SELECT datachunk FROM #__downloads_blob WHERE fileid=$this->fileid AND chunkid=$chunkid";
			$this->database->setQuery($sql);
			$isql .= $this->interface->getEscaped($this->database->loadResult());
		}
		remositoryRepository::doSQL($isql."')");
		remositoryRepository::doSQL("DELETE FROM #__downloads_blob WHERE fileid=$this->fileid");
		$this->fileid = $fileid;
		return true;
	}

	private function textToBlob ($fileid) {
		$this->database->setQuery("SELECT filetext FROM #__downloads_text WHERE fileid=$this->fileid");
		$text = $this->database->loadResult();
		$sql = "DELETE FROM #__downloads_blob WHERE fileid=$fileid";
		remositoryRepository::doSQL($sql);
		$chunkid = 0;
		$sql = "INSERT INTO #__downloads_blob (fileid, chunkid, datachunk, bloblength) VALUES ($fileid, ";
		while ($chunkid*60000 < strlen($text)) {
			$chunk = substr($text,$chunkid*60000,60000);
			$chunk = $this->interface->getEscaped($chunk);
			remositoryRepository::doSQL($sql."$chunkid, '$chunk', LENGTH(datachunk))");
			$chunkid++;
		}
		remositoryRepository::doSQL("DELETE FROM #__downloads_text WHERE fileid=$this->fileid");
		$this->fileid = $fileid;
		return true;
	}

}