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
 * Uploads Table
 *
 * @package     Komento
 * @subpackage  Table
 * @since       1.0
 */
class KomentoUploads extends JTable
{
	var $id			= null;
	var $uid		= null;
	var $filename	= null;
	var $hashname	= null;
	var $path		= null;
	var $created	= null;
	var $created_by	= null;
	var $published	= null;
	var $mime		= null;
	var $size		= null;

	public function __construct( &$db )
	{
		parent::__construct( '#__komento_uploads' , 'id' , $db );
	}

	public function getType()
	{
		$type = explode("/", $this->mime);

		return $type[0];
	}

	public function getSubtype()
	{
		$type = explode("/", $this->mime);

		return $type[1];
	}

	public function upload()
	{
		if( empty( $this->hashname ) )
		{
			$this->hashname = $this->hash();
		}

		return $this->store();
	}

	public function rollback()
	{
		$this->delete();
	}

	private function hash()
	{
		return md5( $this->filename . JFactory::getDate()->toMySQL() );
	}
}
