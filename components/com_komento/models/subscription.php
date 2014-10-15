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

class KomentoModelSubscription extends JModel
{
	var $_total = null;
	var $_pagination = null;
	var $_data = null;

	function __construct()
	{
		parent::__construct();

		$mainframe	= JFactory::getApplication();

		$limit		= $mainframe->getUserStateFromRequest( 'com_komento.subscribers.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( 'com_komento.subscribers.limitstart', 'limitstart', 0, 'int' );
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function checkSubscriptionExist($component, $cid, $userid = 0, $email = '', $type = 'comment')
	{
		$db		= $this->getDBO();

		$query	= 'SELECT `published` FROM `#__komento_subscription`';
		$query	.= ' WHERE `component` = ' . $db->Quote($component);
		$query	.= ' AND `cid` = ' . $db->Quote($cid);

		if($userid)
		{
			$query	.= ' AND `userid` = ' . $db->Quote($userid);
		}
		else
		{
			$query	.= ' AND `email` = ' . $db->Quote($email);
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		// $result = null ( no subscription )
		// $result = 0 ( subscribed but not confirmed )
		// $result = 1 ( subscribed and confirmed )
		return $result;
	}

	function getSubscribers($component, $cid)
	{
		$db		= $this->getDBO();
		$query	= 'SELECT `fullname`, `email` FROM `#__komento_subscription`';
		$query	.= ' WHERE `component` = ' . $db->Quote($component);
		$query	.= ' AND `cid` = ' . $db->Quote($cid);
		$query	.= ' AND `published` = 1';

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	function unsubscribe($component, $cid, $userid, $email= '', $type = 'comment')
	{
		$db		= $this->getDBO();
		$query	= 'DELETE FROM `#__komento_subscription`';
		$query	.= ' WHERE `component` = ' . $db->quote( $component );
		$query	.= ' AND `cid` = ' . $db->quote( $cid );
		$query	.= ' AND `type` = ' . $db->quote( $type );

		if( $userid )
		{
			$query	.= ' AND `userid` = ' . $db->quote( $userid );
		}
		else
		{
			$query	.= ' AND `email` = ' . $db->quote( $email );
		}

		$db->setQuery( $query );

		if( !$db->query() )
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	function confirmSubscription( $id )
	{
		if( !$id )
		{
			return false;
		}

		$subscriptionTable = Komento::getTable( 'subscription' );
		$subscriptionTable->load( $id );
		$subscriptionTable->published = 1;
		return $subscriptionTable->store();
	}

	function remove( $subscribers = array() )
	{
		if( $subscribers == null )
		{
			return false;
		}

		if( !is_array( $subscribers ) )
		{
			$subscribers = array($subscribers);
		}

		if( count( $subscribers ) > 0 )
		{
			$all = implode( ',' , $subscribers );

			$db = $this->getDBO();
			$query  = 'DELETE FROM ' . $db->nameQuote( '#__komento_subscription' );
			$query .= ' WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $all . ')';
			$db->setQuery( $query );

			if( !$db->query() )
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			return true;
		}

		return false;
	}

	function getUniqueComponents()
	{
		$db = JFactory::getDBO();
		$query = 'SELECT DISTINCT ' . $db->namequote( 'component' ) . ' FROM ' . $db->namequote( '#__komento_subscription' ) . ' ORDER BY ' . $db->namequote( 'component' );
		$db->setQuery($query);
		$components = $db->loadResultArray();

		return $components;
	}

	function getData()
	{
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

		$filter_component	= $mainframe->getUserStateFromRequest( 'com_komento.subscribers.filter_component', 'filter_component', '*', 'string' );
		$filter_type		= $mainframe->getUserStateFromRequest( 'com_komento.subscribers.filter_type', 'filter_type', '*', 'string' );
		$filter_order		= $mainframe->getUserStateFromRequest( 'com_komento.subscribers.filter_order', 'filter_order', 'created', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_komento.subscribers.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word' );

		$querySelect = '';
		$queryWhere = array();
		$queryOrder = '';
		$queryLimit = '';
		$queryTotal = '';

		$querySelect  = 'SELECT * FROM ' . $db->namequote( '#__komento_subscription' );

		// filter by component
		if( $filter_component != '*' )
		{
			$queryWhere[] = 'component = ' . $db->quote( $filter_component );
		}

		if( $filter_type != '*' )
		{
			$queryWhere[] = 'type = ' . $db->quote( $filter_type );
		}

		if(count($queryWhere) > 0)
		{
			$queryWhere = ' WHERE ' . implode(' AND ', $queryWhere);
		}

		$queryOrder			= ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir;

		$query = $querySelect . $queryWhere . $queryOrder;

		return $query;
	}
}
