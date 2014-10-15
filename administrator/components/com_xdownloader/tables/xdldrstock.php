<?php
/*------------------------------------------------------------------------
# com_xdownloader - xDownloader alpha component
# ------------------------------------------------------------------------
# author    Dmitri Gorbunov
# copyright Copyright (C) 2012 xrbyte.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.xrbyte.com
# Technical Support:  Forum - http://www.xrbyte.com/forum
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');

class TableXdldrStock extends JTable {

	var $id = null;
	var $menu = null;
	var $guest = null;
	var $user_id = null;
	var $user_group = null;
	var $link_groups = null;
	var $user_alias = null;
	var $user_ip = null;
	var $ip_location = null;
	var $title = null;
	var $filename = null;
	var $filepath = null;
	var $dwn_date = null;
	
    function  __construct(&$db) {
        parent::__construct('#__xdldr_stock', 'id', $db);
    }
}
?>