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
if( ( $system->config->get( 'layout_frontpage_readmore' ) && ( ( $component == 'com_content' && $article->params->get('show_readmore') && $article->readmore ) || $component != 'com_content' ) ) || $system->config->get( 'layout_frontpage_comment') || $system->config->get( 'layout_frontpage_hits') ) { ?>
<div class="kmt-readon">

	<?php if( $system->config->get( 'layout_frontpage_readmore') && ( ( $component == 'com_content' && $article->params->get('show_readmore') && $article->readmore ) || $component != 'com_content' ) ) { ?>
	<span class="kmt-readmore aligned-<?php echo $system->config->get( 'layout_frontpage_alignment' ); ?>">
		<a href="<?php echo $componentHelper->getContentPermalink();?>" title="<?php echo $this->escape( $componentHelper->getContentTitle() );?>"><?php echo JText::_( 'COM_KOMENTO_FRONTPAGE_READMORE' );?></a>
	</span>
	<?php } ?>

	<?php if( $system->config->get( 'layout_frontpage_comment' ) ) { ?>
	<span class="kmt-comment aligned-<?php echo $system->config->get( 'layout_frontpage_alignment' ); ?>">
		<a href="<?php echo $componentHelper->getContentPermalink(array('anchor'=>'section-komento')); ?>"><?php echo JText::_( 'COM_KOMENTO_FRONTPAGE_COMMENT' );?> (<?php echo $commentCount;?>)</a>
	</span>
	<?php } ?>

	<?php if( $system->config->get( 'layout_frontpage_hits' ) ) { ?>
	<span class="kmt-hits aligned-<?php echo $system->config->get( 'layout_frontpage_alignment' ); ?>">
		<?php echo JText::_( 'COM_KOMENTO_FRONTPAGE_HITS' );?>: <?php echo $componentHelper->getContentHits();?>
	</span>
	<?php } ?>
</div>
<?php } ?>
