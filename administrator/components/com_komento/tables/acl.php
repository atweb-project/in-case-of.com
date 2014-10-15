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
class KomentoAcl extends JTable
{

	public $id			= null;
	public $cid			= null;
	public $component	= null;
	public $type		= null;
	public $rules		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $this->_db
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__komento_acl' , 'id' , $db );
	}

	public function compositeLoad( $cid, $type, $component, $reset = true )
	{
		if ($reset)
		{
			$this->reset();
		}

		$query	= 'SELECT * FROM ' . $this->_db->nameQuote( '#__komento_acl' )
				. ' WHERE ' . $this->_db->nameQuote( 'component' ) . ' = ' . $this->_db->quote( $component )
				. ' AND ' . $this->_db->nameQuote( 'type' ) . ' = ' . $this->_db->quote( $type )
				. ' AND ' . $this->_db->nameQuote( 'cid' ) . ' = ' . $this->_db->quote( $cid );
		$this->_db->setQuery( $query );
		$row = $this->_db->loadAssoc();

		// Check for a database error.
		if ($this->_db->getErrorNum() || empty($row))
		{
			return false;
		}

		return $this->bind($row);
	}

	/**
	 *  Method to save the configuration
	 **/
	public function store( $updateNulls = false )
	{
		$query	= 'SELECT COUNT(*) FROM ' . $this->_db->nameQuote(	 '#__komento_acl')
				. ' WHERE ' . $this->_db->nameQuote( 'component' ) . '=' . $this->_db->quote( $this->component )
				. ' AND ' . $this->_db->nameQuote( 'type' ) . ' = ' . $this->_db->quote( $this->type )
				. ' AND ' . $this->_db->nameQuote( 'cid' ) . ' = ' . $this->_db->quote( $this->cid );
		$this->_db->setQuery( $query );

		$exists	= ( $this->_db->loadResult() > 0 ) ? true : false;

		if( $exists )
		{
			return $this->_db->updateObject( '#__komento_acl' , $this , 'id', $updateNulls );
		}

		return $this->_db->insertObject( '#__komento_acl' , $this );
	}
}
