<?php
/**
 * @version     1.0.0
 * @package     com_easyorderexport
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author       <> - 
 */


// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easyorderexport/assets/css/easyorderexport.css');
// Import JS
//$document->addScript("components/com_easyorderexport/assets/js/jquery-1.9.1.js");
?>