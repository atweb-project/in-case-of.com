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

class listFilesHTML extends remositoryAdminHTML {

	private function columnHeads ($files) {
		$this->listHeadingStart(count($files));
		$this->headingItem('5', _DOWN_ID);
		$this->headingItem('15%', _DOWN_NAME_TITLE);
		$this->headingItem('15%', _DOWN_PARENT_CAT);
		$this->headingItem('15%', _DOWN_PARENT_FOLDER);
		$this->headingItem('10%', _DOWN_LOCAL_OR_REMOTE);
		$this->headingItem('10%', _DOWN_PUB1);
		$this->headingItem('10%', _DOWN_DOWNLOADS_SORT);
		$this->headingItem('10%', '');
		echo "\n</tr></thead>";
	}
	
	private function containerLink ($file) {
		$parent = $file->getContainer();
		if ($parent) {
			$grandparent = $parent->getParent();
			if ($grandparent) $linkid = $grandparent->id;
			else $linkid = $parent->id;
		}
		else $linkid = 0;
		$link = '';
		// Change for multiple repositories
		// if ($linkid) $link .= "<a href='index2.php?option=com_remository&amp;repnum=$this->repnum&amp;act=containers&amp;task=list&amp;parentid=$linkid'>";
		if ($linkid) $link .= "<a href=\"{$this->interface->indexFileName()}?option=com_remository&amp;act=containers&amp;task=list&amp;parentid=$linkid\">";
		$link .= $file->getFamilyNames();
		if ($linkid) $link .= '</a>';
		return $link;
	}

	private function listLine ($file, $i, $k) {
		$interface = remositoryInterface::getInstance();
		// Change for multiple repositories
		// $downlink = $interface->getCfg('admin_site').'/index3.php?option=com_remository&amp;repnum=$this->repnum&amp;act=download&amp;id='.$file->id;
		$indexfile = $interface->indexFileName(3);
		$downlink = $interface->getCfg('admin_site')."/$indexfile?option=com_remository&amp;act=download&amp;id=".$file->id;
		?>
				<tr class="<?php echo "row$k"; ?>">
					<td width="5">
						<input type="checkbox" id="cb<?php echo $i;?>" name="cfid[]" value="<?php echo $file->id; ?>" onclick="isChecked(this.checked);" />
					</td>
					<td>
						<?php echo $file->id; ?>
					</td>
					<td width="15%" align="left">
							<?php echo $this->editLink($file->id, $file->filetitle, $file->containerid); ?>
					</td>
					<td width="15%" align="left"><?php echo $file->getCategoryName();?></td>
					<td width="15%" align="left"><?php echo $this->containerLink($file);?></td>
					<td width="10%" align="left"><?php echo $this->fileLocation($file);?></td>
					<?php if ($file->published==1) { ?>
					<td width="10%" align="center"><img src="<?php echo $interface->getAdminImagePath(); ?>/publish_g.png" border="0" alt="Published" /></td>
					<?php } else { ?>
					<td width="10%" align="center"><img src="<?php echo $interface->getAdminImagePath(); ?>/publish_x.png" border="0" alt="Published" /></td>
					<?php } ?>
					<td width="10%" align="left"><?php echo $file->downloads;?></td>
					<td width="10%" align="left"><a href="<?php echo $downlink ?>"><?php echo _DOWNLOAD ?></a></td>
				</tr>
		<?php
	}
	
	private function fileLocation ($file) {
		return $file->islocal ? _DOWN_IS_LOCAL : _DOWN_IS_REMOTE;
	}

	public function view (&$files, $descendants, $search='')  {
		$this->formStart(_DOWN_FILES);
		$this->listHeader($descendants, $search);
		echo '</table>';
		$this->columnHeads($files);
		$this->pageNav->listFormEnd();
		$k = 0;
		echo "\n\t\t<tbody>";
		foreach ($files as $i=>$file) {
			$this->listLine($file, $i, $k);
			$k = 1 - $k;
		}
		echo "\n\t\t</tbody></table></form>";
	}
	
}