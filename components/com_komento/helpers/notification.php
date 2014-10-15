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

class KomentoNotificationHelper
{
	/**
	 * Push the email notification to MailQ
	 * @param	string	$type			type of notification
	 * @param	string	$recipient		recipient (subscribers,admins,author,me)
	 * @param	array	$options		various options
	 *
	 * @return nothing
	 */
	public function push( $type, $recipients, $options = array() )
	{
		if( isset( $options['commentId'] ) )
		{
			$comment = Komento::getComment( $options['commentId'] );
			$options['comment'] = $comment;
			$options['component'] = $comment->component;
			$options['cid'] = $comment->cid;

			unset( $options['commentId'] );
		}

		$options['comment'] = Komento::getHelper( 'comment' )->process( $options['comment'] );

		if( !isset( $options['component'] ) || !isset( $options['cid'] ) )
		{
			return;
		}

		$recipients		= explode(',', $recipients);
		$rows			= array();
		$skipMe			= true;

		foreach ($recipients as $recipient)
		{
			$recipient		= 'get' . ucfirst( strtolower( trim($recipient) ) );

			if( !method_exists($this, $recipient) )
			{
				continue;
			}

			if( $recipient == 'getMe' )
			{
				$skipMe = false;
			}

			// stacking up all the emails and details
			$rows	= $rows + $this->$recipient( $options );
		}

		if( $type == 'report' )
		{
			$admins = $this->getAdmins();

			foreach( $admins as $admin )
			{
				if( isset($rows[$options['comment']->email]) && $options['comment']->email === $admin->email )
				{
					$skipMe = false;
				}
			}
		}

		if( empty($rows) )
		{
			return;
		}

		// Do not send to the commentor
		if( $skipMe && isset($rows[$options['comment']->email]) )
		{
			unset( $rows[$options['comment']->email] );
		}

		// Load front end's language file.
		JFactory::getLanguage()->load( 'com_komento' , JPATH_ROOT );
		$jconfig	= JFactory::getConfig();
		$data		= $this->prepareData( $type, $options );
		$template	= $this->prepareTemplate( $type );
		$subject	= $this->prepareTitle( $type );

		$mailfrom	= Komento::_( 'JFactory::getConfig', 'mailfrom' );
		$fromname	= Komento::_( 'JFactory::getConfig', 'fromname' );

		// Storing notifications into mailq
		foreach ($rows as $row)
		{
			$body	= $this->getTemplateBuffer( $template, $data, array( 'recipient' => $row ) );
			$mailQ	= Komento::getTable( 'mailq' );
			$mailQ->mailfrom	= $mailfrom;
			$mailQ->fromname	= $fromname;
			$mailQ->recipient	= $row->email;
			$mailQ->subject		= $subject;
			$mailQ->body		= $body;
			$mailQ->created		= JFactory::getDate()->toMySQL();
			$mailQ->status		= 0;
			$result = $mailQ->store();
		}
	}

	public function getTemplateBuffer( $template, $data, $params = array() )
	{
		$theme	= Komento::getTheme();

		foreach( $data as $key => $val )
		{
			$theme->set( $key , $val );
		}

		$theme->set( 'data', $data );
		$theme->set( 'options', $params );

		$contents	= $theme->fetch( $template );

		return $contents;
	}

	private function prepareData( $type = 'new', $options )
	{
		$application = Komento::loadApplication( $options['component'] )->load( $options['cid'] );
		Komento::import( 'helper', 'date' );
		$profile	= Komento::getProfile();

		$data							= array();
		$data['contentTitle']			= $application->getContentTitle();
		$data['contentPermalink']		= $application->getContentPermalink( array('external'=>true) );
		$data['commentAuthorName']		= $profile->getName();
		$data['commentAuthorAvatar']	= $profile->getAvatar();

		switch( $type )
		{
			case 'confirm':
				$data['confirmLink']	= rtrim( JURI::root() , '/' ) . '/index.php?option=com_komento&task=confirmSubscription&id=' . $options['subscribeId'];
				break;
			case 'moderate':
				$hashkeys = Komento::getTable( 'hashkeys' );
				$hashkeys->uid = $options['comment']->id;
				$hashkeys->type = 'comment';
				$hashkeys->store();
				$key = $hashkeys->key;

				$data['approveLink']	= rtrim( JURI::root() , '/' ) . '/index.php?option=com_komento&task=approveComment&token=' . $key;
				$data['commentContent']	= JFilterOutput::cleanText($options['comment']->comment);
				$date					= KomentoDateHelper::dateWithOffSet( $options['comment']->unformattedDate );
				$date					= KomentoDateHelper::toFormat( $date , '%A, %B %e, %Y' );
				$data['commentDate']	= $date;
				break;
			case 'report':
				$action = Komento::getTable( 'actions' );
				$action->load( $options['actionId'] );
				$actionUser = $action->action_by;

				$data['actionUser']			= Komento::getProfile( $actionUser );
				$data['commentPermalink']	= $data['contentPermalink'] . '#kmt-' . $options['comment']->id;
				$data['commentContent']		= JFilterOutput::cleanText($options['comment']->comment);
				$date						= KomentoDateHelper::dateWithOffSet( $options['comment']->unformattedDate );
				$date						= KomentoDateHelper::toFormat( $date , '%A, %B %e, %Y' );
				$data['commentDate']		= $date;
				break;
			case 'new':
			default:
				$data['commentPermalink']	= $data['contentPermalink'] . '#kmt-' . $options['comment']->id;
				$data['commentContent']		= JFilterOutput::cleanText($options['comment']->comment);
				$date						= KomentoDateHelper::dateWithOffSet( $options['comment']->unformattedDate );
				$date						= KomentoDateHelper::toFormat( $date , '%A, %B %e, %Y' );
				$data['commentDate']		= $date;
				$data['unsubscribe'] 		= rtrim( JURI::root(), '/' ) . '/index.php?option=com_komento&task=unsubscribe&id=';
				break;
		}

		return $data;
	}

	private function prepareTemplate( $type = 'new' )
	{
		$config			= Komento::getConfig();
		$templateType	= $config->get( 'notification_sendmailinhtml' ) ? 'html' : 'text';

		switch( $type )
		{
			case 'moderate':
				$file	= 'moderatecomment';
				break;
			case 'confirm':
				$file	= 'confirmsubscription';
				break;
			case 'report':
				$file	= 'reportcomment';
				break;
			case 'new':
			default:
				$file	= 'newcomment';
				break;
		}

		$file 	= 'emails/' . $file . '.' . $templateType . '.php';

		return $file;
	}

	private function prepareTitle( $type = 'new' )
	{
		$subject = '';

		switch( $type )
		{
			case 'moderate':
				$subject = JText::_('COM_KOMENTO_NOTIFICATION_PENDING_COMMENT_SUBJECT');
				break;
			case 'confirm':
				$subject = JText::_('COM_KOMENTO_NOTIFICATION_CONFIRM_SUBSCRIPTION_SUBJECT');
				break;
			case 'report':
				$subject = JText::_('COM_KOMENTO_NOTIFICATION_REPORT_COMMENT_SUBJECT');
				break;
			case 'new':
			default:
				$subject = JText::_('COM_KOMENTO_NOTIFICATION_NEW_COMMENT_SUBJECT');
				break;
		}

		return $subject;
	}

	public function getMe()
	{
		$obj		= new stdClass();
		$my			= JFactory::getUser();

		if( empty($my->id) )
		{
			return array();
		}

		$obj->id	= $my->id;
		$obj->fullname	= $my->name;
		$obj->email	= $my->email;

		return array( $my->email => $obj );
	}

	public function getAuthor( $options )
	{
		$config		= Komento::getConfig();
		if( !$config->get( 'notification_to_author' ) )
		{
			return array();
		}

		$userid		= Komento::loadApplication( $options['component'] )->load( $options['cid'] )->getAuthorId();

		$obj		= new stdClass();
		$user		= JFactory::getUser( $userid );
		$obj->id	= $user->id;
		$obj->fullname	= $user->name;
		$obj->email	= $user->email;

		return array( $user->email => $obj );
	}

	public function getSubscribers( $options )
	{
		$config		= Komento::getConfig();
		if( !$config->get( 'notification_to_subscribers' ) )
		{
			return array();
		}

		$db		= JFactory::getDbo();
		$query	= 'SELECT `id` AS subscriptionid, `userid` AS id, `fullname`, `email` FROM `#__komento_subscription`'
				. ' WHERE `component` = ' . $db->quote( $options['component'] )
				. ' AND `cid` = ' . $db->quote( $options['cid'] )
				. ' AND `published` = 1';
		$db->setQuery( $query );

		$subscribers = $db->loadObjectList();

		if (!$subscribers)
		{
			return array();
		}
		else
		{
			$result = array();

			foreach ($subscribers as $subscriber)
			{
				$result[$subscriber->email] = $subscriber;
			}

			return $result;
		}
	}

	public function getAdmins()
	{
		$config		= Komento::getConfig();
		if( !$config->get( 'notification_to_admins' ) )
		{
			return array();
		}

		$saUsersIds	= Komento::getSAUsersIds();


		$db		= JFactory::getDbo();

		$query 	= 'SELECT ' . $db->nameQuote( 'id' ) . ',' . $db->nameQuote( 'name' ) . ' AS `fullname`,' . $db->nameQuote( 'email' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__users' ) . ' '
				. 'WHERE 1 ';

		if( $saUsersIds )
		{
			$query	.= ' AND ' . $db->nameQuote( 'id' ) . ' IN(';
			for( $i = 0; $i < count( $saUsersIds ); $i++ )
			{
				$id 	= $saUsersIds[$i];

				$query	.= $db->Quote( $id );

				if( next( $saUsersIds) !== false )
				{
					$query .= ',';
				}
			}
			$query	.= ')';
		}

		$query	.= ' AND ' . $db->nameQuote( 'sendEmail' ) . '=' . $db->Quote( 1 );

		$db->setQuery( $query );
		$admins	= $db->loadObjectList();

		if( !$admins )
		{
			return array();
		}
		else
		{
			$result = array();

			foreach ($admins as $admin)
			{
				$result[$admin->email] = $admin;
			}

			return $result;
		}
	}
}
