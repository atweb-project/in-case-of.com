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

class remositoryBasicHTML extends remositoryHTML {
	protected $pageNav = '';
	protected $act = '';
	protected $limit = 10;
	protected $repnum = 0;

	function __construct (&$controller, $limit) {
		parent::__construct();
		$this->repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		$this->act = $_REQUEST['act'];
		$this->limit = $limit;
		$this->pageNav = $controller->pageNav;
	}

	function show ($string) {
		return $string;
	}

	function setUpCalendar () {
		$interface = remositoryInterface::getInstance();
		$live_site = $interface->getCfg('live_site');
		$links = <<<LINKS_CALENDAR

		<!-- import the calendar CSS -->
		<link rel="stylesheet" type="text/css" media="all" href="$live_site/includes/js/calendar/calendar-mos.css" title="green" />
		<!-- import the calendar script -->
		<script type="text/javascript" src="$live_site/includes/js/calendar/calendar_mini.js"></script>
		<!-- import the language module -->
		<script type="text/javascript" src="$live_site/includes/js/calendar/lang/calendar-en.js"></script>

LINKS_CALENDAR;
		$interface->addCustomHeadTag($links);
	}

	function calendarSelector ($fieldname) {
		static $first = true;
		if ($first) $html = <<<CALENDAR_FIRST

			<input name="reset" type="reset" class="button" onclick="return showCalendar('$fieldname', 'y-mm-dd');" value="..." />

CALENDAR_FIRST;

		else $html = <<<CALENDAR_AFTER

			<input type="reset" class="button" value="..." onclick="return showCalendar('$fieldname', 'y-mm-dd');" />

CALENDAR_AFTER;

		$first = false;
		return $html;
	}

	function tickBox ($object, $property) {
		if (is_object($object) AND $object->$property) $checked = "checked='checked'";
		else $checked = '';
		echo "<td><input type='checkbox' name='$property' value='1' $checked /></td>";
	}

	function yesNoList ($object, $property) {
		$yesno[] = $this->repository->makeOption( 0, _NO );
		$yesno[] = $this->repository->makeOption( 1, _YES );
		if ($object) $default = $object->$property;
		else $default = 0;
		echo '<td valign="top">';
		echo $this->repository->selectList($yesno, $property, 'class="inputbox" size="1"', $default);;
		echo '</td></tr>';
	}

	function yesNoRadio ($object, $property, $title) {
		$yes = (is_object($object) AND isset($object->$property) AND (bool) $object->$property) ? true : false;
		$yescheck = $yes ? 'checked="checked"' : '';
		$nocheck = $yes ? '' : 'checked="checked"';
		$yestext = _YES;
		$notext = _NO;

		$html = <<<INPUT_BOX

		<div>
			<label for="$property" style="width=30%"><strong>$title</strong></label>
			<input class="inputbox" type="radio" id="$property" name="$property" $yescheck value="1" />$yestext
			<input class="inputbox" type="radio" name="$property" $nocheck value="0" />$notext
		</div>
INPUT_BOX;

		return $html;
	}

	function inputTop ($title, $redstar=false, $maxsize=0) {
		?>
		<tr>
		  	<td width="30%" valign="top" align="right">
				<b><?php if ($redstar) echo '<font color="red">*</font>'; echo $title; if ($maxsize) echo "</b>&nbsp;<br /><i>$maxsize</i>&nbsp;"; ?></b>&nbsp;
			</td>
		<?php
	}

	function blankRow () {
		?>
			<tr><td>&nbsp;</td></tr>
		<?php
	}

	function fileInputBox ($title, $name, $value, $width, $tooltip=null) {
		?>
		<tr>
		  	<td width="30%" valign="top" align="right">
		  	<b><?php echo $title; ?></b>
			</td>
			<td align="left" valign="top">
				<input class="inputbox" type="text" name="<?php echo $name; ?>" size="<?php echo $width; ?>" value="<?php echo $value; ?>" />
				<?php if ($tooltip) echo $this->tooltip($tooltip); ?>
			</td>
		</tr>
		<?php
	}

	function simpleInputBox ($title, $name, $value, $width, $tooltip=null) {
		$html = <<<INPUT_BOX

		<div>
			<label for="$name" style="width=30%"><strong>$title</strong></label>
			<input id="$name" class="inputbox" type="text" name="$name" size="$width" value="$value" />
		</div>
INPUT_BOX;

		if ($tooltip) $html .= $tooltip;
		return $html;
	}

	function narrowInputBox ($title, $name, $value, $width, $tooltip=null) {
		$html = <<<INPUT_BOX

		<label for="$name"><strong>$title</strong></label>
		<div>
			<input id="$name" class="inputbox remositoryNarrow" type="text" name="$name" size="$width" value="$value" />
		</div>
INPUT_BOX;

		if ($tooltip) $html .= $tooltip;
		return $html;
	}

	function fileUploadBox ($title, $width, $tooltip=null) {
		?>
		<tr>
		  	<td width="30%" valign="top" align="right">
		  	<b><?php echo $title; ?></b>
			</td>
			<td align="left" valign="top">
				<input class="inputbox" type="file" name="userfile" size="<?php echo $width ?>" />
				<?php if ($tooltip) echo $this->tooltip($tooltip); ?>
			</td>
		</tr>
		<?php
	}

	function simpleUploadBox ($title, $width, $tooltip=null) {
		$html = <<<SIMPLE_UPLOAD

		<label for="userfile"><strong>$title</strong></label>
		<div>
			<input class="inputbox" type="file" id="userfile" name="userfile" size="$width" />
		</div>

SIMPLE_UPLOAD;

		if ($tooltip) $html .= $tooltip;
		return $html;
	}

	function fileInputArea ($title, $maxsize, $name, $value, $rows, $cols, $editor=false, $tooltip=null) {
		echo <<<FILE_INPUT

		<tr>
		  	<td width="30%" valign="top" align="right">
				<b>$title</b>&nbsp;<br />
				<i>$maxsize</i>&nbsp;
			</td>
			<td valign="top">

FILE_INPUT;

		$interface = remositoryInterface::getInstance();
		if ($editor) $interface->editorArea( 'description', $value, $name, 450, 200, $rows, $cols );
		else echo "<textarea class='inputbox' name='$name' rows='$rows' cols='$cols'>$value</textarea>";
		if ($tooltip) echo $this->tooltip($tooltip);
		echo '</td></tr>';
	}

	function simpleInputArea ($title, $maxsize, $name, $value, $rows, $cols, $editor=false, $tooltip=null) {
		$interface = remositoryInterface::getInstance();
		// last params were $rows, $cols
		ob_start();
		if ($editor) $editbox = $interface->editorAreaText( 'description', $value, $name, 450, 350, $cols, $rows );
		else $editbox = "<textarea class='inputbox' id='$name' name='$name' rows='$rows' cols='$cols'>$value</textarea>";
		$editbox .= ob_get_clean();

		return <<<INPUT_AREA

		<div class="remositoryinputarea">
			<label for="$name"><strong>$title</strong>
			<br /><em>$maxsize</em></label>
			<div>
				$editbox
				$tooltip
			</div>
		</div>

INPUT_AREA;

	}

	function tickBoxField ($object, $property, $title) {
		?>
		<tr>
			<td width="30%" valign="top" align="right">
				<b><?php echo $title; ?></b>&nbsp;
			</td>
		<?php
		if (is_object($object) AND $object->$property) $checked = "checked='checked'";
		else $checked = '';
		echo "<td><input type='checkbox' name='$property' value='1' $checked /></td>";
		echo '</tr>';
	}

	function simpleTableTickBox ($title, $name, $checked=false) {
		if ($checked) $check = 'checked="checked"';
		else $check = '';
		?>
		<tr>
			<td width="30%" valign="top" align="right">
				<b><?php echo $title; ?></b>&nbsp;
			</td>
			<td>
				<input type="checkbox" name="<?php echo $name; ?>" value="1" <?php echo $check; ?> />
			</td>
		</tr>
		<?php
	}
	
	function formStart ($title) {
		echo <<<FORM_START

		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
		<script type="text/javascript" src="../includes/js/overlib_mini.js"></script>
		<form action="{$this->interface->indexFileName()}" method="post" name="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
        {$this->interface->adminPageHeading('Remository '.$title, 'remository')}

FORM_START;

	}

	function oldFormStart ($title) {
		echo <<<FORM_START

		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
		<script type="text/javascript" src="../includes/js/overlib_mini.js"></script>
		<form action="{$this->interface->indexFileName()}" method="post" name="adminForm">
		<table cellpadding="4" cellspacing="0" border="0" width="100%">
   		<tr>
			<td width="75%" colspan="3">
				<div class="title header">
					{$this->repository->RemositoryImageURL('header.gif',64,64)}
					<span class="sectionname">&nbsp;Remository $title</span>
				</div>
			</td>
			<td width="25%">
			</td>
    	</tr>

FORM_START;

	}

	function simpleFormEnd () {
		echo <<<FORM_END

		<tr>
			<td>
				<div class="remositoryblock">&nbsp;</div>
				<input type="hidden" name="option" value="com_remository" />
				<input type="hidden" name="repnum" value="$this->repnum" />
				<input type="hidden" name="task" value="" />
			</td>
		</tr>
		</table>
		</form>

FORM_END;

	}

	function listFormEnd ($pagecontrol=true) {
		if ($pagecontrol) {
			?>
			<tr>
	    		<th align="center" colspan="13"> <?php echo $this->pageNav->writePagesLinks(); ?></th>
			</tr>
			<tr>
				<td align="center" colspan="13"> <?php echo $this->pageNav->writePagesCounter(); ?></td>
			</tr>
			<?php
		}
		echo <<<FORM_END

		</tbody>
		</table>
		<div>
			<input type="hidden" name="option" value="com_remository" />
			<input type="hidden" name="repnum" value="$this->repnum" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="act" value="{$_REQUEST['act']}" />
			<input type="hidden" name="boxchecked" value="0" />
		</div>
		</form>

FORM_END;

	}

	function listHeadingStart ($count) {
		echo <<<LIST_HEAD

		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<thead>
			<tr>
				<th width="5" align="left">
					<input type="checkbox" name="toggle" value="" onclick="checkAll($count);" />
				</th>

LIST_HEAD;

	}

	function headingItem ($width, $title) {
		echo "\n<th width=\"$width\" align=\"left\">$title</th>";
	}

	function commonScripts ($edit_fields) {
		$interface = remositoryInterface::getInstance();
		?>
		<script type="text/javascript">
        function submitbutton(pressbutton) {
                <?php
				if (is_array($edit_fields)) foreach ($edit_fields as $field) $interface->getEditorContents( $field );
				else $interface->getEditorContents ($edit_fields);
				?>
                submitform( pressbutton );
        }
        </script>
        <?php
	}

	function editFormEnd ($id, $oldpath) {
		echo <<<EDIT_END

		<input type="hidden" name="cfid" value="$id" />
		<input type="hidden" name="limit" value="$this->limit" />
		<input type="hidden" name="oldpath" value="$oldpath" />
		<input type="hidden" name="option" value="com_remository" />
		<input type="hidden" name="repnum" value="$this->repnum" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="act" value="{$_REQUEST['act']}" />
		</table>
		</form>

EDIT_END;

	}

	function multiOptionList ($name, $title, $options, $current, $tooltip=null) {
		$alternatives = explode(',',$options);
		$already = explode(',', $current);
		?>
		<tr>
	    <td width="30%" valign="top" align="right">
	  	<b><?php echo $title; ?></b>&nbsp;
	    </td>
	    <td valign="top">
		<?php
		foreach ($alternatives as $one) {
			if (in_array($one,$already)) $mark = 'checked="checked"';
			else $mark = '';
			$value = $name.'_'.$one;
			echo "<input type=\"checkbox\" name=\"$value\" $mark />$one";
		}
		if ($tooltip) echo '&nbsp;'.$this->tooltip($tooltip);
		echo '</td></tr>';
	}

	function tooltip ($text) {
		return '<a href="javascript:void(0)"  onmouseover="return escape('."'".$text."'".')">'
		.RemositoryRepository::getInstance()->RemositoryImageURL('tooltip.png').'</a>';
	}

}

class remositoryAdminHTML extends remositoryBasicHTML {
	var $repository = '';
	var $pageNav = '';
	var $clist = '';
	var $act = '';
	var $yesno = null;  //used for option lists
	var $yesnoboth = null; //used for option lists

	function __construct (&$controller, $limit, $clist) {
	    parent::__construct($controller, $limit);
	    $this->repository = $controller->repository;
	    $this->clist = $clist;
	    	
	    $this->yesno[] = $this->repository->makeOption( 0, _NO );
	    $this->yesno[] = $this->repository->makeOption( 1, _YES );

	    $this->yesnoboth[] = $this->repository->makeOption( 1, _NO );
	    $this->yesnoboth[] = $this->repository->makeOption( 2, _YES );
	    $this->yesnoboth[] = $this->repository->makeOption( 3, _DOWN_BOTH );
	}

	function fieldset ($legend, $fields) {
		return <<<FIELD_SET

		<div class="remositoryfieldset">
		<fieldset>
			<legend>$legend</legend>
			$fields
		</fieldset>
		</div>

FIELD_SET;

	}

	function displayIcons ($object, $iconList) {
		if (is_object($object)) $icon = $object->icon;
		else $icon = '';
		echo <<<ICON_LIST

		<tr>
			<td width="30%" valign="top" align="right">
				<script type="text/javascript">
				function paste_strinL(strinL){
					var input=document.forms["adminForm"].elements["icon"];
					input.value='';
					input.value=strinL;
				}
				</script>
				<b>{$this->show(_DOWN_ICON)}</b>&nbsp;
			</td>
			<td valign="top">
				<input class="inputbox" type="text" name="icon" size="25" value="$icon" />
				<table>
					<tr>
						<td>
							<div id="remositoryiconlist">
								$iconList
							</div>
						</td>
					</tr>
				</table>
			</td>
  		</tr>

ICON_LIST;

	}

	function simpleIcons ($object, $iconList) {
		if (is_object($object)) $icon = $object->icon;
		else $icon = '';
		$html = <<<ICON_HTML

		<script type="text/javascript">
		function paste_strinL(strinL){
			var input=document.forms["adminForm"].elements["icon"];
			input.value='';
			input.value=strinL;
		}
		</script>
		<div id="remositoryiconlist">
			<label for="remositoryicon"><strong>{$this->show(_DOWN_ICON)}</strong></label>
			<div>
				<input class="inputbox" type="text" id="remositoryicon" name="icon" size="25" value="$icon" />
			</div>
			<div>
				$iconList
			</div>
		</div>

ICON_HTML;

		return $html;
	}

	function listHeader ($descendants, $search) {
		$checked = $descendants ? 'checked="checked"' : '';
		echo <<<LIST_HEADER

		<tr>
			<td align="left">{$this->show(_DOWN_SEARCH_COLON)} <input type="text" name="search" value="$search" class="inputbox" onchange="document.adminForm.submit();" />
    		</td>
			<td align="left">{$this->show(_DOWN_SHOW_DESCENDANTS)}<input type="checkbox" name="descendants" value="1" $checked onchange="document.adminForm.submit();" />
			</td>
		</tr>
		{$this->clistInTable()}

LIST_HEADER;

	}

	private function clistInTable () {
	
		if ($this->clist) return <<<CLIST_ROW
		<tr>
			<td align="left" colspan="3">
				$this->clist
			</td>
		</tr>

CLIST_ROW;

	}

	function containerSelectBox () {
		$suggesttext = _DOWN_SUGGEST_LOC;
		echo <<<SELECT_BOX

		<tr>
			<td width="30%" valign="top" align="right">
				<b>$suggesttext</b>&nbsp;
			</td>
			<td valign="top">
				$this->clist
			</td>
		</tr>

SELECT_BOX;

	}

	function startEditHeader () {
		$tabclass_arr = $this->repository->getTableClasses();
		echo <<<EDIT_HEADER

		<form method="post" name="adminForm" action="{$this->interface->indexFileName()}" enctype="multipart/form-data">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="{$tabclass_arr[0]}">
EDIT_HEADER;

		$this->containerSelectBox();
	}

	function publishedBox (&$object) {
		$pubtext = _DOWN_PUB;
		$checked = (is_object($object) AND !empty($object->published)) ? 'checked="checked"' : '';
		echo <<<PUB_BOX

				<tr>
					<td width="30%" align="right">
						<b>$pubtext</b>&nbsp;
					</td>
					<td>
						<input type="checkbox" name="published" value="1" $checked />
					</td>
				</tr>

PUB_BOX;

	}

	function simpleCheckBox (&$object, $property, $title, $value=1, $default=0) {
		if (is_object($object) AND @$object->$property) $checked = "checked='checked'";
		else $checked = '';
		$html = <<<INPUT_BOX

		<div>
			<label for="$property" style="width=30%"><strong>$title</strong></label>
			<input type="hidden" name="$property" value="$default" />
			<input id="$property" class="inputbox" type="checkbox" $checked name="$property" value="$value" />
		</div>

INPUT_BOX;

		return $html;
	}

	function editLink ($id, $linkname, $containerid=0) {
		// Change for multiple repositories
		// $url = "index2.php?option=com_remository&amp;repnum=$this->repnum&amp;act=$this->act&amp;task=edit&amp;cfid=$id";
		$url = "{$this->interface->indexFileName()}?option=com_remository&amp;act=$this->act&amp;task=edit&amp;cfid=$id";
		if ($containerid) $url .= "&amp;containerid=$containerid";
		return <<<EDIT_LINK

		<a href="$url">$linkname</a>

EDIT_LINK;

	}

	function visitLink ($id, $linkname) {
		$manager = remositoryContainerManager::getInstance();
		$childcount = count($manager->getChildren($id, false));
		if (0 == $childcount) return '';
		// Change for multiple repositories
		// <a href="index2.php?option=com_remository&amp;repnum=$this->repnum&amp;act=$this->act&amp;parentid=$id">$linkname</a>
	    return <<<VISIT_LINK

	    <a href="{$this->interface->indexFileName()}?option=com_remository&amp;act=$this->act&amp;parentid=$id">$linkname</a>

VISIT_LINK;

	}

	protected function legalTypeList ($current) {
		$html = '';
		foreach (explode(',',_REMOS_LEGAL_TYPES) as $one) {
			$mark = ($one == $current) ? 'selected="selected"' : '';
			$html .= <<<LEGAL_OPTION

			<option $mark value="$one">$one</option>
LEGAL_OPTION;

		}
		return $html;
	}

}
