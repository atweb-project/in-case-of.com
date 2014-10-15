<?php
/**
 * @version     1.0.0
 * @package     com_easyorderexport
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author       <> - 
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Easyorderexport.
 */
class EasyorderexportViewSearches extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		
		$this->form		= $this->get('Form');
		
		JToolBarHelper::title( 'Export orders - Download CSV', 'generic.png' );
		parent::display($tpl);
	}
}
