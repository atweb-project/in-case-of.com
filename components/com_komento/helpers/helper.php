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

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_komento' . DS . 'bootstrap.php' );

class Komento
{
	public static $component;
	public static $application;

	public static function import( $type, $filename )
	{
		if ($type == 'helper')
		{
			require_once( KOMENTO_HELPERS . DS . $filename . '.php' );
		}
		if ($type == 'class')
		{
			require_once( KOMENTO_CLASSES . DS . $filename . '.php' );
		}

		return;
	}

	/**
	 * Retrieve specific helper objects.
	 *
	 * @param	string	$helper	The helper class. Class name should be the same name as the file. e.g KomentoXXXHelper
	 * @return	object	Helper object.
	 **/
	public static function getHelper( $name )
	{
		static $helpers	= array();

		if( empty( $helpers[ $name ] ) )
		{
			$file	= KOMENTO_HELPERS . DS . JString::strtolower( $name ) . '.php';

			if( JFile::exists( $file ) )
			{
				require_once( $file );
				$classname	= 'Komento' . ucfirst( $name ) . 'Helper';

				$helpers[ $name ] = class_exists($classname) ? new $classname() : false;
			}
			else
			{
				$helpers[ $name ]	= false;
			}
		}

		return $helpers[ $name ];
	}

	/**
	 * Retrieve JTable objects.
	 *
	 * @param	string	$tableName	The table name.
	 * @param	string	$prefix		JTable prefix.
	 * @return	object	JTable object.
	 **/
	public static function getTable( $tableName, $prefix = 'Komento' )
	{
		JTable::addIncludePath( KOMENTO_TABLES );
		$table    = JTable::getInstance( $tableName, $prefix );
		return $table;
	}

	/**
	 * Retrieve Model objects.
	 *
	 * @param	string	$name	The model name.
	 * @return	object	model object.
	 **/
	public static function getModel( $name, $backend = false )
	{
		static $models = array();

		$signature	= json_encode(array($name, (bool) $backend));

		if( empty( $models[ $signature ] ) )
		{
			$file	= $backend ? KOMENTO_ADMIN_ROOT . DS . 'models' : KOMENTO_MODELS;
			$file	.= DS . JString::strtolower( $name ) . '.php';

			if( JFile::exists( $file ) )
			{
				require_once( $file );
				$classname	= 'KomentoModel' . ucfirst( $name );

				$models[ $signature ] = class_exists($classname) ? new $classname() : false;
			}
			else
			{
				$models[ $signature ] = false;
			}
		}

		return $models[ $signature ];
	}

	/**
	 * Retrieve Class objects.
	 *
	 * @param	string	$filename	File name of the class.
	 * @param	string	$classname	Class name.
	 * @return	object	class object.
	 **/
	public static function getClass( $filename, $classname )
	{
		static $classes	= array();

		$sig	= md5(serialize(array($filename,$classname)));

		if ( empty($classes[$sig]) )
		{
			$file	= KOMENTO_CLASSES . DS . JString::strtolower( $filename ) . '.php';

			if( JFile::exists($file) )
			{
				require_once( $file );

				$classes[ $sig ] = class_exists($classname) ? new $classname() : false;
			}
			else{
				$classes[ $sig ] = false;
			}
		}

		return $classes[ $sig ];
	}

	/**
	 * Retrieve Komento's configuration.
	 *
	 * @return	object	JParameter object.
	 **/
	public static function getConfig( $component = '' )
	{
		static $configs	= array();

		$component	= $component ? $component : ( self::$component ? self::$component : 'com_content' );
		$component	= preg_replace('/[^A-Z0-9_\.-]/i', '', $component);

		if( empty( $configs[$component] ) )
		{
			//load default ini data first
			$ini		= KOMENTO_ADMIN_ROOT . DS . 'configuration.ini';
			$config		= new JParameter(JFile::read($ini));

			$default	= clone $config;

			if( Komento::joomlaVersion() >= '1.6' )
			{
				$config->default = $default->toObject();
			}
			else
			{
				$config->default = $default->_registry['_default']['data'];
			}

			// get config stored in db
			$dbConfig	= self::getTable( 'configs' );
			$dbConfig->load( $component );

			if( Komento::joomlaVersion() >= '1.6' )
			{
				$config->bind( $dbConfig->params , 'INI' );
			}
			else
			{
				$config->bind( $dbConfig->params );
			}

			$config->_current = $component;

			$configs[$component] = $config;
		}

		return $configs[$component];
	}

	public static function getKonfig()
	{
		static $konfig = null;

		if( !( $konfig instanceof JParameter ) )
		{
			//load default ini data first
			$konfig		= new JParameter(JFile::read( KOMENTO_ADMIN_ROOT . DS . 'konfiguration.ini' ));

			//get config stored in db
			$dbConfig	= self::getTable( 'configs' );
			$dbConfig->load( 'com_komento' );

			if( Komento::joomlaVersion() >= '1.6' )
			{
				$konfig->bind( $dbConfig->params , 'INI' );
			}
			else
			{
				$konfig->bind( $dbConfig->params );
			}
		}

		return $konfig;
	}

	public static function getACL()
	{
		$my			= JFactory::getUser();
		$userId		= $my->id;

		self::import( 'helper', 'acl' );

		$acl		= KomentoACLHelper::getRules( $userId, self::$component );
		$acl		= JArrayHelper::toObject( $acl );

		return $acl;
	}

	/**
	 * Retrieve Theme objects.
	 *
	 * @param	string	$sel_theme	Theme name.
	 * @return	object	Theme class
	 **/
	public static function getTheme( $new = false )
	{
		static $themeObj = array();

		if ( !class_exists('KomentoThemes') )
		{
			require_once(KOMENTO_CLASSES . DS . 'themes.php');
		}

		$config		= Komento::getConfig();
		$selected	= $config->get( 'layout_theme', 'kuro' );
		$override	= JRequest::getCmd( 'theme', '' );
		$theme		= $override ? $override : $selected;

		if( $new )
		{
			return new KomentoThemes( $theme );
		}
		else
		{
			if( empty( $themeObj[$theme] ) )
			{
				$themeObj[$theme] = new KomentoThemes( $theme );
			}
		}

		return $themeObj[$theme];
	}

	/**
	 * Method to get user's profile
	 *
	 * @param	$id		The user id. leave empty for current user.
	 * @return	object
	 */
	public static function getProfile( $id = null )
	{
		if (!class_exists('KomentoProfile'))
		{
			require_once( KOMENTO_CLASSES . DS . 'profile.php' );
		}

		return KomentoProfile::getUser($id);
	}

	public static function getComment( $id = 0, $process = 0, $admin = 0 )
	{
		static $commentsObj = array();

		if( empty( $commentsObj[$id] ) )
		{
			$comment = new KomentoComment( $id );
			$commentsObj[$id] = $comment;

			if( $process )
			{
				self::import( 'helper', 'comment' );
				$commentsObj[$id] = KomentoCommentHelper::process( $commentsObj[$id], $admin );
			}
		}

		return $commentsObj[$id];
	}

	public static function getCaptcha()
	{
		return self::getHelper( 'Captcha' )->getInstance();
	}

	/**
	 * A model to get data from a component's content item
	 */
	public static function loadApplication( $component )
	{
		static $instances = null;

		if( is_numeric($instances) )
		{
			$instances = array();
		}

		$component = preg_replace('/[^A-Z0-9_\.-]/i', '', $component);

		if( empty($instances[$component]) )
		{
			// Load from component's folder
			$file = JPATH_ROOT . DS . 'components' . DS . $component . DS . 'komento_helper.php';
			if( !JFile::exists($file) )
			{
				// Load from Komento's plugin folder
				$file = KOMENTO_ROOT . DS . 'komento_plugins' . DS . $component . '.php';
				if ( !JFile::exists($file) )
				{
					return false;
				}
			}

			require_once( KOMENTO_ROOT . DS . 'komento_plugins' . DS . 'abstract.php' );
			require_once( $file );

			// Load the class
			$className = 'Komento' . ucfirst( strtolower( preg_replace('/[^A-Z0-9]/i', '', $component) ) );
			$classObject = new $className( $component );

			if( !($classObject instanceof KomentoExtension) )
			{
				return false;
			}

			$files	= $classObject->getIncludedFiles();

			if (!empty($files))
			{
				foreach ($files as $file) {
					include_once( $file );
				}
			}

			$instances[$component] = $classObject;
		}

		return $instances[$component];
	}

	/**
	 * Prerequisites check, right after an event is triggered.
	 *
	 * @param	$plugin			string
	 * @param	$eventTrigger	string
	 * @param	$extension		string
	 * @param	$context		string
	 * @return 	boolean
	 */
	public static function onAfterEventTriggered( $plugin, $eventTrigger, $extension, $context, $article, $params )
	{
		// 1. modules check
		// 2. component check
		// 3. trigger check

		// exception to com_k2 module
		if( $eventTrigger != 'onK2CommentsCounter' )
		{

			/**
			 * modules check, generally, don't run komento within modules
			 */
			if( $context && stristr( $context , 'mod_' ) !== false )
			{
				return false;
			}

			if( $params instanceof JRegistry && $params->exists('moduleclass_sfx') ) //cache,count
			{
				return false;
			}

		}


		/**
		 * check with the current extension
		 */
		if( !$extension )
		{
			return false;
		}

		$config = Komento::getConfig( $extension );

		if( !$config->get( 'enable_komento' ) )
		{
			// Komento is disabled
			return false;
		}

		$application = Komento::loadApplication( $extension );


		/**
		 * check is this view, are we triggering the correct trigger
		 */
		if( $application->isEntryView() && $config->get('entryEventTrigger') )
		{
			return self::_eventTriggerCompareHelper( $eventTrigger, $config->get('entryEventTrigger') );
		}
		elseif( $application->isListingView() && $config->get('listingEventTrigger') )
		{
			return self::_eventTriggerCompareHelper( $eventTrigger, $config->get('listingEventTrigger') );
		}
		else
		{
			return self::_eventTriggerCompareHelper( $eventTrigger, $application->getEventTrigger() );
		}


		return true;
	}

	private static function _eventTriggerCompareHelper( $needle, $haystack )
	{
		if( empty($haystack) )
		{
			return false;
		}
		elseif( is_array($haystack) )
		{
			return in_array($needle, $haystack);
		}
		elseif( is_string($haystack) )
		{
			return $needle == $haystack;
		}
		else
		{
			return true;
		}
	}

	/**
	 * This is the heart of Komento that does magic
	 *
	 * @param	$component	string
	 * @param	$article	object
	 * @param	$options	array
	 * @return null
	 */
	public static function commentify( $component, &$article, $options = array() )
	{
		$eventTrigger	= '';
		$context		= '';
		$params			= array();
		$page			= 0;

		if( array_key_exists('trigger', $options) )
		{
			$eventTrigger = $options[ 'trigger' ];
		}
		if( array_key_exists('context', $options) )
		{
			$context = $options[ 'context' ];
		}
		if( array_key_exists('params', $options) )
		{
			$params = $options[ 'params' ];
		}
		if( array_key_exists('page', $options) )
		{
			$page = $options[ 'page' ];
		}

		// @task: set the component
		self::setCurrentComponent($component);

		// @task: checking the config
		$config		= self::getConfig( $component );
		if( !$config->get('enable_komento') )
		{
			return false;
		}

		// @task: prepare data and checking on plugin level
		$application = Komento::loadApplication( $component );

		// @trigger: onBeforeLoad
		// we do this checking before load because in some cases,
		// article is not an object and the article id might be missing.
		if( !$application->onBeforeLoad( $eventTrigger, $context, $article, $params, $page, $options ) )
		{
			return false;
		}

		// @task: process in-content parameters
		self::processParameter( $article, $options );

		// terminate if it's disabled
		if( $options['disable'] )
		{
			return false;
		}

		// @task: loading article infomation with defined get methods
		if( !$application->load( $article->id ) )
		{
			return false;
		}

		$config			= Komento::getConfig( $application->getComponentName() );

		// If enabled flag exists, bypass category check
		if( array_key_exists('enable', $options ) && !$options['enable'] )
		{
			// @task: category access check
			$categories		= $config->get( 'allowed_categories' );

			// no categories mode
			switch( $config->get( 'allowed_categories_mode' ) )
			{
				// selected categories
				case 1:
					if( empty( $categories ) )
					{
						return false;
					}
					else
					{
						// @task: For some reason $article->catid might not be set. If it it's not set, just return false.
						$catid	= $application->getCategoryId();

						if( !$catid )
						{
							if( !$application->onRollBack( $eventTrigger, $context, $article, $params, $page, $options ) )
							{
								// raise error
							}
							return false;
						}

						$categories	= explode( ',' , $categories );

						if( !in_array( $catid , $categories ) )
						{
							if( !$application->onRollBack( $eventTrigger, $context, $article, $params, $page, $options ) )
							{
								// raise error
							}

							return false;
						}
					}
					break;

				// except selected categories
				case 2:
					if( !empty( $categories ) )
					{
						// @task: For some reason $article->catid might not be set. If it it's not set, just return false.
						$catid	= $application->getCategoryId();

						if( !$catid )
						{
							if( !$application->onRollBack( $eventTrigger, $context, $article, $params, $page, $options ) )
							{
								// raise error
							}
							return false;
						}

						$categories	= explode( ',' , $categories );

						if( in_array( $catid , $categories ) )
						{
							if( !$application->onRollBack( $eventTrigger, $context, $article, $params, $page, $options ) )
							{
								// raise error
							}
							return false;
						}
					}
					break;

				// no categories
				case 3:
					return false;
					break;

				// all categories
				case 0:
				default:
					break;
			}
		}

		// @trigger: onAfterLoad
		// Now the article with id has been loaded.
		if( !$application->onAfterLoad( $eventTrigger, $context, $article, $params, $page, $options ) )
		{
			return false;
		}

		// @task: send mail on page load
		if( $config->get( 'notification_sendmailonpageload' ) )
		{
			self::getMailQueue()->sendOnPageLoad();
		}

		// @task: load necessary css and javascript files.
		self::getHelper( 'Document' )->loadHeaders();


		/**********************************************/
		// Run Komento!

		$commentsModel	= Komento::getModel( 'comments' );

		$comments		= '';
		$return			= false;

		$commentCount	= $commentsModel->getCount( $component, $article->id );

		if( $application->isListingView() )
		{
			$html = '';

			if( !array_key_exists('skipBar', $options) )
			{
				$theme	= Komento::getTheme();
				$theme->set( 'commentCount'		, $commentCount );
				$theme->set( 'componentHelper'	, $application );
				$theme->set( 'component', $component );
				$theme->set( 'article', $article );
				$html	= $theme->fetch('comment/bar.php');
			}

			$return	= $application->onExecute( $article, $html, 'listing', $options );
		}

		if( $application->isEntryView() )
		{
			// check for escaped_fragment (google ajax crawler)
			$fragment = JRequest::getVar( '_escaped_fragment_', '' );

			if( $fragment != '' )
			{
				$tmp = explode( '=', $fragment );

				$fragment = array( $tmp[0] => $tmp[1] );

				if( isset( $fragment['kmt-start'] ) )
				{
					$options['limitstart'] = $fragment['kmt-start'];
				}
			}
			else
			{
				// Sort comments oldest first by default.
				if (!isset($options['sort']))
				{
					$options['sort'] = JRequest::getVar('kmt-sort', 'default');
				}

				if( $config->get( 'load_previous' ) )
				{
					$options['limitstart'] = $commentCount - $config->get( 'max_comments_per_page' );
					if( $options['limitstart'] < 0 )
					{
						$options['limitstart'] = 0;
					}
				}
			}

			$profile		= Komento::getProfile();

			if( $profile->allow( 'read_comment' ) )
			{
				$comments	= $commentsModel->getComments( $component, $article->id, $options );
			}

			$theme	= Komento::getTheme();
			$theme->set( 'component', $component );
			$theme->set( 'cid', $article->id );
			$theme->set( 'comments', $comments );
			$theme->set( 'options', $options );
			$theme->set( 'componentHelper', $application );
			$theme->set( 'application', $application );
			$theme->set( 'commentCount', $commentCount );
			$contentLink = $application->getContentPermalink(array('external' => true));

			$theme->set( 'contentLink', $contentLink );

			$html	= $theme->fetch('comment/box.php');

			$html .= '<div style="text-align: center; padding: 20px 0;"><a href="http://stackideas.com">' . JText::_( 'COM_KOMENTO_POWERED_BY_KOMENTO' ) . '</a></div>';

			// free version powered by link append (for reference only)
			// $html	.= '<div style="text-align: center; padding: 20px 0;"><a href="http://stackideas.com">' . JText::_( 'COM_KOMENTO_POWERED_BY_KOMENTO' ) . '</a></div>';

			$return	= $application->onExecute( $article, $html, 'entry', $options );
		}

		return $return;
	}

	public static function processParameter( &$article, &$options )
	{
		// Retrieve user parameters e.g.
		// {KomentoDisable}, {KomentoLock}

		if( is_string($article) )
		{
			$text		= &$article;
		}
		elseif( is_object($article) )
		{
			// adjust to standard format
			if( !property_exists($article, 'introtext') )
			{
				$article->introtext = '';
			}

			if( !property_exists($article, 'text') )
			{
				$article->text = '';
			}

			$introtext	= &$article->introtext;
			$text		= &$article->text;
		}
		else
		{
			return;
		}

		$options['disable'] = ( JString::strpos($introtext, '{KomentoDisable}') !== false || JString::strpos($text, '{KomentoDisable}') !== false );
		$options['enable'] = ( JString::strpos($introtext, '{KomentoEnable}') !== false || JString::strpos($text, '{KomentoEnable}') !== false );
		$options['lock'] = ( JString::strpos($introtext, '{KomentoLock}') !== false || JString::strpos($text, '{KomentoLock}') !== false );

		// Remove in-content parameters
		if (!empty($introtext))
		{
			$introtext	= JString::str_ireplace( '{KomentoDisable}', '', $introtext );
			$introtext	= JString::str_ireplace( '{KomentoEnable}', '', $introtext );
			$introtext	= JString::str_ireplace( '{KomentoLock}', '', $introtext );
		}

		if (!empty($text))
		{
			$text		= JString::str_ireplace( '{KomentoDisable}', '', $text );
			$text		= JString::str_ireplace( '{KomentoEnable}', '', $text );
			$text		= JString::str_ireplace( '{KomentoLock}', '', $text );
		}
	}

	public static function mergeOptions( $defaults, $options )
	{
		$options	= array_merge($defaults, $options);
		foreach ($options as $key => $value)
		{
			if( !array_key_exists($key, $defaults) )
				unset($options[$key]);
		}

		return $options;
	}

	public static function setMessageQueue($message, $type = 'info')
	{
		$session 	= JFactory::getSession();

		$msgObj = new stdClass();
		$msgObj->message    = $message;
		$msgObj->type       = strtolower($type);

		//save messsage into session
		$session->set('komento.message.queue', $msgObj, 'KOMENTO.MESSAGE');
	}

	public static function getMessageQueue()
	{
		$session 	= JFactory::getSession();
		$msgObj 	= $session->get('komento.message.queue', null, 'KOMENTO.MESSAGE');

		//clear messsage into session
		$session->set('komento.message.queue', null, 'KOMENTO.MESSAGE');

		return $msgObj;
	}

	public static function getMailQueue()
	{
		static $mailq = false;

		if( !$mailq )
		{
			require_once( KOMENTO_CLASSES . DS . 'mailqueue.php' );

			$mailq	= new KomentoMailQueue();
		}
		return $mailq;
	}

	// deprecated. moved to activity helper
	public static function addActivity( $type, $comment_id, $uid )
	{
		return Komento::getHelper( 'activity' )->addActivity( $type, $comment_id, $uid );
	}

	public static function client()
	{
		static $client	= null;

		if( !$client )
		{
			$app = JFactory::getApplication();
			$client = $app->getName(); // return site or administrator
		}

		return $client;
	}

	/**
	 * Method to get Joomla's version
	 *
	 * @return	string
	 */
	public static function joomlaVersion()
	{
		static $version = null;

		if (!$version)
		{
			require_once( KOMENTO_HELPERS . DS . 'version.php' );
			$version = KomentoVersionHelper::getJoomlaVersion();
		}

		return $version;
	}

	/**
	 * Method to get installed Komento version
	 *
	 * @return	string
	 */
	public static function komentoVersion()
	{
		static $version = null;

		if (!$version)
		{
			require_once( KOMENTO_HELPERS . DS . 'version.php' );
			$version = KomentoVersionHelper::getLocalVersion();
		}

		return $version;
	}

	public static function getCurrentComponent()
	{
		return self::$component;
	}

	public static function setCurrentComponent( $component = 'com_component' )
	{
		$component	= preg_replace('/[^A-Z0-9_\.-]/i', '', $component);

		self::$component = $component;

		return self::$component;
	}

	/**
	 * Used in J1.6!. To retrieve list of superadmin users's id.
	 * array
	 */
	public static function getSAUsersIds()
	{
		$saGroup	= array();
		$db = JFactory::getDBO();

		if( Komento::joomlaVersion() >= '1.6' )
		{
			$query	= 'SELECT a.`id`, a.`title`';
			$query	.= ' FROM `#__usergroups` AS a';
			$query	.= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
			$query	.= ' GROUP BY a.id';
			$query	.= ' ORDER BY a.lft ASC';

			$db->setQuery($query);
			$result = $db->loadObjectList();

			foreach($result as $group)
			{
				if(JAccess::checkGroup($group->id, 'core.admin'))
				{
					$saGroup[]	= $group;
				}
			}
		}
		else
		{
			$tmp = new stdClass();
			$tmp->id = 25;
			$tmp->title = 'Super Administrator';
			$saGroup[] = $tmp;
		}


		//now we got all the SA groups. Time to get the users
		$saUsers	= array();
		if(count($saGroup) > 0)
		{
			foreach($saGroup as $sag)
			{
				$userArr = array();

				if( Komento::joomlaVersion() >= '1.6' )
				{
					$userArr	= JAccess::getUsersByGroup($sag->id);
				}
				else
				{
					$query = 'SELECT `id` FROM `#__users` WHERE `gid` = ' . $sag->id;
					$db->setQuery($query);
					$userArr = $db->loadResultArray();
				}

				if(count($userArr) > 0)
				{
					foreach($userArr as $user)
					{
						$saUsers[]	= $user;
					}
				}
			}
		}

		return $saUsers;
	}

	public function getDefaultSAIds()
	{
		$saUserId	= '62';

		if(Komento::joomlaVersion() >= '1.6')
		{
			$saUsers	= self::getSAUsersIds();
			$saUserId	= $saUsers[0];
		}

		return $saUserId;
	}

	/**
	 * Method to return a list of user groups mapped to a user. The returned list can optionally hold
	 * only the groups explicitly mapped to the user or all groups both explicitly mapped and inherited
	 * by the user.
	 *
	 * @param   integer  $userId     Id of the user for which to get the list of groups.
	 * @param   boolean  $recursive  True to include inherited user groups.
	 *
	 * @return  array    List of user group ids to which the user is mapped.
	 */
	public static function getGroupsByUser( $userId, $recursive = true )
	{
		if( Komento::joomlaVersion() >= '1.6' )
		{
			return JAccess::getGroupsByUser( $userId, $recursive );
		}
		else
		{
			$user = JFactory::getUser( $userId );

			if( $user->gid == 0 )
			{
				return array( 29 );
			}

			return array( $user->gid );
		}
	}

	// Method to liase legacy functions
	public static function _()
	{
		$class = 'KomentoLegacy16';
		if( Komento::joomlaVersion() == '1.5' )
		{
			$class = 'KomentoLegacy15';
		}

		$legacy = Komento::getClass( 'legacy', $class );
		$args = func_get_args();
		$function = array_shift( $args );

		if( strstr( $function, '::' ) ) // strstr( $function, '->' )
		{
			$function = str_replace( '::', '_', $function );
		}

		if( is_callable( array( $class, $function ) ) )
		{
			return call_user_func_array( array( $class, $function ), $args );
		}
		else
		{
			return false;
		}
	}

	// Method to route standard links (bugged)
	public static function route( $link )
	{
		if( JPATH_BASE == JPATH_ADMINISTRATOR )
		{
			JFactory::$application = JApplication::getInstance('site');
		}

		$link = JRoute::_( $link );

		if( JPATH_BASE == JPATH_ADMINISTRATOR )
		{
			$link = str_ireplace( '/administrator/', '/', $link );
			JFactory::$application = JApplication::getInstance('administrator');
		}

		return $link;
	}

	// deprecated. use Komento::getUsergroups instead
	public static function getJoomlaUserGroups( $cid = '' )
	{
		/*$db = JFactory::getDBO();

		if(Komento::joomlaVersion() >= '1.6')
		{
			$query = 'SELECT a.id, a.title AS `name`, COUNT(DISTINCT b.id) AS level';
			$query .= ' , GROUP_CONCAT(b.id SEPARATOR \',\') AS parents';
			$query .= ' FROM #__usergroups AS a';
			$query .= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
		}
		else
		{
			$query	= 'SELECT `id`, `name` as title, 0 as `level` FROM ' . $db->nameQuote('#__core_acl_aro_groups') . ' AS a';
		}

		// condition
		$where  = array();

		// we need to filter out the ROOT and USER dummy records.
		if(Komento::joomlaVersion() < '1.6')
		{
			$where[] = 'a.`id` > 17 AND a.`id` < 26 OR a.`id` = 29';
		}

		if( !empty( $cid ) )
		{
			$where[] = ' a.`id` = ' . $db->quote($cid);
		}
		$where = ( count( $where ) ? ' WHERE ' .implode( ' AND ', $where ) : '' );

		$query  .= $where;

		// grouping and ordering
		if( Komento::joomlaVersion() >= '1.6' )
		{
			$query	.= ' GROUP BY a.id';
			$query	.= ' ORDER BY a.lft ASC';
		}
		else
		{
			$query 	.= ' ORDER BY a.lft';
		}

		$db->setQuery( $query );
		$result = $db->loadObjectList();*/
		$result = Komento::getUsergroups();

		return $result;
	}

	public static function getJoomlaUserGroupsSelectionBox( $selected = array(), $key = 'joomlaUsergroup')
	{
		if( !is_array( $selected ) )
		{
			$selected	= explode( ',' , $selected );
		}

		$groups = Komento::getUsergroups();

		$selections = array();

		foreach( $groups as $group )
		{
			$selections[] = JHtml::_( 'select.option', $group->id, str_repeat( '|—', $group->depth ) . $group->title );
		}

		$selection = JHtml::_( 'select.genericlist', $selections, $key, 'multiple="multiple" size="10" style="height: auto !important;"', 'value', 'text', $selected );

		return $selection;
	}

	public static function getUserGids( $userId = '' )
	{
		$user   = '';

		if( empty($userId) )
		{
			$user   = JFactory::getUser();
		}
		else
		{
			$user   = JFactory::getUser($userId);
		}

		if( Komento::joomlaVersion() >= '1.6' )
		{
			$groupIds	= $user->groups;

			if( !$userId )
			{
				$groupIds[1]	= '1';
			}

			$grpId 		= array();

			foreach($groupIds as $key => $val)
			{
				$grpId[] = $val;
			}

			return $grpId;
		}
		else
		{
			// Joomla 1.5 uses 29 as public by default
			if( $user->gid == 0 )
			{
				return array( 29 );
			}
			return array( $user->gid );
		}
	}

	public static function getUniqueFileName($originalFilename, $path)
	{
		$ext			= JFile::getExt($originalFilename);
		$ext			= $ext ? '.'.$ext : '';
		$uniqueFilename	= JFile::stripExt($originalFilename);

		$i = 1;

		while( JFile::exists($path.DS.$uniqueFilename.$ext) )
		{
			// $uniqueFilename	= JFile::stripExt($originalFilename) . '-' . $i;
			$uniqueFilename	= JFile::stripExt($originalFilename) . '_' . $i . '_' . JFactory::getDate()->toFormat( "%Y%m%d-%H%M%S" );
			$i++;
		}

		//remove the space into '-'
		$uniqueFilename = str_ireplace(' ', '-', $uniqueFilename);

		return $uniqueFilename.$ext;
	}

	public static function getUsergroupById( $id )
	{
		$db		= JFactory::getDbo();
		$query	= 'SELECT ' . $db->nameQuote( 'title' )
				. ' FROM ' . $db->nameQuote( '#__usergroups' )
				. ' WHERE ' . $db->nameQuote( 'id' ) . ' = ' .$db->quote( $id );

		if( Komento::joomlaVersion() == '1.5' )
		{
			$query	= 'SELECT `id`, `name` as `title`, 0 as `depth` FROM ' . $db->nameQuote('#__core_acl_aro_groups');
			$query .= ' WHERE ' . $db->nameQuote( 'id' ) . ' = ' .$db->quote( $id );
		}

		$db->setQuery( $query );
		return $db->loadResult();
	}

	public static function getUsergroups()
	{
		$db = JFactory::getDBO();

		$query  = 'SELECT x.*, COUNT(y.id) - 1 AS depth FROM ' . $db->nameQuote( '#__usergroups' ) . ' AS x';
		$query .= ' INNER JOIN ' . $db->nameQuote( '#__usergroups' ) . ' AS y ON x.lft BETWEEN y.lft AND y.rgt';
		$query .= ' GROUP BY x.id';
		$query .= ' ORDER BY x.lft';

		if( Komento::joomlaVersion() == '1.5' )
		{
			$query	= 'SELECT `id`, `name` as `title`, 0 as `depth` FROM ' . $db->nameQuote('#__core_acl_aro_groups');
			$query .= ' WHERE id > 17 AND id < 26 OR id = 29';
			$query .= ' ORDER BY lft';
		}

		$db->setQuery( $query );

		return $db->loadObjectList();
	}

	public static function addJomSocialPoint( $action , $userId = 0 )
	{
		$my	= JFactory::getUser();

		if( !empty( $userId ) )
		{
			$my	= JFactory::getUser( $userId );
		}

		if( $my->id != 0 )
		{
			$jsUserPoint	= JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'userpoints.php';

			if( JFile::exists( $jsUserPoint ) )
			{
				require_once( $jsUserPoint );
				CUserPoints::assignPoint( $action , $my->id );
			}
		}
		return true;
	}

	public static function addAUP( $plugin_function = '', $referrerid = '', $keyreference = '', $datareference = '' )
	{
		$my	= JFactory::getUser();

		if( !empty( $referrerid ) )
		{
			$my	= JFactory::getUser( $referrerid );
		}

		if( $my->id != 0 )
		{
			$aup	= JPATH_ROOT . DS . 'components' . DS . 'com_alphauserpoints' . DS . 'helper.php';
			if ( JFile::exists( $aup ) )
			{
				require_once( $aup );
				AlphaUserPointsHelper::newpoints( $plugin_function, AlphaUserPointsHelper::getAnyUserReferreID( $referrerid ), $keyreference, $datareference );
			}
		}
	}

	public static function debugSql( $query )
	{
		return nl2br(str_replace('#__', 'jos_', $query));
	}
}
