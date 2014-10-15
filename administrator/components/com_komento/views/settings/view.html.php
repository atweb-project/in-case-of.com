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

class KomentoViewSettings extends KomentoAdminView
{
	public function display($tpl = null)
	{
		// This is necessary for tabbing.
		jimport('joomla.html.pane');

		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		if( Komento::joomlaVersion() >= '1.6' )
		{
			if(!$user->authorise('core.manage.settings' , 'com_komento') )
			{
				$mainframe->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				$mainframe->close();
			}
		}

		$component	= $mainframe->getUserStateFromRequest( 'com_komento.settings.component' , 'component' , 'com_content' );

		$konfig		= Komento::getKonfig();
		$componentObj	= Komento::getHelper( 'components' )->getComponentObject( $component );

		$version	= Komento::joomlaVersion();

		// Get a list of components
		$components	= array();
		$result		= Komento::getHelper( 'components' )->getAvailableComponents();

		// @task: Translate each component with human readable name.
		foreach( $result as $item )
		{
			$components[ $item ]	= JText::_( 'COM_KOMENTO_' . strtoupper( $item ) );
		}

		$this->assignRef( 'konfig'			, $konfig );
		$this->assignRef( 'joomlaVersion'	, $version );
		$this->assignRef( 'component'		, $component );
		$this->assignRef( 'components'		, $components );
		$this->assignRef( 'componentObj'	, $componentObj );

		parent::display($tpl);
	}

	public function registerToolbar()
	{
		$mainframe = JFactory::getApplication();
		$component = $mainframe->getUserStateFromRequest( 'com_komento.acl.component', 'component', 'com_content' );

		JToolBarHelper::title( JText::_( 'COM_KOMENTO_SETTINGS' ) , 'settings' );
		JToolBarHelper::back( 'Home' , 'index.php?option=com_komento');
		JToolBarHelper::divider();
		JToolBarHelper::apply( 'apply' );
		JToolBarHelper::save();
		JToolBarHelper::divider();
		JToolBarHelper::cancel();
	}

	public function registerSubmenu()
	{
		return 'submenu.php';
	}

	public function renderSetting( $text, $configName, $type = 'checkbox', $options = '' )
	{
		$type = 'render'.$type;

		$config = Komento::getKonfig();
		$state = $config->get( $configName, 0 );

		ob_start();
	?>
		<tr>
			<td width="300" class="key">
				<span class="<?php echo $configName; ?>"><?php echo JText::_( $text ); ?></span>
			</td>
			<td valign="top">
				<div class="has-tip">
					<div class="tip"><i></i><?php echo JText::_( $text . '_DESC' ); ?></div>
					<?php echo $this->$type( $configName, $state, $options );?>
				</div>
			</td>
		</tr>

	<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
