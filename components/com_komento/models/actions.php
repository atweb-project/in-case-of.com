<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class KomentoModelActions extends JModel
{
	var $_total = null;
	var $_pagination = null;
	var $_data = null;
	var $flags = array('spam', 'offensive', 'offtopic');

	function __construct()
	{
		parent::__construct();

		$mainframe	= JFactory::getApplication();

		$limit		= $mainframe->getUserStateFromRequest( 'com_komento.reports.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( 'com_komento.reports.limitstart', 'limitstart', 0, 'int' );
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getData()
	{
		// Lets load the content ifit doesn't already exist
		if(empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	function getPagination()
	{
		// Lets load the content ifit doesn't already exist
		if(empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	function getTotal()
	{
		// Lets load the content ifit doesn't already exist
		if(empty($this->_total))
		{
			$query = 'SELECT COUNT(1) FROM (' . $this->_buildQuery() . ') as x';

			$this->_db->setQuery($query);
			$this->_total = $this->_db->loadResult();
		}

		return $this->_total;
	}

	function _buildQuery()
	{
		$mainframe	= JFactory::getApplication();
		$db			= $this->getDBO();

		$filter_publish 	= $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_publish', 'filter_publish', '*', 'string' );
		$filter_component	= $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_component', 'filter_component', '*', 'string' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_komento.reports.search', 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_order', 'filter_order', 'created', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word' );

		$querySelect  = 'SELECT a.*';
		$querySelect .= ', IFNULL(actions.reports, 0) AS reports';
		$querySelect .= ' FROM ' . $db->namequote('#__komento_comments') . ' AS a';

		$querySelect .= ' LEFT JOIN (';
		$querySelect .= ' SELECT comment_id as actionid,';
		$querySelect .= ' SUM(type = ' . $db->quote('report') . ') as reports';
		$querySelect .= ' FROM `#__komento_actions` ';
		$querySelect .= ' GROUP BY comment_id)';
		$querySelect .= ' AS actions on a.id = actions.actionid';

		$queryWhere[] = 'reports > 0';

		// filter by component
		if($filter_component != '*')
		{
			$queryWhere[] = 'component = ' . $db->quote($filter_component);
		}

		// filter by publish state
		if($filter_publish != '*')
		{
			$queryWhere[] = 'published = ' . $db->quote($filter_publish);
		}

		if($search)
		{
			$queryWhere[] = 'LOWER( comment ) LIKE \'%' . $search . '%\' ';
		}

		if(count($queryWhere) > 0)
		{
			$queryWhere = ' WHERE ' . implode(' AND ', $queryWhere);
		}

		$queryOrder			= ' GROUP BY a.id ORDER BY '.$filter_order.' '.$filter_order_Dir;

		$query = $querySelect . $queryWhere . $queryOrder;

		return $query;
	}

	function clearReports($comments)
	{
		$db = $this->getDBO();

		$allComments = implode( ',', $comments);
		$query  = 'DELETE FROM ' . $db->namequote( '#__komento_actions' );
		$query .= ' WHERE ' . $db->namequote( 'comment_id' ) . ' IN (' . $allComments . ')';
		$query .= ' AND ' . $db->namequote( 'type' ) . ' = ' . $db->quote( 'report' );

		$db->setQuery( $query );

		if( !$db->query() )
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	function addAction($type, $comment_id, $user_id)
	{
		$comment	= Komento::getComment( $comment_id );

		$now			= JFactory::getDate()->toMySQL();

		$actionsTable	= Komento::getTable( 'actions' );

		$actionsTable->type			= $type;
		$actionsTable->comment_id	= $comment_id;
		$actionsTable->action_by	= $user_id;
		$actionsTable->actioned		= $now;

		if(!$actionsTable->store())
		{
			return false;
			// return JText::_( 'COM_KOMENTO_LIKES_ERROR_SAVING_LIKES' );
		}

		return $actionsTable->id;
	}

	function removeAction($type = 'all', $comment_id, $user_id = 'all')
	{
		$db = JFactory::getDBO();
		$where = array();
		$query  = 'DELETE FROM `#__komento_actions`';

		if($type != 'all')
		{
			$where[] = '`type` = ' . $db->quote($type);
		}

		if($comment_id)
		{
			$where[] = '`comment_id` = ' . $db->quote($comment_id);
		}

		if($user_id != 'all')
		{
			$where[] = '`action_by` = ' . $db->quote($user_id);
		}

		if(count($where))
		{
			$query .= ' WHERE ' . implode(' AND ', $where);
		}

		$db->setQuery($query);
		$db->query();

		return $db->query();
	}

	function countAction($type, $comment_id, $user_id = 0)
	{
		$db = JFactory::getDBO();

		$where = array();
		$query  = 'SELECT COUNT(1) FROM `#__komento_actions`';

		if($type)
		{
			$where[] = '`type` = ' . $db->quote($type);
		}

		if($comment_id)
		{
			$where[] = '`comment_id` = ' . $db->quote($comment_id);
		}

		if($user_id)
		{
			$where[] = '`action_by` = ' . $db->quote($user_id);
		}

		if(count($where))
		{
			$query .= ' WHERE ' . implode(' AND ', $where);
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	function liked($commentId, $userId)
	{
		if( $userId == 0 )
		{
			return 0;
		}

		return $this->countAction('likes', $commentId, $userId);
	}

	function reported($commentId, $userId)
	{
		if( $userId == 0 )
		{
			return 0;
		}

		return $this->countAction('report', $commentId, $userId);
	}

	function unlikeComment($commentId, $userId)
	{
		if( $userId == 0 )
		{
			return 0;
		}

		return $this->removeAction('likes', $commentId, $userId);
	}

	function getLikesReceived( $userId, $type = 'likes' )
	{
		$db = JFactory::getDBO();

		$query  = 'SELECT COUNT(1) FROM ' . $db->namequote( '#__komento_actions' );
		$query .= ' WHERE ' . $db->namequote( 'comment_id' ) . ' IN (';
		$query .= ' SELECT ' . $db->namequote( 'id' ) . ' FROM ' . $db->namequote( '#__komento_comments' );
		$query .= ' WHERE ' . $db->namequote( 'created_by' ) . ' = ' . $db->quote( $userId );
		$query .= ' ) AND ' . $db->namequote( 'type' ) . ' = ' . $db->quote( $type );

		$db->setQuery( $query );
		return $db->loadResult();
	}

	function getLikesGiven( $userId, $type = 'likes' )
	{
		$db = JFactory::getDBO();

		$query  = 'SELECT COUNT(1) FROM ' . $db->namequote( '#__komento_actions' );
		$query .= ' WHERE ' . $db->namequote( 'action_by' ) . ' = ' . $db->quote( $userId );
		$query .= ' AND ' . $db->namequote( 'type' ) . ' = ' . $db->quote( $type );

		$db->setQuery( $query );
		return $db->loadResult();
	}

	function getLikedUsers( $id )
	{
		$db = JFactory::getDBO();

		$query  = 'SELECT * FROM ' . $db->nameQuote( '#__komento_actions' );
		$query .= ' WHERE ' . $db->nameQuote( 'comment_id' ) . ' = ' . $db->quote( $id );
		$query .= ' AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->quote( 'likes' );

		$db->setQuery( $query );
		$result = $db->loadObjectList();

		foreach( $result as &$row )
		{
			$row->author = Komento::getProfile( $row->action_by );
		}

		return $result;
	}
}
