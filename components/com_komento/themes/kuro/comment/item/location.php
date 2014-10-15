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
defined( '_JEXEC' ) or die( 'Restricted access' );

if( $system->config->get( 'enable_location' ) ) { ?>
<span class="kmt-location">
	<?php if( $row->address && $row->latitude && $row->longitude ) { ?>
		<?php echo JText::_( 'COM_KOMENTO_COMMENT_FROM' );?> <a href="http://maps.google.com/maps?z=15&amp;q=<?php echo $row->latitude;?>,<?php echo $row->longitude;?>" target="_blank"><?php echo $row->address;?></a>
	<?php } else {
		if( $row->address ) {
			echo JText::_( 'COM_KOMENTO_COMMENT_FROM' ) . $row->address;
		}
	} ?>
</span>
<?php }
