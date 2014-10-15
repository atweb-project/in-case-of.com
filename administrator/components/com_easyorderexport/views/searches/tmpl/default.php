<?php
/**
 * @version     1.0.0
 * @package     com_easyorderexport
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author       <> - 
 */


// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easyorderexport/assets/css/easyorderexport.css');
// Import JS
$document->addScript("components/com_easyorderexport/assets/js/jquery-1.9.1.js");
?>
<fieldset>
<legend>
<?php echo JText::_("DOWNLOAD1_TITLE")?>
</legend>
<script>
	
	$(document).ready(function() {
		
		$("#adminForm").submit(function(){
		 $ret1	=	true;
			if($("#date_from").val() == ""){
				$("#date_from").css("border","solid 1px #f00");
				$ret1	=	false;	
			}else{
				$("#date_from").css("border","solid 1px silver");
			}
			if($("#date_to").val() == ""){	
				$("#date_to").css("border","solid 1px #f00");
				$ret1	=	false;	
			}else{
				$("#date_to").css("border","solid 1px silver");
			}
			if($ret1)
				return true;
			else
				return false;
		});
	});
</script>

<form name="adminForm" id="adminForm" method="post">
<table class="adminlist table table-striped table-hover" cellpadding="1">
	<tbody>
		<tr>
			<td width="50%" class="title">
				<?php echo JText::_("DATE_FROM")?>
			</td>
			<td class="title">
				<?php echo $this->form->getInput('date_from'); ?>				
			</td>
		</tr>
		<tr>
			<td class="title">
				<?php echo JText::_("DATE_TO")?>
			</td>
			<td class="title">
				<?php echo $this->form->getInput('date_to'); ?>
			</td>
		</tr>
		<tr>
			<td class="title">
				<?php echo JText::_("STATE_ORDER")?>
			</td>
			<td class="title">
				<?php echo $this->form->getInput('order_status'); ?>
			</td>
		</tr>
		<tr>
			<td class="title" colspan="2">
				<input type="submit" value="<?php echo JText::_("DOWNLOAD")?>"/>
			</td>
		</tr>
	</tbody>
</table>
<input type="hidden" name="option" value="com_easyorderexport"/>
<input type="hidden" name="controller" value="result"/>
<input type="hidden" name="task" value="search"/>
<input type="hidden" name="type" value="searchbydate"/>
</form>

</fieldset>

<fieldset>
<legend>
<?php echo JText::_("DOWNLOAD2_TITLE")?>
</legend>

<script>
	
	$(document).ready(function() {
		
		$("#adminForm1").submit(function(){
		$ret2	=	true;
			if($("#id").val() == ""){
				$("#id").css("border","solid 1px #f00");
				$ret2	=	false;	
			}
			
			if($ret2)
				return true;
			else
				return false;
		});
	});
</script>

<form name="adminForm1" id="adminForm1" method="post">
<table class="adminlist table table-striped table-hover" cellpadding="1">
	<tbody>
		<tr>
			<td width="50%" class="title">
				<?php echo JText::_("SELECT_ID")?>
			</td>
			<td class="title">
				<?php echo $this->form->getInput('id'); ?>				
			</td>
		</tr>
		<tr>
			<td class="title" colspan="2">
				<input type="submit" value="<?php echo JText::_("DOWNLOAD")?>"/>
			</td>
		</tr>
	</tbody>
</table>
<input type="hidden" name="option" value="com_easyorderexport"/>
<input type="hidden" name="controller" value="result"/>
<input type="hidden" name="task" value="search"/>
<input type="hidden" name="type" value="searchbyids"/>
</form>

</fieldset>


<fieldset>
<legend>
<?php echo JText::_("DOWNLOAD3_TITLE")?>
</legend>
<script>
	
	$(document).ready(function() {
		
		$("#adminForm2").submit(function(){
		$ret3	=	true;
			if($("#email").val() == ""){
				$("#email").css("border","solid 1px #f00");
				$ret3	=	false;	
			}
			
			if($ret3)
				return true;
			else
				return false;
		});
	});
</script>

<form name="adminForm2" id="adminForm2" method="post">
<table class="adminlist table table-striped table-hover" cellpadding="1">
	<tbody>
		<tr>
			<td width="50%" class="title">
				<?php echo JText::_("SELECT_EMAIL")?>
			</td>
			<td class="title">
				<?php echo $this->form->getInput('email'); ?>				
			</td>
		</tr>
		<tr>
			<td class="title" colspan="2">
				<input type="submit" value="<?php echo JText::_("DOWNLOAD")?>"/>
			</td>
		</tr>
	</tbody>
</table>
<input type="hidden" name="option" value="com_easyorderexport"/>
<input type="hidden" name="controller" value="result"/>
<input type="hidden" name="task" value="search"/>
<input type="hidden" name="type" value="searchbycustemail"/>
</form>


</fieldset>

<fieldset>
<legend>
<?php echo JText::_("DOWNLOAD4_TITLE")?>
</legend>
<script>
	
	$(document).ready(function() {
		
		$("#adminForm3").submit(function(){
		$ret4	=	true;
			if($("#item_sku").val() == ""){
				$("#item_sku").css("border","solid 1px #f00");
				$ret4	=	false;	
			}
			
			if($ret4)
				return true;
			else
				return false;
		});
	});
</script>

<form name="adminForm3" id="adminForm3" method="post">
<table class="adminlist table table-striped table-hover" cellpadding="1">
	<tbody>
		<tr>
			<td width="50%" class="title">
				<?php echo JText::_("SELECT_SKU")?>
			</td>
			<td class="title">
				<?php echo $this->form->getInput('item_sku'); ?>				
			</td>
		</tr>
		<tr>
			<td class="title" colspan="2">
				<input type="submit" value="<?php echo JText::_("DOWNLOAD")?>"/>
			</td>
		</tr>
	</tbody>
</table>
<input type="hidden" name="option" value="com_easyorderexport"/>
<input type="hidden" name="controller" value="result"/>
<input type="hidden" name="task" value="search"/>
<input type="hidden" name="type" value="searchbysku"/>
</form>

</fieldset>

