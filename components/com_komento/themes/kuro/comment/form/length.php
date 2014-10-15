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
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div class="commentLength kmt-form-length">
	<b><span class="commentLengthCount">0</span>
	<?php if( $system->config->get( 'antispam_max_length_enable' ) == 1 && $system->config->get( 'antispam_max_length' ) > 0 && ( ( $system->config->get( 'antispam_min_length_enable' ) && $system->config->get( 'antispam_max_length' ) > $system->config->get( 'antispam_min_length' ) ) || !$system->config->get( 'antispam_min_length_enable' ) ) ) { ?>
		<span class="commentMaxCount">/ <?php echo $system->config->get( 'antispam_max_length' ) . ' ' . JText::_( 'COM_KOMENTO_FORM_CHARACTER_LIMIT' ); ?></span>
	<?php } else {
		echo JText::_( 'COM_KOMENTO_FORM_CHARACTERS' );
	}  ?>
	</b>
</div>
