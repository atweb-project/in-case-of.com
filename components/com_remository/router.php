<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

// NOTE: LOWER CASE URI - If you want Remository URIs to be all lower case, please change the
// following definition from 0 to 1:

if (!defined( '_VALID_MOS' ) AND !defined('_JEXEC')) die( sprintf ('Direct Access to %s is not allowed.', __FILE__ ));

$cmsapi_addon_topdir = 'mambots/plugins/modules/components';
$cmsapi_addon_tops = explode('/', $cmsapi_addon_topdir);
$cmsapi_mydir = array_reverse(explode('/', str_replace('\\', '/', __FILE__)));
do $cmsapi_shifted = array_shift($cmsapi_mydir); while (!in_array($cmsapi_shifted, $cmsapi_addon_tops));
$cmsapi_absolute_path = implode('/', array_reverse($cmsapi_mydir));

require_once($cmsapi_absolute_path.'/components/com_remository/remository.interface.php');

// SEF handling for Joomla internal system

// This is passed an associative array of URI query values,
// and must return an array of SEF'd values

function remositoryBuildRoute (&$query) {
	foreach ($query as $key=>$value) {
		$item[] = strtolower($key).'='.$value;
		if ('option' != $key AND 'Itemid' != $key) unset ($query[$key]);
	}
	$sefstring = isset($item) ? sef_remository::create(implode('&', $item), _REMOSITORY_SEF_LOWER_CASE) : '';
	return $sefstring ? explode('/', $sefstring) : array();
}

// This is passed an associative array of SEF'd values,
// and must return an array of query string values

function remositoryParseRoute ($segments) {
	$replacements = 1;
	foreach ($segments as $key=>$segment) $segments[$key] = str_replace(':', '-', $segment, $replacements);
	foreach (explode('&', substr(sef_remository::revert($segments, -2), 1)) as $part) {
		$dv = explode('=', $part);
		if (1 < count($dv)) $results[$dv[0]] = $dv[1];
	}
	return isset($results) ? $results : array();
}
