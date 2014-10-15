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

class listUnlinkedHTML extends remositoryAdminHTML {

	function columnHeads ($count) {
		echo "\n\t</table>";
		$this->listHeadingStart($count);
		$this->headingItem('35%', 'Path');
		$this->headingItem('45%', '');
		echo "\n</tr></thead>";
	}

	function orphanLine ($baselink, $fullpath, $i, $k) {
		$link64 = base64_encode($fullpath);
		$link = $baselink.$link64;
		return <<<ORPHAN_LINE

		<tr class="row$k">
			<td width="5">
				<input type="checkbox" id="cb$i" name="cfid[]" value="$link64" onclick="isChecked(this.checked);" />
			</td>
			<td width="60%" align="left">
				<a href="$link">$fullpath</a>
			</td>
			<td width="40%"></td>
		</tr>

ORPHAN_LINE;

	}

	function view( &$OrphanDownloads, &$OrphanUploads, $baselink ) {
		$this->formStart('- Fichiers non class&eacute;s');
		$count = count($OrphanDownloads)+count($OrphanUploads);
		$this->columnHeads($count);
		$this->pageNav->listFormEnd(false);
		$k = 0;
		$i = 0;
		echo "\n\t\t<tbody>";
		foreach ($OrphanDownloads as $fullpath) {
			echo $this->orphanLine ($baselink, $fullpath, $i, $k);
			$k = 1 - $k;
			$i++;
		}
		foreach ($OrphanUploads as $fullpath) {
			echo $this->orphanLine ($baselink, $fullpath, $i, $k);
			$k = 1 - $k;
			$i++;
		}
		if (0 == $count) {
			$text = '0 '._DOWN_RECORDS;
			echo <<<NO_RECORDS

			<tr>
				<td colspan="13" align="center"><span class="message">$text</span></td>
			</tr>

NO_RECORDS;

		}
		echo "\n\t\t</tbody></table></form>";
	}
}