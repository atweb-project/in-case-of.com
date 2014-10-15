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

class remositoryFile extends remositoryAbstract {
	public static $flist = '';

	/** @var int Repository number */
	public $repnum = 1;
	/** @var int Type of metadata - 0 = Standard file, 1 = File awaiting approval, 2 = Metadata only awaiting approval */
	public $metatype = 0;
	/** @var string File name on disk or as blob */
	public $realname='';
	/** @var bool Is the name in the file system supplemented by the id? */
	public $realwithid = 0;
	/** @var bool Is the file in the local file system? */
	public $islocal='0';
	/** @var int Container ID */
	public $containerid=0;
	/** @var string File path if non-standard, derived from container */
	public $filepath='';
	/** @var string File size */
	public $filesize='';
	/** @var string File extension */
	public $filetype='';
	/** @var string File Title for browser title bar */
	public $filetitle='';
	/** @var string File Subtitle */
	public $subtitle='';
	/** @var string File description */
	public $description='';
	/** @var string Short file description */
	public $smalldesc='';
	/** @var bool Is the short description automatically derived from the full description? */
	public $autoshort='';
	/** @var string License conditions for the file */
	public $license='';
	/** @var bool Does the user have to confirm the license conditions? */
	public $licenseagree='0';
	/** @var int Price in currency units with two decimal places */
	public $price=0;
	/** @var string Currency code e.g. GBP */
	public $currency='';
	/** @var int File download count */
	public $downloads=0;
	/** @var int File number of times appeared in a list */
	public $listings=0;
	/** @var int File number of times detailed info viewed */
	public $viewings=0;
	/** @var string URL to the file, if it is held elsewhere */
	public $url='';
	/** @var string Icon - not sure how this is used */
	public $icon='';
	/** @var bool Is this file published? */
	public $published=false;
	/** @var date Publish from date */
	public $publish_from = '';
	/** @var date Publish to date */
	public $publish_to = '';
	/** @var int Republish counter */
	public $republish_num = 0;
	/** @var int Republish unit, 0 = days, 1 = weeks, 2 = months */
	public $republish_unit = 1;
	/** @var bool Is this file confined to registered users? */
	public $registered='2';
	/** @var User options 1=upload, 2=download, 3=both */
	public $userupload='3';
	/** @var bool Is this file recommended? */
	public $recommended=false;
	/** @var string Description of why recommended */
	public $recommend_text='';
	/** @var bool Is this file featured? */
	public $featured=false;
	/** @var date Start date for feature */
	public $featured_st_date='';
	/** @var date End date for feature */
	public $featured_end_date='';
	/** @var int Priority among featured files */
	public $featured_priority=0;
	/** @var int Sequencing number (calculated) */
	public $featured_seq=0;
	/** @var text Discussion of featured file */
	public $featured_text='';
	/** @var string Operating system for which file is intended */
	public $opsystem='';
	/** @var string Legal type - shareware, freeware, commercial, etc */
	public $legaltype='';
	/** @var text Requirements - what is the environment for running this file? */
	public $requirements='';
	/** @var Company name owning file */
	public $company='';
	/** @var date Release date */
	public $releasedate='';
	/** @var text Languages supported */
	public $languages='';
	/** @var string Company URL */
	public $company_URL='';
	/** @var string Translator name */
	public $translator='';
	/** @var string Version of this file */
	public $fileversion='';
	/** @var string Name of the author of the file */
	public $fileauthor='';
	/** @var string URL for web site of author of file */
	public $author_URL='';
	/** @var string URL for email address of author of file */
	public $author_email='';
	/** @var string Publisher ID */
	public $publish_id='';
	/** @var string Publish Date */
	public $publish_date='';
	/** @var date The last modified date for the file */
	public $filedate='';
	/** @var string Home page related to this file (URL) */
	public $filehomepage='';
	/** @var string Link to some kind of image referring to the file */
	public $screenurl='';
	/** @var bool Is this file in plain text? */
	public $plaintext=false;
	/** @var bool Is this file held in the database as a blob? */
	public $isblob=false;
	/** @var int Number of chunks for a file stored as blob in DB */
	public $chunkcount = 0;
	/** @var int Group of users that has access to this file */
	public $groupid=0;
	/** @var int Group of users who may edit this file */
	public $editgroup=0;
	/** @var string Information to be displayed during download */
	public $download_text = '';
	/** @var int The ID of the user who submitted this file */
	public $submittedby=0;
	/** @var date Date on which the file was submitted */
	public $submitdate='';
	/** @var string Custom field 1 */
	public $custom_1 = '';
	/** @var string Custom field 2 */
	public $custom_2 = '';
	/** @var string Custom field 3 */
	public $custom_3 = '';
	/** @var string Custom field 4 */
	public $custom_4 = 0;
	/** @var time stamp Custom field 5 */
	public $custom_5 = '';
	/** @var int original ID for resubmitted file */
	public $oldid=0;
	/** @var int Average rating of votes for this file */
	public $vote_value=0;
	/** @var int Count of votes for this file */
	public $vote_count=0;
	/** @var bool Active Feature - this file is currently featured */
	public $active_feature=0;
	/** @var string Custom field names and values, serialized */
	public $custom_values='';

	/** @var string Count of comments for this file */
	public $comment_count=0;
	/** @var string Count logged downloads over some period */
	public $logdownloads=0;
	/** @var string name of container */
	public $name='';

	/**
	* File object constructor
	* @param int File ID from database or null
	*/
	public function remositoryFile ( $id=0 ) {
		$repository = remositoryRepository::getInstance();
		$this->id = $id;
		$this->fileversion = $repository->Default_Version;
		$database = remositoryInterface::getInstance()->getDB();
		$database->setQuery("SELECT NOW()");
		// $this->publish_from = date('Y-m-d H:i:s');
		$this->publish_from = $database->loadResult();
	}

	public function realName () {
		if ($this->islocal) return $this->realname;
		else return '';
	}

	public function url () {
		if ($this->islocal) return '';
		else return $this->url;
	}

	public function forceBools () {
		if ($this->published) $this->published=1;
		else $this->published=0;
		if ($this->licenseagree) $this->licenseagree=1;
		else $this->licenseagree=0;
		if ($this->autoshort) {
			$this->autoshort=1;
			$this->smalldesc='';
		} else $this->autoshort=0;
	}

	public function notSQL () {
		return array ('id','vote_value', 'vote_count','submitdate','active_feature', 'flist', 'comment_count', 'logdownloads', 'name');
	}

	public function tableName () {
		return '#__downloads_files';
	}

	public function timeStampField () {
		return 'submitdate';
	}

	public function stripTagsFields () {
		return array ('smalldesc', 'keywords', 'filetitle', 'license', 'windowtitle', 'requirements', 'company',
		  'languages', 'company_URL', 'translator', 'fileversion', 'fileauthor', 'author_URL', 'filehomepage',
		  'screenurl');
	}

	public function stripTags () {
		$fields = $this->stripTagsFields();
		foreach ($fields as $field) {
			$this->$field = strip_tags($this->$field);
		}
	}

	public function validate () {
		$this->stripTags();
		$this->forceBools();
		$this->makeAutoshort();
		$this->checkLicenseagree();
		$interface = remositoryInterface::getInstance();
		$interface->triggerMambots('remositoryValidateFile', array($this));
	}

	public function is_av () {
		return ($this->islocal AND ($this->is_audio() OR $this->is_video())) ? true : false;
	}

	public function is_audio () {
		$audio_exts = remositoryRepository::getInstance()->ExtsAudio;
		return ($audio_exts AND $this->islocal) ? in_array($this->filetype, explode(',', $audio_exts)) : false;
	}

	public function is_video () {
		$video_exts = remositoryRepository::getInstance()->ExtsVideo;
		return ($video_exts AND $this->islocal) ? in_array($this->filetype, explode(',', $video_exts)) : false;
	}

	public function getPlainText () {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$sql = "SELECT filetext FROM #__downloads_text WHERE fileid=$this->id";
		$database->setQuery($sql);
		return $database->loadResult();
	}

	public function addPostData ($adminside=false) {
		// Clear all tick boxes - will be sent by POST data if and only if tick is present
		$this->autoshort = 0;
		$this->licenseagree = 0;
		if ($adminside) {
			$this->published = 0;
			$this->featured = 0;
			$this->recommended = 0;
		}
		parent::addPostData();
	}

	public function insertFileDB () {
		if (0 == $this->containerid) return;
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		remositoryRepository::doSQL($this->insertSQL());
		$this->id = $database->insertid();
		if ($this->published) $this->incrementCounts('+1');
	}

	public function saveFile () {
		$default_icon = array (
		'txt' => 'document.gif',
		'exe' => 'executable.gif',
		'tar' => 'archive_tar.gif',
		'gz' => 'archive_gz.gif',
		'rar' => 'archive_rar.gif',
		'zip' => 'archive_zip.gif',
		'png' => 'pics.gif',
		'gif' => 'pics.gif',
		'jpg' => 'pics.gif',
		'pdf' => 'pdf1.gif',
		'doc' => 'word.gif',
		'rtf' => 'word.gif',
		'xls' => 'excel.gif'
		);
		if ($this->islocal) {
			$this->filetype = $this->lastPart($this->realname, '.');
			if (!$this->icon) {
				if (isset($default_icon [$this->filetype])) $this->icon = $default_icon [$this->filetype];
				else $this->icon = 'stuff1.gif';
			}
		}
		$this->metatype = 0;
		$this->oldid = 0;
		if ($this->id == 0) $this->insertFileDB();
		else $this->updateObjectDB();
	}

	public function obtainPhysical () {
		$physical = new remositoryPhysicalFile();
		$physical->setData($this->filepath.$this->realname, $this->id, $this->isblob, $this->plaintext, $this->realwithid);
		return $physical;
	}

	public function storePhysicalFile ($physical, $extensiontitle=true, $checkExt=true) {
		$this->url = '';
	  $this->islocal = '1';
		$this->filetype = $this->getExtension();
		if ($this->filetitle == '') {
			$nicetitle = str_replace('_', ' ', $physical->proper_name);
			if ($extensiontitle) $this->filetitle = $nicetitle;
			else $this->filetitle = remositoryRepository::allButLast($nicetitle, '.');
		}
		if ($this->filepath) {
			if ($this->onDiskCheckFail($physical, $checkExt)) {
				echo "<script> alert('"._ERR6."'); window.history.go(-1); </script>\n";
				exit;
			}
			if ($checkExt AND !remositoryRepository::getInstance()->isExtensionOK($this->realname)) {
				echo "<script> alert('"._ERR4."'); window.history.go(-1); </script>\n";
				exit;
			}
		}
		else {
			if (!$this->plaintext) $this->isblob = 1;
			$this->getPhysicalData($physical);
		}
		$withid = remositoryRepository::getInstance()->Real_With_ID;
		$this->realwithid = $withid;
		$this->saveFile();
		$newphysical = $this->obtainPhysical();
		return $physical->moveTo($newphysical->file_path, $this->id, $newphysical->isblob, $newphysical->plaintext, $withid);
	}

	public function newPublication ($userid) {
		$interface = remositoryInterface::getInstance();
		$filesize = $this->islocal ? $this->filesize : 0;
		$logentry = new remositoryLogEntry(_REM_LOG_UPLOAD, $userid, $this->id, $filesize);
		$logentry->insertEntry();
		if (array_sum($interface->triggerMambots('remositoryNewFilePublication', $this))) return;
		if (_REMOSITORY_EMAIL_ACCESSORS) {
			$message = _DOWN_MAIL_MESSAGE_PREFIX.sprintf(_DOWN_NEW_UPDATED_MSG, $interface->getCfg('sitename'), $this->filetitle, $this->realname);
			remositoryUser::mailPeopleViewingContainer($this->containerid, _DOWN_NEW_OR_UPDATED.' : '.$this->filetitle, $message, $this);
		}
	}

	public function downloadURL ($autodown) {
	  if ($autodown) $function = 'download';
		else $function = 'showdown';
		$repository = remositoryRepository::getInstance();
		$downURL = $repository->RemositoryBasicFunctionURL($function,$this->id);
		$downURL = "'".str_replace('&amp;','&',$downURL)."'";
		return $downURL;
	}

	public function basicDownloadLink ($autodown) {
	  if ($autodown) $function = 'download';
		else $function = 'startdown';
		if ($this->islocal AND $autodown) $fname = $this->realname;
		else $fname = null;
		$repository = remositoryRepository::getInstance();
		$downlink = $repository->RemositoryBasicFunctionURL($function,$this->id, null, null, null, $fname);
		return $downlink;
	}

	public function downloadLink ($autodown) {
		$downURL = $this->downloadURL ($autodown);
		if ($this->islocal) $addon = ' rel="nofollow">';
		elseif ($autodown == 2) $addon = ' target="_blank" rel="nofollow">';
		else $addon = ' onclick="download('.$downURL.')" rel="nofollow" target="_blank">';
		$downlink = $this->basicDownloadLink($autodown);
		$downlink = '<a href="'.$downlink.'"'.$addon;
		return $downlink;
	}

	public function cloneFile () {
		$this->id = 0;
		$this->insertFileDB();
	}

	public function deleteFileDB () {
		$sql = "DELETE FROM #__downloads_files WHERE id=$this->id";
		remositoryRepository::doSQL($sql);
		remositoryComment::deleteComments($this->id);
		remositoryLogEntry::deleteEntries($this->id);
		if ($this->published) $this->incrementCounts('-1');
	}

	public function nameWithID () {
		if ($this->realwithid) {
			$elements = explode ('.', $this->realname);
			if (1 < count($elements)) $extension = array_pop($elements);
			else $extension = '';
			array_push ($elements, (string) $this->id);
			if ($extension) array_push ($elements, $extension);
			return implode('.', $elements);
		}
		else return $this->realname;
	}

	public function filePath () {
		if ($this->filepath) return $this->filepath.$this->nameWithID();
		else return '';
	}

	public function deleteFile () {
		$physical = $this->obtainPhysical();
		$physical->delete();
		$thumbnails = new remositoryThumbnails($this);
		$thumbnails->deleteAllThumbnails();
		$this->deleteFileDB();
	}

	public function setMetaData () {
		$interface = remositoryInterface::getInstance();
		$interface->prependMetaTag('description', strip_tags($this->smalldesc));
		if ($this->keywords) $interface->prependMetaTag('keywords', $this->keywords);
		else $interface->prependMetaTag('keywords', $this->filetitle);
	}

	public function checkLicenseagree () {
		if ($this->licenseagree AND $this->license != '') $this->licenseagree = 1;
		else $this->licenseagree = 0;
	}

	public function getValues ($user=null, $onlypublished=true) {
		$sql = "SELECT f.*, AVG(l.value) AS vote_value, COUNT(l.value) AS vote_count FROM #__downloads_files AS f LEFT JOIN #__downloads_log AS l ON l.type=3 AND l.fileid=f.id WHERE f.id = $this->id";
		if ($onlypublished AND (!is_object($user) OR !$user->isAdmin())) $sql .= " AND published=1";
		$sql .= ' GROUP BY f.id';
		$this->readDataBase($sql);
	}

/* Gabriela new code
	public function getValuesUpload ($user) {
		$sql = "SELECT f.*, AVG(l.value) AS vote_value, COUNT(l.value) AS vote_count FROM #__downloads_files AS f LEFT JOIN #__downloads_log AS l ON l.type=3 AND l.fileid=f.id WHERE f.id = $this->id";
		$sql .= ' GROUP BY f.id';
		$this->readDataBase($sql);
	}
*/

	public function evaluateVote () {
		return round($this->vote_value);
	}

	public function addVote ($user, $vote) {
		$newvote = new remositoryLogEntry(_REM_VOTE_USER_GENERAL,$user->id,$this->id,$vote);
		$newvote->insertEntry();
		$totalvalue = $this->vote_value * $this->vote_count + $vote;
		$this->vote_count++;
		$this->vote_value = $totalvalue/$this->vote_count;
	}

	public function userVoted ($user) {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		if ($user->id) $sql = "SELECT COUNT(*) FROM #__downloads_log WHERE type=3 AND userid=$user->id AND fileid=$this->id";
		else {
			$ipaddress = $interface->getIP();
			$sql = "SELECT COUNT(*) FROM #__downloads_log WHERE type=3 AND ipaddress='$ipaddress' AND fileid=$this->id";
		}
		remositoryRepository::doSQL($sql);
		return $database->loadResult();
	}

	public function userCommented ($user) {

		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		if ($user->id) $sql = "SELECT count(*) FROM #__downloads_reviews WHERE itemid = $this->id AND userid = $user->id";
		else {
			$ipaddress = $interface->getIP();
			$sql = "SELECT COUNT(*) FROM #__downloads_reviews WHERE itemid $this->id AND ipaddress='$ipaddress'";
		}
		$database->setQuery ($sql);
		if ($database->loadResult() == 0) return false;
		echo '<h4>'._DOWN_ALREADY_COMM.'</h4>';
		return true;
	}

	public function getContainer () {
		$manager = remositoryContainerManager::getInstance();
		return $manager->getContainer(abs($this->containerid));
	}

	public function memoContainer ($container) {
		$this->registered = $container->registered;
		$this->userupload = $container->userupload;
		$this->groupid = $container->groupid;
		$this->plaintext = $container->plaintext;
		if ($this->plaintext) {
			$this->filepath = '';
			$this->isblob = 0;
		}
		else {
			$this->filepath = $container->filepath;
			if ($this->filepath) $this->isblob = 0;
			else $this->isblob = 1;
		}
		$this->editgroup = $container->editgroup;
		if (!$this->filepath And !$this->plaintext) $this->isblob = 1;;
	}

	public function getCategoryName () {
  	$parent = $this->getContainer();
  	return $parent->getCategoryName(true);
  }

  public function getFamilyNames () {
  	$parent = $this->getContainer();
  	return $parent->getFamilyNames(true);
  }

	public function incrementCounts ($by) {
		$container = $this->getContainer();
		while ($container != null) {
			$container->increment($by);
			$container=$container->getParent();
		}
	}

	public function checkCountStats ($user, &$message) {
		$repository = remositoryRepository::getInstance();
		$maxdown = $user->maxDownloadsAllFiles();
		$alldown = $user->maxDownloadsOneFile();
		if ($alldown > 0 AND $user->downloadCount($this->id) >= $alldown) {
			$message = '<br/>&nbsp;<br/> '._DOWN_COUNT_EXCEEDED_FILE;
			//.' '.remositoryGroup::getName($this->groupid);
			return true;
		}
		if ($maxdown > 0 AND $maxdown <= $user->totalDown()) {
			$message = '<br/>&nbsp;<br/> '._DOWN_COUNT_EXCEEDED;
			//.' '.remositoryGroup::getName($this->groupid);
			return true;
		}
		return false;
	}

	// Diagnostic display
	public function displayCountStats ($user) {
		$repository = remositoryRepository::getInstance();
		$maxdown = $user->isLogged() ? $repository->Max_Down_Reg_Day : $repository->Max_Down_Per_Day;
		printf ('<br />Checking against maximum downloads of any one file. Limit is %s. Actual for %s is %s', $repository->Max_Down_File_Day, $this->filetitle, $user->downloadCount($this->id));
		printf ('<br />Checking against maximum total downloads. Limit is %s and number already used is %s <br />', $maxdown, $user->totalDown());
	}

	public function downloadForbidden ($user, &$message) {
	  $message = '';
		if ($user->isAdmin()) return false;
		if ($this->metatype) return true;
		$authoriser = aliroAuthoriser::getInstance();
		if ($authoriser->checkPermission ('aUser', $user->id, 'download', 'remosFolder', $this->containerid)
		OR $authoriser->checkPermission ('aUser', $user->id, 'edit', 'remosFolder', $this->containerid)) {
			return $this->checkCountStats ($user, $message);
		}
		if ($user->isLogged()) {
			$container = $this->getContainer();
			return $this->forbidHandling($message, 'remositoryRegisteredRefused', '<br/>&nbsp;<br/> '._DOWN_MEMBER_ONLY_WARN.$container->name);
		}
		return $this->forbidHandling($message, 'remositoryVisitorRefused', '<br/>&nbsp;<br/> '._DOWN_REG_ONLY_WARN);
	}

	public function isAffordable ($user) {
		if (_REMOSITORY_USE_CREDITS) {
			if ($user->userHasPaidForFile($this->id) OR $user->creditsAvailable() >= $this->price) return true;
			return false;
		}
		return true;
	}

	public function forbidHandling (&$message, $event, $default) {
		$interface = remositoryInterface::getInstance();
		$result = $interface->triggerMambots($event, array($this));
		if (empty($result) OR true === $result[0]) {
			$message = $default;
			return true;
		}
		if (is_string($result[0])) {
			$message = $result[0];
			return true;
		}
		return false;
	}

	public function updatePermitted ($user) {
		return $this->checkUpOrDel ($user, 'Allow_User_Edit');
	}

	// Gabriela has this commented out?
	public function deletePermitted ($user) {
		return $this->checkUpOrDel ($user, 'Allow_User_Delete');
	}

	public function checkUpOrDel ($user, $config) {
		if ($user->isAdmin()) return true;
		$repository = remositoryRepository::getInstance();
		if (!$repository->$config) return false;
		if ($this->submittedby == $user->id AND $user->isLogged()) return true;
		$authoriser = aliroAuthoriser::getInstance();
		return $authoriser->checkPermission ('aUser', $user->id, 'edit', 'remosFolder', $this->containerid);
	}

/* Gabriela new code
	public function myUploadsCheck ($user) {
		if ($user->isAdmin() OR $this->submittedby == $user->id) return true;
	}
*/

	public function getExtension () {
		if ($this->islocal) return $this->lastPart($this->realname, '.');
		else return $this->lastPart($this->url, '.');
	}

	public function makeAutoShort () {
		if ($this->autoshort) {
			$this->autoshort = 1;
			$repository = remositoryRepository::getInstance();
			$max = $repository->Small_Text_Len-3;
			$plain = strip_tags($this->description);
			$plain = str_replace('&nbsp;', ' ', $plain);
			if (strlen($plain) > $max) $this->smalldesc=substr($plain,0,$max).'...';
			else $this->smalldesc = $plain;
		}
		else $this->autoshort = 0;
	}

	// Used only on the admin side
	public function getEditSelectList ($type, $parm, $user) {
		$repository = remositoryRepository::getInstance();
		return $repository->getSelectList(false, $this->containerid, $type, $parm, $user);
	}

	public function getPhysicalData ($physicalFile) {
		$this->realname = $physicalFile->proper_name;
		$this->filedate = $physicalFile->date;
		$this->filesize = $physicalFile->size;
		if (!$this->filetitle) $this->filetitle = $physicalFile->proper_name;
		$this->islocal = 1;
		$this->url = '';
	}

	public function onDiskCheckFail ($physicalFile) {
		$repository = remositoryRepository::getInstance();
		$physicalFile->antiLeech();
		$this->getPhysicalData($physicalFile);
		$file_path = $this->filepath.$this->realname;
  		if (file_exists($file_path) AND !$repository->Allow_Up_Overwrite) return true;
		return false;
	}

	public function isFieldHTML ($field) {
		return in_array($field, array('description', 'smalldesc', 'license'));
	}

	public function fieldSizeLimit ($field) {
		$repository = remositoryRepository::getInstance();
		$large = array ('description', 'license');
		if (in_array($field,$large)) return $repository->Large_Text_Len;
		else return $repository->Small_Text_Len;
	}

	public static function getIcons () {
		return remositoryRepository::getIcons ('file_icons');
	}

	public static function togglePublished ($idlist, $value) {
		$cids = implode( ',', $idlist );
		$sql = "UPDATE #__downloads_files SET published=$value". "\nWHERE id IN ($cids)";
		remositoryRepository::doSQL($sql);
	}

	public function classifyFile ($classifications) {
		remositoryRepository::doSQL("DELETE FROM #__downloads_file_classify WHERE file_id = $this->id");
		$sql = "INSERT INTO #__downloads_file_classify (file_id, classify_id) VALUES ";
		foreach ($classifications as $classification) if ($classification) $values[] = "($this->id, $classification)";
		if (isset($values)) {
			$sql .= implode(',', $values);
			remositoryRepository::doSQL($sql);
		}
	}

	public function addSubmitterEmail () {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$database->setQuery("SELECT email FROM #__users WHERE id = $this->submittedby");
		$email = $database->loadResult();
		if ($email) $this->submit_email = $email;
	}

	// Alternative to use the CMS pathway instead of a separate Remository one
	public function showCMSPathway () {
		$parent = $this->getContainer();
		if (!is_null($parent)) $parent->showCMSPathway();
		$interface = remositoryInterface::getInstance();
		$link = remositoryRepository::getInstance()->RemositoryRawfunctionURL('fileinfo', $this->id);
		$interface->appendPathWay($this->filetitle, $link);
	}

	public static function countUserUploads ($user) {
		$database = remositoryInterface::getInstance()->getDB();
		$database->setQuery("SELECT COUNT(*) FROM #__downloads_files WHERE submittedby = $user->id");
		return $database->loadResult();
	}

	public static function resetDownloadCounts () {
		// Change for multiple repositories
		// $repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		// remositoryRepository::doSQL("UPDATE #__downloads_files SET downloads=0 WHERE repnum = $repnum");
		remositoryRepository::doSQL("UPDATE #__downloads_files SET downloads=0");
	}

	public static function storeMemoFields ($container, $inherit=false) {
		$actions = "SET f.registered='$container->registered', f.userupload='$container->userupload', f.groupid='$container->groupid', f.editgroup='$container->editgroup'";
		if ($inherit) {
			$familylist = remositoryContainerManager::getInstance()->listGivenAndDescendants($container->id);
			$sql = "UPDATE #__downloads_files AS f $actions WHERE containerid IN ($familylist)";
		}
		else $sql = "UPDATE #__downloads_files AS f $actions WHERE containerid=$container->id";
		remositoryRepository::doSQL($sql);
	}

	public static function getFilesSQL ($published, $count=false, $containerid=0, $descendants=false, $orderby=_REM_DEFAULT_ORDERING, $search='', $limitstart=0, $limit=0, $submitter=0, $classify=0) {
		$sorter = array ('', ' ORDER BY id', ' ORDER BY filetitle', ' ORDER BY downloads DESC', ' ORDER BY submitdate DESC', ' ORDER BY u.username', ' ORDER BY fileauthor', ' ORDER BY vote_value DESC, submitdate DESC');
		if (!isset($sorter[$orderby]) OR $orderby == 0) $orderby = _REM_DEFAULT_ORDERING;
		if ($count) $results = 'count(*)';
		else $results = 'f.*, AVG(l.value) AS vote_value, COUNT(l.value) AS vote_count, 0 AS active_feature';
		if ($submitter) $results .= ', u.username';
		// Change for multiple repositories
		// $repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		// $where[] = "repnum = $repnum";
		$sql = "SELECT $results FROM #__downloads_files AS f";
		if ($descendants AND $containerid) {
			$familylist = remositoryContainerManager::getInstance()->listGivenAndDescendants($containerid);
			if ($familylist) $where[] = "f.containerid IN ($familylist)";
			else $where[] = "f.metatype = 0";
		}
		else {
			if ($containerid) $where[] = "f.containerid = $containerid";
			else $where[] = "f.metatype = 0";
		}
		if ($classify) {
			$sql .= " INNER JOIN #__downloads_file_classify AS fc ON f.id = fc.file_id ";
			$where[] = "fc.classify_id IN ($classify)";
		}
		if ($submitter) $where[] = "f.submittedby = $submitter";
		if (self::$flist) {
			$where[] = 'f.id NOT IN ('.self::$flist.')';
			self::$flist = '';
		}
		if (!$count) $sql .= ' LEFT JOIN #__downloads_log AS l ON l.type=3 AND l.fileid=f.id AND l.value != 0';
		if ($submitter OR (5 == $orderby)) $sql .= ' LEFT JOIN #__users AS u ON u.id=f.submittedby';
		if ($published) {
			$where[] = 'f.published=1';
			$where[] = 'NOW() >= f.publish_from AND (f.publish_to >= NOW() OR f.publish_to="0000-00-00 00:00:00")';
		}
		$interface = remositoryInterface::getInstance();
		if ($search) {
			$search = $interface->getEscaped($search);
			$where[] = "LOWER(f.filetitle) LIKE '%$search%'";
		}
		if (isset($where)) $sql .= ' WHERE '.implode(' AND ',$where);
		$user = $interface->getUser();
		$visibility = remositoryAbstract::visibilitySQL ($user);
		if ($visibility) $sql .= ' AND '.$visibility;
		if (!$count) {
			$sql .= ' GROUP BY f.id ';
			$sql .= $sorter[$orderby];
		}
		if ($limit) $sql .= " LIMIT $limitstart,$limit";
		return $sql;
	}

	public static function getFeaturedFiles ($published, $containerid) {
		$containerid = intval($containerid);
		$sql = "SELECT f.*, AVG(l.value) AS vote_value, COUNT(l.value) AS vote_count, 1 AS active_feature"
		."\n FROM #__downloads_files AS f"
		."\n LEFT JOIN #__downloads_log AS l ON l.type=3 AND l.fileid=f.id AND l.value != 0"
		."\n WHERE f.featured != 0 AND f.featured_st_date <= CURDATE()"
		."\n AND (f.featured_end_date = '0000-00-00' OR CURDATE() <= f.featured_end_date)";
		if ($containerid) {
			$familylist = remositoryContainerManager::getInstance()->listGivenAndDescendants($containerid);
			if ($familylist) $sql .= " AND f.containerid IN ($familylist)";
		}
		if ($published) {
			$sql .= ' AND published != 0';
			$sql .= ' AND NOW() >= f.publish_from AND (f.publish_to >= NOW() OR f.publish_to="0000-00-00 00:00:00")';
		}
		$repository = remositoryRepository::getInstance();
		$sql .= " GROUP BY f.id ORDER BY featured_seq LIMIT $repository->Featured_Number";
		$featured = remositoryRepository::doSQLget($sql, 'remositoryFile');
		foreach ($featured as $file) $fid[] = $file->id;
		if (isset($fid)) {
			$flist = implode(',', $fid);
			self::$flist = $flist;
			remositoryRepository::doSQL("UPDATE #__downloads_files SET featured_seq = featured_seq + 1 WHERE id IN ($flist)");
		}
		return $featured;
	}

	// The following are helper methods that do file related things that are not specific to an individual file
	public static function popularLoggedFiles ($category, $max, $days, $user, $repnum=0) {
		if (1 > $repnum) $repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		remositoryFile::makeLogSummary($database, $days);
		$summarytype = _REM_LOG_DOWN_SUMMARY;
		$sql = 'SELECT f.*, AVG(v.value) AS vote_value, COUNT(v.value) AS vote_count, 0 AS active_feature, c.name, l.value AS logdownloads FROM #__downloads_log AS l'
		." INNER JOIN #__downloads_files AS f ON l.type = $summarytype AND l.fileid = f.id"
		." INNER JOIN #__downloads_containers AS c ON c.id = f.containerid"
		."\n LEFT JOIN #__downloads_log AS v ON v.type=3 AND v.fileid=f.id AND v.value != 0";
		if ($category) {
			$familylist = remositoryContainerManager::getInstance()->listGivenAndDescendants($category);
			if ($familylist) $where[] = "f.containerid IN ($familylist)";
		}
		// Change for multiple repositories
		// $sql .= ' WHERE c.id = f.containerid AND f.published=1 AND l.type=1 AND l.fileid=f.id AND repnum = '.$repnum;
		$where[] = 'f.published != 0';
		$where[] = 'NOW() >= f.publish_from AND (f.publish_to >= NOW() OR f.publish_to="0000-00-00 00:00:00")';
		$visibility = remositoryAbstract::visibilitySQL ($user);
		if ($visibility) $where[] = $visibility;
		$sql .= ' WHERE '.implode(' AND ', $where);
		$sql .= " GROUP BY l.fileid ORDER BY logdownloads DESC, f.filetitle ASC LIMIT $max";
		$files = remositoryRepository::doSQLget($sql, 'remositoryFile');
		foreach ($files as $file) $file->downloads = $file->logdownloads;
		return $files;
	}

	private static function makeLogSummary ($database, $days) {
		$days = intval($days);
		$type = _REM_LOG_DOWN_SUMMARY;
		$database->setQuery("SELECT * FROM #__downloads_log WHERE price = $days AND date > DATE_SUB(NOW(), INTERVAL 24 HOUR) AND type = "._REM_LOG_DOWN_SUMMARY);
		$summaries = $database->loadObjectList();
		if (empty($summaries)) {
			remositoryRepository::doSQL("DELETE FROM #__downloads_log WHERE price = $days AND type = "._REM_LOG_DOWN_SUMMARY);
			remositoryRepository::doSQL("INSERT INTO #__downloads_log (fileid, value, type, date, price)"
			." (SELECT fileid, COUNT(*) AS value, $type AS type, NOW() AS date, $days as price"
			." FROM #__downloads_log WHERE DATE_SUB( CURDATE( ) , INTERVAL $days DAY ) <= date"
			." GROUP BY fileid ORDER BY value DESC LIMIT 100 )");
		}
	}

	public static function popularDownloadedFiles ($category, $max, $user, $repnum=0) {
		if (1 > $repnum) $repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$sql = 'SELECT f.*, AVG(v.value) AS vote_value, COUNT(v.value) AS vote_count, 0 AS active_feature, c.name from #__downloads_files AS f INNER JOIN #__downloads_containers AS c ON f.containerid = c.id'
		."\n LEFT JOIN #__downloads_log AS v ON v.type=3 AND v.fileid=f.id AND v.value != 0";
		if ($category) {
			$familylist = remositoryContainerManager::getInstance()->listGivenAndDescendants($category);
			if ($familylist) $where[] = "f.containerid IN ($familylist)";
		}
		// Change for multiple repositories
		// $sql .= ' WHERE f.containerid = c.id AND f.published=1 AND repnum = '.$repnum;
		$where[] = 'f.published != 0';
		$where[] = 'NOW() >= f.publish_from AND (f.publish_to >= NOW() OR f.publish_to="0000-00-00 00:00:00")';
		$visibility = remositoryAbstract::visibilitySQL ($user);
		if ($visibility) $where[] = $visibility;
		$sql .= ' WHERE '.implode(' AND ', $where)." GROUP BY f.id ORDER BY f.downloads DESC, f.filetitle ASC LIMIT $max";
		$files = remositoryRepository::doSQLget($sql, 'remositoryFile');
		return $files;
	}

	public static function newestFiles ($category, $max, $user, $repnum=0) {
		if (1 > $repnum) $repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$sql = 'SELECT f.*, AVG(v.value) AS vote_value, COUNT(v.value) AS vote_count, 0 AS active_feature, c.name from #__downloads_files AS f INNER JOIN #__downloads_containers AS c ON f.containerid = c.id'
		."\n LEFT JOIN #__downloads_log AS v ON v.type=3 AND v.fileid=f.id AND v.value != 0";
		if ($category) {
			$familylist = remositoryContainerManager::getInstance()->listGivenAndDescendants($category);
			if ($familylist) $where[] = "f.containerid IN ($familylist)";
		}
		// Change for multiple repositories
		// $sql .= ' WHERE f.containerid = c.id AND f.published=1 AND repnum = '.$repnum;
		$where[] = 'f.published != 0';
		$where[] = 'NOW() >= f.publish_from AND (f.publish_to >= NOW() OR f.publish_to="0000-00-00 00:00:00")';
		$visibility = remositoryAbstract::visibilitySQL ($user);
		if ($visibility) $where[] = $visibility;
		$sql .= ' WHERE '.implode(' AND ', $where)." GROUP BY f.id ORDER BY f.filedate DESC LIMIT $max";
		$files = remositoryRepository::doSQLget($sql, 'remositoryFile');
		return $files;
	}

	public static function ratedFiles ($category, $max, $user, $repnum=0) {
		if (1 > $repnum) $repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$sql = 'SELECT f.*, AVG(l.value) AS vote_value, COUNT(l.value) AS vote_count, 0 AS active_feature, c.name'
		.' from #__downloads_files AS f INNER JOIN #__downloads_containers AS c ON f.containerid = c.id'
		.' LEFT JOIN #__downloads_log AS l ON l.type = 3 AND l.fileid = f.id AND l.value != 0';
		if ($category) {
			$familylist = remositoryContainerManager::getInstance()->listGivenAndDescendants($category);
			if ($familylist) $where[] = "f.containerid IN ($familylist)";
		}
		// Change for multiple repositories
		// $sql .= ' WHERE f.containerid = c.id AND f.published=1 AND repnum = '.$repnum;
		$where[] = 'f.published != 0';
		$where[] = 'NOW() >= f.publish_from AND (f.publish_to >= NOW() OR f.publish_to="0000-00-00 00:00:00")';
		$visibility = remositoryAbstract::visibilitySQL ($user);
		if ($visibility) $where[] = $visibility;
		$sql .= ' WHERE '.implode(' AND ', $where)." GROUP BY f.id ORDER BY vote_value DESC, vote_count DESC LIMIT $max";
		$files = remositoryRepository::doSQLget($sql, 'remositoryFile');
		return $files;
	}

	public static function uploadedFiles ($category, $max, $user, $repnum=0) {
		// if (1 > $repnum) $repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$sql = 'SELECT f.*, AVG(l.value) AS vote_value, COUNT(l.value) AS vote_count, 0 AS active_feature, c.name'
		.' from #__downloads_files AS f INNER JOIN #__downloads_containers AS c ON f.containerid = c.id'
		.' LEFT JOIN #__downloads_log AS l ON l.type = 3 AND l.fileid = f.id AND l.value != 0';
		if ($category) {
			$familylist = remositoryContainerManager::getInstance()->listGivenAndDescendants($category);
			if ($familylist) $where[] = "f.containerid IN ($familylist)";
		}
		$where[] = "f.submittedby = $user->id";
		$where[] = 'f.published != 0';
		$where[] = 'NOW() >= f.publish_from AND (f.publish_to >= NOW() OR f.publish_to="0000-00-00 00:00:00")';
		$visibility = remositoryAbstract::visibilitySQL ($user);
		if ($visibility) $where[] = $visibility;
		$sql .= ' WHERE '.implode(' AND ', $where)." GROUP BY f.id ORDER BY submitdate DESC, name ASC, f.containerid ASC, filetitle ASC";
		if ($max > 0) $sql .= " LIMIT $max";
		$files = remositoryRepository::doSQLget($sql, 'remositoryFile');
		return $files;
	}

	public static function randomFiles ($category, $max, $user, $featuredOnly=false, $repnum=0) {
		if (1 > $repnum) $repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$sql = 'SELECT f.id FROM #__downloads_files AS f INNER JOIN #__downloads_containers AS c ON f.containerid = c.id';
		if ($category) {
			$familylist = remositoryContainerManager::getInstance()->listGivenAndDescendants($category);
			if ($familylist) $where[] = "f.containerid IN ($familylist)";
		}
		// Change for multiple repositories
		// $sql .= ' WHERE f.containerid = c.id AND f.published=1 AND repnum = '.$repnum;
		$where[] = 'f.published != 0';
		$where[] = 'NOW() >= f.publish_from AND (f.publish_to >= NOW() OR f.publish_to="0000-00-00 00:00:00")';
		if ($featuredOnly) {
			$where[] = 'f.featured != 0';
			$where[] = 'f.featured_st_date <= CURDATE()';
			$where[] = "(f.featured_end_date = '0000-00-00' OR CURDATE() <= f.featured_end_date)";
		}
		$visibility = remositoryAbstract::visibilitySQL ($user);
		if ($visibility) $where[] = $visibility;
		$database->setQuery($sql." WHERE ".implode(' AND ', $where));
		$fileids = $database->loadResultArray();
		if (empty($fileids)) return array();
		if ($max >= count($fileids)) $chosen = $fileids;
		else for ($i=0; $i<min(count($fileids),$max); $i++) {
			$thisone = mt_rand(0,count($fileids)-1);
			$chosen[] = $fileids[$thisone];
			unset($fileids[$thisone]);
			$fileids = array_values($fileids);
		}
		$where[] = ' f.id IN ('.implode(',', $chosen).')';
		$sql = 'SELECT f.*, AVG(v.value) AS vote_value, COUNT(v.value) AS vote_count, 0 AS active_feature, c.name from #__downloads_files AS f INNER JOIN #__downloads_containers AS c ON f.containerid = c.id'
		."\n LEFT JOIN #__downloads_log AS v ON v.type=3 AND v.fileid=f.id AND v.value != 0";
		$sql .= ' WHERE '.implode(' AND ', $where)." GROUP BY f.id ORDER BY f.filedate DESC LIMIT $max";
		$files = remositoryRepository::doSQLget($sql, 'remositoryFile');
		return $files;
	}

	public static function eventFiles ($category, $max, $user, $repnum=0) {
		if (1 > $repnum) $repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$database->setQuery("SELECT COUNT(*) FROM #__downloads_files WHERE republish_num != 0 AND publish_to < CURDATE()");
		if ($database->loadResult()) {
			$database->setQuery("UPDATE #__downloads_files SET publish_to ="
			." (CASE WHEN 0=republish_unit THEN adddate(publish_to, INTERVAL republish_num DAY)"
			." WHEN 1=republish_unit THEN adddate(publish_to, INTERVAL republish_num WEEK)"
			." WHEN 2=republish_unit THEN adddate(publish_to, INTERVAL republish_num MONTH) END)"
			." WHERE republish_num != 0 AND publish_to < CURDATE()"
			);
			$database->query();
		}
		$sql = 'SELECT f.id, f.filetitle, f.autoshort, f.description, f.smalldesc, f.filedate, f.icon, f.containerid, f.publish_to, c.name from #__downloads_files AS f, #__downloads_containers AS c';
		if ($category) {
			$familylist = remositoryContainerManager::getInstance()->listGivenAndDescendants($category);
			if ($familylist) $where[] = "f.containerid IN ($familylist)";
		}
		// Change for multiple repositories
		// $sql .= ' WHERE f.containerid = c.id AND f.published=1 AND repnum = '.$repnum;
		$where[] = 'f.containerid = c.id AND f.published=1';
		$visibility = remositoryAbstract::visibilitySQL ($user);
		if ($visibility) $where[] = $visibility;
		$sql .= ' WHERE '.implode(' AND ', $where)." ORDER BY f.publish_to LIMIT $max";
		$database->setQuery($sql);
		$files = $database->loadObjectList();
		return empty($files) ? array() : $files;
	}

	public static function getCountInContainer ($id, $published, $search='', $repnum=0) {
		if (1 > $repnum) $repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		// Change for multiple repositories
		// $sql = "SELECT COUNT(id) FROM #__downloads_files WHERE containerid = $id AND repnum = $repnum";
		$sql = "SELECT COUNT(id) FROM #__downloads_files WHERE containerid = $id";
		if ($published) {
			$sql .= ' AND published=1';
			$sql .= ' AND NOW() >= f.publish_from AND (f.publish_to >= NOW() OR f.publish_to="0000-00-00 00:00:00")';
		}
		if ($search) $sql .= " AND LOWER(filetitle) LIKE '%$search%'";
		$database->setQuery($sql);
		return $database->loadResult();
	}

	public static function searchFilesSQL($search_text, $seek_fields, $user, $countOnly, $containers=array(), $limitstart=0, $limit=0, $ordering='alpha', $repnum=0) {
		if (1 > $repnum) $repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		$results = $countOnly ? 'COUNT(*)' : 'f.*, AVG(l.value) AS vote_value, COUNT(l.value) AS vote_count';
		// Change for multiple repositories
		// $sql="SELECT $results FROM #__downloads_files AS f WHERE metatype = 0 AND repnum = $repnum";
		$sql="SELECT $results FROM #__downloads_files AS f";
		if (!$countOnly) $sql .= ' LEFT JOIN #__downloads_log AS l ON l.type = 3 AND l.fileid = f.id AND l.value != 0';
		$where[] = "metatype = 0";
		foreach ($seek_fields as $field) $orcondition[] = "$field LIKE '%$search_text%'";
		if (isset($orcondition)) $where[] = '('.implode(' OR ', $orcondition).')';
		else {
			echo '<br/>&nbsp;<br/>'._DOWN_SEARCH_ERR;
			exit;
		}
		$visibility = remositoryAbstract::visibilitySQL ($user);
		if ($visibility) $where[] = $visibility;
		if (!empty($containers)) {
			$cmanager = remositoryContainerManager::getInstance();
			foreach ((array) $containers as $containerid) {
				$familylist = $cmanager->listGivenAndDescendants($containerid);
				if ($familylist) $wherepart[] = "f.containerid IN ($familylist)";
			}
			if (isset($wherepart)) $where[] = '('.implode(' OR ', $wherepart).')';
		}
		$where[] = 'f.published=1';
		$where[] = 'NOW() >= f.publish_from AND (f.publish_to >= NOW() OR f.publish_to="0000-00-00 00:00:00")';
		if (!(empty($where))) $sql .= ' WHERE '.implode(' AND ', $where);
		if (!$countOnly) $sql .= ' GROUP BY f.id ';
		switch ($ordering) {
			case 'popular':
			  $seq = 'f.downloads DESC';
			  break;
			case 'category':
			  $seq = 'c.name, f.filetitle ASC';
			  $section = "\n c.name AS section,";
			  break;
			case 'oldest':
				$seq = 'f.submitdate ASC';
				break;
			case 'newest':
				$seq = 'f.submitdate DESC';
				break;
			case 'alpha':
			default:
			  $seq = 'f.filetitle ASC';
		}
		$sql .= ' ORDER BY '.$seq;
		if ($limit AND !$countOnly) $sql .= " LIMIT $limitstart,$limit";
		return $sql;
	}

	public static function getPopularAuthors () {
		$repository = remositoryRepository::getInstance();
		$threshold = (int) $repository->Author_Threshold;
		$authorlist = trim($repository->Main_Authors);
		$config_authors = $authorlist ? explode(',', $authorlist) : array();
		if ($threshold) {
			$interface = remositoryInterface::getInstance();
			$database = $interface->getDB();
			$database->setQuery("SELECT a.fileauthor FROM (SELECT fileauthor, COUNT( * ) AS number FROM aliro_downloads_files GROUP BY fileauthor)"
			."\n AS a WHERE a.number >=5 AND a.fileauthor != '' ORDER BY fileauthor");
			$authors = $database->loadResultArray();
		}
		$authors = empty($authors) ? $config_authors : array_merge($config_authors, $authors);
		function trim_value (&$value) {$value = trim($value);}
		array_walk($authors, 'trim_value');
		return $authors;
	}

}
