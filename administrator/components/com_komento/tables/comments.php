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
 * Comments table
 *
 * @package     Komento
 * @subpackage  Table
 * @since       1.0
 */
class KomentoComments extends JTable
{
	/**
	 * The id of the comment
	 * @var int
	 */
	var $id 			= null;

	/**
	 * The related component name
	 * @var string
	 */
	var $component		= null;

	/**
	 * The unique content id
	 * @var int
	 */
	var $cid			= null;

	/**
	 * The comment
	 * @var string
	 */
	var $comment		= null;

	/**
	 * The name of the commenter
	 * @var string
	 */
	var $name			= null;

	/**
	 * The title of the comment
	 * optional
	 * @var string
	 */
	var $title			= null;

	/**
	 * The email of the commenter
	 * optional
	 * @var string
	 */
	var $email			= null;

	/**
	 * The website of the commenter
	 * optional
	 * @var string
	 */
	var $url			= null;

	/**
	 * The ip of the visitor
	 * optional
	 * @var string
	 */
	var $ip				= null;

	/**
	 * The author of the comment
	 * optional
	 * @var int
	 */

	var $created_by		= null;


	/**
	 * Created datetime of the comment
	 * @var datetime
	 */

	var $created		= null;

	/**
	 * modified datetime of the comment
	 * optional
	 * @var datetime
	 */

	var $modified_by	=null;

	/**
	 * last modified user
	 * optional
	 * @var int
	 */

	var $modified		= null;

	/**
	 * deleted datetime of the comment
	 * optional
	 * @var datetime
	 */

	var $deleted_by		=null;

	/**
	 * user that deleted comment
	 * optional
	 * @var int
	 */

	var $deleted		= null;

	/**
	 * flag deleted/inappropriate/report comment
	 * @var int
	 */

	var $flag			= null;

	/**
	 * Tag publishing status
	 * @var int
	 */

	var $published		= null;

	/**
	 * comment publish datetime
	 * optional
	 * @var datetime
	 */
	var $publish_up		= null;

	/**
	 * Comment un-publish datetime
	 * optional
	 * @var datetime
	 */
	var $publish_down	= null;

	/**
	 * Comment sticked
	 * @var int
	 */
	var $sticked		= null;

	/**
	 * Comment notification sent
	 * @var int
	 */
	var $sent			= null;

	/**
	 * Comment's parent_id
	 * @var int
	 */
	var $parent_id		= null;

	/**
	 * Comment lft - used in threaded comment
	 * @var int
	 */
	var $lft			= null;

	/**
	 * Comment rgt - used in threaded comment
	 * @var int
	 */
	var $rgt			= null;

	/**
	 * Comment latitude - for location
	 * @var int
	 */
	var $latitude		= null;

	/**
	 * Comment longitude - for location
	 * @var int
	 */
	var $longitude		= null;

	/**
	 * Comment address - for location
	 * @var string
	 */
	var $address		= null;


	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__komento_comments' , 'id' , $db );
	}

	public function store( $updateNulls = false )
	{
		return parent::store( $updateNulls );
	}

	public function updateSent()
	{
		if(! empty($this->id))
		{
			$query  = 'UPDATE `#__komento_comments` SET `sent` = 1 WHERE `id` = ' . $this->_db->Quote($this->id);

			$this->_db->setQuery($query);
			$this->_db->query();
		}

		return true;
	}
}
