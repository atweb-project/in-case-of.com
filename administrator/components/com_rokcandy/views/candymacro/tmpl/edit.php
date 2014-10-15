<?php // no direct access
/**
 * @version $Id$
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'candymacro.cancel' || document.formvalidator.isValid(document.id('candymacro-form'))) {
			Joomla.submitform(task, document.getElementById('candymacro-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rokcandy&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="candymacro-form" class="form-validate">
	<div class="width-60 fltlft">

        <fieldset class="adminform">
            <legend><?php echo empty($this->item->id) ? JText::_('COM_ROKCANDY_NEW_MACRO') : JText::sprintf('COM_ROKCANDY_EDIT_MACRO', $this->item->id); ?></legend>
			<ul class="adminformlist">
                <li><?php echo $this->form->getLabel('macro'); ?>
				<?php echo $this->form->getInput('macro'); ?>
                <?php echo JHTML::_('tooltip', JText::_('COM_ROKCANDY_TIP_DESC'), '', 'tooltip.png');?></li>

                <li><?php echo $this->form->getLabel('html'); ?>
				<?php echo $this->form->getInput('html'); ?>
                <?php echo JHTML::_('tooltip', JText::_('COM_ROKCANDY_TIP_DESC'), '', 'tooltip.png');?></li>

				<li><?php echo $this->form->getLabel('catid'); ?>
				<?php echo $this->form->getInput('catid'); ?></li>

				<li><?php echo $this->form->getLabel('published'); ?>
				<?php echo $this->form->getInput('published'); ?></li>

				<li><?php echo $this->form->getLabel('ordering'); ?>
				<?php echo $this->form->getInput('ordering'); ?></li>

				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
			</ul>
		</fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<div class="clr"></div>
