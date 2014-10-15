<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006-9 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

if (!defined('_MOS_NOTRIM')) define( '_MOS_NOTRIM', 0x0001 );  		// prevent getParam trimming input
if (!defined('_MOS_ALLOWHTML')) define( '_MOS_ALLOWHTML', 0x0002 );		// cause getParam to allow HTML - purified on user side
if (!defined('_MOS_ALLOWRAW')) define( '_MOS_ALLOWRAW', 0x0004 );		// suppresses forcing of integer if default is numeric

class remositoryRepository extends remositoryAbstract {

	/** @public string Repsitory name */
	public $name='';
	/** @public string Repository alias, used instead of name in SEF (if present) */
	public $alias='';
	/** @public string Remository version number */
	public $version='';
	/** @public book Default to database to store files */
	public $Use_Database='1';
	/** @public string Main Page Title */
	public $Main_Page_Title='Remository';
	/** @public string Table classes */
	public $tabclass='sectiontableentry2,sectiontableentry1';
	/** @public string Table headers */
	public $tabheader='sectiontableheader';
	/** @public string URL to header picture */
	public $headerpic='/components/com_remository/images/header.gif';
	/** @public array Permitted file extensions */
	public $ExtsOk='txt,exe,tar,gz,rar,zip,png,gif,jpg,pdf,doc,rtf,odt,odc,odp,odg,mp3,mov';
	/** @public string File extensions for immediate display */
	public $ExtsDisplay='';
	/** @public string File extensions to be handled by audio player */
	public $ExtsAudio='';
	/** @public string File extensions to be handled by video player */
	public $ExtsVideo='';
	/** @public string Classification types for tagging */
	public $Classification_Types='';
	/** @public string Scribd publishing key */
	public $Scribd='';
	/** @public string Download file path */
	public $Down_Path='';
	/** @public string Upload file path */
	public $Up_Path='';
	/** @public int Length of full description (maximum) */
	public $Large_Text_Len=300;
	/** @public int Length of short description (maximum) */
	public $Small_Text_Len=150;
	/** @public int Small Image width (pixels) */
	public $Small_Image_Width=100;
	/** @public int Small Image height (pixels) */
	public $Small_Image_Height=100;
	/** @public int Large Image width (pixels) */
	public $Large_Image_Width=600;
	/** @public int Large Image height (pixels) */
	public $Large_Image_Height=600;
	/** @public bool Allow Large images to be popped up */
	public $Allow_Large_Images=1;
	/** @public bool Activate AEC integration */
	public $Activate_AEC=0;
	/** @public bool Use Remository own pathway */
	public $Remository_Pathway=1;
	/** @public int Maximum file size in Kbytes */
	public $MaxSize=5000;
	/** @public int Maximum uploads per user per day */
	public $Max_Up_Per_Day=5;
	/** @public int Maximum downloads per user per day */
	public $Max_Down_Per_Day=5;
	/** @public int Maximum downloads per registered user per day */
	public $Max_Down_Reg_Day=10;
	/** @public int Maximum downloads per file per day */
	public $Max_Down_File_Day=5;
	/** @public bool Count all downloads using a subscription manager, if present. 1 = count all downloads, 2 = count by container */
	public $Count_Down=0;
	/** @public int Number of featured items to be shown per folder */
	public $Featured_Number=0;
	/** @public int Maximum space allowed for files directory */
	public $Max_Up_Dir_Space=50000;
	/** @public int Number of favourites to be marked by a registered user */
	public $Favourites_Max=0;
	/** @public int Maximum number of thumbnail image files, 0 = use URL in file data */
	public $Max_Thumbnails=1;
	/** @public int Minimum length for a comment on a file */
	public $Min_Comment_length=1;
	/** @public bool Make automatic thumbnail for image file */
	public $Make_Auto_Thumbnail=0;
	/** @public string Default Version Number */
	public $Default_Version='';
	/** @public string Main authors for selection */
	public $Main_Authors='';
	/** @public int Threshold for popular authors */
	public $Author_Threshold=0;
	/** @public string Date format string for PHP date function  */
	public $Date_Format='d M Y';
	/** @public string Date locale string for PHP date functions  */
	public $Set_date_locale='';
	/** @public string Language to override CMS settings  */
	public $Force_Language='';
	/** @public bool Show full container path in search results */
	public $Show_all_containers=0;
	/** @public bool Show sub-categories under categories */
	public $Show_SubCategories=0;
	/** @public bool Anti Leach in effect */
	public $Anti_Leach=0;
	/** @public bool Allow uploads that overwrite an earlier file */
	public $Allow_Up_Overwrite=1;
	/** @public bool Allow users to submit files */
	public $Allow_User_Sub=1;
	/** @public bool Allow users to edit existing file information */
	public $Allow_User_Edit=1;
	/** @public bool Allow users to delete the files they submitted */
	public $Allow_User_Delete=0;
	/** @public bool Enable Auto approve and publish for admin */
	public $Enable_Admin_Autoapp=1;
	/** @public bool Enable Auto approve and publish for registered users */
	public $Enable_User_Autoapp=0;
	/** @public bool Allow comments on files */
	public $Allow_Comments=1;
	/** @public bool Allow votes on files */
	public $Allow_Votes=1;
	/** @public bool Enable downloads directly from a list of files */
	public $Enable_List_Download=0;
	/** @public bool Allow Audio files to be downloaded */
	public $Audio_Download=0;
	/** @public bool Allow Video files to be downloaded */
	public $Video_Download=0;
	/** @public bool Show users remote file upload option */
	public $User_Remote_Files=0;
	/** @public bool Let users see containers where download not permitted */
	public $See_Containers_no_download=1;
	/** @public bool Let users see files that are not permitted to be downloaded */
	public $See_Files_no_download=1;
	/** @public bool Show RSS feed */
	public $Show_RSS_feeds=1;
	/** @public bool Allow display of detailed file information */
	public $Allow_File_Info=1;
	/** @public bool Show Remository Footer (Search/Submit/Credits) */
	public $Show_Footer=1;
	/** @public bool Show File and Folder Counts */
	public $Show_File_Folder_Counts=1;
	/** @public bool Display Page Heading */
	public $Display_PageHeading=1;
	/** @public bool Display Folder List Heading */
	public $Display_FolderListHeading=1;
	/** @public bool Display Folder Icons */
	public $Display_FolderIcons=1;
	/** @public bool Display File Icons */
	public $Display_FileIcons=1;
	/** @public bool Display File List Heading */
	public $Display_FileListHeading=1;
	/** @public bool Send mail when a file is submitted */
	public $Send_Sub_Mail=1;
	/** @public string Submit Mail Alt Add */
	public $Sub_Mail_Alt_Addr='';
	/** @public string Submit Mail Alt Name */
	public $Sub_Mail_Alt_Name='';
	/** @public time Timestamp for authentication */
	public $Time_Stamp=1136491012;
	/** @public string URI for profile with %userid where user ID should go */
	public $Profile_URI='';
	/** @public string Information to be displayed during download */
	public $download_text = "<script type=\"text/javascript\"><!--\r\ngoogle_ad_client = \"pub-9523985668336222\";\r\ngoogle_ad_width = 300;\r\ngoogle_ad_height = 250;\r\ngoogle_ad_format = \"300x250_as\";\r\ngoogle_ad_type = \"image\";\r\ngoogle_ad_channel =\"\";\r\n//--></script>\r\n<script type=\"text/javascript\"\r\nsrc=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\">\r\n</script>";
	/** @public string Information to be displayed at start of main page */
	public $preamble = '';
	/** @public string Default Licence for downloads */
	public $Default_Licence = '';
	/** @public string Custom field control - serialized array */
	public $customizer = '';
	/** @public string Custom field names and titles */
	public $custom_names='';
	/** @public string Template for main display */
	public $template='default';
	/** @public int unix timestamp for automatic timed triggering */
	public $Cron_Timer=0;
	/** @public bool Do files have ID number when stored in file system? */
	public $Real_With_ID=1;
	/** @public bool Should users be taken straight to download, skipping file info page? */
	public $Immediate_Download=0;
	/** @public bool User the CMS groups instead of Remository groups? */
	public $Use_CMS_Groups=0;
	/** @public bool Allow users to create containers inside containers they have upload rights on */
	public $Allow_Container_Add=0;
	/** @public bool Allow users to delete the containers they have upload rights on */
	public $Allow_Container_Delete=0;
	/** @public bool Allow users to edit (in this case, just title renaming) the containers they have upload rights on */
	public $Allow_Container_Edit=0;
	
	
	public function __construct () {
		$repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		if (true OR $type=='GLOBAL') {
			$live_site = $interface->getCfg('live_site');
			$database->setQuery("SELECT * FROM #__downloads_repository ORDER BY id LIMIT 1");
			$repositories = $database->loadObjectList();
			// Change for multiple repositories
			// if ($repositories) foreach ($repositories as $repository) if ($repnum == $repository->id) break;
			if (!empty($repositories)) $repository = $repositories[0];
			if (isset($repository)) {
				if ($repnum != $repository->id) {
					$repository = $repositories[0];
					$_REQUEST['repnum'] = $repository->id;
				}
				$this->setValues($repository);
			}
			else {
				$this->headerpic = $live_site.$this->headerpic;
				$this->saveValues();
			}
		}
		foreach (get_class_vars(get_class($this)) as $k=>$v) {
			$this->$k = str_replace ('{live_site}', $live_site, $this->$k);
		}
	}

    public static function getInstance () {
        static $instance;
        if (!is_object($instance)) $instance = new remositoryRepository();
        return $instance;
    }

	protected function tableName () {
		return '#__downloads_repository';
	}

	protected function getVarText() {
		$txt = '';
		$this->Time_Stamp = time();
		foreach (get_class_vars(get_class($this)) as $k=>$v) {
			if (substr($k,0,1) != '_') {
				if (is_numeric($this->$k)){
					$txt .= "\$$k = ".intval($this->$k).";\n";
				} elseif (strlen($k) > 0) $txt .= "\$$k = \"".addslashes( $this->$k )."\";\n";
			}
		}
		return $txt;
	}

	public function saveValues ($makenew=false) {
	    
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$this->forceBools();
		if (!$makenew) {
		
			$repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
			// Change for multiple repositories
			// $sql = "SELECT COUNT(*) FROM #__downloads_repository WHERE id = $repnum";
			$sql = "SELECT COUNT(*) FROM #__downloads_repository";
			$database->setQuery($sql);
			if (!$database->loadResult()) $makenew = true;
		}
		if ($makenew) {
			$abspath = $interface->getCfg('absolute_path');
			$this->Down_Path = $abspath.'/remos_downloads';
			$this->Up_Path = $this->Down_Path.'/uploads';
			$sql = $this->insertSQL();
		}
		else {
			$this->id = $repnum;
			$sql = $this->updateSQL();
		}
		remositoryRepository::doSQL ($sql);
	}
	
	public function searchRepository($search_text, $seek_fields, $user, $pagecontrol, $containers, $countOnly=false, $notEmpty=true) {
		if ($notEmpty AND !$search_text) return $countOnly ? 0 : array();
		if ($countOnly) {
			$sql = remositoryFile::searchFilesSQL($search_text, $seek_fields, $user, true, $containers);
			$interface = remositoryInterface::getInstance();
			$database = $interface->getDB();
			$database->setQuery($sql);
			return $database->loadResult();
		}
		$startitem = $pagecontrol instanceof remositoryPage ? $pagecontrol->startItem() : 0;
		$perpage = $pagecontrol instanceof remositoryPage ? $pagecontrol->itemsperpage : 99999;
		$sql = remositoryFile::searchFilesSQL($search_text, $seek_fields, $user, false, $containers, $startitem, $perpage);
		return remositoryRepository::doSQLget($sql,'remositoryFile');
	}

	// May be redundant
	function getUploadLimit () {
		return $this->maxUploads;
	}

	// May be redundant
    function canUserSubmit () {
    	return $this->userSubmit;
    }

	public function getTableClasses () {
		return explode(",",$this->tabclass);
	}

	public function isExtensionOK ($file) {
		if ('*' == trim($this->ExtsOk)) return true;
		$extsok = explode(",",strtolower($this->ExtsOk));
		foreach ($extsok as $ok) if (0 == strcasecmp($ok, substr($file, -strlen($ok)))) return true;
		return false;
	}

	public function getSelectList ($allowTop, $default, $type, $parm, $user, $usable=false, $zerotext=_DOWN_NO_PARENT) {
		if ($allowTop) $selector[] = $this->makeOption(0,$zerotext);
		else $selector = array();
		$manager = remositoryContainerManager::getInstance();
		foreach ($manager->getCategories() as $category) $category->addSelectList('', $selector, null, $user, $usable);
		if (count($selector)) return $this->selectList( $selector, $type, $parm, $default );
		else return '';
	}

	public static function getIcons ($location) {

		$interface = remositoryInterface::getInstance();

		$mosConfig_live_site = $interface->getCfg('live_site');
		$iconList='';
		$live_site = $interface->getCfg('live_site');
		$iconsdone = array();
		$handle=@opendir(_REMOS_ABSOLUTE_PATH.'/images/remository/images/'.$location);
		if ($handle) {
			while (($file = readdir($handle))!==false) {
				if ($file != "." AND $file != "..") {
					$iconList.="\n\t\t\t\t<a href=\"JavaScript:paste_strinL('{$file}')\" onmouseover=\"window.status='{$file}'; return true\"><img src=\"{$live_site}/components/com_remository/images/{$location}/{$file}\" width=\"32\" height=\"32\" alt=\"{$file}\" /></a>&nbsp;&nbsp;";
					$iconsdone[] = basename($file);
				}
			}
   			closedir($handle);
		}
		$handle=@opendir(_REMOS_ABSOLUTE_PATH.'/components/com_remository/images/'.$location);
		if ($handle) {
			while (($file = readdir($handle))!==false) {
				if ($file != "." AND  $file != ".." AND !in_array(basename($file), $iconsdone)) {
					$iconList.="\n\t\t\t\t<a href=\"JavaScript:paste_strinL('{$file}')\" onmouseover=\"window.status='{$file}'; return true\"><img src=\"{$live_site}/components/com_remository/images/{$location}/{$file}\" width=\"32\" height=\"32\" alt=\"{$file}\" /></a>&nbsp;&nbsp;";
				}
			}
   			closedir($handle);
		}
		if (empty($iconList)) $iconList="_DOWN_NOT_AUTH";
		return $iconList;
	}

	public function resetCounts ($chain=null) {
		$manager = remositoryContainerManager::getInstance();
		$categories = $manager->getCategories();
		foreach ($categories as $category) $category->setFileCount($chain);
	}

	// May be redundant
	function getFiles ($search='', $limitstart=0, $limit=0) {
		$sql = remositoryFile::getFilesSQL(true, false, 0, true, 2, $search, $limitstart, $limit);
		return remositoryRepository::doSQLget($sql,'remositoryFile');
	}

	// May be redundant
	function getFilesCount ($search) {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$sql = remositoryFile::getFilesSQL(true, true, 0, true, 2, $search);
		$database->setQuery( $sql );
		return $database->loadResult();
	}

	public function getTempFiles () {
		$repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		// Change for multiple repositories
		// $sql = "SELECT * FROM #__downloads_files WHERE metatype > 0 AND repnum = $repnum ORDER BY id";
		$sql = "SELECT * FROM #__downloads_files WHERE metatype > 0 ORDER BY id";
		$results = remositoryRepository::doSQLget($sql,'remositoryTempFile');
		foreach ($results as $key=>$result) $results[$key]->containerid = -$result->containerid;
		return $results;
	}

	public function RemositoryFunctionURL ($func=null, $idparm=null, $os=null, $orderby=null, $item=null) {

		return '<a href="'.$this->RemositoryBasicFunctionURL($func,$idparm,$os,$orderby,$item).'">';

	}

	public function RemositoryRawFunctionURL ($func=null, $idparm=null, $os=null, $orderby=null, $item=null, $fname=null ) {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();

		$repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		if (!defined('_ALIRO_IS_PRESENT')) {
			// Change for multiple repositories
			// $url = "index.php?option=com_remository&repnum=$repnum&Itemid=";
			$url = "index.php?option=com_remository&Itemid=";
			$url .= $item ? $item : $this->getItemid();
		}
		// Change for multiple repositories
		// elseif (1 < $repnum) $url = 'index.php?option=com_remository&repnum='.$repnum;
		else $url = 'index.php?option=com_remository';
		if ($func) $url .= '&func='.$func;
		if ($idparm) $url .= '&id='.$idparm;
		if (!$os) $os = remositoryRepository::getParam($_REQUEST,'os',null);
		if (!in_array($os,array('win','mac','linux','all'))) $os = null;
		if ($os AND $os != 'All') $url .= '&os='.$os;
		if ($orderby) $url .= '&orderby='.$orderby;
		if ($func == 'download') $url .= '&chk='.$this->makeCheck($idparm,$func).'&no_html=1';
		elseif ($func == 'rss') $url .= '&no_html=1';
		// if ($fname) $url .= '&fname='.urlencode($fname);
		$thisfunc = remositoryRepository::getParam($_REQUEST, 'func');
		if ('direct' == substr($thisfunc,0,6)) {
			if (!$func) $url .= '&func=directlist';
			$url .= '&indextype=2';
		}
		return $url;
	}
	
	public function getItemid ($component='com_remository') {
		$database = remositoryInterface::getInstance()->getDB();
		if (isset($GLOBALS['remosef_itemids'][$component])) return $GLOBALS['remosef_itemids'][$component];
		$callingID = remositoryRepository::getParam($_REQUEST, 'Itemid', 0);
		if ($callingID) {
			$database->setQuery("SELECT link FROM #__menu WHERE id = $callingID");
			$link = $database->loadResult();
			if (false !== strpos($link, 'option='.$component)) {
				$GLOBALS['remosef_itemids'][$component] = $Itemid = $callingID;
				return $Itemid;
			}
		}
		$database->setQuery("SELECT id, (CASE menutype WHEN 'mainmenu' THEN 1 WHEN 'topmenu' THEN 2 WHEN 'othermenu' THEN 3 ELSE 99 END) menorder,"
			." (CASE type WHEN 'url' THEN 2 ELSE 1 END) typorder"
			." FROM #__menu WHERE link LIKE 'index.php?option=$component%' AND type LIKE 'component%' AND published=1 ORDER BY typorder, menorder");
		$GLOBALS['remosef_itemids'][$component] = $Itemid = $database->loadResult();
		return $Itemid;
	}

	public function RemositoryBasicFunctionURL ($func=null, $idparm=null, $os=null, $orderby=null, $item=null, $fname=null ) {
		$interface = remositoryInterface::getInstance();
		$url = $this->RemositoryRawFunctionURL($func, $idparm, $os, $orderby, $item, $fname);
		if (!defined('REMOSITORY_ADMIN_SIDE')) $url = $interface->sefRelToAbs($url);
		$ampencode = '/(&(?!(#[0-9]{1,5};))(?!([0-9a-zA-Z]{1,10};)))/';
		return preg_replace($ampencode, '&amp;', $url);
	}

	function wrongCheck ($chk, $id, $func) {

		if ($chk == $this->makeCheck($id, $func)) return false;
		return true;
	}

	function makeCheck ($id, $func) {

		$interface = remositoryInterface::getInstance();
		return md5($this->Time_Stamp.$interface->getCfg('absolute_path').date('md').$id.$func);
	}

	// Containerid may be needed for notification by email of intererested people
	public function sendAdminMail ($user_full, $filetitles, $containerid, $published=true) {
		$interface = remositoryInterface::getInstance();
		$subject = $interface->getCfg('sitename').':'._DOWN_MAIL_SUB;
		$message = $published ? _DOWN_MAIL_MSG_APP : _DOWN_MAIL_MSG;
		$message .= "\n"._DOWN_FILE_TITLE."\n". $filetitles;
		$message = sprintf($message, $user_full, $interface->getCfg('sitename'));
		$interface->sendMail (($this->Sub_Mail_Alt_Addr ? $this->Sub_Mail_Alt_Addr : remositoryUser::superAdminMail()), $subject, $message);
	}

	public function checkCronTimer () {
		if (_REMOSITORY_CRON_INTERVAL < (time() - $this->Cron_Timer)) {
			$this->Cron_Timer = time();
			$this->saveValues();
			remositoryInterface::getInstance()->triggerMambots('remositoryCronTimer');
		}
	}

	public function RemositoryImageURL($imageName, $width=_REMOSITORY_ICON_SIZE, $height=_REMOSITORY_ICON_SIZE, $title='') {
		$interface = remositoryInterface::getInstance();
		$imageurl = $this->RemositoryBareImageURL($imageName);
		$titlehtml = $title ? "title=\"$title\"" : '';
		return <<<REMOS_IMAGE
		
			<img src="$imageurl" width="$width" height="$height" style="border:0;" $titlehtml alt="" />
		
REMOS_IMAGE;

	}
	
	protected function RemositoryBareImageURL ($imageName) {
		$live_site = remositoryInterface::getInstance()->getCfg('live_site');
		if (file_exists(_REMOS_ABSOLUTE_PATH.'/images/remository/images/'.$imageName)) {
			return $live_site.'/images/remository/images/'.$imageName;
		}
		return $live_site.'/components/com_remository/images/'.$imageName;
	}

	public static function doSQL ($sql) {
		if ($sql) {
			$interface = remositoryInterface::getInstance();
			$database = $interface->getDB();
			$database->setQuery($sql);
			if (!$database->query()) {
				$message = addslashes($database->getErrorMsg());
				echo <<<ERROR_SCRIPT
				
	<script type="text/javascript"> alert('$message'); window.history.go(-1); </script>\n";
	
ERROR_SCRIPT;
						
				exit();
			}
		}
	}

	public static function doSQLget ($sql, $classname) {
		if ($sql) {
			$interface = remositoryInterface::getInstance();
			$database = $interface->getDB();
			$database->setQuery($sql);
			$rows = $database->loadObjectList();
		}
		else $rows = null;
		$target = get_class_vars($classname);
		if ($rows) {
			foreach ($rows as $row) {
				$next = new $classname(0);
				foreach (get_object_vars($row) as $field=>$value) {
					if (isset($target[$field])) $next->$field = $value;
				}
				$result[] = $next;
			}
		}
		else $result = (array());
		return $result;
	}

	public function makeOption($value, $text='', $value_name='value', $text_name='text') {
		$obj = new stdClass;
		$obj->$value_name = $value;
		$obj->$text_name = trim($text) ? $text : $value;
		return $obj;
	}

	public function selectList (&$arr, $tag_name, $tag_attribs='', $selected=NULL, $key='value', $text='text' ) {
		$html = "\n\t\t<select name=\"$tag_name\" id=\"$tag_name\" $tag_attribs>";
		foreach ($arr as $option) {
			$picked = '';
			if (is_array($selected)) {
				if (in_array($option->$key, $selected)) $picked = 'selected="selected"';
			}
			elseif ($option->$key == $selected) {
			    $picked = 'selected="selected"';
			}
			$html .= "\n\t\t\t<option value='{$option->$key}' $picked>{$option->$text}</option>";
		}
		$html .= "\n\t\t</select>\n";
		
		return $html;
	}

	public function apache_request_headers () {
		if (function_exists('apache_request_headers')) return apache_request_headers();
		$rx_http = '/\AHTTP_/';
		foreach ($_SERVER as $key=>$value) {
			$arh_key = preg_replace($rx_http, '', $key);
			if ($arh_key != $key) {
				// do some nasty string manipulations to restore the original letter case
				// this should work in most cases
				$rx_matches = explode('_', strtolower($arh_key));
				if (count($rx_matches) AND strlen($arh_key) > 2) {
					$arh_key = implode('-', array_map('ucfirst', $rx_matches));
					$arh[$arh_key] = $value;
				}
			}
		}
		return empty($arh) ? array() : $arh;
	}

	public static function getParam (&$arr, $name, $def='', $mask=_MOS_ALLOWHTML) {
		if (!defined('_JOOMLA_15PLUS') AND !defined('_ALIRO_IS_PRESENT') AND class_exists('cmsapiInterface') AND method_exists('cmsapiInterface', 'getInstance')) {
			return cmsapiInterface::getInstance('com_remository')->getParam($arr, $name, $def, $mask);
		}
	    if (isset( $arr[$name] )) {
	        if (is_array($arr[$name])) foreach ($arr[$name] as $key=>$element) {
	        	$result[$key] = self::getParam ($arr[$name], $key, $def, $mask);
	        }
	        else {
	            $result = $arr[$name];
	            if (!($mask&_MOS_NOTRIM)) $result = trim($result);
	            if (!is_numeric($result)) {
	                if (!($mask&_MOS_ALLOWRAW) AND is_numeric($def)) $result = $def;
	                elseif ($result) {
	                	if (!($mask & _MOS_ALLOWHTML)) {
							$result = strip_tags($result);
						}
						$result = remositoryInterface::getInstance()->purify($result);
	                }
	            }
	        }
	        return $result;
	    }
	    return $def;
	}

}
