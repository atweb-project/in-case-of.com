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
<table class="noshow">
	<tr>
		<td valign="top" width="50%">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_WORKFLOW_GENERAL' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Enable Comments on this Component -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ENABLE_SYSTEM', 'enable_komento' ); ?>

				</tbody>
			</table>
			</fieldset>

			<?php if( method_exists( $this->componentObj , 'getCategories' ) ){ ?>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_CATEGORIES' ); ?></legend>
			<p class="small"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_CATEGORIES_INFO' ); ?></p>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Categories Assignment -->
					<?php $options = array();
						$options[] = $this->renderOption( '0', 'COM_KOMENTO_SETTINGS_CATEGORIES_ON_ALL_CATEGORIES' );
						$options[] = $this->renderOption( '1', 'COM_KOMENTO_SETTINGS_CATEGORIES_ON_SELECTED_CATEGORIES' );
						$options[] = $this->renderOption( '2', 'COM_KOMENTO_SETTINGS_CATEGORIES_ON_ALL_CATEGORIES_EXCEPT_SELECTED' );
						$options[] = $this->renderOption( '3', 'COM_KOMENTO_SETTINGS_CATEGORIES_NO_CATEGORIES' );
						echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_CATEGORIES_ASSIGNMENT', 'allowed_categories_mode', 'dropdown', $options );
					?>

					<!-- Enable Comments on this Categories -->
					<tr>
						<td width="300" class="key">
							<span><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ENABLE_ON_CATEGORIES' ); ?></span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_('COM_KOMENTO_SETTINGS_ENABLE_ON_CATEGORIES_DESC'); ?></div>
								<?php echo $this->componentObj->getCategories( $this->config->get( 'allowed_categories' ) , 'allowed_categories[]' );?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			</fieldset>
			<?php } ?>
		</td>
		<td valign="top" width="50%">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_MODERATION' ); ?></legend>
			<p class="small"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_MODERATION_INFO' ); ?></p>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Requires Moderation -->
					<tr>
						<td width="300" class="key">
							<span><?php echo JText::_( 'COM_KOMENTO_SETTINGS_MODERATION_USERGROUP' ); ?></span>
						</td>
						<td valign="top">
							<div class="has-tip">
								<div class="tip"><i></i><?php echo JText::_( 'COM_KOMENTO_SETTINGS_MODERATION_USERGROUP_DESC' ); ?></div>
								<?php echo Komento::getJoomlaUserGroupsSelectionBox( $this->config->get( 'requires_moderation' ), 'requires_moderation[]' ); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SUBSCRIPTION' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Enforce subscription -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SUBSCRIPTION_AUTO', 'subscription_auto' ); ?>

					<!-- Requires confirmation -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SUBSCRIPTION_CONFIRMATION', 'subscription_confirmation' ); ?>

				</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_RSS' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Enable RSS -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_RSS_ENABLE', 'enable_rss' ); ?>

					<!-- Max RSS Items -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_RSS_MAX_ITEMS', 'rss_max_items', 'input' ); ?>

				</tbody>
			</table>
			</fieldset>
		</td>
	</tr>
</table>
