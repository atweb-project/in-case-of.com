<?php
/**
 * @autor       Valent�n Garc�a
 * @website     www.valentingarcia.com.mx
 * @package		Joomla.Site
 * @subpackage	mod_circle_contact
 * @copyright	Copyright (C) 2012 Valent�n Garc�a. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
	  
//library
jimport('joomla.application.module.helper');

//vars
//$class_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));
$c_emailto = explode( '@', $params->get('emailto') );
$c_justdata = $params->get('justdata');
$c_justsocial = $params->get('justsocial');

echo '<div class="contactform"> <span class="error"></span> <span class="error"></span> <span class="error"></span> <span class="error"></span>
        <form id="contactForm" method="post" action="">
          <input type="hidden" name="emailto1" value="' . $c_emailto[0] . '" />
		  <input type="hidden" name="emailto2" value="' . $c_emailto[1] . '" />
		  <input type="text"  name="contactName" id="contactName" class="requiredField" value="" placeholder="' . JText::_('VG_SK_CONTACT_NAME') . '" />
          <input type="text" name="email" id="email" value="" class="requiredField email" placeholder="' . JText::_('VG_SK_CONTACT_EMAIL') . '" />
          <textarea class="requiredField" name="comments" id="comments" placeholder="' . JText::_('VG_SK_CONTACT_MESSAGE') . '"></textarea>
          <input type="hidden" name="submitted" id="submitted" value="true" />
          <div class="clearfix"></div>
          <button type="submit" name="submit" id="submitMsg" class="large_btn contact-btn">' . JText::_('VG_SK_CONTACT_SUBMIT') . '</button>
        </form>
		<div id="note"></div>
      </div>
      <div class="contactinfo">
        ' . $c_justdata . '
		' . $c_justsocial . '
      </div>';
?>

<script>
// mail-form
jQuery(document).ready(function($){
	$("#contactForm").submit(function(){
	var str = $(this).serialize();

	$.ajax({
		type: "POST",
		url: "<?php echo JURI::base(); ?>modules/mod_circle_contact/ajax/send.php",
		data: str,
		success: function(msg){
    
			$("#note").ajaxComplete(function(event, request, settings){
			if(msg == 'OK')
			{
				result = '<p style="color:green;"><?php echo JText::_('VG_SK_CONTACT_SUCCESS'); ?></p>';
			}
			else
			{
				result = '<p style="color:red;"><?php echo JText::_('VG_SK_CONTACT_FORGOT'); ?></p>';
			}

			$(this).html(result).fadeIn();
			$(this).html(result);

			});
			//alert(msg);		

		}

	});
return false;
});
});
</script>