<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
Komento.ready( function($) {
	<?php if($this->joomlaVersion >= '1.6') { ?>
		Joomla.submitbutton = function(task) {
			$('#submenu li').children().each( function(){
				if( $(this).hasClass( 'active' ) )
				{
					$( '#active' ).val( $(this).attr('id') );
				}
			});

			$('dl#subtabs').children().each( function(){
				if( $(this).hasClass( 'open' ) )
				{
					$( '#activechild' ).val( $(this).attr('class').split(" ")[0] );
				}

			});

			Joomla.submitform(task);
		}
	<?php } else { ?>
		window.submitbutton = function( action ) {
			$('#submenu li').children().each( function(){
				if( $(this).hasClass( 'active' ) )
				{
					$( '#active' ).val( $(this).attr('id') );
				}
			});

			$('dl#subtabs').children().each( function(){
				if( $(this).hasClass( 'open' ) )
				{
					$( '#activechild' ).val( $(this).attr('id') );
				}
			});

			submitform( action );
		}
	<?php } ?>

	window.changeComponent = function(component) {
		document.adminForm.target.value = component;
		document.adminForm.submit();
	}
} );

</script>
<?php
// There seems to be some errors when suhosin is configured with the following settings
// which most hosting provider does! :(
//
// suhosin.post.max_vars = 200
// suhosin.request.max_vars = 200
if(in_array('suhosin', get_loaded_extensions()))
{
	$max_post		= @ini_get( 'suhosin.post.max_vars');
	$max_request	= @ini_get( 'suhosin.request.max_vars' );

	if( !empty( $max_post ) && $max_post < 300 || !empty( $max_request ) && $max_request < 300 )
	{
?>
	<div class="error" style="background: #ffcccc;border: 1px solid #cc3333;padding: 5px;">
		<?php echo JText::_( 'COM_KOMENTO_SETTINGS_SUHOSIN_CONFLICTS' );?>
		<?php echo JText::_( 'COM_KOMENTO_SETTINGS_SUHOSIN_CONFLICTS_MAX' );?>
		<?php echo JText::_( 'COM_KOMENTO_SETTINGS_SUHOSIN_RESOLVE_MESSAGE' ); ?>
	</div>
<?php
	}
}
?>
<form action="index.php" method="post" name="adminForm" id="settingsForm">
<!-- <div class="kmt-component-select">
	<?php echo JText::_( 'COM_KOMENTO_SETTINGS_SELECT_COMPONENT' ); ?>:
	<select name="target" class="inputbox" onchange="this.form.submit()">
		<?php echo JHtml::_('select.options', $this->components, 'value', 'text', $this->component); ?>
	</select>
</div> -->
<div id="config-document">
	<div id="page-main" class="tab">
		<?php echo $this->loadTemplate('main');?>
	</div>
	<div id="page-antispam" class="tab">
		<?php echo $this->loadTemplate('antispam');?>
	</div>
	<div id="page-layout" class="tab">
		<?php echo $this->loadTemplate('layout');?>
	</div>
	<div id="page-notifications" class="tab">
		<?php echo $this->loadTemplate('notifications');?>
	</div>
	<div id="page-activities" class="tab">
		<?php echo $this->loadTemplate('activities');?>
	</div>
	<div id="page-attachment" class="tab">
		<?php echo $this->loadTemplate('attachment');?>
	</div>
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="active" id="active" value="" />
<input type="hidden" name="activechild" id="activechild" value="" />
<input type="hidden" name="task" value="change" />
<input type="hidden" name="option" value="com_komento" />
<input type="hidden" name="c" value="integrations" />
<input type="hidden" name="component" value="<?php echo $this->escape($this->component); ?>" />
<input type="hidden" name="target" value="<?php echo $this->escape($this->component); ?>" />
</form>
