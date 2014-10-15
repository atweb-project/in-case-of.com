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

class remositoryMenuMaker {
	protected $cidname = 'componentid';
	protected $remonum = 0;
	protected $database = null;
	
	public function __construct () {
		$this->cidname = defined('_JOOMLA_16PLUS') ? 'component_id' : 'componentid';
		$this->database = remositoryInterface::getInstance()->getDB();
		$this->remonum = $this->getComponentID();
	}

	public function makeMenuEntry () {
		if ($this->getMenuCount()) {
			$this->database->setQuery("UPDATE #__menu SET $this->cidname = $this->remonum WHERE link LIKE 'index.php?option=com_remository%'");
			$this->database->query();
			return;
		}
		if (defined('_ALIRO_IS_PRESENT')) $this->makeAliroMenuEntry();
		elseif(defined('_JOOMLA_15PLUS')) $this->makeJoomlaMenuEntry();
		else {
			$this->database->setQuery("SELECT MAX(ordering) FROM `#__menu`");
			$ordering = intval($this->database->loadResult() + 1);
			$this->database->setQuery("INSERT INTO `#__menu` "
			." (`id`, `menutype`, `name`, `link`, `type`, `published`, `parent`, `componentid`, `sublevel`, `ordering`, `checked_out`, `checked_out_time`, `pollid`, `browserNav`, `access`, `utaccess`, `params`) "
			." VALUES (NULL , 'mainmenu', 'Remository', 'index.php?option=com_remository', 'components', '1', '0', $this->remonum, '0', $ordering, '0', '0000-00-00 00:00:00', '0', '0', '0', '0', '')");
			$this->database->query();
		}
	}
	
	protected function makeAliroMenuEntry () {
		
	}
	
	protected function makeJoomlaMenuEntry () {
		// Import JTableMenu
		JLoader::register('JTableMenu', JPATH_LIBRARIES . '/joomla/database/table/menu.php');
		$newmenu = new JTableMenu($this->database);
		$newmenu->menutype = 'mainmenu';
		$menuname = defined('_JOOMLA_16PLUS') ? 'title' : 'name';
		$newmenu->$menuname = 'Remository';
		$newmenu->alias = 'repository';
		$newmenu->link = 'index.php?option=com_remository';
		$newmenu->type = 'component';
		$newmenu->published = 1;
		$property = $this->cidname;
		$newmenu->$property = $this->remonum;
		$previous = $this->getMenuOrdering();
		if (defined('_JOOMLA_16PLUS')) {
			$newmenu->parent_id = 1;
			$newmenu->level = 1;
			$newmenu->language = '*';
			$newmenu->component_id = $this->remonum;
			$newmenu->setLocation($previous);
		}
		else {
			$newmenu->componentid = $this->remonum;
			$newmenu->ordering = $previous + 1;
		}
		$newmenu->store();
	}
	
	protected function getComponentID () {
		if (defined('_JOOMLA_16PLUS')) {
			$this->database->setQuery("SELECT MIN(extension_id) FROM `#__extensions` WHERE `element` = 'com_remository'");
		}
		else $this->database->setQuery("SELECT MIN(id) FROM `#__components` WHERE `option` = 'com_remository'");
		return intval($this->database->loadResult());
	}
	
	protected function getMenuCount () {
		$this->database->setQuery("SELECT count(*) FROM `#__menu` WHERE menutype = 'mainmenu' AND link LIKE 'index.php?option=com_remository%'");
		return intval($this->database->loadResult());
	}
	
	protected function getMenuOrdering () {
		if (defined('_JOOMLA_16PLUS')) $this->database->setQuery("SELECT MAX(id) FROM `#__menu` WHERE menutype = 'mainmenu'");
		else $this->database->setQuery("SELECT MAX(ordering) FROM `#__menu` WHERE menutype = 'mainmenu'");
		return intval($this->database->loadResult());
	}
}