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

class remositoryDownloadAgreeHTML extends remositoryCustomUserHTML {

	public function downloadAgreeHTML( &$file ) {
	
		$formurl = remositoryRepository::getInstance()->RemositoryBasicFunctionURL ('finishdown', $file->id);
		$file->showCMSPathway();
		echo $this->pathwayHTML($file->getContainer());
		$chk = $this->repository->makeCheck($file->id,'finishdown');
		$licence = $file->licenseagree ? $file->license : $this->repository->Default_Licence;
		$licence = $this->translateDefinitions($licence);
		?>
		<script type='text/javascript'>
			function enabledl () {	
				document.forms['agreeform'].elements['remositorydlbutton'].disabled=false
			}
		</script>
		<h2><?php echo _DOWN_LICENSE_AGREE; ?></h2>
		<form id='agreeform' method='post' action='<?php echo $formurl; ?>'>
		<p>
		<?php echo $licence; ?>
		</p>
		<div id='remositorylicenseagree'>
			<input name='agreecheck' type='checkbox' onclick='enabledl()' /><strong><?php echo _DOWN_LICENSE_CHECKBOX; ?></strong>
			<input class="button" id='remositorydlbutton' type="submit" value="<?php echo _DOWNLOAD; ?>" />
			<input type="hidden" name="da" value="<?php echo $chk; ?>" />
		</div>
		</form>
		<script type='text/javascript'>
			document.forms['agreeform'].elements['remositorydlbutton'].disabled=true
		</script>
		<?php
	}
}