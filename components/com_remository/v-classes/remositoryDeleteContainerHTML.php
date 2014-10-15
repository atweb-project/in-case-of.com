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
*
* Written by Gebus
*/

class remositoryDeleteContainerHTML extends remositoryCustomUserHTML {

	public function waitForConfirmationHTML ($containerId, $isEmpty){
		$warning = "";
		if(! $isEmpty){
			$warning = '<p>' . $this->show(_DOWN_CONTAINER_DELETE_NOT_EMPTY) . '</p>';
		}
		$formurl = remositoryRepository::getInstance()->RemositoryBasicFunctionURL('deletecontainer', $containerId);
		echo <<<CONFIRM_DELETE
		
		$warning
		<div id="remositoryDeleteContainer">
			<p>{$this->show(_DOWN_CONFIRM_MSG)}</p>
			<form name="confirmDeleteForm" enctype="multipart/form-data" action="$formurl" method="post">
				<input class="button" type="submit" name="confirm" value="{$this->show(_DOWN_CONFIRM)}" />
				<input class="button" type="submit" name="cancel" value="{$this->show(_CANCEL)}" />
			</form>
		</div>
		
CONFIRM_DELETE;
	}
}
