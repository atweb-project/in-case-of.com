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
$app    =  JFactory::getApplication('site');
$template  =  $app->getTemplate(); 

$document = JFactory::getDocument();
$document->addScriptDeclaration("
jQuery(function($) {
$(document).ready(function(){

  $('#vmCartModule').hover(
     function(){ 
         $('#product_list').slideUp(0).stop(true, true).fadeIn(300);
      $('#vmCartModule').stop(true, true).addClass('carthover');
      },
     function(){ 
         $('#product_list').stop(true, true).delay(200).fadeOut(300); 
      $('#vmCartModule').stop(true, true).delay(300).queue(function(next){
          $(this).removeClass('carthover');
               next();
        });
     }
  )
  
  
});
});

");
?>
<script type="text/javascript">
window.addEvent('domready',function() { 
  <?php
  if((JRequest::getCmd('view')=='productdetails') || (JRequest::getCmd('view')=='category')) {
      ?>
      $$('.addtocart-button').addEvent('click',function() {
        document.id('product_list').addClass('show_products');
        (function(){document.id('product_list').removeClass('show_products')}).delay(5000);
        window.location.hash='cart';
      });
      <?php
  }
  ?>
});

function remove_product_cart(elm) {
  var cart_id=elm.getChildren('span').get('text');
  if(document.id('is_opc')) {
      remove_product(elm.getChildren('span').get('text'));
  } else {
  new Request.HTML({
    'url':'index.php?option=com_virtuemart&view=cart&task=delete',
    'method':'post',
    'data':'cart_virtuemart_product_id='+cart_id,
    'evalScripts':false,
    'onSuccess':function(tree,elms,html,js) {
      //jQuery('.vmCartModule').productUpdate();
      mod=jQuery('.vmCartModule');
      //addon by atweb
      vmSiteurl = 'http://in-case-of.com/';
      vmLang = "";
      //end of addon
      jQuery.getJSON(vmSiteurl+'index.php?option=com_virtuemart&nosef=1&view=cart&task=viewJS&format=json'+vmLang,
        function(datas, textStatus) {
          if (datas.totalProduct >0) {
            mod.find('.vm_cart_products').html('');
            jQuery.each(datas.products, function(key, val) {
              jQuery('#hiddencontainer .container').clone().appendTo('.vmCartModule .vm_cart_products');
              jQuery.each(val, function(key, val) {
                if (jQuery('#hiddencontainer .container .'+key)) mod.find('.vm_cart_products .'+key+':last').html(val) ;
              });
            });
            mod.find('.total').html(datas.billTotal);
            mod.find('.show_cart').html(datas.cart_show);
          } else {
            mod.find('.vm_cart_products').html('');
            mod.find('.show_cart').html('');
            mod.find('.total').html('Cart empty');
            //mod.find('.total').html(datas.billTotal);
          }
          mod.find('.total_products').html(datas.totalProductTxt);
        }
      );
    }
  }).send();
  }
}
</script>

<div class="vmCartModule <?php echo $moduleclass_sfx; ?>" id="vmCartModule">
  <div class="total" id="total"> <?php echo count($data->products)?(JText::_('COM_VIRTUEMART_CART_OVERVIEW').' ('. $data->totalProduct.')'):JText::_('COM_VIRTUEMART_CART_OVERVIEW').' (0)'; ?> </div>
  <div id="hiddencontainer" style="display:none">
    <div class="cartTitle">Cart Buraya</div>
    
      <div class="cartEmpty"></div>
      
      <div class="clear"></div>
      <div class="container">
      <div class="product_row">
        <div class="product_row_inner">
          <div class="image"></div>
          <div class="product_name_container"> <span class="quantity"></span>&nbsp;x&nbsp;<span class="product_name"></span> </div>
          <div class="prices"></div>
          <div class="product_attributes"></div>
          <div class="remove_button"><a class="vmicon vmicon vm2-remove_from_cart" onclick="remove_product_cart(this);"><span class="product_cart_id" style="display:none;"></span></a></div>
        </div>
      </div>
    </div>
     
  </div>
  <div id="product_list" style="display:none;">
  <div class="cartTitle"><?php echo JText::_('COM_VIRTUEMART_CART_OVERVIEW'); ?></div>
   
  <div class="show_cart">
          <?php
      if($data->totalProduct) {
        echo JHTML::_('link',JRoute::_('index.php?option=com_virtuemart&view=cart'.($data->dataValidated==true?'&task=confirm':''),true,vmConfig::get('useSSL',0)),$lang->_($data->dataValidated==true?'COM_VIRTUEMART_CART_CONFIRM':'COM_VIRTUEMART_CHECKOUT_TITLE')); ?>
      
          <div class="sub_total"> <?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SUBTOTAL'); ?>: <?php echo $data->billTotal; ?> </div>
         <?php }
      ?>
        </div>
        
        <div class="clear"></div>
  
    <div class="vm_cart_products" id="vm_cart_products">
      
        
        <?php if (!($data->products)) { ?>
        <div class="cartEmpty"><?php echo JText::_('COM_VIRTUEMART_CART_NO_PRODUCT'); ?></div>
        <?php } ?>
        
        <div class="clear"> </div>
        <div class="container">
        <?php
        foreach($data->products as $product) {
          ?>
        <div class="product_row">
          <div class="product_row_inner">
            <div class="image"><?php echo $product["image"]; ?></div>
            <div class="product_name_container"> <span class="quantity"><?php echo $product["quantity"]; ?></span>&nbsp;x&nbsp;<span class="product_name"><?php echo $product["product_name"]; ?></span> </div>
            <div class="prices"><?php echo $product["prices"]; ?></div>
            <?php
            if(!empty($product["product_attributes"])) {
            ?>
            <div class="product_attributes"><?php echo $product["product_attributes"]; ?></div>
            <?php
            }
            ?>
            <div class="remove_button"> <a class="vmicon vmicon vm2-remove_from_cart" onclick="remove_product_cart(this);"><span class="product_cart_id" style="display:none;"><?php echo $product["product_cart_id"]; ?></span></a> </div>
          </div>
        </div>
        <?php
        }
        ?>
      </div>
      
    </div>
    <div class="clear"></div>
    
  </div>
  <div style="display:none">
    <div class="total_products"></div>
  </div>
  <input type="hidden" id="extra_cart" value="1" />
</div>
