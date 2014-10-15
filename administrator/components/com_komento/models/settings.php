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

jimport('joomla.application.component.model');

class KomentoModelSettings extends JModel
{
	public function save( $data )
	{
		$component	= $data['component'];
		$component	= preg_replace('/[^A-Z0-9_\.-]/i', '', $component);
		$component	= JString::strtolower( JString::trim($component) );
		unset($data['component']);

		$config	= Komento::getTable( 'Configs' );
		$config->load( $component );
		$config->component	= $component;

		$registry = Komento::_( 'loadRegistry', 'komento', $config->params );

		foreach( $data as $name => $value )
		{
			if( is_array( $value ) )
			{
				$tmpValue	= '';

				for( $i = 0; $i < count( $value ); $i++ )
				{
					$tmpValue	.= $value[ $i ];

					if( next( $value ) !== false )
					{
						$tmpValue	.= ',';
					}
				}

				$registry->setValue( 'komento.' . $name , $tmpValue );
			}
			else
			{
				$registry->setValue( 'komento.' . $name , $value );
			}

		}

		// Get the complete INI string
		$config->params	= $registry->toString( 'INI' );

		// remove environment keys/values
		$config->params = str_replace( "komento_environment=\"production\"\n", '', $config->params);
		$config->params = str_replace( "komento_environment=\"development\"\n", '', $config->params);
		$config->params = str_replace( "foundry_environment=\"production\"\n", '', $config->params);
		$config->params = str_replace( "foundry_environment=\"development\"\n", '', $config->params);

		// Save it
		if(!$config->store($component) )
		{
			return false;
		}

		return true;
	}
}
