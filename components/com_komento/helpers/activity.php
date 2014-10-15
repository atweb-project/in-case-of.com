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

class KomentoActivityHelper
{
	public function process( $action, $comment )
	{
		// process all activities and 3rd party integration here

		if( !is_object( $comment ) )
		{
			$comment = Komento::getComment( $comment );
		}

		$config = Komento::getConfig();
		$profile = Komento::getProfile();
		$application = Komento::loadApplication( $comment->component )->load( $comment->cid );
		$pagelink = $application->getContentPermalink();
		$permalink = $pagelink . '#' . $comment->id;
		$author = $application->getAuthorId();
		$title = $application->getContentTitle();

		if( $profile->id == 0 )
		{
			return false;
		}

		// native activity
		if( ( $action == 'comment' && $config->get( 'activities_comment' ) ) || ( $action == 'reply' && $config->get( 'activities_reply' ) ) || ( $action == 'like' && $config->get( 'activities_like' ) ) )
		{
			$this->addActivity( $action, $comment->id, $profile->id );
		}

		// Add jomsocial activity
		if( $action == 'comment' && $config->get( 'jomsocial_enable_comment' ) )
		{
			$this->addJomSocialActivityComment( $comment );
		}
		if( $action == 'reply' && $config->get( 'jomsocial_enable_reply' ) )
		{
			$this->addJomSocialActivityReply( $comment );
		}
		if( $action == 'like' && $config->get( 'jomsocial_enable_like' ) )
		{
			$this->addJomSocialActivityLike( $comment, $profile->id );
		}

		// Add jomsocial userpoints
		if( $config->get( 'jomsocial_enable_userpoints' ) )
		{
			switch( $action )
			{
				case 'comment':
					Komento::addJomSocialPoint( 'com_komento.comment.add' );
					Komento::addJomSocialPoint( 'com_komento.comment.add.author', $author );
					break;

				case 'reply':
					Komento::addJomSocialPoint( 'com_komento.comment.reply' );
					break;

				case 'like':
					Komento::addJomSocialPoint( 'com_komento.comment.like' );
					Komento::addJomSocialPoint( 'com_komento.comment.liked', $comment->created_by );
					break;

				case 'unlike':
					Komento::addJomSocialPoint( 'com_komento.comment.unlike' );
					Komento::addJomSocialPoint( 'com_komento.comment.unliked', $comment->created_by );
					break;

				case 'report':
					Komento::addJomSocialPoint( 'com_komento.comment.report' );
					Komento::addJomSocialPoint( 'com_komento.comment.reported', $comment->created_by );
					break;

				case 'unreported':
					Komento::addJomSocialPoint( 'com_komento.comment.unreport' );
					Komento::addJomSocialPoint( 'com_komento.comment.unreported', $comment->created_by );
					break;

				case 'stick':
					Komento::addJomSocialPoint( 'com_komento.comment.sticked' );
					break;

				case 'unstick':
					Komento::addJomSocialPoint( 'com_komento.comment.unsticked' );
					break;

				case 'remove':
					Komento::addJomSocialPoint( 'com_komento.comment.removed' );
					Komento::addJomSocialPoint( 'com_komento.comment.removed.author', $author );
					break;
			}
		}

		// Add aup
		if( $config->get( 'enable_aup' ) )
		{
			switch( $action )
			{
				case 'comment':
					Komento::addAUP( 'plgaup_komento_post_comment', $comment->created_by, 'komento_post_comment_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_POST_COMMENT', $permalink, $title ) );
					Komento::addAUP( 'plgaup_komento_add_comment_author', $author, 'komento_post_comment_on_' . $comment->component . '_' . $comment->cid, JText::sprintf( 'COM_KOMENTO_AUP_POST_COMMENT', $permalink, $title ) );
					break;

				case 'reply':
					Komento::addAUP( 'plgaup_komento_reply_comment', $comment->created_by, 'komento_reply_comment_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_REPLY_COMMENT', $permalink, $title ) );
					break;

				case 'like':
					Komento::addAUP( 'plgaup_komento_like_comment', $profile->id, 'komento_like_comment_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_LIKE_COMMENT', $permalink, $title ) );
					Komento::addAUP( 'plgaup_komento_comment_liked', $comment->created_by, 'komento_comment_liked_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_COMMENT_LIKED', $permalink, $title ) );
					break;

				case 'unlike':
					Komento::addAUP( 'plgaup_komento_unlike_comment', $profile->id, 'komento_unlike_comment_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_UNLIKE_COMMENT', $permalink, $title ) );
					Komento::addAUP( 'plgaup_komento_comment_unliked', $comment->created_by, 'komento_comment_unliked_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_COMMENT_UNLIKED', $permalink, $title ) );
					break;
				case 'report':
					Komento::addAUP( 'plgaup_komento_report_comment', $profile->id, 'komento_report_comment_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_REPORT_COMMENT', $permalink, $title ) );
					Komento::addAUP( 'plgaup_komento_comment_reported', $comment->created_by, 'komento_comment_reported_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_COMMENT_REPORTED', $permalink, $title ) );
					break;
				case 'unreport':
					Komento::addAUP( 'plgaup_komento_unreport_comment', $profile->id, 'komento_unreport_comment_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_UNREPORT_COMMENT', $permalink, $title ) );
					Komento::addAUP( 'plgaup_komento_comment_unreported', $comment->created_by, 'komento_comment_unreported_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_COMMENT_UNREPORTED', $permalink, $title ) );
					break;
				case 'stick':
					Komento::addAUP( 'plgaup_komento_comment_sticked', $comment->created_by, 'komento_comment_sticked_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_COMMENT_STICKED', $permalink, $title ) );
					break;
				case 'unstick':
					Komento::addAUP( 'plgaup_komento_comment_unsticked', $comment->created_by, 'komento_comment_unsticked_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_COMMENT_UNSTICKED', $permalink, $title ) );
					break;
				case 'remove':
					Komento::addAUP( 'plgaup_komento_comment_removed', $comment->created_by, 'komento_comment_removed_' . $comment->id, JText::sprintf( 'COM_KOMENTO_AUP_COMMENT_REMOVED', $pagelink, $title ) );
					Komento::addAUP( 'plgaup_komento_remove_comment_author', $author, 'komento_remove_comment_on_' . $comment->component . '_' . $comment->cid, JText::sprintf( 'COM_KOMENTO_AUP_COMMENT_REMOVED', $pagelink, $title ) );
					break;
			}
		}
	}

	public function addActivity( $type, $comment_id, $uid )
	{
		$model	= Komento::getModel( 'Activity' );
		return $model->add( $type, $comment_id, $uid );
	}

	public function addJomSocialActivity( $options = array() )
	{
		$defaultOptions = array(
			'comment'		=> '',
			'title'			=> '',
			'content'		=> '',
			'cmd'			=> '',
			'actor'			=> '',
			'target'		=> 0,
			'app'			=> '',
			'cid'			=> '',
			'comment_id'	=> '',
			'comment_type'	=> ''
		);

		$options = Komento::mergeOptions( $defaultOptions, $options );

		$jsCoreFile	= JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php';
		$config		= Komento::getConfig();

		if( !JFile::exists( $jsCoreFile ) )
		{
			return false;
		}
		require_once( $jsCoreFile );

		$obj				= (object) $options;

		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add( $obj );
	}

	public function addJomSocialActivityComment( $comment )
	{
		if( !is_object( $comment ) )
		{
			$comment = Komento::getComment( $comment );
		}

		$comment = Komento::getHelper( 'comment' )->process( $comment );
		$comment->comment = JString::substr( strip_tags( $comment->comment ), 0 , Komento::getConfig()->get( 'jomsocial_comment_length' ) );

		$options = array(
			'title'			=> JText::sprintf('COM_KOMENTO_JOMSOCIAL_ACTIVITY_COMMENT_ADDED', $comment->pagelink, $comment->extension->getContentTitle() ),
			'content'		=> $comment->comment,
			'cmd'			=> 'komento.comment.add',
			'app'			=> $comment->component,
			'cid'			=> $comment->cid,
			'actor'			=> $comment->created_by,
			'comment_id'	=> $comment->id,
			'comment_type'	=> 'com_komento.comment'
		);
		self::addJomSocialActivity( $options );
	}

	public function addJomSocialActivityReply( $comment )
	{
		if( !is_object( $comment ) )
		{
			$comment = Komento::getComment( $comment );
		}

		$comment = Komento::getHelper( 'comment' )->process( $comment );
		$comment->comment = JString::substr( strip_tags( $comment->comment ), 0 , Komento::getConfig()->get( 'jomsocial_comment_length' ) );

		$parent = Komento::getHelper( 'comment' )->process( Komento::getComment( $comment->parent_id ) );

		$options = array(
			'title'			=> JText::sprintf('COM_KOMENTO_JOMSOCIAL_ACTIVITY_REPLY_ADDED', $parent->permalink, $comment->pagelink, $comment->extension->getContentTitle() ),
			'content'		=> $comment->comment,
			'cmd'			=> 'komento.comment.reply',
			'app'			=> $comment->component,
			'cid'			=> $comment->cid,
			'actor'			=> $comment->created_by,
			'comment_id'	=> $comment->id,
			'comment_type'	=> 'com_komento.reply'
		);
		self::addJomSocialActivity( $options );
	}

	public function addJomSocialActivityLike( $comment, $uid )
	{
		if( !is_object( $comment ) )
		{
			$comment = Komento::getComment( $comment );
		}

		$comment = Komento::getHelper( 'comment' )->process( $comment );
		$comment->comment = JString::substr( strip_tags( $comment->comment ), 0 , Komento::getConfig()->get( 'jomsocial_comment_length' ) );

		$options = array(
			'title'			=> JText::sprintf('COM_KOMENTO_JOMSOCIAL_ACTIVITY_LIKED_COMMENT', $comment->permalink, $comment->pagelink, $comment->extension->getContentTitle() ),
			'content'		=> $comment->comment,
			'cmd'			=> 'komento.comment.like',
			'app'			=> $comment->component,
			'cid'			=> $comment->cid,
			'actor'			=> $uid,
			'comment_id'	=> $comment->id,
			'comment_type'	=> 'com_komento.comments'
		);
		self::addJomSocialActivity( $options );
	}
}
