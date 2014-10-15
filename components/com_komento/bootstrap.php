<?php

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.path' );
jimport( 'joomla.html.parameter' );
jimport( 'joomla.access.access' );

require_once( dirname(__FILE__) . DS . 'constants.php' );
require_once( KOMENTO_HELPERS . DS . 'version.php' );
require_once( KOMENTO_HELPERS . DS . 'document.php' );
require_once( KOMENTO_HELPERS . DS . 'helper.php' );
require_once( KOMENTO_HELPERS . DS . 'router.php' );
require_once( KOMENTO_CLASSES . DS . 'comment.php' );

// Load language here
// initially language is loaded in content plugin
// for custom integration that doesn't go through plugin, language is not loaded
// hence, language should be loaded in bootstrap
JFactory::getLanguage()->load( 'com_komento', JPATH_ROOT, null, false, false );

try{@include_once('FirePHPCore/fb.php');}catch(Exception $e){}
