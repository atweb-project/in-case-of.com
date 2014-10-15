<?php
/**
  * @version   $Id:$
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */
// no direct access
defined('_JEXEC') or die('Restricted access');

// Make sure the user is authorized to view this page
$user = JFactory::getUser();


// Get the media component configuration settings
$params = JComponentHelper::getParams('com_rokcandy');

// Require the base controller
require_once (JPATH_COMPONENT . DS . 'controller.php');

$cmd = JRequest::getCmd('task', null);


$controller = new RokCandyController();

// Set the model and view paths to the administrator folders
$controller->addViewPath(JPATH_COMPONENT_ADMINISTRATOR.DS.'views');
$controller->addModelPath(JPATH_COMPONENT_ADMINISTRATOR.DS.'models');

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();
