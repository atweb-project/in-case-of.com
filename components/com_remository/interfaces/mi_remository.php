<?php
/**
 * @version $Id: mi_mysql_query.php 16 2007-07-02 13:29:29Z mic $
 * @package AEC - Account Control Expiration - Subscription component for Joomla! OS CMS
 * @subpackage Micro Integrations - MySQL Query
 * @copyright 2006/2007 Copyright (C) David Deutsch
 * @author David Deutsch <skore@skore.de> & Team AEC - http://www.globalnerd.org
 * @license GNU/GPL v.2 http://www.gnu.org/copyleft/gpl.html
 */

/***
 * This version copyright (c) 2009 by Martin Brampton
 * martin@remository.com
 * http://www.remository.com
 * Modified to correctly integrate with Remository
 */

defined( '_VALID_MOS' ) OR defined('_JEXEC') OR die( 'Direct Access to this location is not allowed.' );

if (!defined('_JOOMLA_15PLUS') AND defined('_JEXEC') AND !defined('_ALIRO_IS_PRESENT')){
	define ('_JOOMLA_15PLUS', 1);
	if(version_compare(JVERSION,'1.6.0','>=')){
		define ('_JOOMLA_16PLUS', 1);
	}
}

// Temporary definitions so this file can be used with earlier Remository releases
if (!defined('_DOWN_AEC_OPTION_A')) DEFINE ('_DOWN_AEC_OPTION_A', 'Just apply group(s) below.');
if (!defined('_DOWN_AEC_OPTION_B')) DEFINE ('_DOWN_AEC_OPTION_B', 'Delete ALL, then apply group(s) below.');
if (!defined('_DOWN_AEC_OPTION_C')) DEFINE ('_DOWN_AEC_OPTION_C', 'Delete Group Set on Application, then apply group(s) below.');

// Find the absolute path in a relatively CMS independent way
if (!defined('_REMOS_ABSOLUTE_PATH')) {
	if (defined('_JOOMLA_15PLUS')) define ('_REMOS_ABSOLUTE_PATH', JPATH_ROOT);
	elseif (class_exists('mosMainFrame') AND method_exists('mosMainFrame', 'getInstance')) {
		define ('_REMOS_ABSOLUTE_PATH', mosMainFrame::getInstance()->getCfg('absolute_path'));
	}
	else {
		global $mainframe;
		define ('_REMOS_ABSOLUTE_PATH', $mainframe->getCfg('absolute_path'));
	}
}

// Load the standard Remository CMS interface layer
require_once(_REMOS_ABSOLUTE_PATH.'/components/com_remository/remository.interface.php');

class mi_remository {
	public $settings = array();
	private $interface = null;
	private $database = null;
	private $authoriser = null;
	private $dbprefix = '';
	
	// This needs review for compatibility with Joomla 1.5 native or Aliro
	// Other methods should be safe
	public function __construct () {
		// Find the $mainframe object in a relatively CMS independent way
		if (!defined('_JOOMLA_15PLUS') AND class_exists('mosMainFrame') AND method_exists('mosMainFrame', 'getInstance')) $mainframe = mosMainFrame::getInstance();
		else global $mainframe;
		// Load the standard Remository CMS interface layer
		$this->interface = remositoryInterface::getInstance();
		// Obtain the standard database object via the interface layhttp://remository.com/forum/er
		$this->database = $this->interface->getDB();
	}
	
	public function Info() {
		return array ('name' => _AEC_MI_NAME_REMOS, 'desc' => _AEC_MI_DESC_REMOS);
	}

	public function checkInstallation() {
		// Check to see whether the AEC Remository MI table exists
		$this->database->setQuery("SHOW TABLES LIKE '#__acctexp_mi_remository'");
		$results = $this->database->loadObjectList();
		return empty($results) ? false : true;
	}

	public function install() {
		// Create the Remository MI table
		$query = 'CREATE TABLE IF NOT EXISTS `#__acctexp_mi_remository` ('
		. '`id` int(11) NOT NULL auto_increment,'
		. '`userid` int(11) NOT NULL,'
		. '`active` int(4) NOT NULL default 1,'
		. '`granted_downloads` int(11) NOT NULL default 0,'
		. '`unlimited_downloads` int(3) NOT NULL default 0,'
		. '`used_downloads` int(11) NOT NULL default 0,'
		. '`params` text NOT NULL,'
		. ' PRIMARY KEY (`id`)'
		. ')'
		;
		$this->database->setQuery( $query );
		$this->database->query();
	}

	public function Settings () {
		$this->includeRemCore();
		$sg = array();
		foreach ($this->authoriser->getAllRoles() as $role) {
			$sg[] = mosHTML::makeOption( $role, $role);
		}

		$del_opts[0] = mosHTML::makeOption ( "No", _DOWN_AEC_OPTION_A );
		$del_opts[1] = mosHTML::makeOption ( "All", _DOWN_AEC_OPTION_B );
		$del_opts[2] = mosHTML::makeOption ( "Set", _DOWN_AEC_OPTION_C );

		$settings['add_downloads'] = array( 'inputA' );
		$settings['set_downloads'] = array( 'inputA' );
		$settings['set_unlimited'] = array( 'list_yesno' );

		$current_groups = explode(';', $this->settings['group']);
		$settings['lists']['group']	= mosHTML::selectList($sg, 'group[]', 'size="4" multiple="multiple"', 'value', 'text', $current_groups);
		$current_groups_exp = explode(';', $this->settings['group_exp']);
		$settings['lists']['group_exp']	= mosHTML::selectList($sg, 'group_exp[]', 'size="4" multiple="multiple"', 'value', 'text', $current_groups_exp);

		$settings['set_group'] = array( 'list_yesno' );
		$settings['group'] = array( 'list' );
		$settings['lists']['delete_on_exp'] = mosHTML::selectList( $del_opts, 'delete_on_exp', 'size="3"', 'value', 'text', $this->settings['delete_on_exp'] );
		$settings['delete_on_exp'] = array( 'list' );
		$settings['set_group_exp'] = array( 'list_yesno' );
		$settings['group_exp'] = array( 'list' );

		return $settings;
	}

	// Bundle up the parameters
	public function saveparams( $params ) {
		$params['group'] = implode(';', $params['group']);
		$params['group_exp'] = implode(';', $params['group_exp']);
		return $params;
	}

	// Check whether Remository is installed
	public function detect_application() {
		return is_dir( _REMOS_ABSOLUTE_PATH.'/components/com_remository/c-classes' );
	}

	// There is no need to hack Remository as it contains all needed support for AEC
	// But the support should be enabled in the Remository configuration
	public function hacks() {
		return array();
	}

	public function expiration_action ($request) {
		$this->includeRemCore();
		if ( 'Set' == $this->settings['delete_on_exp'] ) {
			$roles = explode(';', $this->settings['group']);
			// The authoriser removes the specified user from specific roles
			foreach ($roles as $role) $this->authoriser->unassign($role, 'aUser', $request->metaUser->userid);
		}

		if ( 'All' == $this->settings['delete_on_exp'] ) {
			// The authoriser drops the specified user from all roles
			$this->authoriser->dropAccess('aUser', $request->metaUser->userid);
		}

		if ($this->settings['set_group_exp']) {
			$roles = explode(';', $this->settings['group_exp']);
			// The authoriser assigns the specified user to all expiry roles
			foreach ($roles as $role) $this->authoriser->assign($role, 'aUser', $request->metaUser->userid);
		}

		$mi_remositoryhandler = new remository_restriction( $this->database );
		$mi_id = $mi_remositoryhandler->getIDbyUserID( $request->metaUser->userid );
		$mi_remositoryhandler->load( $mi_id );

		if ( $mi_id ) $mi_remositoryhandler->deactivateAndStore();

		return true;
	}

	public function action ($request) {
		$this->includeRemCore();
		if ( !empty($this->settings['set_group']) ) {
			$roles = explode(';', $this->settings['group']);
			// The authoriser assigns the given user to the relevant roles
			foreach ($roles as $role) $this->authoriser->assign($role, 'aUser', $request->metaUser->userid);
		}

		$mi_remositoryhandler = new remository_restriction( $this->database );
		$mi_id = $mi_remositoryhandler->getIDbyUserID( $request->metaUser->userid );
		$mi_remositoryhandler->load( $mi_id );

		if ( !$mi_id ) $mi_remositoryhandler->activate($request->metaUser->userid);

		// Set the available downloads according to the specification in the settings
		if ( $this->settings['set_downloads'] ) {
			$mi_remositoryhandler->setDownloads( $this->settings['set_downloads'] );
		} 
		elseif ( $this->settings['add_downloads'] ) {
			$mi_remositoryhandler->addDownloads( $this->settings['add_downloads'] );
		}
		$mi_remositoryhandler->setUnlimited($this->settings['set_unlimited'] ? true : false);

		$mi_remositoryhandler->checkAndStore();

		return true;
	}
	
	// Load basic Remository elements needed for use with AEC
	private function includeRemCore () {
		$this->authoriser = aliroAuthorisationAdmin::getInstance();
		$lang = $this->interface->getCfg('lang');
		$langdir = _REMOS_ABSOLUTE_PATH.'/components/com_remository/language/';
		$Small_Text_Len = $Large_Text_Len = 100;
		if (file_exists($langdir.$lang.'.php')) require_once($langdir.$lang.'.php');
		require_once($langdir.'english.php');
	}
}

class remository_restriction extends remosDBTable {
	/** @var int Primary key */
	public $id					= null;
	/** @var int */
	public $userid 			= null;
	/** @var int */
	public $active				= null;
	/** @var int */
	public $granted_downloads	= null;
	/** @var int */
	public $unlimited_downloads	= null;
	/** @var text */
	public $used_downloads		= null;
	/** @var text */
	public $params				= null;

	// Constructor
	public function __construct ($db) {
		parent::__construct( '#__acctexp_mi_remository', 'id', $db );
	}

	public function getIDbyUserID ($userid) {
		$userid = intval($userid);
		$query = "SELECT `id` FROM #__acctexp_mi_remository WHERE `userid` = $userid";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();
		return $result ? $result : 0;
	}


	public function is_active() {
		return $this->active ? true : false;
	}

	public function getDownloadsLeft() {
		return empty( $this->unlimited_downloads ) ? ($this->granted_downloads - $this->used_downloads) : 9999;
	}

	public function hasDownloadsLeft() {
		return $this->getDownloadsLeft() > 0 ? true : false;
	}

	public function useDownload() {
		return ( $this->is_active() AND $this->hasDownloadsLeft() ) ? ($this->incrementDownloadStore() OR true) : false;
	}
	
	public function activate ($userid=0) {
		if ($userid) $this->userid = $userid;
		$this->active = 1;
	}
	
	public function deactivateAndStore () {
		$this->active = 0;
		$this->checkAndStore();
	}
	
	public function incrementDownloadStore () {
		$this->used_downloads++;
		$this->checkAndStore();
	}
	
	public function checkAndStore () {
		$this->check();
		$this->store();
	}

	public function setDownloads ($set) {
		$this->granted_downloads = $set;
	}

	public function addDownloads ($add) {
		$this->granted_downloads += $add;
	}

	public function setUnlimited ($bool=true) {
		$this->unlimited_downloads = $bool;
	}

}