<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">

Komento.ready(function($){
	$('a.toolbar').attr('href', 'javascript:void(0)');
})

</script>
<form action="index.php?option=com_komento&view=pending" method="post" name="adminForm" id="adminForm">
<div class="adminform-head">
	<table class="adminform">
		<tr>
			<td width="50%">
				<label><?php echo JText::_( 'COM_KOMENTO_COMMENTS_SEARCH' ); ?> :</label>
				<input type="text" name="search" id="search" value="<?php echo $this->escape($this->search); ?>" class="inputbox" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'COM_KOMENTO_COMMENTS_SEARCH' ); ?></button>
				<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_KOMENTO_RESET_BUTTON' ); ?></button>
			</td>
			<td width="50%" style="text-align: right;">
				<label><?php echo JText::_( 'COM_KOMENTO_COMMENTS_FILTER_COMPONENT' ); ?> :</label>
				<?php echo $this->component; ?>
			</td>
		</tr>
	</table>
</div>

<div class="adminform-body">

<table class="adminlist" cellspacing="1">
<thead>

	<!--

	Row
		Number
		Checkbox
		Comment
		Published
		Parent
		Component
		Article Id
		Date
		Author
		ID

	-->

	<tr>
		<th width="1%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_NUM' ); ?></th>
		<th width="1%"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->comments ); ?>);" /></th>
		<th width="30%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_COMMENT' ); ?></th>
		<th width="5%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_STATUS' ); ?></th>
		<th width="5%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_COMMENT_PARENT' ); ?></th>
		<th width="10%"><?php echo JHTML::_( 'grid.sort', JText::_( 'COM_KOMENTO_COLUMN_COMPONENT' ), 'component', $this->orderDirection, $this->order ); ?></th>
		<th width="10%"><?php echo JHTML::_( 'grid.sort', JText::_( 'COM_KOMENTO_COLUMN_ARTICLE' ), 'cid', $this->orderDirection, $this->order ); ?></th>
		<th width="10%"><?php echo JHTML::_( 'grid.sort', JText::_( 'COM_KOMENTO_COLUMN_DATE' ), 'created', $this->orderDirection, $this->order ); ?></th>
		<th width="10%"><?php echo JHTML::_( 'grid.sort', JText::_( 'COM_KOMENTO_COLUMN_AUTHOR' ) , 'created_by', $this->orderDirection, $this->order ); ?></th>
		<th width="5%"><?php echo JHTML::_( 'grid.sort', JText::_( 'COM_KOMENTO_COLUMN_ID' ) , 'id', $this->orderDirection, $this->order ); ?></th>
	</tr>
</thead>
<tbody>
<?php
if( $this->comments )
{
	$k = 0;
	$x = 0;
	$config	= JFactory::getConfig();
	for ($i=0, $n=count($this->comments); $i < $n; $i++)
	{
		$row			= $this->comments[$i];
		$date			= JFactory::getDate( $row->created );
		$date->setOffset( $config->getValue( 'offset' ) );
		// $link 			= 'index.php?option=com_komento&amp;controller=comment&amp;task=edit&amp;commentid='. $row->id;
		$userlink		= JURI::root() . 'index.php?option=com_komento&amp;view=blogger&amp;layout=listBlogs&amp;id='. $row->created_by;
	?>

	<!--

	Row
		Number
		Checkbox
		Comment
		Parent
		Component
		Article Id
		Action
		Date
		Author
		ID

	-->

	<tr id="<?php echo 'kmt-' . $row->id; ?>" class="<?php echo "row$k"; ?>">

		<!-- Row Number -->
		<td align="center">
			<?php echo $this->pagination->getRowOffset( $i ); ?>
		</td>

		<!-- Checkbox -->
		<td align="center">
			<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
		</td>

		<!-- Comment -->
		<td align="left">
			<?php echo $row->comment; ?>
		</td>

		<td align="center" class="published-cell">
			<?php if( $row->published == 2 ) { ?>
				<a title="<?php echo JText::_('COM_KOMENTO_PUBLISH_ITEM'); ?>" onclick="return listItemTask('cb<?php echo $i; ?>', 'publish')" href="javascript:void(0);">
					<img alt="<?php echo JText::_('COM_KOMENTO_MODERATE'); ?>" src="components/com_komento/assets/images/pending-favicon.png" />
				</a>
			<?php } else {
				echo JHTML::_('grid.published', $row, $i );
			} ?>
		</td>

		<!-- Parent -->
		<td align="center">
			<?php if($row->parent_id) { ?>
			<a href="<?php echo JRoute::_('index.php?option=com_komento&amp;view=comments&amp;controller=comment&amp;nosearch=1&amp;parentid=' . $row->parent_id); ?>"><?php echo JText::_('COM_KOMENTO_VIEW_PARENT'); ?></a>
			<?php } else {
				echo JText::_('COM_KOMENTO_NO_PARENT');
			} ?>
		</td>

		<!-- Component -->
		<td align="center">
			<?php echo JText::_( 'COM_KOMENTO_' . strtoupper( $row->component ) ); ?>
		</td>

		<!-- Article Title
			todo::link to article page view=article or link to edit article page task=edit?
		-->
		<td align="center">
			<?php echo Komento::loadApplication( $row->component )->load( $row->cid )->getContentTitle(); ?>
		</td>

		<!-- Date -->
		<td align="center">
			<?php echo $date->toMySQL( true );?>
		</td>

		<!-- Author -->
		<td align="center">
			<?php echo $row->name; ?>
		</td>

		<!-- ID -->
		<td align="center">
			<?php echo $row->id; ?>
		</td>
	</tr>
	<?php $k = 1 - $k; } ?>
<?php
}
else
{
?>
	<tr>
		<td align="center" colspan="10">
			<?php echo JText::_('COM_KOMENTO_COMMENTS_NO_COMMENT');?>
		</td>
	</tr>
<?php
}
?>
</tbody>

<tfoot>
	<tr>
		<td colspan="10">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
</tfoot>
</table>
</div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="affectchild" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_komento" />
<input type="hidden" name="view" value="pending" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="pending" />
<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->order); ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->orderDirection); ?>" />
</form>
