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

class remositoryAddManyFilesHTML extends remositoryCustomUserHTML {

	protected function fileInputBox ($title, $name, $value, $width, $tooltip=null) {
		echo "\n\t\t\t<p>";
		echo "<label for='$name'>$title</label>";
		if ($tooltip) echo $this->tooltip($tooltip);
		echo "\n\t\t\t\t<input class='inputbox' type='text' id='$name' name='$name' size='$width' value='$value' />";
		echo "\n\t\t\t</p>";
	}

	protected function fileInputArea ($title, $maxsize, $name, $value, $rows, $cols, $editor) {
		echo "\n\t\t\t\t<p><label for='$name'>".$title;
		echo '</label>';
		if ($editor) {
			if ($maxsize) echo '<em>'.$maxsize.'</em>';
			echo "\n\t\t\t</p><div id='remositoryeditor'>";
			$interface = remositoryInterface::getInstance();
			$interface->editorArea( 'description', $value, $name, 500, 200, $rows, $cols );
			echo "\n\t\t\t</div>";
		}
		else {
			echo "<textarea class='inputbox' id='$name' name='$name' rows='$rows' cols='$cols'>$value</textarea>";
			echo '</p>';
			if ($maxsize) echo "<p class='remositorymax'><em>".$maxsize.'</em></p>';
		}
	}

	protected function submitEachFile ($i) {
		echo "\n\t\t<p>";
		echo "<label for='userfile$i'>"._SUBMIT_NEW_FILE.'</label>';
		echo "\n\t\t\t\t<input class='text_area' type='file' id='userfile$i' name='userfile$i' />";
		echo "\n\t\t\t<label for='userfile$i'>"._DOWN_FILE_TITLE.'</label>';
		echo "\n\t\t\t<input class='inputbox' id='filetitle$i' name='filetitle$i' type='text' size='25' />";
		echo "\n\t\t\t</p>";
	}


	protected function displayIcons ($object, $iconList) {
		if (is_object($object)) $currenticon = $object->icon;
		else $currenticon = '';
		?>
		<script type="text/javascript">
		function paste_strinL(strinL){
			var input=document.forms["adminForm"].elements["icon"];
			input.value=strinL;
		}
		</script>
		<p id='remositoryiconlist'>
			<label for='icon'><?php echo _DOWN_ICON; ?></label>
			<input class="inputbox" type="text" name="icon" id='icon' size="25" value="<?php echo $currenticon; ?>" />
			<div><?php echo $iconList; ?></div>
  		</p>
  		<?php
	}

	public function addManyFilesHTML($clist)
	{
		echo $this->pathwayHTML(null);
		if ($clist == '') {
			echo _DOWN_FILE_SUBMIT_NOCHOICES;
			return;
		}
		$formurl = remositoryRepository::getInstance()->RemositoryBasicFunctionURL('savemanyfiles');
		echo "\n\t<form id='adminForm' enctype='multipart/form-data' action='$formurl' method='post'>";
		echo "\n\t<div id='remositoryaddmany'>";
		echo <<<HIDDEN

			<input type="hidden" name="option" value="com_remository" />
			<input type="hidden" name="repnum" value="$this->repnum" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="element" value="component" />
			<input type="hidden" name="client" value="" />

HIDDEN;

		$iconList = remositoryFile::getIcons();
		echo "\n\t\t<h2>"._SUBMIT_HEADING.'</h2>';
		echo "\n\t\t<div id='remositorymanycommon'>";
		echo "\n\t\t<p>"._SUBMIT_INSTRUCT3.'</p>';
		$this->fileInputBox(_DOWN_FILE_DATE,'filedate',date('Y-m-d H:i:s', time()),25);
		echo "\n\t\t<dl>";
		$this->fileOutputBox(_DOWN_SUGGEST_LOC, $clist, false);
		echo "\n\t\t</dl>";
		// echo $this->simpleTickBox(_DOWN_EXTENSION_IN_TITLE, 'extensiontitle');
		$this->fileInputArea(_DOWN_LICENSE, _DOWN_DESC_MAX, 'license', '', 4, 50, false);
		echo $this->simpleTickBox(_DOWN_LICENSE_AGREE, 'licenseagree');
		$this->fileInputBox(_DOWN_FILE_VER,'fileversion','',25);
		$this->fileInputBox(_DOWN_FILE_AUTHOR,'fileauthor','',25);
		$this->fileInputBox(_DOWN_FILE_HOMEPAGE,'filehomepage','',50);
		if ($this->repository->Display_FileIcons) $this->displayIcons(null, $iconList);
		echo "\n\t\t</div>";
		echo "\n\t\t<div id='remositorymanyfiles'>";
		for ($i=0; $i<30; $i++) {
			$this->submitEachFile($i);
		}
		echo "\n\t\t<input class='button' type='submit' name='submit' value='"._SUBMIT_FILE_BUTTON."' />";
		echo "\n\t\t</div>";
		echo "\n\t<!-- End of remositoryaddmany and the form -->";
		echo "\n\t</div></form>";
	}
}
