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
class KomentoSubscription extends JTable
{
	var $id			= null;
	var $type		= null;
	var $component	= null;
	var $cid		= null;
	var $userid		= null;
	var $fullname	= null;
	var $email		= null;
	var $created	= null;
	var $published	= null;

	public function __construct( &$db )
	{
		parent::__construct( '#__komento_subscription' , 'id' , $db );
	}
}
