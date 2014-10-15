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
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class XDownloaderHelper {
	
	public static function returnBytes($val) {
	    $val = trim($val);
	    $last = strtolower($val{strlen($val)-1});
	    switch($last) {
	        // The 'G' modifier is available since PHP 5.1.0
	        case 'g':
	            $val *= 1024;
	        case 'm':
	            $val *= 1024;
	        case 'k':
	            $val *= 1024;
	    }
	    return $val;
	}

	/**
	 * Method to get category folder name
	 * 
	 * @return String on success, FALSE otherwise
	 */
	public static function getResourcePath() {
		$componentParams = JComponentHelper::getParams('com_xdownloader');
		$absolutePath = str_replace('\\', '/', $componentParams->get('dwnabsolute_path'));
		if(substr_compare($absolutePath, '/', -1) == 0) {
			$absolutePath = substr($absolutePath, 0, strlen($absolutePath) - 1);
		}
		return $absolutePath;		
	}
	
	
	/**
	 * Method to get category folder name
	 * 
	 * @var cat_id integer
	 * @return String on success, FALSE otherwise
	 */
	public static function getCategoryFolderName($cat_id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.folder');
		$query->from('#__xdldr_categories AS a');
		$query->where('a.id = '.$db->quote($cat_id));
		$db->setQuery($query);
		
		$result = $db->loadResult();
		return $result;
	}
	
	public static function folderExists($folder) {
		$folder = JFolder::makeSafe($folder);
		if(!JFolder::exists($folder)) {
			return false;
		}
		return true;
	}
	
	public static function createFolder($folder) {
		$index_html = '<!DOCTYPE html><title></title>';
		if(JFolder::create($folder)) {
            if(!JFile::write($folder.DS.'index.html', $index_html)) {
                JError::raiseWarning(119, 'Permission denied');
                return false;
            }
		}
		else {
            JError::raiseWarning(119, 'Permission denied');
            return false;			
		}
		return true;
	}
	
	public static function fileExists($filename, $dest) {
		$dest .= DS.$filename;
		return JFile::exists($dest);
	}
	
	public static function uploadFile($file, $dest) {
		$dest .= DS.$file['name'];
		return JFile::upload($file['tmp_name'], $dest);
	}
	
	public static function deleteFile($filename, $dest) {
		$dest .= DS.$filename;
		return JFile::delete($dest);
	} 
	
	public static function getFileExtension($filename) {
		return JFile::getExt($filename);
	}
	
	public static function convertGroupNameToId($groupNames=array()) {
		if(!empty($groupNames)) {
			$db = &JFactory::getDbo();
			
			$where = '';
			for($i = 0, $n = count($groupNames); $i < $n; $i++) {
				$groupNames[$i] = $db->quote($groupNames[$i]);
			}
			$where = implode(',', $groupNames);
			
			$query = $db->getQuery(true);
			$query->select('a.id');
			$query->from('#__usergroups AS a');
			$query->where('a.title IN ('.$where.')');
			$db->setQuery($query);
			
			if(($result = $db->loadResultArray()) != null) {
				return $result;
			}
		}
		return false;	
	}

	public static function convertGroupIdToName($groupID=array()) {
		if(!empty($groupID)) {
			$db = &JFactory::getDbo();
			
			$where = array();
			
			foreach($groupID as $id) {
				array_push($where, $db->quote($id));
			}
			
			$query = $db->getQuery(true);
			$query->select('a.title');
			$query->from('#__usergroups AS a');
			$query->where('a.id IN ('.implode(",",$where).')');
			$db->setQuery($query);
			
			if(($result = $db->loadResultArray()) != null) {
				return $result;
			}
		}
		return false;	
	}
	
	public static function hasUserID($user_id=0) {
		if($user_id > 0) {
			$db = &JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('a.id');
			$query->from('#__users AS a');
			$query->where('a.id = '.$db->quote($user_id));
			$db->setQuery($query);
			if(($result = $db->loadResult()) != null) {
				if($result == $user_id) {
					return true;
				}
			}
		}
		return false;
	}	
}

?>