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

class listGroupsHTML extends remositoryAdminHTML {

	function view ($roles, $rsearch) {
		$text = _DOWN_NO_REMOSITORY_GROUPS;

		if (remositoryRepository::getInstance()->Use_CMS_Groups) {
			$this->formStart(_MBT_GROUP_MANAGER);
			echo '</table><table cellpadding="4" cellspacing="0" border="0" width="100%" class="">';
			echo <<<UNDER_HEADING

		<tr>
			<td colspan="2">
				<div class="remositoryblock">&nbsp;</div>
			</td>
		</tr>
		</table>
		<div>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="option" value="com_remository"/>
			<input type="hidden" name="repnum" value="$this->repnum" />
		</div>
		</form>

UNDER_HEADING;

			echo <<<NO_GROUPS

		<h3>$text</h3>

NO_GROUPS;

			return;
		}
        $k = 0;
        $role_list = '';
        foreach ($roles as $role=>$translated) {
        	$idrole = str_replace(' ', '0', $role);
        	$role_list .= <<<ROLE_LINE
            <tr class="row $k">
            	<td width='20'>
					<input type="checkbox" id="cb$idrole" name="cfid[]" value="$translated" onclick="isChecked(this.checked);" />
				</td>
				<td align="center">
					<a href="#edit" onclick="return listItemTask('cb$idrole','edit')">
						$translated
					</a>
				</td>
				<td align="center">
					$translated
        		</td>
			</tr>

ROLE_LINE;

            $k = 1 - $k;
        }

        $count = count($roles);
		$site = remositoryInterface::getInstance()->getCfg('live_site');
        echo <<<ROLE_LIST1
        <form action="{$this->interface->indexFileName()}" method="post" name="adminForm">
			<table class="adminheading">
				<tr>
					<th>
						<div class="title header">
							<img src="$site/components/com_remository/images/header.gif" width="64" height="64" style="border:0;"  alt="" />
							<span class="sectionname">{$this->show(_MBT_GROUP_MANAGER)}</span>
        				</div>
        			</th>
					<td nowrap="nowrap">
                    	{$this->show(_MBT_GROUP_FILTER)}
                    <input type="text" name="rsearch" value="$rsearch" class="inputbox" onchange="document.adminForm.submit();" />
                    <input type="hidden" name="listype" value="roles" />
					</td>
				</tr>
            </table>
			<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
				<thead>
				<tr>
					<th width="2%" class="title"><input type="checkbox" name="toggle" value="" onclick="checkAll($count);" /></th>
					<th class="title" width="30%"><div align="center">{$this->show(_MBT_GROUP_GROUP)}</div></th>
					<th class="title" width="55%"><div align="center">{$this->show(_MBT_GROUP_DESCRIPTION)}</div></th>
					<th class="title" width="15%"><div align="center">{$this->show(_MBT_GROUP_EMAIL)}</div></th>
				</tr>
				</thead>
ROLE_LIST1;
		$this->pageNav->listFormEnd();
		echo <<<ROLE_LIST
		
				<tbody>
					$role_list
       			</tbody>
       		</table>
       	</form>	

ROLE_LIST;

    }

}

