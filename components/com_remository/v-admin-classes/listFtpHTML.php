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

class listFtpHTML extends remositoryAdminHTML {
	
	public function view () {
		$iconList = remositoryFile::getIcons ();
		$this->formStart(_DOWN_BULK_ADD_FILES);
		echo <<<UNDER_HEADING

		<tr>
			<td colspan="2">
				<div class="remositoryblock">&nbsp;</div>
			</td>
		</tr>

UNDER_HEADING;
		$this->containerSelectBox();
		$this->fileInputBox(_DOWN_ACCEPTABLE_EXTENSIONS, 'extensionlist', '', 50);
		$this->simpleTableTickBox(_DOWN_EXTENSION_IN_TITLE, 'extensiontitle');
		$this->fileInputArea(_DOWN_LICENSE, _DOWN_DESC_MAX, 'license', '', 4, 50, false);
		$this->simpleTableTickBox(_DOWN_LICENSE_AGREE, 'licenseagree');
		$this->fileInputBox(_DOWN_FILE_VER,'fileversion','',25);
		$this->fileInputBox(_DOWN_FILE_AUTHOR,'fileauthor','',25);
		$this->fileInputBox(_DOWN_FILE_HOMEPAGE,'filehomepage','',50);
		$this->displayIcons(null, $iconList);
		?>
		<input type="hidden" name="option" value="com_remository" />
		<input type="hidden" name="repnum" value="<?php echo $this->repnum; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="act" value="<?php echo $_REQUEST['act']; ?>" />
		</table>
		</form>
		<?php
	}
}