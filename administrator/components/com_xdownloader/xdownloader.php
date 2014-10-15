<?php
/*------------------------------------------------------------------------
# com_xdownloader - xDownloader alpha component
# ------------------------------------------------------------------------
# author    Dmitri Gorbunov
# copyright Copyright (C) 2012 xrbyte.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.xrbyte.com
# Technical Support:  Forum - http://www.xrbyte.com/forum
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// import joomla controller library
jimport('joomla.application.component.controller');

// import XDownloaderHelper
JLoader::register('XDownloaderHelper', JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'xdownloaderhelper.php');


// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_users')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller = JController::getInstance('Xdownloader');

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();

?>