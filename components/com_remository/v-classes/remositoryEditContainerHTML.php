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

class remositoryEditContainerHTML extends remositoryCustomUserHTML {

	/**
	*	@param $container : RemositoryContainer Object
	*/
	public function editContainerHTML ($container){
		$comment = '<p class="remositorycomment">' . _DOWN_CONTAINER_RENAME_INSTRUCTION .'</p>';
		$formurl = remositoryRepository::getInstance()->RemositoryBasicFunctionURL('editcontainer', $container->id);
		$box = $this->narrowInputBox(_DOWN_FOLDER_NAME, 'name', $container->name, 50);
		echo <<<INTERFACE_EDIT
		
		<div id="remositoryEditContainer">
			<form name="editForm" enctype="multipart/form-data" action="$formurl" method="post">
				$box
				<input type="hidden" name="id"  value="$container->id" />
                                <input class="button" type="submit" name="rename" value="{$this->show(_DOWN_RENAME)}" />
				<input class="button" type="submit" name="cancel" value="{$this->show(_CANCEL)}" />
			</form>
		</div>
		$comment
		
INTERFACE_EDIT;
	}
	
}
