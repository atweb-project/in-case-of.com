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

// Root path
define( 'KOMENTO_ROOT', JPATH_ROOT . DS . 'components' . DS . 'com_komento' );

// Backend path
define( 'KOMENTO_ADMIN_ROOT', JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_komento' );

// Assets path
define( 'KOMENTO_ASSETS', KOMENTO_ROOT . DS . 'assets' );

// Helper path
define( 'KOMENTO_HELPERS', KOMENTO_ROOT . DS . 'helpers' );

// Controllers path
define( 'KOMENTO_CONTROLLERS', KOMENTO_ROOT . DS . 'controllers' );

// Models path
define( 'KOMENTO_MODELS', KOMENTO_ROOT . DS . 'models' );

// Libraries path
define( 'KOMENTO_CLASSES', KOMENTO_ROOT . DS . 'classes' );

// Tables path
define( 'KOMENTO_TABLES', KOMENTO_ADMIN_ROOT . DS . 'tables' );

// Themes path
define( 'KOMENTO_THEMES', KOMENTO_ROOT . DS . 'themes' );

// Media path
define( 'KOMENTO_MEDIA_ROOT', JPATH_ROOT . DS . 'media' );

// Komento media path
define( 'KOMENTO_MEDIA', KOMENTO_MEDIA_ROOT . DS . 'com_komento' );

// Foundry path
define( 'KOMENTO_FOUNDRY_ROOT', KOMENTO_MEDIA_ROOT . DS . 'foundry' . DS . '2.1' );

// Foundry bootstrap
define( 'KOMENTO_FOUNDRY_BOOTSTRAP', KOMENTO_FOUNDRY_ROOT . DS . 'joomla' . DS . 'bootstrap.php' );

// JavaScripts path
define( 'KOMENTO_JS_ROOT', KOMENTO_MEDIA . DS . 'js' );

// Scripts path
define( 'KOMENTO_SCRIPTS_ROOT', KOMENTO_MEDIA . DS . 'scripts' );

// Scripts_ path
define( 'KOMENTO_SCRIPTS__ROOT', KOMENTO_MEDIA . DS . 'scripts_' );

// Admistrator path
define( 'KOMENTO_ADMIN', JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_komento' );

// Spinner path
define( 'KOMENTO_SPINNER', KOMENTO_MEDIA . DS . 'images' . DS . 'loader.gif' );

// Uploads root
define( 'KOMENTO_UPLOADS_ROOT', KOMENTO_MEDIA . DS . 'uploads' );

// Plugins path
define( 'KOMENTO_PLUGINS' , KOMENTO_ROOT . DS . 'komento_plugins' );

// Comment statuses
define( 'KOMENTO_COMMENT_UNPUBLISHED', 0 );
define( 'KOMENTO_COMMENT_PUBLISHED', 1 );
define( 'KOMENTO_COMMENT_MODERATE', 2 );

// Comment flags
define( 'KOMENTO_COMMENT_NOFLAG', 0 );
define( 'KOMENTO_COMMENT_SPAM', 1 );
define( 'KOMENTO_COMMENT_OFFENSIVE', 2 );
define( 'KOMENTO_COMMENT_OFFTOPIC', 3 );

//bbcode emoticons path
define ( 'KOMENTO_EMOTICONS_DIR', rtrim( JURI::root() , '/' ) . '/components/com_komento/classes/markitup/sets/bbcode/images/');

// Updates server
define( 'KOMENTO_UPDATES_SERVER', 'stackideas.com' );
