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

class remository_savethumb_Controller extends remositoryUserControllers {

	function savethumb ($func) {
		$file = $this->createFile();
		if ($file->updatePermitted($this->remUser)) {
			$thumbnails = new remositoryThumbnails($file);
			$maxcount = $thumbnails->getMaxCount();
			for ($i = 0; $i < $maxcount; $i++) {
				if (isset($_POST['delete_thumb_'.$i])) $thumbnails->deleteThumbFile($i);
			}
			if ($thumbnails->getFreeCount() AND !empty($_FILES['userfile']['tmp_name'])) {
				$upload = new remositoryPhysicalFile();
				$upload->handleUpload();
				$thumbtext = remositoryRepository::getParam($_REQUEST, 'thumbtext');
				$thumbnails->addImage($upload, $file->id, strip_tags($thumbtext));
			}
			$view = remositoryUserHTML::viewMaker('FileInfoHTML', $this);
			$view->fileinfoHTML($file, 0);
		}
		else {
			$view = remositoryUserHTML::viewMaker('ErrorDisplaysHTML', $this);
			$view->noaccess($file);
		}
	}

}
