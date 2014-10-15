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

	private function editfileScript () {
		?>
		<script type="text/javascript">
			function clearshort(){
				if (document.adminForm.autoshort.checked==true){
					if (document.adminForm.description.value!=""){
						if (document.adminForm.description.value.length>=(<?php echo $this->repository->Small_Text_Len; ?>-4)){
							document.adminForm.smalldesc.value=document.adminForm.description.value.substr(0,<?php echo $this->repository->Small_Text_Len; ?>-4) + "...";
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
		<?php
	}

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

	private function showShortDescription ($file) {
		$html = $this->simpleCheckBox ($file, 'autoshort', _DOWN_AUTO_SHORT);
		$html .= $this->simpleInputArea(_DOWN_DESC_SMALL, _DOWN_DESC_SMALL_MAX, 'smalldesc', $file->smalldesc, 6, 44, false);
		return $this->fieldset(_DOWN_SHORT_DESCRIPTION, $html);
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
		$file->addSubmitterEmail();
		$html .= $this->showSubmitterEmail ($file);
		return $this->fieldset(_DOWN_ABOUT_FILE, $html);
	}

	private function showSubmitterEmail ($file) {
		if (isset($file->submit_email)) {
			$legend = _DOWN_SUB_ID_SORT;
			return <<<EMAIL_LINK

			<div>
				<label for="submitemail"><strong>$legend:</strong></label>
				<a id="submitemail" href="mailto:$file->submit_email">$file->submit_email</a>
			</div>

EMAIL_LINK;

		}
	}

	private function showComments ($comments, $interface, $fileid) {
		$listhtml = '';
		foreach ($comments as $comment) {
			$time = strtotime($comment->date);
			if ($this->repository->Set_date_locale) {
				setlocale(LC_TIME, $this->repository->Set_date_locale);
				$date = strftime($this->repository->Date_Format, $time);
			}
			else $date = date ($this->repository->Date_Format, $time);
			// Change for multiple repositories
			// $dlink = $interface->getCfg('admin_site')."/index2.php?option=com_remository&amp;repnum=$this->repnum&amp;act=files&amp;task=dcomment&amp;cfid=$fileid&amp;commentid=$comment->id";
			$dlink = $interface->getCfg('admin_site')."/{$this->interface->indexFileName()}?option=com_remository&amp;act=files&amp;task=dcomment&amp;cfid=$fileid&amp;commentid=$comment->id";
			$listhtml .= <<<COMMENT_LINE

			<tr>
				<td>
					<a href="$dlink">Delete</a>
				</td>
				<td>
					$date
				</td>
				<td>
					$comment->username
				</td>
				<td>
					<input type="text" class="inputbox" name="comment[$comment->id]" value="$comment->comment" size="40" />
				</td>
			</tr>

COMMENT_LINE;

		}
		if (count($comments)) {
			$date_head = _DOWN_DATE;
			$user_head = _DOWN_NAME_TITLE;
			$comment_head = _DOWN_COMMENTS;
			$html = <<<COMMENTS


		<table width="100%">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>$date_head</th>
				<th>$user_head</th>
				<th>$comment_head</th>
			</tr>
		</thead>
		<tbody>
		$listhtml
		</tbody>
		</table>

COMMENTS;

			return $this->fieldset(_DOWN_COMMENTS_HEADING, $html);
		}
	}

	private function showPhysical ($file, $orphanpath) {
		$html = '';
		if ($file->islocal) {
			if ($file->id) $html .= $this->simpleInputBox(_DOWN_REAL_NAME,'realname',$file->realname(),44);
			if (!$orphanpath) $html .= $this->simpleUploadBox(_SUBMIT_NEW_FILE, 39);
		}
		else $html .= $this->simpleInputBox(_DOWNLOAD_URL,'url',$file->url(),44);
		if (!$file->islocal) $html .= $this->showLocaliseLink($file);
		return $this->fieldset(_DOWN_PHYSICAL_FILE, $html);
	}

	private function showLocaliseLink ($file) {
		$interface = remositoryInterface::getInstance();
		$absolute_path = $interface->getCfg('absolute_path');
		$live_site = $interface->getCfg('live_site');
		$oldpath = $absolute_path.substr($file->url,strlen($live_site));
		$linktext = _DOWN_LOCALISE_REMOTE_FILE;
		if (0 === strpos($file->url,$live_site) AND file_exists($oldpath)) return <<<LOCALISE_LINK

			<p>
				<a href="$admin_site/{$this->interface->indexFileName()}?option=com_remository&act=files&task=localise&cfid=$file->id&containerid=$file->containerid">
					$linktext
				</a>
			</p>

LOCALISE_LINK;

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
		$leftcontents .= $this->showComments($comments, $interface, $file->id);
		if ($this->repository->Max_Thumbnails == 0) $leftcontents .= $this->simpleInputBox(_DOWN_SCREEN,'screenurl',$file->screenurl,75);

		$this->editfileScript();
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
		echo $this->showPhysical($file, $orphanpath);
		echo $this->showMetadata($file);
		echo $this->showShortDescription($file);
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
