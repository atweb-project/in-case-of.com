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

class remository_savefile_Controller extends remositoryUserControllers {
    private $containerid = 0;
    
    public function savefile ($func) {
	//Process the variables
	if (_SUBMIT_FILE_BUTTON != $_POST['submit']) {
	    $view = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $this);
	    $view->noFileDiagnostics($this->remUser);
	    return;
	}
	$uploadinfo = $this->interface->triggerMambots('onRemositoryRequestUpload', $this->interface);
	$message = implode(' ', $uploadinfo);
	if (trim($message)) {
	    $viewer = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $this);
	    $viewer->uploadError($message);
	    return;
	}
	$oldid = remositoryRepository::getParam($_POST, 'oldid', 0);
	$containername = $this->interface->getEscaped(remositoryRepository::getParam($_POST, 'containername'));
	$this->containerid = remositoryRepository::getParam($_POST, 'containerid', 0);
	$container = new remositoryContainer($this->containerid);
	if ($containername) {
	    $container = $container->makeCopyAsChild($containername);
	}
	if (0 == $container->id) $this->interface->redirect('index.php?option=com_remository');
	$this->remUser->allowUploadCheck($container, $this);
	if ($this->remUser->hasAutoApprove($container)) {
	    $newfile = new remositoryFile();
	    if ($oldid) {
		    $newfile->id = $oldid;
		    $newfile->getValues($this->remUser);
		    if (!$newfile->updatePermitted($this->remUser)) {
			    $this->error_popup (_DOWN_NOT_AUTH);
			    return;
		    }
		    $oldphysical = $newfile->obtainPhysical();
		}
	    $newfile->published = '1';
	}
	else {
	    $newfile = new remositoryTempFile();
	    if ($oldid) {
		$oldfile = new remositoryFile();
		$oldfile->id = $oldid;
		$oldfile->getValues($this->remUser);
		if (!$oldfile->updatePermitted($this->remUser)) {
		    $this->error_popup (_DOWN_NOT_AUTH);
		    return;
		}
		$newfile->setValues($oldfile);
		$newfile->isblob = $newfile->plaintext = $newfile->published = 0;
	    }
	}
	$newfile->addPostData();
	$newfile->containerid = $container->id;
	$authors = remositoryFile::getPopularAuthors();
	$fileauthor = $newfile->fileauthor;
	$newfile->fileauthor = '';
	foreach ($authors as $author) if ($fileauthor == preg_replace('/[^a-zA-Z0-9]/', '-', $author)) {
	    $newfile->fileauthor = $author;
	    break;
	}
	if (!$newfile->fileauthor) $newfile->fileauthor = remositoryRepository::getParam($_POST, 'otherauthor');
	if (!$newfile->fileauthor) $newfile->fileauthor = $fileauthor;
	$newfile->validate(false);
	$newfile->memoContainer($container);
	if (!$newfile->submittedby OR !$this->remUser->isAdmin()) $newfile->submittedby = $this->remUser->id;
	if (preg_match(_REMOSITORY_REGEXP_URL,$newfile->url) OR preg_match(_REMOSITORY_REGEXP_IP,$newfile->url)) {
	    if ($newfile->filetitle == '') $newfile->filetitle = $newfile->lastPart($newfile->url,'/');
	    $newfile->metatype = 1;
	    $newfile->saveFile();
	    $upload = null;
	    if ($newfile->published) $newfile->newPublication($this->remUser->id);
	}
	else {
	    $upload = new remositoryPhysicalFile();
	    $upload->handleUpload();
	    if ($oldid AND $upload->error_message == _ERR1) {
		// If true, must be auto-approve
		if (isset($oldphysical)) {
		    if (!$oldphysical->moveTo($newfile->filepath.$newfile->realname, $newfile->id, $newfile->isblob, $newfile->plaintext, $this->repository->Real_With_ID)) $this->error_popup(_DOWN_MOVE_FILE_FAILED);
		    $newfile->realwithid = $this->repository->Real_With_ID;
		}
		$newfile->metatype = 2;
		$newfile->saveFile();
		$upload = null;
	    }
	    // In this case, a file should have been received - error condition
	    elseif ($upload->error_message) {
		$view = remositoryUserHTML::viewMaker('ErrorDisplaysHTML' ,$this);
		if (_ERR1 == $upload->error_message) $view->noFileDiagnostics($this->remUser);
		else $view->uploadError($upload->error_message);
		$upload = null;
		return;
	    }
	    // This is the case of having a new file upload
	    else {
		if ($newfile instanceof remositoryTempFile) $newfile->metatype = 1;
		if (isset($oldphysical)) $oldphysical->delete();
		if ($newfile->storePhysicalFile($upload) AND $newfile->published) $newfile->newPublication($this->remUser->id);
		$physical = $newfile->obtainPhysical();
		$this->savethumb($newfile, $physical);
	    }
	}
	$classifications = remositoryRepository::getParam($_POST, 'classification', array());
	if (!empty($classifications)) $newfile->classifyFile($classifications);
	$this->repository->resetCounts(array());
	$thumbs = new remositoryThumbnails($newfile);
	$freecount = $thumbs->getFreeCount();
	if ($freecount) {
	    for ($i = 1; $i <= $freecount; $i++) {
		if (!empty($_FILES['userfile'.(string)$i]['tmp_name'])) {
		    $thumbnail = new remositoryPhysicalFile();
		    $thumbnail->handleUpload((string) $i);
		    $thumbs->addImage($thumbnail, $newfile->id);
		}
	    }
	}

	//Send Admin notice
	if ($this->repository->Send_Sub_Mail) $this->repository->sendAdminMail($this->remUser->fullname.' ('.$this->remUser->name.')', $newfile->filetitle, $newfile->containerid, $newfile->published);
	$uploadhandlers = $this->interface->triggerMambots('remositoryUploadSaved', array($newfile, $oldid, $this->remUser));
	if (array_sum($uploadhandlers)) return;
	$view = remositoryUserHTML::viewMaker('AddFileDoneHTML', $this);
	$view->addFileDoneHTML ($newfile);
    }

    private function savethumb ($file, $upload) {
		if (remositoryRepository::getInstance()->Make_Auto_Thumbnail) {
			$thumbnails = new remositoryThumbnails($file);
			$thumbnails->addAutoThumbnail($file, $upload);
		}
    }

    public function getContainerID () {
	return $this->containerid;
    }
}
