<?php
/**
 * @autor       Valentín García
 * @website     www.valentingarcia.com.mx
 * @package		Joomla.Site
 * @subpackage	mod_myisotope
 * @copyright	Copyright (C) 2012 Valentín García. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

//vars
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));//suffix
$id_ = $module->id; //Moduleid
$layout = $params->get('vglayout', 'default');

$status = array(
	$params->get('vgplan1_status', 1),
	$params->get('vgplan2_status', 1),
	$params->get('vgplan3_status', 1),
	$params->get('vgplan4_status', 1)
);
$names = array(
	$params->get('vgplan1_name'),
	$params->get('vgplan2_name'),
	$params->get('vgplan3_name'),
	$params->get('vgplan4_name')
);
$prices = array(
	$params->get('vgplan1_price'),
	$params->get('vgplan2_price'),
	$params->get('vgplan3_price'),
	$params->get('vgplan4_price')
);
$contents = array(
	$params->get('vgplan1_content'),
	$params->get('vgplan2_content'),
	$params->get('vgplan3_content'),
	$params->get('vgplan4_content')
);
$txtlinks = array(
	$params->get('vgplan1_txtlink'),
	$params->get('vgplan2_txtlink'),
	$params->get('vgplan3_txtlink'),
	$params->get('vgplan4_txtlink')
);
$links = array(
	$params->get('vgplan1_link'),
	$params->get('vgplan2_link'),
	$params->get('vgplan3_link'),
	$params->get('vgplan4_link')
);

require JModuleHelper::getLayoutPath( 'mod_mypricing', $layout );
