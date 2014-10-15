<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006-12 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

class mod_remositoryTree extends mod_remositoryBase {

	public function showTreeOverview ($module, &$content, $area, $params) {
		$this->showTreeOverviewFromNode($module, $content, $area);
	}
	
	/**
	 * --- Added by Gebus
	 * Description : show tree overview from a node in the tree rather only by the root
	 * $categoryRootId (integer) : remository category id which will be used as root - if null or invalid, the remository category root will be chosen
	 * $hiddenCategoryIds (array of integer) : remository category ids which will not appear in the tree
	**/
	public function showTreeOverviewFromNode ($module, &$content, $area, $rootName='remostree', $addFiles = false, $categoryRootId = 0, $hiddenCategoryIds = array()) {
		$live_site = $this->interface->getCfg('live_site');
		if (file_exists(_REMOS_ABSOLUTE_PATH.'/images/remository/css/dtree.css')) {
			$remlink = $live_site.'/images/remository/css/';
		}
		else $remlink = $live_site.'/components/com_remository/css/';
		$links = <<<LINKS

	<link rel="stylesheet" href="{$remlink}dtree.css" type="text/css" />

LINKS;

		$this->interface->addCustomHeadTag($links);
		$manager = remositoryContainerManager::getInstance();
		$addcontainer = $manager->getTreeAdds($this->remUser, "remostree" , $addFiles, $categoryRootId, $rootName);
		$view = remositoryUserHTML::viewMaker('TreeHTML',$this);
		$content = $view->treeHTML($addcontainer, $categoryRootId, $hiddenCategoryIds);
	}

}
