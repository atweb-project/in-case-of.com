<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

Komento::import( 'helper', 'date' );
//Komento::import( 'helper', 'tooltip' );
Komento::import( 'helper', 'string' );
Komento::import( 'helper', 'comment' );
//Komento::import( 'helper', 'router' );

class KomentoThemes
{
	// Holds all the template variables
	public $vars			= null;
	private $_system			= null;
	protected $_json		= null;

	// User selected theme
	protected $_theme		= null;
	protected $_direction	= null;
	protected $_themeInfo	= array();

	/**
	 * Pass theme name from config
	 */
	public function __construct( $theme = 'kuro' )
	{
		$this->_theme	= $theme;

		$obj			= new stdClass();
		$obj->config	= Komento::getConfig();
		$obj->konfig	= Komento::getKonfig();
		$obj->my		= Komento::getProfile();
		$obj->acl		= Komento::getHelper( 'acl' );

		$this->_system	= $obj;
	}

	public function getDirection()
	{
		if ($this->_direction === null)
		{
			$document	= JFactory::getDocument();
			$this->_direction	= $document->getDirection();
		}

		return $this->_direction;
	}

	public function getNouns( $text , $count , $includeCount = false )
	{
		return KomentoStringHelper::getNoun( $text , $count , $includeCount );
	}

	public function chopString( $string , $length )
	{
		return JString::substr( $string , 0 , $length );
	}

	public function formatDate( $format , $dateString )
	{
		$date	= KomentoDateHelper::dateWithOffSet($dateString);
		return KomentoDateHelper::toFormat($date, $format);
	}

	/**
	 * Set a template variable.
	 */
	public function set($name, $value)
	{
		$this->vars[$name] = $value;
	}

	public function getName()
	{
		return $this->_theme;
	}

	/**
	 * Open, parse, and return the template file.
	 *
	 * @param $file string the template file name
	 */
	public function fetch( $file )
	{
		static $tpl = array();

		if (empty($tpl[$file]))
		{
			$mainframe		= JFactory::getApplication();

			// load the file based on the theme's config.ini
			$info 			= $this->getThemeInfo( $this->_theme );

			/**
			 * Precedence in order.
			 * 1. Template override
			 * 2. Selected theme
			 * 3. Parent theme
			 * 4. Default system theme
			 */

			$overridePath	= JPATH_ROOT . DS . 'templates' . DS . $mainframe->getTemplate() . DS . 'html' . DS . 'com_komento' . DS . $file;
			$selectedPath	= KOMENTO_THEMES . DS . $this->_theme . DS . $file;
			$parentPath		= KOMENTO_THEMES . DS . $info->get( 'parent' ) . DS . $file;
			$defaultPath	= KOMENTO_THEMES . DS . 'kuro' . DS . $file;

			// 1. Template overrides
			if( JFile::exists( $overridePath ) )
			{
				$tpl[$file]	= $overridePath;
			}
			// 2. Selected themes
			elseif( JFile::exists( $selectedPath ) )
			{
				$tpl[$file]	= $selectedPath;
			}
			// 3. Parent themes
			elseif( JFile::exists( $parentPath ) )
			{
				$tpl[$file]	= $parentPath;
			}
			// 4. Default system theme
			else
			{
				$tpl[$file]	= $defaultPath;
			}
		}

		$system = $this->_system;

		if( isset( $this->vars ) )
		{
			extract($this->vars);
		}

		ob_start();

		if( !JFile::exists( $tpl[$file] ) )
		{
			echo JText::sprintf( 'Invalid template file %1s' , $tpl[$file] );
		}
		else
		{
			include($tpl[$file]);
		}

		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * Renders a nice checkbox switch.
	 *
	 * @param	string	$option		Name attribute for the checkbox.
	 * @param	string	$sate		State of the checkbox, checked or not.
	 * @return	string	HTML output.
	 */
	public function renderCheckbox( $option , $state )
	{
		ob_start();
	?>
		<div class="si-optiontap">
			<label class="option-enable<?php echo $state == 1 ? ' selected' : '';?>"><span><?php echo JText::_( 'COM_KOMENTO_NO_SWITCH' );?></span></label>
			<label class="option-disable<?php echo $state == 0 ? ' selected' : '';?>"><span><?php echo JText::_( 'COM_KOMENTO_YES_SWITCH' ); ?></span></label>
			<input name="<?php echo $option; ?>" value="<?php echo $state;?>" type="radio" class="radiobox" checked="checked" style="display: none;" />
		</div>
	<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function json_encode( $value )
	{
		if ($this->_json === null)
		{
			include_once( KOMENTO_CLASSES . DS . 'json.php' );
			$this->_json	= new Services_JSON();
		}

		return $this->_json->encode( $value );
	}

	public function json_decode( $value )
	{
		if ($this->_json === null)
		{
			include_once( KOMENTO_CLASSES . DS . 'json.php' );
			$this->_json	= new Services_JSON();
		}

		return $this->_json->decode( $value );
	}

	public function escape( $val )
	{
		return KomentoStringHelper::escape( $val );
	}

	public function getThemeInfo( $name )
	{
		if (empty($this->_themeInfo[$name]))
		{
			$mainframe	= JFactory::getApplication();
			$file		= '';

			// We need to specify if the template override folder also have config.ini file
			if ( JFile::exists( JPATH_ROOT . DS . 'templates' . DS . $mainframe->getTemplate() . DS . 'html' . DS . 'com_komento' . DS . 'config.ini' ) )
			{
				$file = JPATH_ROOT . DS . 'templates' . DS . $mainframe->getTemplate() . DS . 'html' . DS . 'com_komento' . DS . 'config.ini';
			}

			// then check the current theme folder
			elseif ( JFile::exists( JPATH_ROOT . DS . 'components' . DS . 'com_komento'. DS . 'themes' . DS . $name . DS . 'config.ini' ) )
			{
				$file = JPATH_ROOT . DS . 'components' . DS . 'com_komento' . DS . 'themes' . DS . $name . DS . 'config.ini';
			}

			if( !empty( $file ) )
			{
				$this->_themeInfo[$name]	= new JParameter( JFile::read( $file ) );
			}
			else{
				$this->_themeInfo[$name] = new JParameter( '' );
			}
		}

		return $this->_themeInfo[$name];
	}
}
