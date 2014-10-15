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

class remositoryAddFileHTML extends remositoryCustomUserHTML {
	private $remoteok = false;

	protected function fileInputBox ($title, $name, $value, $width) {
		return <<<INPUT_BOX

			<p>
				<label for="$name">$title</label>
				<input class="inputbox" type="text" id="$name" name="$name" size="$width" value="$value" />
			</p>

INPUT_BOX;

	}

	protected function fileInputArea ($title, $maxsize, $name, $value, $rows, $cols, $editor) {
		return <<<INPUT_AREA

				<p>
					<label for="$name">
						$title
						{$this->showMaxSize($maxsize)}
					</label>
				</p>
				{$this->showEditorArea($editor, $value, $name, $rows, $cols, $maxsize)}

INPUT_AREA;

	}

	protected function showMaxSize ($maxsize, $paragraph=false) {
		if ($maxsize) {
			if ($paragraph) return <<<MAX_SIZEP

				<p class="remositorymax"><em>$maxsize</em></p>

MAX_SIZEP;

			else return <<<MAX_SIZE

					<em>$maxsize</em>

MAX_SIZE;

		}
	}

	protected function showEditorArea ($editor, $value, $name, $rows, $cols, $maxsize) {
		$interface = remositoryInterface::getInstance();
		if ($editor) return <<<EDITOR_AREA

				<div id='remositoryeditor'>
					{$interface->editorAreaText('description', $value, $name, 500, 200, $rows, $cols)}
				</div>

EDITOR_AREA;

		else return <<<TEXT_AREA

					<textarea class="inputbox" id="$name" name="$name" rows="$rows" cols="$cols">$value</textarea>
				</p>

TEXT_AREA;

	}

	protected function uploadFileBox ($title, $suffix='') {
		return <<<UPLOAD_BOX

				<p>
					<label for="userfile$suffix">
						$title
					</label>
					<input class="text_area" type="file" id="userfile$suffix" name="userfile$suffix" />
				</p>

UPLOAD_BOX;

	}

	protected function tickBoxField ($object, $property, $title) {
		$checked = (is_object($object) AND $object->$property) ? "checked='checked'" : '';
		return <<<TICK_BOX

				<p>
					<label for="$property">$title</label>
					<input type="checkbox" id="$property" name="$property" value="1" $checked />
				</p>

TICK_BOX;

	}

	protected function autoShortHandling ($file) {
		if ($file->autoshort) return <<<WITH_SHORT

				<p>
					<label for="autoshort">
						{$this->show(_DOWN_AUTO_SHORT)}
					</label>
					<input type="checkbox" name="autoshort" id="autoshort" checked="checked" onclick="clearshort()" value='1' />
					<script type="text/javascript">clearshort()</script>
				</p>

WITH_SHORT;

		else return <<<NO_SHORT

				<p>
					<label for="autoshort">
						{$this->show(_DOWN_AUTO_SHORT)}
					</label>
					<input type="checkbox" name="autoshort" id="autoshort" onclick="clearshort()" value="1" />
				</p>

NO_SHORT;

	}

	protected function displayIcons ($object, $iconList) {
		$currenticon = is_object($object) ? $object->icon : '';
		if ($this->repository->Display_FileIcons) return <<<SHOW_ICONS

		<script type="text/javascript">
		function paste_strinL(strinL){
			var input=document.forms["adminForm"].elements["icon"];
			input.value=strinL;
		}
		</script>
		<p id="remositoryiconlist">
			<label for="icon">
				{$this->show(_DOWN_ICON)}
			</label>
			<input class="inputbox" type="text" name="icon" id="icon" size="25" value="$currenticon" />
			<div class="icons">
				$iconList
			</div>
  		</p>

SHOW_ICONS;

	}

	protected function addManyFiles () {
		if (remositoryInterface::getInstance()->getClassFilePath('remository_addmanyfiles_Controller')) {
			$startlink = $this->repository->RemositoryBasicFunctionURL('addmanyfiles');
			return <<<ADD_MANY

				<a class="right" href="$startlink">
					{$this->show(_DOWN_ADD_NUMBER_FILES)}</a>
				</a>

ADD_MANY;

		}
	}

	protected function instructionsAndBox ($file) {
		$this->remoteok = ($this->remUser->isAdmin() OR ($this->repository->User_Remote_Files)) ? true : false;
		if ($this->remoteok) return <<<REMOTE_OK

		<div id="remositoryuplocal">
			<p class="instructions">
				{$this->show(_SUBMIT_INSTRUCT1)}
			</p>
			{$this->uploadFileBox(_SUBMIT_NEW_FILE)}
		</div>
		<div id="remositoryupremote">
			<p class="instructions">
				{$this->show(_SUBMIT_INSTRUCT2)}
			</p>
			{$this->fileInputBox(_DOWNLOAD_URL, 'url', ($file->url ? $file->url : 'http://'), 50)}
			{$this->fileInputBox(_DOWN_FILE_DATE,'filedate',$file->filedate,25)}
			{$this->fileInputBox(_DOWN_FILE_SIZE,'filesize',$file->filesize,25)}
		</div>

REMOTE_OK;

		else return <<<NO_REMOTE

		<div id="remositoryuplocal">
			<p>
				{$this->show(_SUBMIT_INSTRUCT3)}
			</p>
			{$this->uploadFileBox(_SUBMIT_NEW_FILE)}
		</div>

NO_REMOTE;

	}

	protected function thumbnailBoxes ($thumbs) {
		$freecount = $thumbs->getFreeCount();
		$html = '';
		if ($freecount) for ($i = 0; $i < $freecount; $i++) {
			$html .= $this->uploadFileBox(sprintf(_DOWN_ADDFILE_THUMBNAIL ,$i+1), $i+1);
		}
		return $html;
	}

	protected function oldStyleThumb ($file, $thumbs) {
		if (0 == $thumbs->getMaxCount()) return $this->fileInputBox(_DOWN_SCREEN,'screenurl',$file->screenurl,50);
	}

	protected function showCustomizable ($file) {
		$customobj = new remositoryCustomizer();
		$fieldnames = $customobj->getFileListFields();
		$showadminonly = explode(',', _REMOSITORY_SHOW_ADMIN_ONLY);
		$showadminonly = array_map('trim', $showadminonly);
		$customcontrol = $customobj->getUserCustomSpec();
		foreach ($customcontrol['S'] as $key=>$sequence) $reseq[$sequence][] = $key;
		$html = '';
		if (isset($reseq)) {
			ksort($reseq);
			foreach ($reseq as $kset) foreach ($kset as $key) {
				if (!empty($customcontrol['E'][$key]) OR ($this->remUser->isAdmin() AND in_array($fieldnames[$key][0], $showadminonly))) {
					$fieldname = $fieldnames[$key][0];
					$method = 'show_'.$fieldname;
					if (method_exists($this, $method)) $html .= $this->$method($file);
				}
			}
		}
		return $html;
	}

	protected function show_description ($file) {
		return $this->fileInputArea(_DOWN_DESC, _DOWN_DESC_MAX, 'description', $file->description, 10, 50, true);
	}

	protected function show_smalldesc ($file) {
		return $this->fileInputArea(_DOWN_DESC_SMALL, _DOWN_DESC_SMALL_MAX, 'smalldesc', $file->smalldesc, 3, 50, false)
		.$this->autoShortHandling($file);
	}

	protected function show_license ($file) {
		return $this->fileInputArea(_DOWN_LICENSE, _DOWN_DESC_MAX, 'license', $file->license, 4, 50, false)
		.$this->tickBoxField($file, 'licenseagree', _DOWN_LICENSE_AGREE);
	}

	protected function show_fileversion ($file) {
		return $this->fileInputBox(_DOWN_FILE_VER,'fileversion',$file->fileversion,25);
	}

	protected function show_fileauthor ($file) {
		return $this->fileInputBox(_DOWN_FILE_AUTHOR,'fileauthor',$file->fileauthor,25);
	}

	protected function show_filehomepage ($file) {
		return $this->fileInputBox(_DOWN_FILE_HOMEPAGE,'filehomepage',$file->filehomepage,50);
	}

	protected function show_price ($file) {
		return $this->fileInputBox(_DOWN_PRICE_LABEL,'price',$file->price,50);
	}

	public function addfileHTML ($clist, $file) {
		if ($clist == '') {
			echo $this->pathwayHTML(null);
			echo _DOWN_FILE_SUBMIT_NOCHOICES;
			return;
		}

		$this->addFileScripts();
		if (!$this->remUser->isLogged()) remositoryInterface::getInstance()->initEditor();

		$formurl = remositoryRepository::getInstance()->RemositoryBasicFunctionURL('savefile');
		$iconList = remositoryFile::getIcons();
		$thumbs = new remositoryThumbnails($file);

		echo <<<ADD_FILE
		
		{$this->pathwayHTML()}
		<div id="remositoryAddFile">
		<form name="adminForm" enctype="multipart/form-data" action="$formurl" method="post">
			<div id="remositoryupload">
				<input type="hidden" name="repnum" value="$this->repnum" />
				<input type="hidden" name="oldid" value="$file->id" />
				{$this->addManyFiles()}
				<h2>{$this->show(_SUBMIT_HEADING)}</h2>
				{$this->instructionsAndBox($file)}
				<div id="remositoryuploadinfo">
					{$this->thumbnailBoxes($thumbs)}
					<dl>
						{$this->fileOutputBoxText(_DOWN_SUGGEST_LOC, $clist, false)}
					</dl>
					{$this->newContainer()}
					{$this->fileInputBox(_DOWN_FILE_TITLE,'filetitle',$file->filetitle,25)}

					{$this->showCustomizable($file)}

					{$this->oldStyleThumb($file, $thumbs)}
					{$this->displayIcons($file, $iconList)}
					<input class="button first" type="submit" name="submit" value="{$this->show(_SUBMIT_FILE_BUTTON)}" />
					<input class="button" type="submit" name="submit" value="{$this->show(_DOWN_CANCEL_UPLOAD)}" />
				</div>
			</div>
		</form>
		</div>
		<div class="clr">
		</div>

ADD_FILE;

	}

	protected function newContainer () {
		if (_REMOSITORY_UPLOAD_CREATE_FOLDER) return $this->fileInputBox(_DOWN_UPLOAD_NEW_FOLDER, 'containername', '', 25);
	}

	protected function addFileScripts () {
		$interface = remositoryInterface::getInstance();
		$maxlength = $this->repository->Small_Text_Len-3;
		echo <<<FILE_SCRIPTS

		<script type="text/javascript">
		function clearshort(){

				if (document.adminForm.autoshort.checked==true){
					if (document.adminForm.description.value!=""){
						if (document.adminForm.description.value.length>=($maxlength)){
							document.adminForm.smalldesc.value=document.adminForm.description.value.substr(0,$maxlength) + "...";
						} else {
							document.adminForm.smalldesc.value=document.adminForm.description.value;
						}
					} else {
						document.adminForm.smalldesc.value="";
					}
					document.adminForm.smalldesc.disabled=true;
				} else {
					document.adminForm.smalldesc.value="";
					document.adminForm.smalldesc.disabled=false;
				}
			}
		</script>

FILE_SCRIPTS;

		?>
		<script type="text/javascript">
        function submitbutton(pressbutton) {
                <?php $interface->getEditorContents( 'description' ); ?>
                submitform( pressbutton );
        }
        </script>
		<?php
	}

}
