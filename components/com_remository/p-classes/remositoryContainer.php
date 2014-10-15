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

class InvalidFilenameException extends Exception { }

class remositoryContainer extends remositoryAbstract {
	public static $actions = array('upload', 'download', 'edit', 'selfApprove');

	/** @var int ID for container record in database */
	public $id=0;
	/** @var int ID of parent container in database if a folder */
	public $parentid=0;
	/** @var string Name of container */
	public $name='';
	/** @var string Alias for container name - used in SEF */
	public $alias='';
	/** @var string Path for storing files */
	public $filepath='';
	/** @var string Container description */
	public $description='';
	/** @var bool Is the container published? */
	public $published=false;
	/** @var int Count of contained folders */
	public $foldercount=0;
	/** @var int Files in the container count */
	public $filecount=0;
	/** @var string Icon - not sure how this is used */
	public $icon='';
	/** @var Visitor options 1=upload, 2=download, 3=both, 0=neither */
	public $registered='2';
	/** @var User options 1=upload, 2=download, 3=both, 0=neither */
	public $userupload='3';
	/** @var bool Count downloads using a subscription manager if present */
	public $countdown=0;
	/** @var bool Descendants included in this container download count via subscription manager */
	public $childcountdown=0;
	/** @var bool Count uploads using a subscription manager if present */
	public $countup=0;
	/** @var bool Descendants included in this container upload count via subscription manager */
	public $childcountup=0;
	/** @var bool Is the file to be stored as a text string? */
	public $plaintext=0;
	/** @var int Group of users that has access to this container */
	public $groupid=0;
	/** @var int Editor group of users */
	public $editgroup=0;
	/** @var bool Auto-approve for Admin - Yes or No (Global applies) */
	public $adminauto=0;
	/** @var bool Auto-approve for user - Yes or No (Global applies)*/
	public $userauto=0;
	/** @var int Auto-approve group of users */
	public $autogroup=0;
	/** @var int User ID for personal folder */
	public $userid=0;

	/**
	* File object constructor
	* @param int Container ID from database or null
	*/
	public function __construct ( $id=0 ) {
		$this->id = $id;
		if ($id) {
			$cmanager = remositoryContainerManager::getInstance();
			$category = $cmanager->getContainer($id);
			if ($category) $this->setValues($category);
		}
	}

	public function __clone () {
		$this->id = 0;
		$this->name = $this->alias = $this->description = '';
		$this->foldercount = $this->filecount = 0;
		$this->countdown = $this->childcountdown = $this->countup = $this->childcountup = 0;
	}

	protected function tableName () {
		return '#__downloads_containers';
	}

	function delete ($managePhysicalFolder = false) {
	    if($managePhysicalFolder && is_dir($this->filepath)){
		@rmdir($this->filepath);
	    }
	    $manager = remositoryContainerManager::getInstance();
	    $manager->delete($this->id);
	}

	function deleteAll ($managePhysicalFolder = false) {
	    $folders = $this->getChildren(false);
	    foreach ($folders as $folder) $folder->deleteAll ($managePhysicalFolder);
	    $files = $this->getFiles(true);
	    foreach ($files as $file) $file->deleteFile();
	    $tempfiles = $this->getTempFiles();
	    foreach ($tempfiles as $file) $file->deleteFile();
	    $this->delete($managePhysicalFolder);
	}

	/**
	 * Save the container : if existing, update it ; if not existing, create it
	 * @param type $managePhysicalFolder : if true, the physical folder will be created (if not existing)
	 * throw Exception if the current physical folder can't be created (when $managePhysicalFolder is set to true)
	 */
	function saveValues ($managePhysicalFolder = false) {

	    $interface = remositoryInterface::getInstance();
	    $database = $interface->getDB();
	    $this->forceBools();
	    $new = $this->id ? false : true;
	    if ($new) {
		$sql = $this->insertSQL();
		remositoryRepository::doSQL ($sql);
		$this->id = $database->insertid();
	    }
	    else {
		$sql = $this->updateSQL();
		remositoryRepository::doSQL ($sql);
	    }
	    remositoryContainerManager::getInstance()->storeContainer($this, $new);

	    if($managePhysicalFolder && ! is_dir($this->filepath)) {
		//we (try to) create the folder
		try{
		    if(! mkdir($this->filepath, 0777, true)) {
			throw new Exception(_DOWN_SUBCONTAINER_CREATE_FOLDER_ERR02);
		    }
		}
		catch (Exception $e) {
		    throw new Exception(_DOWN_SUBCONTAINER_CREATE_FOLDER_ERR02);
		}
	    }
	}

	function notSQL () {
		return array ('id', 'actions');
	}


	function setMetaData () {
		$interface = remositoryInterface::getInstance();
		$interface->prependMetaTag('description', strip_tags($this->name));
		if ($this->keywords) $interface->prependMetaTag('keywords', $this->keywords);
		else $interface->prependMetaTag('keywords', $this->name);
	}

	/**
	 *
	 * @param type $name : new container name
	 * @param type $autogeneratePhysicalFolder : if set to true, generate a folder with a name generated from the container name formating
	 * @return type 
	 * throw Exception when the physical folder can't be created ($autogeneratePhysicalFolder is set to true) 
	 */
	function makeCopyAsChild ($name, $autogeneratePhysicalFolder = false) {
	    $copy = clone $this;
	    $copy->parentid = $this->id;
	    $copy->name = $name;
	    $copy->alias = $copy->description = '';
	    $copy->filecount = $copy->foldercount = 0;
	    if($autogeneratePhysicalFolder) {
		if(! is_dir($this->filepath)){
		    throw new Exception(_DOWN_SUBCONTAINER_CREATE_FOLDER_ERR01);
		}
		$folderName = $this->formatFolderName($name);
		if(strlen($folderName) == 0){
		    throw new InvalidFilenameException(_DOWN_SUBCONTAINER_CREATE_FOLDER_WARN01);
		}
		$copy->filepath = preg_replace('/[\\\\\/]$/', '', $this->filepath) . '/' . $folderName . '/';
		
	    }

	    $copy->saveValues($autogeneratePhysicalFolder);
	    $authoriser = aliroAuthorisationAdmin::getInstance();
	    foreach (self::$actions as $action) {
		$roles = $authoriser->permittedRoles ($action, 'remosFolder', $this->id);
		foreach ($roles as $role) $authoriser->permit ($role, 2, $action, 'remosFolder', $copy->id);
	    }
	    return $copy;
	}

	function isCategory () {
		if ($this->parentid == 0) return true;
		else return false;
	}

	function getCategoryName ($showself=false) {
		$category = $this->getCategory();
		if ($this->parentid OR $showself) return $category->name;
		return '*';
    }

    function getCategory () {
		$container = $this;
		while (is_object($container)) {
			$category = $container;
			$container = $category->getParent();
		}
		return $category;
	}

    function getFamilyNames ($include=false) {
    	$names = '';
    	$parent = $this->getParent();
    	if ($parent AND $parent->parentid) {
    		$names .= '/'.$parent->name;
    		$grandparent = $parent->getParent();
    		if ($grandparent AND $grandparent->parentid) {
    			$names = '/'.$grandparent->name.$names;
				$greatgrandparent = $grandparent->getParent();
				if ($greatgrandparent->parentid) $names = '..'.$names;
			}
    	}
    	if ($include AND $this->id AND $this->parentid) $names = $names.'/'.$this->name;
    	if ($names) return $names;
    	return '-';
    }

	function getAllFamilyNames () {
		$names = $this->name;
		$parent = $this->getParent();
		if ($parent) $names = $parent->getAllFamilyNames().'/'.$names;
		return $names;
	}

	function downloadForbidden (&$user) {
		$authoriser = aliroAuthoriser::getInstance();
		if ($authoriser->checkPermission ('aUser', $user->id, 'download', 'remosFolder', $this->id)
		OR $authoriser->checkPermission ('aUser', $user->id, 'edit', 'remosFolder', $this->id)
		) return false;
		if ($user->isLogged()) {
			echo '<br/>&nbsp;<br/> '._DOWN_MEMBER_ONLY_WARN.$this->name;
			return true;
		}
		echo '<br/>&nbsp;<br/> '._DOWN_REG_ONLY_WARN;
		return true;
	}
	
	function isCounted () {
		if ($this->countdown) return true;
		else {
			$ancestor = $this;
			while ($ancestor = new remositoryContainer($ancestor->parentid)) {
				if ($ancestor->countdown AND $ancestor->childcountdown) return true;
			}
		}
		return false;
	}

	function getChildren ($published=true, $search='') {
		return remositoryContainerManager::getInstance()->getChildren($this->id, $published, $search);
	}
	
	function getSelectedChildren ($limitstart, $limit, $search='') {
		$children = remositoryContainerManager::getInstance()->getChildren($this->id, false, $search);
		return array_slice($children, $limitstart, $limit);
	}
	
	function countChildren ($search='') {
		$children = remositoryContainerManager::getInstance()->getChildren($this->id, false, $search);
		return count($children);
	}

	public function descendantSQL ($operation, $actions='') {
		$familylist = remositoryContainerManager::getInstance()->listGivenAndDescendants($this->id);
	    return $familylist ? "$operation #__downloads_containers AS c $actions WHERE c.id IN ($familylist) AND c.id != $this->id" : '';
	}

	function countDescendants ($search='') {
		return remositoryContainerManager::getInstance()->countDescendants($this->id, $search);
	}

	function getDescendants ($limitstart=0, $limit=99999, $search='') {
		return remositoryContainerManager::getInstance()->getDescendants($this->id, $limitstart, $limit, $search);
	}

	function getDescendantIDs ($search='') {
		return remositoryContainerManager::getInstance()->getDescendantIDs($this->id, $search);
	}

	function makeDescendantsInherit () {
	    $fields = $this->inheritableFields();
	    foreach ($fields as $field) {
	    	$value = $this->$field;
	        $update[] = "c.$field='$value'";
		}
		$setter = 'SET '.implode(', ',$update);
	    $sql = $this->descendantSQL('UPDATE', $setter);
	    if ($sql) remositoryRepository::doSQL($sql);
	}

	function inheritableFields () {
		return array ('filepath');
	}

	function memoContainer ($container) {
	    $fields = $this->inheritableFields();
	    foreach ($fields as $field) {
	        $this->$field = $container->$field;
	    }
	 }

	function isDownloadable (&$user) {
		if ($user->isAdmin()) return true;
		$authoriser = aliroAuthoriser::getInstance();
		return ($authoriser->checkPermission ('aUser', $user->id, 'download', 'remosFolder', $this->id)
		OR $authoriser->checkPermission ('aUser', $user->id, 'edit', 'remosFolder', $this->id)
		);
	}

	function getVisibleChildren ($user) {
		$manager = remositoryContainerManager::getInstance();
		return $manager->getVisibleChildren ($this->id, $user);
	}

	function checkFilePath () {
		if ($this->plaintext) $this->filepath = '';
		else {
			$this->filepath=trim(str_replace("\\","/",$this->filepath));
			if (!$this->filepath) {
				$repository = remositoryRepository::getInstance();
				if (!$repository->Use_Database) {
					if ($parent = $this->getParent() AND $parent->filepath) $this->filepath = $parent->filepath;
					else $this->filepath = $repository->Down_Path;
				}
			}
			if ($this->filepath) {
				$dir = new remositoryDirectory($this->filepath, true);
				$this->filepath = $dir->getPath();
			}
		}
	}

	public function getParent () {
		return remositoryContainerManager::getInstance()->getParent($this->parentid);
	}
	
	function increment ($by='0') {
		$parent = $this->getParent();
		if ($parent != null) $parent->increment($by);
		$this->filecount = $this->filecount+$by;
		$sql="UPDATE #__downloads_containers SET filecount=$this->filecount WHERE id=$this->id";
		remositoryRepository::doSQL($sql);
	}

	function areFilesVisible (&$user) {
		$repository = remositoryRepository::getInstance();
		if ($repository->See_Files_no_download OR $user->isAdmin()) return true;
		return $this->isDownloadable($user);
	}

	function getFiles ($published, $orderby=_REM_DEFAULT_ORDERING, $search='', $limitstart=0, $limit=0, $descendants=false) {
		$sql = remositoryFile::getFilesSQL($published, false, $this->id, $descendants, $orderby, $search, $limitstart, $limit);
		return remositoryRepository::doSQLget($sql, 'remositoryFile');
	}
	
	function getFileCount ($published, $orderby=_REM_DEFAULT_ORDERING, $search='', $descendants=false) {
		$sql = remositoryFile::getFilesSQL($published, true, $this->id, $descendants, $orderby, $search);
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$database->setQuery($sql);
		return $database->loadResult();
	}
	
	function getFeaturedFiles ($published) {
		return remositoryFile::getFeaturedFiles($published, $this->id);
	}

	function getFilesCount ($search='', $remUser, $descendants=false) {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		if ($remUser->isAdmin()) $published = false;
		else $published = true;
		$sql = remositoryFile::getFilesSQL($published, true, $this->id, $descendants, 2, $search);
		$database->setQuery( $sql );
		return $database->loadResult();
	}

	function setFileCount ($chain=null) {
		$newfilecount = 0;
		$newfoldercount = 0;
		$children = $this->getChildren(false);
		foreach ($children as $child) {
			$counts = $child->setFileCount($chain);
			$newfilecount = $newfilecount + $counts[0];
			$newfoldercount = $newfoldercount + $counts[1];
		}
		$newfilecount = $newfilecount + remositoryContainerManager::getInstance()->getFileCount($this->id);
		$newfoldercount = $newfoldercount + count($children);
		if ($newfilecount != $this->filecount OR $newfoldercount != $this->foldercount) {
			$this->filecount = $newfilecount;
			$this->foldercount = $newfoldercount;
			$sql="UPDATE #__downloads_containers SET filecount=$this->filecount, foldercount=$this->foldercount WHERE id=$this->id";
			remositoryRepository::doSQL($sql);
		}
		return array($this->filecount,$this->foldercount);
	}

	function getTempFiles ($search='') {
		$interface = remositoryInterface::getInstance();
		if ($this->id == 0) return array();
		// Change for multiple repositories
		// $repnum = max(1,remositoryRepository::getParam($_REQUEST, 'repnum', 1));
		// $sql = "SELECT * FROM #__downloads_files WHERE repnum = $repnum AND containerid = $this->id AND metatype > 0";
		$sql = "SELECT * FROM #__downloads_files WHERE containerid = $this->id AND metatype > 0";
		if ($search) {
			$search = $interface->getEscaped($search);
			$sql .= " AND LOWER(filetitle) LIKE '%$search%'";
		}
		$results = remositoryRepository::doSQLget($sql,'remositoryTempFile');
		foreach ($results as $key=>$result) $results[$key]->containerid = -$result->containerid;
		return $results;
	}

	// This is used admin side, and wants all containers, whether they can accept files or not
	function getSelectList ($type, $parm, &$user, $notThis=0) {
		$repository = remositoryRepository::getInstance();
		if ($this->id){ 
			$selector[] = $repository->makeOption(0,_DOWN_NO_PARENT);
		}
		else $selector = array();
		$this->addSelectList('',$selector,$notThis,$user);
		return $repository->selectList( $selector, $type, $parm, $this->id );
	}

	// This is used on user side for uploads, only want containers that can accept files
	function getPartialSelectList ($type, $parm, &$user, $notThis=0) {
		$repository = remositoryRepository::getInstance();
		$selector = array();
		$this->addSelectList('', $selector, $notThis, $user, true);
		return (count($selector) ? $repository->selectList( $selector, $type, $parm, $this->id ) : '');
	}

// $category->addSelectList('', $selector, null, $this->remUser, true, 'download');

	function addSelectList ($prefix, &$selector, $notThis, &$user, $usable=false, $action='upload') {
		if ($notThis AND $this->id == $notThis) return;
		
		$repository = remositoryRepository::getInstance();
		if ($user->isAdmin()) {
			$published = false;
			$addthis = true;
		}
		else {
			$published = true;
			$authoriser = aliroAuthoriser::getInstance();
			if ($authoriser->checkPermission ('aUser', $user->id, $action, 'remosFolder', $this->id)
			OR $authoriser->checkPermission ('aUser', $user->id, 'edit', 'remosFolder', $this->id)
			) $addthis = true;
			else $addthis = false;
		}
		if ($usable AND $this->filepath AND (!file_exists($this->filepath) OR !is_writeable($this->filepath))) $addthis = false;
		if ($addthis AND ((!$notThis) OR ($this->id != $notThis))) {
			$name = $this->id ? $this->name : _DOWN_NO_PARENT;
			$selector[] = $repository->makeOption($this->id, $prefix.htmlspecialchars($name));
		}
		foreach ($this->getChildren($published) as $container) $container->addSelectList($prefix.$this->name.'/',$selector,$notThis,$user, $usable, $action);
	}

	function getURL () {
		$func = remositoryRepository::getParam ($_REQUEST, 'func');
		$type = 'direct' == substr($func,0,6) ? 'directlist' : 'select';
		return remositoryRepository::getInstance()->remositoryFunctionURL($type, $this->id);
	}
	
	private function isLoneCategory () {
		if (0 == $this->parentid) {
			$manager = remositoryContainerManager::getInstance();
			return 1 == count($manager->getCategories()) ? true : false;
		}
		return false;
	}

	public function showPathway () {
		$interface = remositoryInterface::getInstance();
		$parent = $this->getParent();
		$html = ($parent != null) ? $parent->showPathway() : '';
		if ($this->isLoneCategory()) return $html;
		$name = htmlspecialchars($this->name);
		$imagelink = remositoryRepository::getInstance()->remositoryImageURL('arrow.png',9,9);
		$html .= <<<PATH_WAY

		$imagelink
		{$this->getURL()}
			$name
		</a>

PATH_WAY;

		return $html;
	}

	// Alternative to use the CMS pathway instead of a separate Remository one
	public function showCMSPathway () {
		$parent = $this->getParent();
		if (!is_null($parent)) $parent->showCMSPathway();
		if ($this->isLoneCategory()) return;
		$interface = remositoryInterface::getInstance();
		$link = remositoryRepository::getInstance()->RemositoryRawFunctionURL('select', $this->id);
		$interface->appendPathWay(htmlspecialchars($this->name), $link);
	}

	public static function getIcons () {
		return remositoryRepository::getIcons ('folder_icons');
	}

	public static function togglePublished ($idlist, $value) {
		$cids = implode( ',', $idlist );
		$sql = "UPDATE #__downloads_containers SET published=$value". "\nWHERE id IN ($cids)";
		remositoryRepository::doSQL ($sql);
	}
	
	
	/************ ADDED BY GEBUS ************/
	public function  updatePermitted ($user) {
		return $this->checkUpOrDel ($user, 'Allow_User_Edit');
	}
        
	public function  createPermitted ($user) {
		return $this->checkUpOrDel ($user, 'Allow_User_Edit');
	}
	
	public function  deletePermitted ($user) {
		return $this->checkUpOrDel ($user, 'Allow_User_Edit');
	}

	// Think this may not work - appears never used - Martin
	public function  deleteFilesPermitted ($user) {
		return $this->checkUpOrDel ($user, 'Allow_User_Delete');
	}
	
	// Not sure this is going to give correct answer, call removed from remository.html.php - Martin
	public function  submitFilesPermitted ($user) {
		return $this->checkUpOrDel ($user, 'Allow_User_Sub');
	}

	public function  checkUpOrDel ($user, $config) {
		if ($user->isAdmin()) return true;
		$repository = remositoryRepository::getInstance();
		if (!$repository->$config OR !$this->id) return false;
		$authoriser = aliroAuthoriser::getInstance();
		return $authoriser->checkPermission ('aUser', $user->id, 'edit', 'remosFolder', $this->id);
	}
	
    
    /*function normalize ($string) {
        $table = array(
            'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
            'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
            'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
            'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
            'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
        );

        return strtr($string, $table);
    }*/
    
    /** 
     * Format a string to a filename : lower case, special stressed letters converted, special char removed, space(s) replaced by one score
     * @param type $string : string to be formatted to filename
     * @return type 
     */
    function formatFolderName($string){
        $string = preg_replace("/([ ]+)/", "-", 
                    preg_replace("/([\\\\\/:*?“<>|[]&$,]+)/", "", 
                            $string
                    )
                  );
        
        return $string;
    }

}