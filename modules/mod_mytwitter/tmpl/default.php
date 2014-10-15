<?php
/**
 * @autor       Valentín García
 * @website     www.valentingarcia.com.mx
 * @package		Joomla.Site
 * @subpackage	mod_mytwitter
 * @copyright	Copyright (C) 2012 Valentín García. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

if( $user ){

	echo '<ul id="twitter_update_list"></ul>
		<script>twitter_latest_posts(\'' . $user . '\',' . $number . ',\'twitter_update_list\');</script>
		<p class="align-right text-content twitter-user-link">
			<b><a href="http://twitter.com/' . $user . '" target="_blank" class="font-2 icon-twitter">Follow @' . $user . '</a></b>
		</p>';

}else{
	echo '<p>You must set a twitter user.</p>';
}