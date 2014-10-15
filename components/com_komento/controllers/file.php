<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class KomentoControllerFile extends JController
{
	public function upload()
	{
		$profile	= Komento::getProfile();
		$config		= Komento::getConfig();

		// acl stuffs here

		$file	= JRequest::getVar( 'file', '', 'FILES', 'array' );

		// $file['name'] = filename
		// $file['type'] = mime
		// $file['tmp_name'] = temporary source
		// $file['size'] = size

		$id = Komento::getHelper( 'file' )->upload( $file );

		$result = array(
			'status'	=> 1,
			'id'		=> 0
		);

		if( $id === false )
		{
			$result['status'] = 0;
		}
		else
		{
			$result['id'] = $id;
		}

		// do not return
		// echo json string instead and exit

		echo json_encode( $result ); exit;
	}

	public function download()
	{
		$id	= JRequest::getInt( 'id' );

		// need to get component to check acl because controller link component is com_komento

		$filetable = Komento::getTable( 'uploads' );
		if( !$filetable->load( $id ) )
		{
			echo JText::_( 'COM_KOMENTO_ATTACHMENT_INVALID_ID' );
			exit;
		}

		$comment = Komento::getComment( $filetable->uid );

		$profile = Komento::getProfile();
		if( !$profile->allow( 'download_attachment', $comment->component ) )
		{
			echo JText::_( 'COM_KOMENTO_ATTACHMENT_NO_PERMISSION' );
			exit;
		}

		$filehelper = Komento::getHelper( 'file' );

		return $filehelper->download( $id );
	}
}
