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

class remositoryAddFileDoneHTML extends remositoryCustomUserHTML {
	
	public function addFileDoneHTML (&$file) {
		$file->showCMSPathway();
		echo $this->pathwayHTML($file->getContainer());
		echo "\n\t<h2>"._DOWN_ALL_DONE.'</h2>';
		echo "\n\t<div>";
		if ($file->published) echo _DOWN_AUTOAPP;
		else echo _DOWN_UP_WAIT;
		echo '</div>';
		echo "\n\t<h4>";
		echo $this->repository->RemositoryFunctionURL('addfile');
		echo $this->repository->RemositoryImageURL('add_file.gif');
		echo _DOWN_SUBMIT_ANOTHER.'</a>';
		echo '</h4>';
		if ($file->published) {
			echo "\n\t<h4>";
			echo $this->repository->RemositoryFunctionURL('fileinfo',$file->id);
			echo $this->repository->RemositoryImageURL('stuff1.gif');
			echo _DOWN_SUBMIT_INSPECT;
			echo '</a></h4>';
		}
		$this->filesFooterHTML ();
		$this->remositoryCredits(false);
	}
}