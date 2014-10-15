<?php
/**
 * @version $Id$
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'elements'.DS.'categories.php' );

class RokCandyViewCandyMacros extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 *
	 * @return	void
	 */
	public function display($tpl = null)
	{
        $doc =  JFactory::getDocument();

        $this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
        $this->template     = $this->get('Template');
        $this->overrides    = $this->get('TemplateOverrides');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		foreach ($this->items as &$item) {
			$item->order_up = true;
			$item->order_dn = true;
		}

        $doc->addStyleSheet(JURI::base(true).'/components/com_rokcandy/assets/rokcandy.css');

        $published = ($this->state->get('filter.published')==1 || $this->state->get('filter.published')=="" || $this->state->get('filter.published')=="*") ? true : false;
        $inCat = ($this->state->get('filter.category_id')=="" || $this->state->get('filter.category_id')==-1 ) ? true : false;
        $showOverrides = ($published && $inCat) ? true : false;

		$this->assign('showOverrides', $showOverrides);

        // We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}
        
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/rokcandy.php';
        $option = JRequest::getCmd('option');
        $view = JRequest::getCmd('view');
        $lang   = JFactory::getLanguage();
		$canDo	= RokCandyHelper::getActions($this->state->get('filter.category_id'));
		$user	= JFactory::getUser();
		JToolBarHelper::title(JText::_('COM_ROKCANDY_MANAGER_MACRO'), 'rokcandy.png');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_rokcandy', 'core.create'))) > 0) {
			JToolBarHelper::addNew('candymacro.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			JToolBarHelper::editList('candymacro.edit');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::publish('candymacros.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('candymacros.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('candymacros.archive');
			JToolBarHelper::checkin('candymacros.checkin');
		}

			if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'candymacros.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		}
		elseif ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('candymacros.trash');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_rokcandy');
			JToolBarHelper::divider();
		}

        JToolBarHelper::help(strtoupper($option).'_'.strtoupper($view).'_HELP_URL', TRUE, '', $option);
	}
}