<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class KomentoControllerMailQ extends JController
{
	public function purge()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$app	= JFactory::getApplication();
		$model	= $this->getModel( 'Mailq' );
		$model->purge();

		$app->redirect( 'index.php?option=com_komento&view=mailq' , JText::_( 'COM_KOMENTO_EMAILS_PURGED' ) , 'success');
	}
}
