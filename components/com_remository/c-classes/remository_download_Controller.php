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

class remository_download_Controller extends remositoryUserControllers {

	public function download ($func) {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$mosConfig_live_site = $interface->getCfg('live_site');
		$ua = remositoryRepository::getParam($_SERVER, 'HTTP_USER_AGENT');
		$scribd = (false !== strpos($ua, 'Scribdbot') AND false !== strpos($ua, 'http://www.scribd.com'));
		if (!isset($_GET['chk']) OR $this->repository->wrongCheck($_GET['chk'],$this->idparm,'download')){
			if (_REMOSITORY_CHECKFAIL_FILE) $this->idparm = _REMOSITORY_CHECKFAIL_FILE;
			else die('Illegal download attempt');
		}
		// Following two lines needed for com_remository_startdown operation
		// $chk = $this->repository->makeCheck($this->idparm,'startdown');
		// header("Location: $mosConfig_live_site/components/com_remository/com_remository_startdown.php?id=$this->idparm&chk=$chk&userid={$this->remUser->id}");
		// Rest of code is former com_remository_startdown

		$repository = remositoryRepository::getInstance();
		$Small_Text_Len = $repository->Small_Text_Len;
		$Large_Text_Len = $repository->Large_Text_Len;
		$id = $this->idparm;
		$fileinfo = $this->createFile();
		$rheaders = $repository->apache_request_headers();
		if (isset($rheaders['If-Modified-Since'])) {
	        $headdate = $rheaders['If-Modified-Since'];
	        if (strtotime($headdate) >= strtotime($fileinfo->filedate)) {
                header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
                exit;
		    }
		}
		$message = '';
		if (!$scribd AND $fileinfo->id AND $fileinfo->downloadForbidden($this->remUser, $message)) {
			die($message);
		}
		if (_REMOSITORY_USE_CREDITS AND $this->remUser->creditsAvailable() < $fileinfo->price) {
			die(_DOWN_TOO_FEW_CREDITS);
		}
		// Integration with AEC
		$aec_code = $interface->getCfg('absolute_path').'/components/com_acctexp/micro_integration/mi_remository.php';
		if ($repository->Activate_AEC AND !$scribd AND file_exists($aec_code)) {
			require_once($aec_code);
			$restrictionhandler = new remository_restriction( $database );
			$restrict_id = $restrictionhandler->getIDbyUserID( $this->remUser->id );
			$restrictionhandler->load( $restrict_id );
			if (!$restrictionhandler->hasDownloadsLeft()) $interface->redirect('index.php?option=com_remository&repnum='.$this->repnum, _DOWN_AEC_REFUSED);
			else $restrictionhandler->useDownload();
		}
		// End of AEC integration
		if ($fileinfo->id) $this->deliverFile($fileinfo, $repository, $interface, $this->remUser);
	}
	
	public function deliverFile ($fileinfo, $repository, $interface, $user) {
		clearstatcache();
		$nofile = $fileinfo->id ? false : true;
		$database = $interface->getDB();
		$downpath = false;
		$id = $fileinfo->id;
		$len = 0;
		if ($fileinfo->islocal) {
			if ($fileinfo->filepath != '') {
				$downpath = $fileinfo->filePath();
				$downpath = html_entity_decode($downpath,ENT_QUOTES);
				if(!file_exists($downpath)) $nofile = true;
				else $len = filesize($downpath);
			}
			elseif ($fileinfo->plaintext) {
				$sql = "SELECT LENGTH(filetext) FROM #__downloads_text WHERE fileid=$id";
				$database->setQuery($sql);
				$len = $database->loadResult();
			}
			elseif ($fileinfo->isblob) {
				$sql = "SELECT SUM(bloblength) FROM #__downloads_blob WHERE fileid=$id GROUP BY fileid";
				$database->setQuery($sql);
				$len = $database->loadResult();
				//		echo 'Length of file from DB='.$len;
				//		$sql = "SELECT datachunk FROM #__downloads_blob WHERE fileid=$id AND chunkid=0";
				//		$database->setQuery($sql);
				//		$datachunk = $database->loadResult();
				//		echo 'Beginning of data from DB='.substr($datachunk,0,20);
			}
			else {
				$downpath = $repository->Down_Path.'/'.$fileinfo->realname;
				$downpath = html_entity_decode($downpath,ENT_QUOTES);
				if(!file_exists($downpath)) $nofile = true;
				else $len = filesize($downpath);
			}
			$displayname = $fileinfo->realname;
			//this fixes the single quotes (apostrophes)
			$displayname = html_entity_decode($displayname,ENT_QUOTES);
			$file_extension = remositoryAbstract::lastPart($displayname, '.');
		}
		if ($nofile) {
			echo _DOWN_FILE_NOTFOUND;
			$this->remositoryHome();
			exit;
		}
		while (@ob_end_clean());

		if (!$fileinfo->islocal) {
			$this->logDownload ($id, $user, $database, 0, 0);
			$interface->triggerMambots('remositoryDoneDownload', array($fileinfo, $len, $user, $repository, $database));
			$slimurl = trim($fileinfo->url);
			header("Location:$slimurl");
			exit;
		}

		$ctype = $this->setCtype($file_extension, $downpath);

		$offset = $origoffset = $this->rangeHandler($len);
		$this->writeHeaders($ctype, $displayname, $fileinfo, $repository);
		$sent = 0;

		if ($fileinfo->plaintext) {
			$sql = "SELECT filetext FROM #__downloads_text WHERE fileid=$id";
			$database->setQuery($sql);
			$filetext = $database->loadResult();
			echo substr($filetext, $offset);
			$sent += strlen($filetext) - $offset;
			flush();
		}
		elseif ($fileinfo->isblob) {
			$database->setQuery('SET NAMES utf8');
			$database->query();
			$database->setQuery('SET CHARACTER SET utf8');
			$database->query();
			$datachunk = true;
			$sql = "SELECT chunkid, bloblength FROM #__downloads_blob WHERE fileid=$id ORDER BY chunkid";
			$database->setQuery($sql);
			$chunks = $database->loadObjectList();
			if ($chunks) foreach ($chunks as $chunk) {
				@set_time_limit(0);
				if ($offset >= $chunk->bloblength) {
					$offset -= $chunk->bloblength;
					continue;
				}
				$sql = "SELECT datachunk FROM #__downloads_blob WHERE fileid=$id AND chunkid=$chunk->chunkid";
				$database->setQuery($sql);
				$datachunk = $database->loadResult();
				echo $offset ? substr($datachunk, $offset) : $datachunk;
				$sent += ($offset ? strlen($datachunk) - $offset : strlen($datachunk));
				$offset = 0;
				flush();
				usleep(0.25*1000000);
			}
		}
		else {
			$fp = @fopen($downpath, "rb");
			$mqr = ini_get('magic_quotes_runtime');
			ini_set('magic_quotes_runtime', 0);
			$chunksize = 1*(64*1024); // how many bytes per chunk
			if ($offset) fseek($fp, $offset);
			while($fp && !feof($fp)) {
				@set_time_limit(0);
				$data = fread($fp, $chunksize);
				print ($data);
				$sent += strlen($data);
				flush();
				if (!feof($fp)) usleep(0.25*1000000);
			}
			ini_set('magic_quotes_runtime', $mqr);
			fclose($fp);
		}
		$interface->triggerMambots('remositoryDoneDownload', array($fileinfo, $len, $user, $repository, $database));
		if (!$origoffset) {
			if (_REMOSITORY_USE_CREDITS AND $fileinfo->price AND !$user->userHasPaidForFile($fileinfo)) {
				$user->chargeCreditsForFile($fileinfo);
				$pricepaid = $fileinfo->price;
			}
			else $pricepaid = 0;
			$this->logDownload ($id, $user, $database, $sent, $pricepaid);
		}
		exit;
	}

	function logDownload ($id, $user, $database, $sent, $pricepaid) {
		$sql = "UPDATE #__downloads_files SET downloads=downloads+1 WHERE id = $id";
		$database->setQuery( $sql );
		$database->query();
		$type = $user->isAdmin() ? _REM_LOG_ADMIN_DOWNLOAD : _REM_LOG_DOWNLOAD;
		$logentry = new remositoryLogEntry($type, $user->id, $id, $sent, $pricepaid);
		$logentry->insertEntry();
	}

	function setCtype ($file_extension, $downpath) {
		//This will set the Content-Type to the appropriate setting for the file
		switch( $file_extension ) {
			 case "pdf": $ctype="application/pdf"; break;
		     case "exe": $ctype="application/octet-stream"; break;
		     case "zip": $ctype="application/zip"; break;
		     case "doc": $ctype="application/msword"; break;
		     case "xls": $ctype="application/vnd.ms-excel"; break;
		     case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
		     case "gif": $ctype="image/gif"; break;
		     case "png": $ctype="image/png"; break;
		     case "jpeg":
		     case "jpg": $ctype="image/jpg"; break;
		     case "mp3": $ctype="audio/mpeg"; break;
		     case "m4a": $ctype="audio/aacp"; break;
		     case "wav": $ctype="audio/x-wav"; break;
		     case "mpeg":
		     case "mpg":
		     case "mpe": $ctype="video/mpeg"; break;
		     case "mov": $ctype="video/quicktime"; break;
		     case "avi": $ctype="video/x-msvideo"; break;

		     //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
		     case "php":
		     case "htm":
		     case "html": if ($downpath) die("<b>Cannot be used for ". $file_extension ." files!</b>");

		     default: $ctype="application/force-download";
		}
		return $ctype;
	}
	
	function docman () {
		header('Content-Disposition:' . $cont_dis .';'
			. ' filename="' . $this->name . '";'
			. ' modification-date="' . $mod_date . '";'
			. ' size=' . $fsize .';'
			); //RFC2183
        header("Content-Type: "    . $this->mime );			// MIME type
        header("Content-Length: "  . $fsize);
	}

	function writeHeaders ($ctype, $displayname, $fileinfo, $repository) {
		// Do IE specific things
		if (isset($_SERVER['HTTP_USER_AGENT']) AND 
	    (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) {
	    	$displayname = urlencode($displayname);
	    }
	    else $displayname = str_replace(' ', '+', $displayname);
	    
	    // Suppress output compression, can be harmful
		if (ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');
	    
		//Use the switch-generated Content-Type
		header("Content-Type: $ctype");

		//Begin writing other headers
        // header("Pragma: public");
		// header("Cache-Control: no-cache");
		header("Expires: -1");

		//Force the download
		header("Content-Disposition: attachment; filename=\"$displayname\"");
		header("Content-Transfer-Encoding: binary");
		// Length header is now sent by the rangeHandler method
		// if ($len) header("Content-Length: ".$len);
	}
	
	function rangeHandler ($size) {
		if (!empty($_SERVER['HTTP_RANGE'])) {
			$regex = '/^bytes=([0-9]*)\-([0-9]*)/';
			preg_match($regex, $_SERVER['HTTP_RANGE'], $matches);
		}
		$seek_end = (empty($matches[2])) ? ($size - 1) : min((integer) $matches[2] ,($size - 1));
		$seek_start = (empty($matches[1]) OR $seek_end < (integer) $matches[1]) ? 0 : max((integer) $matches[1],0);
		$partial = ($seek_start > 0 OR $seek_end < ($size - 1));
		if ($partial) header($_SERVER['SERVER_PROTOCOL'].' 206 Partial Content');
		header('Accept-Ranges: bytes');
		if ($partial) header("Content-Range: bytes $seek_start-$seek_end/$size");
		header('Content-Length: '.($seek_end - $seek_start + 1));
		return $seek_start;
	}

}
