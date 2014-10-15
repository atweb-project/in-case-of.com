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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class KomentoInstaller
{
	private $jinstaller		= null;
	private $manifest		= null;
	private $messages		= array();
	private $db				= null;
	private $installPath	= null;
	private $joomlaVersion	= null;

	public function __construct( JInstaller $jinstaller )
	{
		$this->db			= JFactory::getDbo();
		$this->jinstaller	= $jinstaller;
		$this->manifest		= $this->jinstaller->getManifest();
		$this->installPath	= $this->jinstaller->getPath('source');
		$this->joomlaVersion= $this->getJoomlaVersion();
		$this->komentoComponentId = $this->getKomentoComponentId();
	}

	public function execute()
	{
		if( !$this->checkDB() )
		{
			$this->setMessage( 'Warning : The system encounter an error when it tries to update the database. Please kindly update the database manually.', 'warning' );
		}

		if( !$this->checkKonfig() )
		{
			$this->setMessage( 'Warning : The system encounter an error when it tries to create default konfig. Please kindly configure Komento manually.', 'warning' );
		}

		if( !$this->checkConfig() )
		{
			$this->setMessage( 'Warning : The system encounter an error when it tries to create default config. Please kindly configure Komento manually.', 'warning' );
		}

		if( !$this->checkACL() )
		{
			$this->setMessage( 'Warning : The system encounter an error when it tries to create default ACL settings. Please kindly configure ACL manually.', 'warning' );
		}

		if( !$this->checkMenu() )
		{
			$this->setMessage( 'Warning : The system encounter an error when it tries to update the menu item. Please kindly update the menu item manually.', 'warning' );
		}

		$this->checkAdminMenu();

		if( !$this->checkPlugins() )
		{
			$this->setMessage( 'Warning : The system encounter an error when it tries to install the user plugin. Please kindly install the plugin manually.', 'warning' );
		}

		if( !$this->checkMedia() )
		{
			$this->setMessage( 'Warning: The system could not copy files to Media folder. Please kindly check the media folder permission.', 'warning' );
		}

		if( !$this->checkModules() )
		{
			$this->setMessage( 'Warning : The system encounter an error when it tries to install the modules. Please kindly install the modules manually.', 'warning' );
		}

		$this->setMessage( 'Success : Installation Completed. Thank you for choosing Komento.', 'info' );

	}

	/**
	 * We only support PHP 5 and above
	 */
	public static function checkPHP()
	{
		$phpVersion = floatval(phpversion());

		return ( $phpVersion >= 5 );
	}

	/**
	 * From time to time, any DB changes will be sync here
	 */
	private function checkDB()
	{
		$check = new KomentoDatabaseUpdate( $this->db );
		return $check->update();
	}

	/**
	 * Make sure there's at least a default entry in configuration table
	 */
	private function checkKonfig()
	{
		$query	= 'SELECT COUNT(*) FROM ' . $this->db->nameQuote( '#__komento_configs' )
				. ' WHERE ' . $this->db->nameQuote( 'component' ) . ' = ' . $this->db->quote( 'com_komento' );

		$this->db->setQuery( $query );

		if( !$this->db->loadResult() )
		{
			$file		= JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_komento' . DS . 'konfiguration.ini';

			$registry	= JRegistry::getInstance( 'komento' );

			// Do not save environmental values
			$data	= JFile::read($file);
			$data	= str_ireplace('foundry_environment="development"', '', $data);
			$data	= str_ireplace('komento_environment="development"', '', $data);
			$data	= str_ireplace('foundry_environment="production"', '', $data);
			$data	= str_ireplace('komento_environment="production"', '', $data);

			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_komento' . DS . 'helpers' . DS . 'helper.php' );
			$registry = Komento::_( 'loadRegistry', 'konfig', $data );

			$obj		= new stdClass();
			$obj->component	= 'com_komento';
			$obj->params	= $registry->toString( 'INI' );

			return $this->db->insertObject( '#__komento_configs', $obj );
		}

		return true;
	}

	private function checkConfig()
	{
		$query	= 'SELECT COUNT(*) FROM ' . $this->db->nameQuote( '#__komento_configs' )
				. ' WHERE ' . $this->db->nameQuote( 'component' ) . ' = ' . $this->db->quote( 'com_content' );

		$this->db->setQuery( $query );

		if( !$this->db->loadResult() )
		{
			$file		= JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_komento' . DS . 'configuration.ini';
			$registry	= JRegistry::getInstance( 'config' );
			$registry->loadFile( $file, 'INI' );

			$registry->set( 'enable_komento', 1 );

			// Escape regex strings to avoid slashes get stripped off during fresh installation
			$registry->set( 'email_regex', addslashes( $registry->get( 'email_regex' ) ) );
			$registry->set( 'website_regex', addslashes( $registry->get( 'website_regex' ) ) );

			$obj		= new stdClass();
			$obj->component	= 'com_content';
			$obj->params	= $registry->toString( 'INI' );

			return $this->db->insertObject( '#__komento_configs', $obj );
		}

		return true;
	}

	/**
	 * Create default ACL settings
	 */
	private function checkACL()
	{
		$query	= 'SELECT COUNT(*) FROM ' . $this->db->nameQuote( '#__komento_acl' );
		$this->db->setQuery( $query );

		if( !$this->db->loadResult() )
		{
			// create default for each existing usergroup
			$db = JFactory::getDBO();

			if( $this->getJoomlaVersion() >= '1.6' )
			{
				$query = 'SELECT a.id, a.title AS `name`, COUNT(DISTINCT b.id) AS level';
				$query .= ' , GROUP_CONCAT(b.id SEPARATOR \',\') AS parents';
				$query .= ' FROM #__usergroups AS a';
				$query .= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
			}
			else
			{
				$query	= 'SELECT `id`, `name`, 0 as `level` FROM ' . $db->nameQuote('#__core_acl_aro_groups');
			}

			// condition
			$where  = array();

			// we need to filter out the ROOT and USER dummy records.
			if($this->getJoomlaVersion() < '1.6')
			{
				$where[] = '`id` > 17 AND `id` < 26 OR `id` = 29';
			}

			$where = ( count( $where ) ? ' WHERE ' .implode( ' AND ', $where ) : '' );

			$query  .= $where;

			// grouping and ordering
			if( $this->getJoomlaVersion() >= '1.6' )
			{
				$query	.= ' GROUP BY a.id';
				$query	.= ' ORDER BY a.lft ASC';
			}
			else
			{
				$query 	.= ' ORDER BY id';
			}

			$db->setQuery( $query );
			$userGroups = $db->loadObjectList();

			$userGroupIDs	= array();

			foreach ($userGroups as $userGroup) {
				$userGroupIDs[] = $userGroup->id;
			}


			$db			= JFactory::getDbo();
			$query		= 'SELECT `cid` FROM `#__komento_acl` WHERE `component` = '.$db->quote( 'com_content' ). ' AND `type` = \'usergroup\'';
			$db->setQuery( $query );
			$current	= $db->loadResultArray();

			foreach ($userGroupIDs as $userGroupID) {
				if ( !is_array($current) || !in_array($userGroupID, $current))
				{
					$rules = '';

					$query = 'INSERT INTO `#__komento_acl` ( `cid`, `component`, `type` , `rules` ) VALUES ( '.$db->quote($userGroupID).','.$db->quote('com_content').','.$db->quote('usergroup').','.$db->quote($rules).')';
					$db->setQuery( $query );
					$db->query();
				}
			}

			$queries = array();

			/* default id mapping
			name					j1.5	j1.6
			Public					29		1
			Registered				18		2
			Author					19		3
			Editor					20		4
			Publisher				21		5
			Manager					23		6
			Administrator			24		7
			Super Administrator		25		8
			*/

			// update default value to default joomla usergroup for >j1,6
			if( $this->getJoomlaVersion() >= '1.6' )
			{
				// Public
				$queries[] = 'UPDATE `#__komento_acl` SET `rules` = \'[
					{
						"name":"read_comment",
						"title":"COM_KOMENTO_ACL_READCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_stickies",
						"title":"COM_KOMENTO_ACL_READSTICKIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_lovies",
						"title":"COM_KOMENTO_ACL_READLOVIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"add_comment",
						"title":"COM_KOMENTO_ACL_ADDCOMMENT",
						"value":"1","section":"comment"
					},{
						"name":"edit_own_comment",
						"title":"COM_KOMENTO_ACL_EDITOWNCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"delete_own_comment",
						"title":"COM_KOMENTO_ACL_DELOWNCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_edit_comment",
						"title":"COM_KOMENTO_ACL_AUTHOREDITCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_delete_comment",
						"title":"COM_KOMENTO_ACL_AUTHORDELCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_publish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORPUBCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_unpublish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORUNPUBCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"edit_all_comment",
						"title":"COM_KOMENTO_ACL_EDITALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"delete_all_comment",
						"title":"COM_KOMENTO_ACL_DELALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"publish_all_comment",
						"title":"COM_KOMENTO_ACL_PUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"unpublish_all_comment",
						"title":"COM_KOMENTO_ACL_UNPUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"like_comment",
						"title":"COM_KOMENTO_ACL_LIKECOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"report_comment",
						"title":"COM_KOMENTO_ACL_REPORTCOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"share_comment",
						"title":"COM_KOMENTO_ACL_SHARECOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"reply_comment",
						"title":"COM_KOMENTO_ACL_REPLYCOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"stick_comment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"upload_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"download_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"delete_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					}]\' WHERE `cid` = 1 AND `component` = \'com_content\' AND `type` = \'usergroup\'';

				// Manager
				$queries[] = 'UPDATE `#__komento_acl` SET `rules` = \'[
					{
						"name":"read_comment",
						"title":"COM_KOMENTO_ACL_READCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_stickies",
						"title":"COM_KOMENTO_ACL_READSTICKIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_lovies",
						"title":"COM_KOMENTO_ACL_READLOVIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"add_comment",
						"title":"COM_KOMENTO_ACL_ADDCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"edit_own_comment",
						"title":"COM_KOMENTO_ACL_EDITOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"delete_own_comment",
						"title":"COM_KOMENTO_ACL_DELOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_edit_comment",
						"title":"COM_KOMENTO_ACL_AUTHOREDITCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_delete_comment",
						"title":"COM_KOMENTO_ACL_AUTHORDELCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_publish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORPUBCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_unpublish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORUNPUBCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"edit_all_comment",
						"title":"COM_KOMENTO_ACL_EDITALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"delete_all_comment",
						"title":"COM_KOMENTO_ACL_DELALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"publish_all_comment",
						"title":"COM_KOMENTO_ACL_PUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"unpublish_all_comment",
						"title":"COM_KOMENTO_ACL_UNPUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"like_comment",
						"title":"COM_KOMENTO_ACL_LIKECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"report_comment",
						"title":"COM_KOMENTO_ACL_REPORTCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"share_comment",
						"title":"COM_KOMENTO_ACL_SHARECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"reply_comment",
						"title":"COM_KOMENTO_ACL_REPLYCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"stick_comment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"upload_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"download_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"delete_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					}]\' WHERE `cid` = 6 AND `component` = \'com_content\' AND `type` = \'usergroup\'';

				// Administrator
				$queries[] = 'UPDATE `#__komento_acl` SET `rules` = \'[
					{
						"name":"read_comment",
						"title":"COM_KOMENTO_ACL_READCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_stickies",
						"title":"COM_KOMENTO_ACL_READSTICKIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_lovies",
						"title":"COM_KOMENTO_ACL_READLOVIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"add_comment",
						"title":"COM_KOMENTO_ACL_ADDCOMMENT",
						"value":"1","section":"comment"
					},{
						"name":"edit_own_comment",
						"title":"COM_KOMENTO_ACL_EDITOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"delete_own_comment",
						"title":"COM_KOMENTO_ACL_DELOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_edit_comment",
						"title":"COM_KOMENTO_ACL_AUTHOREDITCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_delete_comment",
						"title":"COM_KOMENTO_ACL_AUTHORDELCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_publish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORPUBCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_unpublish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORUNPUBCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"edit_all_comment",
						"title":"COM_KOMENTO_ACL_EDITALLCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"delete_all_comment",
						"title":"COM_KOMENTO_ACL_DELALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"publish_all_comment",
						"title":"COM_KOMENTO_ACL_PUBALLCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"unpublish_all_comment",
						"title":"COM_KOMENTO_ACL_UNPUBALLCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"like_comment",
						"title":"COM_KOMENTO_ACL_LIKECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"report_comment",
						"title":"COM_KOMENTO_ACL_REPORTCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"share_comment",
						"title":"COM_KOMENTO_ACL_SHARECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"reply_comment",
						"title":"COM_KOMENTO_ACL_REPLYCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"stick_comment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"upload_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"download_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"delete_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					}]\' WHERE `cid` = 7 AND `component` = \'com_content\' AND `type` = \'usergroup\'';

				// Registered
				$queries[] = 'UPDATE `#__komento_acl` SET `rules` = \'[
					{
						"name":"read_comment",
						"title":"COM_KOMENTO_ACL_READCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_stickies",
						"title":"COM_KOMENTO_ACL_READSTICKIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_lovies",
						"title":"COM_KOMENTO_ACL_READLOVIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"add_comment",
						"title":"COM_KOMENTO_ACL_ADDCOMMENT",
						"value":"1","section":"comment"
					},{
						"name":"edit_own_comment",
						"title":"COM_KOMENTO_ACL_EDITOWNCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"delete_own_comment",
						"title":"COM_KOMENTO_ACL_DELOWNCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_edit_comment",
						"title":"COM_KOMENTO_ACL_AUTHOREDITCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_delete_comment",
						"title":"COM_KOMENTO_ACL_AUTHORDELCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_publish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORPUBCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_unpublish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORUNPUBCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"edit_all_comment",
						"title":"COM_KOMENTO_ACL_EDITALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"delete_all_comment",
						"title":"COM_KOMENTO_ACL_DELALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"publish_all_comment",
						"title":"COM_KOMENTO_ACL_PUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"unpublish_all_comment",
						"title":"COM_KOMENTO_ACL_UNPUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"like_comment",
						"title":"COM_KOMENTO_ACL_LIKECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"report_comment",
						"title":"COM_KOMENTO_ACL_REPORTCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"share_comment",
						"title":"COM_KOMENTO_ACL_SHARECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"reply_comment",
						"title":"COM_KOMENTO_ACL_REPLYCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"stick_comment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"upload_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"download_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"delete_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					}]\' WHERE `cid` = 2 AND `component` = \'com_content\' AND `type` = \'usergroup\'';

				// Author
				$queries[] = 'UPDATE `#__komento_acl` SET `rules` = \'[
					{
						"name":"read_comment",
						"title":"COM_KOMENTO_ACL_READCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_stickies",
						"title":"COM_KOMENTO_ACL_READSTICKIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_lovies",
						"title":"COM_KOMENTO_ACL_READLOVIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"add_comment",
						"title":"COM_KOMENTO_ACL_ADDCOMMENT",
						"value":"1","section":"comment"
					},{
						"name":"edit_own_comment",
						"title":"COM_KOMENTO_ACL_EDITOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"delete_own_comment",
						"title":"COM_KOMENTO_ACL_DELOWNCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_edit_comment",
						"title":"COM_KOMENTO_ACL_AUTHOREDITCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_delete_comment",
						"title":"COM_KOMENTO_ACL_AUTHORDELCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_publish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORPUBCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_unpublish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORUNPUBCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"edit_all_comment",
						"title":"COM_KOMENTO_ACL_EDITALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"delete_all_comment",
						"title":"COM_KOMENTO_ACL_DELALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"publish_all_comment",
						"title":"COM_KOMENTO_ACL_PUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"unpublish_all_comment",
						"title":"COM_KOMENTO_ACL_UNPUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"like_comment",
						"title":"COM_KOMENTO_ACL_LIKECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"report_comment",
						"title":"COM_KOMENTO_ACL_REPORTCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"share_comment",
						"title":"COM_KOMENTO_ACL_SHARECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"reply_comment",
						"title":"COM_KOMENTO_ACL_REPLYCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"stick_comment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"upload_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"download_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"delete_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					}]\' WHERE `cid` = 3 AND `component` = \'com_content\' AND `type` = \'usergroup\'';

				// Editor
				$queries[] = 'UPDATE `#__komento_acl` SET `rules` = \'[
					{
						"name":"read_comment",
						"title":"COM_KOMENTO_ACL_READCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_stickies",
						"title":"COM_KOMENTO_ACL_READSTICKIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_lovies",
						"title":"COM_KOMENTO_ACL_READLOVIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"add_comment",
						"title":"COM_KOMENTO_ACL_ADDCOMMENT",
						"value":"1","section":"comment"
					},{
						"name":"edit_own_comment",
						"title":"COM_KOMENTO_ACL_EDITOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"delete_own_comment",
						"title":"COM_KOMENTO_ACL_DELOWNCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_edit_comment",
						"title":"COM_KOMENTO_ACL_AUTHOREDITCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_delete_comment",
						"title":"COM_KOMENTO_ACL_AUTHORDELCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_publish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORPUBCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_unpublish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORUNPUBCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"edit_all_comment",
						"title":"COM_KOMENTO_ACL_EDITALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"delete_all_comment",
						"title":"COM_KOMENTO_ACL_DELALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"publish_all_comment",
						"title":"COM_KOMENTO_ACL_PUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"unpublish_all_comment",
						"title":"COM_KOMENTO_ACL_UNPUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"like_comment",
						"title":"COM_KOMENTO_ACL_LIKECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"report_comment",
						"title":"COM_KOMENTO_ACL_REPORTCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"share_comment",
						"title":"COM_KOMENTO_ACL_SHARECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"reply_comment",
						"title":"COM_KOMENTO_ACL_REPLYCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"stick_comment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"upload_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"download_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"delete_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					}]\' WHERE `cid` = 4 AND `component` = \'com_content\' AND `type` = \'usergroup\'';

				// Publisher
				$queries[] = 'UPDATE `#__komento_acl` SET `rules` = \'[
					{
						"name":"read_comment",
						"title":"COM_KOMENTO_ACL_READCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_stickies",
						"title":"COM_KOMENTO_ACL_READSTICKIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_lovies",
						"title":"COM_KOMENTO_ACL_READLOVIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"add_comment",
						"title":"COM_KOMENTO_ACL_ADDCOMMENT",
						"value":"1","section":"comment"
					},{
						"name":"edit_own_comment",
						"title":"COM_KOMENTO_ACL_EDITOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"delete_own_comment",
						"title":"COM_KOMENTO_ACL_DELOWNCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_edit_comment",
						"title":"COM_KOMENTO_ACL_AUTHOREDITCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_delete_comment",
						"title":"COM_KOMENTO_ACL_AUTHORDELCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_publish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORPUBCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_unpublish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORUNPUBCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"edit_all_comment",
						"title":"COM_KOMENTO_ACL_EDITALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"delete_all_comment",
						"title":"COM_KOMENTO_ACL_DELALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"publish_all_comment",
						"title":"COM_KOMENTO_ACL_PUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"unpublish_all_comment",
						"title":"COM_KOMENTO_ACL_UNPUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"like_comment",
						"title":"COM_KOMENTO_ACL_LIKECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"report_comment",
						"title":"COM_KOMENTO_ACL_REPORTCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"share_comment",
						"title":"COM_KOMENTO_ACL_SHARECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"reply_comment",
						"title":"COM_KOMENTO_ACL_REPLYCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"stick_comment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"upload_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"download_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"delete_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					}]\' WHERE `cid` = 5 AND `component` = \'com_content\' AND `type` = \'usergroup\'';

				// Super Administrator
				$queries[] = 'UPDATE `#__komento_acl` SET `rules` = \'[
					{
						"name":"read_comment",
						"title":"COM_KOMENTO_ACL_READCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_stickies",
						"title":"COM_KOMENTO_ACL_READSTICKIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_lovies",
						"title":"COM_KOMENTO_ACL_READLOVIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"add_comment",
						"title":"COM_KOMENTO_ACL_ADDCOMMENT",
						"value":"1","section":"comment"
					},{
						"name":"edit_own_comment",
						"title":"COM_KOMENTO_ACL_EDITOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"delete_own_comment",
						"title":"COM_KOMENTO_ACL_DELOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_edit_comment",
						"title":"COM_KOMENTO_ACL_AUTHOREDITCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_delete_comment",
						"title":"COM_KOMENTO_ACL_AUTHORDELCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_publish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORPUBCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_unpublish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORUNPUBCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"edit_all_comment",
						"title":"COM_KOMENTO_ACL_EDITALLCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"delete_all_comment",
						"title":"COM_KOMENTO_ACL_DELALLCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"publish_all_comment",
						"title":"COM_KOMENTO_ACL_PUBALLCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"unpublish_all_comment",
						"title":"COM_KOMENTO_ACL_UNPUBALLCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"like_comment",
						"title":"COM_KOMENTO_ACL_LIKECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"report_comment",
						"title":"COM_KOMENTO_ACL_REPORTCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"share_comment",
						"title":"COM_KOMENTO_ACL_SHARECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"reply_comment",
						"title":"COM_KOMENTO_ACL_REPLYCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"stick_comment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"upload_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"download_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"delete_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					}]\' WHERE `cid` = 8 AND `component` = \'com_content\' AND `type` = \'usergroup\'';
			}
			else
			{
				// Public
				$queries[] = 'UPDATE `#__komento_acl` SET `rules` = \'[
					{
						"name":"read_comment",
						"title":"COM_KOMENTO_ACL_READCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_stickies",
						"title":"COM_KOMENTO_ACL_READSTICKIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_lovies",
						"title":"COM_KOMENTO_ACL_READLOVIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"add_comment",
						"title":"COM_KOMENTO_ACL_ADDCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"edit_own_comment",
						"title":"COM_KOMENTO_ACL_EDITOWNCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"delete_own_comment",
						"title":"COM_KOMENTO_ACL_DELOWNCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_edit_comment",
						"title":"COM_KOMENTO_ACL_AUTHOREDITCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_delete_comment",
						"title":"COM_KOMENTO_ACL_AUTHORDELCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_publish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORPUBCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_unpublish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORUNPUBCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"edit_all_comment",
						"title":"COM_KOMENTO_ACL_EDITALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"delete_all_comment",
						"title":"COM_KOMENTO_ACL_DELALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"publish_all_comment",
						"title":"COM_KOMENTO_ACL_PUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"unpublish_all_comment",
						"title":"COM_KOMENTO_ACL_UNPUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"like_comment",
						"title":"COM_KOMENTO_ACL_LIKECOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"report_comment",
						"title":"COM_KOMENTO_ACL_REPORTCOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"share_comment",
						"title":"COM_KOMENTO_ACL_SHARECOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"reply_comment",
						"title":"COM_KOMENTO_ACL_REPLYCOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"stick_comment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"upload_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"download_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"delete_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					}]\' WHERE `cid` = 29 AND `component` = \'com_content\' AND `type` = \'usergroup\'';

				// Manager
				$queries[] = 'UPDATE `#__komento_acl` SET `rules` = \'[
					{
						"name":"read_comment",
						"title":"COM_KOMENTO_ACL_READCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_stickies",
						"title":"COM_KOMENTO_ACL_READSTICKIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_lovies",
						"title":"COM_KOMENTO_ACL_READLOVIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"add_comment",
						"title":"COM_KOMENTO_ACL_ADDCOMMENT",
						"value":"1","section":"comment"
					},{
						"name":"edit_own_comment",
						"title":"COM_KOMENTO_ACL_EDITOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"delete_own_comment",
						"title":"COM_KOMENTO_ACL_DELOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_edit_comment",
						"title":"COM_KOMENTO_ACL_AUTHOREDITCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_delete_comment",
						"title":"COM_KOMENTO_ACL_AUTHORDELCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_publish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORPUBCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_unpublish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORUNPUBCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"edit_all_comment",
						"title":"COM_KOMENTO_ACL_EDITALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"delete_all_comment",
						"title":"COM_KOMENTO_ACL_DELALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"publish_all_comment",
						"title":"COM_KOMENTO_ACL_PUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"unpublish_all_comment",
						"title":"COM_KOMENTO_ACL_UNPUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"like_comment",
						"title":"COM_KOMENTO_ACL_LIKECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"report_comment",
						"title":"COM_KOMENTO_ACL_REPORTCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"share_comment",
						"title":"COM_KOMENTO_ACL_SHARECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"reply_comment",
						"title":"COM_KOMENTO_ACL_REPLYCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"stick_comment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"upload_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"download_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"delete_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					}]\' WHERE `cid` = 23 AND `component` = \'com_content\' AND `type` = \'usergroup\'';

				// Administrator
				$queries[] = 'UPDATE `#__komento_acl` SET `rules` = \'[
					{
						"name":"read_comment",
						"title":"COM_KOMENTO_ACL_READCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_stickies",
						"title":"COM_KOMENTO_ACL_READSTICKIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_lovies",
						"title":"COM_KOMENTO_ACL_READLOVIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"add_comment",
						"title":"COM_KOMENTO_ACL_ADDCOMMENT",
						"value":"1","section":"comment"
					},{
						"name":"edit_own_comment",
						"title":"COM_KOMENTO_ACL_EDITOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"delete_own_comment",
						"title":"COM_KOMENTO_ACL_DELOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_edit_comment",
						"title":"COM_KOMENTO_ACL_AUTHOREDITCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_delete_comment",
						"title":"COM_KOMENTO_ACL_AUTHORDELCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_publish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORPUBCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_unpublish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORUNPUBCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"edit_all_comment",
						"title":"COM_KOMENTO_ACL_EDITALLCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"delete_all_comment",
						"title":"COM_KOMENTO_ACL_DELALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"publish_all_comment",
						"title":"COM_KOMENTO_ACL_PUBALLCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"unpublish_all_comment",
						"title":"COM_KOMENTO_ACL_UNPUBALLCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"like_comment",
						"title":"COM_KOMENTO_ACL_LIKECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"report_comment",
						"title":"COM_KOMENTO_ACL_REPORTCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"share_comment",
						"title":"COM_KOMENTO_ACL_SHARECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"reply_comment",
						"title":"COM_KOMENTO_ACL_REPLYCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"stick_comment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"upload_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"download_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"delete_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					}]\' WHERE `cid` = 24 AND `component` = \'com_content\' AND `type` = \'usergroup\'';

				// Registered
				$queries[] = 'UPDATE `#__komento_acl` SET `rules` = \'[
					{
						"name":"read_comment",
						"title":"COM_KOMENTO_ACL_READCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_stickies",
						"title":"COM_KOMENTO_ACL_READSTICKIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_lovies",
						"title":"COM_KOMENTO_ACL_READLOVIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"add_comment",
						"title":"COM_KOMENTO_ACL_ADDCOMMENT",
						"value":"1","section":"comment"
					},{
						"name":"edit_own_comment",
						"title":"COM_KOMENTO_ACL_EDITOWNCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"delete_own_comment",
						"title":"COM_KOMENTO_ACL_DELOWNCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_edit_comment",
						"title":"COM_KOMENTO_ACL_AUTHOREDITCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_delete_comment",
						"title":"COM_KOMENTO_ACL_AUTHORDELCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_publish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORPUBCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_unpublish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORUNPUBCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"edit_all_comment",
						"title":"COM_KOMENTO_ACL_EDITALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"delete_all_comment",
						"title":"COM_KOMENTO_ACL_DELALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"publish_all_comment",
						"title":"COM_KOMENTO_ACL_PUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"unpublish_all_comment",
						"title":"COM_KOMENTO_ACL_UNPUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"like_comment",
						"title":"COM_KOMENTO_ACL_LIKECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"report_comment",
						"title":"COM_KOMENTO_ACL_REPORTCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"share_comment",
						"title":"COM_KOMENTO_ACL_SHARECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"reply_comment",
						"title":"COM_KOMENTO_ACL_REPLYCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"stick_comment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"upload_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"download_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"delete_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					}]\' WHERE `cid` = 18 AND `component` = \'com_content\' AND `type` = \'usergroup\'';

				// Author
				$queries[] = 'UPDATE `#__komento_acl` SET `rules` = \'[
					{
						"name":"read_comment",
						"title":"COM_KOMENTO_ACL_READCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_stickies",
						"title":"COM_KOMENTO_ACL_READSTICKIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_lovies",
						"title":"COM_KOMENTO_ACL_READLOVIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"add_comment",
						"title":"COM_KOMENTO_ACL_ADDCOMMENT",
						"value":"1","section":"comment"
					},{
						"name":"edit_own_comment",
						"title":"COM_KOMENTO_ACL_EDITOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"delete_own_comment",
						"title":"COM_KOMENTO_ACL_DELOWNCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_edit_comment",
						"title":"COM_KOMENTO_ACL_AUTHOREDITCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_delete_comment",
						"title":"COM_KOMENTO_ACL_AUTHORDELCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_publish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORPUBCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_unpublish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORUNPUBCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"edit_all_comment",
						"title":"COM_KOMENTO_ACL_EDITALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"delete_all_comment",
						"title":"COM_KOMENTO_ACL_DELALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"publish_all_comment",
						"title":"COM_KOMENTO_ACL_PUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"unpublish_all_comment",
						"title":"COM_KOMENTO_ACL_UNPUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"like_comment",
						"title":"COM_KOMENTO_ACL_LIKECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"report_comment",
						"title":"COM_KOMENTO_ACL_REPORTCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"share_comment",
						"title":"COM_KOMENTO_ACL_SHARECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"reply_comment",
						"title":"COM_KOMENTO_ACL_REPLYCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"stick_comment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"upload_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"download_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"delete_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					}]\' WHERE `cid` = 19 AND `component` = \'com_content\' AND `type` = \'usergroup\'';

				// Editor
				$queries[] = 'UPDATE `#__komento_acl` SET `rules` = \'[
					{
						"name":"read_comment",
						"title":"COM_KOMENTO_ACL_READCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_stickies",
						"title":"COM_KOMENTO_ACL_READSTICKIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_lovies",
						"title":"COM_KOMENTO_ACL_READLOVIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"add_comment",
						"title":"COM_KOMENTO_ACL_ADDCOMMENT",
						"value":"1","section":"comment"
					},{
						"name":"edit_own_comment",
						"title":"COM_KOMENTO_ACL_EDITOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"delete_own_comment",
						"title":"COM_KOMENTO_ACL_DELOWNCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_edit_comment",
						"title":"COM_KOMENTO_ACL_AUTHOREDITCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_delete_comment",
						"title":"COM_KOMENTO_ACL_AUTHORDELCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_publish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORPUBCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_unpublish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORUNPUBCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"edit_all_comment",
						"title":"COM_KOMENTO_ACL_EDITALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"delete_all_comment",
						"title":"COM_KOMENTO_ACL_DELALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"publish_all_comment",
						"title":"COM_KOMENTO_ACL_PUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"unpublish_all_comment",
						"title":"COM_KOMENTO_ACL_UNPUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"like_comment",
						"title":"COM_KOMENTO_ACL_LIKECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"report_comment",
						"title":"COM_KOMENTO_ACL_REPORTCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"share_comment",
						"title":"COM_KOMENTO_ACL_SHARECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"reply_comment",
						"title":"COM_KOMENTO_ACL_REPLYCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"stick_comment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"upload_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"download_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"delete_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					}]\' WHERE `cid` = 20 AND `component` = \'com_content\' AND `type` = \'usergroup\'';

				// Publisher
				$queries[] = 'UPDATE `#__komento_acl` SET `rules` = \'[
					{
						"name":"read_comment",
						"title":"COM_KOMENTO_ACL_READCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_stickies",
						"title":"COM_KOMENTO_ACL_READSTICKIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_lovies",
						"title":"COM_KOMENTO_ACL_READLOVIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"add_comment",
						"title":"COM_KOMENTO_ACL_ADDCOMMENT",
						"value":"1","section":"comment"
					},{
						"name":"edit_own_comment",
						"title":"COM_KOMENTO_ACL_EDITOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"delete_own_comment",
						"title":"COM_KOMENTO_ACL_DELOWNCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_edit_comment",
						"title":"COM_KOMENTO_ACL_AUTHOREDITCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_delete_comment",
						"title":"COM_KOMENTO_ACL_AUTHORDELCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"author_publish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORPUBCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_unpublish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORUNPUBCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"edit_all_comment",
						"title":"COM_KOMENTO_ACL_EDITALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"delete_all_comment",
						"title":"COM_KOMENTO_ACL_DELALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"publish_all_comment",
						"title":"COM_KOMENTO_ACL_PUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"unpublish_all_comment",
						"title":"COM_KOMENTO_ACL_UNPUBALLCOMMENT",
						"value":"0",
						"section":"comment"
					},{
						"name":"like_comment",
						"title":"COM_KOMENTO_ACL_LIKECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"report_comment",
						"title":"COM_KOMENTO_ACL_REPORTCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"share_comment",
						"title":"COM_KOMENTO_ACL_SHARECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"reply_comment",
						"title":"COM_KOMENTO_ACL_REPLYCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"stick_comment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					},{
						"name":"upload_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"download_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"delete_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"0",
						"section":"features"
					}]\' WHERE `cid` = 21 AND `component` = \'com_content\' AND `type` = \'usergroup\'';

				// Super Administrator
				$queries[] = 'UPDATE `#__komento_acl` SET `rules` = \'[
					{
						"name":"read_comment",
						"title":"COM_KOMENTO_ACL_READCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_stickies",
						"title":"COM_KOMENTO_ACL_READSTICKIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"read_lovies",
						"title":"COM_KOMENTO_ACL_READLOVIES",
						"value":"1",
						"section":"comment"
					},{
						"name":"add_comment",
						"title":"COM_KOMENTO_ACL_ADDCOMMENT",
						"value":"1","section":"comment"
					},{
						"name":"edit_own_comment",
						"title":"COM_KOMENTO_ACL_EDITOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"delete_own_comment",
						"title":"COM_KOMENTO_ACL_DELOWNCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_edit_comment",
						"title":"COM_KOMENTO_ACL_AUTHOREDITCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_delete_comment",
						"title":"COM_KOMENTO_ACL_AUTHORDELCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_publish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORPUBCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"author_unpublish_comment",
						"title":"COM_KOMENTO_ACL_AUTHORUNPUBCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"edit_all_comment",
						"title":"COM_KOMENTO_ACL_EDITALLCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"delete_all_comment",
						"title":"COM_KOMENTO_ACL_DELALLCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"publish_all_comment",
						"title":"COM_KOMENTO_ACL_PUBALLCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"unpublish_all_comment",
						"title":"COM_KOMENTO_ACL_UNPUBALLCOMMENT",
						"value":"1",
						"section":"comment"
					},{
						"name":"like_comment",
						"title":"COM_KOMENTO_ACL_LIKECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"report_comment",
						"title":"COM_KOMENTO_ACL_REPORTCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"share_comment",
						"title":"COM_KOMENTO_ACL_SHARECOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"reply_comment",
						"title":"COM_KOMENTO_ACL_REPLYCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"stick_comment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"upload_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"download_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					},{
						"name":"delete_attachment",
						"title":"COM_KOMENTO_ACL_STICKCOMMENT",
						"value":"1",
						"section":"features"
					}]\' WHERE `cid` = 25 AND `component` = \'com_content\' AND `type` = \'usergroup\'';
			}

			$db = JFactory::getDbo();

			foreach ($queries as $query) {
				$db->setQuery( $query );
				$db->query();
			}
		}

		return true;
	}

	/**
	 * Make sure the menu items are correct, create if non.
	 */
	private function checkMenu()
	{
		// At the moment we skip frontend's menu
		return true;

		if ($this->komentoComponentId)
			return true;

		$mainMenutype = $this->getJoomlaDefaultMenutype();

		// Let's see if the menu item exists or not
		if( $this->joomlaVersion >= '1.6' )
		{
			$query	= 'SELECT COUNT(*) FROM ' . $this->db->nameQuote( '#__menu' )
					. ' WHERE ' . $this->db->nameQuote( 'link' ) . ' LIKE ' .  $this->db->Quote( '%option=com_komento%' )
					. ' AND `client_id`=' . $this->db->Quote( '0' )
					. ' AND `type`=' . $this->db->Quote( 'component' );
		} else {
			$query	= 'SELECT COUNT(*) FROM ' . $this->db->nameQuote( '#__menu' )
					. ' WHERE ' . $this->db->nameQuote( 'link' ) . ' LIKE ' .  $this->db->Quote( '%option=com_komento%' );
		}

		$this->db->setQuery( $query );

		// Update or create menu item
		if( $menuExists = $this->db->loadResult() )
		{
			if( $this->joomlaVersion >= '1.6' )
			{
				$query 	= 'UPDATE ' . $this->db->nameQuote( '#__menu' )
					. ' SET `component_id` = ' . $this->db->Quote( $this->komentoComponentId )
					. ' WHERE `link` LIKE ' . $this->db->Quote('%option=com_komento%')
					. ' AND `type` = ' . $this->db->Quote( 'component' )
					. ' AND `client_id` = ' . $this->db->Quote( '0' );
			}
			else
			{
				$query 	= 'UPDATE ' . $this->db->nameQuote( '#__menu' )
					. ' SET `componentid` = ' . $this->db->Quote( $this->komentoComponentId )
					. ' WHERE `link` LIKE ' . $this->db->Quote('%option=com_komento%');
			}

			$this->db->setQuery( $query );
			$this->db->query();
		}
		else
		{
			$query 	= 'SELECT ' . $this->db->nameQuote( 'ordering' )
					. ' FROM ' . $this->db->nameQuote( '#__menu' )
					. ' ORDER BY ' . $this->db->nameQuote( 'ordering' ) . ' DESC LIMIT 1';
			$this->db->setQuery( $query );
			$order 	= $this->db->loadResult() + 1;

			// hardcode the ordering
			$order = 99999;

			$table = JTable::getInstance( 'Menu', 'JTable' );

			if( $this->joomlaVersion >= '1.6' )
			{
				$table->menutype		= $mainMenutype;
				$table->title 			= 'Komento';
				$table->alias 			= 'Komento';
				$table->path 			= 'komento';
				$table->link 			= 'index.php?option=com_komento';
				$table->type 			= 'component';
				$table->published 		= '1';
				$table->parent_id 		= '1';
				$table->component_id	= $this->komentoComponentId;
				$table->ordering 		= $order;
				$table->client_id 		= '0';
				$table->language 		= '*';
				$table->setLocation('1', 'last-child');

			} else {

				$table->menutype	= $mainMenutype;
				$table->name		= 'Komento';
				$table->alias		= 'Komento';
				$table->link		= 'index.php?option=com_komento';
				$table->type		= 'component';
				$table->published	= '1';
				$table->parent		= '0';
				$table->componentid	= $this->komentoComponentId;
				$table->sublivel	= '';
				$table->ordering	= $order;
			}

			return $table->store();
		}
	}

	private function getKomentoComponentId()
	{
		if( $this->joomlaVersion >= '1.6' )
		{
			$query 	= 'SELECT ' . $this->db->nameQuote( 'extension_id' )
				. ' FROM ' . $this->db->nameQuote( '#__extensions' )
				. ' WHERE `element`=' . $this->db->Quote( 'com_komento' )
				. ' AND `type`=' . $this->db->Quote( 'component' );
		}
		else
		{
			$query 	= 'SELECT ' . $this->db->nameQuote( 'id' )
				. ' FROM ' . $this->db->nameQuote( '#__components' )
				. ' WHERE `option`=' . $this->db->Quote( 'com_komento' )
				. ' AND `parent`=' . $this->db->Quote( '0');
		}

		$this->db->setQuery( $query );

		return $this->db->loadResult();
	}

	private function getJoomlaDefaultMenutype()
	{
		$query	= 'SELECT `menutype` FROM ' . $this->db->nameQuote( '#__menu' )
				. ' WHERE ' . $this->db->nameQuote( 'home' ) . ' = ' . $this->db->quote( '1' );
		$this->db->setQuery( $query );

		return $this->db->loadResult();
	}

	/**
	 * There might be issues with the admin menu
	 */
	private function checkAdminMenu()
	{
		if( $this->joomlaVersion >= '1.6' && $this->komentoComponentId )
		{
			$query	= 'UPDATE '. $this->db->nameQuote( '#__menu' )
					. ' SET ' . $this->db->nameQuote( 'component_id' ) . ' = ' . $this->db->quote( $this->komentoComponentId )
					. ' WHERE ' . $this->db->nameQuote( 'client_id' ) . ' = ' . $this->db->quote( 1 )
					. ' AND ' . $this->db->nameQuote( 'title' ) . ' LIKE ' . $this->db->quote( 'com_komento%' )
					. ' AND ' . $this->db->nameQuote( 'component_id' ) . ' != ' . $this->komentoComponentId;
			$this->db->setQuery( $query );
			$this->db->query();
		}
	}

	/**
	 * Install default plugins
	 */
	private function checkPlugins()
	{
		$result = array();

		if($this->joomlaVersion > '1.5')
		{
			//$plugins = $this->manifest->xpath('plugins/plugin');
			$plugins = $this->manifest->plugins;

			if( $plugins instanceof JXMLElement && count($plugins) )
			{
				foreach ($plugins->plugin as $plugin)
				{
					$plgDir = $this->installPath.DS.'plugins'.DS.$plugin->getAttribute('plugin');

					if( JFolder::exists($plgDir) )
					{
						$jinstaller = new JInstaller;
						$result[]	= $jinstaller->install($plgDir);

						$type = (string) $jinstaller->manifest->attributes()->type;

						if (count($jinstaller->manifest->files->children()))
						{
							foreach ($jinstaller->manifest->files->children() as $file)
							{
								if ((string) $file->attributes()->$type)
								{
									$element = (string) $file->attributes()->$type;
									break;
								}
							}
						}

						$query	= ' UPDATE `#__extensions` SET `enabled` = ' . $this->db->quote( 1 )
								. ' WHERE `element` = ' . $this->db->quote( $element )
								. ' AND `folder` = ' . $this->db->quote( $jinstaller->manifest->getAttribute('group') )
								. ' AND `type` = ' . $this->db->quote( 'plugin' );
						$this->db->setQuery( $query );
						$result[] = $this->db->query();
					}
				}
			}
		}
		else
		{
			$plugins = $this->jinstaller->_adapters['component']->manifest->getElementByPath('plugins');

			if( $plugins instanceof JSimpleXMLElement && count($plugins->children()) )
			{
				foreach ($plugins->children() as $plugin)
				{
					$plgDir = $this->installPath.DS.'plugins'.DS.$plugin->attributes('plugin');

					if( JFolder::exists($plgDir) )
					{
						$jinstaller = new JInstaller;
						$result[]	= $jinstaller->install($plgDir);

						$type = $jinstaller->_adapters['plugin']->manifest->attributes('type');

						// Set the installation path
						$element = $jinstaller->_adapters['plugin']->manifest->getElementByPath('files');
						if (is_a($element, 'JSimpleXMLElement') && count($element->children())) {
							$files = $element->children();
							foreach ($files as $file) {
								if ($file->attributes($type)) {
									$element = $file->attributes($type);
									break;
								}
							}
						}

						$query	= 'UPDATE `#__plugins` SET `published` = ' . $this->db->quote( 1 )
								. ' WHERE `element` = ' . $this->db->quote( $element )
								. ' AND `folder` = ' . $this->db->quote( $plugin->attributes('group') );
						$this->db->setQuery($query);
						$this->db->query();
					}
				}
			}
		}

		foreach ($result as $value)
		{
			if( !$value )
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Install default modules
	 */
	private function checkModules()
	{
		$joomlaVersion = JVersion::isCompatible('1.6.0') ? '>1.5' : '1.5';
		$result = array();

		if($this->joomlaVersion > '1.5')
		{
			$modules = $this->manifest->modules;

			if( $modules instanceof JXMLElement && count($modules) )
			{
				foreach ($modules->module as $module)
				{
					$modDir = $this->installPath.DS.'modules'.DS.$module->getAttribute('module');

					if( JFolder::exists($modDir) )
					{
						$jinstaller = new JInstaller;
						$result[]	= $jinstaller->install($modDir);
					}
				}
			}
		}
		else
		{
			$modules = $this->jinstaller->_adapters['component']->manifest->getElementByPath('modules');

			if( $modules instanceof JSimpleXMLElement && count($modules->children()) )
			{
				foreach ($modules->children() as $module)
				{
					$modDir = $this->installPath.'/modules/'.$module->attributes('module');

					if( JFolder::exists($modDir) )
					{
						$jinstaller = new JInstaller;
						$result[]	= $jinstaller->install($modDir);
					}
				}
			}
		}

		foreach ($result as $value)
		{
			if( !$value )
			{
				return false;
			}
		}

		return true;
	}

	private function extract( $archivename, $extractdir )
	{
		$archivename= JPath::clean( $archivename );
		$extractdir	= JPath::clean( $extractdir );

		return JArchive::extract( $archivename, $extractdir );
	}

	/**
	 * Install the foundry folder
	 */
	private function checkMedia()
	{
		// Copy media/com_komento
		// Overwrite all
		$mediaSource	= $this->installPath . DS . 'media' . DS . 'com_komento';
		$mediaDestina	= JPATH_ROOT . DS . 'media' . DS . 'com_komento';

		if( !JFolder::copy($mediaSource, $mediaDestina, '', true) )
		{
			return false;
		}


		// Copy media/foundry
		// Overwrite only if version is newer
		$mediaSource	= $this->installPath . DS . 'media' . DS . 'foundry';
		$mediaDestina	= JPATH_ROOT . DS . 'media' . DS . 'foundry';
		$overwrite		= false;
		$incomingVersion = '';
		$installedVersion = '';

		if(! JFolder::exists( $mediaDestina ) )
		{
			// foundry folder not found. just copy foundry folde without need to check.
			if (! JFolder::copy($mediaSource, $mediaDestina, '', true) )
			{
				return false;
			}

			return true;
		}

		// We don't have a a constant of Foundry's version, so we'll
		// find the folder name as the version number. We assumed there's
		// only ONE folder in foundry that come with the installer.
		$folder	= JFolder::folders($mediaSource);

		if(	!($incomingVersion = (string) JFile::read( $mediaSource . DS . $folder[0] . DS . 'version' )) )
		{
			// can't read the version number
			return false;
		}

		if( !JFile::exists($mediaDestina . DS . $folder[0] . DS . 'version')
			|| !($installedVersion = (string) JFile::read( $mediaDestina . DS . $folder[0] . DS . 'version' )) )
		{
			// foundry version not exists or need upgrade
			$overwrite = true;
		}

		$incomingVersion	= preg_replace('/[^a-zA-Z0-9\.]/i', '', $incomingVersion);
		$installedVersion	= preg_replace('/[^a-zA-Z0-9\.]/i', '', $installedVersion);

		if( $overwrite || version_compare($incomingVersion, $installedVersion) > 0 )
		{
			if( !JFolder::copy($mediaSource . DS . $folder[0], $mediaDestina . DS . $folder[0], '', true) )
			{
				return false;
			}
		}

		return true;
	}

	private function getJoomlaVersion()
	{
		$jVerArr	= explode('.', JVERSION);
		$jVersion	= $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}

	private function setMessage( $msg, $type )
	{
		$this->messages[] = array( 'type' => strtolower($type), 'message' => $msg );
	}

	public function getMessages()
	{
		return $this->messages;
	}
}


class KomentoDatabaseUpdate
{
	protected $db	= null;

	public function __construct()
	{
		$this->db	= JFactory::getDBO();
	}

	public function update()
	{
		// Reset and Alter Activities Table
		// Added in #[3c2d4f952a2bac28bb5da5aaa6d11e8576a3a2db], 18 April 2012
		if( $this->isColumnExists( '#__komento_activities', 'title' ) )
		{
			$query = 'ALTER TABLE `#__komento_activities` DROP COLUMN `title`';
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}
		if( $this->isColumnExists( '#__komento_activities', 'url' ) )
		{
			$query = 'ALTER TABLE `#__komento_activities` DROP COLUMN `url`';
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}
		if( $this->isColumnExists( '#__komento_activities', 'component' ) )
		{
			$query = 'ALTER TABLE `#__komento_activities` DROP COLUMN `component`';
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}
		if( $this->isColumnExists( '#__komento_activities', 'cid' ) )
		{
			$query = 'DELETE FROM `#__komento_activities`';
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;

			$query = 'ALTER TABLE `#__komento_activities` DROP COLUMN `cid`';
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}
		if( !$this->isColumnExists( '#__komento_activities', 'comment_id' ) )
		{
			$query = 'ALTER TABLE  `#__komento_activities` ADD `comment_id` BIGINT(20) NOT NULL AFTER `type`';
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}

		// Fix reports menu link
		// Added in #[87179de3ebc4c226470d1d8f6d83e35daaa715c6], 16 May 2012
		if( $this->getJoomlaVersion() >= '1.6' )
		{
			$query = 'UPDATE `#__menu` SET `link` = ' . $this->db->quote( 'index.php?option=com_komento&view=reports' ) . ' WHERE `title` = ' . $this->db->quote( 'COM_KOMENTO_MENU_REPORTS' );
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}
		else
		{
			$query = 'UPDATE `#__components` SET `admin_menu_link` = ' . $this->db->quote( 'option=com_komento&view=reports' ) . ' WHERE `name` = ' . $this->db->quote( 'COM_KOMENTO_MENU_REPORTS' );
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}

		// Add published column to subscription table
		// Added in #[04b86b4c3feb30bda179a83873e7dcf165dfa668], 23 May 2012
		if( !$this->isColumnExists( '#__komento_subscription', 'published' ) )
		{
			$query = 'ALTER TABLE  `#__komento_subscription` ADD `published` tinyint(1) NOT NULL DEFAULT 0 AFTER `created`';
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}

		// Add hashkeys table
		// Added in #[7daebcea665a143118dc8a6b3b88ee7b03f6b3a7], 19 June 2012
		if( !$this->isTableExists( '#__komento_hashkeys' ) )
		{
			$query = 'CREATE TABLE IF NOT EXISTS `#__komento_hashkeys` (
				`id` bigint(11) NOT NULL auto_increment,
				`uid` bigint(11) NOT NULL,
				`type` varchar(255) NOT NULL,
				`key` text NOT NULL,
				PRIMARY KEY  (`id`),
				KEY `uid` (`uid`),
				KEY `type` (`type`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';

			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}

		// Add uploads table
		// Added in #[0df9f868f227d2db74c16cd9eba0a7e89e882ab7], 21 June 2012
		if( !$this->isTableExists( '#__komento_uploads' ) )
		{
			$query = 'CREATE TABLE IF NOT EXISTS `#__komento_uploads` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`filename` text NOT NULL,
				`hashname` text NOT NULL,
				`path` text NULL,
				`created` datetime NOT NULL,
				`created_by` bigint(20) unsigned DEFAULT \'0\',
				`published` tinyint(1) NOT NULL,
				`mime` text NOT NULL,
				`size` text NOT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';

			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}

		// Add UID column to uploads table
		// Added in #[d1fdeaa0f5ab7dd1874cd88a38bdc869e71c7aa0], 25 June 2012
		if( !$this->isColumnExists( '#__komento_uploads', 'uid' ) )
		{
			$query = 'ALTER TABLE  `#__komento_uploads` ADD `uid` int(11) NULL AFTER `id`';
			$this->db->setQuery( $query );
			if( !$this->db->query() ) return false;
		}

		return true;
	}

	private function isTableExists( $tableName )
	{
		$query	= 'SHOW TABLES LIKE ' . $this->db->quote($tableName);
		$this->db->setQuery( $query );

		return (boolean) $this->db->loadResult();
	}

	private function isColumnExists( $tableName, $columnName )
	{
		$query	= 'SHOW FIELDS FROM ' . $this->db->nameQuote( $tableName );
		$this->db->setQuery( $query );

		$fields	= $this->db->loadObjectList();

		$result = array();

		foreach( $fields as $field )
		{
			$result[ $field->Field ]	= preg_replace( '/[(0-9)]/' , '' , $field->Type );
		}

		if( array_key_exists($columnName, $result) )
		{
			return true;
		}

		return false;
	}

	private function isIndexKeyExists( $tableName, $indexName )
	{
		$query	= 'SHOW INDEX FROM ' . $this->db->nameQuote( $tableName );
		$this->db->setQuery( $query );
		$indexes	= $this->db->loadObjectList();

		$result = array();

		foreach( $indexes as $index )
		{
			$result[ $index->Key_name ]	= preg_replace( '/[(0-9)]/' , '' , $index->Column_name );
		}

		if( array_key_exists($indexName, $result) )
		{
			return true;
		}

		return false;
	}

	private function getJoomlaVersion()
	{
		$jVerArr	= explode('.', JVERSION);
		$jVersion	= $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}
}
