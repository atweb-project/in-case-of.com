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

<?php if(Komento::joomlaVersion() >= 1.6) : ?>
	Joomla.submitbutton = function( action )
	{
		submitbutton(action);
	}
<?php endif; ?>

Komento.require().script('admin.acl.recommended', 'admin.acl.profiles').done(function($) {

	$('.adminform-body').implement('Komento.Controller.AclProfiles');

	window.submitbutton = function(action)
	{
		if(action == 'enableall')
		{
			enableAll();
		}
		else if(action == 'disableall')
		{
			disableAll();
		}
		else
		{
			submitform(action);
		}
	}

	window.enableAll = function()
	{
		$('.adminform-body').controller().renderFull();
	}

	window.disableAll = function()
	{
		$('.adminform-body').controller().renderNone();
	}

	/*$('.adminform-body').implement('Komento.Controller.AclRecommended', {
		type: "<?php echo JRequest::getString( 'type' ); ?>",
		usergroup: <?php echo JRequest::getInt( 'id', 0 ); ?>
	});*/
});

function changeComponent(component) {
	document.adminForm.component.value = component;
	document.adminForm.submit();
}

</script>

<form action="index.php?option=com_komento&view=acl" method="post" name="adminForm" id="adminForm">

	<div class="adminform-head">
		<table class="adminform">
			<tr>
				<td width="50%">
					<?php if( $this->type == 'usergroup' ) { ?>
					<label for="usergroup"><?php echo JText::_( 'COM_KOMENTO_ACL_SELECT_USERGROUP' ); ?> :</label>
					<?php echo $this->usergroups;
					} ?>

					<!-- replace with <input type="hidden" name="component" value="<?php echo $this->escape( $this->component ); ?>" /> -->
					<label for="component"><?php echo JText::_( 'COM_KOMENTO_ACL_SELECT_COMPONENT' ); ?> :</label>
					<?php echo $this->components; ?>
				</td>
			</tr>
		</table>
	</div>

	<div class="adminform-body">
		<table class="aclProfileSelection">
			<tr>
				<td width="50%">
					<fieldset class="adminform">
						<legend>Profiles</legend>
						<table class="admintable" cellspacing="1">
							<tr>
								<td width="300" class="key">
									<span><?php echo JText::_( 'COM_KOMENTO_ACL_PRESET' ); ?></span>
								</td>
								<td valign="top">
									<div class="has-tip">
										<div class="tip"><i></i><?php echo JText::_( 'COM_KOMENTO_ACL_PROFILES_DESC' ); ?></div>
										<select class="aclProfiles">
											<option value="none"><?php echo JText::_( 'COM_KOMENTO_ACL_PROFILES_NONE' ); ?></option>
											<option value="restricted"><?php echo JText::_( 'COM_KOMENTO_ACL_PROFILES_RESTRICTED' ); ?></option>
											<option value="basic"><?php echo JText::_( 'COM_KOMENTO_ACL_PROFILES_BASIC' ); ?></option>
											<option value="author"><?php echo JText::_( 'COM_KOMENTO_ACL_PROFILES_AUTHOR' ); ?></option>
											<option value="admin"><?php echo JText::_( 'COM_KOMENTO_ACL_PROFILES_ADMIN' ); ?></option>
											<option value="full"><?php echo JText::_( 'COM_KOMENTO_ACL_PROFILES_FULL' ); ?></option>
											<option value="custom"><?php echo JText::_( 'COM_KOMENTO_ACL_PROFILES_CUSTOM' ); ?></option>
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<a href="javascript:void(0)" class="showTableButton"><?php echo JText::_( 'COM_KOMENTO_ACL_SHOW_TABLE' ); ?></a>
								</td>
							</tr>
						</table>
					</fieldset>
				</td>
				<td></td>
			</tr>
		</table>

		<?php foreach( $this->rulesets as $type => $rulesets ) {
			$title = '';
			$aclType = explode(':', $type);
			if ($aclType[0] == 'user')
			{
				$title = Komento::getProfile( $aclType[1] )->getName();
			}
			if ($aclType[0] == 'usergroup')
			{
				$title = Komento::getUsergroupById( $aclType[1] );
			} ?>

		<table class="aclTable hidden">
			<tr>
				<?php foreach( $rulesets as $section => $rules ) { ?>
				<td valign="top" width="50%">
					<fieldset class="adminform">
						<legend><?php echo JText::_( 'COM_KOMENTO_ACL_SECTION_' . strtoupper($section) ); ?></legend>
						<table class="admintable" cellspacing="1">
							<?php foreach( $rules as $rule ) { ?>
							<tr>
								<td width="300" class="key">
									<span><?php echo JText::_($rule->title); ?></span>
								</td>
								<td valign="top">
									<div class="has-tip <?php echo $rule->name; ?>">
										<div class="tip"><i></i><?php echo JText::_($rule->title . '_DESC'); ?></div>
										<?php echo $this->renderCheckbox( $type . ':' . $rule->name, $rule->value ); ?>
									</div>
								</td>
							</tr>
							<?php } ?>
						</table>
					</fieldset>
				</td>
				<?php } ?>
			</tr>
		</table>

		<?php } ?>
	</div>

	<input type="hidden" name="target_component" value="<?php echo $this->escape($this->component); ?>" />
	<input type="hidden" name="target_id" value="<?php echo $this->escape($this->id); ?>" />
	<input type="hidden" name="target_type" value="<?php echo $this->escape($this->type); ?>" />
	<input type="hidden" name="target_profile" value=""; ?>
	<input type="hidden" name="layout" value="form" />
	<input type="hidden" name="option" value="com_komento" />
	<input type="hidden" name="c" value="acl" />
	<input type="hidden" name="task" value="change" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
