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

class remository_rss_Controller extends remositoryUserControllers {
	
	function rss ($func) {
		$interface = remositoryInterface::getInstance();
		include($interface->getCfg('absolute_path').'/components/com_remository/p-classes/feedcreator.class.php');
		$validtypes = array (
		'rss091' => 'RSS0.91', 
		'rss10' => 'RSS1.0', 
		'rss20' => 'RSS2.0', 
		'opml' => 'OPML', 
		'atom' => 'ATOM'
		);
		$max = remositoryRepository::GetParam($_REQUEST, 'max', 10);
		$days = remositoryRepository::GetParam($_REQUEST, 'days', 30);
		$rsstype = remositoryRepository::GetParam($_REQUEST, 'rsstype', 'rss20');
		if (isset($validtypes[$rsstype])) $selector = $validtypes[$rsstype];
		else {
			$rsstype = 'rss20';
			$selector = 'RSS2.0';
		}
		$errlevel = error_reporting(E_ERROR);
		$rss = new UniversalFeedCreator();
		$rss->useCached();
		$rss->link = $this->repository->RemositoryBasicFunctionURL ();
		$rss->syndicationURL = $interface->sefRelToAbs('index.php?'.$_SERVER['QUERY_STRING']);

		switch ($rtype = remositoryRepository::GetParam($_REQUEST, 'rtype', 'newest')) {
			case 'download':
			$rss->title = _DOWN_MOST_DOWNLOADED;
			$rss->description = _DOWN_MOST_DOWNLOADED_LONG;
				$files = remositoryFile::popularDownloadedFiles($this->idparm, $max, $this->remUser);
				break;
			case 'popular':
				$rss->title = _DOWN_POPULAR;
				$rss->description = sprintf(_DOWN_POPULAR_LONG, $days);
				$files = remositoryFile::popularLoggedFiles($this->idparm, $max, $days, $this->remUser);
				break;
			case 'newest':
			default:
				$rtype = 'newest';
				$rss->title = _DOWN_NEWEST;
				$rss->description = _DOWN_NEWEST_LONG;
				$files = remositoryFile::newestFiles($this->idparm, $max, $this->remUser);
				break;
		}

		if ($this->repository->headerpic) {
			$image = new FeedImage();
			$image->title = $rss->title;
			$image->url = $this->repository->headerpic;
			$image->link = $this->repository->RemositoryBasicFunctionURL ();
			$image->description = $interface->getCfg('sitename');
			$rss->image = $image;
		}

		foreach ($files as $file) {
    		$item = new FeedItem();
    		$item->title = $file->filetitle;
    		if ('newest' != $rtype) $item->title .= ' ('.$file->downloads.')';
    		$item->link = str_replace('&amp;', '&', $this->repository->RemositoryBasicFunctionURL ('fileinfo', $file->id));
	   		$item->description = str_replace ('&nbsp;', ' ', $file->smalldesc).' ('.remositoryContainerManager::getInstance()->getFullPath($file->containerid).')';
    		// This gives wrong format of date
    		$item->date = $this->revertFullTimeStamp($file->filedate);
    		$item->source = $interface->getCfg('live_site');
    		$item->guid = $item->link;
    		$thumbnails = new remositoryThumbnails($file);
    		$thumb = $thumbnails->oneThumbnailLink();
    		if ($thumb) {
				$image = new FeedImage();
				$image->title = $file->filetitle;
				$image->description = $file->filetitle;
				$image->url = $thumb;
				$image->link = $item->link;
    			$item->image = $image;
    		}
    
    		$rss->addItem($item);
		}
		$filename = $interface->getCfg('cachepath')."/remository.$rtype.$rsstype.xml";
		error_reporting(0);
		$rss->saveFeed($selector, $filename);
		error_reporting($errlevel);
	}
	
}