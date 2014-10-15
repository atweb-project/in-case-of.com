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

class listClassificationsHTML extends remositoryAdminHTML {

	function view ($classifications, $search='', $type='', $types=array())  {
		$this->formStart(_DOWN_CLASSIFICATIONS);
		$this->classifyFilters($search, $type, $types);
		// $this->listHeader($descendants, $search);
		echo '</table>';
		$this->columnHeads($classifications);
		$k = $parentid = 0;
		if ($classifications) foreach ($classifications as $i=>$classification) {
			$this->listLine($classification, $i, $k);
			$k = 1 - $k;
		}
		$this->listFormEnd();
	}

	function classifyFilters ($search, $type, $types) {
		$displaytext = _DOWN_DISPLAY_NUMBER.$this->pageNav->getLimitBox();
		$searchtext = _DOWN_SEARCH_COLON;
		$typoptions = <<<NULL_OPTION

			<option value="">Display all</option>

NULL_OPTION;
		foreach ($types as $one) {
			if ($one == $type) $selected = ' selected="selected"';
			else $selected = '';
			$typoptions .= <<<OPTION

			<option$selected value="$one">$one</option>

OPTION;

		}
		if ($typoptions) $typeselect = <<<SELECTION

			<td align="left">
				<select name="type" onchange="document.adminForm.submit();">
					$typoptions
				</select>
			</td>

SELECTION;

		else $typeselect = '';

		echo <<<FILTER_HTML

		<tr>
    		<td align="left">
    			$displaytext
			</td>
			$typeselect
			<td align="left">
				$searchtext<input type="text" name="search" value="$search" class="inputbox" onchange="document.adminForm.submit();" />
    		</td>
		</tr>

FILTER_HTML;

	}

	function columnHeads ($classifications) {
		$this->listHeadingStart(count($classifications));
		$this->headingItem('15%', _DOWN_NAME_TITLE);
		$this->headingItem('3%', _DOWN_ID);
		$this->headingItem('5%', _DOWN_PUB1);
		$this->headingItem('5%', _DOWN_IS_VISIBLE);
		$this->headingItem('10%', _DOWN_FREQUENCY);
		$this->headingItem('10%', _DOWN_TYPE);
		$this->headingItem('45%', _DOWN_DESCRIPTION);
		echo '</tr></thead><tbody>';
	}

	function listline ($classification, $i, $k) {
		$pimage = $classification->published ? 'publish_g.png' : 'publish_x.png';
		$palt = $classification->published ? _DOWN_PUB1 : _DOWN_NOT_PUBLISHED;
		$himage = $classification->hidden ? 'publish_x.png' : 'tick.png';
		$halt = $classification->hidden ? 'Hidden' : 'Not hidden';
		$interface = remositoryInterface::getInstance();
		$admin_images = $interface->getAdminImagePath();
		// Change for multiple repositories
		//			<td align="left"><a href="index2.php?option=com_remository&amp;repnum=$this->repnum&amp;act=$this->act&amp;task=edit&amp;cfid=$classification->id">$classification->name</a></td>
		echo <<<CLASSN_LINE

				<tr class="row$k">
					<td>
						<input type="checkbox" id="cb$i" name="cfid[]" value="$classification->id" onclick="isChecked(this.checked);" />
					</td>
					<td align="left"><a href="{$this->interface->indexFileName()}?option=com_remository&amp;act=$this->act&amp;task=edit&amp;cfid=$classification->id">$classification->name</a></td>
					<td align="left">$classification->id</td>
					<td align="left"><img src="$admin_images/$pimage" border="0" alt="$palt" /></td>
					<td align="left"><img src="$admin_images/$himage" border="0" alt="$halt" /></td>
					<td align="left">$classification->frequency</td>
					<td align="left">$classification->type</td>
					<td align="left">$classification->description</td>
				</tr>

CLASSN_LINE;

	}

}