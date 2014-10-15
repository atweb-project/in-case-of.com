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
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class KomentoViewComments extends JView
{
	function stick()
	{
		$ajax = Komento::getHelper( 'ajax' );
		$model = Komento::getModel( 'comments' );
		$ids = JRequest::getVar( 'ids' );

		if( $model->stick( $ids ) )
		{
			$ajax->success();
		}
		else
		{
			$ajax->fail();
		}

		$ajax->send();

	}

	function unstick()
	{
		$ajax = Komento::getHelper( 'ajax' );
		$model = Komento::getModel( 'comments' );
		$ids = JRequest::getVar( 'ids' );

		if( $model->unstick( $ids ) )
		{
			$ajax->success();
		}
		else
		{
			$ajax->fail();
		}

		$ajax->send();
	}

	function publish()
	{
		$ajax = Komento::getHelper( 'ajax' );
		$model = Komento::getModel( 'comments' );
		$ids = JRequest::getVar( 'ids' );

		if( $model->publish( $ids ) )
		{
			$ajax->success();
		}
		else
		{
			$ajax->fail();
		}

		$ajax->send();
	}

	function unpublish()
	{
		$ajax = Komento::getHelper( 'ajax' );
		$model = Komento::getModel( 'comments' );
		$ids = JRequest::getVar( 'ids' );

		if( $model->unpublish( $ids ) )
		{
			$ajax->success();
		}
		else
		{
			$ajax->fail();
		}

		$ajax->send();
	}

	function loadReplies()
	{
		$ajax = Komento::getHelper( 'ajax' );
		$model = Komento::getModel( 'comments' );
		$options['parent_id'] = JRequest::getInt( 'parentId' );

		$startCount = JRequest::getInt( 'startCount' );

		$commentsModel	= Komento::getModel( 'comments' );
		$comments		= $commentsModel->getData($options);
		$count = count( $comments );

		$this->assign( 'comments', $comments );
		$this->assign( 'search', '' );
		$this->assign( 'startCount', $startCount );
		$html = $this->loadTemplate('list');

		$ajax->success($html);
		$ajax->send();
	}
}
