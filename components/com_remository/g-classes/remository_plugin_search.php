<?php
/**
* Remository Searchbot - Extend the standard Mambo search function to include Remository files
* @version 3.50
* @package Remository Searchbot
* @copyright (C) 2009 Black Sheep Research
* @license GNU GPL version 2
**/

class remositorySearchPluginHTML extends remositoryCustomUserHTML {
	
	public function showSearchContainerLinks ($container) {
		return $this->showContainerLinks ($container);
	}
}


class remository_plugin_search extends remositoryAddOnController {	
	
	// The real search method - meant to be accessed only by the classes above
	// Parameters are not needed - coded only as a model for other plugins
	public function onSearch ($botparams, $text, $matchtype='', $order='') {

	$interface = remositoryInterface::getInstance();
	$database = $interface->getDB();
	if (!trim($text)) return array();
	// error_reporting(E_ALL);

	switch ($matchtype) {
		case 'exact':
		    $text = '"'.$text.'"';
		    $isbool = 'IN BOOLEAN MODE';
		    break;
		case 'all':
		    $words = explode(' ',$text);
		    foreach ($words as $i=>$value) $words[$i] = '+'.$value;
		    $text = implode(' ',$words);
		    $isbool = 'IN BOOLEAN MODE';
		    break;
		case 'any':
		default:
		    $isbool = '';
	}

	if ($isbool) {
		$database->setQuery('SELECT VERSION()');
		if (substr($database->loadResult(),0,1) < 4) $isbool = '';
	}

    $section = "\n 'File Repository' AS section,";
	switch ($order) {
		case 'popular':
		    $seq = 'f.downloads DESC';
		    break;
		case 'category':
		    $seq = 'c.name, f.filetitle ASC';
		    $section = "\n c.name AS section,";
		    break;
		case 'oldest':
			$seq = 'f.submitdate ASC';
			break;
		case 'newest':
			$seq = 'f.submitdate DESC';
			break;
		case 'alpha':
		default:
		    $seq = 'f.filetitle ASC';
	}
	$seq = ' ORDER BY '.$seq;

	$remUser = $interface->getUser();
	$repository = remositoryRepository::getInstance();
	$Itemid = $repository->getItemid();
	$visibility = remositoryAbstract::visibilitySQL($remUser);
	# Perform database query and return result list
	$where[] = "(MATCH (t.filetext) AGAINST ('$text' $isbool) OR MATCH (f.filetitle,f.description,f.smalldesc,f.fileauthor) AGAINST ('$text' $isbool))";
	$where[] = 'f.published != 0';
	if ($visibility) $where[] = $visibility;
	$sql = "SELECT DISTINCT f.filetitle,"
	. "\n c.name AS containername, f.containerid, f.fileversion,"
    . "\n f.description AS text,"
    . "\n f.filedate AS created,"
    . $section
    . "\n CONCAT('index.php?option=com_remository&Itemid=$Itemid&func=fileinfo&id=',f.id) AS href,"
    . "\n '2' AS browsernav,"
    . "\n f.id AS resultid"
    . " FROM "
    . "\n #__downloads_containers AS c INNER JOIN #__downloads_files AS f ON f.containerid = c.id"
    . "\n LEFT JOIN #__downloads_text AS t ON t.fileid=f.id";
	if (isset($where)) $sql .= ' WHERE '.implode(' AND ', $where);
	$sql .= $seq;
	$database->setQuery($sql);
	$results = $database->loadObjectList();
	$ids = array();
	if ($results) {
		if ($botparams->get('show_allcats', 0)) $view = new remositorySearchPluginHTML($this);
		foreach ($results as $key=>$hit) {
			if (in_array($hit->resultid,$ids)) unset($results[$key]);
			else {
				$fileversion = $hit->fileversion ? " ({$hit->fileversion})" : '';
				if ($botparams->get('show_allcats', 0)) {
					$container = new remositoryContainer($hit->containerid);
					$containerlist = $view->showSearchContainerLinks($container);
					if (defined('_JOOMLA_15PLUS')) $containerlist = strip_tags($containerlist);
					$results[$key]->title = $hit->filetitle.$fileversion.' : '.$containerlist;
				}
				else $results[$key]->title = $hit->filetitle.$fileversion.$results[$key]->containername;
				unset($results[$key]->containername, $results[$key]->containerid, $results[$key]->fileversion);
				$ids[] = $hit->resultid;
			}
		}
	}
	else $results = array();
	// error_reporting(E_ALL|E_STRICT);
	return $results;
}

}

if (defined('_ALIRO_IS_PRESENT')) {
	class bot_remositorySearch extends aliroPlugin {

		public function onSearch ($searchword, $matchtype, $order) {
		 	$worker = new remository_plugin_search();
		 	return $worker->onSearch ($this->params, $searchword, $matchtype, $order);
		}

	}
}
