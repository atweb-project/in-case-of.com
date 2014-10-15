<?php
/**
 * @version     1.0.0
 * @package     com_easyorderexport
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author       <> - 
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Easyorderexport.
 */
class EasyorderexportViewResult extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		
		$this->items		= $this->get('Items');
		
		
		
		
		$fields  	= array();
		$fields		=	$this->getFields();
		
		
		
		$i=1;
		foreach($this->items as $items){
		
			$fields[$i][0] = $items->virtuemart_order_id;
			$fields[$i][1] = $items->virtuemart_product_id;
			$fields[$i][2] = $items->order_item_sku;
			$fields[$i][3] = $items->order_item_name;
			$fields[$i][4] = $items->product_quantity;
			$fields[$i][5] = $items->product_final_price;
			$fields[$i][6] = $items->order_number;
			$fields[$i][7] = $items->order_total;
			$fields[$i][8] = $items->order_tax;
			$fields[$i][9] = $items->order_subtotal;
			$fields[$i][10] = $items->order_shipment;
			$fields[$i][11] = $items->order_discountAmount;
			$order_status   = $this->getOrderStatus($items->order_status);
			$fields[$i][12] = $order_status;
			$fields[$i][13] = $items->created_on;
			$fields[$i][14] = $items->title;
			$fields[$i][15] = $items->first_name;
			$fields[$i][16] = $items->last_name;
			$fields[$i][17] = $items->middle_name;
			$fields[$i][18] = $items->email;
			$fields[$i][19] = $items->phone_1;
			$fields[$i][20] = $items->phone_2;
			$fields[$i][21] = $items->fax;
			$fields[$i][22] = $items->address_1;
			$fields[$i][23] = $items->address_2;
			$fields[$i][24] = $items->city;
			$fields[$i][25] = $items->state_name;
			$fields[$i][26] = $items->country_name;
			$fields[$i][27] = $items->zip;
			$i++;
		}
		
		
		$csv_folder     = JPATH_COMPONENT_SITE."/csv_files";
		
		$filename 	= "virtuemart_orders";
		$CSVFileName    = $csv_folder.'/'.$filename.'.csv';
		$FileHandle     = fopen($CSVFileName, 'w') or die("can't open file");
		fclose($FileHandle);
		$fp = fopen($CSVFileName, 'w');
		
		foreach ($fields as $field) {
			fputcsv($fp, $field);
		}
		fclose($fp);
		
		if (file_exists($CSVFileName)) {

	        //set appropriate headers
	        header('Content-Description: File Transfer');
	        header('Content-Type: application/csv');
	        header('Content-Disposition: attachment; filename='.basename($CSVFileName));
	        header('Expires: 0');
	        header('Cache-Control: must-revalidate');
	        header('Pragma: public');
	        header('Content-Length: ' . filesize($CSVFileName));
	        ob_clean();
	        flush();

	        //read the file from disk and output the content.
	        readfile($CSVFileName);
	        exit;
	    }
		
		JToolBarHelper::title( 'Export orders - Result', 'generic.png' );
		parent::display($tpl); 
	}
	
	//GET CSV FIELDS
	function getFields(){
		$csv_fields[0]   = array();
		//$csv_fields[0][] = 'Company';
		$csv_fields[0][] = 'Order ID';
		$csv_fields[0][] = 'Product ID';
		$csv_fields[0][] = 'SKU';
		$csv_fields[0][] = 'Product Name';
		$csv_fields[0][] = 'Product Quantity';
		$csv_fields[0][] = 'Product Final Price';
		$csv_fields[0][] = 'Order Number';
		$csv_fields[0][] = 'Order Total';
		$csv_fields[0][] = 'Order Tax';
		$csv_fields[0][] = 'Order Subtotal';
		$csv_fields[0][] = 'Order Shipment';
		$csv_fields[0][] = 'Order Discount';
		$csv_fields[0][] = 'Order Status';
		$csv_fields[0][] = 'Order Create Date';
		$csv_fields[0][] = 'Title';
		$csv_fields[0][] = 'First Name';
		$csv_fields[0][] = 'Last Name';
		$csv_fields[0][] = 'Middle Name';
		$csv_fields[0][] = 'Email Address';
		$csv_fields[0][] = 'Phone 1';
		$csv_fields[0][] = 'Phone 2';
		$csv_fields[0][] = 'Fax';
		$csv_fields[0][] = 'Address 1';
		$csv_fields[0][] = 'Address 2';
		$csv_fields[0][] = 'City';
		$csv_fields[0][] = 'State';
		$csv_fields[0][] = 'Country';
		$csv_fields[0][] = 'Zip';
		return $csv_fields;
	}
	
	//GET ORDER STATUS LIKE PENDING, CONFIRMED ETC
	function getOrderStatus($itemsku){
		$db	=	JFactory::getDBO();
		$db->setQuery("SELECT order_status_name FROM #__virtuemart_orderstates WHERE order_status_code = '".$itemsku."'");
		$result	=	$db->loadObject();
		return $result->order_status_name;
	}
}
