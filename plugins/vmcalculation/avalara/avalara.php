<?php

if (!defined('_JEXEC'))
die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

/**
 * Calculation plugin for quantity based price rules
 *
 * @version $Id:$
 * @package VirtueMart
 * @subpackage Plugins - avalara
 * @author Max Milbers
 * @copyright Copyright (C) 2012 iStraxx - All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 *
 */

if (!class_exists('vmCalculationPlugin')) require(JPATH_VM_PLUGINS.DS.'vmcalculationplugin.php');

defined('AVATAX_DEV') or define('AVATAX_DEV', 0);
defined('AVATAX_DEBUG') or define('AVATAX_DEBUG', 0);

function avadebug($string,$arg=NULL){
	if(AVATAX_DEBUG) vmdebug($string,$arg);
}

class plgVmCalculationAvalara extends vmCalculationPlugin {

	var $_connectionType = 'Production';

	function __construct(& $subject, $config) {
		// 		if(self::$_this) return self::$_this;
		parent::__construct($subject, $config);

		$varsToPush = array(
			'activated'          => array(0, 'int'),
			'company_code'       => array('', 'char'),
			'account'       => array('', 'char'),
			'license'     => array('', 'char'),
			'committ'   => array(0,'int'),
			'only_cart' => array(0,'int'),
			'avatax_virtuemart_country_id'  => array(0,'int'), //TODO should be a country dropdown multiselect box
		);

		$this->setConfigParameterable ('calc_params', $varsToPush);

		$this->_loggable = TRUE;
		$this->tableFields = array('id', 'virtuemart_order_id', 'client_ip', 'sentValue','recievedValue');
		$this->_tableId = 'id';
		$this->_tablepkey = 'id';

		if (JVM_VERSION === 2) {
			define ('VMAVALARA_PATH', JPATH_ROOT . DS . 'plugins' . DS . 'vmcalculation' . DS . 'avalara' );
		} else {
			define ('VMAVALARA_PATH', JPATH_ROOT . DS . 'plugins' . DS . 'vmcalculation' );
		}
		define('VMAVALARA_CLASS_PATH', VMAVALARA_PATH . DS . 'classes' );

		require(VMAVALARA_PATH.DS.'AvaTax.php');	// include in all Avalara Scripts

		if(!class_exists('ATConfig')) require (VMAVALARA_CLASS_PATH.DS.'ATConfig.class.php');

	}


	function  plgVmOnStoreInstallPluginTable($jplugin_name) {
//return $this->onStoreInstallPluginTable('calculation');
	}


	/**
	 * Gets the sql for creation of the table
	 * @author Max Milbers
	 */
	public function getVmPluginCreateTableSQL() {

 		return "CREATE TABLE IF NOT EXISTS `" . $this->_tablename . "` (
 			    `id` mediumint(1) unsigned NOT NULL AUTO_INCREMENT ,
 			    `virtuemart_calc_id` mediumint(1) UNSIGNED DEFAULT NULL,
 			    `activated` int(1),
 			    `account` char(255),
 			    `license` char(255),
 			    `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
 			    `created_by` int(11) NOT NULL DEFAULT 0,
 			    `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 			    `modified_by` int(11) NOT NULL DEFAULT 0,
 			    `locked_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 			    `locked_by` int(11) NOT NULL DEFAULT 0,
 			     PRIMARY KEY (`id`),
 			     KEY `idx_virtuemart_calc_id` (`virtuemart_calc_id`)
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Table for avalara' AUTO_INCREMENT=1 ;";

	}


	function plgVmAddMathOp(&$entryPoints){
 		$entryPoints[] = array('calc_value_mathop' => 'avalara', 'calc_value_mathop_name' => 'Avalara');
	}

	function plgVmOnDisplayEdit(&$calc,&$html){

		$html .= '<fieldset>
	<legend>'.JText::_('VMCALCULATION_AVALARA').'</legend>
	<table class="admintable">';

		$html .= VmHTML::row('checkbox','VMCALCULATION_AVALARA_ACTIVATED','activated',$calc->activated);
		$html .= VmHTML::row('input','VMCALCULATION_AVALARA_COMPANY_CODE','company_code',$calc->company_code);
		$html .= VmHTML::row('input','VMCALCULATION_AVALARA_ACCOUNT','account',$calc->account);
		$html .= VmHTML::row('input','VMCALCULATION_AVALARA_LICENSE','license',$calc->license);
		$html .= VmHTML::row('checkbox','VMCALCULATION_AVALARA_COMMITT','committ',$calc->committ);
		$html .= VmHTML::row('checkbox','VMCALCULATION_AVALARA_ONLYCART','only_cart',$calc->only_cart);

		$label = 'VMCALCULATION_AVALARA_VADDRESS';
		$lang =JFactory::getLanguage();
		$label = $lang->hasKey($label.'_TIP') ? '<span class="hasTip" title="'.JText::_($label.'_TIP').'">'.JText::_($label).'</span>' : JText::_($label) ;
		$html .= '
		<tr>
			<td class="key">
				'.$label.'
			</td>
			<td>
				'.shopfunctions::renderCountryList($calc->avatax_virtuemart_country_id,TRUE,array(),'avatax_').'
			</td>
		</tr>';

		//$html .= VmHTML::row('checkbox','VMCALCULATION_AVALARA_VADDRESS','vAddress',$calc->vAddress);
	//	$html .= VmHTML::row('checkbox','VMCALCULATION_ISTRAXX_AVALARA_TRACE','trace',$calc->trace);

		$html .= '</table>';
		if ($calc->activated) {
			$html .= $this->ping($calc);
		}
		$html .= JText::_('VMCALCULATION_AVALARA_MANUAL').'</fieldset>';
		return TRUE;
	}

	function newATConfig($calc){

		if(!class_exists('TextCase')) require (VMAVALARA_CLASS_PATH.DS.'TextCase.class.php');

		$__wsdldir = VMAVALARA_CLASS_PATH."/wsdl";
		$standard = array(
			'url'       => 'no url specified',
			'addressService' => '/Address/AddressSvc.asmx',
			'taxService' => '/Tax/TaxSvc.asmx',
			'batchService'=> '/Batch/BatchSvc.asmx',
			'avacertService'=> '/AvaCert/AvaCertSvc.asmx',
			'addressWSDL' => 'file://'.$__wsdldir.'/Address.wsdl',
			'taxWSDL'  => 'file://'.$__wsdldir.'/Tax.wsdl',
			'batchWSDL'  => 'file://'.$__wsdldir.'/BatchSvc.wsdl',
			'avacertWSDL'  => 'file://'.$__wsdldir.'/AvaCertSvc.wsdl',
			'account'   => '<your account number here>',
			'license'   => '<your license key here>',
			'adapter'   => 'avatax4php,5.10.0.0',
			'client'    => 'VirtueMart2.0.18',
			'name'    => 'PHPAdapter',
			'TextCase' => TextCase::$Mixed,
			'trace'     => TRUE);

		//VmConfig::$echoDebug = TRUE;
		//if(!is_object())avadebug($calc);
		if(!class_exists('ATConfig')) require (VMAVALARA_CLASS_PATH.DS.'ATConfig.class.php');

		//Set this to TRUE for development account
		if(AVATAX_DEV){
			$this->_connectionType = 'Development';
			$devValues = array(
				'url'       => 'https://development.avalara.net',
				'account'   => $calc->account,
				'license'   => $calc->license,
				'trace'     => TRUE); // change to false for production
			$resultingConfig = array_merge($standard,$devValues);
			$config = new ATConfig($this->_connectionType, $resultingConfig);

		} else {
			$this->_connectionType = 'Production';
			$prodValues = array(
				'url'       => 'https://avatax.avalara.net',
				'account'   => $calc->account,
				'license'   => $calc->license,
				'trace'     => FALSE);
			$resultingConfig = array_merge($standard,$prodValues);
			$config = new ATConfig($this->_connectionType, $resultingConfig);

		}

		return $config;
	}

	function ping ($calc) {

		$html = '';
		$this->newATConfig($calc);

		if(!class_exists('TaxServiceSoap')) require (VMAVALARA_CLASS_PATH.DS.'TaxServiceSoap.class.php');
		$client = new TaxServiceSoap($this->_connectionType);

		try
		{
			if(!class_exists('PingResult')) require (VMAVALARA_CLASS_PATH.DS.'PingResult.class.php');
			$result = $client->ping("TEST");
			vmInfo('Avalara Ping ResultCode is: '. $result->getResultCode() );

			if(!class_exists('SeverityLevel')) require (VMAVALARA_CLASS_PATH.DS.'SeverityLevel.class.php');
			if($result->getResultCode() != SeverityLevel::$Success)	// call failed
			{
				foreach($result->Messages() as $msg)
				{
					$html .= $msg->Name().": ".$msg->Summary()."<br />";
				}

			}
			else // successful calll
			{
				vmInfo('Avalara used Ping Version is: '. $result->getVersion() );
			}
		}
		catch(SoapFault $exception)
		{

			$err = "Exception: ping ";
			if($exception)
				$err .= $exception->faultstring;

			$err .='<br />';
			$err .='last request: '. $client->__getLastRequest().'<br />';
			$err .='last response: '. $client->__getLastResponse().'<br />';
			vmError($err);
			avadebug('AvaTax the ping throws exception ',$exception);
		}

		return $html;
	}

	static $validatedAddresses = NULL;

	private function fillValidateAvalaraAddress($calc){

		if(!isset(self::$validatedAddresses)){

			$vmadd = $this->getShopperData($calc);

			if(!empty($vmadd)){

				//First country check
				if(empty($vmadd['virtuemart_country_id'])){

					self::$validatedAddresses = FALSE;
					return self::$validatedAddresses;
				} else {
					if(empty($calc->avatax_virtuemart_country_id)){
						vmError('AvaTax, please select countries, to validate');
						self::$validatedAddresses = FALSE;
						return self::$validatedAddresses;
					} else {
						if(!is_array($calc->avatax_virtuemart_country_id)){
							//Suppress Warning
							$calc->avatax_virtuemart_country_id = @unserialize($calc->avatax_virtuemart_country_id);
						}
						if(!in_array($vmadd['virtuemart_country_id'],$calc->avatax_virtuemart_country_id)){
							avadebug('fillValidateAvalaraAddress not validated, country not set');
							self::$validatedAddresses = FALSE;
							return self::$validatedAddresses;
						}

					}
				}
				$config = $this->newATConfig($calc);

				if(!class_exists('AddressServiceSoap')) require (VMAVALARA_CLASS_PATH.DS.'AddressServiceSoap.class.php');
				$client = new AddressServiceSoap($this->_connectionType,$config);

				if(!class_exists('Address')) require (VMAVALARA_CLASS_PATH.DS.'Address.class.php');
				$address = new Address();
				if(isset($vmadd['address_1'])) $address->setLine1($vmadd['address_1']);
				if(isset($vmadd['address_2'])) $address->setLine2($vmadd['address_2']);
				if(isset($vmadd['city'])) $address->setCity($vmadd['city']);

				if(isset($vmadd['virtuemart_country_id'])){

					$vmadd['country'] = ShopFunctions::getCountryByID($vmadd['virtuemart_country_id'],'country_2_code');
					if(isset($vmadd['country'])) $address->setCountry($vmadd['country']);
				}
				if(isset($vmadd['virtuemart_state_id'])){
					$vmadd['state'] = ShopFunctions::getStateByID($vmadd['virtuemart_state_id'],'state_2_code');
					if(isset($vmadd['state'])) $address->setRegion($vmadd['state']);
				}

				if(isset($vmadd['zip'])) $address->setPostalCode($vmadd['zip']);

				if(!class_exists('SeverityLevel')) require (VMAVALARA_CLASS_PATH.DS.'SeverityLevel.class.php');
				if(!class_exists('Message')) require (VMAVALARA_CLASS_PATH.DS.'Message.class.php');

				//if($calc->vAddress==0){
				if(isset($vmadd['country']) and $vmadd['country']!= 'US' and $vmadd['country']!= 'CA'){


					self::$validatedAddresses = array($address);
					return self::$validatedAddresses;
				}

				$address->Coordinates = 1;
				$address->Taxability = TRUE;
				$textCase = TextCase::$Mixed;
				$coordinates = 1;

				if(!class_exists('ValidateResult')) require (VMAVALARA_CLASS_PATH.DS.'ValidateResult.class.php');
				if(!class_exists('ValidateRequest')) require (VMAVALARA_CLASS_PATH.DS.'ValidateRequest.class.php');
				if(!class_exists('ValidAddress')) require (VMAVALARA_CLASS_PATH.DS.'ValidAddress.class.php');

				//TODO add customer code //shopper_number
				try
				{
					$request = new ValidateRequest($address, ($textCase ? $textCase : TextCase::$Default), $coordinates);
					$result = $client->Validate($request);

					//avadebug('Validate ResultCode is: '. $result->getResultCode());;
					if($result->getResultCode() != SeverityLevel::$Success)
					{
						foreach($result->getMessages() as $msg)
						{
							avadebug('fillValidateAvalaraAddress ' . $msg->getName().": ".$msg->getSummary()."\n");
							//avadebug('fillValidateAvalaraAddress ERROR',$address);
						}
					}
					else
					{
						self::$validatedAddresses = $result->getvalidAddresses();
					}

				}
				catch(SoapFault $exception)
				{
					$msg = "Exception: fillValidateAvalaraAddress ";
					if($exception)
						$msg .= $exception->faultstring;

				 $msg .= "\n";
					$msg .= $client->__getLastRequest()."\n";
					$msg .= $client->__getLastResponse()."\n";
					vmError($msg);
				}

				if(empty(self::$validatedAddresses)){
					self::$validatedAddresses = FALSE;
				}

				//then for BT and/or $cart->STsameAsBT
			} else {
				self::$validatedAddresses = FALSE;
			}
			//avadebug("Number of addresses fillValidateAvalaraAddress is ", self::$validatedAddresses);
		}

		return self::$validatedAddresses;

	}

	static $stop = FALSE;
	private static $_taxResult = NULL;
	function getTax($calculationHelper,$calc,$price,$invoiceNumber=false,$orderNumber = false){

		if($calc->activated==0) return false;

		if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');
		$cart = VirtueMartCart::getCart();

		$products= array();

		if($calculationHelper->inCart){

			$products = $cart->products;
			$prices = $calculationHelper->getCartPrices();
			foreach($products as $k => $product){

				if(!empty($prices[$k]['discountedPriceWithoutTax'])){
					$price = $prices[$k]['discountedPriceWithoutTax'];
				} else if(!empty($prices[$k]['basePriceVariant'])){
					$price = $prices[$k]['basePriceVariant'];
				} else {
					avadebug('There is no price in getTax for product '.$k.' ',$prices);
					$price = 0.0;
				}
				$product->price = $price;

				if(!empty($price[$k]['discountAmount'])){
					$product->discount = $price[$k]['discountAmount'];
				} else {
					$product->discount = FALSE;
				}
			}
		} else {

			$calculationHelper->_product->price = $price;

			$products[0] = $calculationHelper->_product;
			if(!isset($products[0]->amount)){
				$products[0]->amount = 1;
			}

			if(isset($calculationHelper->productPrices['discountAmount'])){
				$products[0]->discount = $calculationHelper->productPrices['discountAmount'];
			} else {
				$products[0]->discount = FALSE;
			}
		}

		if(count($products) == 0){
			return false;
		}
		$shopperData = $this->getShopperData($calc);
		if(!$shopperData){
			return false;
		}

		//if(self::$stop) return self::$stop;

		if(!class_exists('TaxServiceSoap')) require (VMAVALARA_CLASS_PATH.DS.'TaxServiceSoap.class.php');
		if(!class_exists('DocumentType')) require (VMAVALARA_CLASS_PATH.DS.'DocumentType.class.php');
		if(!class_exists('DetailLevel')) require (VMAVALARA_CLASS_PATH.DS.'DetailLevel.class.php');
		if(!class_exists('Line')) require (VMAVALARA_CLASS_PATH.DS.'Line.class.php');
		if(!class_exists('ServiceMode')) require (VMAVALARA_CLASS_PATH.DS.'ServiceMode.class.php');
		if(!class_exists('Line')) require (VMAVALARA_CLASS_PATH.DS.'Line.class.php');
		if(!class_exists('GetTaxRequest')) require (VMAVALARA_CLASS_PATH.DS.'GetTaxRequest.class.php');
		if(!class_exists('GetTaxResult')) require (VMAVALARA_CLASS_PATH.DS.'GetTaxResult.class.php');

		$client = new TaxServiceSoap($this->_connectionType);
		$request= new GetTaxRequest();
		$origin = new Address();

		//$destination = $this->fillValidateAvalaraAddress($calc);


		//In Virtuemart we have not differenct warehouses, but we have a shipment address
		//So when the vendor has a shipment address, we assume that it is his warehouse
		//Later we can combine products with shipment addresses for different warehouse (yehye, future music)
		//But for now we just use the BT address
		if (!class_exists ('VirtueMartModelVendor')) require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'vendor.php');

		$userId = VirtueMartModelVendor::getUserIdByVendorId ($calc->virtuemart_vendor_id);
		$userModel = VmModel::getModel ('user');
		$virtuemart_userinfo_id = $userModel->getBTuserinfo_id ($userId);
		// this is needed to set the correct user id for the vendor when the user is logged
		$userModel->getVendor($calc->virtuemart_vendor_id);
		$vendorFieldsArray = $userModel->getUserInfoInUserFields ('mail', 'BT', $virtuemart_userinfo_id, FALSE, TRUE);
		$vendorFields = $vendorFieldsArray[$virtuemart_userinfo_id];
		//avadebug('my vendor fields',$vendorFields);
		$origin->setLine1($vendorFields['fields']['address_1']['value']);
		$origin->setLine2($vendorFields['fields']['address_2']['value']);
		$origin->setCity($vendorFields['fields']['city']['value']);

		$origin->setCountry($vendorFields['fields']['virtuemart_country_id']['country_2_code']);
		$origin->setRegion($vendorFields['fields']['virtuemart_state_id']['state_2_code']);
		$origin->setPostalCode($vendorFields['fields']['zip']['value']);

		$request->setOriginAddress($origin);	      //Address

		if(isset($this->addresses[0])){
			$destination = $this->addresses[0];
		} else {
			return FALSE;
		}
		$request->setDestinationAddress	($destination);     //Address
		//avadebug('The date',$origin,$destination);
		$request->setCompanyCode($calc->company_code);   // Your Company Code From the Dashboard


		if($calc->committ and $invoiceNumber){
			$request->setDocType(DocumentType::$SalesInvoice);   	// Only supported types are SalesInvoice or SalesOrder
			$request->setCommit(true);
			//invoice number, problem is that the invoice number is at this time not known, but the order_number may reachable
			$request->setDocCode($invoiceNumber);
			avadebug('Request as SalesInvoice with invoiceNumber '.$invoiceNumber);
		} else {
			$request->setDocType(DocumentType::$SalesOrder);
			$request->setCommit(false);
			//invoice number, problem is that the invoice number is at this time not known, neither the order_number
			$request->setDocCode('VM2.0.18_order_request');
			avadebug('Request as SalesOrder');
		}


		$request->setDocDate(date('Y-m-d'));           //date

		//$request->setSalespersonCode("");             // string Optional
		//avadebug(' my customer code '.$shopperData['customer_id']);
		$request->setCustomerCode($shopperData['customer_id']);  //TODO check customer_id existing that way      //string Required

		if(isset($shopperData['tax_usage_type'])){
			$request->setCustomerUsageType($shopperData['tax_usage_type']);   //string   Entity Usage
		}

		$cartPrices = $calculationHelper->getCartPrices();
		//avadebug('$cartPrices',$cartPrices);
		$request->setDiscount($cartPrices['discountAmount']);            //decimal
		//$request->setDiscount(0.0);
		if($orderNumber){
			$request->setPurchaseOrderNo($orderNumber);     //string Optional
		}


		if(isset($shopperData['tax_exemption_number'])){
			$request->setExemptionNo($shopperData['tax_exemption_number']);         //string   if not using ECMS which keys on customer code
		}

		$request->setDetailLevel('Tax');         //Summary or Document or Line or Tax or Diagnostic

	//	$request->setReferenceCode1("");       //string Optional
	//	$request->setReferenceCode2("");       //string Optional
	//	$request->setLocationCode("");        //string Optional - aka outlet id for tax forms
/////////////////////////////////////////


		$lines = array();
		$n = 0;
		$lineNumbersToCartProductId = array();
		foreach($products as $k=>$product){

			$n++;
			$lineNumbersToCartProductId[$n] = $k;
			$line = new Line();
			$line->setNo ($n);                  //string  // line Number of invoice
			$line->setItemCode($product->product_sku);            //string
			$line->setDescription($product->product_name);         //product description, like in cart, atm only the name, todo add customfields

			if(!empty($product->categories)){

				//$catTable = VmModel::getTable ('categories');


				//$prodM = VmModel::getModel('product');
				//$catNames = $prodM->getProductCategories($product->virtuemart_product_id,FALSE);
				//avadebug('AvaTax setTaxCode Product has categories !',$catNames);
				if (!class_exists ('TableCategories')) {
					require(JPATH_VM_ADMINISTRATOR . DS . 'tables' . DS . 'categories.php');
				}
				$db = JFactory::getDbo();
				$catTable = new TableCategories($db);
				foreach($product->categories as $cat){
					$catTable->load ($cat);
					$catslug = $catTable->slug;

					if(strpos($catslug,'avatax-')!==FALSE){
						$taxCode = substr($catslug,7);
						if(!empty($taxCode)){
							$line->setTaxCode($taxCode);
							avadebug('AvaTax setTaxCode '.$taxCode);
						} else {
							vmError('AvaTax setTaxCode, category could not be parsed '.$catslug);
						}

						break;
					}
				}
			}
			//$line->setTaxCode("");             //string
			$line->setQty($product->amount);                 //decimal
			$line->setAmount($product->price * $product->amount);              //decimal // TotalAmmount
			$line->setDiscounted($product->discount * $product->amount);          //boolean

			$line->setRevAcct("");             //string
			$line->setRef1("");                //string
			$line->setRef2("");                //string

			if(isset($shopperData['tax_exemption_number'])){
				$line->setExemptionNo($shopperData['tax_exemption_number']);         //string
			}
			if(isset($shopperData['tax_usage_type'])){
				$line->setCustomerUsageType($shopperData['tax_usage_type']);   //string
			}

			$lines[] = $line;
		}

		$line = new Line();
		$line->setNo (++$n);
		//$lineNumbersToCartProductId[$n] = count($products)+1;
		$line->setItemCode($cart->virtuemart_shipmentmethod_id);
		$line->setDescription('Shipment');
		$line->setQty(1);
		//$line->setTaxCode();
		$cartPrices = $calculationHelper->getCartPrices();
		//avadebug('$calculationHelper $cartPrices',$cartPrices);
		$line->setAmount($cartPrices['shipmentValue']);
		if(isset($shopperData['tax_exemption_number'])){
			$line->setExemptionNo($shopperData['tax_exemption_number']);         //string
		}
		if(isset($shopperData['tax_usage_type'])){
			$line->setCustomerUsageType($shopperData['tax_usage_type']);   //string
		}

		$lines[] = $line;

		//avadebug('avalaragetTax setLines',$lines);
		$request->setLines($lines);

		if($invoiceNumber){
			avadebug('My GetTaxRequest sent to AvaTax',$request);
		}

		$totalTax = 0.0;
		try
		{
			if(!class_exists('TaxLine')) require (VMAVALARA_CLASS_PATH.DS.'TaxLine.class.php');
			if(!class_exists('TaxDetail')) require (VMAVALARA_CLASS_PATH.DS.'TaxDetail.class.php');

			//$cache = JFactory::getCache('com_virtuemart','callback');

			//self::$_taxResult = $cache->call( array( $client, 'getTax' ),$request );
			if(!isset(self::$_taxResult)){
				vmSetStartTime('avagetTax');
				self::$_taxResult = $client->getTax($request);
				vmTime('Avalara getTax','avagetTax');
			}
			

			//vmTrace('get tax agaun');
			/*
			 * [0] => getDocCode
    [1] => getAdjustmentDescription
    [2] => getAdjustmentReason
    [3] => getDocDate
    [4] => getTaxDate
    [5] => getDocType
    [6] => getDocStatus
    [7] => getIsReconciled
    [8] => getLocked
    [9] => getTimestamp
    [10] => getTotalAmount
    [11] => getTotalDiscount
    [12] => getTotalExemption
    [13] => getTotalTaxable
    [14] => getTotalTax
    [15] => getHashCode
    [16] => getVersion
    [17] => getTaxLines
    [18] => getTotalTaxCalculated
    [19] => getTaxSummary
    [20] => getTaxLine
    [21] => getTransactionId
    [22] => getResultCode
    [23] => getMessages
			 */
			//avadebug( 'GetTax is: '. self::$_taxResult->getResultCode(),self::$_taxResult);

			if (self::$_taxResult->getResultCode() == SeverityLevel::$Success)
			{
				//avadebug("DocCode: ".$request->getDocCode() );
				//avadebug("DocId: ".self::$_taxResult->getDocId()."\n");

				//avadebug("TotalAmount: ".self::$_taxResult->getTotalAmount() );

				$totalTax = self::$_taxResult->getTotalTax();

				if($totalTax == 0 ){
				//	avadebug( "Avalara returned false: ", self::$_taxResult);
				}
				foreach(self::$_taxResult->getTaxLines() as $ctl)
				{
					if($calculationHelper->inCart){
						$nr = $ctl->getNo();
						if(isset($lineNumbersToCartProductId[$nr])){
							$quantity = $products[$lineNumbersToCartProductId[$nr]]->amount;

							//on the long hand, the taxAmount must be replaced by taxAmountQuantity to avoid rounding errors
							$prices[$lineNumbersToCartProductId[$ctl->getNo()]]['taxAmount'] = $ctl->getTax()/$quantity;
							$prices[$lineNumbersToCartProductId[$ctl->getNo()]]['taxAmountQuantity'] = $ctl->getTax();

						} else {

							//$prices = array('shipmentValue'=>$cartPrices['shipmentValue'],'shipmentTax'=> $ctl->getTax(), 'shipmentTotal' =>($cartPrices['shipmentValue'] +$ctl->getTax() ));
							//avadebug('my $cartPrices',$cartPrices);
							$prices['shipmentTax'] = $ctl->getTax();
							$prices['salesPriceShipment'] = ($prices['shipmentValue'] + $ctl->getTax() );
								//$cartPrices = array_merge($prices,$cartPrices);

							//$calculationHelper->setCartPrices( $cartPrices );
							$totalTax = $totalTax - $ctl->getTax();
							//avadebug('my $cartPrices danach',$cartPrices);
						}


					}
					//avadebug('my lines ',$ctl);
					//avadebug( "     Line: ".$ctl->getNo()." Tax: ".$ctl->getTax()." TaxCode: ".$ctl->getTaxCode());

					foreach($ctl->getTaxDetails() as $ctd)
					{
						//avadebug( "          Juris Type: ".$ctd->getJurisType()."; Juris Name: ".$ctd->getJurisName()."; Rate: ".$ctd->getRate()."; Amt: ".$ctd->getTax() );
					}

				}

				if($calculationHelper->inCart){
					$calculationHelper->setCartPrices($prices);
				}

			}
			else
			{
				foreach(self::$_taxResult->getMessages() as $msg)
				{
					vmError($msg->getName().": ".$msg->getSummary());
				}
				avadebug('Error, but no exception in getTax avalara',self::$_taxResult);
			}

		}
		catch(SoapFault $exception)
		{
			$msg = "Exception: getTax ";
			if($exception)
				$msg .= $exception->faultstring;

			avadebug( $msg.'<br />'.$client->__getLastRequest().'<br />'.$client->__getLastResponse());

		}
		//self::$stop = $totalTax;

		return $totalTax;
	}




	static $vmadd = NULL;
	private function getShopperData($calc){

		if(!isset(self::$vmadd)){

			$view = JRequest::getWord('view',0);
			if($calc->only_cart == 1 and $view != 'cart'){
				self::$vmadd = FALSE;
				return self::$vmadd;
			}
			//We need for the tax calculation the shipment Address
			//We have this usually in our cart.
			if (!class_exists('VirtueMartCart')) require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
			$cart = VirtueMartCart::getCart();

			//Test first for ST
			if($cart->STsameAsBT){
				if(!empty($cart->BT)) $vmadd = $cart->BT;
			} else if(!empty($cart->ST)){
				$vmadd = $cart->ST;
			} else {
				if(!empty($cart->BT)) $vmadd = $cart->BT;
			}

			$jUser = JFactory::getUser ();
			if($jUser->id){
				$userModel = VmModel::getModel('user');
				$userModel -> setId($jUser->id);
				$vmadd['customer_id'] = $userModel ->getCustomerNumberById();
				avadebug('getShopperData customer_id by user '.$vmadd['customer_id']);
			}

			if(empty($vmadd['customer_id'])){
				$firstName = empty($vmadd['first_name'])? '':$vmadd['first_name'];
				$lastName = empty($vmadd['last_name'])? '':$vmadd['last_name'];
				$email = empty($vmadd['email'])? '':$vmadd['email'];
				$complete = $firstName.$lastName.$email;
				if(!empty($complete)){
					$vmadd['customer_id'] = 'nonreg_'.$vmadd['first_name'].'_'.$vmadd['last_name'].'_'.$vmadd['email'];
				} else {
					$vmadd['customer_id'] = '';
				}
				avadebug('getShopperData customer_id  '.$vmadd['customer_id']);
			}


			//Maybe the user is logged in, but has no cart yet.
		/*	if(empty($vmadd)){
				$jUser = JFactory::getUser ();
				$userModel = VmModel::getModel('user');
				$userModel -> setId($jUser->id);
				$BT_userinfo_id = $userModel->getBTuserinfo_id();
				//Todo check if we actually need this fallback
				//avadebug('getShopperData cart data was empty',$vmadd);
			}*/

			//avadebug('Tax $vmadd',$vmadd);
			if(empty($vmadd) or !is_array($vmadd) or (is_array($vmadd) and count($vmadd) <2) ){

				//VmTable::bindParameterable ($calc, $this->_xParams, $this->_varsToPushParam);
				//avadebug('Insufficient addres, my view '.$view. ' my param ',$calc);

				vmInfo('VMCALCULATION_AVALARA_INSUF_INFO');

				$vmadd=FALSE;
			}

			self::$vmadd = $vmadd;
		}


		return self::$vmadd;
	}

	public function plgVmInterpreteMathOp ($calculationHelper, $rule, $price,$revert){

		$rule = (object)$rule;

		$mathop = $rule->calc_value_mathop;
		$tax = 0.0;

		if ($mathop=='avalara') {
			$requestedProductId = JRequest::getInt('virtuemart_product_id',0);

			if(isset($calculationHelper->_product)){
				$productId = $calculationHelper->_product->virtuemart_product_id;
			} else {
				$productId = $requestedProductId;
			}
			//avadebug('plgVmInterpreteMathOp avalara ',$rule);
			if(($productId!=0 and $productId==$requestedProductId) or $calculationHelper->inCart ){
				VmTable::bindParameterable ($rule, $this->_xParams, $this->_varsToPushParam);
				if($rule->activated==0) return $price;
				if(empty($this->addresses)){
					$this->addresses = $this->fillValidateAvalaraAddress($rule);
				}
				if($this->addresses){
					$tax = $this->getTax( $calculationHelper,$rule,$price);
				}
			}
		}

		if($revert){
			$tax = -$tax;
		}

		return $price + (float)$tax;
	}

	function plgVmConfirmedOrder ($cart, $order) {

		$avaTaxRule = 0;
		if(isset($order['calc_rules'])){
			foreach($order['calc_rules'] as $rule){
				if($rule->calc_mathop == 'avalara' and $rule->calc_kind == 'taxRulesBill'){
					$avaTaxRule=$rule;
					break;
				}
			}
		}

		if($avaTaxRule!==0){
			if(!empty($avaTaxRule->calc_params)){
				VmTable::bindParameterable ($avaTaxRule, $this->_xParams, $this->_varsToPushParam);
				avadebug('$avaTaxRule',$avaTaxRule);
				if($rule->activated==0)return false;
				if(empty($this->addresses)){
					$this->addresses = $this->fillValidateAvalaraAddress($rule);
				}
				if($this->addresses){
					if (!class_exists ('calculationHelper')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');


					$orderModel = VmModel::getModel('orders');
					$invoiceNumber = 'onr_'.$order['details']['BT']->order_number;
					JRequest::setVar('create_invoice',1);
					$orderModel -> createInvoiceNumber($order['details']['BT'],$invoiceNumber);
					$calculator = calculationHelper::getInstance ();

					avadebug('avatax plgVmConfirmedOrder $order',$invoiceNumber,$order);
					if(is_array($invoiceNumber)) $invoiceNumber = $invoiceNumber[0];
					$tax = $this->getTax( $calculator,$rule,0,$invoiceNumber,$order['details']['BT']->order_number);

				//	avadebug('tax',$tax);
				}
			}
		}
	/*	VmTable::bindParameterable ($rule, $this->_xParams, $this->_varsToPushParam);
		if($rule->activated==0) return $price;
		if(empty($this->addresses)){
			$this->addresses = $this->fillValidateAvalaraAddress($rule);
		}
		if($this->addresses){
			$tax = $this->getTax( $calculationHelper,$rule,$price,true);
		}*/

	}

/*	public function plgVmInGatherEffectRulesBill(&$calculationHelper,&$rules){

		return FALSE;
	}*/

	/**
	 * We can only calculate it for the productdetails view
	 * @param $calculationHelper
	 * @param $rules
	 */
	public function plgVmInGatherEffectRulesProduct(&$calculationHelper,&$rules){

		//If in cart, the tax is calculated per bill, so the rule per product must be removed
		if($calculationHelper->inCart){
			foreach($rules as $k=>$rule){
				if($rule['calc_value_mathop']=='avalara'){
					unset($rules[$k]);
				}
			}
		}
	}



	public function plgVmStorePluginInternalDataCalc(&$data){


		//$table = $this->getTable('calcs');
		if (!class_exists ('TableCalcs')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'tables' . DS . 'calcs.php');
		}
		if(!empty($data['avatax_virtuemart_country_id'])){
			$data['avatax_virtuemart_country_id'] = serialize($data['avatax_virtuemart_country_id']);
		}

		$db = JFactory::getDBO ();
		$table = new TableCalcs($db);
		$table->setUniqueName('calc_name');
		$table->setObligatoryKeys('calc_kind');
		$table->setLoggable();
		$table->setParameterable ($this->_xParams, $this->_varsToPushParam);
		$table->bindChecknStore($data);

	}

	public function plgVmGetPluginInternalDataCalc(&$calcData){

		$calcData->setParameterable ($this->_xParams, $this->_varsToPushParam);

		if (!class_exists ('VmTable')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmtable.php');
		}
		VmTable::bindParameterable ($calcData, $this->_xParams, $this->_varsToPushParam);
		if(!is_array($calcData->avatax_virtuemart_country_id)){
			//Suppress Warning
			$calcData->avatax_virtuemart_country_id = @unserialize($calcData->avatax_virtuemart_country_id);

		}
		return TRUE;

	}

	public function plgVmDeleteCalculationRow($id){
		$this->removePluginInternalData($id);
	}

	public function plgVmOnUpdateOrderPayment($data,$old_order_status){

		if($data->order_status=='R' or $data->order_status=='X'){
			avadebug('plgVmOnUpdateOrderPayment cancel order for Avatax '.$old_order_status,$data->order_status);
			$this->cancelOrder($data,$old_order_status);
		}

	}

	/*public function plgVmOnCancelPayment($data,$old_order_status){
		//avadebug('plgVmOnCancelPayment cancel order for Avatax '.$old_order_status,$data->order_status);
		//$this->cancelOrder($data,$old_order_status);
	}*/

	function cancelOrder($data,$old_order_status){

		avadebug('Doing cancel order for Avatax');
		$db = JFactory::getDbo();
//		$q = 'SELECT * FROM `#__virtuemart_invoices` WHERE `virtuemart_order_id`= "'.$data->virtuemart_order_id.'"  AND `order_status` = "'.$old_order_status.'" ORDER BY created_on DESC ';
		$q = 'SELECT * FROM `#__virtuemart_invoices` WHERE `virtuemart_order_id`= "'.$data->virtuemart_order_id.'"  ORDER BY created_on DESC ';
		$db->setQuery($q);
		$result = $db->loadAssocList();

		if(!$result){
			avadebug('AvaTax, plgVmOnCancelPayment no result for '.$data->virtuemart_order_id.' and old orderstatus '.$old_order_status);
			$err = $db->getErrorMsg();
			if($err){
				avadebug('AvaTax, plgVmOnCancelPayment error in query '.$db->getQuery());
			}
			//No invoice number stored, we cannot cancel the order
			return false;
		}

		$q = 'SELECT * FROM `#__virtuemart_order_calc_rules` WHERE `virtuemart_order_id`= "'.$data->virtuemart_order_id.'"  AND `calc_mathop` = "avalara" AND `calc_kind`="taxRulesBill" ';
		$db->setQuery($q);
		$calc = $db->loadObject();
		if(!$calc){
			avadebug('AvaTax, plgVmOnCancelPayment no result for '.$data->virtuemart_order_id.' and old orderstatus '.$old_order_status,$calc);
			$err = $db->getErrorMsg();
			if($err){
				avadebug('AvaTax, plgVmOnCancelPayment error in query '.$db->getQuery());
			}
			//Without the data from the rule we cannot do anything
			return false;
		}

		$params = explode('|', $calc->calc_params);
		foreach($params as $item){

			$item = explode('=',$item);
			$key = $item[0];
			unset($item[0]);

			$item = implode('=',$item);

			if(!empty($item) ){
				$calc->$key = json_decode($item);
			}
		}

		if(!function_exists('EnsureIsArray')) require(VMAVALARA_PATH.DS.'AvaTax.php');	// include in all Avalara Scripts
		if(!class_exists('TaxServiceSoap')) require (VMAVALARA_CLASS_PATH.DS.'TaxServiceSoap.class.php');
		if(!class_exists('CancelTaxRequest')) require (VMAVALARA_CLASS_PATH.DS.'CancelTaxRequest.class.php');

		$this->newATConfig($calc);

		$client = new TaxServiceSoap($this->_connectionType);
		$request= new CancelTaxRequest();
		// Locate Document by Invoice Number (Document Code)
		/*		echo "Enter Invoice Number (Document Code): ";
				$STDIN = fopen('php://stdin', 'r');
				$input = rtrim(fgets($STDIN));*/

		$request->setDocCode($result[0]['invoice_number']);
		$request->setDocType('SalesInvoice');

		$request->setCompanyCode($calc->company_code);	// Dashboard Company Code

		if($calc->committ==0) return false;

		//CancelCode: Enter D for DocDeleted, or P for PostFailed: [D]
		//I do not know the difference, I use always D (I assume this means order got deleted, cancelled, or refund)
		$code = CancelCode::$DocDeleted;
		//if($input == 'P')
		//	$code = CancelCode::$PostFailed;

		$request->setCancelCode($code);

		try
		{
			avadebug('plgVmOnCancelPayment used request',$request);
			$result = $client->cancelTax($request);

			if ($result->getResultCode() != "Success")
			{
				$msg = '';
				foreach($result->getMessages() as $rmsg)
				{
					$msg .= $rmsg->getName().": ".$rmsg->getSummary()."\n";
				}
				vmError($msg);
			} else {
				vmInfo('CancelTax ResultCode is: '.$result->getResultCode());
			}
		}
		catch(SoapFault $exception)
		{
			$msg = "Exception: ";
			if($exception)
				$msg .= $exception->faultstring;

			$msg .="\n";
			$msg .= $client->__getLastRequest()."\n";
			$msg .= $client->__getLastResponse()."\n";
			vmError($msg);
		}

	}

}

// No closing tag
