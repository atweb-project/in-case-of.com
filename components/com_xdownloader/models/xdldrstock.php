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

jimport( 'joomla.application.component.model' );

class ModelXdldrStock extends JModel {

	function __construct($config = array()) {
		parent::__construct($config);		
	}
	
    public function  getTable($type = 'XdldrStock', $prefix = 'Table', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }
    
    public function insertData($data=array()) {
    	$table = $this->getTable();
    	if($table->check()) {
    		if($table->bind($data)) {
    			return $table->store();
    		}
    	}
    	return false;
    }
}
?>