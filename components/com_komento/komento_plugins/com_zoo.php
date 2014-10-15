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

class KomentoComzoo extends KomentoExtension
{
	/**
	 * Parameters
	 *
	 * @var    mixed
	 */
	public $component = 'com_zoo';

	/**
	 * Article object
	 *
	 * @var    mixed
	 */
	private $_item = null;


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
		return false;
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
		$files		= array();
		$files[]	= JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_zoo' . DS . 'config.php';
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

		// @task: If we don't have the id, we really can't do anything here.
		if( !$cid )
		{
			return false;
		}

		if( is_null($instances) )
		{
			$instances = array();
		}

		if( !array_key_exists($cid, $instances) )
		{
			$zooApp = App::getInstance('zoo');
			$this->_item = $zooApp->table->item->get($cid);

			if( !$this->_item)
			{
				return false;
			}

			// include file class JSite for $zooApp->object->create('JSite');
			include_once( JPATH_ROOT . DS . 'includes' . DS . 'application.php' );

			$this->_item->link	= JRoute::_($zooApp->route->item($this->_item));


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
			$query = 'SELECT DISTINCT `item_id` FROM ' . $db->nameQuote( '#__zoo_category_item' ) . ' ORDER BY `item_id`';
		}
		else
		{
			if( is_array( $categories ) )
			{
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT DISTINCT `item_id` FROM ' . $db->nameQuote( '#__zoo_category_item' ) . ' WHERE `category_id` IN (' . $categories . ') ORDER BY `item_id`';
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
		return $this->_item->name;
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
		$link	= $this->_item->link;

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
		return $this->_item->created_by_alias ? $this->_item->created_by_alias : $this->_item->author->name;
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
		return $this->_item->author->link;
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
		return $this->_item->author->avatar;
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
		return $this->_item->getParams()->get('config.primary_category', null);
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
		$query	= 'SELECT c.id, c.name AS title, c.parent AS parent_id, c.name, c.parent'
				. ' FROM `#__zoo_category` as c USE INDEX (APPLICATIONID_ID_INDEX)'
				. ' ORDER BY c.ordering';
		$db->setQuery( $query );
		$rows	= $db->loadObjectList();

		if(count($rows))
		{
			$children = array();

			foreach ($rows as $v)
			{
				$pt		= $v->parent_id;
				$list	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}

			$treelist	= JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

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
		$views	= array('frontpage', 'category', 'alphaindex', 'tag');

		if( in_array(JRequest::getCmd('view'), $views) )
		{
			return true;
		}

		if( in_array(JRequest::getCmd('task'), $views) )
		{
			return true;
		}

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
		$task		= JRequest::getCmd('task');
		$view 		= JRequest::getVar( 'view' );

		if( $task == 'item' || $view == 'item' )
		{
			return true;
		}
		return false;
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
			$article->text	.= $html;
			return $html;
		}

		if( $view == 'entry' )
		{
			$article->text	.= $html;
			return $html;
		}
	}
}
