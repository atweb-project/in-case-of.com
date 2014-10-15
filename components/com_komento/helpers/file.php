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

class KomentoFileHelper
{
	public function upload( $fileItem, $fileName = '', $storagePath = '', $published = 1 )
	{
		// $fileItem['name'] = filename
		// $fileItem['type'] = mime
		// $fileItem['tmp_name'] = temporary source
		// $fileItem['size'] = size

		// store record first
		$uploadtable = Komento::getTable( 'uploads' );

		$now = JFactory::getDate()->toMySQL();
		$uploadtable->created = $now;

		$profile = Komento::getProfile();
		$uploadtable->created_by = $profile->id;

		$uploadtable->published = $published;

		$uploadtable->mime = $fileItem['type'];

		$uploadtable->size = $fileItem['size'];

		if( $fileName == '' )
		{
			$fileName = $fileItem['name'];
		}
		$uploadtable->filename = $fileName;

		if( $storagePath == '' )
		{
			$config = Komento::getConfig();
			$storagePath = $config->get( 'upload_path' );
		}
		$uploadtable->path = $storagePath;

		if( !$uploadtable->upload() )
		{
			return false;
		}

		$source = $fileItem['tmp_name'];
		$path = KomentoFileHelper::getAbsolutePath( $storagePath );
		$destination = $path . $uploadtable->hashname;

		jimport( 'joomla.filesystem.file' );
		if( !JFile::copy( $source , $destination ) )
		{
			$uploadtable->rollback();
			return false;
		}

		return $uploadtable->id;
	}

	public function download( $id )
	{
		$filetable = Komento::getTable( 'uploads' );
		$filetable->load( $id );

		$path = KomentoFileHelper::getAbsolutePath( $filetable->path );

		$file = $path . $filetable->hashname;

		if (!JFile::exists($file))
		{
			return false;
		}

		$length = filesize($file);

		/*switch( $filetable->getType() )
		{
			case 'image':
				echo '<img src="' . JRoute::_( 'index.php?option=com_komento&controller=file&task=displayFile&id=' . $this->id ) . '" />';
				exit;
			break;
			default:
				header('Content-Description: File Transfer');
				header('Content-Type: ' . $this->mime);
				header("Content-Disposition: attachment; filename=\"".basename($this->filename)."\";" );
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . $length );
			break;
		}*/

		header('Content-Description: File Transfer');
		header('Content-Type: ' . $filetable->mime);
		header("Content-Disposition: attachment; filename=\"".basename($filetable->filename)."\";" );
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . $length );

		ob_clean();
		flush();
		readfile( $file );
		exit;
	}

	public static function getAbsolutePath( $relativePath = '' )
	{
		$path = KOMENTO_UPLOADS_ROOT . DS;

		// @task: Ensure that the relativePath is set to the proper directory separator.
		$relativePath	= trim( str_ireplace( array( '/' , '\\' ) , DS , $relativePath ), DS );

		if( $relativePath != '' )
		{
			$path .= $relativePath . DS;
		}

		// @task: Create the directory if it doesn't exist
		if( !file_exists( $path ) )
		{
			jimport('joomla.filesystem.folder');
			JFolder::create( $path );
		}

		return $path;
	}

	public static function getAbsoluteURI( $relativePath = '' )
	{
		$path = rtrim( JURI::root() , '/' ) . '/media/com_komento/uploads/';

		// @task: Ensure that the relativePath is set to the proper directory separator.
		$relativePath	= trim( str_ireplace( '\\' , '/' , $relativePath ), '/' );

		if( $relativePath != '' )
		{
			$path	.= $relativePath . '/';
		}

		return $path;
	}

	public function attach( $id, $uid )
	{
		$table = Komento::getTable( 'uploads' );
		$table->load( $id );
		$table->uid = $uid;

		return $table->store();
	}

	public function getAttachments( $uid )
	{
		$db = JFactory::getDBO();

		$query  = 'SELECT * FROM ' . $db->nameQuote( '#__komento_uploads' );
		$query .= ' WHERE ' . $db->nameQuote( 'uid' ) . ' = ' . $db->quote( $uid );
		$query .= ' ORDER BY ' . $db->nameQuote( 'created' );

		$db->setQuery( $query );
		$result = $db->loadObjectList();

		if( count( $result ) == 0 )
		{
			return false;
		}

		foreach( $result as &$item )
		{
			$item->class = KomentoFileHelper::getIconType( $item->mime, $item->filename );
			$item->link = rtrim( JURI::root(), '/' ) . '/index.php?option=com_komento&controller=file&task=download&id=' . $item->id;
		}

		return $result;
	}

	public function checkAttachment( $id, $uid )
	{
		$table = Komento::getTable( 'uploads' );
		$table->load( $id );

		if( $uid != $table->uid )
		{
			return false;
		}

		return true;
	}

	public function clearAttachments( $uid )
	{
		$attachments = $this->getAttachments( $uid );

		foreach( $attachments as $attachment )
		{
			$this->delete( $attachment->id );
		}
	}

	public function delete( $id )
	{
		$attachment = Komento::getTable( 'uploads' );
		$attachment->load( $id );

		$file = KomentoFileHelper::getAbsolutePath( $attachment->path ) . $attachment->hashname;

		jimport( 'joomla.filesystem.file' );
		if( !JFile::delete( $file ) )
		{
			return false;
		}

		$attachment->delete();

		return true;
	}

	public static function getType( $mime )
	{
		$type = explode("/", $mime);

		return $type[0];
	}

	public static function getSubtype( $mime )
	{
		$type = explode("/", $mime);

		return $type[1];
	}

	public static function getIconType( $mime, $filename = '' )
	{
		$type = KomentoFileHelper::getType( $mime );

		$class = 'file';

		switch( $type )
		{
			case 'image':
			case 'audio':
			case 'video':
			case 'text':
				$class = $type;
				break;
			case 'application':

				$extension = KomentoFileHelper::getExtension( $filename );

				if( $extension !== false )
				{
					switch( $extension )
					{
						case 'doc':
						case 'docx':
						case 'odt':
							$class = 'document';
							break;
						case 'xls':
						case 'xlsx':
						case 'xlb':
						case 'ods':
							$class = 'spreadsheet';
							break;
						case 'ppt':
						case 'pptx':
						case 'pps':
						case 'pot':
						case 'odp':
							$class = 'slideshow';
							break;
						case 'zip':
						case 'rar':
						case 'cab':
						case 'msi':
							$class = 'archive';
							break;
						case 'pdf':
							$class = 'pdf';
							break;
					}
				}
				else
				{
					$subtype = KomentoFileHelper::getSubtype( $mime );

					switch( $subtype )
					{
						case 'msword':
						case 'vnd.oasis.opendocument.text':
							$class = 'document';
							break;
						case 'vnd.ms-excel':
						case 'vnd.oasis.opendocument.spreadsheet':
							$class = 'spreadsheet';
							break;
						case 'vnd.ms-powerpoint':
						case 'vnd.oasis.opendocument.presentation':
							$class = 'slideshow';
							break;
						case 'zip':
						case 'x-rar':
						case 'x-rar-compressed':
						case 'x-cab':
						case 'vnd.ms-cab-compressed':
							$class = 'archive';
							break;
						case 'pdf':
							$class = 'pdf';
							break;
					}
				}
		}

		return $class;
	}

	public static function getExtension( $filename )
	{
		$tmp = explode( '.', $filename );

		if( count( $tmp ) <= 1 )
		{
			return false;
		}

		$extension = $tmp[count($tmp)-1];

		return $extension;
	}
}
