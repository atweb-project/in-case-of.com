<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

abstract class KomentoExtension
{
	const APIVERSION = '1.2';

	/**
	 * The extension name
	 * @var string
	 */
	public $component = null;

	/**
	 * Initialize the plugin
	 */
	public function __construct( $component )
	{
		$this->component	= $component;
	}

	/**
	 * Method to get the name of the current API version number
	 *
	 * @access	public
	 *
	 * @return	string	The version number
	 */
	public function getAPIVersion()
	{
		return self::APIVERSION;
	}

	/**
	 * Method to get the name of current component
	 *
	 * @access	public
	 *
	 * @return	string	This component's name
	 */
	public function getComponentName()
	{
		return $this->component;
	}

	public function prepareLink( &$link, $params = array() )
	{
		if( array_key_exists('append', $params) )
		{
			$link .= $params['append'];
		}

		if( array_key_exists('external', $params) && $params['external'] )
		{
			if( strpos( $link, '/administrator/' ) === 0 )
			{
				$link = substr( $link, 14 );
			}

			// $uri	= JURI::getInstance();
			// $link	= $uri->toString( array('scheme', 'host', 'port')) . '/' . ltrim( $link , '/' );

			$link = rtrim( JURI::root(), '/' ) . '/' . ltrim( $link, '/' );
		}

		if( array_key_exists('anchor', $params) )
		{
			$link .= '#' . $params['anchor'];
		}
	}

	/**
	 * Method to get a list of files in array to be included
	 * when loaded
	 *
	 * @access	public
	 *
	 * @return	array	The list of files
	 */
	abstract public function getIncludedFiles();

	/**
	 * Method to load a plugin object by content id number
	 *
	 * @access	public
	 *
	 * @return	object	Instance of this class
	 */
	abstract public function load( $cid );

	/**
	 * Method to get content's ID
	 *
	 * @access	public
	 *
	 * @return	integer	The ID of the article
	 */
	abstract public function getContentId();

	/**
	 * Method to get content's ID based on categories
	 *
	 * @access	public
	 *
	 * @param	string/array $categories Category Ids
	 * @return	array	The IDs of the article
	 */
	abstract public function getContentIds( $categories = '' );

	/**
	 * Method to get content's title
	 *
	 * @access	public
	 *
	 * @return	string	The title of the article
	 */
	abstract public function getContentTitle();

	/**
	 * Method to get content's hits count
	 *
	 * @access	public
	 *
	 * @return	string	The hits count of the article
	 */
	abstract public function getContentHits();

	/**
	 * Method to get content's permalink
	 *
	 * @access	public
	 *
	 * @return	string	The permalik tho the article
	 */
	abstract public function getContentPermalink( $params = array() );

	/**
	 * Method to get author's ID
	 *
	 * @access	public
	 *
	 * @return	integer	The ID of the article's creator
	 */
	abstract public function getAuthorId();

	/**
	 * Method to get author's display name
	 *
	 * @access	public
	 *
	 * @return	string	The name of the article's creator
	 */
	abstract public function getAuthorName();

	/**
	 * Method to get author's avatar
	 *
	 * @access	public
	 *
	 * @return	string	The avatar of the article's creator
	 */
	abstract public function getAuthorAvatar();

	/**
	 * Method to get article's category ID.
	 * If category is not applicable, return true
	 *
	 * @access	public
	 *
	 * @return	Integer	Category ID
	 */
	abstract public function getCategoryId();

	/**
	 * Method to get a list of categories
	 *
	 * @access	public
	 *
	 * @param	array	$selected	A list of pre-selected categories.
	 * @param	array	$key		Paramater key
	 *
	 * @return	string	The html output of the select list.
	 */
	abstract public function getCategories();

	/**
	 * Method to get custom anchor link to work with comment section jump
	 *
	 * @access	public
	 *
	 * @return	string	The anchor id of the comment section.
	 */
	abstract public function getCommentAnchorId();

	/**
	 * Method to check if the current view is listing view
	 *
	 * @access	public
	 *
	 * @return	boolean	True if it is listing view
	 */
	abstract public function isListingView();

	/**
	 * Method to check if the current view is entry view
	 *
	 * @access	public
	 *
	 * @return	boolean	True if it is entry view
	 */
	abstract public function isEntryView();

	/**
	 * Prepare the data if necessary before the checking
	 *
	 * @access	public
	 *
	 * @param	string	$eventTrigger	The event trigger
	 * @param	string	$context		Context
	 * @param	object	$article		The article
	 * @param	array	$params			Parameter key
	 * @param	array	$page			Parameter key
	 * @param	array	$options		Parameter key
	 *
	 * @return	boolean	True if success
	 */
	abstract public function onBeforeLoad( $eventTrigger, $context, &$article, &$params, &$page, &$options );

	/**
	 * After the loading the content article with id
	 *
	 * @access	public
	 *
	 * @param	string	$eventTrigger	The event trigger
	 * @param	string	$context		Context
	 * @param	object	$article		The article
	 * @param	array	$params			Parameter key
	 * @param	array	$page			Parameter key
	 * @param	array	$options		Parameter key
	 *
	 * @return	boolean	True if success
	 */
	abstract public function onAfterLoad( $eventTrigger, $context, &$article, &$params, &$page, &$options );

	/**
	 * Roll back passed by reference
	 *
	 * @access	public
	 *
	 * @param	string	$eventTrigger	The event trigger
	 * @param	string	$context		Context
	 * @param	object	$article		The article
	 * @param	array	$params			Parameter key
	 * @param	array	$page			Parameter key
	 * @param	array	$options		Parameter key
	 *
	 * @return	boolean	True if success
	 */
	abstract public function onRollBack( $eventTrigger, $context, &$article, &$params, &$page, &$options );

	/**
	 * Method to append the comment to the article
	 *
	 * @access	public
	 *
	 * @param	object	$article	The article object
	 * @param	string	$html		The comment in HTML
	 * @param	string	$view		The current view
	 * @param	array	$options	Parameter key
	 *
	 * @return	void
	 */
	abstract public function onExecute( &$article, $html, $view, $options = array() );
}
