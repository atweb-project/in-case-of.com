<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'administrator' . DS . 'includes' . DS . 'toolbar.php' );

$submenus	= array(
						'komento'		=> JText::_('COM_KOMENTO_TAB_HOME')
					);

$current	= JRequest::getVar( 'view' , 'komento' );

// @task: For the frontpage, we just show the the icons.
if( $current == 'komento' )
{
	$submenus	= array( 'komento' => JText::_('COM_KOMENTO_TAB_HOME') );
}
foreach( $submenus as $view => $title )
{
	$isActive	= ( $current == $view );

 	JSubMenuHelper::addEntry( $title , 'index.php?option=com_komento&view=' . $view , $isActive );
}