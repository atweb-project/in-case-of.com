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

class mod_remositoryTotal extends mod_remositoryBase {

	public function showCount ($module, &$content, $area, $params) {
		$interface = remositoryInterface::getInstance();
		$database = $interface->getDB();

		$sql = "SELECT SUM(downloads) from #__downloads_files";
		$database->setQuery( $sql );
		$total = number_format($database->loadResult());
		$content = "<div align='center' class='sitename'>$total</div>";
	}
	
}