<?php
/*------------------------------------------------------------------------
# com_xdownloader - xDownloader alpha component
# ------------------------------------------------------------------------
# author    Dmitri Gorbunov
# copyright Copyright (C) 2012 xrbyte.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.xrbyte.com
# Technical Support:  Forum - http://www.xrbyte.com/forum
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.modellist' );

class XdownloaderModelDefault extends JModelList {

	function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'a.title', 'a.filename',
				'a.filepath', 'a.dwn_date',
				'a.user_ip', 'a.ip_location',
				'a.menu', 'a.id'
			);
		}
		parent::__construct($config);
		
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('limitstart', $limitstart);
	}
	
    public function getTable($type = 'XdldrStock', $prefix = 'Table', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }
	
    public function delete($pks) {
    	$table = $this->getTable();
    	$counter = 0;
    	if(is_array($pks) && !empty($pks)) {
    		foreach($pks as $pk) {
    			if($table->delete($pk)) 
    				$counter++;
    		}
    	}
    	return $counter;
    }
    
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param	string	An optional ordering field.
	 * @param	string	An optional direction (asc|desc).
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null) {
		$context	= $this->context;
		
		$search = $this->getUserStateFromRequest($context.'.search', 'filter_search', '');
		$this->setState('filter.search', $search);
		
		$userGroups = $this->getUserStateFromRequest($context.'.groups', 'filter_groups', '');
		$this->setState('filter.groups', $userGroups);

		$title = $this->getUserStateFromRequest($context.'.title', 'filter_title', '');
		$this->setState('filter.title', $title);

		$filename = $this->getUserStateFromRequest($context.'.filename', 'filter_filename', '');
		$this->setState('filter.filename', $filename);

		$filepath = $this->getUserStateFromRequest($context.'.filepath', 'filter_filepath', '');
		$this->setState('filter.filepath', $filepath);

		$ip = $this->getUserStateFromRequest($context.'.ip', 'filter_ip', '');
		$this->setState('filter.ip', $ip);

		$iplocation = $this->getUserStateFromRequest($context.'.iplocation', 'filter_iplocation', '');
		$this->setState('filter.iplocation', $iplocation);

		$linkgroups = $this->getUserStateFromRequest($context.'.linkgroups', 'filter_linkgroups', '');
		$this->setState('filter.linkgroups', $linkgroups);

		$date = $this->getUserStateFromRequest($context.'.date', 'filter_date', '');
		$this->setState('filter.date', $date);

		$datefrom = $this->getUserStateFromRequest($context.'.datefrom', 'filter_datefrom', '');
		$this->setState('filter.datefrom', $datefrom);

		$dateto = $this->getUserStateFromRequest($context.'.dateto', 'filter_dateto', '');
		$this->setState('filter.dateto', $dateto);
		
    	// ordering
		$orderCol	= JRequest::getCmd('filter_order', 'a.id');
		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'a.id';
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', 'ASC');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
			$listOrder = 'DESC';
		}
		$this->setState('list.direction', $listOrder);
		
		// List state information.
		parent::populateState('a.id', 'desc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.groups');
		$id	.= ':'.$this->getState('filter.title');
		$id	.= ':'.$this->getState('filter.filename');
		$id	.= ':'.$this->getState('filter.filepath');
		$id	.= ':'.$this->getState('filter.ip');
		$id	.= ':'.$this->getState('filter.iplocation');
		$id	.= ':'.$this->getState('filter.linkgroups');				

		return parent::getStoreId($id);
	}
	
	/**
	 * @return	string
	 * @since	1.6
	 */
	function getListQuery() {
		// Create a new query object.
		$table = $this->getTable();
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select('a.id, a.menu, a.guest, a.user_id, a.user_group, a.link_groups,'
						.'a.user_alias, a.user_ip, a.ip_location, a.title, a.filename,'
						.'a.filepath, a.dwn_date'
					);
		$query->from($table->getTableName().' AS a');

		// Filter by search in title
		$filterSearch = ''; 
		if($this->getState('filter.search') !== '') {
			$value = $this->getState('filter.search');
			$searchingFields = array();
			if(is_numeric($value)) {
				array_push($searchingFields, 
					'a.id = '.$db->quote($value));
				array_push($searchingFields, 
					'a.user_id = '.$db->quote($value));				
			}
			
			if(is_string($value)) {
				if(strtolower($value) == 'guest') {
					array_push($searchingFields, 
						'a.guest = '.$db->quote(1));
				}
				else {
					$token = $db->quote('%'.$db->getEscaped($value).'%');
					array_push($searchingFields, 
						'a.user_alias LIKE '.$token);
					array_push($searchingFields, 
						'a.link_groups LIKE '.$token);
					
				}	
			}
    		if(!empty($searchingFields))
    			$filterSearch = '('.implode(' OR ', $searchingFields).')';
		}
		
		$filterTitle = '';
		if($this->getState('filter.title') !== '') {
			$value = $this->getState('filter.title');
			$searchingFields = array();
			$token = $db->quote('%'.$db->getEscaped($value).'%');
			array_push($searchingFields, 
				'a.title LIKE '.$token);
  			$filterTitle = $searchingFields[0];
		}
		
		$filterFilename = '';
		if($this->getState('filter.filename') !== '') {
			$value = $this->getState('filter.filename');
			$searchingFields = array();
			$token = $db->quote('%'.$db->getEscaped($value).'%');
			array_push($searchingFields, 
				'a.filename LIKE '.$token);
  			$filterFilename = $searchingFields[0];
		}

		$filterFilepath = '';
		if($this->getState('filter.filepath') !== '') {
			$value = $this->getState('filter.filepath');
			$searchingFields = array();
			$token = $db->quote('%'.$db->getEscaped($value).'%');
			array_push($searchingFields, 
				'a.filepath LIKE '.$token);
  			$filterFilepath = $searchingFields[0];
		}

		$filterIP = '';
		if($this->getState('filter.ip') !== '') {
			$value = $this->getState('filter.ip');
			$searchingFields = array();
			$token = ip2long($value);
			array_push($searchingFields, 
				'a.user_ip = '.$db->quote($token));
  			$filterIP = $searchingFields[0];
		}

		$filterIPLocation = '';
		if($this->getState('filter.iplocation') !== '') {
			$value = $this->getState('filter.iplocation');
			$searchingFields = array();
			$token = $db->quote('%'.$db->getEscaped($value).'%');
			array_push($searchingFields, 
				'a.ip_location LIKE '.$token);
  			$filterIPLocation = $searchingFields[0];
		}
		
		$filterLinkgroups = '';
		if($this->getState('filter.linkgroups') !== '') {
			$value = $this->getState('filter.linkgroups');
			$searchingFields = array();
			$token = $db->quote('%'.$db->getEscaped($value).'%');
			array_push($searchingFields, 
				'a.link_groups LIKE '.$token);
  			$filterLinkgroups = $searchingFields[0];
		}

		$filterUserGroups = '';
		if($this->getState('filter.groups') !== '') {
			$value = $this->getState('filter.groups');
			$searchingFields = array();
			array_push($searchingFields, 
				'a.user_group = '.$db->quote($value));
  			$filterLinkgroups = $searchingFields[0];
		}
		
    	$filterDate = '';
    	if($this->getState('filter.date') !== '') {
    		$value = $this->getState('filter.date');
			// Escape the search token.
			$token	= $db->quote(JHtml::_('date', $value, JText::_('Y-m-d')));
			$filterDate = 'DATE(a.dwn_date) = DATE('.$token.')';
    	}

    	$filterDateFrom = '';
    	if($this->getState('filter.datefrom') !== '') {
    		$value = $this->getState('filter.datefrom');
			// Escape the search token.
			$token	= $db->quote(JHtml::_('date', $value, JText::_('Y-m-d H:m:s')));
			$filterDateFrom = 'DATE(a.dwn_date)';
    	}

    	$filterDateTo = '';
    	if($this->getState('filter.dateto') !== '') {
    		$value = $this->getState('filter.dateto');
			// Escape the search token.
			$token	= $db->quote(JHtml::_('date', $value, JText::_('Y-m-d H:m:s')));
			$filterDateTo = 'DATE(a.dwn_date)';
    	}
    	
    	$filterDateFromTo = '';
    	if(!empty($filterDateFrom) && !empty($filterDateTo)) {
    		$valueFrom = $db->quote(JHtml::_('date', $this->getState('filter.datefrom'), JText::_('Y-m-d H:i:s')));
    		$valueTo = $db->quote(JHtml::_('date', $this->getState('filter.dateto'), JText::_('Y-m-d H:i:s')));
    		$filterDateFromTo = 'a.dwn_date BETWEEN '.$valueFrom.' AND '.$valueTo;
    	}
    	elseif(!empty($filterDateFrom) && empty($filterDateTo)) {
			$valueFrom = $db->quote(JHtml::_('date', $this->getState('filter.datefrom'), JText::_('Y-m-d H:i:s')));
			$filterDateFromTo = 'DATE(a.dwn_date) >= DATE('.$valueFrom.')';    		
    	}
    	elseif(empty($filterDateFrom) && !empty($filterDateTo)) {
			$valueTo = $db->quote(JHtml::_('date', $this->getState('filter.dateto'), JText::_('Y-m-d H:i:s')));
			$filterDateFromTo = 'DATE(a.dwn_date) <= DATE('.$valueTo.')';    		
    	}
    	
		$whereCollector = array();
		if(!empty($filterSearch)) {
			$whereCollector[] = $filterSearch;
		}

		if(!empty($filterTitle)) {
			$whereCollector[] = $filterTitle;
		}
		
		if(!empty($filterFilename)) {
			$whereCollector[] = $filterFilename;
		}
		
		if(!empty($filterFilepath)) {
			$whereCollector[] = $filterFilepath;
		}
		
		if(!empty($filterIP)) {
			$whereCollector[] = $filterIP;
		}
		
		if(!empty($filterIPLocation)) {
			$whereCollector[] = $filterIPLocation;
		}

		if(!empty($filterLinkgroups)) {
			$whereCollector[] = $filterLinkgroups;
		}
		
		if(!empty($filterUserGroups)) {
			$whereCollector[] = $filterUserGroups;
		}		

		if(!empty($filterDate)) {
			$whereCollector[] = $filterDate;
		}		

		if(!empty($filterDateFromTo)) {
			$whereCollector[] = $filterDateFromTo;
		}		
		
		
		// Add WHERE query
		$whereCollectorString = '';
		if(!empty($whereCollector)) {
			reset($whereCollector);
			$whereCollectorString = implode(' OR ', $whereCollector);
			$query->where($whereCollectorString);
		}
		
		// Add the list ordering clause.
		$query->order($this->getState('list.ordering').' '.$this->getState('list.direction'));
		
//		JError::raiseNotice(100, 'query = '.$query);
		return $query;
	}
}
?>