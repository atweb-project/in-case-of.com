<?php
/**
 * @version $Id$
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

/**
 * RokCandy Macros RokCandy Macro Controller
 *
 * @package		Joomla
 * @subpackage	RokCandy Macros
 * @since 1.5
 */
class RokCandyController extends JController
{

    /**
	 * @var		string	The default view.
	 * @since	1.6
	 */
	protected $default_view = 'candymacros';

	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/rokcandy.php';

		// Load the submenu.
		RokCandyHelper::addSubmenu(JRequest::getCmd('view', 'candymacros'));

		$view	= JRequest::getCmd('view', 'candymacros');
		$layout = JRequest::getCmd('layout', 'default');
		$id		= JRequest::getInt('id');

		// Check for edit form.
		if ($view == 'candymacro' && $layout == 'edit' && !$this->checkEditId('com_rokcandy.edit.candymacro', $id)) {

			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_rokcandy&view=candymacros', false));

			return false;
		}

		parent::display();

		return $this;
	}
}