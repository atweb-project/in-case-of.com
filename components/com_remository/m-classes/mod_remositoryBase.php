<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006-12 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

abstract class mod_remositoryBase {
	private static $cssdone = false;
	protected $interface = null;
	protected $database = null;
	protected $repository = null;
	protected $live_site = '';
	protected $remUser = null;

	public function __construct () {
		$this->interface = remositoryInterface::getInstance();
		$this->database = $this->interface->getDB();
		$this->repository = remositoryRepository::getInstance();
		$this->live_site = $this->interface->getCfg('live_site');
		$this->remUser = $this->interface->getUser();
		$this->interface->loadLanguageFile();
	}

	protected function remos_get_module_parm ($params, $name, $default) {
		$value =  method_exists($params,'get') ? $params->get($name,$default) : (isset($params->$name) ? $params->$name : $default);
		$isnumeric = is_numeric($default);
		if ($isnumeric AND !is_numeric($value)) return $default;
		if ($isnumeric) return intval($value);
		return $value;
	}

	protected function remos_module_CSS () {
		if (self::$cssdone) return;
		self::$cssdone = true;
		$live_site = $this->interface->getCfg('live_site');
		if (file_exists(_REMOS_ABSOLUTE_PATH.'/images/remository/css/remository.module.css')) {
			$remlink = $live_site.'/images/remository/css/';
		}
		else $remlink = $live_site.'/components/com_remository/';
		$module_css = <<<MODULE_CSS

<link href="{$remlink}remository.module.css" rel="stylesheet" type="text/css" />

MODULE_CSS;

		$this->interface->addCustomHeadTag($module_css);
	}

	protected function remos_getItemID ($component_string) {
		$repository = remositoryRepository::getInstance();
		return $repository->getItemid($component_string);
	}

}
