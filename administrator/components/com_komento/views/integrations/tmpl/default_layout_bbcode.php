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
		<td width="50%" valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_FORM_BBCODE' ); ?></legend>
			<table class="admintable bbcode" cellspacing="1">
				<tbody>

					<!-- Enable Bold -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_BBCODE_BOLD', 'bbcode_bold' ); ?>

					<!-- Enable Italic -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_BBCODE_ITALIC', 'bbcode_italic' ); ?>

					<!-- Enable Underline -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_BBCODE_UNDERLINE', 'bbcode_underline' ); ?>

					<!-- Enable Link -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_BBCODE_LINK', 'bbcode_link' ); ?>

					<!-- Enable Picture -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_BBCODE_PICTURE', 'bbcode_picture' ); ?>

					<!-- Enable Bulletlist -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_BBCODE_BULLETLIST', 'bbcode_bulletlist' ); ?>

					<!-- Enable Numericlist -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_BBCODE_NUMERICLIST', 'bbcode_numericlist' ); ?>

					<!-- Enable Bullet -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_BBCODE_BULLET', 'bbcode_bullet' ); ?>

					<!-- Enable Quote -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_BBCODE_QUOTE', 'bbcode_quote' ); ?>

					<!-- Enable Clean -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_BBCODE_CLEAN', 'bbcode_clean' ); ?>

					<!-- Enable Smile -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_BBCODE_SMILE', 'bbcode_smile' ); ?>

					<!-- Enable Happy -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_BBCODE_HAPPY', 'bbcode_happy' ); ?>

					<!-- Enable Surprised -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_BBCODE_SURPRISED', 'bbcode_surprised' ); ?>

					<!-- Enable Tongue -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_BBCODE_TONGUE', 'bbcode_tongue' ); ?>

					<!-- Enable Unhappy -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_BBCODE_UNHAPPY', 'bbcode_unhappy' ); ?>

					<!-- Enable Wink -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_BBCODE_WINK', 'bbcode_wink' ); ?>

				</tbody>
			</table>
			</fieldset>
		</td>
		<td width="50%" valign="top">
		</td>
	</tr>
</table>
