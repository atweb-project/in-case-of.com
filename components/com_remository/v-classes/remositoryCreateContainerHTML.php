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

class remositoryCreateContainerHTML extends remositoryCustomUserHTML {

	/**
	*	@param $container : RemositoryContainer Object
        *       @param $warningMsg : warning string
	*/
	public function createContainerHTML ($parentContainer, $warningMsg){
            $warning = "";
            if(isset($warningMsg) && strlen(trim($warningMsg)) != 0){
                $warning = '<p class="remositorywarning">' . $warningMsg . '</p>';
            }
            $formurl = remositoryRepository::getInstance()->RemositoryBasicFunctionURL('createcontainer', $containerId);
            $box = $this->narrowInputBox(_DOWN_FOLDER_NAME, 'name', "", 50);
            echo <<<INTERFACE_EDIT
            
            $warning
            <div id="remositoryCreateContainer">
                    <form name="createForm" enctype="multipart/form-data" action="$formurl" method="post">
                            $box
                            <input type="hidden" name="id"  value="$parentContainer->id" />
                            <input class="button" type="submit" name="create" value="{$this->show(_DOWN_ADD_FOLDER)}" />
                            <input class="button" type="submit" name="cancel" value="{$this->show(_CANCEL)}" />
                    </form>
            </div>
            $comment
		
INTERFACE_EDIT;
	}

	
}
