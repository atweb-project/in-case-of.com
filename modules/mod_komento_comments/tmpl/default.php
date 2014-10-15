<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access'); ?>

<div class="kmt-mod modKomentoComments kmt-mod-comments<?php echo $params->get( 'moduleclass_sfx' ) ?>">
	<?php foreach( $comments as $row ) {
		// initialise current row component
		Komento::setCurrentComponent( $row->component );
		$config = Komento::getConfig();

		$row = Komento::getHelper( 'comment' )->process( $row );

		if( !$row ) continue;

		$row->comment = strip_tags( $row->comment );

		// trim comment length
		if( JString::strlen( $row->comment ) > $params->get( 'maxcommentlength' ) )
		{
			$row->comment = JString::substr( $row->comment, 0, $params->get( 'maxcommentlength' ) ) . '...';
		}

		// trim title length
		if( JString::strlen( $row->contenttitle ) > $params->get( 'maxtitlelength' ) )
		{
			$row->contenttitle = JString::substr( $row->contenttitle, 0, $params->get( 'maxtitlelength' ) ) . '...';
		}
	?>

		<div class="mod-item <?php echo 'kmt-' . $row->id; ?>">

			<div class="mod-comment-head clearfix">
				<i></i>

				<!-- Avatar -->
				<div class="mod-avatar">
					<?php if( !Komento::getProfile( $row->created_by )->guest ) { ?>
						<a href="<?php echo Komento::getProfile( $row->created_by )->getProfileLink(); ?>">
					<?php } ?>
					<img src="<?php echo Komento::getProfile( $row->created_by )->getAvatar(); ?>" class="avatar" width="30" />
					<?php if( !Komento::getProfile( $row->created_by )->guest ) { ?>
						</a>
					<?php } ?>
				</div>

				<!-- Author -->
				<?php if( $config->get( 'layout_avatar_integration' ) == 'gravatar' && !empty($row->url) ) { ?>
					<span class="kmt-author">
						<a href="<?php echo $row->url; ?>" target="_blank"><b><?php echo $row->name; ?></b></a>
					</span>
				<?php } else { ?>
					<span class="kmt-author">
						<?php if( !$row->author->guest ) { ?>
							<a href="<?php echo $row->author->getProfileLink(); ?>">
						<?php } ?>
							<b><?php echo $row->name; ?></b>
						<?php if( !$row->author->guest ) { ?>
							</a>
						<?php } ?>
					</span>
				<?php } ?>
			</div>

			<!-- Text -->
			<div class="mod-comment-text">
				<?php echo $row->comment; ?>

				<!-- Title -->
				<?php if( $params->get( 'showtitle' ) ) { ?>
				<div class="mod-comment-page">
					<a href="<?php echo $row->pagelink; ?>"><?php echo $row->contenttitle; ?></a> <?php echo JText::sprintf( 'COM_KOMENTO_TITLE_IN_COMPONENT', $row->componenttitle ); ?>
				</div>
				<?php } ?>
			</div>

			<div class="mod-comment-meta small">
				<!-- Time and Permalink -->
				<span class="mod-comment-time">
					<a class="mod-comment-permalink" href="<?php echo $row->permalink; ?>" alt="<?php echo JText::_( 'COM_KOMENTO_COMMENT_PERMANENT_LINK' ); ?>" title="<?php echo JText::_( 'COM_KOMENTO_COMMENT_PERMANENT_LINK' ); ?>"><?php echo $row->created; ?></a>
				</span>
			</div>

		</div>
	<?php } ?>
</div>
