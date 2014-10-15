<?php
/**
* @package  Komento
* @copyright Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require( KOMENTO_ADMIN_ROOT . DS . 'views.php');

class KomentoViewComment extends KomentoAdminView
{
	var $tag	= null;

	function display($tpl = null)
	{
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		if( Komento::joomlaVersion() >= '1.6' )
		{
			if(!$user->authorise('core.manage.comments' , 'com_komento') )
			{
				$mainframe->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				$mainframe->close();
			}
		}

		//Load pane behavior
		jimport('joomla.html.pane');

		$commentId		= JRequest::getVar( 'commentid' , '' );

		$comment		= Komento::getTable( 'Comments' );

		$comment->load( $commentId );

		$this->comment	= $comment;

		// Set default values for new entries.
		if( empty( $comment->created ) )
		{
			$date   = KomentoDateHelper::getDate();
			$now 	= KomentoDateHelper::toFormat($date);

			$comment->created	= $now;
			$comment->published	= true;
		}

		$flags = array('0' => JText::_( 'COM_KOMENTO_NOFLAG' ), '1' => JText::_( 'COM_KOMENTO_SPAM' ), '2' => JText::_( 'COM_KOMENTO_OFFENSIVE' ), '3' => JText::_( 'COM_KOMENTO_OFFTOPIC' ));

		$flagOptions = array();

		foreach($flags as $key=>$value) :
			$flagOptions[] = JHTML::_('select.option', $key, $value);
		endforeach;

		$this->assign( 'flagOptions', $flagOptions );
		$this->assignRef( 'comment'		, $comment );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		JToolBarHelper::back();
		JToolBarHelper::divider();

		if( $this->comment->id != 0 )
		{
	        JToolBarHelper::title( JText::_('COM_KOMENTO_EDITING_COMMENT'), 'comments' );
		}

		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}
}
