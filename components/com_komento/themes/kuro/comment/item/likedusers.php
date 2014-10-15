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
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<div class="kmt-liked-user-list">
	<?php if( count( $likedUsers ) > 0 ) { ?>
	<ul class="likedUsers kmt-like-users">
		<?php foreach( $likedUsers as $user ) { ?>
		<li class="likedUser">
			<?php if( $system->config->get( 'layout_avatar_enable' ) ) { ?>
				<a class="kmt-like-user-image" href="<?php echo $user->author->getProfileLink(); ?>"><img src="<?php echo $user->author->getAvatar(); ?>" /></a>
			<?php } ?>
			<span><a class="kmt-like-user-name" href="<?php echo $user->author->getProfileLink(); ?>"><?php echo $user->author->getName(); ?></a></span>
		</li>
		<?php } ?>
	</ul>
	<?php } else {
		echo JText::_( 'COM_KOMENTO_COMMENT_NO_USER_LIKE_THIS' );
	} ?>
</div>
