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

class remository_fileinfo_Controller extends remositoryUserControllers {
	private $containerid = 0;
	private $fileid = 0;

	public function fileinfo ($func) {
		if ('directinfo' == $func) $func = 'showdown';
		$directlink = false;
		$interface = remositoryInterface::getInstance();
		switch ($func) {
			case 'fileinfo':
			$autodown = 0;
			break;
			case 'startdown':
			$autodown = 1;
		    break;
			case 'showdown':
			$autodown = 2;
		    break;
			case 'finishdown':
			$autodown = 1;
		}
		if ('fileinfo' != $func) {
			$noindex = <<<NO_INDEX

<meta name="robots" content="noindex, nofollow" />

NO_INDEX;

			$interface->addCustomHeadTag($noindex);
		}
	    $file = $this->createFile ();
	    if ($file->downloadForbidden($this->remUser, $message) AND !$this->repository->See_Files_no_download) {
	    	echo $message;
	    	return;
	    }
	    if ((($file->licenseagree AND $file->license) OR trim(preg_replace('/&#?[a-z0-9]+;/i','',strip_tags($this->repository->Default_Licence)))) AND ($func == 'startdown' OR ($func == 'finishdown' AND remositoryRepository::getParam($_POST, 'agreecheck', 'off') != 'on'))) {
	    	$view = remositoryUserHTML::viewMaker('DownloadAgreeHTML', $this);
	    	$view->downloadAgreeHTML($file);
			return;
		}
		if ($func == 'finishdown' AND $this->repository->wrongCheck($_POST['da'],$this->idparm,'finishdown')){
			echo _DOWN_LEECH_WARN;
			$this->remositoryHome();
			exit;
		}
		$this->fileid = $file->id;
		$this->containerid = $file->containerid;
		if ($file->windowtitle) $interface->SetPageTitle($file->windowtitle);
		else $interface->SetPageTitle($file->filetitle);
		if (remositoryRepository::getParam($_POST,'submit_vote','')) {
			$user_rating = remositoryRepository::getParam($_POST,'user_rating',0);
			if (($user_rating>=1) AND ($user_rating<=5)) {
				if (!$file->userVoted($this->remUser)) $file->addVote($this->remUser, $user_rating);
			}
		}
		$newcomment = remositoryRepository::getParam($_POST,'comment');
		if ($newcomment AND strlen($newcomment) >= $this->repository->Min_Comment_length AND !$file->userCommented($this->remUser)) {
			$thecomment = new remositoryComment();
			$thecomment->setEachValue($this->remUser->id,$this->remUser->fullname,$this->remUser->name,'Review Title',$newcomment);
			$thecomment->saveComment($file);
		}
		if ($this->repository->Scribd AND in_array($file->filetype,explode(',', $this->repository->ExtsDisplay))) {
			$displaynow = remositoryRepository::getParam($_REQUEST, 'displaynow', 2);
		}
		else $displaynow = 0;	
		$view = remositoryUserHTML::viewMaker('FileInfoHTML', $this);
		$view->fileinfoHTML($file, $autodown, $displaynow);
		// Uncomment the next two lines if using JomComment in Joomla 1.5
		// require_once($interface->getCfg('absolute_path').'/plugins/content/jom_comment_bot.php');
		// echo jomcomment($file->id, "com_remository");
	}
	
	public function getContainerID () {
		return $this->containerid;
	}

	public function getFileID () {
		return $this->fileid;
	}
}
