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

class remository_lclassify_Controller extends remositoryUserControllers {
    var $type = '';

	function lclassify ($func) {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$this->type = remositoryRepository::GetParam($_REQUEST, 'type');
		$this->type = $database->getEscaped($this->type);
		$sql = "SELECT * FROM #__downloads_classify";
		if ($this->type) $sql .= " WHERE type='$this->type' AND published != 0 AND hidden = 0";
		else $sql .= " WHERE published = 1";
		$sql .= " ORDER BY type, name";
		$database->setQuery($sql);
		$cls = $database->loadObjectList();
		if ($cls) foreach ($cls as $cl) $cllist[$cl->type][] = $cl;
		else $cllist = array();
		if ($this->type) {
			$link = $this->repository->RemositoryBasicFunctionURL('lclassify');
			// $interface->appendPathway('<a href="'.$link.'">List Tags</a>');
			$interface->appendPathway($this->type);
		}
		else $interface->appendPathway('Category');
		$view = remositoryUserHTML::viewMaker('ClassifyListHTML', $this);
		$view->classifyListHTML($this->type, $cllist);
	}

}