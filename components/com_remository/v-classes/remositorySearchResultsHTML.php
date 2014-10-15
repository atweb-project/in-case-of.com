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

class remositorySearchResultsHTML extends remositoryCustomUserHTML {
	protected $tabcnt=0;

	public function searchResultsHTML ($files, $search_text, $categories, $catselector, $pagecontrol) {

		$searchbox = remositoryUserHTML::viewMaker('SearchBoxHTML', $this->controller);
		$searchbox->searchBoxHTML($categories, $catselector, $search_text);

		if (count($files)) {
			echo "\n\t<div id='remositoryfilelisting'>";
			$pagecontrol->showNavigation();
			foreach ($files as $file) {
				$container = new remositoryContainer($file->containerid);
				$this->fileListing ($file, $container, null, $this->remUser, true, 'B');
				$this->tabcnt = ($this->tabcnt+1) % 2;
			}
			$pagecontrol->showNavigation();
			echo "\n\t</div>\n";
		}
		else echo <<<NO_RESULTS
		
			<div id='remositoryfilelisting'>
				<span class='remositorymessage'>
					{$this->showHTML(_DOWN_SEARCH_NORES)}
				</span>
			</div>
		
NO_RESULTS;

		$this->filesFooterHTML ();
		echo "\n\t\t<div id='remositoryfooter'></div>";
	}
}