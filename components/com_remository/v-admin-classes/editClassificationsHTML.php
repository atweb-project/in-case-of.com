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

class editClassificationsHTML extends remositoryAdminHTML {

	function chooseType ($type) {
		// $regcheck = 'Region' == $type ? 'selected="selected"' : '';
		// <option value="Region" $regcheck>Region</option>
		$repository = remositoryRepository::getInstance();
		$types = explode(',', $repository->Classification_Types);
		$optionhtml = '';
		foreach ($types as $onetype ) {
			$onetype = trim($onetype);
			$check = $onetype == $type ? 'selected="selected"' : ''; 
			$optionhtml .= <<<TYPE_CHOICE
		
			<option value="$onetype" $check>$onetype</option>
TYPE_CHOICE;

		}
		return <<<TYPE_CHOICE

		<select name="type" >
			$optionhtml
		</select>

TYPE_CHOICE;

	}

	function selectList ($title, $selector) {
		$this->inputTop ($title, false);
		?>
			<td valign="top">
				<?php echo $selector; ?>
			</td>
		</tr>
		<?php
	}

	function hiddenBox (&$object) {
		$checked = (is_object($object) AND !@$object->hidden) ? "checked='checked'" : '';
		$heading = _DOWN_DISPLAY_LISTS;
		echo <<<HIDDEN_BOX

				<tr>
					<td width="30%" align="right">
				  		<b>$heading</b>&nbsp;
					</td>
					<td>
				  		<input type='checkbox' name='hidden' value='0' $checked />
					</td>
				</tr>

HIDDEN_BOX;

	}

	function startForm () {
		$tabclass_arr = $this->repository->getTableClasses();
		echo <<<START_FORM
		<form method="post" name="adminForm" action="{$this->interface->indexFileName()}" enctype="multipart/form-data">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="{$tabclass_arr[0]}">
START_FORM;

	}

	function view (&$classification) {
		$this->commonScripts('description');
		$this->startForm();
		$this->publishedBox($classification);
		$this->fileInputBox(_DOWN_CLASSIFICATION_NAME.':', 'name', $classification->name, 50);
		$this->hiddenBox($classification);
		$this->selectList(_DOWN_TYPE.':', $this->chooseType($classification->type));
		$this->fileInputBox(_DOWN_FREQUENCY.':','frequency',$classification->frequency,20);
		$this->fileInputArea(_DOWN_DESC, _DOWN_DESC_MAX, 'description', $classification->description, 50, 100, true);
		$this->editFormEnd ($classification->id, 0);
	}

}
