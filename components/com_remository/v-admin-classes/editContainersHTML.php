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

class editContainersHTML extends remositoryAdminHTML {

	function showPublishing ($container) {
		return $this->fieldset(_DOWN_PUBLISHING, $this->simpleCheckBox ($container, 'published', _DOWN_PUB));
	}

	function showMetadata ($container) {
		return $this->fieldset(_DOWN_METADATA,
			$this->simpleInputBox(_DOWN_KEYWORDS,'keywords',$container->keywords,50).
			$this->simpleInputBox(_DOWN_WINDOW_TITLE,'windowtitle',$container->windowtitle,50)
		);
	}

	function showStorage ($container) {
		return $this->fieldset(_DOWN_STORAGE,
			$this->simpleInputBox(_DOWN_UP_ABSOLUTE_PATH,'filepath',$container->filepath,50).
			$this->yesNoRadio (null, 'inheritpath', _DOWN_INHERIT).
			$this->simpleCheckBox($container, 'plaintext', _DOWN_UP_PLAIN_TEXT)
		);
	}

	function showAccessControl ($container, $roleselect, $submanagers) {
		return $this->fieldset(_DOWN_ACCESS_CONTROL,
			($submanagers ?  $this->yesNoRadio ($container, 'countdown', _DOWN_COUNT_DOWN).
				$this->yesNoRadio ($container, 'childcountdown', _DOWN_COUNT_DOWN_CHILD).
				$this->yesNoRadio ($container, 'countup', _DOWN_COUNT_UP).
				$this->yesNoRadio ($container, 'childcountup', _DOWN_COUNT_UP_CHILD) : '').
			$this->oneAccessSelector ($roleselect, 'download', _DOWN_DOWNLOAD_ROLES).
			$this->oneAccessSelector ($roleselect, 'upload', _DOWN_UPLOAD_ROLES).
			$this->oneAccessSelector ($roleselect, 'edit', _DOWN_EDIT_ROLES).
			$this->oneAccessSelector ($roleselect, 'selfApprove', _DOWN_APPROVE_ROLES).
			$this->yesNoRadio (null, 'inherit', _DOWN_INHERIT)
		);
	}

	function oneAccessSelector ($roleselect, $type, $title) {
		$select = $roleselect[$type];
		return <<<ACCESS_SELECTOR

		<div class="remositoryaccessselector">
		<fieldset>
			<legend>$title</legend>
			<div>
				$select
			</div>
			{$this->newRole($type)}
		</fieldset>
		</div>

ACCESS_SELECTOR;

	}

	private function newRole ($type) {
		$newrole = _DOWN_ADD_NEW_ROLE;
		if (!remositoryRepository::getInstance()->Use_CMS_Groups) return <<<NEW_ROLE

			<div>
				<label for="new_role_$type">$newrole</label>
				<input class="inputbox" type="text" name="new_role_$type" id="new_role_$type" />
			</div>

NEW_ROLE;

	}

	function view (&$container, $roleselect, $subsinfo)	{
		$iconList = remositoryContainer::getIcons ();
		$this->commonScripts('description');

		if (!defined('_ALIRO_IS_PRESENT')) $formstart = <<<FORM_START

		<form method="post" name="adminForm" action="{$this->interface->indexFileName()}" enctype="multipart/form-data">

FORM_START;

		else $formstart = '';

		//$heading = '<span class="sectionname">&nbsp;'._DOWN_REMOSITORY.' '._DOWN_EDIT_CONTAINER.' </span><span class="small">(ID='.$container->id.')</span>';
		$heading = _DOWN_REMOSITORY.' '._DOWN_EDIT_CONTAINER.' <span class="small">(ID='.$container->id.')</span>';
		$loctext = _DOWN_SUGGEST_LOC;
		$leftcontents = $this->narrowInputBox(_DOWN_FOLDER_NAME, 'name', $container->name, 50).
			$this->narrowInputBox(_DOWN_ALIAS.':', 'alias', $container->alias, 50).
			$this->simpleInputArea(_DOWN_DESC, _DOWN_DESC_MAX, 'description', $container->description, 50, 100, true);
		if ($this->repository->Display_FolderIcons) $leftcontents .= $this->simpleIcons($container, $iconList);
		$site = remositoryInterface::getInstance()->getCfg('live_site');

		echo <<<MAIN_DIV

		<div id="remositoryedit">
		$formstart
			<table class="adminheading">
	        {$this->interface->adminPageHeading($heading, 'remository')}
            </table>
		<div class="remositoryblock">&nbsp;</div>
		<strong>$loctext</strong>
		<div id="remositoryclist">
			$this->clist
		</div>
		<div id="remositorycontainermain">
			$leftcontents
		</div>

MAIN_DIV;

		echo $this->showPublishing($container);
		echo $this->showMetadata($container);
		echo $this->showStorage($container);
		echo $this->showAccessControl($container, $roleselect, $subsinfo);

		echo <<<END_PAGE

		<input type="hidden" name="cfid" value="$container->id" />
		<input type="hidden" name="limit" value="$this->limit" />
		<input type="hidden" name="oldpath" value="$container->filepath" />
		<input type="hidden" name="option" value="com_remository" />
		<input type="hidden" name="repnum" value="$this->repnum" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="act" value="$this->act" />

END_PAGE;

		if (!defined('_ALIRO_IS_PRESENT')) echo '</form>';
		echo "\n\t</div>";
	}
}
