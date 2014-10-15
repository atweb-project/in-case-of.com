<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006-9 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

abstract class remositoryUserHTML extends remositoryHTML {
	protected $repnum = 0;
	protected $controller = '';
	protected $repository = '';
	protected $remUser = '';
	protected $submitok = false;
	protected $submit_text = '';
	protected $orderby = _REM_DEFAULT_ORDERING;
	protected $mainpicture = '';
	// For each item, make an entry with name used in HTML pointing to field name in file table
	protected $searchable = array (
	'search_filetitle' => 'filetitle',
	'search_filedesc' => 'description'
 	);
	// Declare this as a null string in the custom class to block it
	protected $basetag = "\n<base href=\"%s\" />";

	public function __construct ($controller) {
		parent::__construct();
		$this->repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		$this->controller = $controller;
		$this->repository = remositoryRepository::getInstance();
		$this->mainpicture = $this->repository->headerpic;
		$iconsize_plus_10 = _REMOSITORY_ICON_SIZE + 10;
		$iconsize_plus_4 = _REMOSITORY_ICON_SIZE + 4;
		$thumb_width_x = $this->repository->Small_Image_Width + 20;
		$thumb_width_x_plus = $thumb_width_x + 20;
		$thumb_height = $this->repository->Small_Image_Height;
		$thumb_height_y = $this->repository->Small_Image_Height + 50;
		if ($this->mainpicture) $headingcss = <<<HEADING_CSS
		
div#remositorypageheading {
        background-image:       url($this->mainpicture);
        height:                 64px;
}
div#remositorypageheading h2, div#remositorypageheading h3 {
        margin-left:       72px;
}
		
HEADING_CSS;

		else $headingcss = '';

		$css = <<<end_css
<style type='text/css'>
/* Remository specific CSS requiring variables */
$headingcss
.remositoryonethumb {
	width: {$thumb_width_x}px;
}
.remositorydelthumb {
	height:		{$thumb_height_y}px;
}
div.remositoryfilesummary {
	padding-right: {$thumb_width_x}px;
	min-height: {$thumb_height}px;
}
#remository h3.remositoryfileleft {
	padding-left: {$iconsize_plus_10}px;
	height: {$iconsize_plus_4}px;
}
#remository h3.remositoryfileright {
	padding-right: {$iconsize_plus_10}px;
	height: {$iconsize_plus_4}px;
}
/* End of variable Remository CSS */
</style>
end_css;

		$this->interface->addCustomHeadTag($css);

		$livesite = $this->interface->getCfg('live_site');
		$cssfile = file_exists(_REMOS_ABSOLUTE_PATH.'/images/remository/css/remository.css') ? $livesite.'/images/remository/css/remository.css' : $livesite.'/components/com_remository/remository.css';

		$css = <<<REMOS_HEADER
		
<link href='$cssfile' rel='stylesheet' type='text/css' />
		
REMOS_HEADER;

		$this->interface->addCustomHeadTag($css);
		if ('/' != substr($livesite,-1)) $livesite .= '/';

		if ($this->basetag) $this->interface->addCustomHeadTag(sprintf($this->basetag, $livesite));

		$this->remUser = $this->interface->getUser();
		$this->submitok = isset($controller->submitok) ? $controller->submitok : true;
		$this->submit_text = isset($controller->submit_text) ? $controller->submit_text : '';
		$this->orderby = isset($controller->orderby) ? $controller->orderby : _REM_DEFAULT_ORDERING;
	}

	public function getSearchable () {
		return $this->searchable;
	}
	
	protected function fileOutputBox ($title, $value, $suppressHTML=false) {
		echo $this->fileOutputBoxText ($title, $value, $suppressHTML);
	}

	protected function fileOutputBoxText ($title, $value, $suppressHTML=false) {
	    if ($suppressHTML) $value = PHP_VERSION_ID < 50203 ? htmlspecialchars($value, ENT_QUOTES, _CMSAPI_CHARSET) : htmlspecialchars($value, ENT_QUOTES, _CMSAPI_CHARSET, false);
		return <<<OUTPUT_BOX
		<dt>$title</dt>
		<dd>
		  $value
		</dd>
OUTPUT_BOX;
		
	}

	protected function displayPicture () {
		if ($this->mainpicture) return <<<MAIN_PICTURE

		<img src="$this->mainpicture" alt="" />
MAIN_PICTURE;

	}

	protected function displayRSS () {
		// The following three lines create RSS links - now controlled by config
		if ($this->repository->Show_RSS_feeds) {
			$rssurl = $this->repository->RemositoryBasicFunctionURL('rss');
			$this->interface->addCustomHeadTag("<link rel='alternate' type='application/rss+xml' title='RSS - "._DOWN_NEWEST."' href='$rssurl' />");
			return <<<SHOW_RSS

		<a href="$rssurl">{$this->repository->RemositoryImageURL('feedicon16.gif',16,16)} RSS</a>
SHOW_RSS;

		}
		// End of RSS link code
	}

	protected function mainPageHeading ($belowTop) {
		$title = _DOWNLOADS_TITLE;
		if ($title OR $this->mainpicture) {
			if ($belowTop) $headlevel = 'h3';
			else $headlevel = 'h2';
			$formurl = remositoryRepository::getInstance()->RemositoryBasicFunctionURL('search');
			$searchtext = _DOWN_SEARCH_FILES;
			$html = '';
			if (_REMOSITORY_TOP_SEARCH) $html .= <<<PAGE_SEARCH

	<div id="remositorylistsearch">
		<form method="post" action="$formurl">
			<div class="remositorysearchdetails">
				<input class="inputbox" type="text" name="search_text" id="search_text" value="$searchtext"
				onfocus="if(this.value=='$searchtext') this.value='';"
				onblur="if(this.value=='') this.value='$searchtext';"
				size="30" alt="$searchtext" maxlength="30" />
				<input type="hidden" name="submit" value="submit" />
				<input type="hidden" name="search_filetitle" value="1" />
				<input type="hidden" name="search_filedesc" value="1" />
			</div>
		</form>
	</div>

PAGE_SEARCH;

			$html .= <<<PAGE_HEAD

	<div id='remositorypageheading'>
		<$headlevel>$title
		{$this->displayRSS()}
		</$headlevel>
	<!-- End of remositorypageheading-->
	</div>

PAGE_HEAD;

			if (!$belowTop AND $this->repository->preamble) $html .= <<<PREAMBLE

			<div id="remositorypreamble">
				{$this->repository->preamble}
			</div>

PREAMBLE;

			return $html;
		}
	}

	protected function folderListHeading($container){
		$cname = PHP_VERSION_ID < 50203 ? htmlspecialchars($container->name, ENT_QUOTES, _CMSAPI_CHARSET) : htmlspecialchars($container->name, ENT_QUOTES, _CMSAPI_CHARSET, false);
		$deleteFolderIcon = $this->showDeleteContainerIcon($container, "remositoryDeleteCurrentFolder");
                $updateFolderIcon = $this->showUpdateContainerIcon($container, "remositoryUpdateCurrentFolder");
                echo "\n\t<div id='remositorycontainer'>" . $updateFolderIcon . $deleteFolderIcon;
		echo "\n\t\t<h2>$cname ";
		// The following three lines create the RSS link for the container - now controlled by config
		if ($this->repository->Show_RSS_feeds) {
			$rssurl = $this->repository->RemositoryBasicFunctionURL('rss', $container->id);
			$this->interface->addCustomHeadTag("<link rel='alternate' type='application/rss+xml' title='RSS - "._DOWN_NEWEST." - $cname' href='$rssurl' />");
			echo "<a href='$rssurl'>".$this->repository->RemositoryImageURL('feedicon16.gif',16,16).' RSS</a>';
		}
		// End of RSS code
		echo '</h2>';
		echo "\n\t\t<div>".$container->description.'</div>';
		echo "\n\t<!-- End of remositorycontainer -->";
		echo "\n\t</div>";
	}

	// To suppress the credits line, change the default for $show_credits to false
	// If you do this, please also make a $50 donation to the Remository project!
	protected function remositoryCredits ($show_credits=true) {
		$version = _REMOSITORY_VERSION;
		echo "\n\t<div id='remositorycredits'>";
		if ($show_credits AND $this->repository->Show_Footer) echo <<<CREDIT_LINE

		<small>
			<a href='http://remository.com' target='_blank'>Remository $version</a> utilise les technologies
			<a href='http://php-ace.com' target='_blank'>PHP</a>,
			<a href='http://sql-ace.com' target='_blank'>SQL</a>,
			<a href='http://css-ace.com' target='_blank'>CSS</a>,
			<a href='http://javascript-ace.com' target='_blank'>JavaScript</a>
		</small>

CREDIT_LINE;

		echo "\n\t<!-- End of remositorycredits-->";
		echo "\n\t</div>\n";
	}

	protected function pathwayHTML ($parent=null) {
		if (0 == ($this->repository->Remository_Pathway & 2)) return '';
		$homelink = $this->repository->RemositoryFunctionURL().$this->repository->RemositoryImageURL('gohome.gif').' '._MAIN_DOWNLOADS.'</a>';
		return <<<PATH_WAY

		<div id="remositorypathway">
			$homelink
			{$this->parentPath($parent)}
		<!-- End of remositorypathway-->
		</div>

PATH_WAY;

	}

	protected function parentPath ($parent) {
		return ($parent instanceof remositoryContainer) ? $parent->showPathway() : '';
	}

	// Extra function needed to integration pathway into CMS pathway
	protected function pathwayImage () {
		$interface = remositoryInterface::getInstance();
		$imagePath =  '/templates/'.$interface->getTemplate().'/images/arrow.png';
		if (file_exists( $interface->getCfg('absolute_path').$imgPath )) $image = '<img src="' . $interface->getCfg('live_site'). $imagePath . '" border="0" alt="arrow" />';
		else $image = $this->repository->RemositoryImageURL('arrow.png',9,9);
		return $image;
	}

	protected function URLDisplay ($text, $value) {
		if (!preg_match(_REMOSITORY_REGEXP_URL,$value)) {
			if (preg_match(_REMOSITORY_REGEXP_URL,'http://'.$value)) $value = 'http://'.$value;
			else {
				echo "\n\t\t\t<dt>$text</dt>";
				echo "\n\t\t\t<dd>$value</dd>";
				return;
			}
		}
		echo "\n\t\t\t<dt>$text</dt>";
		echo "\n\t\t\t<dd><a href='$value'>"._DOWN_CLICK_TO_VISIT.'</a></dd>';
	}
	
	protected function filesFooterHTML () {
		if (!$this->repository->Show_Footer) return;
		$fsearch = $this->footerSearchHTML();
		$fsubmit = $this->footerSubmitHTML();
		echo <<<FILES_FOOTER
		
		<div id='remositoryfooter'>
			$fsearch
			$fsubmit
		<!-- End of remositoryfooter-->
		</div>
		
FILES_FOOTER;

	}
	
	protected function footerSearchHTML () {
		$text = _DOWN_SEARCH;
		$surl = $this->repository->RemositoryFunctionURL('search');
		$simg = $this->repository->RemositoryImageURL('search.gif');
		return <<<FOOTER_SEARCH
		
		<div id='left'>
			$surl
			$simg
			$text</a>
		</div>
		
FOOTER_SEARCH;

	}
	
	protected function footerSubmitHTML () {
		//$container = new remositoryContainer($this->controller->getContainerID());
		if (!$this->repository->Allow_User_Sub){ 
			return '';
		}
		if ($this->submitok) {
			$idparm = remositoryRepository::GetParam($_REQUEST, 'id', 0);
			$startlink = $this->repository->RemositoryFunctionURL('addfile', $idparm);
			$endlink = _SUBMIT_FILE_BUTTON.'</a>';
		}
		else {
			$startlink = '';
			$endlink = $this->submit_text;
		}
		$subimage = $this->repository->RemositoryImageURL('add_file.gif');
		return <<<FOOTER_SUBMIT
		
		<div id='right'>
			$startlink
			$subimage
			$endlink
		</div>
		
FOOTER_SUBMIT;

	}

	protected function fileListing ($file, $container, $downlogo, $remUser, $showContainer=false, $type='A', $downlinktype=0) {
		$thumbnails = new remositoryThumbnails($file);
		$filefunc = $downlinktype ? 'directinfo' : 'fileinfo';
		$filelink = '';
		if ($this->repository->Allow_File_Info) $filelink = $this->repository->RemositoryFunctionURL($filefunc,$file->id);
		$iconfile = $file->icon ? $file->icon : 'stuff1.gif';
		$iconlink = is_readable(_REMOS_ABSOLUTE_PATH.'/images/remository/images/file_icons/'.$iconfile) ?
			'/images/remository/images/file_icons/'.$iconfile : '/components/com_remository/images/file_icons/'.$iconfile;
		$filelink .= $file->filetitle;
		if ($this->repository->Allow_File_Info) $filelink .= '</a>';
		if ($this->repository->Enable_List_Download AND is_object($container) AND $container->isDownloadable($this->remUser)) {
			if ($file->isAffordable($remUser)) {
				$downword =  $file->is_av() ? _DOWN_PLAY : _DOWNLOAD;
				$downlink = $file->downloadLink($downlinktype).$downword.'</a>';
			}
			else $downlink = _DOWN_TOO_FEW_CREDITS;
			$downhtml = <<<DOWN_HTML
			
				<h3 class="remositoryfileright">$downlink</h3>

DOWN_HTML;
			
		}
		else $downhtml = '';
		if ($showContainer AND is_object($container)) $downlink .= $this->showContainerLinks($container);
		$active_feature = $file->active_feature ? ' activefeature' : ''; 
		
		$thumblink = $thumbnails->oneThumbnailLink();
		echo <<<BEFORE_DETAILS
		
			<div class="remositoryfileblock$active_feature">
				<h3 class="remositoryfileleft" style="background-image:url($iconlink);">$filelink</h3>
				$downhtml
   				<div class="remositoryfilesummary" style="background-image:url($thumblink);"><dl>
   			
BEFORE_DETAILS;

		$this->showFileDetails($file, $remUser, $type);
		
		echo <<<AFTER_DETAILS
		
				<!-- End of remositoryfilesummary -->
				</dl>
				<div style="clear:both;"></div>
				</div>
			<!-- End of remositoryfileblock -->
			</div>

AFTER_DETAILS;

	}
	
	protected function showContainerLinks ($container) {
		$links[] = $this->repository->RemositoryFunctionURL('select', $container->id).$container->name.'</a>';
		if ($this->repository->Show_all_containers) while ($container->parentid) {
			$container = $container->getParent();
			array_unshift($links, $this->repository->RemositoryFunctionURL('select', $container->id).$container->name.'</a>');
		}
		return ' ('.implode(' - ', $links).')';
	}
	
	protected function showFileDetails ($file, $remUser, $type, $dodisplay=true) {
		if ($dodisplay AND $remUser->isAdmin()) $this->fileOutputBox(_DOWN_PUB, ($file->published == 1 ? _YES : _NO), false);

		$customobj = new remositoryCustomizer();
		$fieldnames = $customobj->getFileListFields();
		$showadminonly = explode(',', _REMOSITORY_SHOW_ADMIN_ONLY);
		$showadminonly = array_map('trim', $showadminonly);
		$customcontrol = $customobj->getUserCustomSpec();
		$count = 0;
		foreach ($customcontrol['S'] as $key=>$sequence) $reseq[$sequence][] = $key;
		if (isset($reseq)) {
			ksort($reseq);
			foreach ($reseq as $kset) foreach ($kset as $key) {
				if (!empty($customcontrol[$type][$key]) OR ($remUser->isAdmin() AND in_array($fieldnames[$key][0], $showadminonly))) {
					$fieldname = $fieldnames[$key][0];
					$method = 'show_'.$fieldname;
					if (method_exists($this, $method)) {
						$count++;
						if ($dodisplay) $this->$method($file);
					}
				}
			}
		}
		return $count;
	}
	
	protected function show_smalldesc ($file) {
		if ($file->smalldesc<>'') $this->fileOutputBox(_DOWN_DESC_SMALL, $file->smalldesc, !$file->autoshort);
	}
	
	protected function show_submittedby ($file) {
		if ($file->submittedby) {
		    $submitter = remositoryUser::getUser($file->submittedby);
			if ($file->submittedby AND $this->repository->Profile_URI) {
				$uri = sprintf($this->repository->Profile_URI, $file->submittedby);
				$uri = remositoryInterface::getInstance()->sefRelToAbs($uri);
				$subname = '<a href="'.$uri.'">'.$submitter->name.'</a>';
			}
			else $subname = $submitter->name;
		    $this->fileOutputBox(_DOWN_SUB_BY, $subname);
        }
	}
	
	protected function show_submitdate ($file) {
		if ($file->submitdate<>'') {
			$this->fileOutputBox(_DOWN_SUB_DATE, $this->convertAndDisplayDate($file->submitdate));
		}
	}

	protected function convertAndDisplayDate ($textdate) {
		$time = $this->controller->revertFullTimeStamp($textdate);
		return $this->displayDate($time);
	}

	protected function displayDate ($time) {
		if ($this->repository->Set_date_locale) {
			setlocale(LC_TIME, $this->repository->Set_date_locale);
			return strftime($this->repository->Date_Format, $time);
		}
		else return date ($this->repository->Date_Format, $time);
	}
	
	protected function show_filesize ($file) {
		if ($file->filesize<>'') $this->fileOutputBox(_DOWN_FILE_SIZE, $file->filesize);
	}

	protected function show_filedate ($file) {
		if ($file->filedate<>'') {
			$this->fileOutputBox(_DOWN_SUB_DATE, $this->convertAndDisplayDate($file->filedate));
		}
	}

	protected function show_downloads ($file) {
		$this->fileOutputBox(_DOWN_DOWNLOADS, $file->downloads);
	}
	
	protected function show_license ($file) {
		if ($file->license<>'') $this->fileOutputBox(_DOWN_LICENSE, $this->translateDefinitions($file->license), false);
	}
	
	protected function show_fileversion ($file) {
		if ($file->fileversion<>'') $this->fileOutputBox(_DOWN_FILE_VER, $file->fileversion);
	}
	
	protected function show_fileauthor ($file) {
		if ($file->fileauthor<>'') $this->fileOutputBox(_DOWN_FILE_AUTHOR, $file->fileauthor);
	}
	
	protected function show_filehomepage ($file) {
		if ($file->filehomepage<>'') $this->URLDisplay (_DOWN_FILE_HOMEPAGE, $file->filehomepage);
	}
	
	protected function show_vote_value ($file) {
		if ($this->repository->Allow_Votes) {
			$this->bareShowVotes ($file);
			echo "\n\t\t\t\t</dd>";
		}
	}
	
	protected function bareShowVotes ($file, $title='') {
		echo "\n";
		?>
			<dt><?php echo _DOWN_RATING; ?></dt>
			<dd>
				<div class='remositoryrating'><?php echo $this->repository->RemositoryImageURL('stars/'.$file->evaluateVote().'.gif',64,12, $title);
					echo _DOWN_VOTES;
					echo round($file->vote_count); ?></div>
		<?php
	}

	protected function voteDisplay (&$file, $entry, $linkfunc='fileinfo') {
		$logged = $this->remUser->isLogged() OR _REMOSITORY_VISITORS_VOTE;
		if ($entry) {
			if ($file->userVoted($this->remUser)) {
				$hasvoted = true;
				$title = _DOWN_THANKS_FOR_VOTING;
			}
			else {
				$hasvoted = false;
				$title = $logged ? _DOWN_CAST_YOUR_VOTE : _DOWN_LOGIN_TO_VOTE;
			}
		}
		else $title = '';
		$this->bareShowVotes ($file, $title);
		if ($entry AND $logged AND !$hasvoted) {
			// Change for multiple repositories
			// $formurl = $this->interface->sefRelToAbs("index.php?option=com_remository&repnum=$this->repnum&Itemid=".$this->interface->getCurrentItemid()."&func=$linkfunc&id=".$file->id);
			$formurl = $this->interface->sefRelToAbs("index.php?option=com_remository&Itemid=".$this->interface->getCurrentItemid()."&func=$linkfunc&id=".$file->id);
			?>
				<div>
					<form method="post" action="<?php echo $formurl; ?>">
						<select name="user_rating" class="inputbox">
							<option value="0">?</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
						<input class="button" type="submit" name="submit_vote" value="<?php echo _DOWN_RATE_BUTTON; ?>" />
						<input type="hidden" name="id" value="<?php echo $file->id; ?>" />
					</form>
				</div>
			<?php
		}
		echo "\n\t\t\t\t</dd>";
	}

	// Not presently used in Remository, but kept here for potential value of the code
	protected function multiOptionList ($name, $title, $options, $current, $tooltip=null) {
		$alternatives = explode(',',$options);
		$already = explode(',', $current);
		?>
		<tr>
	    <td width="30%" valign="top" align="right">
	  	<b><?php echo $title; ?></b>&nbsp;
	    </td>
	    <td valign="top">
		<?php
		foreach ($alternatives as $one) {
			if (in_array($one,$already)) $mark = 'checked="checked"';
			else $mark = '';
			$value = $name.'_'.$one;
			echo "<input type=\"checkbox\" name=\"$value\" $mark />$one";
		}
		if ($tooltip) echo '&nbsp;'.$this->tooltip($tooltip);
		echo '</td></tr>';
	}

	protected function tooltip ($text) {
		return '<a href="javascript:void(0)"  onmouseover="return escape('."'".$text."'".')">'.
		RemositoryRepository::getInstance()->RemositoryImageURL('tooltip.png').'</a>';
	}
	
	protected function translateDefinitions ($string) {
		$translators = get_defined_constants();
		return str_replace(array_keys($translators), array_values($translators), $string);
	}

	protected function simpleTickBox ($title, $name, $checked=true) {
		$checkcode = $checked ? 'checked="checked"' : '';
		return <<<TICK_BOX

				<p class="remositoryformentry">
					<label for="$name">$title</label>
					<input type="checkbox" name="$name" id="$name" value="1" $checkcode />
				</p>
				
TICK_BOX;

	}

	public static function viewMaker ($name, $controller) {
		$classname = class_exists('remositoryCustom'.$name, false) ? 'remositoryCustom'.$name : 'remository'.$name;
		return new $classname($controller);
	}
        
        
        //added by Gebus : delete container
        protected function showDeleteContainerIcon ($container, $cssClass){
            $mgtDeleteContent = "";
            if($this->repository->Allow_Container_Delete &&  $container->deletePermitted($this->remUser)){
                    $deleteIcon = $this->repository->RemositoryImageURL('management/folder_delete.png', 24, 24, _DOWN_DEL_CAT);
                    $deleteStartlink = $this->repository->RemositoryFunctionURL('deletecontainer', $container->id);
                    $mgtDeleteContent = '<span class="' . $cssClass . '">' . $deleteStartlink . $deleteIcon . '</a></span>';
            }
            return $mgtDeleteContent;
        }
        
        //added by Gebus : delete container
        protected function showUpdateContainerIcon ($container, $cssClass){
            $mgtUpdateContent = "";
            if($this->repository->Allow_Container_Edit &&  $container->updatePermitted($this->remUser)){
                    $updateIcon = $this->repository->RemositoryImageURL('management/folder_edit.png', 24, 24, _DOWN_EDIT_CAT);
                    $updateStartlink = $this->repository->RemositoryFunctionURL('editcontainer', $container->id);
                    $mgtUpdateContent = '<span class="' . $cssClass . '">' . $updateStartlink . $updateIcon . '</a></span>';
            }  
            return $mgtUpdateContent;
        }
	
	/**
	 * - Added by Gebus -
	 * Input box creation
	 * @param $title : text which will appear as input box title
	 * @param $name : field id
	 * @param $value : default value of the text field
	 * @param $width : size of the text field
	 * @param $tooltip : text to add of this form element 
	 */
	function narrowInputBox ($title, $name, $value, $width, $tooltip=null) {
		$html = <<<INPUT_BOX

		<label for="$name"><strong>$title</strong></label>
		<div style="padding: 8px 0;">
			<input id="$name" class="inputbox" type="text" name="$name" size="$width" value="$value" />
		</div>
INPUT_BOX;

		if ($tooltip) $html .= $tooltip;
		return $html;
    }
}
