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

class KomentoDocumentHelper
{
	static $loaded;

	public static function loadHeaders()
	{
		if( !self::$loaded )
		{
			if( Komento::joomlaVersion() >= '1.6' )
			{
				$uri 		= JFactory::getURI();
				$language	= $uri->getVar( 'lang' , 'none' );
				$app		= JFactory::getApplication();
				$jconfig	= JFactory::getConfig();
				$router		= $app->getRouter();
				$url		= rtrim( JURI::root() , '/' ) . '/index.php?option=com_komento&lang=' . $language;

				if( $router->getMode() == JROUTER_MODE_SEF && JPluginHelper::isEnabled("system","languagefilter") )
				{
					$rewrite	= $jconfig->get('sef_rewrite');

					$base		= str_ireplace( JURI::root( true ) , '' , $uri->getPath() );
					$path		= $rewrite ? $base : JString::substr( $base , 10 );
					$path		= JString::trim( $path , '/' );
					$parts		= explode( '/' , $path );

					if( $parts )
					{
						// First segment will always be the language filter.
						$language	= reset( $parts );
					}
					else
					{
						$language	= 'none';
					}

					if( $rewrite )
					{
						$url		= rtrim( JURI::root() , '/' ) . '/' . $language . '/?option=com_komento';
						$language	= 'none';
					}
					else
					{
						$url		= rtrim( JURI::root() , '/' ) . '/index.php/' . $language . '/?option=com_komento';
					}
				}
			}
			else
			{

				$url		= rtrim( JURI::root() , '/' ) . '/index.php?option=com_komento';
			}

			$menu	= JFactory::getApplication()->getMenu();
			$item	= $menu->getActive();
			if( isset( $item->id) )
			{
				$url    .= '&Itemid=' . $item->id;
			}

			// Some SEF components tries to do a 301 redirect from non-www prefix to www prefix.
			// Need to sort them out here.
			$currentURL		= isset( $_SERVER[ 'HTTP_HOST' ] ) ? $_SERVER[ 'HTTP_HOST' ] : '';

			if( !empty( $currentURL ) )
			{
				// When the url contains www and the current accessed url does not contain www, fix it.
				if( stristr($currentURL , 'www' ) === false && stristr( $url , 'www') !== false )
				{
					$url	= str_ireplace( 'www.' , '' , $url );
				}

				// When the url does not contain www and the current accessed url contains www.
				if( stristr( $currentURL , 'www' ) !== false && stristr( $url , 'www') === false )
				{
					$url	= str_ireplace( '://' , '://www.' , $url );
				}
			}

			$document	= JFactory::getDocument();
			$config = Komento::getConfig();
			$konfig = Komento::getKonfig();
			$acl = Komento::getAcl();
			$guest = Komento::getProfile()->guest ? 1 : 0;

			// @task: Include dependencies from foundry.
			$foundry_environment = $konfig->get( 'foundry_environment' );
			require_once( KOMENTO_FOUNDRY_BOOTSTRAP );

			$environment	= JRequest::getVar( 'komento_environment', $konfig->get( 'komento_environment', 'production' ) );

			$folder	= 'scripts';

			// @task: Let's see if we should load the dev scripts.
			if( $environment == 'development' )
			{
				$folder		= 'scripts_';
			}

			$document->addScript( rtrim( JURI::root() , '/' ) . '/media/com_komento/' . $folder . '/abstract.js' );

			ob_start();
				include(KOMENTO_MEDIA_ROOT . DS . 'com_komento' . DS . 'bootstrap.js');
				$output = ob_get_contents();
			ob_end_clean();

			$document->addScriptDeclaration( "/*<![CDATA[*/ $output /*]]>*/ " );

			// only temporary to load development css
			// waiting chang to finalise reset.css and comments.css
			self::addTemplateCss( 'common.css' );
			// self::addTemplateCss( 'comments.css' );
			$document->addStylesheet( JURI::root() . 'components/com_komento/themes/kuro/css/style.css' );

			// support for RTL sites
			// forcertl = 1 for dev purposes
			if( $document->direction == 'rtl' || JRequest::getInt( 'forcertl' ) == 1 )
			{
				$document->addStylesheet( JURI::root() . 'components/com_komento/themes/kuro/css/style-rtl.css' );
			}

			self::load('style', 'css', 'theme');

			// only temporary to load markitup js
			// waiting sean to pack markitup into foundry
			$dir = JURI::root() . 'components/com_komento/classes';

			self::$loaded		= true;
		}
		return self::$loaded;
	}

	/**
	 * Function to add js file, js script block and css file
	 * to HEAD section
	 */
	public static function load( $list, $type='js', $location='themes' )
	{
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config		= Komento::getConfig();

		// Always load mootools first so it will not conflict.
		JHTML::_('behavior.mootools');

		$files		= explode( ',', $list );
		$dir		= JURI::root() . 'components/com_komento/assets';
		$theme		= $config->get( 'layout_theme' );
		$version	= str_ireplace( '.' , '' , Komento::komentoVersion() );

		if ( $location != 'assets' )
		{
			$dir	= JURI::root() . 'components/com_komento/themes/' . $theme;
		}

		foreach( $files as $file )
		{
			if ( $type == 'js' )
			{
				$file .= '.js?' . $version;
			}
			elseif ( $type == 'css' )
			{
				$file .= '.css';
			}

			$path = '';
			if( $location == 'themes' )
			{
				$checkOverride	= JPATH_ROOT . DS . 'templates' . DS . $mainframe->getTemplate() . DS . 'html' . DS . 'com_komento' . DS . $type . DS . $file;
				$checkSelected	= KOMENTO_THEMES . DS . $theme . DS . $type . DS . $file;

				$overridePath	= JURI::root() . 'templates/' . $mainframe->getTemplate() . '/html/com_komento/' . $type . '/' . $file;
				$selectedPath	= $dir . '/' . $type . '/' . $file;
				$defaultPath	= JURI::root() . 'components/com_komento/themes/kuro/' . $type . '/' . $file;

				// 1. Template overrides
				if( JFile::exists( $checkOverride ) )
				{
					$path = $overridePath;
				}
				// 2. Selected themes
				elseif( JFile::exists( $checkSelected ) )
				{
					$path = $selectedPath;
				}
				// 3. Default system theme
				else
				{
					$path = $defaultPath;
				}
			}
			else
			{
				$path = $dir . '/' . $type . '/' . $file;
			}

			if ( $type == 'js' )
			{
				$document->addScript( $path );
			}
			elseif ( $type == 'css' )
			{
				$document->addStylesheet( $path );
			}
		}
	}

	/**
	 * Allows caller to detect specific css files from site's template
	 * and load it into the headers if necessary.
	 *
	 * @param	string $fileName
	 */
	public static function addTemplateCss( $fileName )
	{
		$document		= JFactory::getDocument();
		$document->addStyleSheet( rtrim(JURI::root(), '/') . '/components/com_komento/assets/css/' . $fileName );

		$mainframe		= JFactory::getApplication();
		$templatePath	= JPATH_ROOT . DS . 'templates' . DS . $mainframe->getTemplate() . DS . 'html' . DS . 'com_komento' . DS . 'assets' . DS . 'css' . DS . $fileName;

		if( JFile::exists($templatePath) )
		{
			$document->addStyleSheet( rtrim(JURI::root(), '/') . '/templates/' . $mainframe->getTemplate() . '/html/com_komento/assets/css/' . $fileName );

			return true;
		}

		return false;
	}

	/*
	 * Method for broswer detection
	 */
	public static function getBrowserUserAgent()
	{
		$browser = new stdClass;

		// set to lower case to avoid errors, check to see if http_user_agent is set
		$navigator_user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';

		// run through the main browser possibilities, assign them to the main $browser variable
		if (stristr($navigator_user_agent, "opera"))
		{
			$browser->userAgent = 'opera';
			$browser->dom = true;
		}
		elseif (stristr($navigator_user_agent, "msie 8"))
		{
			$browser->userAgent = 'msie8';
			$browser->dom = false;
		}
		elseif (stristr($navigator_user_agent, "msie 7"))
		{
			$browser->userAgent = 'msie7';
			$browser->dom = false;
		}
		elseif (stristr($navigator_user_agent, "msie 4"))
		{
			$browser->userAgent = 'msie4';
			$browser->dom = false;
		}
		elseif (stristr($navigator_user_agent, "msie"))
		{
			$browser->userAgent = 'msie';
			$browser->dom = true;
		}
		elseif ((stristr($navigator_user_agent, "konqueror")) || (stristr($navigator_user_agent, "safari")))
		{
			$browser->userAgent = 'safari';
			$browser->dom = true;
		}
		elseif (stristr($navigator_user_agent, "gecko"))
		{
			$browser->userAgent = 'mozilla';
			$browser->dom = true;
		}
		elseif (stristr($navigator_user_agent, "mozilla/4"))
		{
			$browser->userAgent = 'ns4';
			$browser->dom = false;
		}
		else
		{
			$browser->dom = false;
			$browser->userAgent = 'Unknown';
		}

		return $browser;
	}

	// Add canonical URL to satify Googlebot. Incase they think it's duplicated content.
	public static function addCanonicalURL( $extraFishes = array() )
	{
		if (empty( $extraFishes ))
		{
			return;
		}

		$juri		= JURI::getInstance();

		foreach( $extraFishes as $fish )
		{
			$juri->delVar( $fish );
		}

		$preferredURL	= $juri->toString();

		$document	= JFactory::getDocument();
		$document->addHeadLink( $preferredURL, 'canonical', 'rel');
	}
}
