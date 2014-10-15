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

class listContainersHTML extends remositoryAdminHTML {

	function columnHeads ($containers) {
		$this->listHeadingStart(count($containers));
		$this->headingItem('10%', _DOWN_NAME_TITLE);
		$this->headingItem('3%', _DOWN_VISIT);
		$this->headingItem('3%', _DOWN_EDIT);
		if ($this->clist) {
			$this->headingItem('3%', 'ID');
			$this->headingItem('10%', _DOWN_PARENT_CAT);
			$this->headingItem('10%', _DOWN_PARENT_FOLDER);
		}
		$this->headingItem('6%', _DOWN_PUB1);
		$this->headingItem('6%', _DOWN_RECORDS);
		$this->headingItem('8%', _DOWN_VISITORS);
		$this->headingItem('8%', _DOWN_REG_USERS);
		$this->headingItem('8%', _DOWN_OTHER_USERS);
		$this->headingItem('10%', _DOWN_STORAGE_STATUS);
		echo "\n</tr></thead>";
	}

	function filecount ($container) {
		if ($container->filecount) {
			// Change for multiple repositories
			// $link = "<a href='index2.php?option=com_remository&amp;repnum=$this->repnum&amp;act=files&amp;task=list&amp;containerid=$container->id'>";
			$link = "<a href='{$this->interface->indexFileName()}?option=com_remository&amp;act=files&amp;task=list&amp;containerid=$container->id'>";
			$link .= $container->filecount;
			$link .= '</a>';
			return $link;
		}
		else return '0';
	}

	function listLine ($container, $i, $k) {
		$interface = remositoryInterface::getInstance();
		$authoriser = aliroAuthoriser::getInstance();
		$categoryname = $this->visitLink(0, $container->getCategoryName());
		$family = $container->getFamilyNames();
		if ($container->parentid) {
		    $parent = $container->getParent();
		    if ($parent->parentid) $family = $this->visitLink($parent->parentid, $family);
		}
		?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<input type="checkbox" id="cb<?php echo $i;?>" name="cfid[]" value="<?php echo $container->id; ?>" onclick="isChecked(this.checked);" />
					</td>
					<td align="left"><?php echo $container->name; ?></td>
					<td align="left">
						<?php echo $this->visitLink($container->id, _DOWN_VISIT); ?>
					</td>
					<td align="left">
						<?php echo $this->editLink($container->id, _DOWN_EDIT); ?>
					</td>
					<?php if ($this->clist) { ?>
					<td align="left"><?php echo $container->id; ?></td>
					<td align="left"><?php echo $categoryname;?></td>
					<td align="left"><?php echo $family;?></td>
					<?php }
					if ($container->published==1) { ?>
					<td align="left"><img src="<?php echo $interface->getAdminImagePath(); ?>/publish_g.png" border="0" alt="Published" /></td>
					<?php } else { ?>
					<td align="left"><img src="<?php echo $interface->getAdminImagePath(); ?>/publish_x.png" border="0" alt="Published" /></td>
					<?php } ?>
					<td align="left"><?php echo $this->filecount($container); ?></td>
					<td align="left">
					<?php
					if ($authoriser->checkRolePermission  ('Public', array('download','edit'), 'remosFolder', $container->id)) echo $this->repository->RemositoryImageURL('download_trans.gif').'/';
					else echo '-/';
					if ($authoriser->checkRolePermission  ('Public', array('upload','edit'), 'remosFolder', $container->id)) echo $this->repository->RemositoryImageURL('add_file.gif');
					else echo '-';
     				?>
					</td>
					<td align="left">
					<?php
					if ($authoriser->checkRolePermission  (array('Public','Registered'), array('download','edit'), 'remosFolder', $container->id)) echo $this->repository->RemositoryImageURL('download_trans.gif').'/';
					else echo '-/';
					if ($authoriser->checkRolePermission  (array('Public','Registered'), array('upload','edit'), 'remosFolder', $container->id)) echo $this->repository->RemositoryImageURL('add_file.gif');
					else echo '-';
     				?>
					</td>
					<td align="left">
					<?php
					if ($this->checkOtherPermission  (array('download','edit'), $container->id)) echo $this->repository->RemositoryImageURL('download_trans.gif').'/';
					else echo '-/';
					if ($this->checkOtherPermission  (array('upload','edit'), $container->id)) echo $this->repository->RemositoryImageURL('add_file.gif');
					else echo '-';
     				?>
					</td>
					<td align="left">
					<?php echo $container->pathstatus ?>
					</td>
				</tr>
		<?php
	}
	
	//private function
	function checkOtherPermission ($actions, $id) {
		$authAdmin = aliroAuthorisationAdmin::getInstance();
		$allowed = $authAdmin->permittedRoles ($actions, 'remosFolder', $id, array('Registered', 'Nobody'));
		return count($allowed);
	}

	// was showContainersHTML
	function view ($container, $containers, $descendants, $search='')  {
		$title = $container->id ? _DOWN_CONTAINERS.' - '.$container->name : _DOWN_CONTAINERS;
		$this->formStart($title);
		$this->listHeader($descendants, $search);
		echo '</table>';
		$this->columnHeads($containers);
		$this->pageNav->listFormEnd();
		$k = $parentid = 0;
		echo "\n\t\t<tbody>";
		foreach ($containers as $i=>$container) {
		    $parentid = $container->parentid;
			$this->listLine($container, $i, $k);
			$k = 1 - $k;
		}
		echo <<<SAVE_PARENT
		</tbody>
		</table>
		<div>
			<input type="hidden" name="currparent" value="$parentid" />
		</div>
		</form>
		
SAVE_PARENT;

	}
}