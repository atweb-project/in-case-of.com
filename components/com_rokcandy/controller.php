<?php
/**
  * @version   $Id:$
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */
// no direct access
defined('_JEXEC') or die('Restricted access'); 
 
jimport( 'joomla.application.component.controller' ); 
 
class RokCandyController extends JController
{
    function __construct($config = array())
	{
		// RokGallery image picker proxying:
		if (JRequest::getCmd('view') === 'candymacros' && JRequest::getCmd('layout') === 'list') {
            JHtml::_('stylesheet','system/adminlist.css', array(), true);
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}
        // Article frontpage Editor article proxying:
		elseif(JRequest::getCmd('view') === 'candymacros' && JRequest::getCmd('layout') === 'default') {
			JHtml::_('stylesheet','system/adminlist.css', array(), true);
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}

		parent::__construct($config);
	}

	function display()
	{
		$vName = JRequest::getCmd('task', 'default');
		switch ($vName)
		{
			case 'default':
			default:
				$vLayout = JRequest::getCmd( 'layout', 'default' );
				$mName = 'candymacros';
				$vName = 'candymacros';

				break;
		}

		$document = JFactory::getDocument();
		$vType		= $document->getType();

		// Get/Create the view
		$view = $this->getView( $vName, $vType);
		$view->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.strtolower($vName).DS.'tmpl');

		// Get/Create the model
		if ($model = $this->getModel($mName)) {
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		// Set the layout
		$view->setLayout($vLayout);

		// Display the view
		$view->display();
	}
}