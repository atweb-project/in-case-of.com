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

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $mainframe;

require_once ($mainframe->getCfg('absolute_path').'/components/com_remository/remository.class.php');
require_once ($mosConfig_absolute_path.'/components/com_remository/remository.utilities.php');

class mod_remositorydowncart {

    function mod_remositorydowncart ($suffix) {
        $downloadcart = isset($_COOKIE['remository_downcart']) ? unserialize(base64_decode($_COOKIE['remository_downcart'])) : array();
        $file = null;
        if ($cart_count = count($downloadcart)) $cart_text = sprintf("%s items", $cart_count);
        else $cart_text = 'Empty';
        $title = $file ? $file->filetitle.' Added' : '';
		$this->downcartHTML($file, $cart_text, $title, $suffix);
    }

    function downcartHTML ($file, $cart_text, $title, $suffix) {
        $action = remositoryRepository::RemositoryFunctionURL('downcart');
        if ($title) $title = "<div>$title</div>";
        $html = <<<START_CART
        <div class="moduletable-$suffix" style="text-align:right">
        <img src="images/citi/img_global/cart_icon.gif" style="width: 19px; height: 19px" alt="Cart" align="default" height="19" width="19" />
        CART: $cart_text ({$action}View Cart</a>)
        <div id="remocartaddition" />
        </div>
START_CART;

        echo $html;
    }

}

$suffix = remositoryUtilities::remos_get_module_parm($params,'moduleclass_sfx','');
new mod_remositorydowncart($suffix);
