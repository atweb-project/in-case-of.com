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

class KomentoModelAcl extends JModel
{
	/**
	 * Total count
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Data array
	 *
	 * @var array
	 */
	var $_data = null;

	public function __construct()
	{
		parent::__construct();

		$mainframe	= JFactory::getApplication();

		$limit		= $mainframe->getUserStateFromRequest( 'com_komento.acls.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the total count
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal($type)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery($type);

			$query = 'SELECT COUNT(1) FROM (' . $query . ') as x';
			$this->_db->setQuery($query);

			$this->_total = $this->_db->loadResult();
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination($type)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal($type), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	public function getRules($key='')
	{
		$sql = 'SELECT * FROM '.$this->_db->nameQuote('#__komento_acl').' WHERE `published`=1 ORDER BY `id` ASC';
		$this->_db->setQuery($sql);

		return $this->_db->loadObjectList($key);
	}

	public function deleteRuleset($cid, $type)
	{
		if(empty($cid) || empty($type))
		{
			return false;
		}

		$sql = 'DELETE FROM ' . $this->_db->nameQuote('#__komento_acl') . ' WHERE '. $this->_db->nameQuote('content_id') . ' = ' . $this->_db->quote($cid) . ' AND `type` = ' . $this->_db->quote($type);

		$this->_db->setQuery($sql);
		$result = $this->_db->query();

		return $result;
	}

	public function insertRuleset($cid, $type, $saveData)
	{
		$rules = $this->getRules('action');

		$newruleset = array();

		foreach($rules as $rule)
		{
			$action = $rule->action;
			$str = "(".$this->_db->quote($cid).", ".$this->_db->quote($rule->id).", ".$this->_db->quote($saveData[$action]).", ".$this->_db->quote($type).")";
			array_push($newruleset, $str);
		}

		if(!empty($newruleset))
		{
			$sql = 'INSERT INTO ' . $this->_db->nameQuote('#__komento_acl') . ' (`content_id`, `acl_id`, `status`, `type`) VALUES ';
			$sql .= implode(',', $newruleset);
			$this->_db->setQuery($sql);

			return $result = $this->_db->query();
		}

		return true;
	}

	public function getRuleSet($type, $cid, $add=false)
	{
		$rulesets = new stdClass();
		$rulesets->rules = new stdClass();

		//get rules
		$rules = $this->getRules('id');
		foreach($rules as $rule)
		{
			$rulesets->rules->{$rule->action} = (INT)$rule->default;
		}

		if(!$add)
		{
			//get user
			$query	= $this->_buildQuery($type, $cid);
			$this->_db->setQuery($query);
			$row	= $this->_db->loadObject();

			$rulesets->id	= $row->id;
			$rulesets->name	= $row->name;
			$rulesets->level	= '0';

			//get acl group ruleset
			$sql	= 'SELECT * FROM ' . $this->_db->nameQuote('#__komento_acl') . ' WHERE '. $this->_db->nameQuote('content_id') . ' = ' . $this->_db->quote($cid) .' AND '. $this->_db->nameQuote('type') . ' = ' . $this->_db->quote($type);
			$this->_db->setQuery($sql);
			$row	= $this->_db->loadAssocList();

			if(count($row) > 0)
			{
				foreach($row as $data)
				{
					if(isset($rules[$data['acl_id']]))
					{
						$action = $rules[$data['acl_id']]->action;
						$rulesets->rules->{$action} = $data['status'];
					}
				}
			}
		}

		return $rulesets;
	}

	public function getRuleSets($type='group', $cid='')
	{
		$rulesets	= new stdClass();
		$ids		= array();

		$rules		= $this->getRules('id');

		//get user
		$query		= $this->_buildQuery($type, $cid);

		$pagination = $this->getPagination( $type );
		$rows		= $this->_getList($query, $pagination->limitstart, $pagination->limit );

		if(!empty($rows))
		{
			foreach($rows as $row)
			{
				$rulesets->{$row->id}			= new stdClass();
				$rulesets->{$row->id}->id		= $row->id;
				$rulesets->{$row->id}->name		= $row->name;
				$rulesets->{$row->id}->level	= $row->level;

				foreach($rules as $rule)
				{
					$rulesets->{$row->id}->{$rule->action} = (INT)$rule->default;
				}

				array_push($ids, $row->id);
			}

			//get acl group ruleset
			$sql	= 'SELECT * FROM ' . $this->_db->nameQuote('#__komento_acl') . ' WHERE '. $this->_db->nameQuote('type') . ' = ' . $this->_db->quote($type) . ' AND `content_id` IN (' . implode( ' , ', $ids ) . ')';
			$this->_db->setQuery($sql);
			$acl	= $this->_db->loadAssocList();

			if( count( $acl ) > 0)
			{
				foreach($acl as $data)
				{
					if(isset($rules[$data['acl_id']]))
					{
						$action = $rules[$data['acl_id']]->action;
						$rulesets->{$data['content_id']}->{$action} = $data['status'];
					}
				}
			}
		}

		return $rulesets;
	}

	public function _buildQuery($type='group', $cid='')
	{
		switch($type)
		{
			case 'group':
				if(Komento::joomlaVersion() >= '1.6')
				{
					$query = 'SELECT a.id, a.title AS `name`, COUNT(DISTINCT b.id) AS level';
					$query .= ' , GROUP_CONCAT(b.id SEPARATOR \',\') AS parents';
					$query .= ' FROM #__usergroups AS a';
					$query .= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
				}
				else
				{
					$query	= 'SELECT `id`, `name`, 0 as `level` FROM ' . $this->_db->nameQuote('#__core_acl_aro_groups') . ' a ';
				}
				break;
			case 'assigned':
			default:
				$query	= 'SELECT DISTINCT(a.`id`), a.`name`, 0 as `level` FROM ' . $this->_db->nameQuote('#__users') . ' a LEFT JOIN ' . $this->_db->nameQuote('#__komento_acl') . ' b ON a.`id` = b.`content_id` ';
				break;
		}

		$where		= $this->_buildQueryWhere($type, $cid);
		$orderby	= $this->_buildQueryOrderBy($type);

		$query .= $where . ' ' . $orderby;

		return $query;
	}

	public function _buildQueryWhere($type='group', $cid='')
	{
		$mainframe	= JFactory::getApplication();

		//$search		= $mainframe->getUserStateFromRequest( 'com_komento.acls.search', 'search', '', 'string' );
		$search		= $this->_db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		if(empty($cid))
		{
			if ( $type )
			{
				if ( $type == 'group' )
				{
					if(Komento::joomlaVersion() < '1.6')
					{
						$where[] = '(a.`id` > 17 AND a.`id` < 26)';
					}
				}
				else if( $type == 'assigned' )
				{
					$where[] = 'b.`type` = '.$this->_db->quote($type);
				}
			}

			if ($search)
			{
				$where[] = ' LOWER( name ) LIKE \'%' . $search . '%\' ';
			}
		}
		else
		{
			if ( $type == 'group' )
			{
				$where[] = 'a.`id` = ' . $this->_db->quote($cid);
			}
			else if( $type == 'assigned' )
			{
				$where[] = 'a.`id` = '.$this->_db->quote($cid);
				$where[] = 'b.`type` = '.$this->_db->quote($type);
			}
		}

		$where = ( count( $where ) ? ' WHERE ' .implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildQueryOrderBy($type = 'group')
	{
		$mainframe			= JFactory::getApplication();

		$filter_order 		= $mainframe->getUserStateFromRequest( 'com_komento.acls.filter_order', 'filter_order', 'a.`id`', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_komento.acls.filter_order_Dir', 'filter_order_Dir', '', 'word' );

		if(($type == 'group') && (Komento::joomlaVersion() >= '1.6'))
		{
			$orderby	 = ' GROUP BY a.id';
			$orderby	.= ' ORDER BY a.lft ASC';
		}
		else
		{
			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
		}

		return $orderby;
	}

	public function getAclObject( $cid = 0, $type = 'group', $component = 'com_content' )
	{
		$db		= JFactory::getDbo();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__komento_acl' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'cid' ) . '=' . $db->quote( $cid ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->quote( $type ) . ' '
				. 'AND ' . $db->nameQuote( 'component' ) . '=' . $db->quote( $component ) . ' '
				. 'ORDER By ' . $db->nameQuote( 'id' );
		$db->setQuery( $query );

		return $db->loadObject();
	}

	// user model
	public function getUserGroupId( $uid )
	{
		$db	= JFactory::getDBO();

		$query	= 'SELECT ' . $db->nameQuote( 'group_id' )
				. ' FROM ' . $db->nameQuote( '#__user_usergroup_map' )
				. ' WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->quote( $uid );
		$db->setQuery( $query );

		return $db->loadResultArray();
	}

	// user model
	private function getPublicGroupId()
	{
		if(Komento::joomlaVer() >= '1.6')
		{
			$query	= 'SELECT '. $db->nameQuote( 'id' )
					. ' FROM ' . $db->nameQuote( '#__usergroups' )
					. ' WHERE ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( '0' );
			$db->setQuery( $query );
			$publicGroup = $db->loadResult();
		}
		else
		{
			$publicGroup = '28';
		}

		return $publicGroup;
	}

	public function getAclGroupPublic()
	{
		$publicGroupId = $this->getPublicGroupId();

		return getAclGroup( $publicGroupId, 'group' );
	}
}
