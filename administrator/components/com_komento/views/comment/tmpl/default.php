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
<?php if(Komento::joomlaVersion() >= 1.6) : ?>
	Joomla.submitbutton = function( action )
	{
		submitbutton(action);
	}
<?php endif; ?>

Komento.ready(function($) {
	window.submitbutton = function( action )
	{
		if(action == 'save' && ($('#name').val() == '' || $('#email').val() == '' || $('#comment').val() == ''))
		{
			alert('<?php echo JText::_( 'COM_KOMENTO_EDIT_FORM_REQUIRED_FIELD' ); ?>');
			return;
		}
		submitform(action);
	}
});

</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<table class="admintable">
	<tr>
		<td>
			<fieldset class="adminform">
			<legend><?php echo JText::_('COM_KOMENTO_EDITING_COMMENT'); ?></legend>
			<table class="admintable">
				<tr>
					<td class="key">
						<span><?php echo JText::_('COM_KOMENTO_COMMENT_NAME'); ?></span>
					</td>
					<td>
						<input class="inputbox" type="text" id="name" name="name" size="45" value="<?php echo $this->escape($this->comment->name);?>" />
						<small>(<?php echo JText::_('COM_KOMENTO_COMMENT_INPUT_REQUIRED'); ?>)</small>
					</td>
				</tr>

				<tr>
					<td class="key">
						<span><?php echo JText::_('COM_KOMENTO_COMMENT_EMAIL'); ?></span>
					</td>
					<td valign="top">
						<input class="inputbox" type="text" id="email" name="email" size="45" value="<?php echo $this->escape($this->comment->email);?>" />
						<small>(<?php echo JText::_('COM_KOMENTO_COMMENT_INPUT_REQUIRED'); ?>)</small>
					</td>
				</tr>

				<tr>
					<td class="key">
						<span><?php echo JText::_('COM_KOMENTO_COMMENT_WEBSITE'); ?></span>
					</td>
					<td valign="top">
						<input class="inputbox" type="text" id="url" name="url" size="45" value="<?php echo $this->escape($this->comment->url);?>" />
					</td>
				</tr>

				<tr>
					<td class="key">
						<span><?php echo JText::_('COM_KOMENTO_COMMENT_TEXT'); ?></span>
					</td>
					<td valign="top">
						<textarea id="comment" name="comment" class="inputbox" cols="50" rows="5"><?php echo $this->comment->comment;?></textarea>
						<small>(<?php echo JText::_('COM_KOMENTO_COMMENT_INPUT_REQUIRED'); ?>)</small>
					</td>
				</tr>

				<tr>
					<td class="key">
						<span><?php echo JText::_( 'COM_KOMENTO_COMMENT_CREATED' ); ?></span>
					</td>
					<td><?php echo JHTML::_('calendar', $this->comment->created , "created", "created", '%Y-%m-%d %H:%M:%S', array('size'=>'30')); ?></td>
				</tr>
				<tr>
					<td class="key">
						<span><?php echo JText::_( 'COM_KOMENTO_COMMENT_PUBLISHED' ); ?></span>
					</td>
					<td>
						<?php echo $this->renderCheckbox( 'published' , $this->comment->published ); ?>
					</td>
				</tr>
			</table>
			</fieldset>
		</td>
		<td width="50%" valign="top">&nbsp;</td>
	</tr>
</table>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_komento" />
<input type="hidden" name="c" value="comment" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="commentid" value="<?php echo $this->escape($this->comment->id);?>" />
</form>
