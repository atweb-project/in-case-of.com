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

/**
 * Comment utilities class.
 *
 */
class KomentoCommentHelper
{
	/**
	 * Process comments data
	 **/
	static public function process( $row, $admin = 0 )
	{
		if( $row->component == '' || $row->cid == '' )
		{
			return false;
		}

		Komento::setCurrentComponent( $row->component );

		$config = Komento::getConfig();
		$konfig = Komento::getKonfig();
		$user = JFactory::getUser()->id;
		$commentsModel	= Komento::getModel( 'comments' );

		$application	= Komento::loadApplication( $row->component )->load( $row->cid );

		if( !$application )
		{
			return false;
		}

		// Duplicate created date first before lapsed time messing up the original date
		$row->unformattedDate = $row->created;

		// set extension object
		$row->extension = $application;

		// set component name and content title
		$row->componenttitle = JText::_( 'COM_KOMENTO_' . strtoupper( $row->component ) );
		$row->contenttitle = $row->extension->getContentTitle();

		// get permalink
		$row->pagelink = $row->extension->getContentPermalink( array('external'=>$admin) );
		$row->permalink = $row->pagelink . '#kmt-' . $row->id;

		// set parentlink
		if( $row->parent_id != 0 )
		{
			$row->parentlink = $row->pagelink . '#kmt-' . $row->parent_id;
		}

		// to be reassign
		$row->shortlink = $row->permalink;

		// get number of child for each comment
		$row->childs = $commentsModel->getTotalChilds( $row->id );

		if( $admin == 0 )
		{
			$actionsModel = Komento::getModel( 'actions' );
			$socialHelper = Komento::getHelper( 'social' );
			$dateHelper = Komento::getHelper( 'date' );

			// parse comments HTML
			$row->comment = self::parseComment( $row->comment );

			// author's object
			$row->author = Komento::getProfile( $row->created_by );

			if( $row->created_by != 0 )
			{
				switch( $config->get( 'name_type' ) )
				{
					case 'username':
						// force username
						$row->name = $row->author->getUsername();
						break;
					case 'name':
						$row->name = $row->author->getName();
						break;
					case 'default':
					default:
						// default name to profile if name is null
						if( empty( $row->name ) )
						{
							$row->name = $row->author->getName();
						}
						break;
				}
			}
			else
			{
				if( empty( $row->name ) )
				{
					$row->name = JText::_( 'COM_KOMENTO_GUEST' );
				}
				else
				{
					$row->name = JText::_( 'COM_KOMENTO_GUEST' ) . ' ('. $row->name . ')';
				}
			}

			// set datetime
			if( $config->get( 'enable_lapsed_time' ) )
			{
				$row->created = $dateHelper->getLapsedTime( $row->created );
			}

			// get actions likes
			$row->likes = $actionsModel->countAction( 'likes', $row->id );

			// get user liked
			$row->liked = $actionsModel->liked( $row->id, $user );

			// get user reported
			$row->reported = $actionsModel->reported( $row->id, $user );
		}
		else
		{
			// format comments
			$row->comment = nl2br( Komento::getHelper( 'comment' )->parseBBCode( $row->comment ) );

			// set depth
			$row->depth = $commentsModel->getCommentDepth( $row->id );
		}

		return $row;
	}

	static public function parseComment( $comment )
	{
		$config = Komento::getConfig();

		// word censoring
		if( $config->get( 'filter_word' ) )
		{
			$comment = self::parseCensor( $comment );
		}

		// parseBBcode to HTML
		$comment = self::parseBBCode( $comment );

		// parse newline to br tags
		$comment = nl2br( $comment );

		return $comment;
	}

	public static function parseCensor($text)
	{
		// Komento::getHelper('filter');
		// $filterHelper = new KomentoFilterHelper;

		$filterHelper = Komento::getHelper('filter');
		$config = Komento::getConfig();

		$textToBeFilter	= explode(',', $config->get('filter_word_text'));
		// lets do some AI here. for each string, if there is a space,
		// remove the space and make it as a new filter text.
		if( count($textToBeFilter) > 0 )
		{
			$newFilterSet   = array();
			foreach( $textToBeFilter as $item)
			{
				if( JString::stristr($item, ' ') !== false )
				{
					$newKeyWord		= JString::str_ireplace(' ', '', $item);
					$newFilterSet[]	= $newKeyWord;
				}
			}

			if( count($newFilterSet) > 0 )
			{
				$tmpNewFitler		= array_merge($textToBeFilter, $newFilterSet);
				$textToBeFilter		= array_unique($tmpNewFitler);
			}
		}

		$filterHelper->strings	= $textToBeFilter;
		$filterHelper->text		= $text;
		return $filterHelper->filter();
	}

	public static function parseBBCode($text)
	{
		//$text	= htmlspecialchars($text , ENT_NOQUOTES );
		$text	= trim($text);
		//$text   = nl2br( $text );

		//$text = preg_replace_callback('/\[code\](.*?)\[\/code\]/ms', "escape", $text);
		$text = preg_replace_callback('/\[code( type="(.*?)")?\](.*?)\[\/code\]/ms', 'escape' , $text );

		// BBCode to find...
		$in = array( 	 '/\[b\](.*?)\[\/b\]/ms',
						 '/\[i\](.*?)\[\/i\]/ms',
						 '/\[u\](.*?)\[\/u\]/ms',
						 '/\[img\](.*?)\[\/img\]/ms',
						 '/\[email\](.*?)\[\/email\]/ms',
						 '/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms',
						 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ms',
						 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ms',
						 '/\[quote\](.*?)\[\/quote\]/ms',
						 '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
						 '/\[list\](.*?)\[\/list\]/ms',
						 '/\[\*\](.*?)\[\/\*\]/ms'
		);
		// And replace them by...
		$out = array(	 '<strong>\1</strong>',
						 '<em>\1</em>',
						 '<u>\1</u>',
						 '<img src="\1" alt="\1" />',
						 '<a href="mailto:\1">\1</a>',
						 '<a href="\1">\2</a>',
						 '<span style="font-size:\1%">\2</span>',
						 '<span style="color:\1">\2</span>',
						 '<blockquote>\1</blockquote>',
						 '<ol start="\1">\2</ol>',
						 '<ul>\1</ul>',
						 '<li>\1</li>'
		);

		$tmp    = preg_replace( $in , '' , $text );
		$text	= self::replaceURL( $tmp, $text );
		$text	= preg_replace($in, $out, $text);

		// Smileys to find...
		$in = array( 	':)',
						':-)',
						':D',
						':o',
						':p',
						':P',
						':(',
						';)',
						';-)'
		);

		// And replace them by...
		$out = array(	'<img alt=":)" class="kmt-emoticon" src="'.KOMENTO_EMOTICONS_DIR.'emoticon-happy.png" />',
						'<img alt=":-)" class="kmt-emoticon" src="'.KOMENTO_EMOTICONS_DIR.'emoticon-happy.png" />',
						'<img alt=":D" class="kmt-emoticon" src="'.KOMENTO_EMOTICONS_DIR.'emoticon-smile.png" />',
						'<img alt=":o" class="kmt-emoticon" src="'.KOMENTO_EMOTICONS_DIR.'emoticon-surprised.png" />',
						'<img alt=":p" class="kmt-emoticon" src="'.KOMENTO_EMOTICONS_DIR.'emoticon-tongue.png" />',
						'<img alt=":P" class="kmt-emoticon" src="'.KOMENTO_EMOTICONS_DIR.'emoticon-tongue.png" />',
						'<img alt=":(" class="kmt-emoticon" src="'.KOMENTO_EMOTICONS_DIR.'emoticon-unhappy.png" />',
						'<img alt=";)" class="kmt-emoticon" src="'.KOMENTO_EMOTICONS_DIR.'emoticon-wink.png" />',
						'<img alt=";-)" class="kmt-emoticon" src="'.KOMENTO_EMOTICONS_DIR.'emoticon-wink.png" />'
		);
		$text = str_replace($in, $out, $text);

		// paragraphs
		$text = str_replace("\r", "", $text);
		$text = "<p>".preg_replace("/(\n){2,}/", "</p><p>", $text)."</p>";


		$text = preg_replace_callback('/<pre>(.*?)<\/pre>/ms', "removeBr", $text);
		$text = preg_replace('/<p><pre>(.*?)<\/pre><\/p>/ms', "<pre>\\1</pre>", $text);

		$text = preg_replace_callback('/<ul>(.*?)<\/ul>/ms', "removeBr", $text);
		// fix [list] within [*] causing dom errors
		$text = preg_replace('/<li>(.*?)<ul>(.*?)<\/ul>(.*?)<\/li>/ms', "\\1<ul>\\2</ul>\\3", $text);
		$text = preg_replace('/<p><ul>(.*?)<\/ul><\/p>/ms', "<ul>\\1</ul>", $text);

		return $text;
	}

	public static function replaceURL( $tmp , $text )
	{
		$pattern    = '@(https?://[-\w\.]+(:\d+)?(/([\w/_\.-]*(\?\S+)?)?)?)@';
		preg_match_all( $pattern , $tmp , $matches );

		if( isset( $matches[ 0 ] ) && is_array( $matches[ 0 ] ) )
		{
			foreach( $matches[ 0 ] as $match )
			{
				$text   = str_ireplace( $match , '<a href="' . $match . '">' . $match . '</a>' , $text );
			}
		}
		return $text;
// 		return preg_replace( $pattern , '<a href="$1">$1</a>', $text );
	}
}

// clean some tags to remain strict
// not very elegant, but it works. No time to do better ;)
if (!function_exists('removeBr')) {
	function removeBr($s) {
		return str_replace("<br />", "", $s[0]);
	}
}

// BBCode [code]
if (!function_exists('escape')) {
	function escape($s) {
		global $text;
		$text = strip_tags($text);
		$code = $s[1];
		$code = htmlspecialchars($code);
		$code = str_replace("[", "&#91;", $code);
		$code = str_replace("]", "&#93;", $code);
		return '<pre><code>'.$code.'</code></pre>';
	}
}
