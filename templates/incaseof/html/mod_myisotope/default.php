<?php
/**
 * @autor       Valent�n Garc�a
 * @website     www.valentingarcia.com.mx
 * @package		Joomla.Site
 * @subpackage	mod_lastworks
 * @copyright	Copyright (C) 2012 Valent�n Garc�a. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>

<div class="entry">

    <?php
		if(count($categories)) {//<--A1.
			if(count($categories) != 1) {   //Custom condition addon for in-case-of.com
		
		echo '<nav id="filter" data-option-key="filter" class="tax inverse">
			<ul>';
		
				echo '<li class="filter_current"><a href="#fliter" data-filter="*">' . JText::_('VG_SK_ALL') . '</a></li>';
			
					foreach($categories as $category) {
						

						echo '<li><a href="javascript:void(0)" data-filter=".mycat-' . $category->id . '">' . $category->title . '</a></li>';
					
				
		}
			echo '</ul>
		</nav>';
			
			}
		}//.A1-->
		?>
		
		<?php
		if(count($articles)) {//<--A2.
		
			echo '<div class="portfolios columns3">';
				
				foreach($articles as $article) {//<--A3.
				
					$images = json_decode($article->images);
					
					echo '<div class="portfolio-item mycat-' . $article->catid . '" data-url="' . JURI::base() . ContentHelperRoute::getArticleRoute(  $article->id,  $article->catid ) . '?&tmpl=ajax">
						<a class="overlay ajax" href="javascript:void(0)" title="' . htmlspecialchars($article->title) . '">
							<h3>' . $article->title . '</h3>
							<p class="intro">' . strip_tags($article->introtext) . '</p>
						</a>
						<div class="tools"><span data-url="' . JURI::base() . ContentHelperRoute::getArticleRoute(  $article->id,  $article->catid ) . '?&tmpl=ajax"><a href="javascript:void(0)" class="zoomin ajax" title="' . htmlspecialchars($article->title) . '">ZoomIn</a></span><a href="' . JURI::base() . ContentHelperRoute::getArticleRoute(  $article->id,  $article->catid ) . '" class="info">Info</a></div>
						<a href="javascript:void(0)" class="item ajax"><img src="' . JURI::base() . $images->image_intro . '" alt="' . htmlspecialchars($article->title) . '" /></a>
					</div>';
					
				}//.A3-->
					
			echo '</div>';
		
		}//.A2-->
		?>
</div>
            