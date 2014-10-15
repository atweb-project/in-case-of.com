<?php
/**
* @package      Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

class KomentoLegacy15
{
	static function loadRegistry( $id, $params )
	{
		$registry = JRegistry::getInstance( $id, $id );
		$registry->loadINI( $params );
		return $registry;
	}

	static function getToken()
	{
		return JUtility::getToken();
	}

	static function JFactory_getConfig( $key, $default = null )
	{
		$jconfig = JFactory::getConfig();
		return $jconfig->getValue( $key, $default );
	}
}

class KomentoLegacy16
{
	static function loadRegistry( $id, $params )
	{
		$registry = JRegistry::getInstance( $id );
		$registry->loadString( $params, 'INI' );
		return $registry;
	}

	static function getToken()
	{
		return JSession::getFormToken();
	}

	static function JFactory_getConfig( $key, $default = null )
	{
		$jconfig = JFactory::getConfig();
		return $jconfig->get( $key, $default );
	}
}
