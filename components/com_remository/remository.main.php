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

class remositoryAPI {
	private static $instance = null;
	private $controller = null;

	public static function getInstance () {
		return is_object(self::$instance) ? self::$instance : (self::$instance = new self());
	}

	public function setController ($controller) {
		$this->controller = $controller;
	}

	public function getContainerID () {
		if (is_object($this->controller) AND method_exists($this->controller, 'getContainerID')) {
			return $this->controller->getContainerID();
		}
		else return 0;
	}

	public function getFileID () {
		if (is_object($this->controller) AND method_exists($this->controller, 'getFileID')) {
			return $this->controller->getFileID();
		}
		else return 0;
	}

}

class remositoryUserAdmin {
	private $magic_quotes_value = 0;
	public $c_classes_path = '';
	public $v_classes_path = '';
	private $repository = null;
	private $controller = null;

	function __construct ($component, $control_name, $alternatives, $default) {
		$interface = remositoryInterface::getInstance();
		// Is magic quotes on?
		if (get_magic_quotes_gpc()) {
		 	// Yes? Strip the added slashes
			$_REQUEST = $interface->remove_magic_quotes($_REQUEST);
			$_GET = $interface->remove_magic_quotes($_GET);
			$_POST = $interface->remove_magic_quotes($_POST);
			$_FILES = $interface->remove_magic_quotes($_FILES, 'name');
		}
		$this->magic_quotes_value = ini_get('magic_quotes_runtime');
		ini_set('magic_quotes_runtime', 0);
		$this->repository = remositoryRepository::getInstance();
		$this->c_classes_path = $this->v_classes_path = $interface->getCfg('absolute_path').'/components/com_remository/';
		$this->c_classes_path .= 'c-classes/';
		$this->v_classes_path .= 'v-classes/';
		$interface->SetPageTitle($this->repository->Main_Page_Title);
		$func = remositoryRepository::getParam ($_REQUEST, $control_name, $default);
		$views = array ('addfile' => 'addfile', 'containers' => 'select');
		$view = remositoryRepository::getParam ($_REQUEST, 'view');
		if (isset($views[$view])) $func = $views[$view];
		// if ('fileinfo' == $func) $func = 'startdown';
		if (isset($alternatives[$func])) $method = $alternatives[$func];
		else $method = $func;
		if ('select' == $method AND empty($_REQUEST['id'])) {
			$params = $interface->getParameters();
			if ($params AND $params->get('id', 0)) $_GET['id'] = $_REQUEST['id'] = $params->get('id');
		}
		if ('fileinfo' == $func AND $this->repository->Immediate_Download) $func = 'startdown';
		$classname = $component.'_'.$method.'_Controller';
		$classfile = $this->c_classes_path.$classname.'.php';
		$no_html = remositoryRepository::getParam($_REQUEST, 'no_html', 0);
		if (!$no_html) {
			echo "\n<!-- Start of Remository HTML -->";
			echo "\n<div id='remository'>";
		}
		if (class_exists($classname)) {
			$this->controller = new $classname($this);
			remositoryAPI::getInstance()->setController($this->controller);
			if (method_exists($this->controller,$method)) $this->controller->$method($func);
			else {
				header ($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
				$method = strip_tags($method);
				trigger_error("Component $component error: attempt to use non-existent method $method in $this->controller");
			}
		}
		else {
			header ($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
			$classname = strip_tags($classname);
			trigger_error("Component $component error: attempt to use non-existent class $classname");
		}
		if (!$no_html) {
			echo "\n</div>";
			echo "\n<!-- End of Remository HTML -->";
		}
		ini_set('magic_quotes_runtime', $this->magic_quotes_value);
		aliroAuthorisationAdmin::getInstance()->dropRole('Public');
	}

}

class remositoryUserControllers {
	var $repnum = 0;
	var $interface = null;
	var $remUser = '';
	var $repository = '';
	var $admin = '';
	var $idparm = 0;
	var $Itemid = 0;
	var $orderby = _REM_DEFAULT_ORDERING;
	var $submit_text = '';
	var $submitok = true;

	function __construct ($admin) {
		$this->repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		$this->interface = remositoryInterface::getInstance();
		$this->interface->loadLanguageFile();
		$mosConfig_absolute_path = $this->interface->getCfg('absolute_path');
		$mosConfig_lang = $this->interface->getCfg('lang');
		$mosConfig_sitename = $this->interface->getCfg('sitename');
		$this->admin = $admin;
		$this->idparm = remositoryRepository::getParam($_REQUEST, 'id', 0);
		$this->Itemid = remositoryRepository::getParam($_REQUEST, 'Itemid', 0);
		$params = $this->interface->getParameters();
		$sort_order = ($params AND $params->get('sort_order', 0)) ? $params->get('sort_order') : _REM_DEFAULT_ORDERING;
		$this->orderby = remositoryRepository::getParam($_REQUEST, 'orderby', $sort_order);
		$this->repository = remositoryRepository::getInstance();
		$this->remUser = $this->interface->getUser();
		$this->submit_text = _SUBMIT_FILE_BUTTON;
		$this->createSubmitText();
		$this->interface->triggerMambots('onRemositoryStartup', $this);
		$this->repository->checkCronTimer();
	}

	function remositoryHome () {
		$maindl = _MAIN_DOWNLOADS;
		$livesite = $this->interface->getCfg('live_site');
		$iconsize = _REMOSITORY_ICON_SIZE;
		// Change for multiple repositories
		//	<br />&nbsp;<br /><a href="../../index.php?option=com_remository&amp;repnum=$this->repnum&amp;Itemid=$this->Itemid"><img src="$livesite/components/com_remository/images/gohome.gif" width="$iconsize" height="$iconsize" border="0" align="absmiddle" alt="" /> $maindl</a>
		echo <<<GO_BACK

			<br />&nbsp;<br /><a href="../../index.php?option=com_remository&amp;Itemid=$this->Itemid"><img src="$livesite/components/com_remository/images/gohome.gif" width="$iconsize" height="$iconsize" border="0" align="absmiddle" alt="" /> $maindl</a>

GO_BACK;

		return;
	}

	function createFile ($onlypublished=true) {
		if ($this->idparm) {
			$file = new remositoryFile ($this->idparm);
			$file->getValues($this->remUser, $onlypublished);
			if ($file->containerid < 0) {
				$file = new remositoryTempFile ($this->idparm);
				$file->getValues($this->remUser, $onlypublished);
			}
			if ($file->id) return $file;
			die ('Fatal error - attempt to access unpublished file by non-admin user');
		}
		die ('Fatal error - we should have had a valid file ID');
	}

	function createContainer () {
		if ($this->idparm) {
			$container = new remositoryContainer ($this->idparm);
			return $container;
		}
		die ('Fatal error - we should have had a valid container ID='.$this->idparm);
	}

	function createSubmitText () {
		if ($this->submitok AND !$this->repository->Allow_User_Sub AND !$this->remUser->isAdmin()){
			$this->submitok = false;
			$this->submit_text = _SUBMIT_FILE_NOUSER;
		}
		clearstatcache();
		if ($this->submitok AND $this->remUser->isUser() AND $this->repository->Max_Up_Per_Day > 0 AND $this->remUser->uploadsToday() >= $this->repository->Max_Up_Per_Day) {
		    $this->submitok = false;
		    $this->submit_text = _SUBMIT_FILE_NOLIMIT;
		}
		// Removed check on disk space usage - too costly - failure message _SUBMIT_FILE_NOSPACE;
	}

	function revertFullTimeStamp($timestamp) {
		$subs = array (5,8,11,14,17);
		$parts = array();
	    $parts[] = substr($timestamp,0,4);
	    foreach ($subs as $i) $parts[] = substr($timestamp,$i,2);
	    $newdate = mktime($parts[3],$parts[4],$parts[5],$parts[1],$parts[2],$parts[0]);
	    return $newdate;
	}

	function error_popup ($message) {
		echo "<script> alert('".$message."'); window.history.go(-1); </script>\n";
	}

}

class remositoryPage {
	var $baseurl = '';
	var $itemcount = 0;
	var $itemsperpage = 10;
	var $startItem = 1;
	var $currentpage = 1;
	var $pagetotal = 1;
	var $itemid = 1;
	var $countshown = false;

	function remositoryPage ($itemcount, &$remUser, $itemsperpage, $page, $querystring) {
		$interface = remositoryInterface::getInstance();
		$repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		$this->itemid = $interface->getCurrentItemid();
		// Change for multiple repositories
		// $this->baseurl = "index.php?option=com_remository&repnum=$repnum&Itemid={$this->itemid}{$querystring}&page=";
		$this->baseurl = "index.php?option=com_remository&Itemid={$this->itemid}{$querystring}&page=";
		$this->itemcount = $itemcount;
		$this->itemsperpage = $itemsperpage;
		$this->startItem = 1;
		$this->finishItem = $itemsperpage;
		$this->pagetotal = ceil($this->itemcount/$this->itemsperpage);
		$this->setPage($page);
	}

	function setPage ($currentpage) {
		$this->currentpage = $currentpage;
		$basecount = ($currentpage - 1) * $this->itemsperpage;
		$this->startItem = $basecount;
	}

	function pageTitle ($page, $special=null) {
		echo 'title="';
		if ($special) echo $special;
		else echo _DOWN_PAGE_SHOW_RESULTS;
		$finish = $page * $this->itemsperpage;
		$start = $finish - $this->itemsperpage + 1;
		if ($finish > $this->itemcount) $finish = $this->itemcount;
		printf (_DOWN_PAGE_SHOW_RANGE, $start, $finish, $this->itemcount).'"';
	}

	// Custom code for Nucleus Research
	function showPageCount () {
		$choices = array (5, 10, 25);
		$radios = '';
		$handler = remositoryClassificationHandler::getInstance();
		$pagecount = $handler->getPageCount();

		foreach ($choices as $choice) {
			if ($choice == $pagecount) $checked = 'checked="checked"';
			else $checked = '';
			$radios .= <<<RADIO_BUTTON

		<input type="radio" name="pagecount" id="pagecount$choice" value="$choice" $checked onclick="document.remositoryperpage.submit();" />
		<label for="pagecount$choice">$choice</label>
RADIO_BUTTON;

			$checked = '';
		}

		$repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		return <<<PAGE_COUNT

		<div class="remositorypagecount">
		<form action="index.php" method="post" name="remositoryperpage">
		<strong>Results per page:&nbsp;</strong>
		$radios
		<input type="hidden" name="option" value="com_remository" />
		<input type="hidden" name="repnum" value="$repnum" />
		<input type="hidden" name="func" value="search" />
		<input type="hidden" name="Itemid" value="$this->itemid" />
		</form>
		<!-- End of remositorypagecount -->
		</div>

PAGE_COUNT;

	}

	function showNavigation () {
		$interface = remositoryInterface::getInstance();
		if ($this->itemcount <= $this->itemsperpage) return;
		$lowpage = max(1,intval($this->currentpage - (_PAGE_SPREAD+1)/2));
		$highpage = $lowpage + _PAGE_SPREAD;
		if ($highpage > $this->pagetotal) {
			$lowpage = max(1, $lowpage - ($highpage-$this->pagetotal));
			$highpage = $this->pagetotal;
		}
		$previous = $this->currentpage - 1;
		if ($previous) {
			$url = $interface->sefRelToAbs($this->baseurl.$previous);
			$prevtext = _DOWN_PREVIOUS;
			$previouslink = <<<PREVIOUS_LINK
			<a href="$url">$prevtext</a>
PREVIOUS_LINK;
			$url = $interface->sefRelToAbs($this->baseurl.'1');
			$startlink = <<<START_LINK
			<a href="$url">&laquo;</a>
START_LINK;
		}
		else $previouslink = $startlink = '';
		$page = $lowpage;
		if ($page > 1) $navdetails = '...';
		else $navdetails = '';
		$spacer = '';
		while ($page <= $highpage) {
			if ($page == $this->currentpage) {
				$navdetails .= $spacer.$page;
			}
			else {
				$url = $interface->sefRelToAbs ($this->baseurl.$page);
				$navdetails .= <<<NAV_DETAIL
				<a href="$url">$page</a>
NAV_DETAIL;
			}
			$spacer = ' ';
			$page++;
		}

		if ($page <= $this->pagetotal) $navdetails .= '...';
		$next = $this->currentpage + 1;

		if ($next <= $this->pagetotal) {
			$url = $interface->sefRelToAbs($this->baseurl.$next);
			$nexttext = _DOWN_NEXT;
			$nextlink = <<<NEXT_LINK
			<a href="$url">$nexttext</a>
NEXT_LINK;
			$url = $interface->sefRelToAbs($this->baseurl.$this->pagetotal);
			$lastlink = <<<LAST_LINK
			<a href="$url">&raquo;</a>
LAST_LINK;
		}
		else $nextlink = $lastlink = '';

		$pagetext = _DOWN_PAGE_TEXT;
		if (!$this->countshown) {
			// $count_control = $this->showPageCount();
			// If used, add $count_control after first div below
			$this->countshown = true;
			echo <<<BIG_NAVIGATION

			<div class="remositorypagecontrols">
			<div class='remositorypagenav'>
				<strong>$pagetext:&nbsp;</strong>
				$startlink $previouslink $navdetails $nextlink $lastlink
			<!-- End of remositorypagenav -->
			</div>
			<div class="remositorypagecontrolsend"></div>
			<!-- End of remositorypagecontrols -->
			</div>

BIG_NAVIGATION;

		}
		else echo <<<NAVIGATION

		<div class="remositoryfilelistingfooter">
		<div class='remositorypagenav'>
			<strong>$pagetext:&nbsp;</strong>
			$startlink $previouslink $navdetails $nextlink $lastlink
		<!-- End of remositorypagenav -->
		</div>
		</div>

NAVIGATION;

	}

	// Custom code for Nucleus Research
	function showItemSummary () {
		$summary = sprintf('<p>Displaying %s-%s results of <strong>%s search results</strong></p>', $this->startItem+1, min($this->startItem+$this->itemsperpage,$this->itemcount), $this->itemcount);
		echo <<<SUMMARY

		<div>
			$summary
		</div>

SUMMARY;

	}

	function startItem () {
		return $this->startItem;
	}

}
