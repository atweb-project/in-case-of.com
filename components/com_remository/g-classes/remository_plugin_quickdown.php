<?php

/**
 * Quickdown plugin for Remository 3.50+
 * License : http://www.gnu.org/copyleft/gpl.html ver 2
 * @ originally by Mamboaddons.com DEV, later Martin Brampton
 * @Copyright (C) 2004 - 2005 http://www.mamboaddons.com, 2006-9 Martin Brampton
 * martin@remository.com
 * http://remository.com
 * Special Thanks to wolfi from http://www.mamboport.de for preparing version
 * 1.1b for Mambo 4.5.1
 */

if (defined('_ALIRO_IS_PRESENT')) {
	class bot_remositoryQuickdown extends aliroPlugin {

		public function onPrepareContent ($article) {
		 	$worker = new remository_plugin_quickdown();
		 	return $worker->onPrepareContent ($this->params, $article);
		}

	}
}

class quickdown_parameters {
	private $general = null;
	private $local = array();

	public function __construct ($parms) {
		$this->general = $parms;
	}

	public function findLocal (&$text) {
		if (preg_match_all('/{quickparm:([a-z_]+):([^}]+)}/', $text, $matches, PREG_PATTERN_ORDER)) {
			foreach ($matches[0] as $sub=>$matchall) {
				$this->local[$matches[1][$sub]] = $matches[2][$sub];
			}
			$text = str_replace($matches[0], '', $text);
		}
	}

	public function get ($name, $default=null) {
		return isset($this->local[$name])  ? $this->local[$name] : $this->general->get($name, $default);
	}
}

class remository_plugin_quickdown {
	private $params = null;
	private $interface = null;
	private $database = null;
	private $remUser = null;
	private $files = array();
	private $base_url = '';
	

	function onPrepareContent ($params, $row, $published=true) {

		$this->params = new quickdown_parameters($params);
		$this->interface = remositoryInterface::getInstance();
		$this->database = $this->interface->getDB();
		$this->remUser = $this->interface->getUser();
		$this->interface->loadLanguageFile();
		// Find out $Itemid
		$this->base_url = 'index.php?option=com_remository&Itemid='.remositoryRepository::getInstance()->getItemid();        	// Base URL string

		// How do we handle published?
		$published = true;
		if (!$published) {
			$row->text = preg_replace('/{quickdown:.+?}/', '', $row->text);
			$row->text = preg_replace('/{quickcat:.+?}/', '', $row->text);
			$row->text = preg_replace('/{quickfolder:.+?}/', '', $row->text);
			return;
		}

		$content = $row->text;
		$this->params->findLocal($content);
		$matches = array ();

		// Get ids from Quickdown command
		if (preg_match_all('/{quickdown:([0-9]+)}/', $content, $matches, PREG_PATTERN_ORDER)) {

			//Get IDS
			foreach ($matches[0] as $sub=>$match) {
				$content = str_replace($match, $this->createLink($matches[1][$sub]), $content);
			}
			unset($matches);
		}

		$sorter = array ('', ' ORDER BY id', ' ORDER BY filetitle', ' ORDER BY downloads DESC', ' ORDER BY submitdate DESC', ' ORDER BY u.username', ' ORDER BY fileauthor', ' ORDER BY vote_value DESC, submitdate DESC');
		$orderby = $this->params->get('sort_order', _REM_DEFAULT_ORDERING);
		if (!isset($sorter[$orderby]) OR $orderby == 0) $orderby = _REM_DEFAULT_ORDERING;

		if (preg_match_all('/{quickcat:([0-9]+)}/', $content, $matches, PREG_PATTERN_ORDER)) {
			//Get IDS from quickcat cómmand
			foreach ($matches[0] as $sub=>$match) {
				$id = intval($matches[1][$sub]);
				$familylist = remositoryContainerManager::getInstance()->listGivenAndDescendants($id);
				$ctest = $familylist ? " AND f.containerid IN ($familylist)" : '';
				$sql = "SELECT f.id from #__downloads_files AS f WHERE f.published=1$ctest";
				$sql .= $sorter[$orderby];
				$this->database->setQuery($sql);
				$catrows = $this->database->loadObjectList();
				$output = '';
				if ($catrows) foreach ($catrows as $catrow){
					$output .= $this->createLink($catrow->id).$this->params->get("delimiter","<br />");
				}
				$content = str_replace($match, $output, $content);
			}
			unset($matches);
		}


		if (preg_match_all('/{quickfolder:([0-9]+)}/', $content, $matches, PREG_PATTERN_ORDER)) {
			//Get IDS from quickcat cómmand
			foreach ($matches[0] as $sub=>$match) {
				$id = intval($matches[1][$sub]);
				$sql = "SELECT id from #__downloads_files WHERE containerid=$id AND published='1'";
				$sql .= $sorter[$orderby];
				$this->database->setQuery($sql);
				$folderrows = $this->database->loadObjectList();
				$output = '';
				if ($folderrows) foreach ($folderrows as $folderrow){
					$output .= $this->createLink($folderrow->id).$this->params->get("delimiter","<br />");
				}
				$content = str_replace($match, $output, $content);
			}
			unset($matches);
		}

		if (preg_match_all('/{quicklatest:([0-9]+)(:([0-9]+))?}/', $content, $matches, PREG_PATTERN_ORDER)) {
			//Get IDS from quickcat cómmand
			foreach ($matches[0] as $sub=>$match) {
				$max = intval($matches[1][$sub]);
				$category = isset($matches[3][$sub]) ? $matches[3][$sub] : 0;
				$files = remositoryFile::newestFiles($category, $max, $this->remUser);
				$output = '';
				foreach ($files as $file){
					$this->files[$file->id] = $file;
					$output .= $this->createLink($file->id).$this->params->get("delimiter","<br />");
				}
				$content = str_replace($match, $output, $content);
			}
			unset($matches);
		}

		if (preg_match_all('/{quickpopular:([0-9]+)(:([0-9]+))?}/', $content, $matches, PREG_PATTERN_ORDER)) {
			//Get IDS from quickcat cómmand
			foreach ($matches[0] as $sub=>$match) {
				$max = intval($matches[1][$sub]);
				$category = isset($matches[3][$sub]) ? $matches[3][$sub] : 0;
				$files = remositoryFile::popularLoggedFiles($category, $max, 30, $this->remUser);
				$output = '';
				foreach ($files as $file){
					$this->files[$file->id] = $file;
					$output .= $this->createLink($file->id).$this->params->get("delimiter","<br />");
				}
				$content = str_replace($match, $output, $content);
			}
			unset($matches);
		}

		if (preg_match_all('/{quickrated:([0-9]+)(:([0-9]+))?}/', $content, $matches, PREG_PATTERN_ORDER)) {
			//Get IDS from quickcat cómmand
			foreach ($matches[0] as $sub=>$match) {
				$max = intval($matches[1][$sub]);
				$category = isset($matches[3][$sub]) ? $matches[3][$sub] : 0;
				$files = remositoryFile::ratedFiles($category, $max, $this->remUser);
				$output = '';
				foreach ($files as $file){
					$this->files[$file->id] = $file;
					$output .= $this->createLink($file->id).$this->params->get("delimiter","<br />");
				}
				$content = str_replace($match, $output, $content);
			}
			unset($matches);
		}

		$row->text = $content;
		return true;
	}

	/**
	 * This function create the output for the file with the given id
	 * @param int $id - The id of the file
	 * @param object $this->params - The parameter object, that hold the parameter
	 * @return String - The generated table with the Information for the file
	 */
	function createLink($id){
		//Getting File-info from Database
		$id = intval($id);
		if (isset($this->files[$id])) $file = $this->files[$id];
		else {
			$file = new remositoryFile($id);
			$file->getValues($this->remUser, true);
		}
		if($file->published) {  //--condition added by Gebus
			$rating = round($file->vote_value);
		
			// short description on top position
			if ($this->params->get("show_shortdesc",false) AND $this->params->get("show_shortdesc_pos")=='top'){
				$element[] = $file->smalldesc;
			}

			// full description on top position
			if ($this->params->get("show_desc",false) AND $this->params->get("show_desc_pos")=='top'){
				$element[] = $file->description;
			}
			//downloadlink
			if ($this->params->get("show_download",false)) {
				if ($file->is_audio() AND $this->params->get('show_audio', false)) $audiohtml = $this->interface->triggerMambots('remositoryAudioPlayer', array($file->basicDownloadLink(1), false, true));
				if ($file->is_video() AND $this->params->get('show_video', false)) $videohtml = $this->interface->triggerMambots('remositoryVideoPlayer', array($file->basicDownloadLink(1)));
				if (!empty($audiohtml)) $element[] = $audiohtml[0]."<strong> $file->filetitle</strong>";
				elseif (!empty($videohtml)) $element[] = $videohtml[0]."<strong> $file->filetitle</strong>";
				else {//-- updated by Gebus
					$icon = "";
					if($this->params->get("show_fileicon",false)) {
						$icon = "<img src=\"components/com_remository/images/file_icons/" . $file->icon . "\" alt=\"" . $file->filetype . "\"/>&nbsp;";
					}
					//we display the download link
					$linkurl = $this->interface->sefRelToAbs($this->base_url."&func=startdown&id=$id");
					$element[] = "<div style=\"float: right;\"><img src=\"components/com_remository/images/download_trans.gif\" border=\"0\" alt=\"\" />&nbsp;<a href=\"$linkurl\"><strong>"._DOWNLOAD."</strong></a></div>" .
						"<div style=\"float: left;\">" . $icon . "<a href=\"$linkurl\"><strong>$file->filetitle</strong></a></div>";
				}
			}

			//filetitle and link to entry
			if ($this->params->get('show_title', false)){
				$labels[] = _DOWN_FILE_TITLE;
				$text = $file->filetitle;

				if ($this->params->get("show_ltentry",false)) {
					$linkurl = $this->interface->sefRelToAbs($this->base_url."&func=fileinfo&id=$id");
					$text .= "&nbsp;<a href=\"$linkurl\"><i>("._DOWN_DETAILS.")</i></a>";
				}
				$values[] = $text;
			}
			//thumbnail
			if ($this->params->get('show_thumb', false)){
				$thumbnails = new remositoryThumbnails($file);
				$thumbdisplay = $thumbnails->displayOneThumbnail();
				//-- condtion if not null added by Gebus
				if(trim($thumbdisplay) != "") {
					$labels[] = _DOWN_THUMBNAILS;
					$values[] = $thumbdisplay;
				}
			}
			//filetype
			if ($this->params->get('show_type', false)){
				$labels[] = _DOWN_FILE_TYPE;
				
				//-- show_fileicon added by Gebus
				if ($this->params->get("show_fileicon",false)){
					$values[] = "<img src=\"components/com_remository/images/file_icons/" . $file->icon . "\" alt=\"" . $file->filetype . "\"/>";
				}
				else {
					$values[] = $file->filetype;
				}
			}
			//file version
			if ($this->params->get('show_version', false)){
				$labels[] = _DOWN_FILE_VER;
				$values[] = $file->fileversion;
			}
			//filesize
			if ($this->params->get('show_size', false)){
				$labels[] = _DOWN_FILE_SIZE;
				$values[] = $file->filesize;
			}
			//license
			if ($this->params->get('show_license', false)){
				$labels[] = _DOWN_LICENSE;
				$values[] = $file->license;
			}
			//author
			if ($this->params->get('show_author', false)){
				$labels[] = _DOWN_FILE_AUTHOR;
				$values[] = $file->fileauthor;
			}
			//homepage
			if ($this->params->get('show_homepage', false)){
				//check if http is given in url
				if(!strpos($file->filehomepage,"ttp://")){$nurl="http://$file->filehomepage";}
				else {$nurl="$file->filehomepage";}
				$labels[] = _DOWN_FILE_HOMEPAGE;
				$values[] = "<a href=\"$nurl\" target=\"_blank\">$file->filehomepage</a>";
			}
			//download count
			if ($this->params->get('show_count', false)){
				$labels[] = _DOWN_DOWNLOADS;
				$values[] = $file->downloads;
			}
			//show rating
			if ($this->params->get('show_rating', false)){
				$labels[] = _DOWN_RATING;
				$values[] = "<img src=\"components/com_remository/images/stars/$rating.gif\" alt=\"Average vote $rating stars\" />&nbsp;($file->vote_count "._DOWN_VOTES_TITLE.")";
			}
			/* form for frontend Voting */
			if ($this->params->get('allow_rating', false)){
				$labels[] = _DOWN_YOUR_VOTE;
				$link = $this->interface->sefRelToAbs('index.php?option=com_remository&func=fileinfo&id='.$id);
				$text = "<form method=\"post\" action=\"".$link."\">";
				$text .= "<select name=\"user_rating\" class=\"inputbox\">";
				$text .= "<option value=\"0\">?</option>";
				$text .= "<option value=\"1\">1</option>";
				$text .= "<option value=\"2\">2</option>";
				$text .= "<option value=\"3\">3</option>";
				$text .= "<option value=\"4\">4</option>";
				$text .= "<option value=\"5\">5</option>";
				$text .= "</select>";
				$text .= "<input class=\"button\" type=\"submit\" name=\"submit_vote\" value=\""._DOWN_RATE_BUTTON."\" />";
				$text .= "</form>";
				/* end form */
				$values[] = $text;
			}

			$innertext = '';
			if (isset($labels)) foreach ($labels as $sub=>$label) {
			
				$innertext .= <<<ONE_PROPERTY

							<tr>
								<td>
									<strong>$label</strong>
								</td>
								<td>
									{$values[$sub]}
								</td>
							</tr>

ONE_PROPERTY;

			}

			if ($innertext) $element[] = <<<ADD_INNERTEXT
	
						<table>
							$innertext
						</table>

ADD_INNERTEXT;

			// short description on bottom position
			if ($this->params->get("show_shortdesc",false) AND $this->params->get("show_shortdesc_pos")=='bottom') {
				$element[] = $file->smalldesc;
			}

			// full description on top position
			if ($this->params->get("show_desc",false) AND $this->params->get("show_desc_pos")=='bottom') {
				$element[] = $file->description;
			}
			
		if (!isset($element)) return '';
		if (1 == count($element)) return <<<ONE_ELEMENT

		<span class="quickdown">
			{$element[0]}
		</span>
		
ONE_ELEMENT;

		$output = '<tr><td>'.implode("</td></tr>\n<tr><td>", $element)."</td></tr>\n";

		return <<<ALL_OUTPUT

		<object class="quickdown">
			<table width="100%">
				$output
			</table>
		</object>

ALL_OUTPUT;
	}

	}

}
