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

require_once( KOMENTO_HELPERS . DS . 'string.php' );
require_once( KOMENTO_HELPERS . DS . 'comment.php' );

class KomentoControllerPending extends KomentoController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'add' , 'edit' );
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$comments	= JRequest::getVar( 'cid' , array(0) , 'POST' );
		$message	= '';
		$type		= 'message';

		if( empty( $comments ) )
		{
			$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID' );
			$type		= 'error';
		}
		else
		{
			$model		= Komento::getModel( 'comments' );

			if( $model->remove( $comments ) )
			{
				$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_REMOVED' );
			}
			else
			{
				$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_REMOVE_ERROR' );
				$type		= 'error';
			}
		}

		/*
		$parentId = JRequest::getInt('parentid', 0);
		$parentId = $this->getTable('comments')->load($parentId)->parent_id;
		$this->setRedirect( 'index.php?option=com_komento&view=comments&parentid=' . $parentId , $message , $type );
		*/

		$this->setRedirect( 'index.php?option=com_komento&view=comments' , $message , $type );
	}

	function publish( $publish = '1' )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$comments	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $comments ) <= 0 )
		{
			$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_INVALID_ID' );
			$type		= 'error';
		}
		else
		{
			$model		= Komento::getModel( 'comments' );

			if( $model->publish( $comments , $publish ) )
			{
				if( $publish )
				{
					$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_PUBLISHED' );
				}
				else
				{
					$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_UNPUBLISHED' );
				}
			}
			else
			{
				if( $publish )
				{
					$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_PUBLISHED_ERROR' );
				}
				else
				{
					$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_UNPUBLISHED_ERROR' );
				}
				$type		= 'error';
			}

		}

		$this->setRedirect( 'index.php?option=com_komento&view=pending' , $message , $type );
	}

	function unpublish()
	{
		$this->publish( '0' );
	}

	function clean()
	{
		// access with c=pending&task=clean
		// clean up comments
	}
}
