<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
Komento.require()
.library( 'dialog' )
.done( function($){

	$( '.preview-mail' ).each(function(){
		var id	= $(this).data( 'id' );

		$( this ).bind( 'click' , function(){

			var content	= '';

			Komento.ajax( 'admin.views.mailq.preview' ,
			{
				'id'	: id
			},
			{
				success: function( html ){
					$.dialog( {
						title: '<?php echo JText::_( 'COM_KOMENTO_MAIL_PREVIEW' , true );?>',
						content: html,
						width: 600,
						height: 350
					});
				}
			});
		});
	});
});
</script>
<form action="index.php?option=com_komento&view=mailq" method="post" name="adminForm" id="adminForm">
<div class="adminform-body">
	<table class="adminlist" cellspacing="1">
		<thead>
			<th width="2%" class="title" style="text-align:center;">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
			</th>
			<th class="title" style="text-align:left;" width="15%">
				<?php echo JText::_( 'COM_KOMENTO_RECIPIENT' ); ?>
			</th>
			<th class="title" style="text-align:left;">
				<?php echo JText::_( 'COM_KOMENTO_SUBJECT' ); ?>
			</th>
			<th class="title" width="1%" style="text-align:center;">
				<?php echo JText::_( 'COM_KOMENTO_STATE' ); ?>
			</th>
			<th class="title" width="10%" style="text-align:center;">
				<?php echo JText::_( 'COM_KOMENTO_CREATED' ); ?>
			</th>
			<th class="title" width="1%" style="text-align:center;">
				<?php echo JText::_( 'COM_KOMENTO_ID' ); ?>
			</th>
		</thead>
		<tbody>
		<?php if( $this->items ){ ?>
			<?php for ($i=0, $n=count($this->items); $i<$n; $i++){ ?>
			<tr>
				<td style="text-align:center;">
					<?php echo JHTML::_( 'grid.id' , $i , $this->items[ $i ]->id ); ?>
				</td>
				<td>
					<a href="mailto:<?php echo $this->escape( $this->items[ $i ]->recipient ); ?>" target="_blank"><?php echo $this->items[ $i ]->recipient; ?></a>
				</td>
				<td>
					<a href="javascript:void(0);" class="preview-mail" data-id="<?php echo $this->items[ $i ]->id;?>"><?php echo $this->items[ $i ]->subject; ?></a>
				</td>
				<td style="text-align:center;">
					<?php echo $this->items[ $i ]->status; ?>
				</td>
				<td style="text-align:center;">
					<?php echo JFactory::getDate( $this->items[ $i ]->created )->toMySQL(true); ?>
				</td>
				<td style="text-align:center;">
					<?php echo $this->items[ $i ]->id; ?>
				</td>
			</tr>
			<?php } ?>
		<?php } else { ?>
			<tr>
				<td colspan="7" style="text-align:center;">
					<?php echo JText::_( 'COM_KOMENTO_NO_PENDING_EMAILS_YET' );?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
</div>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_komento" />
<input type="hidden" name="view" value="mailq" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="mailq" />
</form>
