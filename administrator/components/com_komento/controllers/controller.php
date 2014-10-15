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

jimport('joomla.application.component.controller');

class KomentoController extends JController
{
	protected static $instances;

	public function __construct($config = array())
	{
		$document	= JFactory::getDocument();
		$config		= Komento::getConfig();

		$toolbar	= JToolbar::getInstance( 'toolbar' );
		$toolbar->addButtonPath( KOMENTO_ADMIN_ROOT . DS . 'assets' . DS . 'images');

		$config = Komento::getConfig();
		$konfig = Komento::getKonfig();

		// Load foundry bootstrap
		$foundry_environment = $konfig->get( 'foundry_environment' );
		require_once( KOMENTO_FOUNDRY_BOOTSTRAP );

		$environment = JRequest::getVar( 'komento_environment' , $konfig->get( 'komento_environment' ) );

		$folder	= 'scripts';

		// @task: Let's see if we should load the dev scripts.
		if( $environment == 'development' )
		{
			$folder		= 'scripts_';
		}

		$document->addScript( rtrim( JURI::root() , '/' ) . '/media/com_komento/' . $folder . '/abstract.js' );

		$url		= rtrim( JURI::root() , '/' );

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

		$config 	= Komento::getConfig();
		$konfig		= Komento::getKonfig();
		$token		= Komento::_( 'getToken' );

		$url 		.= '/administrator/index.php?option=com_komento&' . $token . '=1';

		$environment = JRequest::getVar( 'komento_environment' , $konfig->get( 'komento_environment' ) );
		ob_start();
			include(JPATH_ROOT . DS . 'media' . DS . 'com_komento' . DS . 'bootstrap.js');
			$bootstrap = ob_get_contents();
		ob_end_clean();

		$document->addScriptDeclaration($bootstrap);

		$version	= str_ireplace( '.' , '' , Komento::komentoVersion() );
		$document->addScript( rtrim( JURI::root() , '/' ) . '/administrator/components/com_komento/assets/js/admin.js?' . $version );
		$document->addStyleSheet( rtrim( JURI::root() , '/' ) . '/administrator/components/com_komento/assets/css/reset.css?' . $version );
		$document->addStyleSheet( rtrim( JURI::root() , '/' ) . '/components/com_komento/assets/css/common.css?' . $version );
		$document->addStyleSheet( rtrim( JURI::root() , '/' ) . '/administrator/components/com_komento/assets/css/style.css?' . $version );

		// For the sake of loading the core.js in Joomla 1.6 (1.6.2 onwards)
		if( Komento::joomlaVersion() >= '1.6' )
		{
			JHTML::_('behavior.framework');
		}

		parent::__construct($config);
	}

	public function display($cacheable = false, $urlparams = false)
	{
		$document	= JFactory::getDocument();

		// Set the layout
		$viewType	= $document->getType();
		$viewName	= JRequest::getCmd( 'view', $this->getName() );
		$viewLayout	= JRequest::getCmd( 'layout', 'default' );
		$view		= $this->getView( $viewName, $viewType, '' );
		$view->setLayout($viewLayout);

		$format		= JRequest::getCmd( 'format' , 'html' );

		// Test if the call is for Ajax
		if( !empty( $format ) && $format == 'ejax' )
		{
			// Ajax calls.
			if( !JRequest::checkToken( 'GET' ) )
			{
				$ejax	= new Ejax();
				$ejax->script( 'alert("' . JText::_('Not allowed here') . '");' );
				$ejax->send();
			}

			// Process Ajax call
			$data		= JRequest::get( 'POST' );
			$arguments	= array();

			foreach( $data as $key => $value )
			{
				if( JString::substr( $key , 0 , 5 ) == 'value' )
				{
					if(is_array($value))
					{
						$arrVal    = array();
						foreach($value as $val)
						{
							$item   =& $val;
							$item   = stripslashes($item);
							$item   = rawurldecode($item);
							$arrVal[]   = $item;
						}

						$arguments[]	= $arrVal;
					}
					else
					{
						$val			= stripslashes( $value );
						$val			= rawurldecode( $val );
						$arguments[]	= $val;
					}
				}
			}

			// if(!method_exists( $view , $viewLayout ) )
			// {
			// 	$ejax	= new Ejax();
			// 	$ejax->script( 'alert("' . JText::sprintf( 'Method %1$s does not exists in this context' , $viewLayout ) . '");');
			// 	$ejax->send();

			// 	return;
			// }

			// Execute method
			call_user_func_array( array( $view , $viewLayout ) , $arguments );
		}
		else
		{
			// Non ajax calls.
			// Get/Create the model
			if ($model = $this->getModel($viewName))
			{
				// Push the model into the view (as default)
				$view->setModel($model, true);
			}

			if( $viewLayout != 'default' )
			{
				if( $cacheable )
				{
					$cache	= JFactory::getCache( 'com_komento' , 'view' );
					$cache->get( $view , $viewLayout );
				}
				else
				{
					if( !method_exists( $view , $viewLayout ) )
					{
						$view->display();
					}
					else
					{
						// @todo: Display error about unknown layout.
						$view->$viewLayout();
					}
				}
			}
			else
			{
				$view->display();
			}


			// Add necessary buttons to the site.
			if( method_exists( $view , 'registerToolbar' ) )
			{
				$view->registerToolbar();
			}

			// Override submenu if needed
			if( method_exists( $view , 'registerSubmenu' ) )
			{
				$this->loadSubmenu( $view->getName() , $view->registerSubmenu() );
			}
		}
	}

	public static function getInstance( $controllerName, $config = array() )
	{
		if( !self::$instances )
		{
			self::$instances = array();
		}

		$controllerName = preg_replace('/[^A-Z0-9_]/i', '', trim($controllerName));

		// Set the controller name
		$className	= 'KomentoController' . ucfirst( $controllerName );

		if( !isset( self::$instances[ $className ] ) )
		{
			if( !class_exists( $className ) )
			{
				jimport( 'joomla.filesystem.file' );
				$controllerFile	= KOMENTO_ADMIN_ROOT . DS . 'controllers' . DS . JString::strtolower( $controllerName ) . '.php';

				if( JFile::exists( $controllerFile ) )
				{
					require_once( $controllerFile );

					if( !class_exists( $className ) )
					{
						// Controller does not exists, throw some error.
						JError::raiseError( '500' , JText::sprintf('Controller %1$s not found' , $className ) );
					}
				}
				else
				{
					// File does not exists, throw some error.
					JError::raiseError( '500' , JText::sprintf('Controller %1$s.php not found' , $controllerName ) );
				}
			}

			self::$instances[ $className ]	= new $className();
		}

		return self::$instances[ $className ];
	}

	private function loadSubmenu( $viewName , $path = 'submenu.php' )
	{
		JHTML::_('behavior.switcher');

		//Build submenu
		$contents = '';
		ob_start();
			require_once( KOMENTO_ADMIN_ROOT . DS . 'views' . DS . $viewName . DS . 'tmpl' . DS . $path );
			$contents = ob_get_contents();
		ob_end_clean();

		$document = JFactory::getDocument();
		$document->setBuffer($contents, 'modules', 'submenu');
	}

	private function getCurrentUrl()
	{
		$url		= rtrim( JURI::root() , '/' );
		$currentURL	= isset( $_SERVER[ 'HTTP_HOST' ] ) ? $_SERVER[ 'HTTP_HOST' ] : '';

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

		return $url;
	}

	public function ajaxGetSystemString()
	{
		$data = JRequest::getVar('data');
		echo JText::_(strtoupper($data));
	}

	public function updatedb()
	{
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_komento' . DS . 'install.default.php' );
		$class = new KomentoDatabaseUpdate;

		if( $class->update() )
		{
			$type = 'message';
			$message = 'DB Updated';

			$this->setRedirect( 'index.php?option=com_komento' , $message , $type );
		}
		else
		{
			$type = 'error';
			$message = 'Error updating DB';

			$this->setRedirect( 'index.php?option=com_komento' , $message , $type );
		}

		return;
	}

	public function cleardb()
	{
		$db = JFactory::getDBO();

		$query = array();
		$query[] = 'DELETE FROM `#__komento_activities`';
		$query[] = 'DELETE FROM `#__komento_actions`';
		$query[] = 'DELETE FROM `#__komento_comments`';
		$query[] = 'DELETE FROM `#__komento_captcha`';
		$query[] = 'DELETE FROM `#__komento_mailq`';
		$query[] = 'DELETE FROM `#__komento_subscription`';

		foreach( $query as $q )
		{
			$db->setQuery( $q );
			$db->query();
		}

		$this->setRedirect( 'index.php?option=com_komento' , 'DB Reset' , 'message' );

		return;
	}

	public function approveComment()
	{
		$id = JRequest::getInt( 'commentId', '' );
		$comment = Komento::getComment( $id );
		Komento::setCurrentComponent( $comment->component );

		$acl = Komento::getHelper( 'acl' );

		$type = 'message';
		$message = '';

		if( !$acl->allow( 'publish', $comment ) )
		{
			$type = 'error';
			$message = JText::_( 'COM_KOMENTO_NOT_ALLOWED' );
			$this->setRedirect( 'index.php?option=com_komento', $message, $type );
			return false;
		}

		$model		= Komento::getModel( 'comments' );
		if( $model->publish( $id ) )
		{
			$message	= JText::_('COM_KOMENTO_COMMENTS_COMMENT_PUBLISHED');
		}
		else
		{
			$message	= JText::_( 'COM_KOMENTO_COMMENTS_COMMENT_PUBLISH_ERROR' );
			$type		= 'error';
		}

		$this->setRedirect( 'index.php?option=com_komento', $message, $type );
		return true;
	}

	public function pulldb()
	{
		$db = JFactory::getDBO();

		$db->setQuery( 'select id, component, cid from #__komento_comments order by id' );
		$result = $db->loadObjectList();

		echo '<table><tr><td>id</td><td>component</td><td>cid</td></tr>';
		foreach( $result as $row )
		{
			echo '<tr>';
			echo '<td>' . $row->id . '</td>';
			echo '<td>' . $row->component . '</td>';
			echo '<td>' . $row->cid . '</td>';
			echo '</tr>';
		}
		echo '</table>';

		exit;
	}
}
