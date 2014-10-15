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

jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.parameter' );

require_once( KOMENTO_CLASSES . DS . 'themes.php' );
require_once( KOMENTO_HELPERS . DS . 'helper.php' );

class KomentoViewProfile extends JView
{
	function getActivities()
	{
		$loadMore = JRequest::getInt( 'loadMore', 0 );

		$ajax = Komento::getHelper( 'ajax' );
		$model = Komento::getModel( 'activity' );
		$konfig = Komento::getKonfig();

		$uid = JRequest::getInt( 'uid' );
		$start = JRequest::getInt( 'start', 0 );
		$limit = JRequest::getInt( 'limit', 10 );

		$options = array(
			'start' => $start,
			'limit' => $limit
			);

		$type = array();

		if( $konfig->get( 'profile_activities_comments' ) )
		{
			$type[] = 'comment';
		}
		if( $konfig->get( 'profile_activities_replies' ) )
		{
			$type[] = 'reply';
		}
		if( $konfig->get( 'profile_activities_likes' ) )
		{
			$type[] = 'like';
		}

		$options['type'] = implode( ',', $type );

		$activities = $model->getUserActivities( $uid, $options );
		$total = '';

		if( !$loadMore )
		{
			$total = $model->getTotalUserActivities( $uid, $options );
		}
		$count = count( $activities );

		$theme = Komento::getTheme();
		$theme->set( 'items', $activities );
		$theme->set( 'total', $total );

		$html = '';

		if( $loadMore )
		{
			$html = $theme->fetch( 'profile/activities/list.php' );
		}
		else
		{
			$html = $theme->fetch( 'profile/activities.php' );
		}

		$ajax->success( $html, $count, $total);
		$ajax->send();
	}

	function getPopularComments()
	{
		$loadMore = JRequest::getInt( 'loadMore', 0 );

		$ajax = Komento::getHelper( 'ajax' );
		$model = Komento::getModel( 'comments' );

		$uid = JRequest::getInt( 'uid' );
		$start = JRequest::getInt( 'start', 0 );
		$limit = JRequest::getInt( 'limit', 10 );

		$options = array(
			'start' => $start,
			'limit' => $limit,
			'userid' => $uid
			);

		$comments = $model->getPopularComments( 'all', 'all', $options );
		$total = '';

		if( !$loadMore )
		{
			$total = $model->getTotalPopularComments( 'all', 'all', $options );
		}

		$count = count( $comments );

		$theme = Komento::getTheme();
		$theme->set( 'items', $comments );
		$theme->set( 'total', $total );
		$html = '';

		if( $loadMore )
		{
			$html = $theme->fetch( 'profile/popular/list.php' );
		}
		else
		{
			$html = $theme->fetch( 'profile/popular.php' );
		}

		$ajax->success( $html, $count, $total);
		$ajax->send();
	}

	function getStickedComments()
	{
		$loadMore = JRequest::getInt( 'loadMore', 0 );

		$ajax = Komento::getHelper( 'ajax' );
		$model = Komento::getModel( 'comments' );

		$uid = JRequest::getInt( 'uid' );
		$start = JRequest::getInt( 'start', 0 );
		$limit = JRequest::getInt( 'limit', 10 );

		$options = array(
			'limitstart' => $start,
			'limit' => $limit,
			'userid' => $uid,
			'sticked' => 1,
			'threaded' => 0,
			'sort' => 'latest'
			);

		$comments = $model->getComments( 'all', 'all', $options );
		$total = '';

		if( !$loadMore )
		{
			$total = $model->getCount( 'all', 'all', $options );
		}

		$count = count( $comments );

		$theme = Komento::getTheme();
		$theme->set( 'items', $comments );
		$theme->set( 'total', $total );
		$html = '';

		if( $loadMore )
		{
			$html = $theme->fetch( 'profile/sticked/list.php' );
		}
		else
		{
			$html = $theme->fetch( 'profile/sticked.php' );
		}

		$ajax->success( $html, $count, $total);
		$ajax->send();
	}
}
