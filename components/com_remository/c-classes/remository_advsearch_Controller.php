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

class remository_advsearch_Controller extends remositoryUserControllers {

	function advsearch ($func)	{
		$interface = remositoryInterface::getInstance();
		$interface->SetPageTitle(_DOWN_SEARCH_FILES);
		$interface->appendPathWay('Search', '');
		$search_text = remositoryRepository::getParam($_REQUEST,'search_text', '');
		$search_text = strip_tags($search_text);
		$regexquote = '/("[^"]*")/';
		if (false !== strpos(implode('',preg_split($regexquote, $search_text)),'"')) $search_text .= '"';
		// $classify = remositoryRepository::getParam($_REQUEST, 'classify', 0);
		if ('setallcats' == $func) {
			$handler = remositoryClassificationHandler::getInstance();
			$handler->setAllCategories();
			$func = 'search';
		}
		elseif ('classify' == $func) {
			$handler = remositoryClassificationHandler::getInstance(true);
			$handler->setAllCategories();
		}
		elseif ($newsearch = remositoryRepository::getParam($_REQUEST, 'newsearch')) {
			$handler = remositoryClassificationHandler::getInstance('1' == $newsearch);
			$onlyfree = remositoryRepository::getParam($_REQUEST, 'freedocs', 0);
			$handler->setOnlyFree($onlyfree);
			$categories = remositoryRepository::getParam($_REQUEST,'category');
			if (!$categories) $categories = array();
			if (1 == $newsearch AND 0 == count($categories)) $handler->setAllCategories();
			else $handler->setCategories($categories);
		}
		else $handler = remositoryClassificationHandler::getInstance();
		if (!empty($newsearch) OR 'classify' == $func) {
			$handler->setSearchText($search_text);
			if ($search_text) $handler->addClassify('*');
			else $handler->removeClassify('*');
		}
		$noindex = true;
		$classid = remositoryRepository::getParam($_REQUEST,'id');
		if ('' != $classid) {
			if ('*' == $classid) {
				if (!$search_text) {
					$handler->setSearchText('');
					$handler->removeClassify('*');
				}
			}
			elseif ($classid > 0) {
				$handler->addClassify($classid);
				if ('classify' == $func) {
					$noindex = false;
					$classification = $handler->getClassify($classid);
					if (is_object($classification)) $interface->setPageTitle($classification->name);
				}
			}
			elseif ($classid < 0) $handler->removeClassify(-$classid);
			if ('*' == $classid AND !$search_text) {
				$handler->setSearchText('');
				$handler->removeClassify('*');
			}
		}
		if ($noindex) $interface->addMetaTag('robots', 'noindex, follow');
		$solecategory = $handler->getSoleCategory();
		$pagecontrol = null;
		$file_array = $handler->getFileArray ($this->remUser, $this->orderby, 'search', $pagecontrol);
		$clstack = $handler->getClassifyStack();
		$handler->save();

		$view = remositoryUserHTML::viewMaker('AdvSearchResultsHTML', $this);
		$view->advSearchResultsHTML($solecategory, $file_array, $pagecontrol, $clstack);
	}
}