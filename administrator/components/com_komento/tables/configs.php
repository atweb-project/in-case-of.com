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

/**
 * Configuration table
 *
 * @package     Komento
 * @subpackage  Table
 * @since       1.0
 */
class KomentoConfigs extends JTable
{
	/**
	 * The key of the current config
	 *
	 * @var string
	 */
	public $component = null;

	/**
	 * Raw parameters values
	 *
	 * @var string
	 */
	public $params	= null;


	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $this->_db
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__komento_configs' , 'component' , $db );
	}

	/**
	 *  Method to save the configuration
	 **/
	public function store( $key = 'com_content' )
	{
		$key	= $key ? $key : $this->component;

		$query	= 'SELECT COUNT(*) FROM ' . $this->_db->nameQuote( '#__komento_configs') . ' '
				. 'WHERE ' . $this->_db->nameQuote( 'component' ) . '=' . $this->_db->Quote( $key );
		$this->_db->setQuery( $query );

		$exists	= ( $this->_db->loadResult() > 0 ) ? true : false;

		$data				= new stdClass();
		$data->component	= $this->component;
		$data->params		= trim( $this->params );

		if( $exists )
		{
			return $this->_db->updateObject( '#__komento_configs' , $data , 'component' );
		}

		return $this->_db->insertObject( '#__komento_configs' , $data );
	}
}
