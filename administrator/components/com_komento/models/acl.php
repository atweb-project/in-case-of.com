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
	 * Category total
	 *
	 * @var integer
	 */
	protected $total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	protected $pagination = null;

	/**
	 * Category data array
	 *
	 * @var array
	 */
	protected $data = null;


	protected $systemComponents = array();

	public function __construct($config = array())
	{
		parent::__construct($config);

		$mainframe	= JFactory::getApplication();

		$limit		= $mainframe->getUserStateFromRequest( 'com_komento.acls.limit', 'limit', $mainframe->getCfg('list_limit', 20), 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$this->systemComponents = array(
				'com_config', 'com_finder', 'com_media', 'com_redirect', 'com_users', 'com_content', 'com_komento'
			);
	}

	public function getComponents()
	{
		$db		= JFactory::getDbo();
		$query	= 'SELECT `component` FROM `#__komento_acl` GROUP BY `component`';
		$db->setQuery( $query );

		if (!$components = $db->loadResultArray())
		{
			array_unshift($components, 'com_content');
		}

		$this->updateUserGroups( $components );

		return $components;
	}

	public function getAvailableComponents()
	{
		$components	= array();

		if ($components	= $this->getRegisteredComponents())
		{
			$this->filterSystemComponents( $components, $this->systemComponents );
		}

		return $components;
	}

	public function updateUserGroups( $components )
	{
		$userGroups	= Komento::getJoomlaUserGroups();
		$userGroupIDs	= array();

		foreach ($userGroups as $userGroup) {
			$userGroupIDs[] = $userGroup->id;
		}

		if( !is_array($components) )
		{
			$components = (array) $components;
		}

		foreach ($components as $component)
		{
			$db			= JFactory::getDbo();
			$query		= 'SELECT `cid` FROM `#__komento_acl` WHERE `component` = '.$db->quote($component). ' AND `type` = \'usergroup\'';
			$db->setQuery( $query );
			$current	= $db->loadResultArray();

			foreach ($userGroupIDs as $userGroupID) {
				if (!in_array($userGroupID, $current))
				{
					$rules = '';

					// try to get the acl values from com_content
					if ($component != 'com_content')
					{
						$query = 'SELECT ' . $db->nameQuote( 'rules' ) . ' FROM ' . $db->nameQuote( '#__komento_acl' ) . ' WHERE ' . $db->nameQuote( 'component' ) . '=' . $db->quote( 'com_content' ) . ' AND ' . $db->nameQuote( 'cid' ) . '=' . $db->quote( $userGroupID );
						$db->setQuery( $query );
						$rules = $db->loadResult();
					}

					$query = 'INSERT INTO `#__komento_acl` ( `cid`, `component`, `type` , `rules` ) VALUES ( '.$db->quote($userGroupID).','.$db->quote($component).','.$db->quote('usergroup').','.$db->quote($rules).')';
					$db->setQuery( $query );
					$db->query();
				}
			}
		}
	}

	private function filterSystemComponents( &$components, $filter )
	{
		foreach ($components as $key => $component)
		{
			if (in_array( $component, $filter ))
			{
				unset($components[$key]);
			}
		}
	}

	private function getRegisteredComponents()
	{
		$db		= JFactory::getDbo();
		// experimental query
		$query	= 'SELECT `element` FROM `#__extensions` WHERE `type` = \'component\' AND `client_id` = 1 AND `enabled` = 1 AND `access` = 0';
		$db->setQuery( $query );

		return $db->loadResultArray();
	}

	private function getComponentsInDirectory()
	{
		$path	= JPATH_ROOT . DS . 'components';

		return JFolder::folders($path);
	}

	public function getData( $component = 'com_component', $type = 'usergroup', $cid = 0 )
	{
		// create default values for new component entry
		$this->updateUserGroups( $component );

		$db		= JFactory::getDbo();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__komento_acl' );
		$query .= ' WHERE ' . $db->nameQuote( 'component' ) . ' = ' . $db->quote( $component );
		$query .= ' AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->quote( $type );

		if( $cid != 0 )
		{
			$query .= ' AND ' . $db->nameQuote( 'cid' ) . ' = ' . $db->quote( $cid );
		}

		$query .= ' ORDER BY ' . $db->nameQuote( 'type' );
		$db->setQuery( $query );

		$rows = $db->loadObjectList();

		// bind the rules with rules.xml
		foreach($rows as $row)
		{
			if(!$row->rules)
			{
				continue;
			}

			$storedRules = json_decode($row->rules);

			$xmlRules	= $this->getEmptySet();

			$rules = array();
			foreach ($xmlRules as $xmlKey => $xmlRule) {
				$ruleStored = false;
				foreach( $storedRules as $storedKey => $storedRule ) {
					if( $xmlRule->name == $storedRule->name && $xmlRule->section == $storedRule->section ) {
						$rules[] = $storedRule;
						$ruleStored = true;
						break;
					}
				}

				if( !$ruleStored ) {
					$rules[] = $xmlRule;
				}
			}

			// $row->rules = json_encode($storedRules);
			$row->rules = json_encode($rules);
		}

		return $rows;
	}

	// deprecate
	public function getRuleSets( $cid, $type = 'usergroup', $component = 'com_content' )
	{
		$db		= JFactory::getDbo();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__komento_acl' )
				. ' WHERE ' . $db->nameQuote( 'component' ) . ' = ' . $db->quote( $component )
				. ' AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->quote( $type )
				. ' AND ' . $db->nameQuote( 'cid' ) . ' = ' . $db->quote( $cid )
				. ' ORDER By ' . $db->nameQuote( 'id' );
		$db->setQuery( $query );

		$row	= $db->loadObject();

		if ($row)
		{
			$rules		= json_decode($row->rules);
			$this->bindDefaultRulesets($rules);
			$row->data	= $rules;
		}
		else
		{
			$row		= new stdClass();
			$row->id	= 0;
			$row->cid	= 0;
			$row->component = 0;
			$row->type	= 0;
			$row->rules	= '';
			$row->data	= array_values( $this->getDefaultRulesets() );
		}

		return $row;
	}

	private function getDefaultRulesets()
	{
		$xmlFile	= KOMENTO_ROOT . DS . 'rules.xml';

		// Load the xml once
		static $xml = null;
		if (empty($xml)) {
			if (!JFile::exists($xmlFile)) return;
			$xml = simplexml_load_file($xmlFile);
		}

		// Build the default data array
		$rulesets = array();

		foreach ($xml->section as $section) {
			foreach ($section->rule as $rule) {
				$name	= (string) $rule->attributes()->name;
				foreach ($rule->attributes() as $k => $v) {
					$rulesets[$name][$k] = (string) $v;
					$rulesets[$name]['section'] = (string) $section->attributes()->name;
				}
			}
		}

		return $rulesets;
	}

	/**
	 * kind of expensive function, please use with care
	 */
	private function bindDefaultRulesets( &$row )
	{
		// Build the default data array
		$default = $this->getDefaultRulesets();

		// Build the user data array
		$data = array();
		$attributes = '@attributes';

		foreach ($row->section as $section) {
			foreach ($section->rule as $rule) {
				$name	= $rule->{$attributes}->name;
				foreach ($rule->{$attributes} as $k => $v) {
					$data[$name][$k] = $v;
					$data[$name]['section'] = $section->{$attributes}->name;
				}
			}
		}

		// Merge both arrays
		$row	= array_values( array_merge($default, $data) );
	}

	public function save( $data )
	{
		$component = JRequest::getCmd( 'target_component' );
		$this->cleanVar( $data );

		$rules = array();

		// arrange in groups
		$acl = array();
		foreach ($data as $key => $value) {
			$key = explode(':', $key);
			if( isset($key[0] ) && isset($key[1] ) && isset($key[2] ) )
			{
				$acl[$key[0]][$key[1]][$key[2]] = $value;
			}
		}

		// save
		foreach ($acl as $type => $sets) {
			foreach ($sets as $cid => $rows) {
				$table = Komento::getTable( 'Acl' );
				$table->compositeLoad( $cid, $type, $component );
				$table->rules = json_encode( $this->array2Object($rows) );
				if (!$table->store())
				{
					return false;
					break;
				}
			}
		}

		return true;
	}

	private function cleanVar( &$data )
	{
		$var = array();

		foreach ($data as $key => $value)
		{
			$key = preg_replace('/[^A-Z0-9_\.-:]/i', '', trim($key));
			$value	= (bool) $value ? '1' : '0';

			$var[$key] = $value;
		}

		$data = $var;
	}

	private function array2Object( $rows )
	{
		$xmlFile	= KOMENTO_ROOT . DS . 'rules.xml';
		if (!JFile::exists($xmlFile)) return false;
		$xml = simplexml_load_file($xmlFile);

		$rules = array();

		foreach ($xml->children() as $child)
		{
			foreach ($child->children() as $rule)
			{
				$rules[] = (object) array(
					'name' => (string) $rule['name'],
					'title' => (string) $rule['title'],
					'value' => $rows[(string) $rule['name']],
					'section' => (string) $child['name']
				);
			}
		}

		return $rules;
	}

	public function getEmptySet()
	{
		$xmlFile	= KOMENTO_ROOT . DS . 'rules.xml';
		if (!JFile::exists($xmlFile)) return false;
		$xml = simplexml_load_file($xmlFile);

		$rules = array();

		foreach ($xml->children() as $child)
		{
			foreach ($child->children() as $rule)
			{
				$rules[] = (object) array(
					'name' => (string) $rule['name'],
					'title' => (string) $rule['title'],
					'value' => '0',
					'section' => (string) $child['name']
				);
			}
		}

		return $rules;
	}
}
