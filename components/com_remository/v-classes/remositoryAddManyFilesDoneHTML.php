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

class remositoryAddManyFilesDoneHTML extends remositoryCustomUserHTML {
	public function addManyFilesDoneHTML (&$files) {
		$container=$files[0]->getContainer();
		$container->showCMSPathway();
		echo $this->pathwayHTML($container);
		echo "\n\t<h2>"._DOWN_ALL_DONE.'</h2>';
		echo "\n\t<div>";
		echo $this->repository->RemositoryFunctionURL('addfile');
		echo $this->repository->RemositoryImageURL('add_file.gif').'&nbsp;';
		echo _DOWN_SUBMIT_ANOTHER.'</a></div>';
		echo "\n\t<div id='remositoryfilelisting'>";
		foreach ($files as $file) {
			if ($file->filetitle<>'') $this->fileListing ($file, null, null, $this->remUser, false, 'C');
		}
		echo "\n\t</div>\n";
		$this->filesFooterHTML ();
		$this->remositoryCredits(false);
	}
}
