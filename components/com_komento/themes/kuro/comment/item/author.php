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

defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<span class="kmt-author"<?php if( $system->konfig->get( 'enable_schema' ) ) echo ' itemprop="creator"'; ?>>
	<?php if( $row->author->guest ) {
		if( !empty( $row->url ) ) { ?>
			<a href="<?php echo $row->url; ?>"<?php if( $system->konfig->get( 'enable_schema' ) ) echo ' itemprop="url"'; ?>>
		<?php }
	} else { ?>
		<a href="<?php echo $row->author->getProfileLink( $row->email ); ?>"<?php if( $system->konfig->get( 'enable_schema' ) ) echo ' itemprop="url"'; ?>>
	<?php } ?>

	<span<?php if( $system->konfig->get( 'enable_schema' ) ) echo ' itemprop="name"'; ?>><?php echo $row->name; ?></span>

	<?php if( ( $row->author->guest && !empty( $row->url ) ) || !$row->author->guest ) { ?>
			</a>
	<?php } ?>
</span>
