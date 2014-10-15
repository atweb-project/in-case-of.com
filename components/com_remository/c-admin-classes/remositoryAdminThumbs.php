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

class remositoryAdminThumbs extends remositoryAdminControllers {

	function __construct ($admin) {
		parent::__construct ($admin);
	    $_REQUEST['act'] = 'thumbs';
	}

	function listTask (){
		$view = new remositoryAdminHTML ($this, 0, '');
		$view->formStart(_DOWN_ADMIN_ACT_THUMBS);
		echo <<<UNDER_HEADING

		<tr>
			<td>
				<div class="remositoryblock">&nbsp;</div>
			</td>
		</tr>

UNDER_HEADING;

		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$noproblem = true;
		$dirpath = $interface->getCfg('absolute_path').remositoryThumbnails::baseFilePath();
		$pattern = remositoryThumbnails::dirPattern();
		$dir = new remositoryDirectory ($dirpath);
		$directories = $dir->listFiles($pattern,'dir');
		foreach ($directories as $directory) {
			$filenum = substr($directory,strlen($pattern));
			$fileint = intval($filenum);
			$sql = "SELECT id FROM #__downloads_files WHERE id=$fileint";
			$database->setQuery($sql);
			$dbresult = $database->loadResult();
			$filedir = new remositoryDirectory ($dirpath.$directory);
			$files = $filedir->listFiles('th_'.$filenum);
			$allfiles = $filedir->listFiles();
			if ($dbresult AND count($files) == 0) {
				foreach ($allfiles as $delfile) @unlink($dirpath.$directory.'/'.$delfile);
				@rmdir($dirpath.$directory);
				echo '<tr><td>'.$dirpath.$directory._DOWN_NO_RELEVANT_THUMB.'</td></tr>';
				$noproblem = false;
			}
			else {
				foreach ($files as $file) {
					$imgfiles[] = str_replace('th_','img_',$file);
					$txtfiles[] = str_replace('th_','txt_',$file);
				}
				foreach ($allfiles as $file) {
					if (in_array($file,$files) OR in_array($file,$imgfiles) OR ($this->repository->Allow_Legend AND in_array($file,$txtfiles))) continue;
					echo '<tr><td>'.sprintf(_DOWN_THUMB_NOT_BELONG, $file, $dirpath.$directory).'</td></tr>';
					@unlink($dirpath.$directory.'/'.$file);
					$noproblem = false;
				}
			}
			if (!$dbresult) {
				foreach ($allfiles as $delfile) @unlink($dirpath.$directory.'/'.$delfile);
				@rmdir($dirpath.$directory);
				echo '<tr><td>'.$dirpath.$directory._DOWN_THUMB_NOT_IN_DB.'</td></tr>';
				$noproblem = false;
			}
		}
		if ($noproblem) echo '<tr><td class="message">'._DOWN_THUMB_OK.'</td></tr>';
		$view->simpleFormEnd();
	}

}