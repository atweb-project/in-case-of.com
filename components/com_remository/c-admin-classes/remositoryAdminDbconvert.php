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

class remositoryAdminDbconvert extends remositoryAdminControllers {

	function __construct ($admin) {
		parent::__construct ($admin);
	    $_REQUEST['act'] = 'dbconvert';
	}
	
	function listTask () {
		$confirm = remositoryRepository::getParam($_POST, 'confirm');
		if ('confirm' != $confirm) {
			$view = $this->admin->newHTMLClassCheck ('listDbconvertHTML', $this, 0, '');
			if ($view AND $this->admin->checkCallable($view, 'view')) $view->view();
			return;
		}
		$view = new remositoryAdminHTML ($this, 0, '');
		$view->formStart(_DOWN_ADMIN_ACT_DBCONVERT);
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		foreach (array('containers','files','reviews','structure','log','temp') as $tablename) {
			$sql = "TRUNCATE TABLE #__downloads_$tablename";
			remositoryRepository::doSQL($sql);
		}
		$sql = "ALTER TABLE #__downloads_containers AUTO_INCREMENT=2";
		remositoryRepository::doSQL($sql);
		$containermap = array('catid'=>array(),'folderid'=>array());
		$sql = "SELECT * FROM #__downloads_category";
		$database->setQuery($sql);
		$rows = $database->loadObjectList();
		if (!$rows) $rows = array();
		foreach ($rows as $row) {
			if ($row->registered) $row->registered = '0';
			else $row->registered = '2';
			foreach ($row as $field=>$value) {
				if (!is_numeric($row->$field)) $row->$field = $database->getEscaped($row->$field);
			}
			$sql = "INSERT INTO #__downloads_containers (parentid,name,published,description,filecount,icon,registered) VALUES (0,'$row->name',$row->published,'$row->description',$row->files,'$row->icon',$row->registered)";
			$database->setQuery($sql);
			if (!$database->query()) {
				echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
			$newid = $database->insertid();
			$containermap['catid'][$row->id] = $newid;
			$sql = "SELECT * FROM #__downloads_folders WHERE catid=$row->id";
			$database->setQuery($sql);
			$folders = $database->loadObjectList();
			if ($folders) {
				foreach ($folders as $folder) $this->convertfolder ($folder, $newid, $containermap);
			}
		}
		$sql = "SELECT * FROM #__downloads";
		$database->setQuery($sql);
		$files = $database->loadObjectList();
		if (!$files) $files = array();
		foreach ($files as $file) {
			$testurl = strtolower(trim($file->url));
			$findsite = strpos($testurl, strtolower(trim($interface->getCfg('live_site'))));
			if ($findsite===false){
				$islocal = '0';
				$realname = '';
				$filedate = date('Y-m-d H:i:s');
				$url = $file->url;
				if (preg_match(_REMOSITORY_REGEXP_URL,$url) OR preg_match(_REMOSITORY_REGEXP_IP,$url)) $filefound = true;
				else $filefound = false;
			}
			else {
				$islocal = '1';
				$url_array=explode('/',$file->url);
				$url = '';
				$realname = $url_array[(count($url_array)-1)];
				$filepath = $this->repository->Down_Path.'/'.$realname;
				if (file_exists($filepath)) {
					$filefound = true;
					$filedate = date('Y-m-d H:i:s', filemtime($this->repository->Down_Path.'/'.$realname));
				}
				else $filefound = false;
			}
			$containerid = 0;
			if ($file->catid != 0) {
				if (isset($containermap['catid'][$file->catid])) $containerid = $containermap['catid'][$file->catid];
				else echo '<tr><td>'.$file->id.'/'.$realname.'/'.$file->catid.'</td></tr>';
			}
			if ($file->folderid != 0) {
				if (isset($containermap['folderid'][$file->folderid])) $containerid = $containermap['folderid'][$file->folderid];
				else echo '<tr><td>'.$file->id.'/'.$realname.'/'.$file->folderid.'</td></tr>';
			}
			if ($filefound AND $containerid != 0) {
				foreach (get_class_vars(get_class($file)) as $field=>$value) if (is_string($file->$field)) $file->$field = $database->getEscaped($file->$field);
				$sql="INSERT INTO #__downloads_files (realname,islocal,containerid,published,url,description,smalldesc,autoshort,license,licenseagree,filetitle,filesize,filetype,downloads,icon,fileversion,fileauthor,filedate,filehomepage,screenurl,submittedby,submitdate) VALUES ('$realname',$islocal,$containerid,$file->published,'$url','$file->description','$file->smalldesc',$file->autoshort,'$file->license',$file->licenseagree,'$file->filename','$file->filesize','$file->filetype','$file->downloads','$file->icon','$file->fileversion','$file->fileauthor','$filedate','$file->filehomepage','$file->screenurl', $file->submittedby,'$file->submitdate')";
				$database->setQuery($sql);
				if (!$database->query()) {
					echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
					exit();
				}
				$newid = $database->insertid();
				$sql = "SELECT * FROM #__downloads_comments WHERE id=$file->id";
				$database->setQuery($sql);
				$comments = $database->loadObjectList();
				if ($comments) {
					foreach ($comments as $comment) {
						$sql = "INSERT INTO #__downloads_reviews (component,itemid,userid,title,comment,date) VALUES ('com_remository',$newid,'$comment->userid','Review Title','$comment->comment','$comment->time')";
						$database->setQuery($sql);
						remositoryRepository::doSQL($sql);
					}
				}
			}
			else echo '<tr><td>'.$file->url.'</td></tr>';
		}
		$this->repository->resetCounts(array());
		echo '<tr><td class="message">'._DOWN_DB_CONVERT_OK.'</td></tr>';
		echo '</table></form>';
	}
	
	function convertfolder ($folder, $parent, &$containermap) {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		foreach ($folder as $field=>$value) {
			if (!is_numeric($folder->$field)) $folder->$field = $database->getEscaped($folder->$field);
		}
		if ($folder->registered) $folder->registered = '0';
		else $folder->registered = '2';
		$sql = "INSERT INTO #__downloads_containers (parentid,name,published,description,filecount,icon,registered) VALUES ($parent, '$folder->name', $folder->published, '$folder->description', '$folder->files', '$folder->icon', $folder->registered)";
		$database->setQuery($sql);
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$newid = $database->insertid();
		$containermap['folderid'][$folder->id] = $newid;
		$sql = "SELECT * FROM #__downloads_folders WHERE parentid=$folder->id";
		$database->setQuery($sql);
		$children = $database->loadObjectList();
		if ($children) {
			foreach ($children as $child) $this->convertfolder ($child, $newid, $containermap);
		}
	}

}