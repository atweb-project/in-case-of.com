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

class remositoryFileInfoHTML extends remositoryCustomUserHTML {

	protected function relatedDocs ($id) {
		$repository = remositoryRepository::getInstance();
		$types = explode(',', $repository->Classification_Types);
		if (empty($types)) return '';
		$typecase = '';
		foreach ($types as $key=>$type) $typecase .= "WHEN c.type = '$type' THEN $key ";
		$sql = "SELECT c.*, "
		." CASE $typecase ELSE 9 END AS typseq "
		." FROM #__downloads_classify AS c "
		." INNER JOIN #__downloads_file_classify AS fc ON c.id = fc.classify_id "
		." WHERE c.published != 0 AND c.hidden = 0 AND fc.file_id = $id ORDER BY typseq, c.name";
		$items = remositoryRepository::doSQLget($sql, 'remositoryClassification');
		foreach ($items as $item) $typelist[$item->type][] = $item;
		$mainHTML = '';
		if (isset($typelist)) foreach ($typelist as $type=>$classlist) $mainHTML .= $this->relatedDocsType ($type, $classlist);
		if ($mainHTML) return <<<RELATED_DOCS

		<h4>RELATED DOCUMENTS</h4>
		<p>
		This report falls under the following categories.  Click on a link below to explore similar documents.
		</p>
		<table>
		$mainHTML
		</table>

RELATED_DOCS;

	}

	protected function relatedDocsType ($type, $items) {
		if (0 == count($items)) return '';
		foreach ($items as $item) $links[] = $this->repository->RemositoryFunctionURL('classify', $item->id).$this->show($item->name).'</a>';
		$itemlist = implode (', ', $links);
		return <<<TYPE_HTML

		<tr>
		<td class="leftcol">$type:</td>
		<td class="rightcol">
		$itemlist
		</td>
		</tr>

TYPE_HTML;

	}

	protected function showComment (&$legend, &$comment) {
		echo "\n\t\t\t<dt>$legend</dt>";
		$legend = '';
		if ($this->tabcnt == 0) $class = 'remositorylight';
		else $class='remositorydark';
		echo "\n\t\t\t<dd class='$class'><em>$comment->name $comment->date</em> $comment->comment</dd>";
		$this->tabcnt = ($this->tabcnt+1) % 2;
	}

	protected function commentBox ($file) {
		$action = $this->repository->RemositoryBasicFunctionURL('fileinfo',$file->id);
		echo "\n\t\t\t<dt>"._DOWN_YOUR_COMM;
		echo '<br /><em>'._DOWN_MAX_COMM.'</em>';
		echo '</dt><dd>';
   		echo "<form method='post' action='$action'>";
		echo "<div><textarea class='inputbox' name='comment' rows='2' cols='35'></textarea>";
		echo "\n\t\t\t<input class='button' type='submit' name='submit_comm' value='"._DOWN_LEAVE_COMM."' />";
		echo "\n\t\t\t<input type='hidden' name='id' value='$file->id' />";
		echo "\n\t\t\t</div></form></dd>";
	}

	protected function immediateDisplay ($file, $displaynow) {
		$interface = remositoryInterface::getInstance();
		if (2 == $displaynow) {
			$link = $this->repository->RemositoryRawFunctionURL('fileinfo', $file->id).'&displaynow=1';
			$link = $interface->sefRelToAbs($link);
			$linktext = _DOWN_DISPLAY_NOW;
			$linkimage = $this->repository->RemositoryImageURL('monitor-info.png',48,48);
			echo <<<DISPLAY_LINK

			<h2>
				<a href="$link">
					$linkimage
					$linktext
				</a>
			</h2>

DISPLAY_LINK;

		}
		else {
			$downlink = $interface->getCfg('live_site').'/'.$this->repository->RemositoryRawFunctionURL('download',$file->id);
			echo <<<DISPLAY_JS

<script type="text/javascript" src='http://www.scribd.com/javascripts/view.js'></script>
<!-- The contents of this div will get replaced with the Scribd display -- >
  <div id='embedded_flash' >
    <a href='http://www.scribd.com'>Scribd</a>
  </div>

<script type="text/javascript">
  var scribd_doc = scribd.Document.getDocFromUrl('$downlink', '{$this->repository->Scribd}');
  scribd_doc.addParam('height', 800);
  scribd_doc.addParam('width', 600);
  scribd_doc.addParam('public', false);
  scribd_doc.addParam('mode', 'list');
  scribd_doc.addParam('title', '$file->filetitle');
  scribd_doc.addParam('extension',"$file->filetype");
  scribd_doc.write('embedded_flash');


</script>

DISPLAY_JS;

		}
	}

	/* Removed videoPlayer and audioPlayer in favour of using plugins
	protected function videoPlayer ($link, $title) {
		?>
		<!-- id=MediaPlayer1 -->
		<object id=mediaplayer1 type=application/x-oleobject
                  height="24" width="320"
                  classid="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6">
		<param name="url" value="<?php echo $link ?>">
		<param name="animationatstart" value="true">
		<param name="transparentatstart" value="true">
		<param name="autostart" value="false">
		<param name="showcontrols" value="true">

		<embed type="application/x-mplayer2" name="mediaplayer"  autostart="false" loop="false"  width="384" height="364"
       		src="<?php echo $link ?>" showcontrols="true">
		</embed>
		</object>
		<?php
	}

	protected function audioPlayer ($link, $title) {
		$interface = remositoryInterface::getInstance();
		$link = urlencode($link);
		$player_url = $interface->getCfg('live_site')."/components/com_remository/xspf_player/xspf_player_slim.swf?song_url=$link&amp;autoload=true&amp;autoplay=true&amp;song_title=".urlencode($title);
		?>
		<!-- id=FlashMediaPlayer1 -->
		<object type="application/x-shockwave-flash" width="400" height="40"
		data="<?php echo $player_url ?>">
		<param name="movie"
		value="<?php echo $player_url ?>" />
		</object>
		<?php
	}
	*/

	// This is the function that creates the output for a file details page
	public function fileinfoHTML( &$file, $autodown=0, $displaynow=0 ) {
		$interface = remositoryInterface::getInstance();
		$is_video = $file->is_video();
		$is_audio = $file->is_audio();
		$av_no_download = (($is_video AND !$this->repository->Video_Download) OR ($is_audio AND !$this->repository->Audio_Download)) ? true : false;
		$file->setMetaData();
		$thumbnails = new remositoryThumbnails($file);
		echo $this->pathwayHTML($file->getContainer());
		$file->showCMSPathway();
		$forbidden = $file->downloadForbidden($this->remUser, $message);
		if ($forbidden) {
			if (!$this->repository->See_Files_no_download) {
				echo $message;
				return;
			}
			$autodown = 0;
			$displaynow = 0;
		}
		$downloadstuff = $file->filetitle.' ';
		$usersubmitstuff = '';
		if ($file->updatePermitted($this->remUser)) {
			$usersubmitstuff .= "\n\t\t\t<p class='remositorycommand'>";
			$usersubmitstuff .= $this->repository->RemositoryFunctionURL('userupdate',$file->id);
			$usersubmitstuff .= $this->repository->RemositoryImageURL('edit.gif');
			$usersubmitstuff .= _DOWN_UPDATE_SUB.'</a></p>';
		}
		if ($file->deletePermitted($this->remUser)) {
			$deletestuff = "\n\t\t\t<p class='remositorycommand'>";
			$deletestuff .= $this->repository->RemositoryFunctionURL('userdelete',$file->id);
			$deletequery = _DOWN_DELETE_SURE;
			$deletestuff = str_replace('<a', "<a onclick=\"return confirm('$deletequery')\"", $deletestuff);
			$deletestuff .= $this->repository->RemositoryImageURL('edit.gif');
			$deletestuff .= _DOWN_DEL_SUB_BUTTON.'</a></p>';
			$usersubmitstuff .= $deletestuff;
		}
		$thumbupdatestuff = '';
		if ($this->repository->Max_Thumbnails) {
			if ($file->updatePermitted($this->remUser)) {
				$thumbupdatestuff .= "\n\t\t\t<p class='remositorycommand'>";
				$thumbupdatestuff .= $this->repository->RemositoryFunctionURL('thumbupdate',$file->id);
				$thumbupdatestuff .= $this->repository->RemositoryImageURL('edit.gif');
				$thumbupdatestuff .= _DOWN_UPDATE_THUMBNAILS.'</a></p>';
			}
		}
		$thumbimages = $thumbnails->displayAllThumbnails();

		echo "\n\t<div id='remositoryfileinfo'>";
		if (_REMOSITORY_SHOW_DOWN_SUMMARY) $this->showDownloadAccount($file);
		$syndstyle = (remositoryRepository::GetParam($_GET, 'syndstyle', '') == 'yes');
		if (!$forbidden AND $file->isAffordable($this->remUser)) {
			echo "\n";
			?>
		<script type="text/javascript">
		/* <![CDATA[ */
		function download(){window.location = <?php echo $file->downloadURL($autodown); ?>}
		/* ]]> */
		</script>
			<?php
		    if ($autodown == 1 AND !$syndstyle AND !$is_video AND !$is_audio) echo '<script type="text/javascript"> window.onload=download; </script>';
		    if ($autodown AND $syndstyle) {
				echo '<p><strong>In Firefox you can drag and drop the "Download" link to the "Install HTTP URL" box above, in IE you have to right click on "Download" and use "Copy shortcut" and paste into the box above, then click the "Upload URL & Install" button</strong></p>';
			}
			if (!$av_no_download OR !$autodown) {
				$downloadstuff .= $file->downloadLink($autodown);
				$downloadstuff .= '<br />' .$this->repository->RemositoryImageURL('download_trans.gif');
				$downloadstuff .= '<strong id="remositoryinfodown"> '.((!$autodown AND ($is_video OR $is_audio)) ? _DOWN_PLAY : _DOWNLOAD).'</strong></a><br />';
			}
		}
		else $downloadstuff .= $forbidden ? $message : $this->repository->RemositoryImageURL('download_trans.gif')._DOWN_TOO_FEW_CREDITS;
		echo "\n\t\t<h2>$downloadstuff</h2>";
		$thankyou = ($is_audio OR $is_video) ? _DOWN_PLAY_THANK_YOU : _DOWN_THANK_YOU;
		if ($autodown) {
		    echo '<h3>'.$thankyou.$this->show($file->filetitle).'</h3>';
		    if (!$syndstyle) {
		    	if ($is_video) $avhtml = $interface->triggerMambots('remositoryVideoPlayer', array($file->basicDownloadLink($autodown)));
		    	elseif ($is_audio) $avhtml = $interface->triggerMambots('remositoryAudioPlayer', array($file->basicDownloadLink($autodown)));
		    	if (!empty($avhtml)) echo $avhtml[0];
		    	else echo '<h4>'._DOWN_WAIT_OR_CLICK.'</h4>';
		    }
		    if ($file->download_text) $dltext = $file->download_text;
		    else $dltext = $this->repository->download_text;
		    echo <<<DOWNLOAD_TEXT

		    <div>
		    	{$this->translateDefinitions($dltext)}
		    </div>

DOWNLOAD_TEXT;

		}
		if ($thumbupdatestuff OR $thumbimages OR $usersubmitstuff) {
			echo "\n\t\t<div id='remositorythumbbox'>";
			echo "\n\t\t\t<div id='remositorycmdbox'>";
			if ($thumbimages) echo "\n\t\t\t<p>"._DOWN_THUMBNAILS."</p>";
			if ($thumbupdatestuff) echo $thumbupdatestuff;
			if ($usersubmitstuff) echo $usersubmitstuff;
			echo '</div>';
			echo "$thumbimages";
			echo "\n\t\t<!-- End of remositorythumbbox -->";
			echo "\n\t\t</div>";
		}
		echo "\n\t\t<dl>";
		if ($this->remUser->isAdmin()) $this->fileOutputBox(_DOWN_PUB, ($file->published ? _YES : _NO));
		if ($file->description) {
			if (_REMOSITORY_CONTENT_PLUGINS){
				$content = new stdClass();
				$content->text = $file->description;
				$interface = remositoryInterface::getInstance();
				$interface->triggerMambots('onPrepareContent', array (&$content, array(), 0));
				$file->description = $content->text;
			}
			$this->fileOutputBox(_DOWN_DESC, $file->description, false);
		}
		/*
		if (($file->licenseagree==0) AND ($file->license<>'')) $this->fileOutputBox (_DOWN_LICENSE, $file->license);
		if ($file->submitdate<>'') $this->fileOutputBox (_DOWN_SUB_DATE, date ($this->repository->Date_Format, $this->controller->revertFullTimeStamp($file->submitdate)));
		if ($file->submittedby<>'') {
			$submitter = new remositoryUser($file->submittedby,null);
			$subnames = $submitter->fullname().' ('.$submitter->name.')';
			if ($this->remUser->isLogged() AND $this->repository->Profile_URI) {
				$uri = sprintf($this->repository->Profile_URI, $this->remUser->id);
				$uri = remositoryInterface::getInstance()->sefRelToAbs($uri);
				$subnames = '<a href="'.$uri.'">'.$subnames.'</a>';
			}
			$this->fileOutputBox (_DOWN_SUB_BY, $subnames);
		}
		if ($file->filedate<>'') $this->fileOutputBox (_DOWN_FILE_DATE, date($this->repository->Date_Format,$this->controller->revertFullTimeStamp($file->filedate)));
		if ($file->fileauthor<>'') $this->fileOutputBox (_DOWN_FILE_AUTHOR, $file->fileauthor);
		if ($file->fileversion<>'') $this->fileOutputBox (_DOWN_FILE_VER, $file->fileversion);
		if ($file->filesize<>'') $this->fileOutputBox (_DOWN_FILE_SIZE, $file->filesize);
		if ($file->filetype<>'') $this->fileOutputBox (_DOWN_FILE_TYPE, $file->filetype);
		if ($file->filehomepage<>'') $this->URLDisplay (_DOWN_FILE_HOMEPAGE, $file->filehomepage);
		$this->fileOutputBox (_DOWN_DOWNLOADS, $file->downloads);
		*/
		$this->showFileDetails ($file, $this->remUser, 'D');

		if ($this->repository->Allow_Votes) $this->voteDisplay($file, true);
		// The following block of code provides the comment facility
		// If you want to replace it with Jom Comment, remove this block and replace it with:
		// $interface = remositoryInterface::getInstance();
		// include_once($interface->getCfg('absolute_path').'/mambots/content/jom_comment_bot.php');
		// echo jomcomment($file->id, "com_remository");
		// End of code block
		// For Joomla 1.5+ replace the name "mambots" by "plugins" in the code above
		if ($this->repository->Allow_Comments) {
			$commentsdb = remositoryComment::getComments($file->id);
			if ($commentsdb){
				$this->tabcnt = 1;
				$legend = _DOWN_COMMENTS;
				foreach ($commentsdb as $comment) {
					$this->showComment($legend, $comment);
					// Uncomment the next line if you want to restrict to a single comment
					// if ($comment->userid == $this->remUser->id) $hascommented = true;
				}
			}
			else {
				$legend = $this->remUser->isLogged() ? _DOWN_FIRST_COMMENT : _DOWN_FIRST_COMMENT_NL;
				$this->fileOutputBox('', '<div style="margin:10px 0;">'.$legend.'</div>');
			}
			if ($this->remUser->isLogged() AND empty($hascommented)) $this->commentBox($file);
//			include_once('components/com_reviews/reviews.class.php');
//			include_once('components/com_reviews/reviews.html.php');
//			echo HTML_reviews::listItemCommentsHTML('com_remository',$file->id);
//			echo HTML_reviews::solicitCommentHTML('com_remository', $file->id, "&func=fileinfo&id=$file->id");
		}
		// End of code for Remository comment facility
		echo "\n\t</dl>";
		$related = $this->relatedDocs($file->id);
		if ($related) echo <<<RELATED

			<div id="remositoryrelateddocs">
				$related
			<!-- End of remositoryrelateddocs -->
			</div>

RELATED;

		echo "\n\t<!-- End of remositoryfileinfo -->";
		echo "\n\t</div>";
		if ($file->plaintext) {
		echo "\n\t\t<div id='remositoryplaintext'>";
		  	highlight_string($file->getPlainText());
		  	echo "\n\t\t</div>";
		}
		if (!$forbidden AND $displaynow) $this->immediateDisplay($file, $displaynow);
		echo "\n\t\t<div id='remositoryfooter'></div>";
	}

	protected function show_smalldesc ($file) {
		// Suppress small description regardless of customization, since full description is shown
	}

	protected function show_vote_value ($file, $terminate=true) {
		// Suppress read only version of vote display, regardless of customization
		// since this is handled in a different way here
	}

	protected function show_submittedby ($file) {
		// Override common version for detailed information page, may link to profile
		if ($file->submittedby) {
			$submitter = remositoryUser::getUser($file->submittedby);
			$subnames = $submitter->fullname().' ('.$submitter->name.')';
			if ($file->submittedby AND $this->repository->Profile_URI) {
				$uri = sprintf($this->repository->Profile_URI, $file->submittedby);
				$uri = remositoryInterface::getInstance()->sefRelToAbs($uri);
				$subnames = '<a href="'.$uri.'">'.$subnames.'</a>';
			}
			$this->fileOutputBox (_DOWN_SUB_BY, $subnames);
        }
	}

	protected function showDownloadAccount ($file) {
		$downthis = sprintf(_DOWN_THIS_FILE_TODAY, $this->remUser->downloadCount($file->id), $this->remUser->maxDownloadsOneFile());
		$downall = sprintf(_DOWN_ALL_FILES_TODAY, $this->remUser->totalDown(), $this->remUser->maxDownloadsAllFiles());
		$saycredit = _REMOSITORY_USE_CREDITS ? sprintf(_DOWN_YOUR_CREDITS, $this->remUser->creditsAvailable()) : '';
		echo <<<DOWNLOAD_ACCOUNT

			<div class="remositorydownstatus">
				$downthis
			</div>
			<div class="remositorydownstatus">
				$downall
			</div>
			{$this->showDownloadCredits($saycredit)}

DOWNLOAD_ACCOUNT;

	}

	protected function showDownloadCredits ($credit) {
		if (_REMOSITORY_USE_CREDITS) return <<<SHOW_CREDITS

			<div class="remositorydownstatus">
				$credit
			</div>

SHOW_CREDITS;

	}
}
