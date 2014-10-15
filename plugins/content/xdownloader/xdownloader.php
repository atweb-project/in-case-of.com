<?php
/*------------------------------------------------------------------------
# plg_xdownloader - xDownloader alpha plug-in
# ------------------------------------------------------------------------
# author    Dmitri Gorbunov
# copyright Copyright (C) 2012 xrbyte.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.xrbyte.com
# Technical Support:  Forum - http://www.xrbyte.com/forum
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.user.helper' );

class plgContentXdownloader extends JPlugin
{	
	protected $_plugin_number	= 0;
	protected $_menu = '';
	protected $_sess_key = '';
	protected $_plg_key = 'xdldr';
	
	public function __construct(& $subject, $config) {	
		parent::__construct($subject, $config);
		$this->_updateSessionKey();
		$this->initJavaScript();
	}
	
	protected function _setPluginNumber() {
		$this->_plugin_number = (int)$this->_plugin_number + 1;
	}
	
	protected function _updateSessionKey() {
		$this->_sess_key = md5($this->_plg_key.JUtility::getToken());
		$session = &JFactory::getSession();
		if(!$session->has($this->_sess_key)) {
			$session->set($this->_sess_key, array());	
		}
		else {
			$session->clear($this->_sess_key);
			$session->set($this->_sess_key, array());
		}
	}
	
	public function onContentPrepare($context, &$article, &$params, $page = 0) {
		$session = &JFactory::getSession();
		$menu = &JFactory::getURI();
		$this->_menu = $menu->getScheme().'://'.$menu->getHost().JRequest::getURI();
		$session->set($this->_plg_key.'.menu', $this->_menu);

		// Load plugin language
		$lang = &JFactory::getLanguage();
		$lang->load('plg_xdownloader', JPATH_ADMINISTRATOR);		
		
		// Start Plugin
		$regex_one		= '/({xdownloader\s*)(.*?)(})/si';
		$regex_all		= '/{xdownloader\s*.*?}/si';
		$matches 		= array();
		$count_matches	= preg_match_all($regex_all,$article->text,$matches,PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);
		
		// Start if count_matches
		if ($count_matches != 0) {
		
			for($i = 0; $i < $count_matches; $i++) {
				$this->_setPluginNumber();
				
				// Plugin variables
				$title		= '';
				$filename 	= '';
				$filepath 	= '';
				$groups		= '';
				
				// Get plugin parameters
				$xdownloader = $matches[0][$i][0];
				preg_match($regex_one,$xdownloader,$xdownloader_parts);
				$parts			= explode("|", $xdownloader_parts[2]);
				$values_replace = array ("/^'/", "/'$/", "/^&#39;/", "/&#39;$/", "/<br \/>/");
				foreach($parts as $key => $value) {
					$values = explode("=", $value, 2);

					foreach ($values_replace as $key2 => $values2) {
						$values = preg_replace($values2, '', $values);
					}

					// Get plugin parameters from article
					if($values[0]=='title') {
						$title = $values[1];
					}
					if($values[0]=='filename') {
						$filename = $values[1];
					}
					else if($values[0]=='filepath') {
						$filepath = JPath::clean($values[1], '/');
					}
					else if($values[0]=='groups') {
						$groups = $values[1];
					}
				}
				
				// Check parts
				if(empty($title)) {
					if(!empty($filename)) {
						$title = $filename;
					}
					else if(!empty($filepath)) {
						$title = basename($filepath);
					}
					else {
						$title = 'file not found';
					}
				}
				
				if(empty($filename)) {
					if(!empty($filepath)) {
						$filename = basename($filepath);
					}
					else {
						$filename = 'no file';
					}
				}

				$token = JUtility::getHash(JUserHelper::genRandomPassword());
				$details = array();
				$details['title'] = $title;
				$details['filename'] = $filename;
				$details['filepath'] = $filepath;
				$details['menu'] = $this->_menu;
				
				$arrayGroups = array();
				$byGroupAccess = false;
				if(!empty($groups)) {
					$details['link_groups'] = $groups;
					$arrayGroups = explode(',', $groups);	
					$user = &JFactory::getUser();
					$userGroups = $user->get('groups');
					$arrayGroups = $this->convertGroupNameToId($arrayGroups);
					$details['groups'] = $arrayGroups;				
					
					foreach($arrayGroups as $gName) {
						if(in_array($gName, $userGroups)) {
							$byGroupAccess = true;
						}
					}
				}
				
				$datastore = $session->get($this->_sess_key, array());
				$datastore[$token] = $details;
				
				$link = '';				
				if(JFile::exists($filepath)) {
					$session->set($this->_sess_key, $datastore);
					
					if($byGroupAccess) {
						$link = 'jsXDownloader(\''.$token.'\');';
					}
					else if(!$byGroupAccess && empty($arrayGroups)) {
						$link = 'jsXDownloader(\''.$token.'\');';
					}
					else {
						$link = 'alert(\''.JText::_('PLG_XDOWNLOADER_PERMISSION_DENIED').'\');';
					}
				}
				
				$countPlg = $this->_plugin_number;
				$output = '<a class="downloadbtn" href="javascript: void(0);"';
				if(!empty($link)) {
					$output .= ' onclick="'.$link.'"';
				}
				$output .= '>';
				$output .= $title;
				$output .= '</a>';
				
				$article->text = preg_replace($regex_all, $output, $article->text, 1);
			}
			
		}// end if count_matches

		return true;
	}
	
	protected function initJavaScript() {
		$document = &JFactory::getDocument();
		$jsScript = '
			var jsXDownloader = function(val) {
				var rurl = \''.base64_encode('index.php?option=com_xdownloader&task=checkdownload&id=').'\';
				window.location.href = base64_decode(rurl) + val;;
			};

			var base64_decode = function( data ) {	// Decodes data encoded with MIME base64
				// 
				// +   original by: Tyler Akins (http://rumkin.com)


				var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
				var o1, o2, o3, h1, h2, h3, h4, bits, i=0, enc=\'\';
			
				do {  // unpack four hexets into three octets using index points in b64
					h1 = b64.indexOf(data.charAt(i++));
					h2 = b64.indexOf(data.charAt(i++));
					h3 = b64.indexOf(data.charAt(i++));
					h4 = b64.indexOf(data.charAt(i++));
			
					bits = h1<<18 | h2<<12 | h3<<6 | h4;
			
					o1 = bits>>16 & 0xff;
					o2 = bits>>8 & 0xff;
					o3 = bits & 0xff;
			
					if (h3 == 64)	  enc += String.fromCharCode(o1);
					else if (h4 == 64) enc += String.fromCharCode(o1, o2);
					else			   enc += String.fromCharCode(o1, o2, o3);
				} while (i < data.length);
			
				return enc;
			};			
		';
		$document->addScriptDeclaration($jsScript);
	}
	
	protected function convertGroupNameToId($groupNames=array()) {
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
	
}
?>