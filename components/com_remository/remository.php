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

// Don't allow direct linking
if (!defined( '_VALID_MOS' ) AND !defined('_JEXEC')) die( 'Direct Access to this location is not allowed.' );

if ('5.1.2' > PHP_VERSION) die ('Sorry, this version of Remository requires PHP version 5.1.2 or above');

if (!defined('_ALIRO_IS_PRESENT')) {
	$remository_dir = str_replace('\\','/',dirname(__FILE__));
	require_once($remository_dir.'/remository.main.php');
	require_once($remository_dir.'/remository.interface.php');
	$interface = remositoryInterface::getInstance();
}
// Make sure interface class is loaded to force definition of _REMOSITORY_VERSION
else $interface = remositoryInterface::getInstance();

//error_reporting(E_ALL);

$alternatives = array ('startdown' => 'fileinfo',
                'showdown' => 'fileinfo',
                'finishdown' => 'fileinfo',
                'filelist' => 'select',
                'classify' => 'advsearch',
                'setallcats' => 'advsearch',
                'directlist' => 'select',
                'directinfo' => 'fileinfo'
                );

$admin = new remositoryUserAdmin('remository', 'func', $alternatives, 'select');
