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

class listCpanelHTML extends remositoryAdminHTML {
	private $site = '';
	private $path = '';
	private $cachepath = '';

	public function __construct (&$controller, $limit, $clist) {
		parent::__construct($controller, $limit, $clist);
		$interface = remositoryInterface::getInstance();
		$this->site = $interface->getCfg('live_site');
		$this->adminsite = $interface->getCfg('admin_site');
		$this->cachepath = $interface->getCfg('cachepath');
	}

	private function display ($service) {
	    
		// Change for multiple repositories
		// $link = $this->adminsite."/index2.php?option=com_remository&repnum=$this->repnum&act=".$service[1];
		$link = $this->adminsite."/{$this->interface->indexFileName()}?option=com_remository&act=".$service[1];
		echo "\n\t<div class='remositorycpitem' style='height:68px; width:81px; padding:5px; border:1px solid #999; margin:2px; float:left'>";
		echo "\n\t\t<a href='$link'>";
		echo "\n\t\t<img style='border:0' src='$this->site/components/com_remository/images/admin/{$service[2]}' height='24' width='24' />";
		echo "\n\t\t<div>{$service[0]}</div></a>";
		echo "\n\t<!-- End of remositorycpitem-->";
		echo "\n\t</div>";
	}

	public function view ($repositories, $repnum) {

		$basic = array (
			array(_DOWN_ADMIN_ACT_CONTAINERS, 'containers', 'categories.png'),
			array(_DOWN_ADMIN_ACT_FILES, 'files', 'addedit.png'),
			array(_DOWN_ADMIN_ACT_GROUPS, 'groups', 'user.png'),
			array(_DOWN_ADMIN_ACT_UPLOADS, 'uploads', 'module.png'),
			array(_DOWN_ADMIN_ACT_CONFIG, 'config', 'config.png')
		);

		$handlefiles = array (
			array(_DOWN_ADMIN_ACT_UNLINKED, 'unlinked', 'langmanager.png'),
			// array(_DOWN_ADMIN_ACT_FTP, 'ftp', 'sections.png'),
			array(_DOWN_ADMIN_ACT_ADDSTRUCTURE, 'addstructure', 'sections.png'),
			array(_DOWN_ADMIN_ACT_MISSING, 'missing', 'searchtext.png')
		);

		$housekeeping = array (
			array(_DOWN_ADMIN_ACT_COUNTS, 'counts', 'cpanel.png'),
			array(_DOWN_ADMIN_ACT_DOWNLOADS, 'downloads', 'cpanel.png'),
			array(_DOWN_ADMIN_ACT_PRUNE, 'prune', 'trash.png'),
			array(_DOWN_ADMIN_ACT_THUMBS, 'thumbs', 'mediamanager.png'),
			array(_DOWN_CLASSIFICATIONS, 'classifications', 'categories.png')
		);

		$specials = array (
			array(_DOWN_ADMIN_ACT_DBCONVERT, 'dbconvert', 'dbrestore.png'),
			array(_DOWN_ADMIN_ACT_DBCONVERT2, 'dbconvert2', 'dbrestore.png'),
		);

		$info = array (
			array(_DOWN_ADMIN_ACT_STATS, 'stats', 'impressions.png'),
			array(_DOWN_ADMIN_ACT_ABOUT, 'about', 'credits.png'),
			array(_DOWN_ADMIN_ACT_SUPPORT, 'support', 'support.png')
		);

		$this->formStart(_DOWN_CPANEL_RETURN);
		echo '</table>';

		$repository = remositoryRepository::getInstance();
		if (!empty($_SESSION['remositoryResetCounts'])) {
			$repository->resetCounts(array());
			unset ($_SESSION['remositoryResetCounts']);
		}
		if ($repository->Use_Database) $status = "<span style='color:green'>"._DOWN_DATABASE."</span>";
		else $status = _DOWN_FILE_SYSTEM;
		$legend = _DOWN_ADMIN_CPANEL_STORE;
		echo "\n<div style='font-weight:bold; padding:5px; margin:5px; border:1px solid #999'>$legend $status</div>";
		if (is_writeable($repository->Down_Path)) $status = "<span style='color:green'>"._DOWN_WRITEABLE."</span>";
		else $status = "<span style='color:red'>"._DOWN_NOT_WRITEABLE."</span>";
		$legend = _DOWN_ADMIN_CPANEL_FILESTORE;
		echo "\n<div style='font-weight:bold; padding:5px; margin:5px; border:1px solid #999'>$legend $repository->Down_Path $status</div>";
		if (is_writeable($repository->Up_Path)) $status = "<span style='color:green'>"._DOWN_WRITEABLE."</span>";
		else $status = "<span style='color:red'>"._DOWN_NOT_WRITEABLE."</span>";
		$legend = _DOWN_ADMIN_CPANEL_UPLOADS;
		echo "\n<div style='font-weight:bold; padding:5px; margin:5px; border:1px solid #999'>$legend $repository->Up_Path $status</div>";
		if (is_writeable($this->cachepath)) $status = "<span style='color:green'>"._DOWN_WRITEABLE."</span>";
		else $status = "<span style='color:red'>"._DOWN_NOT_WRITEABLE."</span>";
		$legend = _DOWN_ADMIN_CPANEL_CACHEPATH;
		echo "\n<div style='font-weight:bold; padding:5px; margin:5px; border:1px solid #999'>$legend $this->cachepath $status</div>";
		
		// Change for multiple repositories
		// $this->listRepositories($repositories, $repnum);
		
		echo "\n<div id='remositorycpbasic' style='width:640px; padding:2px;'>";
		echo "\n\t<h3 style='float:left; width:150px'>"._DOWN_CPANEL_SUB_BASIC."</h3>";
		foreach ($basic as $service) $this->display($service);
		echo "\n<!-- End of remositorycpbasic -->";
		echo "\n</div>";

		echo "\n<div id='remositorycpfiles' style='clear:left; width:640px; padding:2px;'>";
		echo "\n\t<h3 style='float:left; width:150px'>"._DOWN_CPANEL_SUB_FILES."</h3>";
		foreach ($handlefiles as $service) $this->display($service);
		echo "\n<!-- End of remositorycpfiles -->";
		echo "\n</div>";

		echo "\n<div id='remositorycphkeep' style='clear:left; width:640px; padding:2px;'>";
		echo "\n\t<h3 style='float:left; width:150px'>"._DOWN_CPANEL_SUB_HKEEP."</h3>";
		foreach ($housekeeping as $service) $this->display($service);
		echo "\n<!-- End of remositorycphkeep -->";
		echo "\n</div>";

		echo "\n<div id='remositorycpspecial' style='clear:left; width:640px; padding:2px;'>";
		echo "\n\t<h3 style='float:left; width:150px'>"._DOWN_CPANEL_SUB_UTILS."</h3>";
		foreach ($specials as $service) $this->display($service);
		echo "\n<!-- End of remositorycpspecial -->";
		echo "\n</div>";

		echo "\n<div id='remositorycpinfo' style='clear:left; width:640px; padding:2px;'>";
		echo "\n\t<h3 style='float:left; width:150px'>"._DOWN_CPANEL_SUB_INFO."</h3>";
		foreach ($info as $service) $this->display($service);
		echo "\n<!-- End of remositorycpinfo -->";
		echo "\n</div>";

	}
	
	// Not currently used - may be needed to support multiple repositories
	private function listRepositories ($repositories, $repnum) {
		$body = '';
		foreach ($repositories as $repository) {
			$isactive = $repnum == $repository->id ? 'YES' : 'NO';
			// Change for multiple repositories
			//			<a href="$this->adminsite/index.php?option=com_remository&amp;repnum=$repository->id&amp;act=cpanel">
			$body .= <<<ONE_REPOSITORY
		
				<tr>
					<td>
						$isactive
					</td>
					<td>
						<a href="$this->adminsite/index.php?option=com_remository&amp;act=cpanel">
						$repository->name
					</td>
					<td>
						$repository->alias
					</td>
				</tr>
		
ONE_REPOSITORY;

		}
		$isactive = _DOWN_IS_ACTIVE;
		$name = _DOWN_REPOSITORY_NAME;
		$alias = _DOWN_REPOSITORY_ALIAS;
		$makenew = _DOWN_NEW_REPOSITORY;
		// Change for multiple repositories
		//				<a href="$this->adminsite/index2.php?option=com_remository&amp;repnum=$this->repnum&amp;act=cpanel&amp;task=newrep">
		echo <<<REPOSITORY_LIST
		
		
		<table>
			<thead>
				<tr>
					<th>
						$isactive
					</th>
					<th>
						$name
					</th>
					<th>
						$alias
					</th>
				</tr>
			</thead>
			<tbody>
				$body
				<tr>
					<td colspan="3">
						<a href="$this->adminsite/{$this->interface->indexFileName()}?option=com_remository&amp;act=cpanel&amp;task=newrep">
							$makenew
						</a>
					</td>
				</tr>
			</tbody>
		</table>
					
REPOSITORY_LIST;

	}
}
