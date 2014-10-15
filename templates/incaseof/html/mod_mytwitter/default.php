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

if( $vg_html ){

	echo '<div class="entry">
        <div class="column one_third"><a href="' . htmlspecialchars($params->get('moduleclass_sfx')) . '"><img class="alignnone size-full wp-image-218 vg-twitter-avatar" alt="' . $user . '" src="' . htmlspecialchars($params->get('moduleclass_sfx')) . '" /></a></div>
        <div class="column two_third last">';
          
			echo $vg_html;
		  
        echo '<div class="clearfix"></div>
      </div>';

}else{
	echo '<p style="color:#fff; background:#666; padding:10px;">You must add HTML code from Twitter Widget Generator through your Twitter account: <a href="https://twitter.com/settings/widgets">https://twitter.com/settings/widgets</a></p>';
}