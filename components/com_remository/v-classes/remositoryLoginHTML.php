<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

class remositoryLoginHTML extends remositoryCustomUserHTML {

	public function loginHTML ($heading) {
		$interface = remositoryInterface::getInstance();
		$request = $_SERVER['REQUEST_URI'];
		?>
		<form action="<?php echo $interface->sefRelToAbs( 'index.php' ); ?>" method="post" name="login" >
		<table class="remos_login">
		<tr>
		    <th>
		    <?php echo $heading; ?>
		    </th>
		</tr>
		<tr>
			<td align="center">
			<?php echo _USERNAME; ?>
			<br />
			<input name="username" type="text" class="inputbox" alt="username" size="10" />
			<br />
			<?php echo _PASSWORD; ?>
			<br />
			<input type="password" name="passwd" class="inputbox" size="10" alt="password" />
			<br />
			<input type="checkbox" name="remember" class="inputbox" value="yes" alt="Remember Me" />
			<?php echo _REMEMBER_ME; ?>
			<br />
			<input type="hidden" name="option" value="login" />
			<input type="submit" name="Submit" class="button" value="<?php echo _BUTTON_LOGIN; ?>" />
			</td>
		</tr>
		<tr>
			<td>
			<a href="<?php echo $interface->sefRelToAbs( 'index.php?option=com_registration&amp;task=lostPassword' ); ?>">
			<?php echo _LOST_PASSWORD; ?>
			</a>
			</td>
		</tr>
		<?php
		if ( $interface->getCfg( 'allowUserRegistration' ) ) {
			?>
			<tr>
				<td>
				<?php echo _NO_ACCOUNT; ?>
				<a href="<?php echo $interface->sefRelToAbs( 'index.php?option=com_registration&amp;task=register' ); ?>">
				<?php echo _CREATE_ACCOUNT; ?>
				</a>
				</td>
			</tr>
			<?php
		}
		?>
		</table>
		<input type="hidden" name="op2" value="login" />
		<input type="hidden" name="lang" value="<?php echo $interface->getCfg('lang'); ?>" />
		<input type="hidden" name="return" value="<?php echo $interface->sefRelToAbs( $request ); ?>" />
		<input type="hidden" name="message" value="" />
		</form>
	<?php
	}
}