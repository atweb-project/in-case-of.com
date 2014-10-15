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

class remositoryAdminDbconvert2 extends remositoryAdminControllers {

	function __construct ($admin) {
		parent::__construct ($admin);
	    $_REQUEST['act'] = 'dbconvert2';
	}
	
	function listTask () {
		$view = new remositoryAdminHTML ($this, 0, '');
		$view->formStart(_DOWN_ADMIN_ACT_DBCONVERT2);
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$database->setQuery ("SHOW COLUMNS FROM #__downloads_repository");
		$fields = $database->loadObjectList();
		$fieldnames = array();
		foreach ($fields as $field) $fieldnames[] = $field->Field;
		
		$sql = 'ALTER TABLE `#__downloads_repository`'
		   	.' CHANGE `id` `id` int NOT NULL;';
		$database->setQuery($sql);
		$database->query();
		$sql = 'DELETE FROM `#__downloads_repository` WHERE id != 0';
		$database->setQuery($sql);
		$database->query();
		
		if (!in_array('Use_Database', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Use_Database` smallint NOT NULL default \'1\' AFTER `version`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('keywords', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `keywords` varchar(255) NOT NULL default \'\' AFTER `windowtitle`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('Large_Image_Width', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Large_Image_Width` smallint NOT NULL default \'600\' AFTER `Small_Image_Height`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('Large_Image_Height', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Large_Image_Height` smallint NOT NULL default \'600\' AFTER `Large_Image_Width`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('Max_Thumbnails', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Max_Thumbnails` smallint NOT NULL default \'0\' AFTER `Favourites_Max`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Allow_Large_Images', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Allow_Large_Images` tinyint unsigned NOT NULL default \'1\' AFTER `Allow_Votes`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('download_text', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `download_text` text NOT NULL default \'\' AFTER `Time_Stamp`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('Max_Down_Per_Day', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_repository`'
			   	.' ADD `Max_Down_Per_Day` int NOT NULL default 5 AFTER `Max_Up_Per_Day`;';
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

		
		$database->setQuery ("SHOW COLUMNS FROM #__downloads_files");
		$fields = $database->loadObjectList();
		$fieldnames = array();
		foreach ($fields as $field) $fieldnames[] = $field->Field;
		
		if (!in_array('keywords', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
			   	.' ADD `keywords` varchar(255) NOT NULL default \'\' AFTER `windowtitle`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('userid', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `userid` int NOT NULL default \'0\' AFTER `containerid`;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('download_text', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `download_text` text NOT NULL default \'\' AFTER `userupload`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('chunkcount', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `chunkcount` int NOT NULL default \'0\' AFTER `isblob`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('editgroup', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `editgroup` smallint NOT NULL default \'0\' AFTER `groupid`;';
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
				.' ADD `custom_3` text NOT NULL default \'\' AFTER custom_2;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('custom_4', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `custom_4` int NOT NULL default \'0\' AFTER custom_3;';
			$database->setQuery($sql);
			$database->query();
		}

		if (!in_array('custom_5', $fieldnames)) {
			$sql = 'ALTER TABLE `#__downloads_files`'
				.' ADD `custom_5` datetime NOT NULL default \'0000-00-00\' AFTER custom_4;';
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
		
		if (!in_array('editgroup', $fieldnames)) {	
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `editgroup` smallint NOT NULL default \'0\' AFTER `groupid`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('adminauto', $fieldnames)) {	
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `adminauto` tinyint unsigned NOT NULL default \'0\' AFTER `editgroup`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('userauto', $fieldnames)) {	
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `userauto` tinyint unsigned NOT NULL default \'0\' AFTER `adminauto`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('autogroup', $fieldnames)) {	
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `autogroup` smallint NOT NULL default \'0\' AFTER `userauto`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('userid', $fieldnames)) {	
			$sql = 'ALTER TABLE `#__downloads_containers`'
				.' ADD `userid` int NOT NULL default \'0\' AFTER `autogroup`;';
			$database->setQuery($sql);
			$database->query();
		}

		
		$database->setQuery ("SHOW COLUMNS FROM #__downloads_temp");
		$fields = $database->loadObjectList();
		$fieldnames = array();
		if ($fields) foreach ($fields as $field) $fieldnames[] = $field->Field;
		
		if (!in_array('keywords', $fieldnames)) {	
			$sql = 'ALTER TABLE `#__downloads_temp`'
				.' ADD `keywords` varchar(255) NOT NULL default \'\' AFTER `windowtitle`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('userid', $fieldnames)) {	
			$sql = 'ALTER TABLE `#__downloads_temp`'
				.' ADD `userid` int NOT NULL default \'0\' AFTER `containerid`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('download_text', $fieldnames)) {	
			$sql = 'ALTER TABLE `#__downloads_temp`'
				.' ADD `download_text` text NOT NULL default \'\' AFTER `userupload`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('chunkcount', $fieldnames)) {	
			$sql = 'ALTER TABLE `#__downloads_temp`'
				.' ADD `chunkcount` int NOT NULL default \'0\' AFTER `isblob`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('editgroup', $fieldnames)) {	
			$sql = 'ALTER TABLE `#__downloads_temp`'
				.' ADD `editgroup` smallint NOT NULL default \'0\' AFTER `groupid`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('custom_1', $fieldnames)) {	
			$sql = 'ALTER TABLE `#__downloads_temp`'
				.' ADD `custom_1` varchar(255) NOT NULL default \'\' AFTER `editgroup`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('custom_2', $fieldnames)) {	
			$sql = 'ALTER TABLE `#__downloads_temp`'
				.' ADD `custom_2` varchar(255) NOT NULL default \'\' AFTER `custom_1`;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('custom_3', $fieldnames)) {	
			$sql = 'ALTER TABLE `#__downloads_temp`'
				.' ADD `custom_3` text NOT NULL default \'\' AFTER custom_2;';
			$database->setQuery($sql);
			$database->query();
		}
		
		if (!in_array('custom_4', $fieldnames)) {	
			$sql = 'ALTER TABLE `#__downloads_temp`'
				.' ADD `custom_4` int NOT NULL default \'0\' AFTER custom_3;';
			$database->setQuery($sql);
			$database->query();
		}

		$database->setQuery("SELECT COUNT(*) FROM #__permissions");
		if (0 == $database->loadResult()) {
			$database->setQuery("INSERT INTO #__permissions(SELECT 0 , 'Nobody', 2, 'edit', 'remosFolder', id, 0 FROM jos_downloads_containers)");
			$database->query();
			$database->setQuery("INSERT INTO #__permissions(SELECT 0 , 'Registered', 2, 'upload', 'remosFolder', id, 0 FROM jos_downloads_containers WHERE (userupload & 1) AND NOT (registered & 1))");
			$database->query();
			$database->setQuery("INSERT INTO #__permissions(SELECT 0 , 'Registered', 2, 'download', 'remosFolder', id, 0 FROM jos_downloads_containers WHERE (userupload & 2) AND NOT (registered & 2))");
			$database->query();
			$database->setQuery("INSERT INTO #__permissions(SELECT 0 , 'Nobody', 2, 'upload', 'remosFolder', id, 0 FROM jos_downloads_containers WHERE NOT(userupload & 1) AND NOT (registered & 1))");
			$database->query();
			$database->setQuery("INSERT INTO #__permissions(SELECT 0 , 'Nobody', 2, 'download', 'remosFolder', id, 0 FROM jos_downloads_containers WHERE NOT(userupload & 2) AND NOT (registered & 2))");
			$database->query();
		}

		$repository = remositoryRepository::getInstance();
		$repository->resetCounts(array());
		$downdir = $repository->Down_Path;
		$sql = "SELECT containerid FROM #__downloads_files WHERE MAX(isblob) = 0 AND MAX(plaintext) = 0 GROUP BY containerid";
		$database->setQuery($sql);
		$containers = $database->loadResultArray();
		$containerlist = implode(',',$containers);
		$sql = "UPDATE #__downloads_containers SET filepath='$downdir/' WHERE filepath='' AND filecount!=0 AND containerid IN($containerlist)";
		$database->setQuery($sql);
		$database->query();
		$sql = "UPDATE #__downloads_files SET filepath='$downdir/' WHERE filepath='' AND isblob=0 AND plaintext=0";
		$database->setQuery($sql);
		$database->query();
		
		echo '<tr><td class="message">'._DOWN_DB_CONVERT_OK.'</td></tr>';
		echo '</table></form>';
	}

}