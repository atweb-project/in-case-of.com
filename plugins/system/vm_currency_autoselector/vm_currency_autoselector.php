<?php

/**
 * $Id: vm_currency_autoselector.php 1.0.0 2013-04-04 17:31:42 Alex Lavrik $
 * @package	    Joomla! 
 * @subpackage	VM Country Detect
 * @version     1.2.0
 * @description 
 * @copyright	  Copyright © 2013 Alex Lavrik All rights reserved.
 * @license		  GNU General Public License v2.0
 * @author		  Alex Lavrik
 * @author mail	lavrik_av@yahoo.com
 * @website		  www.mysite4u.net
 *
 * 
* See COPYRIGHT.php for more copyright notices and details.

* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
* 
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * @package	    Joomla! 
 * @subpackage	VM Country Detect
 * @class       plgSystemVm_currency_autoselector
 * @since       1.5
 */
 
class plgSystemVm_currency_autoselector extends JPlugin {
	
	
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @access	      protected
	 * @param	object	$subject The object to observe
	 */
	 
	 
	var $pluigin_id;
	var $db;
	var $mainframe;
	var $default_currency;
	
	 
   function plgSystemVm_currency_autoselector( &$subject, $config )
   {
        parent::__construct( $subject, $config );
	
		$this->db = & JFactory::getDBO();
		
		$this->plugin_id = $this->getPluginIdByName($this->get('_name'));
		$this->extension_id = JRequest::getVar('extension_id');
		
		$app = JFactory::getApplication();
		$this->mainframe = Jfactory::getApplication();
		
		if(!$this->vendor_default_currency)
			$this->default_currency = $this->getDefaultVendorCurrency();
			
   }
   /**
     * Do something onAfterRoute 
     *
	   * @access	
	   * @param	
     */
    function onAfterRoute()
    {
		
		if($this->params->get("test")) {
			
			$this->TestMode();		
				
		}
		else{
			
			$this->WorkingMode();	
								
		}

    }

	function MessageOutput( $message ){
		
		if(JFactory::getApplication()->isAdmin()) {
			
			if($this->extension_id == $this->plugin_id)
			
				JError::raiseNotice( 100, "Plug-in VM Currency AutoSelector. ".$message );
			
		}
		else{
			
			JError::raiseNotice( 100, "Plug-in VM Currency AutoSelector. ".$message );			
			
		}
	}
	
	function WorkingMode(){

		/* test servers */
		/* 157.166.226.25 - cnn.com */
		/* 212.58.253.67 - bbc.co.uk */
		/* 202.108.119.195 - chinaview.cn */
		/* 146.159.229.70 - swissinfo.ch */
		
		$visitor_ip = NULL;
		$visitor_ip = $_SERVER["REMOTE_ADDR"]; 
		
		if(!$visitor_ip){
			$visitor_ip = $this->_getClientIP(); 
			$visitor_ip = explode(",", $visitor_ip);
			$visitor_ip = $visitor_ip[0];			
		}
				
		$detected_country_code = NULL;
								
		$detected_country_code = $this->getIPcountry($visitor_ip);
		
		if(!$detected_country_code)
				$detected_country_code = $this->getCountryByIP($visitor_ip);
		
		if($this->params->get("visitorip")) {

			if(!$detected_country_code) {
				
				$this->MessageOutput(  "--------------------" );
				$this->MessageOutput(  "Can't detect country");
				$this->MessageOutput(  "--------------------" );
				
				return NULL;
				
			}
			
			$this->MessageOutput(  "--------------------" );
			$this->MessageOutput(  "Visitor IP: ".$visitor_ip);
			$this->MessageOutput(  "--------------------" );
			
			$this->MessageOutput(  "--------------------" );
			$this->MessageOutput(  "Detected code: ".$detected_country_code);
			$this->MessageOutput(  "--------------------" );
			
			$this->MessageOutput(  "--------------------" );
			$this->MessageOutput(  'Detected country : '.$this->getCountryNameByCode($detected_country_code));
			$this->MessageOutput(  "--------------------" );
			
		}
				
		/* applying rules */
		
		$rule = FALSE;
		
		for($i = 1; $i < 6; $i++){
			
			if($this->params->get("currency".$i) && $this->params->get("country".$i)){
				
				$countries_list = str_replace(";","",$this->params->get("country".$i));
				$countries_list = explode("\r\n",$countries_list);
												
				if(in_array($detected_country_code, $countries_list)){
													
					$currency_code = $this->getCurrencyCode($this->params->get("currency".$i));
					
					if($currency_code){
						
						$this->mainframe->setUserState( "virtuemart_currency_id", $currency_code );	
																		
						$rule = TRUE;
						
					}
					
				}
			}			
		}
		
		if(!$rule) {
			
			$this->mainframe->setUserState( "virtuemart_currency_id", $this->vendor_default_currency );
			
		}		
								
		/* applying rules */
		
	}
			
	function TestMode(){		
				
		if($this->params->get("ctrip")){
			
			/* test by ip */
			$visitor_ip = $this->params->get("ip");
			
						
			$this->MessageOutput(  'Test mode. You are using IP: '.$this->params->get("ip") );
			
			$this->MessageOutput(  'Default Vendor Currency: '.$this->getCurrencyName($this->default_currency));
			
			$this->MessageOutput(  'Detected country code: '.$this->getCountryByIP($visitor_ip)  );
			
			$detected_country_code = $this->getCountryByIP($visitor_ip);
			
			$this->MessageOutput(  'Detected country : '.$this->getCountryNameByCode($this->getCountryByIP($visitor_ip))  );
			
		}
		else{
			
			/* test by country */
			/* test servers */
			/* 157.166.226.25 - cnn.com */
			/* 212.58.253.67 - bbc.co.uk */
			/* 202.108.119.195 - chinaview.cn */
			/* 146.159.229.70 - swissinfo.ch */
			
			/* it needs to have accepted currency for vendor to set some currency!!! */
			/* it's recommended to hide currency selector */
			
			$detected_country_code = $this->params->get("ctrlist");				
			
			$this->MessageOutput(  'Test mode. You are using Country Code: '.$this->params->get("ctrlist") );
			
			$this->MessageOutput(  'Detected country : '.$this->getCountryNameByCode($detected_country_code)  );
			
		}
		
		/* applying rules */
		
		$rule = FALSE;
		
		for($i = 1; $i < 6; $i++){
			
			if($this->params->get("currency".$i) && $this->params->get("country".$i)){
				
				$countries_list = str_replace(";","",$this->params->get("country".$i));
				$countries_list = explode("\r\n",$countries_list);
												
				if(in_array($detected_country_code, $countries_list)){
										
					$this->MessageOutput(  "--------------------" );
			
					$this->MessageOutput(  'Rule #: '.$i );
			
					$currency_code = $this->getCurrencyCode($this->params->get("currency".$i));
										
					if($currency_code){
						
						$this->mainframe->setUserState( "virtuemart_currency_id", $currency_code );	
												
						$this->MessageOutput(  'Currency set to: '.$this->params->get("currency".$i));
												
						$this->MessageOutput(  "--------------------" );
						
						$rule = TRUE;
					}
					else {
						
						$this->MessageOutput(  "Rule $i. Wrong currency code: ".$this->params->get("currency".$i));
												
						$this->MessageOutput(  "--------------------" );
						
						$rule = FALSE;
						
					}
					
				}
			}			
		}
		
		if(!$rule) {
			
			$this->mainframe->setUserState( "virtuemart_currency_id", $this->vendor_default_currency );
			$this->MessageOutput(  'Plug-in VM Country AutoSelector. Currency set to default vendor currency' );
			
		}
							
		/* applying rules */
		
	}

	function getDefaultVendorCurrency(){
		
		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');

		VmConfig::loadConfig();
		VmConfig::loadJLang('mod_virtuemart_currencies', true);

		$mainframe = Jfactory::getApplication();
		$vendorId = JRequest::getInt('vendorid', 1);
		
		$q  = 'SELECT 
		CONCAT(`vendor_accepted_currencies`, ",",`vendor_currency`) AS all_currencies, 
		`vendor_currency` 
		FROM 
		`#__virtuemart_vendors` 
		WHERE 
		`virtuemart_vendor_id`='.$vendorId;
		
		$this->db->setQuery($q);
		
		$vendor_currency = $this->db->loadAssoc();
		
		if($vendor_currency['vendor_currency'])
			return $vendor_currency['vendor_currency'];
		
		return FALSE;
		
	}

	function getCurrencyCode($currency){
		
		$q = "
			SELECT 
			virtuemart_currency_id
			FROM 
			#__virtuemart_currencies
			WHERE
			currency_code_3 = '$currency'
		";
		
		$this->db->setQuery($q);
		
		return $this->db->loadResult();
		
	}
	function getCurrencyName($currency_id){
		
		$q = "
			SELECT 
			currency_code_3
			FROM 
			#__virtuemart_currencies
			WHERE
			virtuemart_currency_id = $currency_id
		";
		
		$this->db->setQuery($q);
		
		return $this->db->loadResult();
		
	}
	
	function getCountryNameByCode($code){
		
		$q = "
			SELECT 
			country_name 
			FROM 
			#__virtuemart_countries 
			WHERE 
			country_2_code = '$code'		
		";
		
		$this->db->setQuery($q);
		
		return $this->db->loadResult();
		
	}
	
	function getCountryByIP($ip){
		
		$ctry = NULL;
		
		$country_array = array();
				
		exec("whois $ip", $country_array);
				
		krsort($country_array);
		
		$country = "";
		
		foreach($country_array as $key => $value){
			
			$ctr = strtolower($value);
			
			if( strpos($ctr, 'country:') === 0){
				
				$ctr = explode(":", $ctr);
				
				$country = $ctr[1];
				
				break;
				
			}
		}
		
		if($country){
			
			$s = str_split($country);
			
			foreach($s as $value){
				
				if($value != " ")
					$ctry .= $value;
					
			}
			
			return strtoupper($ctry);			
		}
			
		return $ctry;		
	}
	
	function getPluginIdByName($plugin_name){
		
		$q = "
			SELECT
			extension_id
			FROM 
			#__extensions
			WHERE 
			type = 'plugin'
			AND
			element = '$plugin_name'
		";
		
		$this->db->setQuery($q);
		
		return $this->db->loadResult();
		
	}
	
	public function getIPcountry($ip) {
				
		$ipInf = $this->getIPinfo($ip);
		
		if (empty($ipInf))
			return null;

		$country = explode(' - ', $ipInf['country']);
		

		return !empty($country[0]) ? $country[0] : null;
	}
	
	public function getIPinfo($ip) {
		
		if(!filter_var($ip, FILTER_VALIDATE_IP))
			return false;

		$response = @file_get_contents('http://www.netip.de/search?query='.$ip);

		if (empty($response))
			return false;

		$patterns=array();
		$patterns["domain"] = '#Domain: (.*?)&nbsp;#i';
		$patterns["country"] = '#Country: (.*?)&nbsp;#i';
		$patterns["state"] = '#State/Region: (.*?)<br#i';
		$patterns["town"] = '#City: (.*?)<br#i';

		$ipInfo=array();

		foreach ($patterns as $key => $pattern)
			$ipInfo[$key] = preg_match($pattern, $response, $value) && !empty($value[1]) ? $value[1] : null;

		return $ipInfo;
	}
	
	function _getClientIP() {
		
		if (isset($_SERVER)) {

		    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
		        return $_SERVER["HTTP_X_FORWARDED_FOR"];

		    if (isset($_SERVER["HTTP_CLIENT_IP"]))
		        return $_SERVER["HTTP_CLIENT_IP"];

		    return $_SERVER["REMOTE_ADDR"];
		}

		if (getenv('HTTP_X_FORWARDED_FOR'))
		    return getenv('HTTP_X_FORWARDED_FOR');

		if (getenv('HTTP_CLIENT_IP'))
		    return getenv('HTTP_CLIENT_IP');

		return getenv('REMOTE_ADDR');
		
	}	

   /**
     * Do something onAfterDispatch 
     *
	   * @access	
	   * @param	
     */
    function onAfterDispatch()
    {
      // Your custom code here    
    }

   /**
     * Do something onAfterRender 
     *
	   * @access	
	   * @param	
     */
    function onAfterRender()
    {
      // Your custom code here    
    }
    
} // END PLUGIN  Vmcountrydetect

?>