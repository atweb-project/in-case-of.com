<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2008 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

class remository_overview_Controller extends remositoryUserControllers {
	
	function overview ($func) {
		$max = 10;
		$container = new remositoryContainer();
		$subfolders = $container->getVisibleChildren($this->remUser);
		foreach ($subfolders as $folder) {
			$names[] = $folder->name;
			$files[] = remositoryFile::newestFiles ($folder->id, $max, $this->remUser);
		}
		$view = remositoryUserHTML::viewMaker('OverviewHTML', $this);
		if (isset($names)) $view->overviewHTML($names, $files);
		else $view->emptyHTML();
	}
	
}