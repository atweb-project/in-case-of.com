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

class remositoryMyDownloadsHTML extends remositoryCustomUserHTML {
	
	public function myDownloadHTML ($unratedfiles, $ratedfiles) {
		if ($unratedfiles) foreach ($unratedfiles as $file) {
			echo '<br />Here is a file you have downloaded but not rated: ';
		}
		else echo '<br />There are no files you have downloaded and not rated';
		
		if ($ratedfiles) foreach ($ratedfiles as $file) {
			echo '<br />Here is a file you have downloaded and rated: ';
		}
		else echo '<br />There are no files you have downloaded and rated';
	}
	
	function listFile ($file, $rate) {
		$infolink = $this->repository->RemositoryFunctionURL('fileinfo',$file->id);
		if ($file->icon == '') $infolink .= $this->repository->RemositoryImageURL('stuff1.gif');
		else $infolink .= $this->repository->RemositoryImageURL('file_icons/'.$file->icon);
		$infolink .= $file->filetitle.'</a>';
		if ($rate) {
			echo <<<NEEDS_RATING
			
			<div class='remositoryfileblock'>
				<h3>$infolink</h3>
				<p>
					 You downloaded this item on $downdate. Please rate this item for us and maybe 
					 you could leave a comment for the benefit of other users?
				</p>
			</div>
			
NEEDS_RATING;

			$this->voteDisplay($file, true);
		}
		else {
			
		}
		
	}
	
}
