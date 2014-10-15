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

echo '<div class="container">';

if(count($articles)) {

	foreach($articles as $article) {//<--A2.
	
		$images = json_decode($article->images);
		$category = modLastWorksHelper::getCategoryLW( $article->catid );
		
		echo '<div class="work-item column-one-third">
			<div class="view-item">
				<img src="' . JURI::base() . $images->image_intro .'" alt="' . $article->title . '" />
				<div class="mask">
					' . $article->introtext . '
					<a href="' . JURI::base() . $images->image_fulltext .'" class="info fancybox" data-fancybox-group="gallery" title="' . $article->title . '"></a>
				</div>
			</div>
			<h4>' . $article->title . '</h4>
			<p>' . $category . '</p>
		</div>';
		
	}//.A2-->

}

echo '</div>';