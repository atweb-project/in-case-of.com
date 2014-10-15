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

require_once( dirname( __FILE__ ) . DS . 'abstract.php' );

class KomentoComMtree extends KomentoExtension
{
	/**
	 * Parameters
	 *
	 * @var    mixed
	 */
	public $component = 'com_mtree';

	/**
	 * Article object
	 *
	 * @var    mixed
	 */
	private $_item;


	/**
	 * Method to get the default event trigger
	 * when loaded
	 *
	 * @access	public
	 *
	 * @return	mixed	The event triggers
	 */
	public function getEventTrigger()
	{
		$entryTrigger = ( Komento::joomlaVersion() > '1.5' ) ? 'onContentAfterDisplay' : 'onAfterDisplayContent';

		return $entryTrigger;
	}

	/**
	 * Method to get a list of files in array to be included
	 * when loaded
	 *
	 * @access	public
	 *
	 * @return	array	The list of files
	 */
	public function getIncludedFiles()
	{
		$files = array();
		array_push( $files, JPATH_ROOT . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS .'route.php' );

		return $files;
	}

	/**
	 * Method to load a plugin object by content id number
	 *
	 * @access	public
	 *
	 * @return	object	Instance of this class
	 */
	public function load( $cid )
	{
		static $instances = null;

		if( is_null($instances) )
		{
			$instances = array();
		}

		if( !array_key_exists($cid, $instances) )
		{
			$db		= JFactory::getDbo();
			$query	= 'SELECT a.`link_id` AS `id`, a.`link_name` AS `title`, a.`alias`, c.`cat_id` AS `catid`, a.`user_id` AS `created_by`, a.`link_hits` AS `hits` , '
					. ' c.`cat_name` AS category_title, c.`alias` AS category_alias,'
					. ' u.`name` AS author'
					. ' FROM ' . $db->nameQuote( '#__mt_links') . ' AS a'
					. ' INNER JOIN ' . $db->nameQuote( '#__mt_cl' ) . ' AS b ON a.`link_id` = b.`link_id`'
					. ' LEFT JOIN ' . $db->nameQuote( '#__mt_cats' ) . ' AS c ON b.`cat_id` = c.`cat_id`'
					. ' LEFT JOIN ' . $db->nameQuote( '#__users') . ' AS u ON u.id = a.`user_id`'
					. ' WHERE a.`link_id` = ' . $db->quote( (int) $cid );
			$db->setQuery( $query );

			if( !$this->_item = $db->loadObject() )
			{
				return false;
			}

			$instances[$cid] = $this->_item;
		}

		$this->_item = $instances[$cid];

		return $this;
	}

	/**
	 * Method to get content's ID
	 *
	 * @access	public
	 *
	 * @return	integer	The ID of the article
	 */
	public function getContentId()
	{
		return $this->_item->id;
	}

	/**
	 * Method to get content's ID based on categories
	 *
	 * @access	public
	 *
	 * @param	string/array $categories Category Ids
	 * @return	array	The IDs of the article
	 */
	public function getContentIds( $categories = '' )
	{
		$db		= JFactory::getDbo();
		$query = '';

		if( empty( $categories ) )
		{
			$query = 'SELECT `id` FROM ' . $db->nameQuote( '#__content' ) . ' ORDER BY `id`';
		}
		else
		{
			if( is_array( $categories ) )
			{
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT `id` FROM ' . $db->nameQuote( '#__content' ) . ' WHERE `catid` IN (' . $categories . ') ORDER BY `id`';
		}

		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	/**
	 * Method to get content's title
	 *
	 * @access	public
	 *
	 * @return	string	The title of the article
	 */
	public function getContentTitle()
	{
		return $this->_item->title;
	}

	/**
	 * Method to get content's hits count
	 *
	 * @access	public
	 *
	 * @return	string	The hits count of the article
	 */
	public function getContentHits()
	{
		return $this->_item->hits;
	}

	/**
	 * Method to get content's permalink
	 *
	 * @access	public
	 *
	 * @return	string	The permalik tho the article
	 */
	public function getContentPermalink( $params = array() )
	{
		$link 	= 'index.php?option=com_mtree&task=viewlink&link_id=' . $this->_item->id;

		if( Komento::client() == 'site' )
		{
			$link = JRoute::_( $link );
		}

		$this->prepareLink( $link, $params );

		return $link;

		//return JRoute::_(ContentHelperRoute::getArticleRoute($this->_item->slug, $this->_item->catslug, $this->_item->sectionid));
	}

	/**
	 * Method to get author's ID
	 *
	 * @access	public
	 *
	 * @return	integer	The ID of the article's creator
	 */
	public function getAuthorId()
	{
		return $this->_item->created_by;
	}

	/**
	 * Method to get author's display name
	 *
	 * @access	public
	 *
	 * @return	string	The name of the article's creator
	 */
	public function getAuthorName()
	{
		return $this->_item->created_by_alias ? $this->_item->created_by_alias : $this->_item->author;
	}

	/**
	 * Method to get author's permalink
	 *
	 * @access	public
	 *
	 * @return	string	The permalink to the article's creator
	 */
	public function getAuthorPermalink()
	{
		return '';
	}

	/**
	 * Method to get author's avatar
	 *
	 * @access	public
	 *
	 * @return	string	The avatar of the article's creator
	 */
	public function getAuthorAvatar()
	{
		return '';
	}

	/**
	 * Method to get article's category ID
	 *
	 * @access	public
	 *
	 * @return	Integer	Category ID
	 */
	public function getCategoryId()
	{
		return $this->_item->catid;
	}

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
	public function getCategories( $selected = array() , $key = 'category' )
	{
		jimport( 'joomla.html.html.category' );

		if( !is_array( $selected ) )
		{
			$selected	= explode( ',' , $selected );
		}

		$db		= JFactory::getDbo();
		$query	= 'SELECT a.`cat_id` AS `id`, a.`cat_name` AS `title` , a.`cat_parent` AS `parent_id`'
				. ' FROM `#__mt_cats` AS a'
				. ' WHERE a.`cat_parent` > 0'
				. ' ORDER BY a.`lft`';

		$db->setQuery( $query );
		$rows	= $db->loadObjectList();

		$categories	= array();

		foreach ($rows as &$item)
		{
			$categories[] = JHtml::_('select.option', $item->id, $item->title);
		}

		$select		= JHTML::_( 'select.genericlist' , $categories , $key , 'multiple="multiple" size="10" style="height: auto !important;"' , 'value' , 'text' , $selected );

		return $select;
	}

	/**
	 * Method to get custom anchor link to work with comment section jump
	 *
	 * @access	public
	 *
	 * @return	string	The anchor id of the comment section.
	 */
	public function getCommentAnchorId()
	{
		return '';
	}

	/**
	 * Method to check if the current view is listing view
	 *
	 * @access	public
	 *
	 * @return	boolean	True if it is listing view
	 */
	public function isListingView()
	{
		return false;
	}

	/**
	 * Method to check if the current view is entry view
	 *
	 * @access	public
	 *
	 * @return	boolean	True if it is entry view
	 */
	public function isEntryView()
	{
		return JRequest::getCmd('task') == 'viewlink';
	}

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
	public function onBeforeLoad( $eventTrigger, $context, &$article, &$params, &$page, &$options )
	{
		return false;
	}

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
	public function onAfterLoad( $eventTrigger, $context, &$article, &$params, &$page, &$options )
	{
		return true;
	}

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
	public function onRollBack( $eventTrigger, $context, &$article, &$params, &$page, &$options )
	{
		return true;
	}

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
	public function onExecute( &$article, $html, $view, $options = array() )
	{
		if( $view == 'entry' )
		{
			return $html;
		}
	}
}
