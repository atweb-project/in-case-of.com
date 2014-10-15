<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006-10 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

class remository_mailfiles_Controller extends remositoryUserControllers {

	public function mailfiles () {
		$files = remositoryRepository::getParam($_POST, 'tomail', array());
		$files = array_map('intval', $files);
		$users = remositoryRepository::getParam($_POST, 'remositoryemailusers', array());
		$users = array_map('intval', array_keys($users));
		$database = $this->interface->getDB();
		if (count($files) AND count($users)) {
			$filelist = implode(',', $files);
			$userlist = implode(',', $users);
			$myemail = $this->remUser->getEmailAddress();
			$fileobjects = remositoryRepository::doSQLget("SELECT id, filetitle FROM #__downloads_files WHERE id IN ($filelist)", 'remositoryFile');

			foreach ($users as $user) $userobjects[] = remositoryUser::getUser($user);
			foreach ($userobjects as $user) {
				$mailtext = $screentext = '';
				foreach ($fileobjects as $file) {
					if ($file->downloadForbidden($user, $message)) $screentext .= sprintf(_DOWN_COULD_NOT_SEND, $file->filetitle, $user->fullname).'<br />';
					else {
						$screenlink = $this->repository->RemositoryFunctionURL('startdown', $file->id).$file->filetitle.'</a>';
						$maillink = $this->repository->RemositoryBasicFunctionURL('startdown', $file->id);
						$mailtext .= sprintf(_DOWN_EMAIL_NOTIFY, $file->filetitle, $maillink)."\n\n";
						$screentext .= sprintf(_DOWN_ATTEMPTING_MAIL, $file->filetitle, $user->fullname, $screenlink).'<br />';
					}
				}
				echo $screentext;
				if ($mailtext) {
					$mailtext = _DOWN_EMAIL_NOTIFY_PREFIX."\n\n".$mailtext."\n\n"._DOWN_EMAIL_NOTIFY_POSTFIX;
					if ($user->sendMailTo ($myemail, _DOWN_LINKS_TO_FILES, $mailtext, $this->remUser->fullname)) {
						echo sprintf(_DOWN_EMAIL_SUCCESSFUL, $user->fullname).'<br />';
					}
					else echo sprintf(_DOWN_EMAIL_FAILED, $user->fullname, $user->email).'<br />';
				}
			}
		}
		else echo _DOWN_NOTHING_TO_DO;
	}
}
