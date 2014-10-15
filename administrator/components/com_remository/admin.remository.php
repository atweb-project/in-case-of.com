<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006-10 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

// Don't allow direct linking
if (!defined( '_VALID_MOS' ) AND !defined('_JEXEC')) die( 'Direct Access to this location is not allowed.' );

if ('5.1.2' > PHP_VERSION) die ('Sorry, this version of Remository requires PHP version 5.1.2 or above');

if (!defined('REMOSITORY_ADMIN_SIDE')) define ('REMOSITORY_ADMIN_SIDE', 1);
	
//error_reporting(E_ALL);

if (!defined('_ALIRO_IS_PRESENT')) {
	// Include files that contain classes
	$remository_dir = str_replace('\\','/',dirname(__FILE__));
	$components_dir = dirname($remository_dir);
	$admin_dir = dirname($components_dir);
	$absolute_path = dirname($admin_dir);
	require_once($absolute_path.'/components/com_remository/remository.interface.php');
	$interface = remositoryInterface::getInstance();
}
// Make sure interface class is loaded to force definition of _REMOSITORY_VERSION
else $interface = remositoryInterface::getInstance();

new remositoryAdminManager('remository');