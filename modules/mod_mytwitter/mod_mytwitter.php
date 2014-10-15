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

//vars
$vg_html = $params->get('vg_html');
$layout = $params->get('vglayout', 'default');

require JModuleHelper::getLayoutPath( 'mod_mytwitter', $layout );
