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

// import joomla controller library
jimport( 'joomla.application.component.controller' );
jimport( 'joomla.user.helper' );

class XdownloaderController extends JController {
	protected $_plg_key = 'xdldr';
	
	function __construct($config=array()) {
		parent::__construct($config);
	}
	
    public function  getModel($name = 'XdldrStock', $prefix = 'Model', $config = array()) {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }
    
	public function checkDownload() {
		$id = JRequest::getVar('id', '', 'get', 'string');
		$session = JFactory::getSession();
		$user = JFactory::getUser();
		$link = JRoute::_('index.php');
		
		$msg = JText::_('COM_XDOWNLOADER_FILENOTFOUND');
		if(!empty($id)) {
			$sess_key = md5($this->_plg_key.JUtility::getToken());
			if($session->has($sess_key)) {
				$datastore = $session->get($sess_key);
				
				if(array_key_exists($id, $datastore)) {
					$details = $datastore[$id];

					$menu = $details['menu'];
					$link = JRoute::_($menu); 
					$link = str_replace(JURI::base(), '', $link);
					
					if(file_exists($details['filepath'])) {
						$hasAccess = true;
						if(array_key_exists('groups', $details)) {
							if(!empty($details['groups'])) {
								$hasAccess = false;
								$msg = JText::_('COM_XDOWNLOADER_DOWNLOADACCESS');
								if($user->get('guest') == 0) {
									$userGroups = JUserHelper::getUserGroups($user->get('id'));
									foreach($details['groups'] as $group) {
										if(in_array($group, $userGroups)) {
											$details['user_group'] = $group;
											$hasAccess = true;
											break;
										}
									}
								}
							}					
						}
						
						if($hasAccess) {
							$this->saveDownload($user, $details);
							$this->initDownload($details);
						}
					}
					else {
						$msg = JText::_('COM_XDOWNLOADER_FILENOTFOUND');
					}
					unset($datastore);
				}
			}
			else {
				$msg = JText::_('JLIB_ENVIRONMENT_SESSION_EXPIRED');
			}	
		}
		
		$this->setRedirect($link, $msg);
	}

	protected function saveDownload(&$user, &$details) {
		$model = $this->getModel();
		if(!empty($details)) {
			$dataStock = array();
			$dataStock['menu'] = $details['menu'];
			$dataStock['guest'] = $user->get('guest');
			$dataStock['link_groups'] = '';
			
			if(array_key_exists('link_groups', $details)) {
				$dataStock['link_groups'] = $details['link_groups'];	
			}
			
			if((int)$user->get('guest') == 0) {
				$dataStock['user_id'] = $user->get('id');
				$dataStock['user_group'] = $details['user_group'];
				$dataStock['user_alias'] = $user->get('name');
			}
			$dataStock['user_ip'] = ip2long($_SERVER['REMOTE_ADDR']); // MySQL INET_ATON(); backwards INET_NTOA()
			
			$dataStock['ip_location'] = 'N/A';
			if('127.0.0.1' == $_SERVER['REMOTE_ADDR']) {
				$dataStock['ip_location'] = 'localhost';
			}
			else {
				$xwhois = new xwhoisHelper($_SERVER['REMOTE_ADDR']);
				if($xwhois->getWhois()) {
					if(is_array($xwhois->country) && count($xwhois->country) == 1) {
						$dataStock['ip_location'] = $xwhois->country[0];
					}
				}
			}
			
			$dataStock['title'] = $details['title'];
			$dataStock['filename'] = $details['filename'];
			$dataStock['filepath'] = $details['filepath'];
			$dataStock['dwn_date'] = &JFactory::getDate()->toMySQL();

			$model->insertData($dataStock);
			unset($dataStock);
		}
	}
	
	protected function initDownload(&$details) {
		if(!empty($details)) {
			// Must be fresh start
			if( headers_sent() )
				die('Headers Sent');
			
			// Required for some browsers
			if(ini_get('zlib.output_compression'))
			ini_set('zlib.output_compression', 'Off');
			if( file_exists($details['filepath']) ){
				// Parse Info / Get Extension
				$fsize = filesize($details['filepath']);
				$path_parts = pathinfo($details['filepath']);
				$ext = strtolower($path_parts["extension"]);
				// Determine Content Type
				switch ($ext) {
					case "pdf": 
						$ctype="application/pdf"; 
						break;
					case "exe": 
						$ctype="application/octet-stream"; 
						break;
					case "zip": 
						$ctype="application/zip"; 
						break;
					case "doc": 
						$ctype="application/msword"; 
						break;
					case "xls": 
						$ctype="application/vnd.ms-excel"; 
						break;
					case "ppt": 
						$ctype="application/vnd.ms-powerpoint"; 
						break;
					case "gif": 
						$ctype="image/gif"; 
						break;
					case "png": 
						$ctype="image/png"; 
						break;
					case "jpeg":
					case "jpg": 
						$ctype="image/jpg"; 
						break;
					default: 
						$ctype="application/force-download";
				}
				header("Pragma: public"); // required
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Cache-Control: private",false); // required for certain browsers
				header("Content-Type: ".$ctype);
				header("Content-Disposition: attachment; filename=\"".$details['filename']."\";" );
				header("Content-Transfer-Encoding: binary");
				header("Content-Length: ".$fsize);
				ob_clean();
				flush();
				@readfile( $details['filepath'] );
				exit();
			}
		}
	}
	
}
?>
