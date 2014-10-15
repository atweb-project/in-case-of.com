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

class KomentoComredshop extends KomentoExtension
{
	/**
	 * Parameters
	 *
	 * @var    mixed
	 */
	public $component = 'com_redshop';

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
		return 'onAfterDisplayProduct';
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
		return array();
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
			$query	= 'SELECT p.*, c.category_id, c.category_name, c.category_back_full_image, c.category_full_image, m.manufacturer_name, pcx.ordering '
					. ' FROM `#__redshop_product` AS p'
					. ' LEFT JOIN `#__redshop_product_category_xref` AS pcx ON pcx.product_id = p.product_id'
					. ' LEFT JOIN `#__redshop_manufacturer` AS m ON m.manufacturer_id = p.manufacturer_id'
					. ' LEFT JOIN `#__redshop_category` AS c ON c.category_id = pcx.category_id'
					. ' WHERE p.product_id = ' . $db->quote( $cid );
					//. ' AND pcx.category_id = ' . $db->quote( JRequest::getInt('cid', 0) );
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
		return $this->_item->product_id;
	}

	/**
	 * Method to get content's ID based on categories
	 *
	 * @access	public
	 *
	 * @return	array	The IDs of the article
	 */
	public function getContentIds( $categories = '' )
	{
		$db		= JFactory::getDbo();
		$query = '';

		if( empty( $categories ) )
		{
			$query = 'SELECT `product_id` FROM ' . $db->nameQuote( '#__redshop_product_category_xref' ) . ' ORDER BY `product_id`';
		}
		else
		{
			if( is_array( $categories ) )
			{
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT `product_id` FROM ' . $db->nameQuote( '#__redshop_product_category_xref' ) . ' WHERE `category_id` IN (' . $categories . ') ORDER BY `product_id`';
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
		return $this->_item->product_name;
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
		return $this->_item->visited;
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
		$link = 'index.php?opiton=com_redshop&view=product&pid=' . $this->_item->product_id . '&cid=' . $this->_item->category_id;

		if( Komento::client() == 'site' )
		{
			$link	= JRoute::_( $link );
		}

		$this->prepareLink( $link, $params );

		return $link;
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
		return '';
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
		return '';
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
		return $this->_item->category_id;
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
		if( !is_array( $selected ) )
		{
			$selected	= explode( ',' , $selected );
		}

		$html	= '';

		$db		= JFactory::getDbo();
		$query	= 'SELECT c.category_id, cx.category_child_id, cx.category_child_id AS id, cx.category_parent_id,'
				. ' cx.category_parent_id AS parent_id, c.category_name, c.category_name AS title,'
				. ' c.category_description, c.published,ordering,'
				. ' c.category_name AS name, cx.category_parent_id AS parent'
				. ' FROM `#__redshop_category` AS c,'
				. ' `#__redshop_category_xref` AS cx'
				. ' WHERE c.category_id = cx.category_child_id'
				. ' ORDER BY c.ordering';

		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		if( count($rows) )
		{
			$children = array();

			foreach ($rows as $v )
			{
				$pt		= $v->parent_id;
				$list	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}

			$treelist	= JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );

			$categories	= array();

			foreach ($treelist as $row)
			{
				$categories[] = JHtml::_('select.option', $row->id, $row->treename);
			}

			$html	= JHTML::_( 'select.genericlist' , $categories , $key , 'multiple="multiple" size="10" style="height: auto !important;"' , 'value' , 'text' , $selected );
		}

		return $html;
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
		$views = array('featured', 'category', 'categories', 'archive' );

		return in_array(JRequest::getCmd('view'), $views);
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
		return JRequest::getCmd('view') == 'product';
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
		// @task: variable type check
		if( !is_string( $article ) || !is_object( $params ) || !is_object( $page ) )
		{
			return false;
		}

		// @task: prepare everything else before execute
		$obj		= new stdClass;
		$obj->text	= $article;
		$obj->introtext	= '';
		$obj->id	= JRequest::getInt( 'pid' );
		$article	= $obj;

		$this->_item = $page;

		return true;
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
		$article = $article->text;

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
		if( $view == 'listing' )
		{
			$article->introtext	.= $html;
			$article = $article->introtext;
			return $html;
		}

		if( $view == 'entry' )
		{
			$article->text	.= $html;
			$article = $article->text;
			return $html;
		}
	}
}
