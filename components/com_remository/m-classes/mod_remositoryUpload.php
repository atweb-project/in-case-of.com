<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006-12 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

class mod_remositoryUpload extends mod_remositoryBase {
	private $showsmall = 0;
	private $maxchars = 100;
	private $maxfiles = 0;
	private $date_format = '%b.%g';
	private $category = 0;

	private $tabclass_arr = array();
	private $base_url = '';
	private $linecount = 0;

	public function showFileList ($module, &$content, $area, $params) {
		// Find out $Itemid
		$this->base_url = 'index.php?option=com_remository';        	// Base URL string
		$this->base_url .= '&Itemid='.$this->remos_getItemID('com_remository');
		$this->base_url .= '&func=fileinfo&id=';

		/*********************Configuration*********************/
		// Set to '1' to Show the Description, set to 0 to not show it
		$this->showsmall = $this->remos_get_module_parm($params,'showsmall',0);
		// Max number of description characters
		$this->maxchars = $this->remos_get_module_parm($params,'maxchars',100);
		// Date format for display
		$this->date_format = $this->remos_get_module_parm($params,'dateformat','%b.%g');
		// Category from which to select files
		$this->category = $this->remos_get_module_parm($params,'category', 0);
		// Number of files to show
		$this->maxfiles = max($this->remos_get_module_parm($params,'maxfiles', 0),0);
		// Number of characters of description to show
		$this->maxchars = max($this->maxchars,20);
		/*******************************************************/

		$this->tabclass_arr = explode(",",$this->repository->tabclass);

		// Get my uploads
		$myfiles = remositoryFile::uploadedFiles ($this->category, $this->maxfiles, $this->remUser);
		if ($this->category AND count($myfiles)==0) $myfiles = remositoryFile::newestFiles (0, 0, $this->remUser);

		$tabcnt = 0;
		$lines = '';

		$manager = remositoryContainerManager::getInstance();

		foreach ($myfiles as $file) {
			if (!isset($folders[$file->containerid])) {
				$container = $manager->getContainer($file->containerid);
				$fullname = is_object($container) ? $container->getAllFamilyNames() : '?';
				$foldernames[$fullname] = $file->containerid;
			}
			$folders[$file->containerid][] = $file;
		}
		if (!empty($folders)) {
			ksort($foldernames);
			foreach ($foldernames as $title=>$id) {
				$lines .= <<<FOLDER_LINE

			<tr>
				<td colspan="3">
					<strong>$title</strong>
				</td>
			</tr>

FOLDER_LINE;

				foreach ($folders[$id] as $file) {
					$lines .= $this->uploadLine($file, $tabcnt);
					$tabcnt = 1 - $tabcnt;
				}
			}
		}
		if ($lines) {
			$userlist = $this->showUserSelect();
			$content = <<<MY_UPLOADS

		<div class="remositorymyuploads">
		<form action="index.php" method="post">
		<table class="remositoryuploaded">
			$lines
		</table>
		<div>
			<div class="remositoryuserlist"
				<h3>Choose users to receive by email</h3>
				$userlist
			</div>
			<input type="submit" class="inputbox" value="Send Emails" />
			<input type="hidden" name="option" value="com_remository" />
			<input type="hidden" name="func" value="mailfiles" />
		</div>
		</form>
		</div>

MY_UPLOADS;

		}
	}

	private function uploadLine ($file, $tabcnt) {
		$html = '';
		$sdesc = '';
		if ($this->showsmall) {
			if (($file->description<>'') AND ($file->autoshort)) $sdesc.='<br/>'.strip_tags($file->description);
			elseif ($file->smalldesc<>'') $sdesc.='<br/>'.strip_tags($file->smalldesc);
			if (strlen($sdesc)>$this->maxchars) $sdesc=substr($sdesc,0,$this->maxchars-3).'...';
		}
		$curicon = $file->icon ? $file->icon : 'generic.png';
		$url = $this->interface->sefRelToAbs($this->base_url.$file->id);
		$class = $this->tabclass_arr[$tabcnt];
		$datestring = 'none' == strtolower($this->date_format) ? '' : strftime($this->date_format, strtotime($file->filedate));

		$html = <<<UPLOAD_LINE

			<tr class='$class'>
				<td>
					<a href="$url">
						{$this->repository->RemositoryImageURL('file_icons/'.$curicon,16,16)}
						$file->filetitle
					</a>
				</td>
				<td>
					$datestring
				</td>
				<td>
					$sdesc
				</td>
				<td>
					<input type="checkbox" class="inputbox" name="tomail[]" value="$file->id" />
				</td>
			</tr>

UPLOAD_LINE;

		return $html;
	}

	private function showUserSelect () {
		$this->database->setQuery("SELECT * FROM #__users WHERE block = 0 AND username NOT LIKE '%admin%' ORDER BY name");
		$users = $this->database->loadObjectList();
		$userlist = '';
		foreach ($users as $user) {
			$userlist .= <<<USER_OPTION

					<div>
						<input type="checkbox" class="inputbox" name="remositoryemailusers[$user->id]" value="1" />$user->username - $user->name
					</div>
USER_OPTION;

		}
		return $userlist;
	}
}