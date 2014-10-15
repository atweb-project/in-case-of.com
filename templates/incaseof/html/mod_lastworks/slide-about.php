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

	echo '<div class="entry">
        <div class="flexslider vg-slide-about-' . $id_ . '">
          <ul class="slides maxHeight">';
		  
			foreach($articles as $article) {//<--A2.
			
				$images = json_decode($article->images);
				
				//image
				if($images->image_intro){
					echo '<li><img src="' . JURI::base() . $images->image_intro .'" alt="' . htmlspecialchars($article->title) . '" /></li>';
				}else{
					echo '<li>' . JText::_('VG_SK_ABOUT_SLIDE') . $article->title . '</li>';
				}
				
			}//.A2-- >
			
          echo '</ul>
        </div>
	</div>';

}else{
	
	echo '<p><span class="vg-alert-modules">' . JText::_('VG_SK_ALERT_SLIDESHOW') . '</span></p>';
	
}
?>

<script>
//adding height to the top
jQuery(document).ready(function($){
	$('.<?php echo 'vg-slide-about-' . $id_; ?>').flexslider({
		slideshow: false
	});
});
</script>

