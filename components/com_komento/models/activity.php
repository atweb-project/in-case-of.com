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

jimport('joomla.application.component.model');

class KomentoModelActivity extends JModel
{
	public $_total = null;

	public function add( $type, $comment_id, $uid )
	{
		$db		= JFactory::getDbo();
		$now	= JFactory::getDate()->toMySQL();
		$query	= 'INSERT INTO ' . $db->nameQuote( '#__komento_activities' )
				. ' ( `type`, `comment_id`, `uid`,  `created`, `published` ) '
				. ' VALUES ( '
				. $db->quote( $type ) . ', '
				. $db->quote( $comment_id ) . ', '
				. $db->quote( $uid ) . ', '
				. $db->quote( $now ) . ', '
				. $db->quote( 1 ) . ')';
		$db->setQuery( $query );

		return $db->query();
	}

	public function delete( $comment_id )
	{
		$db = JFactory::getDBO();
		$query = 'DELETE FROM `#__komento_activities` WHERE `comment_id` = ' . $db->quote( $comment_id );
		$db->setQuery( $query );

		return $db->query();
	}

	public function getUserActivities( $id, $options = array() )
	{
		// comments, likes, recommends, articles, forum post, feature... need a hook to get 3rd party content what say you?
		$db		= $this->_db;

		// define default values
		$defaultOptions	= array(
			'type'		=> 'like,comment,reply',
			'sort'		=> 'latest',
			'start'		=> 0,
			'limit'		=> 10,
			// 'search'	=> '', future todo
			'published'	=> 1,
			'component'	=> 'all',
			'cid'		=> 'all'
		);

		// take the input values and clear unexisting keys
		$options	= array_merge($defaultOptions, $options);

		$query = $this->buildQuery( $id, $options );
		$db->setQuery( $query );

		return $db->loadObjectList();
	}

	public function getTotalUserActivities( $id, $options = array() )
	{
		if (empty($this->_total))
		{
			$db		= $this->_db;

			// define default values
			$defaultOptions	= array(
				'type'		=> 'like,comment,reply',
				'published'	=> 1,
				'component'	=> 'all',
				'cid'		=> 'all'
			);

			$options	= array_merge($defaultOptions, $options);

			$query = $this->buildQueryTotal( $id, $options );
			$db->setQuery( $query );
			$this->_total = $db->loadResult();
		}

		return $this->_total;
	}

	private function buildQuery( $id, $options )
	{
		$querySelect = $this->buildSelect( $id, $options );
		$queryWhere = $this->buildWhere( $id, $options );
		$queryOrder = $this->buildOrder( $id, $options );
		$queryLimit = $this->buildLimit( $id, $options );

		return $querySelect . $queryWhere . $queryOrder . $queryLimit;
	}

	private function buildQueryTotal( $id, $options )
	{
		$querySelect = $this->buildSelect( $id, $options );
		$queryWhere = $this->buildWhere( $id, $options );

		return 'SELECT COUNT(1) FROM (' . $querySelect . $queryWhere . ') as X';
	}

	private function buildSelect( $id, $options )
	{
		$db		= $this->_db;
		$query  = 'SELECT activities.*, comments.component, comments.cid, comments.comment, comments.name, comments.created_by, comments.parent_id FROM ' . $db->nameQuote( '#__komento_activities' ) . ' AS activities';
		$query .= ' LEFT JOIN ' . $db->nameQuote( '#__komento_comments' ) . ' AS comments ON activities.comment_id = comments.id';
		return $query;
	}

	private function buildWhere( $id, $options )
	{
		$db		= $this->_db;
		$query = array();

		if( $id !== 'all' )
		{
			$query[] = 'activities.uid = ' . $db->quote( $id );
		}

		$query[] = 'activities.published = ' . $db->quote( $options['published'] );

		$query[] = 'comments.published = 1';

		if( $options['component'] !== 'all' )
		{
			$query[] = 'comments.component = ' . $db->quote( $options['component'] );
		}
		else
		{
			$query[] = 'comments.component IS NOT null';
		}

		if( $options['cid'] !== 'all' )
		{
			if( is_array( $options['cid'] ) )
			{
				$options['cid'] = implode( ',', $options['cid'] );
			}

			if( !empty( $cid ) )
			{
				$query[] = 'comments.cid = 0';
			}
			else
			{
				$query[] = 'comments.cid IN (' . $options['cid'] . ')';
			}
		}
		else
		{
			$query[] = 'comments.cid IS NOT null';
		}

		if( $options['type'] !== 'all' )
		{
			$tmp = $options['type'];

			if( !is_array( $options['type'] ) )
			{
				$tmp = explode( ',', $options['type'] );
			}

			foreach( $tmp as &$t )
			{
				$t = $db->quote( $t );
			}

			$tmp = implode( ',', $tmp );

			$query[] = 'activities.type IN (' . $tmp . ')';
		}

		$query = ' WHERE ' . implode(' AND ', $query);
		return $query;
	}

	private function buildOrder( $id, $options )
	{
		$db		= $this->_db;
		$query = '';
		switch( $options['sort'] )
		{
			case 'oldest':
				$query = ' ORDER BY activities.created ASC';
				break;
			case 'latest':
			default:
				$query = ' ORDER BY activities.created DESC';
				break;
		}
		return $query;
	}

	private function buildLimit( $id, $options )
	{
		$db		= $this->_db;
		$query = ' LIMIT ' . $options['start'] . ',' . $options['limit'];
		return $query;
	}
}
