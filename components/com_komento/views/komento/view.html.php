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

defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

class KomentoViewKomento extends JView
{
	public function display( $tpl = null )
	{
		return;
	}

	function getModel( $name = null )
	{
		return Komento::getModel( $name );
	}

	function getView( $name , $tmpl = 'html')
	{
		static $view = array();

		if( !isset( $view[ $name ] ) )
		{
			$file	= JString::strtolower( $name );

			$path	= KOMENTO_ROOT . DS . 'views' . DS . $file . DS . 'view.'. $tmpl . '.php';

			if ( JFolder::exists( $path ))
			{
				JError::raiseWarning( 0, 'View file not found.' );
			}

			$viewClass		= 'KomentoView' . ucfirst( $name );

			if( !class_exists( $viewClass ) )
				require_once( $path );


			$view[ $name ] = new $viewClass();
		}

		return $view[ $name ];
	}
}
