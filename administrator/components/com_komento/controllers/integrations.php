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

class KomentoControllerIntegrations extends JController
{
	public function change()
	{
		$component = JRequest::getCmd( 'target', 'com_content' );
		$mainframe = JFactory::getApplication();
		$mainframe->redirect( 'index.php?option=com_komento&view=integrations&component=' . $component );
	}

	public function apply()
	{
		$this->doSave();

		$layout		= Jstring::strtolower(JRequest::getString( 'active' , '' ));
		$child		= Jstring::strtolower(JRequest::getString( 'activechild' , '' ));

		$mainframe	= JFactory::getApplication();
		$mainframe->redirect( 'index.php?option=com_komento&view=integrations&active=' . $layout . '&activechild=' . $child );
	}

	public function cancel()
	{
		$mainframe	= JFactory::getApplication();
		$mainframe->redirect( 'index.php?option=com_komento' );
	}

	public function save()
	{
		$this->doSave();

		$mainframe	= JFactory::getApplication();
		$mainframe->redirect( 'index.php?option=com_komento' );
	}

	private function doSave()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe	= JFactory::getApplication();

		if( !JRequest::getMethod() == 'POST' )
		{
			$mainframe->enqueueMessage( JText::_('COM_KOMENTO_SETTINGS_STORE_INVALID_REQUEST', 'error') );
			return false;
		}

		// Unset unecessary post data.
		$post	= JRequest::get( 'POST' );
		unset( $post['active'] );
		unset( $post['activechild'] );
		unset( $post['task'] );
		unset( $post['option'] );
		unset( $post['c'] );

		$token = Komento::_( 'getToken' );
		unset( $post[$token] );

		// custom field that requires processing before save should all goes here
		if( Komento::joomlaVersion() >= '1.6' )
		{
			if( array_key_exists( 'email_regex', $post ) )
			{
				$post['email_regex'] = addslashes( $post['email_regex'] );
			}
			if( array_key_exists( 'website_regex', $post ) )
			{
				$post['website_regex'] = addslashes( $post['website_regex'] );
			}
		}

		// check the target component
		if ( !$post['component'] )
		{
			$mainframe->enqueueMessage( JText::_('COM_KOMENTO_SETTINGS_MISSING_TARGET_COMPONENT') );
			return false;
		}

		// rememeber user's choice
		$mainframe->setUserState('com_komento.integrations.component', $post['component']);

		// Overwrite the value by using getVar to preserve the html tag
		$post['tnc']	= JRequest::getVar( 'tnc', '', 'post', 'string', JREQUEST_ALLOWRAW );

		// Fix multiple select
		if( !array_key_exists( 'allowed_categories', $post ) )
		{
			$post['allowed_categories'] = array(0);
		}
		if( !array_key_exists( 'requires_moderation', $post ) )
		{
			$post['requires_moderation'] = array(0);
		}

		// Check ACL exist for component
		$aclModel = Komento::getModel( 'acl', true );
		$aclComponents = $aclModel->getComponents();
		if( !in_array( $post['component'], $aclComponents ) )
		{
			$aclModel->updateUserGroups( $post['component'] );
		}

		// Save post data
		$model	= $this->getModel( 'settings' );

		if ( !$model->save($post) )
		{
			$mainframe->enqueueMessage( JText::_('COM_KOMENTO_SETTINGS_STORE_ERROR', 'error') );
			return false;
		}

		$mainframe->enqueueMessage( JText::_('COM_KOMENTO_SETTINGS_STORE_SUCCESS', 'message') );

		// Clear the component's cache
		$cache = JFactory::getCache('com_komento');
		$cache->clean();

		return true;
	}
}
