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

class remositoryThumbUpdateHTML extends remositoryCustomUserHTML {

	public function thumbUpdateHTML (&$file) {
		$interface = remositoryInterface::getInstance();
		$Itemid = $interface->getCurrentItemid();
		$file->showCMSPathway();
		echo $this->pathwayHTML($file->getContainer());
		$thumbnails = new remositoryThumbnails($file);
		if ($file->published==1) $pub=_YES;
		else $pub=_NO;
		// Change for multiple repositories
		// $formurl = $interface->sefRelToAbs("index.php?option=com_remository&repnum=$this->repnum&Itemid=".$Itemid.'&func=savethumb');
		$formurl = $interface->sefRelToAbs("index.php?option=com_remository&Itemid=".$Itemid.'&func=savethumb');
		?>
		<form name="adminForm" enctype="multipart/form-data" action="<?php echo $formurl; ?>" method="post">
		<div id='remositorythumbupdate'>
		<h2><?php echo _DOWN_UPDATE_THUMBNAILS; ?></h2>
			<input type="hidden" name="repnum" value="<?php echo $this->repnum; ?>" />
			<input type='hidden' name='id' value='<?php echo $file->id; ?>' />
		<?php
		$url = $this->repository->RemositoryFunctionURL('fileinfo',$file->id).$this->show($file->filetitle).'</a>';
		$this->fileOutputBox(_DOWN_FILE_TITLE, $url, false);
		if ($this->remUser->isAdmin()) $this->fileOutputBox (_DOWN_PUB, $pub);
		if ($file->description<>'') $this->fileOutputBox (_DOWN_DESC, $file->description, false);
		$can_delete = $thumbnails->displayAllThumbnailsDeletable();
		$this->fileOutputBox('', $can_delete, false);
		if ($thumbnails->getCount() < $thumbnails->getMaxCount()) {
			?>
			<p>
				<label for='userfile'> </label>
				<input class='inputbox' name='userfile' type='file' />
			</p>
			<p>
				<label for='thumbtext'>Legend:</label>
				<input class='inputbox' id='thumbtext' name='thumbtext', type='text', size='25' />
			</p>
			<p>
				<input class='button' type='submit' name='submit' value='<?php echo _DOWN_SUBMIT_NEW_THUMBNAIL; ?>' />
			</p>
			<?php
		}
		echo "\n\t<!-- End of remositorythumbupdate -->";
		echo "\n\t</div>";
		echo "\n\t</form>";
	}
}