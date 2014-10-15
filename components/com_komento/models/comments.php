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

/**
 * Content Component Article Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class KomentoModelComments extends JModel
{
	public $_total = null;
	public $_comments = null;

	// set views without depth
	// move this to hidden config?
	private $viewWithoutDepth = array('rss', 'dashboard', 'pending');

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	public function __construct()
	{
		parent::__construct();

		$mainframe	= JFactory::getApplication();
		$config		= Komento::getConfig();

		if( $mainframe->isAdmin() )
		{
			$limit		= $mainframe->getUserStateFromRequest( 'com_komento.comments.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
			$limitstart = $mainframe->getUserStateFromRequest( 'com_komento.comments.limitstart', 'limitstart', 0, 'int' );
			$this->setState('limit', $limit);
			$this->setState('limitstart', $limitstart);
		}
	}

	public function getCount( $component = 'all', $cid = 'all', $options = array() )
	{
		if( empty( $this->_total[$component][$cid] ) )
		{
			$db = JFactory::getDBO();
			// define default values
			$defaultOptions	= array(
				'sort'			=> 'default',
				'limit'			=> 0,
				'limitstart'	=> 0,
				'search'		=> '',
				'sticked'		=> 'all',
				'published'		=> 1,
				'userid'		=> '',
				'threaded'		=> 0,
				'view'			=> JRequest::getVar('view', '')
			);
			$options = Komento::mergeOptions( $defaultOptions, $options );
			$queryTotal	= $this->buildTotal( $component, $cid, $options );
			$db->setQuery( $queryTotal );
			$this->_total[$component][$cid] = $db->loadResult();
		}
		return $this->_total[$component][$cid];
	}

	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	public function getComments( $component = 'all', $cid = 'all', $options = array() )
	{
		$config		= Komento::getConfig();
		$db			= JFactory::getDBO();
		$userId		= JFactory::getUser()->id;

		// define default values
		$defaultOptions	= array(
			'sort'			=> 'default',
			'limit'			=> 0,
			'limitstart'	=> 0,
			'search'		=> '',
			'sticked'		=> 'all',
			'published'		=> 1,
			'userid'		=> '',
			'threaded'		=> $config->get( 'enable_threaded', 0 ),
			'view'			=> JRequest::getVar('view', ''),
			'nocount'		=> 0
		);

		// take the input values and clear unexisting keys
		$options = Komento::mergeOptions( $defaultOptions, $options );

		// if threaded is on, ignore sorting and alws sort by lft
		if( $options['threaded'] )
		{
			$options['sort'] = 'default';
		}

		// set total value $this->_total
		if( !$options['nocount'] && empty( $this->_total[$component][$cid] ) )
		{
			$this->getCount( $component, $cid, $options );
		}

		// the actuall data query
		$query = $this->buildQuery( $component, $cid, $options );

		$db->setQuery($query);

		$this->_comments	= $db->loadObjectList();

		if($db->getErrorNum() > 0)
		{
			JError::raiseError( $db->getErrorNum() , $db->getErrorMsg() . $db->stderr());
		}

		return $this->_comments;
	}

	private function buildQuery( $component = 'all', $cid = 'all', $options = array() )
	{
		$querySelect = $this->buildSelect( $component, $cid, $options );
		$queryWhere = $this->buildWhere( $component, $cid, $options );
		$queryGroup = $this->buildGroup( $component, $cid, $options );
		$queryOrder = $this->buildOrder( $component, $cid, $options );
		$queryLimit = $this->buildLimit( $component, $cid, $options );

		$query	= $querySelect . $queryWhere . $queryGroup . $queryOrder . $queryLimit;

		return $query;
	}

	private function buildTotal( $component = 'all', $cid = 'all', $options = array() )
	{
		$querySelect = $this->buildSelect( $component, $cid, $options );
		$queryWhere = $this->buildWhere( $component, $cid, $options );
		$queryGroup = $this->buildGroup( $component, $cid, $options );

		$query	= 'SELECT COUNT(1) FROM (' . $querySelect . $queryWhere . $queryGroup . ') AS x';

		return $query;
	}

	private function buildSelect( $component = 'all', $cid = 'all', $options = array() )
	{
		$db = JFactory::getDBO();
		$querySelect = 'SELECT x.*';

		if( $options['threaded'] == 1 && !in_array($options['view'], $this->viewWithoutDepth) && ($options['sort'] == 'default' || $options['sort'] == 'oldest') )
		{
			$querySelect .= ', count(y.id) - 1 AS depth';
		}
		else
		{
			$querySelect .= ', 0 AS depth';
		}

		$querySelect .= ' FROM ' . $db->namequote('#__komento_comments') . ' AS x';

		if( $options['threaded'] == 1 && !in_array($options['view'], $this->viewWithoutDepth) && ($options['sort'] == 'default' || $options['sort'] == 'oldest') )
		{
			$querySelect .= ' INNER JOIN ' . $db->namequote('#__komento_comments') . ' AS y';
			$querySelect .= ' ON x.component = y.component';
			$querySelect .= ' AND x.cid = y.cid';
			$querySelect .= ' AND x.lft BETWEEN y.lft and y.rgt';
		}

		return $querySelect;
	}

	private function buildWhere( $component = 'all', $cid = 'all', $options = array() )
	{
		$db = JFactory::getDBO();
		$queryWhere = array();

		// filter by component
		if($component !== 'all')
		{
			$queryWhere[] = 'x.component = ' . $db->quote($component);
		}

		// filter by content id
		if($cid !== 'all')
		{
			if( is_array( $cid ) )
			{
				$cid = implode( ',', $cid );
			}

			if( empty( $cid ) )
			{
				$queryWhere[] = 'x.cid = 0';
			}
			else
			{
				$queryWhere[] = 'x.cid IN (' . $cid . ')';
			}
		}

		// filter by published status
		// get moderation falls here
		if($options['published'] !== 'all')
		{
			$queryWhere[] = 'x.published = ' . $db->quote($options['published']);
		}

		// filter by sticked
		if($options['sticked'] !== 'all')
		{
			$queryWhere[] = 'x.sticked = ' . $db->quote($options['published']);
		}

		// filter by user id (generally for rss feed) to track user's comment
		// to track only guest comments, userid = 0
		if($options['userid'] != '')
		{
			$queryWhere[] = 'x.created_by = ' . $db->quote($options['userid']);
		}

		// text search
		if($options['search'] != '')
		{
			$queryWhere[] = 'x.comment LIKE ' . $db->quote('%' . $options['search'] . '%');
		}

		if( count($queryWhere) > 0 )
		{
			$queryWhere = ' WHERE ' . implode(' AND ', $queryWhere);
		}
		else
		{
			$queryWhere = '';
		}

		return $queryWhere;
	}

	private function buildGroup( $component = 'all', $cid = 'all', $options = array() )
	{
		$queryGroup = '';

		if( $options['threaded'] == 1 && !in_array($options['view'], $this->viewWithoutDepth) && ($options['sort'] == 'default' || $options['sort'] == 'oldest') )
		{
			$queryGroup = ' GROUP BY x.id';
		}

		return $queryGroup;
	}

	private function buildOrder( $component = 'all', $cid = 'all', $options = array() )
	{
		$config = Komento::getConfig();
		$queryOrder = '';
		switch( strtolower($options['sort']) )
		{
			case 'latest' :
				$queryOrder = ' ORDER BY x.created DESC';
				break;
			case 'oldest' :
			default :
				if( $options['threaded'] && !in_array($options['view'], $this->viewWithoutDepth) )
				{
					$queryOrder = ' ORDER BY x.lft ASC';
				}
				else
				{
					$queryOrder = ' ORDER BY x.created ASC';
				}
				break;
		}

		return $queryOrder;
	}

	private function buildLimit( $component = 'all', $cid = 'all', $options = array() )
	{
		$config = Komento::getConfig();
		$queryLimit = '';

		if($options['limit'] > 0)
		{
			$queryLimit = ' LIMIT ' . $options['limitstart'] . ',' . $options['limit'];
		}
		else
		{
			$jLimit		= JFactory::getConfig()->get('list_limit');
			$limit		= JRequest::getInt('limit', null) !== null ? JRequest::getInt('limit') : $config->get('max_comments_per_page', $jLimit);
			$limitstart = JRequest::getInt('limitstart', null) !== null ? JRequest::getInt('limitstart') : $options['limitstart'];
			$queryLimit	= ' LIMIT ' . $limitstart . ',' . $limit;
		}

		return $queryLimit;
	}

	function getData($options = array())
	{
		$mainframe	= JFactory::getApplication();
		$view		= JRequest::getVar('view');
		$db			= $this->getDBO();

		// define default values
		$defaultOptions	= array(
			'no_tree'	=> 0,
			'component' => '*',
			'published'	=> '*',
			'userid'	=> '',
			'parent_id'	=> 0,
			'no_search' => 0
		);

		// take the input values and clear unexisting keys
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$querySelect		= '';
		$queryWhere			= array();
		$queryGroup			= '';
		$queryOrder			= '';
		$queryLimit			= '';
		$queryTotal			= '';

		$filter_publish 	= $mainframe->getUserStateFromRequest( 'com_komento.' . $view . '.filter_publish', 'filter_publish', $options['published'], 'string' );
		$filter_component	= $mainframe->getUserStateFromRequest( 'com_komento.' . $view . '.filter_component', 'filter_component', $options['component'], 'string' );
		$filter_order		= $mainframe->getUserStateFromRequest( 'com_komento.' . $view . '.filter_order', 'filter_order', 'created', 'string' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_komento.' . $view . '.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_komento.' . $view . '.search', 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		if( $options['no_search'] )
		{
			$search = '';
		}

		if( $options['no_tree'] == 0 )
		{
			$querySelect  = 'SELECT x.*, COUNT(y.id) - 1 AS childs FROM ' . $db->namequote('#__komento_comments') . ' AS x';
			$querySelect .= ' INNER JOIN ' . $db->namequote('#__komento_comments') . ' AS y';
			$querySelect .= ' ON x.component = y.component';
			$querySelect .= ' AND x.cid = y.cid';
			$querySelect .= ' AND x.lft BETWEEN y.lft AND y.rgt';
		}
		else
		{
			$querySelect  = 'SELECT x.*, y.depth FROM (';
			$querySelect .= ' SELECT a.*, COUNT(a.id) - 1 AS childs FROM ' . $db->namequote('#__komento_comments') . ' AS a';
			$querySelect .= ' INNER JOIN ' . $db->namequote('#__komento_comments') . ' AS b';
			$querySelect .= ' WHERE a.component = b.component';
			$querySelect .= ' AND a.cid = b.cid';
			$querySelect .= ' AND b.lft BETWEEN a.lft AND a.rgt';
			$querySelect .= ' GROUP BY a.id) AS x';
			$querySelect .= ' LEFT JOIN (';
			$querySelect .= ' SELECT a.*, COUNT(c.id) - 1 AS depth FROM ' . $db->namequote('#__komento_comments') . ' AS a';
			$querySelect .= ' INNER JOIN ' . $db->namequote('#__komento_comments') . ' AS c';
			$querySelect .= ' WHERE a.component = c.component';
			$querySelect .= ' AND a.cid = c.cid';
			$querySelect .= ' AND a.lft BETWEEN c.lft AND c.rgt';
			$querySelect .= ' GROUP BY a.id) AS y ON x.id = y.id';
		}

		// filter by component
		if( $filter_component != '*' )
		{
			$queryWhere[] = 'x.component = ' . $db->quote($filter_component);
		}

		// filter by publish state
		if( $filter_publish != '*' )
		{
			$queryWhere[] = 'x.published = ' . $db->quote($filter_publish);
		}

		/*
		// filter by user
		// not implemented yet
		$filter_user		= $mainframe->getUserStateFromRequest( 'com_komento.comments.filter_user', 'filter_user', '*', 'string' );
		if($filter_user != '*')
		{
			$queryWhereForA[] = 'a.created_by = ' . $db->quote($filter_user);
		}
		*/

		if( $search )
		{
			$queryWhere[] = 'LOWER( x.comment ) LIKE \'%' . $search . '%\' ';
		}
		else
		{
			if( $options['no_tree'] == 0 )
			{
				$queryWhere[] = 'x.parent_id = ' . $db->quote($options['parent_id']);
			}
		}

		if(count($queryWhere) > 0)
		{
			$queryWhere  = ' WHERE ' . implode(' AND ', $queryWhere);
		}
		else
		{
			$queryWhere = '';
		}

		if( $options['no_tree'] == 0 )
		{
			$queryGroup = ' GROUP BY x.id';
		}

		$queryOrder = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir;

		if( $options['parent_id'] == 0 )
		{
			if( $this->getState('limit') != 0 )
			{
				$queryLimit = ' LIMIT ' . $this->getState('limitstart') . ',' . $this->getState('limit');
			}
		}

		$queryTotal = 'SELECT COUNT(1) FROM (' . $querySelect . $queryWhere . $queryGroup . ') AS x';

		// set pagination
		$db->setQuery( $queryTotal );
		$this->_total = $db->loadResult();

		jimport('joomla.html.pagination');
		$this->_pagination	= new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );

		// actual query
		$query = $querySelect . $queryWhere . $queryGroup . $queryOrder . $queryLimit;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if($db->getErrorNum() > 0)
		{
			JError::raiseError( $db->getErrorNum() , $db->getErrorMsg() . $db->stderr());
		}

		return $result;

		/*// Lets load the content if it doesn't already exist
		if(empty($this->_data))
		{
			$db->setQuery($query);
			$result = $db->loadObjectList();

			$result	= $db->loadObjectList();

			if($db->getErrorNum() > 0)
			{
				JError::raiseError( $db->getErrorNum() , $db->getErrorMsg() . $db->stderr());
			}
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;*/
	}

	function publish( $comments = array(), $publish = 1 )
	{
		if( $comments == null )
		{
			return false;
		}

		if( !is_array($comments) )
		{
			$comments = array($comments);
		}

		$affectChild = JRequest::getInt('affectchild', 0);

		if( count( $comments ) > 0 )
		{
			$now = JFactory::getDate()->toMySql();

			$publishDateColumn = '';

			if($publish == 0)
			{
				$publishDateColumn = 'publish_down';
			}
			else
			{
				$publishDateColumn = 'publish_up';
			}

			$nodes = $comments;

			foreach($nodes as $comment)
			{
				$related = array();

				if($publish == 1)
				{
					$related = array_merge($related, self::getParents($comment));
				}

				if($publish == 0 || ($publish == 1 && $affectChild))
				{
					$related = array_merge($related, self::getChilds($comment));
				}

				if(count($related) > 0)
				{
					$comments = array_merge($comments, $related);
				}
			}

			$comments		= array_unique($comments);
			$allComments	= implode( ',' , $comments );

			foreach( $comments as $comment )
			{
				if( !Komento::getComment( $comment )->publish( $publish ) )
				{
					return false;
				}
			}

			return true;
		}
		return false;
	}

	function unpublish($comments = array(), $publish = 0)
	{
		return self::publish($comments, $publish);
	}

	function remove($comments = array())
	{
		if( $comments == null )
		{
			return false;
		}

		if( !is_array($comments) )
		{
			$comments = array($comments);
		}

		$affectChild = JRequest::getInt('affectchild', 0);

		if( count( $comments ) > 0 )
		{
			$node = $comments;

			foreach($node as $comment)
			{
				if($affectChild)
				{
					$childs = self::getChilds($comment);
					if(count($childs) > 0)
					{
						$comments = array_merge($comments, $childs);
					}
				}
				else
				{
					self::moveChildsUp($comment);
				}
			}

			$comments		= array_unique($comments);

			foreach($comments as $comment)
			{
				$obj = Komento::getComment($comment);
				$obj->delete();
			}

			return true;
		}
		return false;
	}

	function stick($comments = array(), $stick = 1)
	{
		if( !is_array($comments) )
		{
			$comments = array($comments);
		}

		if( count( $comments) > 0 )
		{
			$allComments = implode( ',', $comments );

			$db = $this->getDBO();
			$query  = 'UPDATE ' . $db->namequote( '#__komento_comments' );
			$query .= ' SET ' . $db->namequote( 'sticked' ) . ' = ' . $db->quote( $stick );
			$query .= ' WHERE ' . $db->namequote( 'id' ) . ' IN (' . $allComments . ')';

			$db->setQuery( $query );

			if( !$db->query() )
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			foreach( $comments as $comment )
			{
				// process all activities
				if( $stick )
				{
					$activity = Komento::getHelper( 'activity' )->process( 'stick', $comment );
				}
				else
				{
					$activity = Komento::getHelper( 'activity' )->process( 'unstick', $comment );
				}
			}

			return true;
		}
		return false;
	}

	function unstick($comments = array())
	{
		return self::stick( $comments, 0 );
	}

	function flag($comments = array(), $flag)
	{
		$affectChild = JRequest::getInt('affectchild', 0);

		if( count( $comments ) > 0 )
		{
			$user = JFactory::getUser()->id;
			$db = $this->getDBO();

			if($affectChild)
			{
				$node = $comments;

				foreach($node as $comment)
				{
					$childs = self::getChilds($comment);
					if(count($childs) > 0)
					{
						$comments = array_merge($comments, $childs);
					}
				}
			}

			$comments		= array_unique($comments);
			$allComments	= implode( ',', $comments);

			$query  = 'UPDATE ' . $db->namequote( '#__komento_comments' );
			$query .= ' SET ' . $db->namequote( 'flag' ) . ' = ' . $db->quote($flag);
			$query .= ', ' . $db->namequote('flag_by') . ' = ' .$db->quote($user);
			$query .= ' WHERE ' . $db->namequote( 'id' ) . ' IN (' . $allComments . ')';

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

	function getTotalComment( $userId = 0 )
	{
		$db		= $this->getDBO();
		$config	= Komento::getConfig();

		$where  = array();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__komento_comments' );

		if(! empty($userId))
			$where[]  = '`created_by` = ' . $db->Quote($userId);

		$extra 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$query      = $query . $extra;

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	function getTotalReplies( $userId = 0 )
	{
		$db		= $this->getDBO();
		$config	= Komento::getConfig();

		$where  = array();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__komento_comments' );

		$where[] = '`parent_id` <> 0';

		if(! empty($userId))
			$where[]  = '`created_by` = ' . $db->Quote($userId);

		$extra 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$query      = $query . $extra;

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	function getUniqueComponents()
	{
		$db = JFactory::getDBO();
		$query = 'SELECT DISTINCT ' . $db->namequote('component') . ' FROM ' . $db->namequote( '#__komento_comments' ) . ' ORDER BY ' . $db->namequote('component');
		$db->setQuery($query);
		$components = $db->loadResultArray();

		return $components;
	}

	function getLatestComment($component, $cid, $parentId = 0)
	{
		$db	= JFactory::getDBO();

		$query  = 'SELECT `id`, `lft`, `rgt` FROM `#__komento_comments`';
		$query .= ' WHERE `component` = ' . $db->Quote($component);
		$query .= ' AND `cid` = ' . $db->Quote($cid);
		$query .= ' AND `parent_id` = ' . $db->Quote($parentId);
		$query .= ' ORDER BY `lft` DESC LIMIT 1';

		$db->setQuery($query);
		$result	= $db->loadObject();

		return $result;
	}

	function getCommentDepth($id)
	{
		$comment = Komento::getComment( $id );
		$component = $comment->component;
		$cid = $comment->cid;

		$db = JFactory::getDBO();

		$query  = 'SELECT COUNT(`parent`.`id`)-1 AS `depth`';
		$query .= ' FROM `#__komento_comments` AS `node`';
		$query .= ' INNER JOIN `#__komento_comments` AS `parent` on parent.component = node.component and node.cid = parent.cid';
		$query .= ' WHERE `node`.`component` = ' . $db->Quote($component);
		$query .= ' AND `node`.`cid` = ' . $db->Quote($cid);
		$query .= ' AND `node`.`id` = ' . $db->Quote($id);
		$query .= ' AND `node`.`lft` BETWEEN `parent`.`lft` AND `parent`.`rgt`';
		$query .= ' GROUP BY `node`.`id`';

		$db->setQuery($query);
		$result = $db->loadObject();

		return $result->depth;
	}

	function updateCommentSibling($component, $cid, $nodeValue)
	{
		$db	= JFactory::getDBO();

		$query  = 'UPDATE `#__komento_comments` SET `rgt` = `rgt` + 2';
		$query .= ' WHERE `component` = ' . $db->Quote($component);
		$query .= ' AND `cid` = ' . $db->Quote($cid);
		$query .= ' AND `rgt` > ' . $db->Quote($nodeValue);
		$db->setQuery($query);
		$db->query();

		$query  = 'UPDATE `#__komento_comments` SET `lft` = `lft` + 2';
		$query .= ' WHERE `component` = ' . $db->Quote($component);
		$query .= ' AND `cid` = ' . $db->Quote($cid);
		$query .= ' AND `lft` > ' . $db->Quote($nodeValue);
		$db->setQuery($query);
		$db->query();
	}

	function getChilds($id)
	{
		$commentTable = Komento::getTable('comments');
		$commentTable->load($id);

		$component	= $commentTable->component;
		$cid		= $commentTable->cid;
		$lft		= $commentTable->lft;
		$rgt		= $commentTable->rgt;


		$db = JFactory::getDBO();
		$query = 'SELECT ' . $db->namequote('id') . ' FROM ' . $db->namequote( '#__komento_comments' );
		$query .= ' WHERE ' . $db->namequote('component') . ' = ' . $db->quote($component);
		$query .= ' AND ' . $db->namequote('cid') . ' = ' . $db->quote($cid);
		$query .= ' AND ' . $db->namequote('lft') . ' BETWEEN ' . $db->quote($lft) . ' AND ' . $db->quote($rgt);
		$db->setQuery($query);

		return $db->loadResultArray();
	}

	function getParents($id)
	{
		$commentTable = Komento::getTable('comments');
		$commentTable->load($id);

		$component	= $commentTable->component;
		$cid		= $commentTable->cid;
		$lft		= $commentTable->lft;

		$db = JFactory::getDBO();
		$query = 'SELECT ' . $db->namequote('id') . ' FROM ' . $db->namequote( '#__komento_comments' );
		$query .= ' WHERE ' . $db->namequote('component') . ' = ' . $db->quote($component);
		$query .= ' AND ' . $db->namequote('cid') . ' = ' . $db->quote($cid);
		$query .= ' AND ' . $db->quote($lft) . ' BETWEEN ' . $db->namequote('lft') . ' AND ' . $db->namequote('rgt');

		$db->setQuery($query);

		return $db->loadResultArray();
	}

	function getTotalChilds($id)
	{
		// CANNOT RELY ON JUST RGT-LFT
		$commentTable = Komento::getTable('comments');
		$commentTable->load($id);

		$component	= $commentTable->component;
		$cid		= $commentTable->cid;
		$lft		= $commentTable->lft;
		$rgt		= $commentTable->rgt;

		$db = JFactory::getDBO();
		$query = 'SELECT COUNT(1) FROM ' . $db->namequote( '#__komento_comments' );
		$query .= ' WHERE ' . $db->namequote('component') . ' = ' . $db->quote($component);
		$query .= ' AND ' . $db->namequote('cid') . ' = ' . $db->quote($cid);
		$query .= ' AND ' . $db->namequote('lft') . ' BETWEEN ' . $db->quote($lft) . ' AND ' . $db->quote($rgt);
		$query .= ' AND ' . $db->namequote('lft') . ' != ' .$db->quote($lft);
		$db->setQuery($query);

		return $db->loadResult();
	}

	function moveChildsUp($id)
	{
		$commentTable = Komento::getTable('comments');
		$commentTable->load($id);

		$db = JFactory::getDBO();
		$query = 'UPDATE ' . $db->namequote( '#__komento_comments' );
		$query .= ' SET ' . $db->namequote('parent_id') . ' = ' . $db->quote($commentTable->parent_id);
		$query .= ' WHERE ' . $db->namequote('parent_id') . ' = ' . $db->quote($commentTable->id);

		$db->setQuery($query);

		if( !$db->query() )
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	function isSticked($id)
	{
		$commentTable = Komento::getTable('comments');
		$commentTable->load($id);
		return $commentTable->sticked;
	}

	function getConversationBarAuthors($component, $cid)
	{
		$config = Komento::getConfig();

		$db = JFactory::getDBO();

		$limit = ' LIMIT ' . $config->get( 'conversation_bar_max_authors', 10 );
		$order = ' ORDER BY ' . $db->namequote( 'created' ) . ' DESC';

		$main  = 'SELECT `name`, `created_by`, `created` FROM ' . $db->namequote( '#__komento_comments' );
		$main .= ' WHERE ' . $db->namequote( 'component' ) . ' = ' . $db->quote( $component );
		$main .= ' AND ' . $db->namequote( 'cid' ) . ' = ' . $db->quote( $cid );

		$query  = $main . ' AND ' . $db->namequote( 'created_by' ) . ' <> ' . $db->quote( '0' ) . ' AND ' . $db->namequote( 'created_by' ) . ' <> ' . $db->quote( '' );
		$query .= ' GROUP BY ' . $db->namequote( 'created_by' ) . $order . $limit;

		if( $config->get( 'conversation_bar_include_guest' ) )
		{
			$temp  = $main . ' AND ' . $db->namequote( 'created_by' ) . ' = ' . $db->quote( '0' );
			$temp .= ' GROUP BY ' . $db->namequote( 'name' ) . $order . $limit;

			$query = '(' . $query . ') UNION (' . $temp . ')';
		}

		$query = 'SELECT `name`, `created_by` FROM (' . $query . ') AS x' . $order . $limit;
		$db->setQuery( $query );
		$result = $db->loadObjectList();

		$authors = new stdClass();
		$authors->guest = array();
		$authors->registered = array();

		foreach( $result as $item )
		{
			if($item->created_by == '0')
			{
				$authors->guest[] = $item->name;
			}
			else
			{
				$authors->registered[] = $item->created_by;
			}
		}

		return $authors;
	}

	function getPopularComments( $component = 'all', $cid = 'all', $options = array() )
	{
		$db = JFactory::getDBO();

		// define default values
		$defaultOptions	= array(
			'start'		=> 0,
			'limit'		=> 10,
			'userid'	=> 'all',
			'sticked'	=> 'all',
			// 'search'	=> '', future todo
			'published'	=> 1
		);

		$querySelect = '';
		$queryWhere = array();
		$queryGroup = '';
		$queryOrder = '';
		$queryLimit = '';

		// take the input values and clear unexisting keys
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$querySelect  = 'SELECT comments.*, COUNT(actions.comment_id) AS likes, 0 AS depth FROM ' . $db->nameQuote( '#__komento_comments' ) . ' AS comments';
		$querySelect .= ' LEFT JOIN ' . $db->nameQuote( '#__komento_actions' ) . ' AS actions ON comments.id = actions.comment_id';

		if( $component !== 'all' )
		{
			$queryWhere[] = 'comments.component = ' . $db->quote( $component );
		}

		if( $cid !== 'all' )
		{
			if( is_array( $cid ) )
			{
				$cid = implode( ',', $cid );
			}

			if( empty( $cid ) )
			{
				$queryWhere[] = 'comments.cid = 0';
			}
			else
			{
				$queryWhere[] = 'comments.cid IN (' . $cid . ')';
			}
		}

		if( $options['userid'] !== 'all' )
		{
			$queryWhere[] = 'comments.created_by = ' . $db->quote( $options['userid'] );
		}

		if( $options['published'] !== 'all' )
		{
			$queryWhere[] = 'comments.published = ' . $db->quote( $options['published'] );
		}

		if( $options['sticked'] !== 'all' )
		{
			$queryWhere[] = 'comments.sticked = ' . $db->quote( $options['published'] );
		}

		$queryWhere[] = 'actions.type = ' . $db->quote( 'likes' );

		if( count($queryWhere) > 0 )
		{
			$queryWhere = ' WHERE ' . implode(' AND ', $queryWhere);
		}
		else
		{
			$queryWhere = '';
		}

		$queryGroup = ' GROUP BY actions.comment_id';
		$queryOrder = ' ORDER BY likes DESC, created DESC';
		$queryLimit = ' LIMIT ' . $options['start'] . ',' . $options['limit'];

		$query = $querySelect . $queryWhere . $queryGroup . $queryOrder . $queryLimit;
		$db->setQuery( $query );

		return $db->loadObjectList();
	}

	function getTotalPopularComments( $component = 'all', $cid = 'all', $options = array() )
	{
		$db = JFactory::getDBO();

		// define default values
		$defaultOptions	= array(
			'start'		=> 0,
			'limit'		=> 10,
			'userid'	=> 'all',
			'sticked'	=> 'all',
			// 'search'	=> '', future todo
			'published'	=> 1
		);

		$querySelect = '';
		$queryWhere = array();
		$queryGroup = '';
		$queryOrder = '';
		$queryLimit = '';

		// take the input values and clear unexisting keys
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$querySelect  = 'SELECT comments.*, COUNT(actions.comment_id) AS likes FROM ' . $db->nameQuote( '#__komento_comments' ) . ' AS comments';
		$querySelect .= ' LEFT JOIN ' . $db->nameQuote( '#__komento_actions' ) . ' AS actions ON comments.id = actions.comment_id';

		if( $component !== 'all' )
		{
			$queryWhere[] = 'comments.component = ' . $db->quote( $component );
		}

		if( $cid !== 'all' )
		{
			if( is_array( $cid ) )
			{
				$cid = implode( ',', $cid );
			}

			if( empty( $cid ) )
			{
				$queryWhere[] = 'comments.cid = 0';
			}
			else
			{
				$queryWhere[] = 'comments.cid IN (' . $cid . ')';
			}
		}

		if( $options['userid'] !== 'all' )
		{
			$queryWhere[] = 'comments.created_by = ' . $db->quote( $options['userid'] );
		}

		if( $options['published'] !== 'all' )
		{
			$queryWhere[] = 'comments.published = ' . $db->quote( $options['published'] );
		}

		if( $options['sticked'] !== 'all' )
		{
			$queryWhere[] = 'comments.sticked = ' . $db->quote( $options['published'] );
		}

		$queryWhere[] = 'actions.type = ' . $db->quote( 'likes' );

		if( count($queryWhere) > 0 )
		{
			$queryWhere = ' WHERE ' . implode(' AND ', $queryWhere);
		}
		else
		{
			$queryWhere = '';
		}

		$queryGroup = ' GROUP BY actions.comment_id';

		$query = 'SELECT COUNT(1) FROM (' . $querySelect . $queryWhere . $queryGroup . ') AS x';
		$db->setQuery( $query );

		return $db->loadResult();
	}

	public function deleteArticleComments( $component, $cid )
	{
		$db = JFactory::getDBO();

		$query  = 'DELETE FROM ' . $db->nameQuote( '#__komento_comments' );
		$query .= ' WHERE ' . $db->nameQuote( 'component' ) . ' = ' . $component;
		$query .= ' AND ' . $db->nameQuote( 'cid' ) . ' = ' . $cid;

		$db->setQuery( $query );
		return $db->query();
	}
}
