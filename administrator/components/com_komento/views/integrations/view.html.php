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

class KomentoViewIntegrations extends KomentoAdminView
{
	static $config;

	public function display($tpl = null)
	{
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		if( Komento::joomlaVersion() >= '1.6' )
		{
			if(!$user->authorise('core.manage.integration' , 'com_komento') )
			{
				$mainframe->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				$mainframe->close();
			}
		}

		// This is necessary for tabbing.
		jimport('joomla.html.pane');

		$app		= JFactory::getApplication();
		$component	= $app->getUserStateFromRequest( 'com_komento.integrations.component' , 'component' , 'com_content' );

		$config		= Komento::getConfig( $component );

		$this->config = $config;

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

		$this->assignRef( 'config'			, $config );
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

		/* big badass drop down list directly on title
		$components	= array();
		$result		= Komento::getHelper( 'components' )->getAvailableComponents();

		// @task: Translate each component with human readable name.
		foreach( $result as $item )
		{
			$components[ $item ]	= JText::_( 'COM_KOMENTO_' . strtoupper( $item ) );
		}

		$components = JHTML::_( 'select.genericlist' , $components , 'components' , 'class="inputbox" onchange="changeComponent(this.value)"' , 'value' , 'text' , $component );

		JToolBarHelper::title( '<table id="componentSelection"><tr><td class="title">' . JText::_( 'COM_KOMENTO_INTEGRATIONS' ) . ':</td><td>' . $components . '</td></tr></table>' , 'integrations' );*/

		JToolBarHelper::title( JText::_( 'COM_KOMENTO_INTEGRATIONS' ) . ': ' . JText::_( 'COM_KOMENTO_' . strtoupper( $component ) ) , 'integrations' );

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

	public function getEditorList( $selected )
	{
		$db		= JFactory::getDBO();

		// compile list of the editors
		if( Komento::joomlaVersion() >= '1.6' )
		{
			$query = 'SELECT `element` AS value, `name` AS text'
					.' FROM `#__extensions`'
					.' WHERE `folder` = "editors"'
					.' AND `type` = "plugin"'
					.' AND `enabled` = 1'
					.' ORDER BY ordering, name';
		}
		else
		{
			$query = 'SELECT element AS value, name AS text'
					.' FROM #__plugins'
					.' WHERE folder = "editors"'
					.' AND published = 1'
					.' ORDER BY ordering, name';
		}

		//echo $query;

		$db->setQuery( $query );
		$editors = $db->loadObjectList();

		if(count($editors) > 0)
		{
			if(Komento::joomlaVersion() >= '1.6')
			{
				$lang = JFactory::getLanguage();
				for($i = 0; $i < count($editors); $i++)
				{
					$editor = $editors[$i];
					$lang->load($editor->text . '.sys', JPATH_ADMINISTRATOR, null, false, false);
					$editor->text   = JText::_($editor->text);
				}
			}
		}

		// temporary. remove when wysiwyg editors are ready
		$editors = array();

		$bbcode = new stdClass();
		$bbcode->value = 'bbcode';
		$bbcode->text = JText::_( 'COM_KOMENTO_EDITOR_BBCODE' );

		$none = new stdClass();
		$none->value = 'none';
		$none->text = JText::_( 'COM_KOMENTO_EDITOR_NONE' );

		$editors[] = $bbcode;
		$editors[] = $none;

		return JHTML::_('select.genericlist',  $editors , 'form_editor', 'class="inputbox" size="1"', 'value', 'text', $selected );
	}

	public function renderSetting( $text, $configName, $type = 'checkbox', $options = '' )
	{
		$type = 'render'.$type;

		// $config = Komento::getConfig();
		$state = $this->config->get( $configName, 0 );

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
