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
?>

<div class="portfolio">

    <?php
		if(count($categories)) {//<--A1.
		
		echo '<ul id="portfolio-filter">';
		
			echo '<li><a href="#" class="current" data-filter="*">' . JText::_('VG_FS_ALL') . '</a></li>';
			
            foreach($categories as $category) {

				echo '<li><a href="#" class="current" data-filter=".mycat-' . $category->id . '">' . $category->title . '</a></li>';
			
			}
			
        echo '</ul>';
		
		}//.A1-->
		?>
		
		<?php
		if(count($articles)) {//<--A2.
		
			echo '<ul class="portfolio-4 portfolio-list" id="portfolio-list">';
				
				foreach($articles as $article) {//<--A3.
				
					$images = json_decode($article->images);
					
					echo '<li class="mycat-' . $article->catid . '">
						<div class="column-5">
							<h3><a href="' . ContentHelperRoute::getArticleRoute(  $article->id,  $article->catid ) . '">' . $article->title . '</a></h3>
							<div class="viewport">
								<span>';
									
									if( $images->image_fulltext != '' && file_exists($images->image_fulltext) ){ 
										echo '<a href="' . JURI::base() . $images->image_fulltext . '" class="zoom fancybox"></a>';
									}else{
										echo '<a href="#" class="zoom fancybox"></a>';
									}
									
									echo '<a href="' . ContentHelperRoute::getArticleRoute(  $article->id,  $article->catid ) . '" class="more"></a>
								</span>
								<a href="#" class="fancybox img"><img src="' . JURI::base() . $images->image_intro .'" alt="' . htmlspecialchars( $article->title ) . '" /></a>
							</div>
						</div>
					</li>';
					
				}//.A3-->
					
			echo '</ul>';
		
		}//.A2-->
		?>
</div>
            