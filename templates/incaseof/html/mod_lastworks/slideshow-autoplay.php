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

	echo '<section id="featured" class="flexslider">
		<ul class="slides">';
	
			foreach($articles as $article) {//<--A2.
		
				$images = json_decode($article->images);
				$category = modLastWorksHelper::getCategoryLW( $article->catid );
		
				echo '<li>';
				
					//content
					echo $article->introtext;
					
					//image
					if($images->image_intro){
						echo '<div class="slide_bg" style="background:#000000"><img src="' . JURI::base() . $images->image_intro .'" alt="' . htmlspecialchars($article->title) . '" /></div>';
					}
				
				echo '</li>';
				
			}//.A2-->
		
		echo '</ul>
	</section>';

}else{
	
	echo '<p><span class="vg-alert-message">' . JText::_('VG_SK_ALERT_SLIDESHOW') . '</span></p>';
	
}
?>

<script>
//adding height to the top
jQuery(document).ready(function($){
	$('.home #top').addClass('vg-TopHeight');
	$('.flexslider').flexslider({
		slideshow: true
	});
});
</script>

