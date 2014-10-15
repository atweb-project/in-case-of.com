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

class remositoryAdminDownload extends remositoryAdminControllers {
	var $idparm = 0;

	function listTask () {
		if (!$this->remUser->isAdmin()) die ('Illegal file download attempt');
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$mosConfig_live_site = $interface->getCfg('live_site');
		$this->idparm = remositoryRepository::getParam($_REQUEST, 'id', 0);
		// Following two lines needed for com_remository_startdown operation
		// $chk = $this->repository->makeCheck($this->idparm,'startdown');
		// header("Location: $mosConfig_live_site/components/com_remository/com_remository_startdown.php?id=$this->idparm&chk=$chk&userid={$this->remUser->id}");
		// Rest of code is former com_remository_startdown

		// +----------------------------------------------------------------------+
		// | PHP Version 4                                                        |
		// +----------------------------------------------------------------------+
		// | Copyright (c) 1997-2004 The PHP Group                                |
		// +----------------------------------------------------------------------+
		// | This source file is subject to version 3.0 of the PHP license,       |
		// | that is bundled with this package in the file LICENSE, and is        |
		// | available at through the world-wide-web at                           |
		// | http://www.php.net/license/3_0.txt.                                  |
		// | If you did not receive a copy of the PHP license and are unable to   |
		// | obtain it through the world-wide-web, please send a note to          |
		// | license@php.net so we can mail you a copy immediately.               |
		// +----------------------------------------------------------------------+
		// | Authors: David Irvine <dave@codexweb.co.za>                          |
		// |          Aidan Lister <aidan@php.net>                                |
		// +----------------------------------------------------------------------+
		//
		// $Id: com_remository_startdown.php,v 1.4 2006/03/13 10:16:52 buliang Exp $


		if (!defined('ENT_NOQUOTES')) {
		    define('ENT_NOQUOTES', 0);
		}

		if (!defined('ENT_COMPAT')) {
		    define('ENT_COMPAT', 2);
		}

		if (!defined('ENT_QUOTES')) {
		    define('ENT_QUOTES', 3);
		}


		/**
		 * Replace html_entity_decode()
		 *
		 * @category    PHP
		 * @package     PHP_Compat
		 * @link        http://php.net/function.html_entity_decode
		 * @author      David Irvine <dave@codexweb.co.za>
		 * @author      Aidan Lister <aidan@php.net>
		 * @version     $Revision: 1.4 $
		 * @since       PHP 4.3.0
		 * @internal    Setting the charset will not do anything
		 * @require     PHP 4.0.1 (trigger_error)
		 */
		if (!function_exists('html_entity_decode')) {
		    function html_entity_decode($string, $quote_style = ENT_COMPAT, $charset = null)
		    {
		        if (!is_int($quote_style)) {
		            trigger_error('html_entity_decode() expects parameter 2 to be long, ' . gettype($quote_style) . ' given', E_USER_WARNING);
		            return;
		        }

		        $trans_tbl = get_html_translation_table(HTML_ENTITIES);
		        $trans_tbl = array_flip($trans_tbl);

		        // Add single quote to translation table;
		        $trans_tbl['&#039;'] = '\'';

		        // Not translating double quotes
		        if ($quote_style & ENT_NOQUOTES) {
		            // Remove double quote from translation table
		            unset($trans_tbl['&quot;']);
		        }

		        return strtr($string, $trans_tbl);
		    }
		}
		// End of PHP group code for html_entity_decode

		$repository = remositoryRepository::getInstance();
		$Small_Text_Len = $repository->Small_Text_Len;
		$Large_Text_Len = $repository->Large_Text_Len;
		$id = $this->idparm;
		$userid = $this->remUser->id;
		$fileinfo = $this->createFile();
		$nofile = $fileinfo->id ? false : true;
		$message = '';
		clearstatcache();
		$downpath = false;
		$len = 0;
		if ($fileinfo->islocal) {
			if ($fileinfo->filepath != '') {
				$downpath = $fileinfo->filePath();
				$downpath = html_entity_decode($downpath,ENT_QUOTES);
				if(!file_exists($downpath)) $nofile = true;
				else $len = filesize($downpath);
			}
			elseif ($fileinfo->plaintext) {
				$sql = "SELECT LENGTH(filetext) FROM #__downloads_text WHERE fileid=$id";
				$database->setQuery($sql);
				$len = $database->loadResult();
			}
			elseif ($fileinfo->isblob) {
				$sql = "SELECT SUM(bloblength) FROM #__downloads_blob WHERE fileid=$id GROUP BY fileid";
				$database->setQuery($sql);
				$len = $database->loadResult();
				//		echo 'Length of file from DB='.$len;
				//		$sql = "SELECT datachunk FROM #__downloads_blob WHERE fileid=$id AND chunkid=0";
				//		$database->setQuery($sql);
				//		$datachunk = $database->loadResult();
				//		echo 'Beginning of data from DB='.substr($datachunk,0,20);
			}
			else {
				$downpath = $repository->Down_Path.'/'.$fileinfo->realname;
				$downpath = html_entity_decode($downpath,ENT_QUOTES);
				if(!file_exists($downpath)) $nofile = true;
				else $len = filesize($downpath);
			}
			$displayname = $fileinfo->realname;
			//this fixes the single quotes (apostrophes)
			$displayname = html_entity_decode($displayname,ENT_QUOTES);
			$file_extension = remositoryAbstract::lastPart($displayname, '.');
		}
		if ($nofile) {
			echo _DOWN_FILE_NOTFOUND;
			$maindl = _MAIN_DOWNLOADS;
			$iconsize = _REMOSITORY_ICON_SIZE;
			// Change for multiple repositories
			// <br />&nbsp;<br /><a href=../../index.php?option=com_remository&amp;repnum=$this->repnum><img src="$mosConfig_live_site/components/com_remository/images/gohome.gif" width="$iconsize" height="$iconsize" border="0" align="absmiddle"> $maindl</a>
			echo <<<LINK_REMOS
			
			<br />&nbsp;<br /><a href=../../index.php?option=com_remository><img src="$mosConfig_live_site/components/com_remository/images/gohome.gif" width="$iconsize" height="$iconsize" border="0" align="absmiddle"> $maindl</a>

LINK_REMOS;

			exit;
		}
		while (@ob_end_clean());

		if (!$fileinfo->islocal) {
			$slimurl = trim($fileinfo->url);
			header("Location:$slimurl");
			exit;
		}

		$ctype = $this->setCtype($file_extension, $downpath);

		$this->writeHeaders($ctype, $displayname, $len);

		if ($fileinfo->plaintext) {
			$sql = "SELECT filetext FROM #__downloads_text WHERE fileid=$id";
			$database->setQuery($sql);
			$filetext = $database->loadResult();
			echo $filetext;
		}
		elseif ($fileinfo->isblob) {
			$chunkid = 0;
			$datachunk = true;
			$sql = "SELECT chunkid FROM #__downloads_blob WHERE fileid=$id ORDER BY chunkid";
			$database->setQuery($sql);
			$chunks = $database->loadResultArray();
			foreach ($chunks as $chunkid) {
				$sql = "SELECT datachunk FROM #__downloads_blob WHERE fileid=$id AND chunkid=$chunkid";
				$database->setQuery($sql);
				$datachunk = $database->loadResult();
				echo $datachunk;
			}
		}
		else {
			@set_time_limit(0);
			$fp = @fopen($downpath, "rb");
			$mqr = ini_get('magic_quotes_runtime');
			ini_set('magic_quotes_runtime', 0);
			$chunksize = 1*(512*1024); // how many bytes per chunk
			while($fp && !feof($fp)) {
				$buffer = fread($fp, $chunksize);
				print $buffer;
				flush();
				sleep(1);
			}
			ini_set('magic_quotes_runtime', $mqr);
			fclose($fp);
		}
		exit;
	}

	function &createFile () {
		if (is_numeric($this->idparm) AND ($this->idparm != 0)) {
			$file = new remositoryFile ($this->idparm);
			$file->getValues($this->remUser);
			if ($file->containerid < 0) {
				$file = new remositoryTempFile ($this->idparm);
				$file->getValues($this->remUser);
			}
			if ($file->id) return $file;
			die ('Fatal error - attempt to access unpublished file by non-admin user');
		}
		die ('Fatal error - we should have had a valid file ID');
	}

	function setCtype ($file_extension, $downpath) {
		//This will set the Content-Type to the appropriate setting for the file
		switch( $file_extension ) {
			 case "pdf": $ctype="application/pdf"; break;
		     case "exe": $ctype="application/octet-stream"; break;
		     case "zip": $ctype="application/zip"; break;
		     case "doc": $ctype="application/msword"; break;
		     case "xls": $ctype="application/vnd.ms-excel"; break;
		     case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
		     case "gif": $ctype="image/gif"; break;
		     case "png": $ctype="image/png"; break;
		     case "jpeg":
		     case "jpg": $ctype="image/jpg"; break;
		     case "mp3": $ctype="audio/mpeg"; break;
		     case "wav": $ctype="audio/x-wav"; break;
		     case "mpeg":
		     case "mpg":
		     case "mpe": $ctype="video/mpeg"; break;
		     case "mov": $ctype="video/quicktime"; break;
		     case "avi": $ctype="video/x-msvideo"; break;

		     //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
		     case "php":
		     case "htm":
		     case "html": if ($downpath) die("<b>Cannot be used for ". $file_extension ." files!</b>");

		     default: $ctype="application/force-download";
		}
		return $ctype;
	}

	function writeHeaders ($ctype, $displayname, $len) {
		//Begin writing headers
		header("Cache-Control: max-age=60");
		header("Cache-Control: private");
		header("Content-Description: File Transfer");

		//Use the switch-generated Content-Type
		header("Content-Type: $ctype");

		//Force the download
		header("Content-Disposition: attachment; filename=\"$displayname\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".$len);
	}

}