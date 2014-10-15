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

class xwhoisHelper {
	
	public $country = '';
	protected $_url = '';

	public function __construct($ip = '')
	{
		if(!empty($ip)) {
			$this->_url = 'http://smartwhois.com/whois/'.$ip;
		}
				
	}
	
	public function getWhois() {
		if(!empty($this->_url)) {
			$response = file_get_contents($this->_url);
			if (strstr($response, "No whois server is known for this kind of object.")) { 
				return false; 
			}
			
			$this->country = $this->getSegment('country:' , $response);
			if(is_array($this->country) && !empty($this->country)) {
				return true;
			}			
		}
		return false;
	} 

	protected function getSegment($segment, $source)
	{
		// searching for segments
		$pattern = '/' . $segment . '(.*?)<[^>]*>/i';
//		$pattern = '/' . $segment . '([^\n]*)\n/i';
		$count = preg_match_all($pattern, $source, $matches);

		if(is_array($matches[1]) && !empty($matches[1])) {
			$result = $matches[1];
			return $result;
		}
				
		/* split to array strings devided by tags */
/*		$segmentArray = preg_split("'<[\/\!]*?[^<>]*?>'si", $matches[1], 2, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE); */			
//		$segment = preg_replace('/<[^>]*>/', '', $matches[1]); // <[^>]*>		
		return false;
	}
}

?>