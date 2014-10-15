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

// import joomla controller library
jimport( 'joomla.application.component.view' );
jimport( 'joomla.application.component.model' );
jimport( 'joomla.form.form' );

class XdownloaderViewDefault extends JView {
	
    function  display($tpl = null) {
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination'); 

		JModel::addIncludePath(JPATH_ROOT.DS.'administrator/components/com_users/models', 'UsersModel');
		$modelGroups = JModel::getInstance('Groups', 'UsersModel');
		
		$groups = $modelGroups->getItems();
		
		$options = array();
		$values = new stdClass();
		$values->value = '';
		$values->text = '- Select group -';
		array_push($options, $values);
		
		if(!empty($groups)) {
			foreach($groups as $group) {
				$values = new stdClass();
				$values->value = $group->id;
				$values->text = str_repeat('- ', $group->level).$group->title;
				array_push($options, $values);
			}
		}
		
		$this->filterGroups = JHtml::_('select.genericlist', $options, 'filter_groups', 'title="'.JText::_('COM_XDOWNLOADER_USERGRUOPS_DESC').'"', 'value', 'text', $this->state->get('filter.groups', ''));
		$this->filterForm = JForm::getInstance('form_dates', JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'forms'.DS.'filterdates.xml', array('load_data' => false));		
		
		$this->addToolbar();    	
        parent::display($tpl);
    }
    
    protected function addToolbar() {
        // add system menu buttons
        JTooLBarHelper::title(JText::_('COM_XDOWNLOADER_LOG_TITLE'));
        JToolbarHelper::divider();
        JToolbarHelper::deleteList(JText::_('COM_XDOWNLOADER_DELETE_DLG'), 'default.delete', 'Delete');
    }        
}
?>