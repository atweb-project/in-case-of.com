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

class editFilesHTML extends remositoryAdminHTML {

	private function showCustomFields ($custom_values, $customnames) {
		if (empty($customnames)) return '';
		$values = $custom_values ? unserialize(base64_decode($custom_values)) : array();
		$html = '';
		foreach ($customnames as $name=>$info) {
			$currentvalue = isset($values[$name]) ? $values[$name] : null;
			if (!empty($info['values'])) {
				$options = explode(',', $info['values']);
				$optionhtml = '';
				foreach ($options as $i=>$option) {
					$option = trim($option);
					$optionhtml .= <<<CUSTOM_OPTION

						<option value="$i" {$this->isSelected($currentvalue, $option)}>$option</option>
CUSTOM_OPTION;

				}
				$html .= <<<OPTION_HTML

				<p>
					<label for="$name">{$info['title']}: </label>
					<select id="$name" name="$name" class="inputbox">
						$optionhtml
					</select>
				</p>

OPTION_HTML;

			}
			else {
				$html .= <<<CUSTOM_INPUT

				<p>
					<label for="$name">{$info['title']}: </label>
					<input name="$name" id="$name" class="inputbox" type="text" value="$currentvalue" />
				</p>

CUSTOM_INPUT;

			}
		}
		return $this->fieldset(_DOWN_CUSTOM_FIELDS_HEAD, $html);
	}

	private function isSelected ($value, $option) {
		if ($value == $option) return 'selected="selected"';
	}

	private function showClassification ($fileid) {
		$classfnlist = remositoryClassification::classfnList($fileid);
		$html = '';
		foreach ($classfnlist as $type=>$clist) {
			$html .= <<<CLASSN_LIST
			<tr>
			<td width="30%" valign="top" align="right"><b>$type:</b></td>
			<td align="left" valign="top">$clist</td>
			</tr>
CLASSN_LIST;
		}
		if ($html) $classnhtml = <<<CLASSN_HTML

		<table>
			$html
		</table>

CLASSN_HTML;

		if (!empty($classnhtml)) return $this->fieldset(_DOWN_CLASSIFICATIONS, $classnhtml);
	}

	private function showPublishing ($file) {
		$html = $this->simpleCheckBox ($file, 'published', _DOWN_PUB);
		$html .= $this->simpleInputBox(_DOWN_PUBLISH_FROM,'publish_from',$file->publish_from,25);
		$html .= $this->simpleInputBox(_DOWN_PUBLISH_TO,'publish_to',$file->publish_to,25);
		return $this->fieldset(_DOWN_PUBLISHING, $html);
	}

	private function showFeaturing ($file) {
		$html = $this->simpleCheckBox ($file, 'featured', _DOWN_IS_FEATURED);
		$html .= $this->simpleInputBox(_DOWN_FEATURE_START,'featured_st_date',$file->featured_st_date,25);
		$html .= $this->simpleInputBox(_DOWN_FEATURE_END,'featured_end_date',$file->featured_end_date,25);
		return $this->fieldset(_DOWN_FEATURED, $html);
	}

	private function showMetadata ($file) {
		$html = $this->simpleInputBox(_DOWN_KEYWORDS,'keywords',$file->keywords,50);
		$html .= $this->simpleInputBox(_DOWN_WINDOW_TITLE,'windowtitle',$file->windowtitle,50);
		return $this->fieldset(_DOWN_METADATA, $html);
	}

	private function showLicense ($file) {
		$html = $this->simpleCheckBox ($file, 'licenseagree', _DOWN_LICENSE_AGREE);
		$html .= $this->simpleInputArea(_DOWN_LICENSE, _DOWN_DESC_MAX, 'license', $file->license, 6, 44, false);
		return $this->fieldset(_DOWN_LICENSE_HEADING, $html);
	}

	private function showAboutFile ($file) {
		$html = $this->simpleInputBox(_DOWN_FILE_AUTHOR,'fileauthor',$file->fileauthor,25);
		$html .= $this->simpleInputBox(_DOWN_DOWNLOADS,'downloads',$file->downloads,25);
		$html .= $this->simpleInputBox(_DOWN_FILE_DATE,'filedate',$file->filedate,25);
		$html .= $this->simpleInputBox(_DOWN_FILE_SIZE,'filesize',$file->filesize,25);
		$html .= $this->simpleInputBox(_DOWN_FILE_TYPE,'filetype',$file->filetype,25);
		$html .= $this->simpleInputBox(_DOWN_FILE_HOMEPAGE,'filehomepage',$file->filehomepage,25);
		return $this->fieldset(_DOWN_ABOUT_FILE, $html);
	}

	public function view ($file, $customnames, $comments=array())
	{
        if (!$this->clist) {
            echo _DOWN_FILE_SUBMIT_NOCHOICES;
            return;
        }
		$interface = remositoryInterface::getInstance();
		$live_site = $interface->getCfg('live_site');
		$orphanpath = base64_decode(remositoryRepository::getParam($_REQUEST, 'orphanpath', ''));
		$containerid = isset($file->containerid) ? $file->containerid : '';
		$oldid = isset($file->oldid) ? $file->oldid : '';
		if (!realpath($orphanpath)) $orphanpath = '';
		$iconList = remositoryFile::getIcons();
		$this->commonScripts('description');
		if (!defined('_ALIRO_IS_PRESENT')) $formstart = <<<FORM_START

		<form method="post" name="adminForm" action="{$this->interface->indexFileName()}" enctype="multipart/form-data">

FORM_START;

		else $formstart = '';

		//$heading = '<span class="sectionname">&nbsp;'._DOWN_REMOSITORY.' '._DOWN_EDIT_FILE.' </span><span class="small">(ID='.$file->id.')</span>';
		$heading = _DOWN_REMOSITORY.' '._DOWN_EDIT_FILE.' <span class="small">(ID='.$file->id.')</span>';
		$loctext = _DOWN_SUGGEST_LOC;
		$leftcontents = $this->narrowInputBox(_DOWN_FILE_TITLE, 'filetitle', $file->filetitle, 50);
		$leftcontents .= $this->narrowInputBox(_DOWN_SUBTITLE, 'subtitle', $file->subtitle, 50);
		$leftcontents .= $this->narrowInputBox(_DOWN_FILE_VER,'fileversion',$file->fileversion,15);
		$leftcontents .= $this->narrowInputBox(_DOWN_PRICE_LABEL,'price',$file->price,15);
		$leftcontents .= $this->simpleInputArea(_DOWN_DESC, _DOWN_DESC_MAX, 'description', $file->description, 50, 100, true);
		$leftcontents .= $this->showCustomFields($file->custom_values, $customnames);
		$leftcontents .= $this->showClassification($file->id);
		if ($this->repository->Display_FileIcons) $leftcontents .= $this->simpleIcons($file, $iconList);
		if ($this->repository->Max_Thumbnails == 0) $leftcontents .= $this->simpleInputBox(_DOWN_SCREEN,'screenurl',$file->screenurl,75);

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
			<div>

MAIN_DIV;

		echo $this->showPublishing($file);
		echo $this->showFeaturing($file);
		echo $this->showMetadata($file);
		echo $this->showLicense($file);
		echo $this->showAboutFile($file);
		$orphancoded = base64_encode($orphanpath);
		//echo $this->showAccessControl($file, $roleselect);

		echo <<<END_PAGE

				<input type="hidden" name="cfid" value="$file->id" />
				<input type="hidden" name="limit" value="$this->limit" />
				<input type="hidden" name="option" value="com_remository" />
				<input type="hidden" name="repnum" value="$this->repnum" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="act" value="$this->act" />
				<input type="hidden" name="islocal" value="$file->islocal" />
				<input type="hidden" name="oldid" value="$oldid" />
				<input type="hidden" name="oldcontainerid" value="$containerid" />
				<input type="hidden" name="orphanpath" value="$orphancoded" />
				<input type="hidden" name="filetemp" />
			</div>
		</div>
		<!-- End of remositoryedit -->

END_PAGE;

		if (!defined('_ALIRO_IS_PRESENT')) echo '</form>';
		$tooltip = <<<TOOL_TIP

<script type="text/javascript" src="$live_site/includes/js/wz_tooltip.js"></script>

TOOL_TIP;

		$interface->addCustomHeadTag($tooltip);
	}
}
