<?php
/*------------------------------------------------------------------------
# mod_sp_google_chart - Google Chart module for Joomla by JoomShaper.com
# ------------------------------------------------------------------------
# author    JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2012 JoomShaper.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomshaper.com
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');
//Parameters
$uniqid 				= $module->id;

$width					= $params->get ('width');
$height					= $params->get ('height');
$chart_data				= $params->get ('chart_data');
$chart_title			= $params->get ('chart_title');
$hAxis					= $params->get ('hAxis');
$vAxis					= $params->get ('vAxis');
$colors					= $params->get ('colors');
$is3D					= $params->get ('is3D');
$chart_galllery			= $params->get ('chart_galllery');

/*Package*/
$package = 'corechart';
if ($chart_galllery=='Gauge') {
	$package = 'gauge';
} else if ($chart_galllery=='GeoChart') {
	$package = 'geochart';
} else if ($chart_galllery=='Table') {
	$package = 'table';
} else if ($chart_galllery=='TreeMap') {
	$package = 'treemap';
} else {
	$package = 'corechart';
}

/*Options*/
$options = '';
if ($width) 
	$options .= ",width: {$width}";

if ($height) 
	$options .= ",height: {$height}";
	
if ($chart_title) 
	$options .= ",title: \"{$chart_title}\"";
	
if ($hAxis) 
	$options .= ",hAxis: {title: \"{$hAxis}\"}";
	
if ($vAxis) 
	$options .= ",vAxis: {title: \"{$vAxis}\"}";
	
if ($colors) {
	$colors = explode(',', $colors);
	$cols = array();
	foreach ($colors as $color) $cols[] = "'" . trim($color) . "'";
	$colors = '[' . implode ($cols, ', ') . ']';
	$options .= ",colors: {$colors}";
}

if ($is3D==1) 
	$options .= ',is3D: true';

$scripts='google.load("visualization", "1", {packages:["' . $package . '"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable(['.trim($chart_data,',').']);
        var options = {'.trim($options, ',').'};
        var chart = new google.visualization.' . $chart_galllery . '(document.getElementById("sp_chart_div_' . $uniqid . '"));
        chart.draw(data, options);
      }';
	  
$doc = JFactory::getDocument();
$doc->addScript('https://www.google.com/jsapi');//Add chart api script
$doc->addScriptDeclaration ($scripts);
require(JModuleHelper::getLayoutPath('mod_sp_google_chart'));//Load layout