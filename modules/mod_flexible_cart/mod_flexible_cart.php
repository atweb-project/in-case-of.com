<?php

/*------------------------------------------------------------------------
 # Flexible Dropdown Shopping Cart   - Version 2.0
 # ------------------------------------------------------------------------
 # Copyright (C) 2013 Flexible Web Design. All Rights Reserved.
 # @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 # Author: Flexible Web Design Team
 # Websites: http://www.flexiblewebdesign.com
 -------------------------------------------------------------------------*/
 

defined('_JEXEC') or die('Restricted access');
include'tmpl/function.php';
if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');
//$cart = VirtueMartCart::getCart(false);
//$data = $cart->prepareAjaxData();
require_once JPATH_SITE.DS.'plugins'.DS.'system'.DS.'vm2_cart'.DS.'vm2_cart.php';
$plg=new plgSystemVM2_Cart(JDispatcher::getInstance(),array());
$data=$plg->prepareAjaxData();

$lang = JFactory::getLanguage();
$extension = 'com_virtuemart';
$lang->load($extension);

$web_url = JURI::base();
$url_to_module = $web_url . "modules/".$module->module."/media/";

// Getting Parameters
$theme = $params->get( 'theme', 'theme1');
$orientation = $params->get( 'orientation', 'left');
$dropdownwidth = $params->get( 'dropdownwidth', '400');
$dropdowcount = $params->get( 'dropdowcount', '3');
$dropback = $params->get( 'dropback', '#fff');
$dropcolor = $params->get( 'dropcolor', '#000');
$dropborder = $params->get( 'dropborder', '#DDDDDD');

$dropbutton = $params->get( 'dropbutton', '#333');
$dropbuttontext = $params->get( 'dropbuttontext', '#fff');
$dropbuttonhover = $params->get( 'dropbuttonhover', '#666');
$droplink = $params->get( 'droplink', '#333');
$droplinkhover = $params->get( 'droplinkhover', '#000');

$modulecolor = $params->get( 'modulecolor');
$modulecolorhover = $params->get( 'modulecolorhover');
$moduletext = $params->get( 'moduletext');
$moduleclass_sfx = $params->get( 'moduleclass_sfx');

 
if ($dropborder != " ") {
	$dropborder2 = '1px solid '.$dropborder;
}

// Add Style Decleration based on the parameter in the module
$document = JFactory::getDocument();
$style = '
		#product_list {
        ' . $orientation . ':0px;
		width:'. $dropdownwidth .'px;
		background-color:'. $dropback .';
		color:'. $dropcolor .';
        }
		#vmCartModule .cartTitle, #vmCartModule .show_cart {
			border-bottom: '. $dropborder2 .';
		}
		#vmCartModule .show_cart a {
			background:'. $dropbutton .';
			color:'. $dropbuttontext .';	
		}
		#vmCartModule .show_cart a:hover {
			background:'. $dropbuttonhover .';
		}
		div#vmCartModule span a {
			color:'. $droplink .';
		}
		div#vmCartModule span a:hover {
			color:'. $droplinkhover .';
		}
		#vmCartModule {
			background-color: '. $modulecolor .';
		}
		#vmCartModule.carthover {
			background-color: '. $modulecolorhover .';
		}
		#vmCartModule .total {
			color: '. $moduletext .';
		}
		#vmCartModule .product_row:nth-child('.$dropdowcount.'):after {
			content:".";
			visibility:hidden;
			clear:both;
		}
		#vmCartModule .product_row {
			width:'. floor(100/$dropdowcount).'%;
		}
			

		 
';
$document->addStyleDeclaration($style);


// loading necessary CSS file to stylize the dropdown shoppin Cart
$CSSfilename = $theme.'.css';
JHTML::stylesheet($CSSfilename, $url_to_module);

require_once JModuleHelper::getLayoutPath('mod_flexible_cart');
?>