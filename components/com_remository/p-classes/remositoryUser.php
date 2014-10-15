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

class remositoryUser {
	private static $users = array();
	/** @var int ID for the user in the database */
	public $id=0;
	/** @var bool Is the current user of administrator status? */
	public $admin=false;
	/** @var bool Is the current user logged in?  */
	public $logged=false;
	/** @var string User name if loggged in */
	public $name='';
	/** @var string User full name if logged in */
	public $fullname='';
	/** @var string User type if logged in */
	public $usertype='';
	/** @var string User current IP address */
	public $currIP='';
	/** @var array Downloads so far today by file ID */
	public $downloads = array();
	/** @var int Total downloads all files today */
	public $totaldown = 0;
	/** @var array Container IDs where user does not have permission */
	public $refused=array();

	private $credits = 0;
	private $credits_set = false;

	private $totaldown_set = false;

	/**
	* File object constructor
	* @param int Directory full path
	*/
	public function __construct ( $id=0, $my=null ) {
		$interface = remositoryInterface::getInstance();
		$this->id = $id;
		if ($id) {
			if (!$my) $my = $interface->getIdentifiedUser($id);
			if (!empty($my->gid) OR !empty($my->groups)) {
				$this->name = $my->username;
				$this->fullname = $my->name;
				$this->usertype = $my->usertype;
				$this->logged = true;
				if (defined('_JOOMLA_16PLUS') AND count(array_intersect($my->groups, array(7,8)))) $this->admin = true;
				elseif ((strtolower($my->usertype)=='administrator') OR (strtolower($my->usertype)=='superadministrator')
					OR (strtolower($my->usertype)=='super administrator') OR !empty($my->groups['Super Users'])){
					$this->admin = true;
				}
			}
			if (isset($my->jaclplus)) $this->jaclplus = $my->jaclplus;
		}
		$this->currIP = $interface->getIP();
		$authoriser = aliroAuthoriser::getInstance();
		$this->refused = $authoriser->getRefusedList ('aUser', $this->id, 'remosFolder', 'download,edit');
	}

	public function isAdmin () {
		return $this->admin;
	}
	public function isUser () {
		if ($this->isAdmin()) return false;
		return $this->isLogged();
	}
	public function isLogged () {
		return $this->logged;
	}
	public function fullname () {
		return $this->fullname;
	}
	public function canDownloadContainer($id) {
		return (!in_array($id, $this->refused));
	}
	public function uploadsToday () {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();

		$today = date('Y-m-d');
		// Change for multiple repositories
		// $repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		// $sql="SELECT COUNT(*) from #__downloads_files WHERE repnum = $repnum AND submittedby=".$this->id." AND submitdate LIKE '".$today."%'";
		$sql="SELECT COUNT(*) from #__downloads_files WHERE submittedby=".$this->id." AND submitdate LIKE '".$today."%'";
		$database->setQuery($sql);
		return $database->loadResult();
	}

	public function allowUploadCheck ($container, $controller) {
		$view = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $controller);
		if ($this->isAdmin()) return;
		$repository = remositoryRepository::getInstance();
		if (!$repository->Allow_User_Sub) {
			echo "<script> alert('"._DOWN_NOT_AUTH."'); window.history.go(-1); </script>\n";
			exit();
		}
		$authoriser = aliroAuthoriser::getInstance();
		if ($authoriser->checkPermission ('aUser', $this->id, 'upload', 'remosFolder', $container->id)
		OR $authoriser->checkPermission ('aUser', $this->id, 'edit', 'remosFolder', $container->id)) {
			if ($this->logged) {
				if ($this->uploadsToday() > $repository->Max_Up_Per_Day) {
					$view->uploadLimit();
					exit;
				}
			}
			return;
		}
		$view->noaccess($container);
		exit;

	}

	public function hasAutoApprove ($container) {
		// Remove this line if visitors can self-approve -
		// containers must also be set with self approve permission for Visitor
		if (!$this->isLogged()) return false;
		$repository = remositoryRepository::getInstance();
		if ($this->isAdmin()) {
			if ($repository->Enable_Admin_Autoapp) return true;
		}
		$authoriser = aliroAuthoriser::getInstance();
		if ($authoriser->checkPermission ('aUser', 0, 'selfApprove', 'remosFolder', $container->id)) return false;
		if ($authoriser->checkPermission ('aUser', $this->id, 'selfApprove', 'remosFolder', $container->id)) return true;
		return false;
	}

	public function sendMailFrom ($emailto, $subject, $text='') {
		$email = $this->getEmailAddress();
		if ($email) return $this->sendMail($email, $this->fullname(), $emailto, $subject, $text, null, null, null, null, null, null);
		else return false;
	}

	public function sendMailTo ($subject, $text='', $emailfrom='', $fromname='', $file=null) {
		$email = $this->getEmailAddress();
		$transformer = array(
			'{toname}' => $this->fullname,
			'{touser}' => $this->name,
			'{toemail}' => $email,
			'{fromname}' => $fromname,
			'{fromemail}' => $emailfrom
		);
		if (is_object($file)) {
			$filetransform = array(
			    '{fileversion}' => $file->fileversion,
			    '{filetitle}' => $file->filetitle
			);
			$transformer = array_merge($transformer, $filetransform);
	    }
		$text = str_replace(array_keys($transformer), array_values($transformer), $text);
		if ($email) return $this->sendMail($emailfrom, $fromname, $email, $subject, $text, null, null, null, null, null, null);
		else return false;
	}
	
	protected function sendMail ($emailfrom, $fromname, $recipient, $subject, $body) {
		if (defined('_JOOMLA_15PLUS')) return JUTility::sendMail($emailfrom, $fromname, $recipient, $subject, $body, 0, null, null, null, null, null);
		else return mosMail ($emailfrom, $fromname, $recipient, $subject, $body, 0, null, null, null, null, null);
	}

	public function getEmailAddress () {
		$database = remositoryInterface::getInstance()->getDB();
		$database->setQuery("SELECT email FROM #__users WHERE id = $this->id");
		return $database->loadResult();
	}

	public static function superAdminMail () {
		$interface = remositoryInterface::getInstance();
		return $interface->getOneAdminEmail();
	}

	private function getDownloadInfo () {
		if ($this->totaldown_set) return;
		$results = $this->id ? $this->loggedCount() : $this->nonLoggedCount();
		if ($results) foreach ($results as $result) {
			$this->downloads[$result->fileid] = $result->number;
			$this->totaldown += $result->number;
		}
		$this->totaldown_set = true;
	}

	private function nonLoggedCount () {
		$interface = remositoryInterface::getInstance();
		$ipaddress = $interface->getIP();
		$database = $interface->getDB();
		$type = _REM_LOG_DOWNLOAD;
		$database->setQuery("SELECT fileid, COUNT(fileid) AS number FROM #__downloads_log WHERE type = $type AND ipaddress = '$ipaddress' AND date > SUBDATE(NOW(), INTERVAL 24 HOUR) GROUP BY fileid");
		return $database->loadObjectList();
	}

	private function loggedCount () {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$type = _REM_LOG_DOWNLOAD;
		$typadmin = _REM_LOG_ADMIN_DOWNLOAD;
		$database->setQuery("SELECT fileid, COUNT(fileid) AS number FROM #__downloads_log WHERE (type = $type OR type = $typadmin) AND userid = $this->id AND date > SUBDATE(NOW(), INTERVAL 24 HOUR) GROUP BY fileid");
		return $database->loadObjectList();
	}

	public function downloadCount ($id) {
		$this->getDownloadInfo();
		return isset($this->downloads[$id]) ? $this->downloads[$id] : 0;
	}

	public function maxDownloadsOneFile () {
		return remositoryRepository::getInstance()->Max_Down_File_Day;
	}

	public function totalDown () {
		$this->getDownloadInfo();
		return $this->totaldown;
	}

	public function maxDownloadsAllFiles () {
		$repository = remositoryRepository::getInstance();
		return $this->isLogged() ? $repository->Max_Down_Reg_Day : $repository->Max_Down_Per_Day;
	}

	public function creditsAvailable () {
		if ($this->credits_set) return $this->credits;
		if (_REMOSITORY_REQUIRE_VODES_FOR_DOWNLOAD) {
			$interface = remositoryInterface::getInstance();
			$database = $interface->getDB();
			$database->setQuery("SELECT credit FROM #__vodes_credits WHERE userid = $this->id");
			$result = $database->loadResult();
			$this->credits = $result ? $result : 0;
		}
		elseif (_REMOSITORY_REQUIRE_AUP_FOR_DOWNLOAD AND $this->loadAUP()) {
			$profile = AlphaUserPointsHelper::getUserInfo('', $this->id);
			$this->credits = $profile->points;
		}
		else $this->credits = _REMOSITORY_USE_CREDITS ? 0 : 999999;
		$this->credits_set = true;
		return $this->credits;
	}

	public function chargeCreditsForFile ($file) {
		if (_REMOSITORY_REQUIRE_VODES_FOR_DOWNLOAD AND !$this->userHadPaidForFile($file)) {
			remositoryRepository::doSQL("UPDATE #__vodes_credits SET credit = credit - $file->price WHERE userid = $this->id");
		}
		elseif (_REMOSITORY_REQUIRE_AUP_FOR_DOWNLOAD) {
			if ($this->loadAUP()) AlphaUserPointsHelper::newpoints('plgaup_remository_download','','remository_download_'.$file->id,'',-$file->price);
		}
	}
	
	protected function loadAUP () {
		$api_AUP = JPATH_SITE.DS.'/components/com_alphauserpoints/helper.php';
		if (is_readable($api_AUP)) return require_once($api_AUP);
		return false;
	}

	public function userHasPaidForFile ($file) {
		$limit = _REMOSITORY_DOWN_WINDOW_COUNT;
		$units = _REMOSITORY_DOWN_WINDOW_UNIT;
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$database->setQuery("SELECT COUNT(*) FROM #__downloads_log WHERE userid = $this->id AND fileid = $file->id AND date > SUBDATE(NOW(), INTERVAL $limit $units) AND price != 0 ORDER BY date LIMIT 1");
		return $database->loadResult() ? true : false;
	}

	public static function getUser ($id=0, $my=null) {
		return isset(self::$users[$id]) ? self::$users[$id] : self::$users[$id] = new self($id, $my);
	}

	public static function mailPeopleViewingContainer ($containerid, $subject, $body, $file=null) {
		$interested = aliroAuthoriser::getInstance()->listAccessorsToSubject ('remosFolder', $containerid, 'aUser', 'download');
		if (!empty($interested)) {
			$interface = remositoryInterface::getInstance();
			$repository = remositoryRepository::getInstance();
			if ($file instanceof remositoryFile) $body .= sprintf(_DOWN_LINK_TO_FILE, $repository->remositoryBasicFunctionURL('fileinfo', $file->id));
			else $body .= sprintf(_DOWN_LINK_TO_CONTAINER, $repository->remositoryBasicFunctionURL('select', $containerid));
			$listed = implode(',', $interested);
			$database = $interface->getDB();
			$database->setQuery("SELECT * FROM #__users WHERE id IN ($listed)");
			$people = $database->loadObjectList();
			if (!empty($people)) {
				foreach ($people as $person) {
					$pbody = sprintf($body, $person->name);
					$interface->sendMail($person->email, $subject, utf8_encode($pbody));
					sleep(1);
				}
			}
		}
	}
}
