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

class remository_showlist_Controller extends remositoryUserControllers {
	private $showdownloads = false;
	private $showvotecount = true;
	private $showrating = true;

	function showlist ($func) {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$logtype = _REM_LOG_FILE_SELECTION;
		$uniqid = remositoryRepository::getParam($_REQUEST, 'uniqid');
		$uniqid = $database->getEscaped($uniqid);
		$bguri = $interface->getCfg('live_site').'/modules/mod_remositoryplayer/images/background.jpg';
		$banner = ('english' == $interface->getCfg('lang')) ? 'enterprise.jpg' : 'empresario.jpg';
		$database->setQuery("SELECT f.*, AVG(v.value) AS vote_value, COUNT(v.value) AS vote_count, 0 AS active_feature FROM #__downloads_email AS e"
			." INNER JOIN #__downloads_log AS l ON e.id = l.value AND l.type = $logtype"
			." INNER JOIN #__downloads_files AS f ON f.id = l.fileid"
			." LEFT JOIN #__downloads_log AS v ON v.type=3 AND v.fileid=f.id AND v.value != 0"
			." WHERE e.uniqid = '$uniqid' GROUP BY f.id"
		);
		$files = $database->loadObjectList();

		$site = $interface->getCfg('live_site');
		$extraheaders = <<<HEADERS

<link type="text/css" rel="stylesheet" href="$site/modules/mod_remositoryplayer/playlist.css" />

HEADERS;
		
		require_once ($interface->getCfg('absolute_path').'/modules/mod_remositoryplayer/mod_remositoryplayer.class.php');
		$player = new mod_remositoryPlayer('Selected songs', 3);
		$filelist = $player->displayAllFiles($files);

		echo <<<FILE_LIST

		<div id="remositoryshowlist" style="width: 500px; margin: auto; background-image: url($bguri);">
			<div class="remositorybanner">
				<a href="http://www.myhitplace.com">
					<img src="{$interface->getCfg('live_site')}/modules/mod_remositoryplayer/images/pageheader.png" />
				</a>
			</div>
			<div class="remositoryshowlistfiles" style="padding: 60px;">
				$filelist
			</div>
			<!-- AddThis Button BEGIN -->
			<div class="addthis_toolbox addthis_default_style" style="float:right; padding-right:50px;">
				<a href="http://addthis.com/bookmark.php?v=250&amp;pub=myhitplace" class="addthis_button_compact">Share / Compartir</a>
				<span class="addthis_separator">|</span>
				<a class="addthis_button_facebook"></a>
				<a class="addthis_button_live"></a>
				<a class="addthis_button_myspace"></a>
				<a class="addthis_button_google"></a>
				<a class="addthis_button_twitter"></a>
			</div>
			<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pub=myhitplace"></script>
			<!-- AddThis Button END -->
			<div class="remositorybanner">
				<a href="http://www.myhitplace.com">
					<img src="{$interface->getCfg('live_site')}/modules/mod_remositoryplayer/images/$banner" />
				</a>
			</div>
		</div>

FILE_LIST;

	}
	
	private function show ($string) {
		return $string;
	}

	private function showTitleHeading ($interface) {
		return <<<TITLE

					<th>
						<img src="{$interface->getCfg('live_site')}/components/com_remository/images/cd_audio.png" height="16" width = "16" alt="{$this->show(_DOWN_FILE_TITLE_SORT)}" />
					</th>

TITLE;

	}

	private function showDownloadsHeading ($interface) {
		if ($this->showdownloads) return <<<DOWNLOADS

					<th>
						<img src="{$interface->getCfg('live_site')}/components/com_remository/images/download.png" height="16" width = "16" alt="{$this->show(_DOWN_DOWNLOADS_SORT)}" />
					</th>

DOWNLOADS;

	}

	private function showVoteCountHeading ($interface) {
		if ($this->showvotecount) return <<<VOTE_COUNT

					<th>
						<img src="{$interface->getCfg('live_site')}/components/com_remository/images/20px-ballot_box.png" height="20" width = "20" alt="{$this->show(_DOWN_VOTES_TITLE)}" />
					</th>

VOTE_COUNT;

	}

	private function showRatingHeading ($interface) {
		if ($this->showrating) return <<<RATING

					<th>
						<img src="{$interface->getCfg('live_site')}/components/com_remository/images/favorite.png" height="16" width = "16" alt="{$this->show(_DOWN_RATING_TITLE)}" />
					</th>

RATING;

	}

	private function displayFile ($file) {
		$interface = remositoryInterface::getInstance();
		$repository = remositoryRepository::getInstance();
		// Both $link and $infolink are set, only one is used
		$link = $repository->RemositoryBasicFunctionURL('startdown', $file->id);
		$infolink = $repository->RemositoryBasicFunctionURL('fileinfo', $file->id);
		$ampencode = '/(&(?!(#[0-9]{1,5};))(?!([0-9a-zA-Z]{1,10};)))/';
		$link = preg_replace($ampencode, '&amp;', $link);
		// $starimage = $repository->RemositoryImageURL('stars/'.round($file->vote_value).'.gif',64,12);

		$titlewords = explode(' ', $file->filetitle);
		if (count($titlewords) > 3) {
			$file->filetitle = implode(' ', array_slice($titlewords, 0, 3)).' ...';
		}

		return <<<DISPLAY_FILE

				<tr>
					<td>
						{$this->smallAudioPlayer($file)}
					</td>
					<td>
						<a href="$link">
						<!-- <img src="{$interface->getCfg('live_site')}/components/com_remository/images/cd_audio.png" height="16" width = "16" alt="" /> -->
						$file->filetitle
						</a>
					</td>
					{$this->showDownloads($file)}
					{$this->showVoteCount($file)}
					{$this->showRating($file)}
				</tr>

DISPLAY_FILE;

	}

	private function showDownloads ($file) {
		if ($this->showdownloads) return <<<DOWNLOADS

					<td>
						$file->downloads
					</td>

DOWNLOADS;

	}

	private function showVoteCount ($file) {
		if ($this->showvotecount) return <<<VOTE_COUNT

					<td>
						$file->vote_count
					</td>

VOTE_COUNT;

	}

	private function showRating ($file) {
		$vote_value = number_format($file->vote_value, 1);
		if ($this->showrating) return <<<RATING

					<td>
						$vote_value
					</td>

RATING;

	}

	public function smallAudioPlayer ($file) {
		$interface = remositoryInterface::getInstance();
		$link = urlencode($this->basicDownloadLink($file));
		$player_url = $interface->getCfg('live_site')."/components/com_remository/xspf_player/dewplayer-mini.swf";
		return <<<SMALL_PLAYER

		<!-- id=FlashMediaPlayer1 -->
		<object type="application/x-shockwave-flash" width="19" height="22" data="$player_url">
			<param name="movie" value="$player_url" />
			<param name="FlashVars" value="mp3=$link&amp;autoload=0&amp;autoplay=0&amp;showslider=0&amp;width=25" />
			<param name="autoload" value="0" />
			<param name="wmode" value="transparent">
		</object>

SMALL_PLAYER;

	}

	private function  basicDownloadLink ($file) {
		$repository = remositoryRepository::getInstance();
		$downlink = $repository->RemositoryBasicFunctionURL('download',$file->id, null, null, null, $file->realname);
		return $downlink;
	}
}
