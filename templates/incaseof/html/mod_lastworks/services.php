<?php
/**
 * @autor       Valentín García
 * @website     www.valentingarcia.com.mx
 * @package		Joomla.Site
 * @subpackage	mod_lastworks
 * @copyright	Copyright (C) 2012 Valentín García. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

if(count($articles)) {

	echo '<div class="entry ' . $moduleclass_sfx . '">
        <div class="config">';
	
			foreach($articles as $article) {//<--A2.
		
				$images = json_decode($article->images);
				$category = modLastWorksHelper::getCategoryLW( $article->catid );
		
				echo '<div class="item">
					<h2>' . $article->title . '</h2>';
				
					//content
					echo $article->introtext;
					
					//image
					if($images->image_intro){
						echo '<div class="thumbnail"><img src="' . JURI::base() . $images->image_intro .'" alt="' . htmlspecialchars($article->title) . '" /></div>';
					}
				
				echo '</div>';
				
			}//.A2-->
		
		echo '</div>
	</div>';

}else{
	
	echo '<p><span class="vg-alert-message">' . JText::_('VG_SK_ALERT_SLIDESHOW') . '</span></p>';
	
}
?>

