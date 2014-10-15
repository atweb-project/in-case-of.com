<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

// Don't allow direct linking
if (!defined( '_VALID_MOS' ) AND !defined('_JEXEC')) die( 'Direct Access to this location is not allowed.' );

class remositoryInstaller {

	public function permission_all_from_dir($Dir){
		// delete everything in the directory
		$handle = @opendir($Dir);
		if ($handle) {
			while (($file = readdir($handle)) !== false) {
				if ($file == '.' || $file == '..') continue;
				$newpath = $Dir.$file;
				if (is_dir($newpath)) $this->permission_all_from_dir($newpath.'/');
				else $this->setFilePerms ($newpath);
			}
		}
		@closedir($handle);
		$this->setDirPerms($Dir);
	}

	public function setDirPerms ($dir) {
		$interface = remositoryInterface::getInstance();
   		$origmask = @umask(0);
		if ($interface->getCfg('dirperms')) {
	    	$mode = octdec($interface->getCfg('dirperms'));
			$result = @chmod($dir, $mode);
		}
		else $result = @chmod($dir,0755);
		@umask($origmask);
		return $result;
	}

	public function setFilePerms ($file) {
		$interface = remositoryInterface::getInstance();
   		$origmask = @umask(0);
		if ($interface->getCfg('fileperms')) {
	    	$mode = octdec($interface->getCfg('fileperms'));
	    	$result = @chmod($file, $mode);
		}
		else $result = @chmod($file,0644);
		@umask($origmask);
		return $result;
	}

	public function makeDefaultContainer () {
		if (0 == remositoryContainerManager::getInstance()->count()) {
			$container = new remositoryContainer();
			$container->name = _DOWN_SAMPLE;
			$container->description = _DOWN_SAMPLE_DESCRIPTION;
			$container->published = 1;
			$container->saveValues();
			$authoriser = aliroAuthorisationAdmin::getInstance();
			$authoriser->permit ('Registered', 2, 'upload', 'remosFolder', $container->id);
			$authoriser->permit ('Nobody', 2, 'edit', 'remosFolder', $container->id);
			$authoriser->permit ('Nobody', 2, 'selfApprove', 'remosFolder', $container->id);
		}
	}
	
	public function approverPermissions ($approver) {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		if (!defined('_ALIRO_IS_PRESENT')) {
			$database->setQuery("SELECT COUNT(*) FROM #__permissions WHERE action = 'selfApprove' AND subject_type = 'remosFolder'");
			if (0 == $database->loadResult()) {
				$database->setQuery("INSERT INTO #__permissions(SELECT 0 , '$approver', 2, 'selfApprove', 'remosFolder', id, 0 FROM #__downloads_containers)");
				$database->query();
			}
		}
	}
	
	public function dbcreate () {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$mosConfig_live_site = $interface->getCfg('live_site');
		$mosConfig_absolute_path = _REMOS_ABSOLUTE_PATH;
		
		$maketable['repository'] = "CREATE TABLE IF NOT EXISTS `#__downloads_repository` (
						  `id` int(11) NOT NULL auto_increment,
						  `name` varchar(255) NOT NULL default '',
						  `alias` varchar(255) NOT NULL default '',
						  `sequence` int(11) NOT NULL default '0',
						  `windowtitle` varchar(255) NOT NULL default '',
						  `keywords` varchar(255) NOT NULL default '',
						  `version` char(10) NOT NULL default '',
						  `Use_Database` smallint(6) NOT NULL default '1',
						  `Main_Page_Title` varchar(100) NOT NULL default 'Remository',
						  `tabclass` varchar(255) NOT NULL default 'sectiontableentry2,sectiontableentry1',
						  `tabheader` varchar(255) NOT NULL default 'sectiontableheader',
						  `headerpic` varchar(255) NOT NULL default '$mosConfig_live_site/components/com_remository/images/header.gif',
						  `ExtsOk` varchar(255) NOT NULL default 'txt,exe,tar,gz,rar,zip,png,gif,jpg,pdf,doc',
						  `ExtsDisplay` varchar(255) NOT NULL default '',
						  `ExtsAudio` varchar(255) NOT NULL default '',
						  `ExtsVideo` varchar(255) NOT NULL default '',
						  `Classification_Types` varchar(255) NOT NULL default '',
						  `Scribd` varchar(30) NOT NULL default '',
						  `Down_Path` varchar(255) NOT NULL default '$mosConfig_absolute_path/components/com_remository/downloads',
						  `Up_Path` varchar(255) NOT NULL default '$mosConfig_absolute_path/components/com_remository/uploads',
						  `Large_Text_Len` int(11) NOT NULL default '500',
						  `Small_Text_Len` smallint(6) NOT NULL default '150',
						  `Small_Image_Width` smallint(6) NOT NULL default '100',
						  `Small_Image_Height` smallint(6) NOT NULL default '100',
						  `Large_Image_Width` smallint(6) NOT NULL default '600',
						  `Large_Image_Height` smallint(6) NOT NULL default '600',
						  `MaxSize` int(11) NOT NULL default '5000',
						  `Max_Up_Per_Day` smallint(6) NOT NULL default '5',
						  `Max_Down_Per_Day` smallint(6) NOT NULL default '5',
						  `Max_Down_Reg_Day` smallint(6) NOT NULL default '10',
						  `Max_Down_File_Day` smallint(6) NOT NULL default '2',
						  `Count_Down` tinyint(3) unsigned NOT NULL default '0',
						  `Featured_Number` int(11) unsigned NOT NULL default '0',
						  `Max_Up_Dir_Space` int(11) NOT NULL default '50000',
						  `Favourites_Max` smallint(6) NOT NULL default '5',
						  `Max_Thumbnails` smallint(6) NOT NULL default '0',
						  `Min_Comment_length` smallint(6) NOT NULL default '1',
						  `Make_Auto_Thumbnail` tinyint(3) unsigned NOT NULL default '0',
						  `Default_Version` char(20) NOT NULL default '',
						  `Main_Authors` text NOT NULL,
						  `Author_Threshold` smallint(6) NOT NULL default '0',
						  `Date_Format` char(20) NOT NULL default '',
						  `Set_date_locale` varchar(20) NOT NULL,
						  `Force_Language` varchar(20) NOT NULL,
						  `Show_all_containers` tinyint(3) unsigned NOT NULL default '0',
						  `Anti_Leach` tinyint(3) unsigned NOT NULL default '0',
						  `Allow_Up_Overwrite` tinyint(3) unsigned NOT NULL default '1',
						  `Allow_User_Sub` tinyint(3) unsigned NOT NULL default '1',
						  `Allow_User_Edit` tinyint(3) unsigned NOT NULL default '1',
						  `Allow_User_Delete` tinyint(3) unsigned NOT NULL default '0',
						  `Allow_User_Up` tinyint(3) unsigned NOT NULL default '1',
						  `Enable_Admin_Autoapp` tinyint(3) unsigned NOT NULL default '1',
						  `Enable_User_Autoapp` tinyint(3) unsigned NOT NULL default '0',
						  `Allow_Comments` tinyint(3) unsigned NOT NULL default '1',
						  `Allow_Votes` tinyint(3) unsigned NOT NULL default '1',
						  `Download_Browser_Save` tinyint(3) unsigned NOT NULL default '0',
						  `Allow_Large_Images` tinyint(3) unsigned NOT NULL default '1',
						  `Activate_AEC` tinyint(3) unsigned NOT NULL default '0',
						  `Remository_Pathway` tinyint(3) unsigned NOT NULL default '0',
						  `Enable_List_Download` tinyint(3) unsigned NOT NULL default '0',
						  `Audio_Download` tinyint(3) unsigned NOT NULL default '0',
						  `Video_Download` tinyint(3) unsigned NOT NULL default '0',
						  `User_Remote_Files` tinyint(3) unsigned NOT NULL default '0',
						  `See_Containers_no_download` tinyint(3) unsigned NOT NULL default '1',
						  `See_Files_no_download` tinyint(3) unsigned NOT NULL default '1',
						  `Show_RSS_feeds` tinyint(3) unsigned NOT NULL default '1',
						  `Allow_File_Info` tinyint(3) unsigned NOT NULL default '1',
						  `Show_Footer` tinyint(3) unsigned NOT NULL default '1',
						  `Show_File_Folder_Counts` tinyint(3) unsigned NOT NULL default '1',
						  `Display_PageHeading` tinyint(3) unsigned NOT NULL default '1',
						  `Display_FolderListHeading` tinyint(3) unsigned NOT NULL default '1',
						  `Display_FolderIcons` tinyint(3) unsigned NOT NULL default '1',
						  `Display_FileIcons` tinyint(3) unsigned NOT NULL default '1',
						  `Display_FileListHeading` tinyint(3) unsigned NOT NULL default '1',
						  `Send_Sub_Mail` tinyint(3) unsigned NOT NULL default '1',
						  `Sub_Mail_Alt_Addr` varchar(255) NOT NULL default '',
						  `Sub_Mail_Alt_Name` varchar(100) NOT NULL default '',
						  `Time_Stamp` varchar(15) NOT NULL default '1119386419',
						  `Profile_URI` varchar(255) NOT NULL,
						  `download_text` text NOT NULL,
						  `preamble` text NOT NULL,
						  `Default_Licence` text NOT NULL,
						  `customizer` text NOT NULL,
						  `custom_names` text NOT NULL,
						  `Cron_Timer` int(11) NOT NULL default '0',
						  `Real_With_ID` tinyint(3) unsigned NOT NULL default '1',
						  `Immediate_Download` tinyint(3) unsigned NOT NULL default '0',
						  `Use_CMS_Groups` tinyint(3) unsigned NOT NULL default '0',
						  `template` varchar(25) NOT NULL default 'default',
						  `Show_SubCategories` tinyint(3) unsigned NOT NULL default '0',
						  UNIQUE KEY `id` (`id`)
						)";
		$maketable['files'] = "CREATE TABLE IF NOT EXISTS `#__downloads_files` (
  						`id` int NOT NULL auto_increment,
  						`sequence` int NOT NULL default 0,
  						`windowtitle` varchar(255) NOT NULL default '',
  						`keywords` varchar(255) NOT NULL default '',
  						  `metatype` smallint NOT NULL default 0,
						  `realname` varchar(255) NOT NULL default '',
						  `realwithid` tinyint unsigned NOT NULL default 0,
						  `islocal` tinyint unsigned NOT NULL default 1,
						  `containerid` smallint NOT NULL default 0,
						  `userid` int NOT NULL default 0,
						  `filepath` varchar (255) NOT NULL default '',
						  `filesize` varchar(255) NOT NULL default '',
						  `filetype` varchar(255) NOT NULL default '',
						  `filetitle` varchar(255) NOT NULL default '',
						  `description` text NOT NULL default '',
						  `smalldesc` text NOT NULL default '',
						  `autoshort` tinyint unsigned NOT NULL default 1,
						  `license` text NOT NULL default '',
						  `licenseagree` tinyint unsigned NOT NULL default 0,
						  `price` int NOT NULL default 0,
						  `currency` char(3) NOT NULL default '',
						  `downloads` int NOT NULL default 0,
						  `url` varchar (255) NOT NULL default '',
						  `icon` varchar(50) NOT NULL default '',
						  `published` tinyint unsigned NOT NULL default 1,
						  `registered` tinyint unsigned NOT NULL default 2,
						  `userupload` tinyint unsigned NOT NULL default 3,
						  `download_text` text NOT NULL default '',
						  `recommended` tinyint unsigned NOT NULL default 0,
						  `recommend_text` text NOT NULL default '',
						  `featured` tinyint NOT NULL default 0,
						  `featured_st_date` date NOT NULL default '0000-00-00',
						  `featured_end_date` date NOT NULL default '0000-00-00',
						  `featured_priority` smallint NOT NULL default 0,
						  `featured_seq` smallint NOT NULL default 0,
						  `featured_text` text NOT NULL default '',
						  `opsystem` varchar (50) NOT NULL default '',
						  `legaltype` varchar (50)NOT NULL default '',
						  `requirements` text NOT NULL default '',
						  `company` varchar (255) NOT NULL default '',
						  `releasedate` date NOT NULL default '0000-00-00',
						  `languages` text NOT NULL default '',
						  `company_URL` varchar (255) NOT NULL default '',
						  `translator` varchar (255) NOT NULL default '',
						  `fileversion` varchar(50) NOT NULL default '',
						  `fileauthor` varchar(100) NOT NULL default '',
						  `author_URL` varchar (255) NOT NULL default '',
						  `filedate` datetime NOT NULL default '0000-00-00 00:00:00',
						  `filehomepage` varchar(255) NOT NULL default '',
						  `screenurl` varchar(255) NOT NULL default '',
						  `plaintext` tinyint unsigned NOT NULL default 0,
						  `isblob` tinyint unsigned NOT NULL default 0,
						  `chunkcount` int NOT NULL default 0,
						  `groupid` smallint NOT NULL default 0,
						  `editgroup` smallint NOT NULL default 0,
						  `custom_1` varchar(255) NOT NULL default '',
						  `custom_2` varchar(255) NOT NULL default '',
						  `custom_3` text NOT NULL default '',
						  `custom_4` int NOT NULL default 0,
						  `custom_5` datetime NOT NULL default '0000-00-00',
						  `oldid` int NOT NULL default 0,
						  `submittedby` mediumint NOT NULL default 0,
						  `submitdate` datetime NOT NULL default '0000-00-00',
						  `custom_values` text NOT NULL default '',
						  UNIQUE KEY `id` (`id`),
						  KEY `filetitle` (`filetitle`),
						  KEY `url` (`url`),
						  KEY `containerid` (`containerid`,`published`),
						  KEY `recommended` (`containerid`,`recommended`,`published`),
						  KEY `featured` (`containerid`,`featured`,`published`,`featured_st_date`,`featured_end_date`),
						  KEY `opsystem` (`containerid`,`opsystem`,`published`),
						  FULLTEXT `words` (`filetitle`,`description`,`smalldesc`,`fileauthor`)
					) ENGINE=MyISAM";
		$maketable['reviews'] = "CREATE TABLE IF NOT EXISTS `#__downloads_reviews` (
						  `id` int NOT NULL auto_increment,
						  `sequence` int NOT NULL default 0,
						  `windowtitle` varchar(255) NOT NULL default '',
						  `keywords` varchar(255) NOT NULL default '',
						  `component` varchar (255) NOT NULL default '',
						  `itemid` int NOT NULL default 0,
						  `userid` mediumint NOT NULL default 0,
						  `userURL` varchar(255) NOT NULL default '',
						  `title` varchar (255) NOT NULL default '',
						  `comment` text NOT NULL default '',
						  `fullreview` text NOT NULL default '',
						  `date` datetime NOT NULL default '0000-00-00',
						  UNIQUE KEY `id` (`id`),
						  KEY `userid` (`component`,`itemid`,`userid`)
						)";
		$maketable['containers'] = "CREATE TABLE IF NOT EXISTS `#__downloads_containers` (
						  `id` int(255) NOT NULL auto_increment,
  						  `sequence` int NOT NULL default '0',
  						  `windowtitle` varchar(255) NOT NULL default '',
						  `keywords` varchar(255) NOT NULL default '',
						  `parentid` smallint(255) NOT NULL default 0,
						  `name` varchar(255) NOT NULL default '',
						  `filepath` varchar(255) NOT NULL default '',
						  `published` tinyint(1) NOT NULL default 1,
						  `description` text NOT NULL default '',
						  `filecount` smallint NOT NULL default 0,
						  `foldercount` smallint NOT NULL default 0,
						  `icon` varchar(50) NOT NULL default '',
						  `registered` tinyint unsigned NOT NULL default 2,
						  `userupload` tinyint unsigned NOT NULL default 3,
						  `countdown` tinyint(3) unsigned NOT NULL default '0',
						  `childcountdown` tinyint(3) unsigned NOT NULL default '0',
						  `countup` tinyint(3) unsigned NOT NULL default '0',
						  `childcountup` tinyint(3) unsigned NOT NULL default '0',
						  `plaintext` tinyint unsigned NOT NULL default 0,
						  `groupid` smallint NOT NULL default 0,
						  `editgroup` smallint NOT NULL default 0,
						  `adminauto` tinyint unsigned NOT NULL default 0,
						  `userauto` tinyint unsigned NOT NULL default 0,
						  `autogroup` smallint NOT NULL default 0,
						  `userid` int NOT NULL default 0,
						  UNIQUE KEY `id` (`id`),
						  KEY `parentid` (`parentid`,`published`)
						)";
		$maketable['credits'] = "CREATE TABLE IF NOT EXISTS `#__downloads_credits` (
						  `id` int(11) NOT NULL auto_increment,
						  `userid` int(11) NOT NULL default '0',
						  `credits` int(11) NOT NULL default '0',
						  PRIMARY KEY  (`id`),
						  KEY `userid` (`userid`)
						)";
		$maketable['log'] = "CREATE TABLE IF NOT EXISTS `#__downloads_log` (
						  `id` int NOT NULL auto_increment,
						  `type` tinyint unsigned NOT NULL default 0,
						  `date` datetime NOT NULL default '0000-00-00',
						  `userid` mediumint NOT NULL default 0,
						  `fileid` int NOT NULL default 0,
						  `value` int NOT NULL default 0,
						  `price` int NOT NULL default 0,
						  `ipaddress` char (15) NOT NULL default '',
							UNIQUE KEY `id` (`id`),
							KEY `userid` (`type`,`userid`),
							KEY `fileid` (`type`,`fileid`),
							KEY `ipaddress` (`type`,`ipaddress`,`date`)
						)";
		$maketable['text'] = "CREATE TABLE IF NOT EXISTS `#__downloads_text` (
						  `id` int NOT NULL auto_increment,
						  `fileid` int NOT NULL default 0,
						  `filetext` longtext NOT NULL,
							UNIQUE KEY `id` (`id`),
							KEY `fileid` (`fileid`),
							FULLTEXT `words` (`filetext`)
						) ENGINE=MyISAM";
		$maketable['blob'] = "CREATE TABLE IF NOT EXISTS `#__downloads_blob` (
						  `id` int NOT NULL auto_increment,
						  `fileid` int NOT NULL default 0,
						  `chunkid` int NOT NULL default 0,
						  `bloblength` int NOT NULL default 0,
						  `datachunk` mediumblob NOT NULL,
							UNIQUE KEY `id` (`id`),
							UNIQUE KEY `filechunk` (`fileid`,`chunkid`),
							KEY `size` (`fileid`,`bloblength`)
						)";
		$maketable['classify'] = "CREATE TABLE IF NOT EXISTS `#__downloads_classify` (
						  `id` int(11) NOT NULL auto_increment,
						  `sequence` int(11) NOT NULL default '0',
  						  `windowtitle` varchar(255) NOT NULL default '',
						  `keywords` varchar(255) NOT NULL default '',
						  `frequency` int(11) NOT NULL default '0',
						  `published` tinyint(3) unsigned NOT NULL default '0',
						  `hidden` tinyint(3) unsigned NOT NULL default '0',
						  `type` varchar(30) NOT NULL,
						  `name` varchar(100) NOT NULL,
						  `description` text NOT NULL,
						  PRIMARY KEY  (`id`)
						)";
		$maketable['fileclassify'] = "CREATE TABLE IF NOT EXISTS `#__downloads_file_classify` (
						  `file_id` int(11) NOT NULL default '0',
						  `classify_id` int(11) NOT NULL default '0',
						  PRIMARY KEY  (`file_id`,`classify_id`)
						)";
		$maketable['email'] = "CREATE TABLE IF NOT EXISTS `#__downloads_email` (
						  `id` int(11) NOT NULL auto_increment,
						  `userid` int(11) NOT NULL default '0',
						  `ipaddress` varchar(15) NOT NULL,
						  `role` varchar(60) NOT NULL,
						  `email` varchar(255) NOT NULL,
						  `stamp` datetime NOT NULL,
						  `uniqid` char(32) NOT NULL,
						  PRIMARY KEY  (`id`),
						  UNIQUE KEY `uniqid` (`uniqid`)
						)";
		$maketable['assignments'] = "CREATE TABLE IF NOT EXISTS `#__assignments` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `access_type` varchar(60) NOT NULL,
						  `access_id` text NOT NULL,
						  `role` varchar(60) NOT NULL,
						  PRIMARY KEY (`id`),
						  KEY `access_type` (`access_type`,`access_id`(60),`role`)
						)";
		$maketable['permissions'] = "CREATE TABLE IF NOT EXISTS `#__permissions` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `role` varchar(60) NOT NULL,
						  `control` tinyint(3) unsigned NOT NULL DEFAULT '0',
						  `action` varchar(60) NOT NULL,
						  `subject_type` varchar(60) NOT NULL,
						  `subject_id` text NOT NULL,
						  `system` smallint(5) unsigned NOT NULL DEFAULT '0',
						  PRIMARY KEY (`id`),
						  KEY `role_type` (`role`,`action`,`subject_type`,`subject_id`(60)),
						  KEY `subaction` (`subject_type`,`action`,`subject_id`(60))
						)";
		foreach ($maketable as $remtable) {
			$database->setQuery($remtable);
			$database->query();
		}
	}
	
	public function dbupgrade () {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();

		if (!defined('_ALIRO_IS_PRESENT')) {
			$database->setQuery("SELECT COUNT(*) FROM #__permissions");
			if (0 == $database->loadResult()) {
				$database->setQuery("INSERT INTO #__permissions(SELECT 0 , 'Nobody', 2, 'edit', 'remosFolder', id, 0 FROM #__downloads_containers)");
				$database->query();
				$database->setQuery("INSERT INTO #__permissions(SELECT 0 , 'Registered', 2, 'upload', 'remosFolder', id, 0 FROM #__downloads_containers WHERE (userupload & 1) AND NOT (registered & 1))");
				$database->query();
				$database->setQuery("INSERT INTO #__permissions(SELECT 0 , 'Registered', 2, 'download', 'remosFolder', id, 0 FROM #__downloads_containers WHERE (userupload & 2) AND NOT (registered & 2))");
				$database->query();
				$database->setQuery("INSERT INTO #__permissions(SELECT 0 , 'Nobody', 2, 'upload', 'remosFolder', id, 0 FROM #__downloads_containers WHERE NOT(userupload & 1) AND NOT (registered & 1))");
				$database->query();
				$database->setQuery("INSERT INTO #__permissions(SELECT 0 , 'Nobody', 2, 'download', 'remosFolder', id, 0 FROM #__downloads_containers WHERE NOT(userupload & 2) AND NOT (registered & 2))");
				$database->query();
			}
		}

		$database->setQuery ("SHOW COLUMNS FROM #__downloads_repository");
		$fields = $database->loadObjectList();
		$fieldnames = array();
		foreach ($fields as $field) $fieldnames[] = $field->Field;
		
		$database->setQuery("ALTER TABLE #__downloads_repository MODIFY id int(11) NOT NULL auto_increment");
		$database->query();
		$database->setQuery("UPDATE #__downloads_repository AS r1 LEFT JOIN #__downloads_repository AS r2 ON r2.id = 1 SET r1.id = 1 WHERE r1.id = 0 AND r2.id IS NULL");
		$database->query();

		if (!in_array('Use_Database', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Use_Database` smallint NOT NULL default 1 AFTER `version`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('keywords', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `keywords` varchar(255) NOT NULL default \'\' AFTER `windowtitle`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Up_Path', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Up_Path` varchar(255) NOT NULL default \'\' AFTER `Down_Path`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Large_Image_Width', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Large_Image_Width` smallint NOT NULL default 600 AFTER `Small_Image_Height`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Large_Image_Height', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Large_Image_Height` smallint NOT NULL default 600 AFTER `Large_Image_Width`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Max_Thumbnails', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Max_Thumbnails` smallint NOT NULL default 0 AFTER `Favourites_Max`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Allow_Large_Images', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Allow_Large_Images` tinyint unsigned NOT NULL default 1 AFTER `Allow_Votes`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('download_text', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `download_text` text NOT NULL AFTER `Time_Stamp`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Max_Down_Per_Day', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Max_Down_Per_Day` int NOT NULL default 5 AFTER `Max_Up_Per_Day`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Max_Down_Reg_Day', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Max_Down_Reg_Day` int NOT NULL default 10 AFTER `Max_Down_Per_Day`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Max_Down_File_Day', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Max_Down_File_Day` int NOT NULL default 2 AFTER `Max_Down_Per_Day`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Allow_User_Delete', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Allow_User_Delete` tinyint unsigned NOT NULL default 0 AFTER `Allow_User_Edit`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Make_Auto_Thumbnail', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Make_Auto_Thumbnail` tinyint unsigned NOT NULL default 0 AFTER `Max_Thumbnails`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('preamble', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `preamble` text NOT NULL AFTER `download_text`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Default_Licence', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Default_Licence` text NOT NULL AFTER `preamble`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('customizer', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `customizer` text NOT NULL AFTER `Default_Licence`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('ExtsDisplay', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `ExtsDisplay` varchar(255) NOT NULL default  \'\' AFTER `ExtsOk`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Scribd', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Scribd` varchar(30) NOT NULL default  \'\' AFTER `ExtsDisplay`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Show_RSS_feeds', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Show_RSS_feeds` tinyint unsigned NOT NULL default 1 AFTER `See_Files_no_download`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Classification_Types', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Classification_Types` varchar(255) NOT NULL default \'\' AFTER `ExtsDisplay`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Remository_Pathway', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Remository_Pathway` tinyint unsigned NOT NULL default 0 AFTER `Allow_Large_Images`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('name', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `name` varchar(255) NOT NULL default \'\' AFTER `id`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('alias', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `alias` varchar(255) NOT NULL default \'\' AFTER `name`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Allow_File_Info', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Allow_File_Info` tinyint(3) unsigned NOT NULL default 1 AFTER `Show_RSS_feeds`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Show_Footer', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Show_Footer` tinyint(3) unsigned NOT NULL default 1 AFTER `Allow_File_Info`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Show_File_Folder_Counts', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Show_File_Folder_Counts` tinyint(3) unsigned NOT NULL default 1 AFTER `Show_Footer`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Display_PageHeading', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Display_PageHeading` tinyint(3) unsigned NOT NULL default 1 AFTER `Show_File_Folder_Counts` ;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Display_FolderListHeading', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Display_FolderListHeading` tinyint(3) unsigned NOT NULL default 1 AFTER `Display_PageHeading` ;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Display_FolderIcons', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Display_FolderIcons` tinyint(3) unsigned NOT NULL default 1 AFTER `Display_FolderListHeading` ;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Display_FileIcons', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Display_FileIcons` tinyint(3) unsigned NOT NULL default 1 AFTER `Display_FolderIcons` ;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Display_FileListHeading', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Display_FileListHeading` tinyint(3) unsigned NOT NULL default 1 AFTER `Display_FileIcons` ;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Count_Down', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Count_Down` tinyint(3) unsigned NOT NULL default 0 AFTER `Max_Down_File_Day`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Featured_Number', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Featured_Number` int(11) unsigned NOT NULL default 0 AFTER `Count_Down`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('ExtsAudio', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `ExtsAudio` varchar(255) NOT NULL default \'\' AFTER `ExtsDisplay`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('ExtsVideo', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `ExtsVideo` varchar(255) NOT NULL default \'\' AFTER `ExtsAudio`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Audio_Download', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Audio_Download` tinyint(3) unsigned NOT NULL default 0 AFTER `Enable_List_Download`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Video_Download', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Video_Download` tinyint(3) unsigned NOT NULL default 0 AFTER `Audio_Download`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('custom_names', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `custom_names` text NOT NULL AFTER `customizer`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('Min_Comment_length', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Min_Comment_length` smallint(6) NOT NULL default 1 AFTER `Max_Thumbnails`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Main_Authors', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Main_Authors` text NOT NULL AFTER `Default_Version`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Author_Threshold', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Author_Threshold` smallint(6) NOT NULL default 0 AFTER `Main_Authors`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Main_Page_Title', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Main_Page_Title` varchar(100) NOT NULL default \'\' AFTER `Use_Database`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Activate_AEC', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Activate_AEC` tinyint(3) unsigned NOT NULL default 0 AFTER `Allow_Large_Images`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Allow_Comments', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Allow_Comments` tinyint(3) unsigned NOT NULL default 1 AFTER `Enable_User_Autoapp`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Integrate_Comments', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Integrate_Comments` tinyint(3) unsigned NOT NULL default 0 AFTER `Allow_Comments`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Profile_URI', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Profile_URI` varchar(255) NOT NULL AFTER `Time_Stamp`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Set_date_locale', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Set_date_locale` varchar(20) NOT NULL AFTER `Date_Format`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Force_Language', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Force_Language` varchar(20) NOT NULL AFTER `Set_date_locale`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Show_all_containers', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Show_all_containers` tinyint(3) unsigned NOT NULL default 0 AFTER `Force_Language`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('template', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `template` varchar(25) NOT NULL default \'default\' AFTER `custom_names`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('headerpic', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
				.' ADD `headerpic` varchar(255) NOT NULL default \'\' AFTER `tabheader`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Show_SubCategories', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Show_SubCategories` tinyint(3) unsigned NOT NULL default 0 AFTER `Show_all_containers`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Cron_Timer', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Cron_Timer` int(11) NOT NULL default 0 AFTER `custom_names`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Real_With_ID', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Real_With_ID` tinyint(3) unsigned NOT NULL default 1 AFTER `Cron_Timer`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Immediate_Download', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Immediate_Download` tinyint(3) unsigned NOT NULL default 0 AFTER `Real_With_ID`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Use_CMS_Groups', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Use_CMS_Groups` tinyint(3) unsigned NOT NULL default 0 AFTER `Immediate_Download`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Download_Browser_Save', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Download_Browser_Save` tinyint(3) unsigned NOT NULL default 0 AFTER `Allow_Votes`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Allow_Container_Add', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Allow_Container_Add` tinyint(3) unsigned NOT NULL default 0 AFTER `Allow_User_Delete`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Allow_Container_Delete', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Allow_Container_Delete` tinyint(3) unsigned NOT NULL default 0 AFTER `Allow_Container_Add`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Allow_Container_Edit', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Allow_Container_Edit` tinyint(3) unsigned NOT NULL default 0 AFTER `Allow_Container_Delete`;';
			$database->setQuery($sql);
			$database->query();
		}

		$database->setQuery ("SHOW COLUMNS FROM #__downloads_files");
		$fields = $database->loadObjectList();
		$fieldnames = array();
		foreach ($fields as $field) $fieldnames[] = $field->Field;

		if (in_array('filedate', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' MODIFY `filedate` datetime NOT NULL default \'0000-00-00 00:00:00\';';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('realwithid', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
			   	.' ADD `realwithid` tinyint unsigned NOT NULL default 0 AFTER `realname`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('keywords', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
			   	.' ADD `keywords` varchar(255) NOT NULL default \'\' AFTER `windowtitle`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('userid', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `userid` int NOT NULL default 0 AFTER `containerid`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('download_text', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `download_text` text NOT NULL AFTER `userupload`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('plaintext', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `plaintext` tinyint NOT NULL default 0 AFTER `screenurl`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('isblob', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `isblob` tinyint NOT NULL default 0 AFTER `plaintext`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('chunkcount', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `chunkcount` int NOT NULL default 0 AFTER `isblob`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('groupid', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `groupid` int NOT NULL default 0 AFTER `chunkcount`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('editgroup', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `editgroup` smallint NOT NULL default 0 AFTER `groupid`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('custom_1', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `custom_1` varchar(255) NOT NULL default \'\' AFTER `editgroup`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('custom_2', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `custom_2` varchar(255) NOT NULL default \'\' AFTER `custom_1`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('custom_3', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `custom_3` text NOT NULL AFTER custom_2;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('custom_4', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `custom_4` int NOT NULL default 0 AFTER custom_3;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('custom_5', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `custom_5` datetime NOT NULL default \'0000-00-00\' AFTER custom_4;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('metatype', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `metatype` tinyint NOT NULL default 0 AFTER keywords;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('oldid', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `oldid` int NOT NULL default 0 AFTER userid;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('publish_id', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `publish_id` varchar(50) NOT NULL default \'\' AFTER author_URL;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('publish_date', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `publish_date` date NOT NULL default \'0000-00-00\' AFTER publish_id;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('subtitle', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `subtitle` text NOT NULL AFTER filetitle;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('repnum', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `repnum` tinyint(3) unsigned NOT NULL default 1 AFTER id;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('registered', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `registered` tinyint NOT NULL default 0 AFTER `published`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('userupload', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `userupload` tinyint NOT NULL default 0 AFTER `registered`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('publish_from', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `publish_from` datetime NOT NULL default \'0000-00-00 00:00:00\' AFTER published;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('publish_to', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `publish_to` datetime NOT NULL default \'0000-00-00 00:00:00\' AFTER publish_from;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('republish_num', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `republish_num` tinyint(3) unsigned NOT NULL default 0 AFTER publish_to;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('republish_unit', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `republish_unit` tinyint(3) unsigned NOT NULL default 1 AFTER republish_num;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('listings', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `listings` int(11) unsigned NOT NULL default 0 AFTER `downloads`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('viewings', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `viewings` int(11) unsigned NOT NULL default 0 AFTER `listings`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('custom_values', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `custom_values` text NOT NULL AFTER `submitdate`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('author_email', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `author_email` varchar(255) NOT NULL default \'\' AFTER `author_URL`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		$database->setQuery ("SHOW COLUMNS FROM #__downloads_reviews");
		$fields = $database->loadObjectList();
		$fieldnames = array();
		foreach ($fields as $field) $fieldnames[] = $field->Field;

		if (!in_array('keywords', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_reviews`'
				.' ADD `keywords` varchar(255) NOT NULL default \'\' AFTER `windowtitle`;';
			$database->setQuery($sql);
			$database->query();
		}


		$database->setQuery ("SHOW COLUMNS FROM #__downloads_containers");
		$fields = $database->loadObjectList();
		$fieldnames = array();
		foreach ($fields as $field) $fieldnames[] = $field->Field;

		if (!in_array('keywords', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `keywords` varchar(255) NOT NULL default \'\' AFTER `windowtitle`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('plaintext', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `plaintext` tinyint unsigned NOT NULL default 0 AFTER `childcountup`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('groupid', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `groupid` int NOT NULL default 0 AFTER `plaintext`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('editgroup', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `editgroup` smallint NOT NULL default 0 AFTER `groupid`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('adminauto', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `adminauto` tinyint unsigned NOT NULL default 0 AFTER `editgroup`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('userauto', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `userauto` tinyint unsigned NOT NULL default 0 AFTER `adminauto`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('autogroup', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `autogroup` smallint NOT NULL default 0 AFTER `userauto`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('userid', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `userid` int NOT NULL default 0 AFTER `autogroup`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('alias', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `alias` varchar(255) NOT NULL default \'\' AFTER `name`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('countdown', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `countdown` tinyint(3) unsigned NOT NULL default 0 AFTER `userupload`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('childcountdown', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `childcountdown` tinyint(3) unsigned NOT NULL default 0 AFTER `countdown`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('countup', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `countup` tinyint(3) unsigned NOT NULL default 0 AFTER `childcountdown`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('childcountup', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `childcountup` tinyint(3) unsigned NOT NULL default 0 AFTER `countup`;';
			$database->setQuery($sql);
			$database->query();
		}

		$database->setQuery ("SHOW COLUMNS FROM #__downloads_blob");
		$fields = $database->loadObjectList();
		$fieldnames = array();
		foreach ($fields as $field) $fieldnames[] = $field->Field;

		if (!in_array('bloblength', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_blob`'
			   	.' ADD `bloblength` int NOT NULL default 0 AFTER `chunkid`;';
			$database->setQuery($sql);
			$database->query();
			$sql = 'UPDATE `#__downloads_blob` SET bloblength = LENGTH(datachunk)';
			$database->setQuery($sql);
			$database->query();
			$sql = 'ALTER TABLE `#__downloads_blob` ADD INDEX `size` (`fileid`, `bloblength`)';
			$database->setQuery($sql);
			$database->query();
		}
		
		$database->setQuery ("SHOW COLUMNS FROM #__downloads_log");
		$fields = $database->loadObjectList();
		$fieldnames = array();
		foreach ($fields as $field) $fieldnames[] = $field->Field;

		if (!in_array('price', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_log`'
				.' ADD `price` int(11) NOT NULL default 0 AFTER `value`;';
			$database->setQuery($sql);
			$database->query();
		}
	}
}

function com_install() {


	$installer = new remositoryInstaller();

	$remository_dir = str_replace('\\','/',dirname(__FILE__));
	$components_dir = dirname($remository_dir);
	$admin_dir = dirname($components_dir);
	$mosConfig_absolute_path = dirname($admin_dir);
	require_once($mosConfig_absolute_path.'/components/com_remository/remository.interface.php');
	$interface = remositoryInterface::getInstance();
	$mosConfig_live_site = $interface->getCfg('live_site');
	$mosConfig_lang = $interface->getCfg('lang');
	// Set some values arbitrarily to avoid errors
	$Small_Text_Len = 150;
	$Large_Text_Len = 500;
	$mosConfig_sitename = $interface->getCfg('sitename');

    $installer->dbcreate();
    $installer->dbupgrade();

	$interface->loadLanguageFile();
	$repository = remositoryRepository::getInstance();
	$customobj = new remositoryCustomizer();
	
	$approver = $repository->Enable_User_Autoapp ? 'Registered' : 'Nobody';
	$installer->approverPermissions($approver);

	if (file_exists($mosConfig_absolute_path.'/classes')) {
		$installer->permission_all_from_dir($mosConfig_absolute_path.'/components/com_remository/');
		$admin_path = defined('_ALIRO_IS_PRESENT') ? _ALIRO_ADMIN_PATH : $mosConfig_absolute_path.'/administrator';
		$installer->permission_all_from_dir($admin_path.'/components/com_remository/');
	}
	if (!file_exists($mosConfig_absolute_path.'/components/com_remository_files')) {
		@mkdir($mosConfig_absolute_path.'/components/com_remository_files', 0755);
		$installer->setDirPerms($mosConfig_absolute_path.'/components/com_remository_files');
	}
	if (!is_dir($repository->Down_Path)) {
		$aboveroot = dirname($mosConfig_absolute_path).'/remos_downloads';
		if (is_writeable($aboveroot) AND @mkdir($aboveroot, 0755)) {
			$repository->Down_Path = $aboveroot;
			$repository->Up_Path = $aboveroot.'/uploads';
		}
		else {
			@mkdir($repository->Down_Path, 0755);
			$controlfile = $repository->Down_Path.'/.htaccess';
			if (is_writeable($repository->Down_Path) AND !file_exists($controlfile) AND $fp = fopen($controlfile, 'wb')) {
				fwrite($fp, "order deny,allow\ndeny from all\n");
				fclose($fp);
			}
		}
		$downisok = $installer->setDirPerms ($repository->Down_Path);
		@mkdir($repository->Up_Path, 0755);
		$upisok = $installer->setDirPerms ($repository->Up_Path);
		$repository->saveValues();
	}

	$installer->makeDefaultContainer();
	$itext1 = sprintf(_DOWN_INSTALL_DONE1, $mosConfig_live_site.'/administrator/components/com_remository/read_me.txt');
	$itext2 = _DOWN_INSTALL_DONE2;
	$itext3 = _DOWN_INSTALL_DONE3;
	$itext4 = _DOWN_INSTALL_DONE4;

	echo <<<INSTALL_DONE
	
	<h3>$itext1</h3>
	<h3>
	$itext2
	</h3>
	<h3>$itext3</h3>
	<p>	
		$itext4
	</p>
	<p>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
	<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
	<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
	</form>
	</p>
	
INSTALL_DONE;

	return true;
}
