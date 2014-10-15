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

/*
Row
	Number
	Checkbox
	Comment
	Child / Parent
	Edit
	Component
	Article Id
	Publish/Unpublish
	Sticked
	Date
	Author
	Parent ID
	Id
*/

$this->row = Komento::getHelper( 'comment' )->process( $this->row, 1 );

if( $this->row ) { ?>

<tr id="<?php echo 'kmt-' . $this->row->id; ?>" class="<?php echo 'row' . $this->k; ?> kmt-row" childs="<?php echo $this->row->childs; ?>" depth="<?php echo $this->row->depth; ?>" parentid="<?php echo $this->row->parent_id; ?>">

	<!-- Row Number -->
	<td align="center">
		<?php echo isset( $this->pagination ) ? $this->pagination->getRowOffset( $this->i ) : ''; ?>
	</td>

	<!-- Checkbox -->
	<td align="center">
		<?php echo JHTML::_('grid.id', $this->i, $this->row->id); ?>
	</td>

	<!-- Comment
		todo::truncate comments (overflow css)
		todo::better edit hyperlink
	 -->
	<td align="left" class="comment-cell">
		<table>
			<tr>
				<?php if( $this->row->depth > 0 ) { ?>
				<td width="1%"><?php echo str_repeat('|—', $this->row->depth); ?></td>
				<?php } ?>
				<td><?php echo $this->row->comment; ?></td>
			</tr>
		</table>
	</td>

	<!-- Publish/Unpublish -->
	<td align="center" class="published-cell">
		<?php if( $this->row->published == 2 ) { ?>
			<a title="<?php echo JText::_('COM_KOMENTO_PUBLISH_ITEM'); ?>" onclick="return listItemTask('cb<?php echo $this->i; ?>', 'publish')" href="javascript:void(0);">
				<img alt="<?php echo JText::_('COM_KOMENTO_MODERATE'); ?>" src="components/com_komento/assets/images/pending-favicon.png" />
			</a>
		<?php } else {
			echo JHTML::_('grid.published', $this->row, $this->i );
		} ?>
	</td>

	<!-- Sticked -->
	<td align="center" class="sticked-cell">
		<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $this->i; ?>','<?php echo ( Komento::getModel( 'comments' )->isSticked( $this->row->id ) ) ? 'unstick' : 'stick';?>')">
			<img src="components/com_komento/assets/images/<?php echo ( Komento::getModel( 'comments' )->isSticked( $this->row->id ) ) ? 'sticked.png' : 'unsticked.png'; ?>" width="16" height="16" border="0" />
		</a>
	</td>

	<!-- Link to childs / parent
	todo::check if child exist
	if rgt-lft > 1
	-->
	<td align="center" class="linked-cell">
	<?php if( !$this->search ) {
		if( $this->row->childs ) { ?>
		<a href="javascript:void(0);" onclick="Komento.actions.loadReplies(<?php echo $this->row->id; ?>);"><?php echo JText::_('COM_KOMENTO_VIEW_CHILD'); ?></a>
		<?php } else {
			echo JText::_('COM_KOMENTO_NO_CHILD');
		}
	} else {
		if($this->row->parent_id) { ?>
		<a href="<?php echo JRoute::_('index.php?option=com_komento&amp;view=comments&amp;controller=comment&amp;nosearch=1&amp;parentid=' . $this->row->parent_id); ?>"><?php echo JText::_('COM_KOMENTO_VIEW_PARENT'); ?></a>
		<?php } else {
			echo JText::_('COM_KOMENTO_NO_PARENT');
		}
	} ?>
	</td>

	<!-- Edit -->
	<td align="center">
		<a href="<?php echo JRoute::_('index.php?option=com_komento&view=comment&amp;task=edit&amp;c=comment&amp;commentid=' . $this->row->id); ?>"><?php echo JText::_('COM_KOMENTO_COMMENT_EDIT'); ?></a>
	</td>

	<!-- Component -->
	<td align="center">
		<?php echo JText::_( 'COM_KOMENTO_' . strtoupper( $this->row->component ) ); ?>
	</td>

	<!-- Article Id
		todo::link to article page view=article or link to edit article page task=edit?
	-->
	<td align="center">
		<a href="<?php echo $this->row->pagelink; ?>"><?php echo $this->row->contenttitle; ?></a>
	</td>

	<!-- Date -->
	<td align="center">
		<?php echo $this->date->toMySQL( true );?>
	</td>

	<!-- Author -->
	<td align="center">
		<?php echo $this->row->name; ?>
	</td>

	<!-- ID -->
	<td align="center">
		<?php echo $this->row->id; ?>
	</td>
</tr>

<?php } ?>
