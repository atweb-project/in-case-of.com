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

jimport('joomla.application.component.controlleradmin');

class XdownloaderControllerDefault extends JControllerAdmin {

	public function  __construct($config = array()) {
        parent::__construct($config);
    }
	
    /**
    * Proxy for getModel.
    * @since	1.6
    */
    public function getModel($name = 'Default', $prefix = 'XdownloaderModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    public function delete() {
    	$cid = JRequest::getVar('cid', array(), 'post', 'array');
    	$model = $this->getModel();
    	$count = $model->delete($cid);
    	$msg = JText::sprintf('COM_XDOWNLOADER_DELETE_MSG', $count);   	
    	$this->setRedirect('index.php?option=com_xdownloader&view=default', $msg);
    }
}
?>