<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2008 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

class remositoryOverviewHTML extends remositoryCustomUserHTML {
	
	public function overviewHTML ($names, $files) {
		foreach ($names as $sub=>$name) $names[$sub] = $this->show($name);
		$tabobject = new aliroTabs('latest', false, false);
		$html = '';
		foreach ($files as $fileset) {
			$start_tab = $tabobject->startTab();
			$end_tab = $tabobject->endTab();
			$html .= <<<TAB_DATA
				
			$start_tab
				{$this->fileList($fileset)}
			$end_tab
				
TAB_DATA;

		}
		echo <<<OVERVIEW
		
		<div id="remositoryoverview">
			<h2>{$this->show(_DOWN_OVERVIEW_LATEST)}</h2>
			{$tabobject->startPane($names)}
			$html
			{$tabobject->endPane()}
		</div>
		
OVERVIEW;

	}
	
	protected function fileList ($fileset) {
		$html = '';
		foreach ($fileset as $file) {
			$link = $this->repository->RemositoryBasicFunctionURL('startdown', $file->id);
			$html .= <<<ONE_FILE

		
				<p>
					<strong>{$this->show($file->filetitle)}</strong>
					{$this->show($file->smalldesc)}
					<a href="$link"> {$this->show(_DOWN_DOWNLOAD_LC)} &raquo;</a>
				</p>
				
ONE_FILE;

		}
		return $html;
	}
	
	public function emptyHTML () {
		echo <<<EMPTY_HTML
		
		<div id="remositoryoverview">
			<h2>{$this->show(_DOWN_EMPTY_REPOSITORY)}</h2>
			<p>
				{$this->show(_DOWN_NO_CATS)}
			</p>
		</div>
		
EMPTY_HTML;

	}
}
