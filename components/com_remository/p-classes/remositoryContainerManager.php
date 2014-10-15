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

/**
* Sorts an Array of containers
*/

class remositoryContainerSorter {
    private $_object_array = array();

    public function __construct (&$a) {
        $this->_object_array =& $a;
        $this->sort();
    }

    // This is not genuinely public, but has to be declared so for the callback
    public function containerCompare (&$a, &$b) {
        if ($a->sequence > $b->sequence) return 1;
        if ($a->sequence < $b->sequence) return -1;
        if ($a->name > $b->name) return 1;
        if ($a->name < $b->name) return -1;
        return 0;
    }

    private function sort () {
        usort($this->_object_array, array($this,'containerCompare'));
    }

}

class remositoryContainerManager {
	private static $instance = null;
	private $total = 0;
	private $remository_links = array();
	private $remository_containers = array();
	private $familytree = array();
	private $filecounts = array();
	private $personal_folders = array();

	private function __construct () {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();
		$database->setQuery("SELECT COUNT(*) FROM #__downloads_containers");
		$this->total = $database->loadResult();
		$starter = 0;
		
		$sql = 'SELECT * FROM #__downloads_containers';
		while ($starter < $this->total) {
			$rows = remositoryRepository::doSQLget($sql." LIMIT $starter, 1000", 'remositoryContainer');
			foreach (array_keys($rows) as $i) $this->storeContainer($rows[$i]);
			$starter += 1000;
		}
	}

	public function storeContainer ($container, $new=false) {
		$this->remository_containers[$container->id] =& $container;
		$this->familytree[$container->parentid][$container->id] = 1;
		if ($container->userid) $this->personal_folders[$container->userid] = $container->id;
		if ($new) $this->count++;
	}

    public static function getInstance () {
    	return is_object(self::$instance) ? self::$instance : self::$instance = new self();
    }

    public function count () {
        return $this->total;
    }
    
    public function getAll () {
    	return $this->remository_containers;
	}

    public function getFromIDs ($ids) {
    	return array_intersect_key($this->remository_containers, array_flip($ids));
    }

	public function getFileCount ($id) {
		if (0 == count($this->filecounts)) {
			$interface = remositoryInterface::getInstance();
			$database = $interface->getDB();
			$database->setQuery("SELECT containerid, COUNT(*) AS filecount FROM #__downloads_files"
				.' WHERE containerid > 0 AND published != 0 AND NOW() >= publish_from AND (publish_to >= NOW() OR publish_to="0000-00-00 00:00:00")'
				. 'GROUP BY containerid');
			$results = $database->loadObjectList();
			if ($results) foreach ($results as $result) $this->filecounts[$result->containerid] = $result->filecount;
		}
		return empty($this->filecounts[$id]) ? 0 : $this->filecounts[$id];
	}

	public function getPersonalFolder ($user) {
		return isset($this->personal_folders[$user->id]) ? $this->remository_containers[$this->personal_folders[$user->id]] : null;
	}

	public function createPersonalFolder ($user, $name, $description, $usedb, $basepath, $parentid) {
		$folder = $this->getPersonalFolder($user);
		if (!$folder AND $user->isLogged()) {
			$folder = new remositoryContainer();
			$folder->name = sprintf($name, $user->fullname);
			$folder->description = sprintf($description, $user->fullname);
			$codename = 'PersonalFolderForID'.$user->name;
			$folder->alias = $codename;
			if ($usedb OR !is_dir($basepath)) $folder->filepath = '';
			else {
				if ('/' != substr($basepath,-1)) $basepath .= '/';
				$folder->filepath = $basepath.$codename;
			}
			$folder->userid = $user->id;
			$folder->parentid = $parentid;
			$folder->published = 1;
			$folder->saveValues();
			$this->remository_containers[$folder->id] =& $folder;
			$this->personal_folders[$user->id] = $folder->id;
			$this->familytree[$parentid][$folder->id] = 1;
		}
		return $folder;
	}

	public function getChildren ($id, $published=true, $search='') {
		if (isset($this->familytree[$id])) foreach (array_keys($this->familytree[$id]) as $key) {
			$container =& $this->remository_containers[$key];
			if (($published AND $container->published == 0) OR ($search AND strpos($container->name, $search) === false)) continue;
			$children[] =& $container;
		}
		if (isset($children)) {
			new remositoryContainerSorter($children);
			return $children;
		}
		else return array();
	}

	public function getVisibleChildren ($id, &$user) {
		$repository = remositoryRepository::getInstance();
		if (isset($this->familytree[$id])) foreach (array_keys($this->familytree[$id]) as $key) {
			if ($user->isAdmin()) $children [] =& $this->remository_containers[$key];
			else {
				$container =& $this->remository_containers[$key];
				if (0 != $container->published AND ($repository->See_Containers_no_download OR $user->canDownloadContainer($container->id))) {
					$children[] =& $container;
				}
			}
		}
		if (isset($children)) {
			new remositoryContainerSorter($children);
			return $children;
		}
		else return array();
	}
	
	public function getVisibleDescendants ($id, $user) {
		$children = $this->getVisibleChildren($id, $user);
		foreach ($children as $child) {
			$grandfamily = $this->getVisibleDescendants($child->id, $user);
			$descendants = empty($descendants) ? $grandfamily : (empty($grandfamily) ? $descendants : array_merge($grandfamily, $descendants));
		}
		return empty($descendants) ? $children : array_merge($children, $descendants);
	}
	
	public function getDescendantIDs ($id, $search='') {
		$children = isset($this->familytree[$id]) ? array_keys($this->familytree[$id]) : array();
		foreach ($children as $child) {
			if (!$search OR false !== strpos($this->remository_containers[$child]->name, $search)) $descendants[] = $child;
			$grandfamily = $this->getDescendantIDs($child, $search);
			if (count($grandfamily)) $descendants = empty($descendants) ? $grandfamily : array_merge($descendants, $grandfamily);
		}
		return isset($descendants) ? $descendants : array();
	}
	
	public function countDescendants ($id, $search='') {
		return count($this->getDescendantIDs($id, $search));
	}
	
	public function getDescendants ($id, $limitstart, $limit, $search='') {
		$ids = $this->getDescendantIDs($id, $search);
		$all = $this->getFromIDs($ids);
		new remositoryContainerSorter($all);
		return array_slice($all, $limitstart, $limit);
	}

	public function listGivenAndDescendants ($id) {
		$itemids = $this->getDescendantIDs($id);
		if (isset($this->remository_containers[$id])) array_push($itemids,$id);
		return count($itemids) ? implode(',', $itemids) : '';
	}

	public function getContainer ($id) {
		if (isset($this->remository_containers[$id])) $container =& $this->remository_containers[$id];
		else {
			$container = new remositoryContainer();
			$container->name = _INVALID_ID;
		}
		return $container;
	}

	public function getParent ($parentid) {
		if (isset($this->remository_containers[$parentid])) $parent =& $this->remository_containers[$parentid];
		else $parent = null;
		return $parent;
	}
	
	public function getFullPath ($id) {
		$container = $this->getContainer($id);
		return $container->parentid ? $this->getFullPath($container->parentid).' / '.$container->name : $container->name;
	}

	public function delete ($id) {
		$sql = "DELETE FROM #__downloads_containers WHERE id=$id";
		remositoryRepository::doSQL($sql);
		if (isset($this->remository_containers[$id])) {
			$container =& $this->remository_containers[$id];
			if (isset($this->familytree[$container->parentid][$id])) unset($this->familytree[$container->parentid][$id]);
			unset($container, $this->remository_containers[$id]);
		}
		aliroAuthorisationAdmin::getInstance()->dropAllPermissions('remosFolder', $id);
	}

	public function makeSelectedList ($containers, $type, $parm) {
		$repository = remositoryRepository::getInstance();
		$ids = explode(',', $containers);
		foreach ($this->getByIDs($ids) as $container) $selector[] = $repository->makeOption($container->id, $container->name);
		return isset($selector) ? $repository->selectList ($selector, $type, $parm) : '';
	}

	public function getFilePathData ($path='') {
		$database = remositoryInterface::getInstance()->getDB();
		$database->setQuery("UPDATE `#__downloads_containers` SET filepath = CONCAT(filepath,'/') WHERE filepath != '' AND RIGHT(filepath, 1) != '/'");
		$database->query();
		$defaultdown = remositoryRepository::getInstance()->Down_Path.'/';
		foreach ($this->remository_containers as $container) {
			if ($path == '' OR ($path AND ($container->filepath == $path OR $container->filepath == ''))) {
				if ($container->filepath) $results[$container->filepath][] = $container->id;
				else $results[$defaultdown][] = $container->id;
			}
		}
		return empty($results) ? array() : $results;
	}
	
	public function getFolders ($search='') {
		foreach ($this->remository_containers as $key=>$container) {
			if ($search AND strpos(strtolower($container->name), $search) === false) continue;
			$folders[] =& $this->remository_containers[$key];
		}
		if (isset($folders)) {
			new remositoryContainerSorter($folders);
			return $folders;
		}
		else return array();
	}

	public function getCategories ($published=false, $search='') {
		return $this->getChildren(0, $published, $search);
	}

	public function getVisibleCategories ($user) {
		$categories = $this->getVisibleChildren (0, $user);
		if (1 == count($categories) AND 0 == $categories[0]->getFileCount(false)) {
			$subcategories = $categories[0]->getVisibleChildren($user);
			if (!empty($subcategories)) $categories = $subcategories;
		}
		return $categories;
	}

	public function getCategoryIDs ($published=false, $search='') {
		$categories = $this->getCategories($published, $search);
		foreach ($categories as $category) $results[] = $category->id;
		return isset($results) ? $results : array();
	}

	public function getTreeAdds ($user, $treename='remostree', $addfiles=false, $root=0, $rootName) {
		$repository = remositoryRepository::getInstance();
		$home = $repository->RemositoryBasicFunctionURL();
		if(isset($rootName) && trim($rootName) != ""){
			$maintitle = $rootName;
		}
		else{
			$maintitle = $root ? $this->getContainer($root)->name : $repository->Main_Page_Title;
		}
		$adds = <<<TREE_ROOT
		
		$treename.add($root, -1, '$maintitle', '$home');
		
TREE_ROOT;

		$max = 0;
		$descendants = $this->getVisibleDescendants($root, $user);
		foreach ($descendants as $container) {
			$max = max($max, $container->id);
			$parent = $container->parentid ? $container->parentid : 0;
			$link = $repository->RemositoryBasicFunctionURL('select', $container->id);
			$name = preg_replace("/\r?\n/", "\\n", addslashes($container->name));
			$adds .= <<<ADD_ONE

		$treename.add($container->id, $parent, '$name', '$link');

ADD_ONE;

		}
		
		if ($addfiles AND !empty($descendants)) {
			foreach ($descendants as $descendant) $dids[] = $descendant->id;
			$didlist = implode(',', $dids);
			$interface = remositoryInterface::getInstance();
			$database = $interface->getDB();
			// Change for multiple repositories
			// $repnum = max(1, remositoryRepository::getParam($_REQUEST, 'repnum', 1));
			// $database->setQuery("SELECT id, filetitle, containerid FROM #__downloads_files WHERE repnum = $repnum");
			$database->setQuery("SELECT id, filetitle, containerid, filetype FROM #__downloads_files WHERE containerid IN ($didlist)");
			$files = $database->loadObjectList();
			if ($files) foreach ($files as $file) {
				$max++;
				$link = $repository->RemositoryBasicFunctionURL('fileinfo', $file->id);
				$filename = preg_replace("/\r?\n/", "\\n", addslashes($file->filetitle));
				$adds .= <<<ADD_FILE
				
			$treename.addFile($max, $file->containerid, '$filename', '$link', '$file->filetype');
			
ADD_FILE;

			}

		}
		return $adds;
	}

}
