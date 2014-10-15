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

class remositoryFileListHTML extends remositoryCustomUserHTML {
	private $tabcnt=0;

	protected function fileListHeading ($orderby, $idparm, $func) {
		$downfiles = _DOWN_FILES;
		$downorder = _DOWN_ORDER_BY;
		$formurl = remositoryRepository::getInstance()->RemositoryBasicFunctionURL('search');
		$ordername = array ('zero', _DOWN_ID, _DOWN_FILE_TITLE_SORT, _DOWN_DOWNLOADS_SORT, _DOWN_SUB_DATE_SORT, _DOWN_SUB_ID_SORT, _DOWN_AUTHOR_ABOUT, _DOWN_RATING_TITLE);
		for ($by = 1, $n=count($ordername); $by < $n; $by++) {
			if ($orderby<>$by) $option[] = <<<SORT_OPTION

			<a href="{$this->repository->RemositoryBasicFunctionURL($func,$idparm,null,$by)}">{$ordername[$by]}</a>
SORT_OPTION;

			else $option[] = $ordername[$by];
		}
		$sortlist = implode (' | ', $option);
		return <<<LIST_HEADING

	<div id="remositoryfilelisthead">
		<h3>$downfiles</h3>
		<span id="remositoryorderby"><em>$downorder </em>
			$sortlist
		</span>
	<!-- End of remositoryfilelisthead -->
	</div>

LIST_HEADING;

	}

	protected function displayContainer ($container, $func) {
		
		$name = $this->show($container->name);
		
                $folderPath = $this->repository->RemositoryBasicFunctionURL($func, $container->id);
                
		$icon = $this->showContainerIcon($container, "remositoryFolder");
                $deleteFolderIcon = $this->showDeleteContainerIcon($container, "remositoryDeleteFolder");//added by Gebus : delete container
                $updateFolderIcon = $this->showUpdateContainerIcon($container, "remositoryUpdateFolder");//added by Gebus : update container
		
		return <<<SHOW_CONTAINER

		<tr>
                    <td colspan="2">
			
                        <div>
                            <h3>
                                $updateFolderIcon
                                $deleteFolderIcon
                                <a href="$folderPath">
                                    $icon
                                    $name
                                </a>
                            </h3>
                        </div>
                    </td>
			{$this->displayFolderCounts($container)}
		</tr>
		{$this->displayDescription($container)}
		{$this->displayAllSubcontainers($container, $func)}

SHOW_CONTAINER;

	}

	protected function displayFolderCounts ($container) {
		if (is_object($container) AND $this->repository->Show_File_Folder_Counts) return <<<FOLDER_COUNTS

			<td>
				($container->foldercount/$container->filecount)
			</td>
FOLDER_COUNTS;

	}

	protected function displayDescription ($container) {
		if ($container->description) return <<<A_DESCRIPTION

		<tr class='remositoryfolderinfo'>
			<td>$container->description</td>
		</tr>

A_DESCRIPTION;

	}

	protected function displaySubContainer ($child, $func, $oddness) {
		// May be called with null subcontainer to even up the columns
		if (is_object($child)) {
			$name = ' '.$this->show($child->name);
			$link = $this->repository->RemositoryFunctionURL($func, $child->id).$name.'</a>';
		}
		else $link = '';
		// Organise two column layout, use alternate columns
		if ('odd' == $oddness) return <<<ODD_PART

			<tr>
				<td width="40%" class="indent $oddness">
					$link
				</td>
				{$this->displayFolderCounts($child)}

ODD_PART;

		else return <<<EVEN_PART

				<td width="40%" class="indent $oddness">
					$link
				</td>
				{$this->displayFolderCounts($child)}
			</tr>

EVEN_PART;

	}
	
	protected function displayAllSubContainers ($folder, $func) {
		$html = '';
		// Check configuration option for showing sub containers
		if ($this->repository->Show_SubCategories) {
			$children = $folder->getVisibleChildren($this->remUser);
			$i = 0;
			foreach ($children as $child) {
				// Check whether this is an odd or even item
				$oddness = ($i++ % 2) ? 'even' : 'odd';
				$html .= $this->displaySubContainer($child, $func, $oddness);
			}
			// Make sure we have an even number of calls in total
			if ($i) {
				if ('odd' == $oddness) $html .= $this->displaySubContainer(null, $func, 'even');
				return <<<ALL_SUBCONTAINERS

		<tr>
			<td>
				<table>
					$html
				</table>
			</td>
		</tr>

ALL_SUBCONTAINERS;

			}
		}
	}

	protected function displayFolderCountHeading () {
		if (is_object($container) AND $this->repository->Show_File_Folder_Counts) return <<<FOLDER_COUNT_HEAD

			<th>
				{$this->show(_DOWN_FOLDERS_FILES)}
			</th>
FOLDER_COUNT_HEAD;

	}

	public function fileListHTML( $id, $container, $folders, $files, $page, $func, $directlink ) {
	    
	    if ($container->id) {
		    $container->setMetaData();
		    $container->showCMSPathway();
		    echo $this->pathwayHTML($container->getParent());
	    }
	    echo $this->mainPageHeading($container->id);

	    if ($container->id) $this->folderListHeading($container);
	    
	    $createContainerIcon = $this->showCreateContainerIcon($container, "remositoryCreateFolder");

	    if ($folders || $createContainerIcon != ""){
		$title = _DOWN_CONTAINERS;
		$ff = _DOWN_FOLDERS_FILES;
		echo $createContainerIcon;
		echo "\n\t<div id='remositorycontainerlist'>";
		echo "\n\t\t<table>";
		echo "\n\t\t<thead><tr>";
		echo "\n\t\t\t<th colspan='2' id='remositorycontainerhead'>$title</th>";
		if ($this->repository->Show_File_Folder_Counts) echo "\n\t\t\t<th>$ff</th>";
		echo "\n\t\t</tr></thead><tbody>";
		foreach ($folders as $folder) {
		    $i = 0;
		    echo $this->displayContainer($folder, $func);
		    $this->tabcnt = ($this->tabcnt+1) % 2;
		}

		echo "\n\t\t</tbody></table>";
		echo "\n\t<!-- End of remositorycontainerlist -->";
		
		echo "\n\t</div>\n";
	    }


	    if ($files){
		$this->tabcnt = 0;
		$downlogo = $this->repository->RemositoryImageURL('download_trans.gif');
		echo $this->fileListHeading($this->orderby, $id, $func);
		$page->showNavigation();
		echo "\n\t<div id='remositoryfilelisting'>";
		foreach ($files as $file) {
			$this->fileListing ($file, $container, $downlogo, $this->remUser, false, 'A', $directlink);
			$this->tabcnt = ($this->tabcnt+1) % 2;
		}
		echo "\n\t<!-- End of remositoryfilelisting -->";
		echo "\n\t</div>\n";
		$page->showNavigation();
		?>
		<script type="text/javascript">
		function download(url){window.location = url}
		</script>
		<?php
	    }
	    $this->filesFooterHTML ();
	    $this->remositoryCredits();
	}

	public function emptyHTML () {
		$mosConfig_sitename = remositoryInterface::getInstance()->getCfg('sitename');
		echo <<<EMPTY_HTML

		<div id="remositoryoverview">
			<h2>{$this->show(_DOWN_EMPTY_REPOSITORY)}</h2>
			<p>
				{$this->showHTML(_DOWN_NO_CATS)}
			</p>
		</div>

EMPTY_HTML;

	}
        
	//added by Gebus : create container
        protected function showContainerIcon ($container, $cssClass){
	    $icon = $container->icon ? $this->repository->RemositoryImageURL('folder_icons/'.$container->icon) : $this->repository->RemositoryImageURL('folder_icons/folder_yellow.gif');
	    $mgtContent = '<span class="' . $cssClass . '">' . $icon . '</span>';
            return $mgtContent;
        }
	
	protected function showCreateContainerIcon ($container, $cssClass){
		$mgtContent = "";
		if($this->repository->Allow_Container_Add AND $container->createPermitted($this->remUser) ){
			$iconsize = _REMOSITORY_ICON_SIZE;
			$icon = $this->repository->RemositoryImageURL('management/folder_new.png', $iconsize, $iconsize, _DOWN_SUBCONTAINER_CREATE_LABEL);
			$startlink = $this->repository->RemositoryFunctionURL('createcontainer', $container->id);
			$mgtContent = '<div class="' . $cssClass . '">' . $startlink . $icon . _DOWN_SUBCONTAINER_CREATE_LABEL . '</a></div>';
		}  
		return $mgtContent;
	}
        

}
