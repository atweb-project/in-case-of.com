<?php
/*------------------------------------------------------------------------
# com_xdownloader - xDownloader alpha component
# ------------------------------------------------------------------------
# author    Dmitri Gorbunov
# copyright Copyright (C) 2012 xrbyte.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.xrbyte.com
# Technical Support:  Forum - http://www.xrbyte.com/forum
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class XdownloaderController extends JController {

    function  display($cachable = false, $urlparams = false) {
        // set default view if not set
        JRequest::setVar('view', JRequest::getCmd('view', 'default'));		
        parent::display();
    }
}
?>
