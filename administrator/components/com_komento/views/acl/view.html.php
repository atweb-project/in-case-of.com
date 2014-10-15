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

require( KOMENTO_ADMIN_ROOT . DS . 'views.php');

class KomentoViewAcl extends KomentoAdminView
{
	public function display($tpl = null)
	{
		$mainframe	= JFactory::getApplication();
		$component	= $mainframe->getUserStateFromRequest( 'com_komento.acl.component', 'component', 'com_content' );
		$components = $this->getComponentState( $component );

		$usergroups = Komento::getUsergroups();

		$this->assignRef( 'usergroups', $usergroups );
		$this->assignRef( 'component', $component );
		$this->assignRef( 'components', $components );

		parent::display( $tpl );
	}

	public function form($tpl = null)
	{
		$mainframe	= JFactory::getApplication();
		$component	= $mainframe->getUserStateFromRequest( 'com_komento.acl.component', 'component', 'com_content' );
		$components = $this->getComponentState( $component );

		$id			= $mainframe->getUserStateFromRequest( 'com_komento.acl.id', 'id', '0' );
		$type		= JRequest::getCmd( 'type', 'usergroup' );

		$usergroups	= '';
		if( $type == 'usergroup' )
		{
			$usergroups = $this->getUsergroupState( $id );
		}

		$type = JRequest::getCmd( 'type' );
		$id = JRequest::getInt( 'id' );

		$model = $this->getModel( 'acl' );
		$rulesets = $model->getData( $component, $type, $id );

		$this->arrangeRulesets( $rulesets );

		$this->assignRef( 'rulesets', $rulesets );
		$this->assignRef( 'component', $component );
		$this->assignRef( 'components', $components );
		$this->assignRef( 'type', $type );
		$this->assignRef( 'id', $id );
		$this->assignRef( 'usergroups', $usergroups );

		parent::display( $tpl );
	}

	public function registerToolbar()
	{
		$mainframe = JFactory::getApplication();
		$component = $mainframe->getUserStateFromRequest( 'com_komento.acl.component', 'component', 'com_content' );

		/* big badass drop down list directly on title
		$components	= array();
		$result		= Komento::getHelper( 'components' )->getAvailableComponents();

		// @task: Translate each component with human readable name.
		foreach( $result as $item )
		{
			$components[ $item ]	= JText::_( 'COM_KOMENTO_' . strtoupper( $item ) );
		}

		$components = JHTML::_( 'select.genericlist' , $components , 'components' , 'class="inputbox" onchange="changeComponent(this.value)"' , 'value' , 'text' , $component );*/

		JToolBarHelper::title( JText::_( 'COM_KOMENTO_ACL' ) . ': ' . JText::_( 'COM_KOMENTO_' . strtoupper( $component ) ), 'acl' );

		JToolBarHelper::back( JText::_( 'COM_KOMENTO_BACK' ) , 'index.php?option=com_komento');

		if( JRequest::getCmd( 'layout' ) == 'form' )
		{
			JToolBarHelper::divider();
			JToolBarHelper::custom( 'enableall', 'kmt-enableall', '', JText::_( 'COM_KOMENTO_ACL_ENABLE_ALL' ), false );
			JToolBarHelper::custom( 'disableall', 'kmt-disableall', '', JText::_( 'COM_KOMENTO_ACL_DISABLE_ALL' ), false );
			JToolBarHelper::divider();
			JToolBarHelper::apply( 'apply' );
			JToolBarHelper::save();
			JToolBarHelper::divider();
			JToolBarHelper::cancel();
		}
	}

	private function arrangeRulesets( &$rows )
	{
		$data = array();

		foreach ($rows as $row)
		{
			$row->data = json_decode( $row->rules );

			if (empty($row->data))
			{
				$model	= $this->getModel( 'Acl' );
				$row->data = $model->getEmptySet();
			}

			foreach ($row->data as $rules)
			{
				$data[$row->type.':'.$row->cid][$rules->section][] = $rules;
			}
		}

		$rows = $data;
	}

	function getComponentState($filter_component = '')
	{
		$result		= Komento::getHelper( 'components' )->getAvailableComponents();
		$components	= array();

		foreach( $result as $item )
		{
			$components[$item] = JHTML::_( 'select.option', $item, JText::_( 'COM_KOMENTO_' . strtoupper( $item ) ) );
		}

		return JHTML::_( 'select.genericlist', $components, 'component', 'class="inputbox" size="1" onchange="submitform();"', 'value', 'text', $filter_component );
	}

	function getUsergroupState($filter_usergroup = '')
	{
		$result = Komento::getUsergroups();
		$usergroups = array();

		foreach( $result as $item )
		{
			$usergroups[] = JHTML::_( 'select.option', $item->id, str_repeat( '|—', $item->depth ) . ' ' . $item->title );
		}

		return JHTML::_( 'select.genericlist', $usergroups, 'id', 'class="inputbox" size="1" onchange="submitform();"', 'value', 'text', $filter_usergroup );
	}

	/*public function registerSubmenu()
	{
		return 'submenu.php';
	}*/
}
