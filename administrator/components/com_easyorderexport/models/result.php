<?php
/**
 * @version     1.0.0
 * @package     com_easyorderexport
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author       <> - 
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
/**
 * Methods supporting a list of Easyorderexport records.
 */
class EasyorderexportModelresult extends JModelList
{

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                
            );
        }

        parent::__construct($config);
    }
	// SET NO LIMIT
	protected function populateState()
	{
        $this->setState('list.limit', 0);
	}
	
	//GENERATE QUERY
	protected function getListQuery()
	{
		$query	=	"";
		$query	=	"SELECT 
						vo.virtuemart_order_id, 
						vo.virtuemart_vendor_id, 
						vo.order_number, 
						vo.order_total, 
						vo.order_discountAmount,
						vo.order_tax,
						vo.order_subtotal,
						vo.order_shipment,
						vo.order_shipment_tax,
						vo.order_payment ,
						vo.order_payment_tax ,
						vo.coupon_discount ,
						vo.coupon_code ,
						vo.order_currency ,
						vo.order_status ,
						vo.virtuemart_paymentmethod_id ,
						vo.virtuemart_shipmentmethod_id ,
						vo.customer_note ,
						vo.ip_address ,
						vo.created_on ,
						vo.modified_on ,
						
						voi.order_item_name, 
						voi.order_item_sku, 
						voi.virtuemart_product_id, 
						voi.product_quantity, 
						voi.product_item_price, 
						voi.product_tax, 
						voi.product_basePriceWithTax, 
						voi.product_subtotal_discount, 
						voi.product_final_price, 
						
						voui.address_type ,
						voui.address_type_name ,
						voui.title ,
						voui.last_name ,
						voui.first_name ,
						voui.middle_name ,
						voui.phone_1 ,
						voui.phone_2 ,
						voui.fax ,
						voui.address_1 ,
						voui.address_2 ,
						voui.city ,
						voui.virtuemart_state_id ,
						voui.virtuemart_country_id ,
						voui.zip ,
						voui.email,
						
						vs.state_name,
						vc.country_name
						
						
						FROM #__virtuemart_orders AS vo 
						JOIN #__virtuemart_order_items AS voi ON voi.virtuemart_order_id = vo.virtuemart_order_id
						JOIN #__virtuemart_order_userinfos AS voui ON voui.virtuemart_order_id = vo.virtuemart_order_id
						JOIN #__virtuemart_states AS vs ON vs.virtuemart_state_id = voui.virtuemart_state_id
						JOIN #__virtuemart_countries AS vc ON vc.virtuemart_country_id = voui.virtuemart_country_id
						WHERE 1=1 AND ";
		$type	=	JRequest::getVar("type");
		
		switch ($type) {
		    case "searchbydate":
		        $datefrom		=	JRequest::getVar("date_from");
				$datefrom		=	$datefrom." 00:00:00";
				$dateto			=	JRequest::getVar("date_to");
				$dateto			=	$dateto." 12:00:00";
				$orderstatus	=	JRequest::getVar("order_status");
				
				if($orderstatus!='A')
					$query	.=	"vo.order_status='".$orderstatus."' AND ";
				if($datefrom!='' && $dateto!='')
					$query	.=	"vo.created_on BETWEEN '".$datefrom."' AND '".$dateto."'";
					
		        break;
		    case "searchbyids":
		        $ids		=	JRequest::getVar("id");
				$query	.=	"vo.virtuemart_order_id IN (".$ids.")";
		        break;
		    case "searchbycustemail":
		        $email		=	JRequest::getVar("email");
				$query	.=	"voui.email='".$email."'";
		        break;
			case "searchbysku":
				$item_sku		=	JRequest::getVar("item_sku");
				$query	.=	"voi.order_item_sku='".$item_sku."'";
				break;
		}
		
		$query	.=	" ORDER BY vo.virtuemart_order_id ASC";
		//echo $query;exit;
		return $query;
	}
	
}