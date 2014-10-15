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

class KomentoModelProfile extends JModel
{
	public function exists( $id )
	{
		$query	= 'SELECT COUNT(*) FROM ' . $this->_db->nameQuote( '#__users' )
				. ' WHERE ' . $this->_db->nameQuote( 'id' ) . '=' . $this->_db->quote( $id )
				. ' AND ' . $this->_db->nameQuote( 'block' ) . '=' . $this->_db->quote( 0 );
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}
