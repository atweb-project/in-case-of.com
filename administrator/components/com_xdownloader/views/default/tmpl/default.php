<?php
/*------------------------------------------------------------------------
# com_xdownloader - xDownloader alpha component
# ------------------------------------------------------------------------
# author    Dmitri Gorbunov
# copyright Copyright (C) 2012 xrbyte.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.xrbyte.com
# Technical Support:  Forum - http://www.xrbyte.com/forum
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.user.helper' );
// load tooltip behavior
JHtml::_('behavior.tooltip');

$listOrder	= strtolower($this->escape($this->state->get('list.ordering')));
$listDirn	= strtolower($this->escape($this->state->get('list.direction')));
?>
<script type="text/javascript">
<!--
	var deselectSelectBox = function(idName) {
		var form = document.id('adminForm');
		var element = form.getElementById(idName);
		for(var i = 0, n = element.options.length; i < n; i++) {
			element.options[i].selected = false;
		}
	};

	var clearFilters = function() {
		var form = document.id('adminForm');
		
		document.id('filter_search').value = '';
		document.id('filter_title').value = '';
		document.id('filter_filename').value = '';
		document.id('filter_filepath').value = '';
		document.id('filter_ip').value = '';
		document.id('filter_iplocation').value = '';
		document.id('filter_linkgroups').value = '';
		document.id('filter_date').value = '';
		document.id('filter_datefrom').value = '';
		document.id('filter_dateto').value = '';
		deselectSelectBox('filter_groups');

		form.submit();
	};
//-->
</script>

<form id="adminForm" name="adminForm" method="post" action="<?php echo 'index.php?option=com_xdownloader&view=default'; ?>">
	<fieldset id="filter-bar" style="height: 85px;">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('COM_XDOWNLOADER_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_XDOWNLOADER_FILTER_DESC'); ?>" />
		</div>
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_groups"><?php echo JText::_('COM_XDOWNLOADER_USERGRUOPS_LABEL'); ?></label>
			<?php
				echo $this->filterGroups;  
			?>
		</div>
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_title"><?php echo JText::_('COM_XDOWNLOADER_FILTER_TITLE_LABEL'); ?></label>
			<input type="text" name="filter_title" id="filter_title" value="<?php echo $this->escape($this->state->get('filter.title')); ?>" title="<?php echo JText::_('COM_XDOWNLOADER_FILTER_TITLE_DESC'); ?>" />
		</div>
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_filename"><?php echo JText::_('COM_XDOWNLOADER_FILTER_FILENAME_LABEL'); ?></label>
			<input type="text" name="filter_filename" id="filter_filename" value="<?php echo $this->escape($this->state->get('filter.filename')); ?>" title="<?php echo JText::_('COM_XDOWNLOADER_FILTER_FILENAME_DESC'); ?>" />
		</div>
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_filepath"><?php echo JText::_('COM_XDOWNLOADER_FILTER_FILEPATH_LABEL'); ?></label>
			<input type="text" name="filter_filepath" id="filter_filepath" value="<?php echo $this->escape($this->state->get('filter.filepath')); ?>" title="<?php echo JText::_('COM_XDOWNLOADER_FILTER_FILEPATH_DESC'); ?>" />
		</div>
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_ip"><?php echo JText::_('COM_XDOWNLOADER_FILTER_IP_LABEL'); ?></label>
			<input type="text" name="filter_ip" id="filter_ip" value="<?php echo $this->escape($this->state->get('filter.ip')); ?>" title="<?php echo JText::_('COM_XDOWNLOADER_FILTER_IP_DESC'); ?>" />
		</div>
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_iplocation"><?php echo JText::_('COM_XDOWNLOADER_FILTER_IPLOCATION_LABEL'); ?></label>
			<input type="text" name="filter_iplocation" id="filter_iplocation" value="<?php echo $this->escape($this->state->get('filter.iplocation')); ?>" title="<?php echo JText::_('COM_XDOWNLOADER_FILTER_IPLOCATION_DESC'); ?>" />
		</div>
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_linkgroups"><?php echo JText::_('COM_XDOWNLOADER_FILTER_LINKGROUPS_LABEL'); ?></label>
			<input type="text" name="filter_linkgroups" id="filter_linkgroups" value="<?php echo $this->escape($this->state->get('filter.linkgroups')); ?>" title="<?php echo JText::_('COM_XDOWNLOADER_FILTER_LINKGROUPS_DESC'); ?>" />
		</div>
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_date"><?php echo JText::_('COM_XDOWNLOADER_FILTER_DATE_LABEL'); ?></label>
			<?php
			 	$this->filterForm->setFieldAttribute('filter_date', 'default', $this->state->get('filter.date', ''));
				echo  $this->filterForm->getInput('filter_date');
			?>
		</div>
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_datefrom"><?php echo JText::_('COM_XDOWNLOADER_FILTER_DATEFROM_LABEL'); ?></label>
			<?php
			 	$this->filterForm->setFieldAttribute('filter_datefrom', 'default', $this->state->get('filter.datefrom', ''));
				echo  $this->filterForm->getInput('filter_datefrom');
			?>
		</div>
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_dateto"><?php echo JText::_('COM_XDOWNLOADER_FILTER_DATETO_LABEL'); ?></label>
			<?php
			 	$this->filterForm->setFieldAttribute('filter_dateto', 'default', $this->state->get('filter.dateto', ''));
				echo  $this->filterForm->getInput('filter_dateto');
			?>
		</div>
		
		<div class="filter-search fltlft">
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="clearFilters();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>		
	</fieldset>

	<table class="adminlist">
        <thead>
            <tr>
                <th width="1%">
	                <input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this);" />
                </th>
                <th>
                    <?php echo JHtml::_('grid.sort',  JText::_('COM_XDOWNLOADER_TITLE_LABEL'), 'a.title', $listDirn, $listOrder); ?>
                </th>
                <th>
                	<?php echo JHtml::_('grid.sort',  JText::_('COM_XDOWNLOADER_FILENAME_LABEL'), 'a.filename', $listDirn, $listOrder); ?>
                </th>
                <th>
                	<?php echo JHtml::_('grid.sort',  JText::_('COM_XDOWNLOADER_FILEPATH_LABEL'), 'a.filepath', $listDirn, $listOrder); ?>
                </th>
                <th>
                	<?php echo JHtml::_('grid.sort',  JText::_('COM_XDOWNLOADER_DATE_LABEL'), 'a.dwn_date', $listDirn, $listOrder); ?>
                </th>
                <th>
                	<?php echo JHtml::_('grid.sort',  JText::_('COM_XDOWNLOADER_IP_LABEL'), 'a.user_ip', $listDirn, $listOrder); ?>
                </th>
                <th>
                	<?php echo JHtml::_('grid.sort',  JText::_('COM_XDOWNLOADER_IPCOUNTRY_LABEL'), 'a.ip_location', $listDirn, $listOrder); ?>
                </th>                
                <th>
                	<?php echo JHtml::_('grid.sort',  JText::_('COM_XDOWNLOADER_LINKLOCATION_LABEL'), 'a.menu', $listDirn, $listOrder); ?>
                </th>                
                <th>
                	<?php echo JText::_('COM_XDOWNLOADER_LINKGROUPS_LABEL'); ?>
                </th>
                <th>
                	<?php echo JText::_('COM_XDOWNLOADER_USERDETAILS_LABEL'); ?>
                </th>
                <th width="1%" class="nowrap">
                    <?php echo JHtml::_('grid.sort', JText::_('ID'), 'a.id', $listDirn, $listOrder); ?>
                </th>
            </tr>
        </thead>
        <tbody>
        	<?php if(empty($this->items)): ?>
        	<tr>
        		<td colspan="11" align="center">
        			<?php echo JText::_('COM_XDOWNLOADER_EMPTYLIST_NOTE');?>
        		</td>
        	</tr>
        	<?php 
        		else:
        			$i = 0;
        			foreach($this->items as $item):
        				$checked = JHTML::_('grid.id', $i, $item->id);
        				$rowclass = 'row'.($i%2);
        				$datetime = explode(' ', $item->dwn_date);
        				$ip = long2ip($item->user_ip);
        				
        				$link_groups = $item->link_groups;
/*        				if(!empty($item->link_groups)) {
	        				$link_groups = explode(",", $item->link_groups);
	        				$link_groups = XDownloaderHelper::convertGroupIdToName($link_groups);
	        				if($link_groups) {
	        					$link_groups = implode(", ", $link_groups);
	        				}
	        				else {
	        					$link_groups = '';
	        				}
        				}
	*/					
						$deletedUser = false;
						
        				if((int) $item->guest == 1) {
        					$userDetails = 'guest';
        				}
        				else {
							if(XDownloaderHelper::hasUserID($item->user_id)) {
							
								$userDetails = new stdClass();
								$userDetails->id = $item->user_id;
								$userDetails->name = $item->user_alias;
								$groups = JUserHelper::getUserGroups($item->user_id);
								$groups = XDownloaderHelper::convertGroupIdToName($groups);
								$userDetails->groups = implode(',', $groups);
							}
							else {
								$deletedUser = true;
								$userDetails = 'Deleted';
							}
        				}
        	?>
        	<tr class="<?php echo $rowclass; ?>">
        		<td><?php echo $checked; ?></td>
        		<td><?php echo $item->title; ?></td>
        		<td><?php echo $item->filename; ?></td>
        		<td><input type="text" readonly="readonly" value="<?php echo $item->filepath; ?>" size="20"/></td>
        		<td><?php echo $datetime[0].'<br/>'.$datetime[1]; ?></td>
        		<td><?php echo $ip; ?></td>
        		<td><?php echo $item->ip_location; ?></td>
        		<td><a href="<?php echo $item->menu; ?>" target="_blank">Front-end menu</a></td>
        		<td><?php echo $link_groups; ?></td>
        		<td>
        			<?php if((int) $item->guest == 1): ?>
        			<?php 	echo $userDetails; ?>
        			<?php elseif($deletedUser): ?>
					<?php 	echo $userDetails; ?>
					<?php else: ?>
        			<label>ID:</label><?php echo $userDetails->id; ?><br/>
        			<label>name:</label><?php echo $userDetails->name; ?><br/>
        			<label>groups:</label><?php echo $userDetails->groups; ?>
        			<?php endif; ?>
        		</td>
        		<td>
        			<?php echo $item->id; ?>
        		</td>
        	</tr>
        	<?php 		$i++;   ?>
        	<?php 	endforeach; ?>
        	<?php endif; 		?>
        </tbody>
        <tfoot>
        	<tr>
				<td colspan="11">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
        	</tr>
        </tfoot>
	</table>
	
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>	
</form>

<div style="width: 100%">
	<span style="">
		xDownloader v.1.0.5 alpha
		<a href="http://www.gnu.org/licenses/old-licenses/gpl-2.0.html" target="_blank">GNU General Public License v.2</a><br />
		Copyright &copy; 2012 <a href="http://www.xrbyte.com" target="_blank">www.xrbyte.com</a>
	</span>
</div>