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

class remositoryAdvSearchResultsHTML extends remositoryCustomUserHTML {
	protected $tabcnt = 0;
	
	public function advSearchResultsHTML(&$solecategory, &$files, $page, $clstack, $idsave = 0) {
		
		if ($idsave > 0) {
			$thispage = $this->repository->RemositoryBasicFunctionURL ('savesearch', $idsave);
			$title = urlencode ( "Saved Search " . $idsave );
			$rss = $this->repository->RemositoryBasicFunctionURL ( 'srss', $idsave );
			$rssicon = $this->repository->RemositoryImageURL ( 'feedicon16.png', 16, 16 );
			echo <<<SAVE_SEARCH_HEADER

			<div class="clipnote">
				<h3>Save Search</h3>
				<div class="clipnote-description">
					Save search as a bookmark or RSS feed using your browser's built-in capabilities, or by clicking below:
				</div>
				<div class="clearfix">
					<div class="icon-table-left"><strong>Bookmark:</strong></div>
					<div class="icon-table-right"><a href="$thispage">$thispage</a></div>
				</div>
				<div class="clearfix">
					<div class="icon-table-left"><strong>&nbsp;</strong></div>
					<div class="icon-table-right">
						<a id="icon-google-bookmark" target="_blank" title="save to google bookmarks" href="http://www.google.com/bookmarks/mark?op=add&amp;bkmk=$thispage&amp;title=$title">Google</a>
						<a id="icon-yahoo-bookmark" target="_blank" title="save to yahoo bookmarks" href="http://bookmarks.yahoo.com/toolbar/savebm?opener=tb&amp;u=$thispage&amp;t=$title">Yahoo</a>
						<a id="icon-live-bookmark" target="_blank" title="save to microsoft live favorites" href="https://favorites.live.com/quickadd.aspx?marklet=1&amp;mkt=en-us&amp;url=$thispage&amp;title=$title">Live Favorites</a>
						<a id="icon-delicious-bookmark" target="_blank" title="save to del.icio.us" href="http://del.icio.us/post?url=$thispage&amp;title=$title">Del.icio.us</a>
						<a id="icon-technorati-bookmark" target="_blank" title="save to technorati favorites" href="http://technorati.com/faves/?add=$thispage">Technorati</a>
					</div>
				</div>
				<div class="clearfix">
					<div class="icon-table-left" style="padding-top:4px;"><strong>RSS Feed:</strong></div>
					<div class="icon-table-right"><a href="$rss">$rss</a> $rssicon</div>
				</div>
				<div class="clearfix">
					<div class="icon-table-left"><strong>&nbsp;</strong></div>
					<div class="icon-table-right">
						<a id="icon-google-rss" target="_blank" title="add to google homepage or reader" href="http://fusion.google.com/add?feedurl=$rss">Add to Google</a>
						<a id="icon-yahoo-rss" target="_blank" title="add to my yahoo page" href="http://add.my.yahoo.com/content?.intl=us&amp;url=$rss">My Yahoo!</a>
						<a id="icon-netvibes-rss" target="_blank" title="add to netvibes" href="http://www.netvibes.com/subscribe.php?url=$rss&amp;type=feed">Netvibes</a>
						<a id="icon-newsgator-rss" target="_blank" title="add to newsgator" href="http://www.newsgator.com/ngs/subscriber/subext.aspx?url=$rss">Newsgator</a>
					</div>
				</div>
			</div>

SAVE_SEARCH_HEADER;
		
		} 

		elseif ($idsave < 0) {
			$link = $this->repository->RemositoryBasicFunctionURL ( 'resavesearch', abs ( $idsave ) );
			echo <<<ALREADY_SAVED_HEADER

			<div class="clipnote">
				<div class="clipnote-description">
					<strong>This page is a previously saved search&nbsp;-&nbsp;</strong>to redisplay bookmark / rss links, <a href="$link">click here</a>
				</div>
			</div>

ALREADY_SAVED_HEADER;
		
		}
		
		$handler = remositoryClassificationHandler::getInstance ();
		// $remove_icon = $this->repository->RemositoryImageURL('remove.gif', 19, 19);
		$classifylist = '';
		foreach ( $clstack as $id ) {
			if ('*' == $id) {
				$labeltext = _DOWN_SEARCHING_BY;
				$classify = new stdClass();
				$classify->name = $handler->getSearchText ();
				$classify->description = '';
				$classify->hidden = 0;
			}
			elseif ($id > 0) {
				$labeltext = _DOWN_FILTERING_BY;
				$classify = $handler->getClassify ( $id );
				$id = - $id;
			}
			else $classify = null;
			
			if (is_object ( $classify )) {
				if (! $classify->hidden) {
					$removelink = $this->repository->RemositoryBasicFunctionURL ( 'advsearch', $id );
					$removeterm = _DOWN_REMOVE_TERM;
					$classifylist .= <<<CLASSIFY_LINK

				<p>$labeltext:
				<strong>$classify->name</strong>
				(<a class="remove_term" href="$removelink">
				$removeterm
				</a>)
				</p>

CLASSIFY_LINK;
				
				}
				if ($classify->description) $described = $classify;
			}
		}
		if (0 == count($clstack) AND is_object($solecategory)) $this->folderListHeading($solecategory);
		elseif (isset($described) AND 0 == $idsave) echo <<<CLASSIFY_HEAD

		<div class="stickynote">
			<h2>$described->name</h2>
			<p>
				$described->description
			</p>
		</div>

CLASSIFY_HEAD;
		
		elseif (0 == count($clstack) AND $handler->countCategories()) {
			$nofilters = _DOWN_NO_FILTERS;
			echo <<<NO_FILTERS

		<div class="infonote">
			$nofilters
		</div>

NO_FILTERS;
		
		}
		echo <<<CLASSIFY_LIST

		<div class="remositorysearchblock">
			$classifylist
		</div>

CLASSIFY_LIST;
		
		$interface = remositoryInterface::getInstance();
		if (count ( $files )) {
			$page->showItemSummary ();
			$page->showNavigation ();
			echo "\n\t<div id='remositoryfilelisting'>";
			foreach ( $files as $file ) {
				if (empty($container) OR $file->containerid != $container->id) $container = new remositoryContainer($file->containerid);
				$this->fileListing ( $file, $container, null, $this->remUser );
				$this->tabcnt = ($this->tabcnt + 1) % 2;
			}
			echo "\n\t</div>\n";
			$page->showNavigation ();
		}
		else {
			if ($handler->areAllCategoriesSet ()) {
				$Itemid = remositoryRepository::getParam ( $_REQUEST, 'Itemid', 1 );
				$types = explode ( ',', $this->repository->Classification_Types );
				$noresults1 = _DOWN_NO_RESULTS_1;
				if (! empty ( $types )) {
					$html = '';
					$reviewtags = _DOWN_REVIEW_TAGS;
					foreach ( $types as $type ) {
						// Change for multiple repositories
						// $link = $interface->sefRelToAbs("index.php?option=com_remository&repnum=$this->repnum&Itemid=$Itemid&func=lclassify&type=$type");
						$link = $interface->sefRelToAbs ( "index.php?option=com_remository&Itemid=$Itemid&func=lclassify&type=$type" );
						$html .= <<<ONE_TYPE

					<li>$reviewtags <a href="$link">$type</a></li>
					
ONE_TYPE;
					
					}
					$noresults2 = _DOWN_NO_RESULTS_2;
					$contactus = _DOWN_CONTACT_US;
					echo <<<NO_CATS_NO_HITS

			<div id='remositoryfilelisting'>
				<h3>$noresults1</h3>
				<p>$noresults2</p>
				<ul>
					$html
					<li>$contactus</li>
				</ul>
			</div>

NO_CATS_NO_HITS;
				
				}
			} 

			else {
				$Itemid = remositoryRepository::getParam ( $_REQUEST, 'Itemid', 1 );
				// Change for multiple repositories
				// $setcatslink = sefRelToAbs("index.php?option=com_remository&repnum=$this->repnum&Itemid=$Itemid&func=setallcats");
				$setcatslink = $interface->sefRelToAbs ( "index.php?option=com_remository&Itemid=$Itemid&func=setallcats" );
				$allcatretry = _DOWN_ALL_CATS_RETRY;
				$addall = _DOWN_ADD_ALL_CATS;
				echo <<<CATS_NO_HITS

			<div id='remositoryfilelisting'>
				<h3>$noresults1</h3>
				<p>$allcatretry  <a href="$setcatslink">$addall</a></p>
			</div>

CATS_NO_HITS;
			
			}
		
		}
		$this->filesFooterHTML ();
	}
}
