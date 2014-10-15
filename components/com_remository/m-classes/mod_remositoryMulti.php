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

class mod_remositoryMulti extends mod_remositoryBase {

	private $modtype = 'newest';
	private $listtype = 'list';
	private $showcat = 0;
	private $showthumb = 0;
	private $showversion = 0;
	private $showdate = 0;
	private $showdownloads = 0;
	private $iconsize = 16;
	private $diconsize = 16;
	private $max = 5;
	private $maxchars = 0;
	private $date_format = 'M.d';
	private $category= 0;
	private $days = 30;
	private $html = '';
	
	public function showFileList ($module, &$content, $area, $params) {	
		$interface = remositoryInterface::getInstance();
		$this->remUser = $interface->getUser();
		
		/*********************Configuration*********************/
		// Type of module - popular, downloads, newest
		$this->modtype = $this->remos_get_module_parm($params,'modtype','newest');
		// Type of output - list of files or RSS link
		$this->listtype = $this->remos_get_module_parm($params,'listtype','list');
		// Set to 1 to show container, set to 0 to omit
		$this->showcat = $this->remos_get_module_parm($params,'showcat',0);
		// Set to 1 to show the file thumbnail (if any), set to 0 to not show thumbnail
		$this->showthumb = $this->remos_get_module_parm($params,'showthumb',0);
		// Set to 1 to show the file version (if any), set to 0 to not show version
		$this->showversion = $this->remos_get_module_parm($params,'showversion',0);
		// Set to non zero pixel size to show file icon, 0 to not show
		$this->iconsize = $this->remos_get_module_parm($params,'iconsize',16);
		// Set to non zero pixel size to show date icon, 0 to not show
		$this->diconsize = $this->remos_get_module_parm($params,'diconsize',16);
		// Set to 1 to show the date, set to 0 to not show date
		$this->showdate = $this->remos_get_module_parm($params,'showdate',0);
		// Set to 1 to show the number of downloads, set to 0 to not show downloads
		$this->showdownloads = $this->remos_get_module_parm($params,'showdownloads',0);
		// Max number of entries to show
		$this->max = $this->remos_get_module_parm($params,'max',5 );
		// Max number of description characters, 0 for no description
		$this->maxchars = $this->remos_get_module_parm($params,'maxchars',100);
		// Date format for display, 'none' if no display required
		$this->date_format = $this->remos_get_module_parm($params,'dateformat','%b.%g');
		// Category from which to select files
		$this->category = $this->remos_get_module_parm($params,'category', 0);
		// if (!$this->category) $this->showcat = 0;
		// Maximum number of days to consider where log file is used
		$this->days = $this->remos_get_module_parm($params, 'days', 30);

		$this->max = max($this->max,1);
		/*******************************************************/
	
		if ($this->listtype == 'list') {
			switch ($this->modtype) {
				case 'random':
					$files = remositoryFile::randomFiles ($this->category, $this->max, $this->remUser);
					break;
				case 'popular':
					$files = remositoryFile::popularLoggedFiles ($this->category, $this->max, $this->days, $this->remUser);
					break;
				case 'download':
					$files = remositoryFile::popularDownloadedFiles ($this->category, $this->max, $this->remUser);
					break;
				case 'newest':
				default:
					$files = remositoryFile::newestFiles ($this->category, $this->max, $this->remUser);
	
			}
		
			foreach ($files as $file) $this->displayFile($file);
		}
		else $this->displayRSS();
		$content = $this->html;
	}

	private function displayFile ($file) {
		$this->html .= <<<FILE_DISPLAY
				
			<div>
				{$this->displayVersion($file)}
				{$this->displayCategory($file)}
				{$this->displayThumb($file)}
				{$this->displayDate($file)}
				{$this->displayDownloads($file)}
				{$this->displayDescription($file)}
			</div>
				
FILE_DISPLAY;
		
	}
	
	private function displayVersion ($file) {
		$icon = $file->icon ? $file->icon : 'generic.png';
		$iconurl = $this->iconsize ? $this->repository->RemositoryImageURL('file_icons/'.$icon, $this->iconsize, $this->iconsize) : '';
		$version = $this->showversion ? ' '.$file->fileversion : '';
		$link = $this->repository->RemositoryFunctionURL('fileinfo', $file->id);
		$link = preg_replace('/(&(?!(#[0-9]{1,5};))(?!([0-9a-zA-Z]{1,10};)))/', '&amp;', $link);
		return <<<VERSION
			
			<div>$iconurl$link$file->filetitle</a>$version</div>
				
VERSION;

	}
	
	private function displayCategory ($file) {
		if ($this->showcat) {
			$caturl = $this->repository->RemositoryFunctionURL('select', $file->containerid);
			return <<<CATEGORY

			<div>($caturl$file->name</a>)</div>
CATEGORY;
		
		}
	}
	
	private function displayThumb ($file) {
		if ($this->showthumb) {
			$thumbnails = new remositoryThumbnails($file);
			return <<<THUMBNAIL
			
			{$thumbnails->displayOneThumbnail()}
			
THUMBNAIL;
			
		}
	}
	
	private function displayDate ($file) {
		if ($this->showdate AND strtolower($this->date_format) != 'none') {
			$dateurl = $this->diconsize ? $this->repository->RemositoryImageURL('calendar.gif', $this->diconsize, $this->diconsize) : '';
			$time = strtotime($file->filedate);
			if ($this->repository->Set_date_locale) setlocale(LC_TIME, $this->repository->Set_date_locale);
			$datetext = strftime($this->date_format, $time);
			return <<<DATE
			
			<div>$dateurl$datetext</div>

DATE;
			
		}
	}
	
	private function displayDownloads ($file) {
		if ($this->showdownloads) {
			$downtext = _DOWN_DOWNLOADS;
			return <<<DOWNLOADS
			
			$downtext $file->downloads
			
DOWNLOADS;
			
		}
	}
	
	private function displayDescription ($file) {
		if ($this->maxchars) {
			$description = (strlen($file->smalldesc) > $this->maxchars-3) ? substr($file->smalldesc,0,$this->maxchars-3).'...' : $file->smalldesc;
			return <<<DESCRIPTION
			
			<p>$description</p>
			
DESCRIPTION;
			
		}
	}
	
	private function displayRSS () {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$repository = remositoryRepository::getInstance();
		$url = "index.php?option=com_remository&func=rss&no_html=1&rtype=$this->modtype&max=$this->max&days=$this->days&Itemid=";
		if (isset($GLOBALS['remosef_itemids']['com_remository'])) $Itemid = $GLOBALS['remosef_itemids']['com_remository'];
		else {
			$database->setQuery("SELECT id FROM #__menu WHERE link = 'index.php?option=com_remository'");
			$GLOBALS['remosef_itemids']['com_remository'] = $Itemid = $database->loadResult();
		}
		$url .= intval($Itemid);
		if ($this->category) $url .= "&id=".$this->category;
		$sefurl = $interface->sefRelToAbs($url);
		$sefurl = preg_replace('/(&)([^#]|$)/','&amp;$2', $sefurl);
		if ($this->showcat) {
			$caturl = $repository->RemositoryFunctionURL('select', $this->category);
			$database->setQuery("SELECT name FROM #__downloads_containers WHERE id=$this->category");
			$catname = $database->loadResult();
		}
		
		$this->html .= "\n<div style='text-align:center'>";
		if ($this->showcat) $this->html .= "\n\t<h4>$caturl$catname</a></h4>";
		$this->html .= "\n\t<a href='$sefurl'>";
		$rssimage = $repository->RemositoryImageURL('feed-icon-32x32.gif').' RSS</a>';
		$rssimage = str_replace('img', 'img style="border:0"', $rssimage);
		$this->html .= $rssimage;
		$this->html .= "\n</div>";
	}

}
