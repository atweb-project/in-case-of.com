<?php
/**
 * @package		Joomla.Site
 * @subpackage	Templates.vg_simplekey
 * @copyright	Copyright (C) 2012 Valentín García - http://www.valentingarcia.com.mx - All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

//top
function modChrome_top($module, &$params, &$attribs){

	echo '<div class="vg-top">';
	
		echo $module->content;
	
	echo '</div>';
	
}

//mymenu
function modChrome_mymenu($module, &$params, &$attribs){

	echo $module->content;
	
}
//slideshow
function modChrome_slideshow($module, &$params, &$attribs){

	echo $module->content;
	
}

//blocks
function modChrome_blocks($module, &$params, &$attribs){

	$mytitle = $module->title;
	
	if ($module->showtitle) {
		echo '<hgroup class="title"><h1>' . $mytitle . '</h1></hgroup>';
	}
	
	echo $module->content;
	
}

//blockstop
function modChrome_blockstop($module, &$params, &$attribs){

	$mytitle = $module->title;
	
	if ($module->showtitle) {
		echo '<hgroup class="title"><h1>' . $mytitle . '</h1></hgroup>';
	}
	
	echo '<div class="entry">' . $module->content . '</div>';
	
}

//footer
function modChrome_footer($module, &$params, &$attribs){

	echo $module->content;
	
}
