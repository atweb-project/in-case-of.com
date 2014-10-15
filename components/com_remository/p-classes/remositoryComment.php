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

class remositoryComment extends remositoryAbstract {

	public $component='com_remository';
	public $id=0;
	public $itemid=0;
	public $userid=0;
	public $name='';
	public $username='';
	public $userURL='';
	public $title='';
	public $comment='';
	public $fullreview='';
	public $date='';

	public function __construct ($id=0) {
		$this->id = intval($id);
		if ($this->id) {
			$interface = remositoryInterface::getInstance();
			$database = $interface->getDB();
			$database->setQuery("SELECT * FROM #__downloads_reviews WHERE id = $this->id");
			$results = $database->loadObjectList();
			$result = empty($results) ? null : $results[0];
			if (is_object($result)) $this->setValues($result);
			else $this->id = 0;
		}
	}
	
	public function setEachValue ($userid, $name, $username, $title, $comment, $date=null) {
		$this->userid = $userid;
		$this->name = $name;
		$this->username = $username;
		$this->title = $title;
		$this->comment = strip_tags($comment);
		$this->date = $date;
	}

	public function saveComment ($file) {
		$interface = remositoryInterface::getInstance();
		if ($this->date == null) $this->date = date('Y-m-d H:i:s');
		$comment = $interface->getEscaped($this->comment);
		$title = $interface->getEscaped($this->title);
		$this->itemid = intval($this->itemid);
		if ($this->id) $sql = "UPDATE #__downloads_reviews SET title = '$title', comment = '$comment' WHERE id = $this->id"; 
		else $sql="INSERT INTO #__downloads_reviews (component, itemid, userid, title, comment, date) VALUES ('$this->component', $file->id, $this->userid, '$title', '$comment', '$this->date')";
		remositoryRepository::doSQL($sql);
		if (0 == $this->id) {
			if (array_sum($interface->triggerMambots('remositoryNewFileComment', $file))) return;
			if (_REMOSITORY_EMAIL_COMMENTS_ACCESSORS) {
				$interface = remositoryInterface::getInstance();
				$commenter = remositoryUser::getUser($this->userid);
				$message = _DOWN_MAIL_MESSAGE_PREFIX.sprintf(_DOWN_NEW_COMMENT_MSG, $interface->getCfg('sitename'), $file->filetitle, $commenter->name, $this->comment);
				remositoryUser::mailPeopleViewingContainer($file->containerid, _DOWN_NEW_FILE_COMMENT.' : '.$file->filetitle, $message, $file);
			}
		}
	}

	public static function getComments ($itemid) {
		$sql = "SELECT c.id, c.title, c.comment, c.date, u.id as userid, u.name, u.username FROM #__downloads_reviews AS c INNER JOIN #__users AS u ON c.userid=u.id WHERE c.itemid=$itemid";
		return remositoryRepository::doSQLget($sql, 'remositoryComment');
	}

	public static function deleteComments ($itemid) {
		$sql = "DELETE FROM #__downloads_reviews WHERE component='com_remository' AND itemid=$itemid";
		remositoryRepository::doSQL($sql);
	}

	public static function deleteComment ($commentid) {
		$sql = "DELETE FROM #__downloads_reviews WHERE component='com_remository' AND id=$commentid";
		remositoryRepository::doSQL($sql);
	}

}