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

class editGroupsHTML extends remositoryAdminHTML {

    function view (&$rows, $selected, $search, $lists, $role, $usersToAdd, $task) {
    	$total = count($rows);
    	if ($role) {
	    	$heading = $usersToAdd ? _DOWN_ADD_USERS_ROLE : _DOWN_REMOVE_USERS_ROLE;
			$showrole = $role;
    		$hiddenrole = <<<HIDDEN_ROLE

		<input type="hidden" name="role" value="$role" />

HIDDEN_ROLE;

		}
    	else {
    		$heading = _DOWN_ADD_USERS_NEW_ROLE;
    		$hiddenrole = '';
    		$showrole = <<<NEW_ROLE

    	<input type="text" name="role" size"25" class="inputbox" />

NEW_ROLE;

    	}
    	if (defined('_ALIRO_IS_PRESENT')) {
	    	$link = aliroAdminRequest::getInstance()->simpleURL();
			$link .= '&amp;listype=users';
	    	if ($role) $link .= '&amp;role='.$role;
	    	$footer = $this->pageNav->getListFooter($link);
    	}
    	else $footer = $this->pageNav->getListFooter();
		$k = $i = 0;
		$list_html = '';
		foreach ($rows as $i=>$row) {
			$item_num = $i+1+$this->pageNav->limitstart;
			$last_visit = date('Y-m-d H:i:S', strtotime($row->lastvisitDate));
			$checked = in_array($row->id, $selected) ? 'checked="checked"' : '';
			$list_html .= <<<USER_LINE

			<tr class="row$k">
				<td>
					$item_num
				</td>
				<td>
					<input type="checkbox" id="cb$i" name="cfid[]" value="$row->id" $checked onclick="isChecked(this.checked);" />
					<input type="hidden" name="cfall[]" value="$row->id" />
				</td>
				<td>
					$row->name
				</td>
				<td>
					$row->id
				</td>
				<td>
					$row->username
				</td>
				<td>
					$row->email
				</td>
			</tr>

USER_LINE;

			$k = 1 - $k;
			$i++;
		}
		$filter_by = $lists['filter_by'];
		$selcount = count($selected);
		echo <<<USER_LIST

		<form action="{$this->interface->indexFileName()}" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class="user">
				$heading $showrole
			</th>
			<td>
				Filter:
			</td>
			<td>
				<input type="text" name="search" value="$search" class="inputbox" onchange="document.adminForm.submit();" />
			</td>
			<td>
				by&nbsp;
			</td>
			<td>
				$filter_by
			</td>
			<td>
				<input type="submit" name="go" value=" Go " />
			</td>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
			<th width="2%" class="title">
			#
			</th>
			<th width="3%" class="title">
				<input type="checkbox" name="toggle" value="" onClick="checkAll($total);" />
			</th>
			<th class="title">
				Name
			</th>
			<th width="2%" class="title">
				ID
			</th>
			<th width="15%" class="title" >
				UserID
			</th>
			<th width="15%" class="title">
				E-Mail
			</th>
		</tr>
		$list_html
		</table>
		$footer
		<input type="hidden" name="listype" value="users" />
		<input type="hidden" name="option" value="com_remository" />
		<input type="hidden" name="repnum" value="$this->repnum" />
		<input type="hidden" name="act" value="groups" />
		<input type="hidden" name="task" value="$task" />
		$hiddenrole
		<input type="hidden" name="boxchecked" value="$selcount" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>

USER_LIST;

	}

}