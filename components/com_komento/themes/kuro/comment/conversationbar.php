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
if( $system->my->allow( 'read_comment' ) ) {

if( $system->config->get( 'enable_conversation_bar' ) && count( $comments ) > 0 ) {
$authors = Komento::getModel( 'comments' )->getConversationBarAuthors( $component, $cid );
?>

<div class="conversationBar kmt-conversation-bar">
	<h3 class="kmt-title"><?php echo JText::_( 'COM_KOMENTO_COMMENT_CONVERSATION_BAR_TITLE' ); ?></h3>
	<ul class="kmt-people-list reset-ul float-li clearfix">

		<!-- Avatar mode -->
		<?php if( $system->config->get( 'layout_avatar_enable' ) ) { ?>
			<!-- Render registered user -->
			<?php foreach( $authors->registered as $author ) { ?>
			<li>
				<a href="<?php echo Komento::getProfile( $author )->getProfileLink(); ?>">
					<img src="<?php echo Komento::getProfile( $author )->getAvatar(); ?>" class="avatar" />
					<b><i></i><?php echo Komento::getProfile( $author )->getName(); ?></b>
				</a>
			</li>
			<?php } ?>

			<!-- Render guests (if settings set to not include guest, then guest will be empty) -->
			<?php foreach( $authors->guest as $author ) { ?>
			<li>
				<a href="javascript: void(0);">
					<img src="<?php echo Komento::getProfile( '0' )->getAvatar(); ?>" class="avatar" />
					<b><i></i><?php echo $author; ?></b>
				</a>
			</li>
			<?php } ?>

		<!-- No avatar mode -->
		<?php } else { ?>
			<!-- Render registered user -->
			<?php foreach( $authors->registered as $author ) { ?>
			<li>
				<a href="<?php echo Komento::getProfile( $author )->getProfileLink(); ?>"><?php echo Komento::getProfile( $author )->getName(); ?></a>
			</li>
			<?php } ?>

			<!-- Render guests (if settings set to not include guest, then guest will be empty) -->
			<?php foreach( $authors->guest as $author ) { ?>
			<li><?php echo $author; ?></li>
			<?php } ?>
		<?php } ?>
	</ul>
</div>
<?php } ?>
<?php } ?>
