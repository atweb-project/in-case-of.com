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
Komento.ready(function($){

	Komento.ajax( 'admin.views.komento.getVersion' , {} , function( html ){

		$( '#submenu-box #submenu' ).append( '<li style="float:right;margin:5px 10px 0 0;">' + html + '</li>' );

	});
});
</script>

<form name="adminForm" method="post" action="index.php">
<div class="pa-15">
	<table id="kmt-panel" width="100%">
		<tr>
			<td valign="top" width="65%" style="padding: 10px;">
				<ul id="kmt-items" class="reset-ul">
					<?php
						echo $this->addButton( 'index.php?option=com_komento&view=settings', 'settings.png', JText::_( 'COM_KOMENTO_SETTINGS' ), JText::_( 'COM_KOMENTO_SETTINGS_DESC' ), false, 'settings' );
						echo $this->addButton( 'index.php?option=com_komento&view=integrations', 'integrations.png', JText::_( 'COM_KOMENTO_INTEGRATIONS' ), JText::_( 'COM_KOMENTO_INTEGRATIONS_DESC' ), false, 'integrations' );
						echo $this->addButton( 'index.php?option=com_komento&view=comments', 'comments.png', JText::_( 'COM_KOMENTO_COMMENTS' ), JText::_( 'COM_KOMENTO_COMMENTS_DESC' ), false, 'comments' );
						echo $this->addButton( 'index.php?option=com_komento&view=pending', 'pending.png', JText::_( 'COM_KOMENTO_PENDING' ), JText::_( 'COM_KOMENTO_PENDING_DESC' ), false, 'pendings' );
						echo $this->addButton( 'index.php?option=com_komento&view=reports', 'reports.png', JText::_( 'COM_KOMENTO_REPORTS' ), JText::_( 'COM_KOMENTO_REPORTS_DESC' ), false, 'reports' );
						echo $this->addButton( 'index.php?option=com_komento&view=subscribers', 'subscribers.png', JText::_( 'COM_KOMENTO_SUBSCRIBERS' ), JText::_( 'COM_KOMENTO_SUBSCRIBERS_DESC' ), false, 'subscribers' );
						echo $this->addButton( 'index.php?option=com_komento&view=acl', 'acl.png', JText::_( 'COM_KOMENTO_ACL' ), JText::_( 'COM_KOMENTO_ACL_DESC' ), false, 'acl' );
						echo $this->addButton( 'index.php?option=com_komento&view=migrators', 'migrators.png', JText::_( 'COM_KOMENTO_MIGRATORS' ), JText::_( 'COM_KOMENTO_MIGRATORS_DESC' ), false, 'migrators' );
						echo $this->addButton( 'index.php?option=com_komento&view=mailq', 'mailq.png', JText::_( 'COM_KOMENTO_MAIL_QUEUE' ), JText::_( 'COM_KOMENTO_MAIL_QUEUE_DESC' ), false, 'mailq' );
						echo $this->addButton( 'http://stackideas.com/docs/komento.html', 'docs.png', JText::_( 'COM_KOMENTO_DOCUMENTATION' ), JText::_( 'COM_KOMENTO_DOCUMENTATION_DESC' ) , true );
					?>
				</ul>
				<div class="clr"></div>
			</td>
			<td valign="top" style="padding:10px 10px 10px 0;">
				<?php echo $this->loadTemplate( 'right' ); ?>
			</td>
		</tr>
	</table>
	<div style="text-align: right;margin: 10px 5px 0 0;">
		<?php echo JText::_('Komento is a product of <a href="http://stackideas.com" target="_blank">StackIdeas</a>');?>
	</div>
</div>
</form>
