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

class aliroObjectSorter {
    var $_keyname = '';
    var $_direction = 0;
    var $_object_array = array();

    public function __construct (&$a, $k, $sort_direction=1) {
        $this->_keyname = $k;
        $this->_direction = $sort_direction;
        $this->_object_array =& $a;
        $this->sort();
    }

    // DO NOT USE THIS METHOD - SIMPLY SORT BY INVOKING new aliroObjectSorter
    // This is not genuinely public, but has to be declared so for the callback
    public function aliroObjectCompare (&$a, &$b) {
        $key = $this->_keyname;
        if ($a->$key > $b->$key) return $this->_direction;
        if ($a->$key < $b->$key) return -$this->_direction;
        return 0;
    }

    private function sort () {
        usort($this->_object_array, array($this,'aliroObjectCompare'));
    }

}


class remository_allcats_Controller extends remositoryUserControllers {
    var $container = null;

	function select($func) {
		$interface = remositoryInterface::getInstance();
	    if ($this->idparm) {
	        $container = $this->createContainer ();
	        if (!$this->repository->See_Containers_no_download AND !$container->isDownloadable($this->remUser)) {
    			echo "<span class='remositorymessage'>"._DOWN_RESTRICTED_WARN.'</span>';
    	   		return;
	        }
	    }
	    if (!$this->idparm OR $container->parentid) $interface->redirect($this->repository->RemositoryBasicFunctionURL('select'));
	}
	
}