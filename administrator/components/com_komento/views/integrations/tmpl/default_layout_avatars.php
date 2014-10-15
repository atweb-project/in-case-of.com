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
			<legend><?php echo JText::_( 'COM_KOMENTO_LAYOUT_AVATAR' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Avatar Integration -->
					<?php
						$options = array();
						$options[] = $this->renderOption( 'communitybuilder', 'Community Builder' );
						$options[] = $this->renderOption( 'easyblog', 'Easyblog' );
						$options[] = $this->renderOption( 'easydiscuss', 'EasyDiscuss' );
						$options[] = $this->renderOption( 'k2', 'K2' );
						$options[] = $this->renderOption( 'gravatar', 'Gravatar' );
						$options[] = $this->renderOption( 'jomsocial', 'Jomsocial' );
						$options[] = $this->renderOption( 'kunena', 'Kunena' );
						$options[] = $this->renderOption( 'phpbb', 'PHPBB' );

						// $options[] = $this->renderOption( 'anahita', 'Anahita');
						// $options[] = $this->renderOption( 'mightyregistration', 'Mighty Registration');

						echo $this->renderSetting( 'COM_KOMENTO_LAYOUT_AVATAR_INTEGRATION', 'layout_avatar_integration', 'dropdown', $options );
					?>

					<!-- Use Komento Profile -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_LAYOUT_AVATAR_USE_KOMENTO_PROFILE', 'use_komento_profile' ); ?>

					<!-- PHPBB Path -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_LAYOUT_LAYOUT_PHPBB_PATH', 'layout_phpbb_path', 'input', '60' ); ?>

					<!-- PHPBB Url -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_LAYOUT_LAYOUT_PHPBB_URL', 'layout_phpbb_url', 'input', '60' ); ?>

				</tbody>
			</table>
			</fieldset>
		</td>
		<td width="50%" valign="top">
		</td>
	</tr>
</table>
