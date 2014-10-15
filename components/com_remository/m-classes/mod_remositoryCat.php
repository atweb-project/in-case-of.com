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

define ('_REMOSITORY_CAT_MAX_DEPTH', 0);

class mod_remositoryCat extends mod_remositoryBase {
	private $showsmall = 0;
	private $max = 5;
	private $maxchars = 100;
	private $iconsize = 16;
	private $sortcat = 'filecount';
	private $sortorder = 'DESC';

	public function showFileList ($module, &$content, $area, $params) {

		/*********************Configuration*********************/
		// Set to '1' to Show the Description, set to 0 to not show it
		$this->showsmall = $this->remos_get_module_parm($params,'showsmall',0);
		// Max number of entries to show
		$this->max = $this->remos_get_module_parm($params,'max',5 );
		// Max number of description characters
		$this->maxchars = $this->remos_get_module_parm($params,'maxchars',100);
		// Size for icons
		$this->iconsize = $this->remos_get_module_parm($params,'iconsize',16);
		// Select Sorting methode : sort by id, name or files
		$this->sortcat = $this->remos_get_module_parm($params,'sortcat','filecount');
		// Select Sorting methode : ascending or descending
		$this->sortorder = $this->remos_get_module_parm($params, 'sortorder','DESC');
		// The container within which folders are to be displayed
		$container = $this->remos_get_module_parm($params, 'container', 0);

		$this->max = max($this->max,1);
		$this->maxchars = max($this->maxchars,20);
		if ($this->sortcat != 'filecount' AND $this->sortcat != 'id' AND $this->sortcat != 'name') $this->sortcat = 'filecount';
		if ($this->sortorder != 'ASC' AND $this->sortorder != 'DESC') $this->sortorder = 'DESC';
		/*******************************************************/
		
		$content = $this->makeCatList($container);
	}
	
	private function makeCatList ($container, $depth=0) {
		$tabclass_arr=explode(",",$this->repository->tabclass);

		// Find out $Itemid and make base URI
		$base_url = 'index.php?option=com_remository&Itemid='.$this->remos_getItemID('com_remository').'&func=select&id=';

		$sql = "SELECT id, name, description, filecount, icon from #__downloads_containers WHERE parentid=$container";
		if (!$this->repository->See_Files_no_download AND !$this->remUser->isAdmin()) {
			$authoriser = aliroAuthoriser::getInstance();
			$refuseSQL = $authoriser->getRefusedListSQL ('aUser', $this->remUser->id, 'remosFolder', 'download,edit', 'id');
			if ($refuseSQL) $sql .= ' AND '.$refuseSQL;
		}
		$sql .= " ORDER BY $this->sortcat $this->sortorder LIMIT $this->max";
		$this->database->setQuery( $sql );
		$categories = $this->database->loadObjectList();
		if (!$categories) $categories = array();

		// $this->remos_module_CSS ();
		$html = <<<START_CATS
		
		<table cellspacing="2" cellpadding="1" border="0" width="100%">

START_CATS;

		$tabcnt = 0;
		foreach ($categories as $category) {
			if ($this->showsmall) {
				$sdesc = '';
				if ($category->description<>'') {
					$slen = 0;
					$stripped = strip_tags($category->description);
					$slen = strlen($stripped);
					if ($slen>=$this->maxchars) $sdesc="<br/>".substr($stripped,0,$this->maxchars).'...';
					else $sdesc="<br/>".$stripped;
				} 
				elseif ($category->name<>'') {
					$slen = 0;
					$slen = strlen($category->name);
					if ($slen>=$this->maxchars) $sdesc="<br/>".substr($category->name,0,$this->maxchars).'...';
					else $sdesc="<br/>".$category->name;
				}
			}
			else $sdesc = '';
			$filesnum = $category->filecount ? $category->filecount : 0;
			$component_url = remositoryInterface::getInstance()->sefRelToAbs($base_url.$category->id);
			$curicon = $this->iconOrIndents($depth, $category);
			$html .= <<<ADD_CAT
			
			<tr class="{$tabclass_arr[$tabcnt]}"><td width="80%"><a href="$component_url">$curicon $category->name</a>$sdesc</td><td width="15%" class="number">($filesnum)</td></tr>
			
ADD_CAT;

			if (_REMOSITORY_CAT_MAX_DEPTH > $depth) $html .= <<<ADD_SUBCAT
			
			<tr class="{$tabclass_arr[$tabcnt]}"><td colspan="2" width="100%">{$this->makeCatList($category->id, $depth+1)}</td></tr>
			
ADD_SUBCAT;
			$tabcnt = 1 - $tabcnt;
		}
		$html .= "\n</table>\n";	
		return $html;
	}
	
	private function iconOrIndents ($depth, $category) {
		if (0 == $depth) return remositoryRepository::RemositoryImageURL('folder_icons/'.($category->icon ? $category->icon : 'folder_yellow.gif'), $this->iconsize, $this->iconsize);
		$indent = '';
		for ($i = 0; $i < $depth; $i++) $indent .= '&nbsp;&nbsp;-';
		return $indent;
	}

}
