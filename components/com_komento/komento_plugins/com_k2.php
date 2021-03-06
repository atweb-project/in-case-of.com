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

class KomentoComk2 extends KomentoExtension
{
	/**
	 * Parameters
	 *
	 * @var    mixed
	 */
	public $component = 'com_k2';

	/**
	 * Article object
	 *
	 * @var    mixed
	 */
	private $_item;


	private $_currentTrigger = '';

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
		$entryTrigger = '';

		if( $this->isEntryView() )
		{
			$entryTrigger = 'onK2CommentsBlock';
		}
		//elseif( $this->isListingView() )
		else
		{
			$entryTrigger = 'onK2CommentsCounter';
		}

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
		array_push( $files, JPATH_ROOT . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS .'route.php' );

		if( !class_exists('K2HelperPermissions' ))
		{
			array_push( $files, JPATH_ROOT . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS .'permissions.php' );
		}

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
			$query	= 'SELECT a.*, c.alias AS category_alias'
					. ' FROM ' . $db->nameQuote( '#__k2_items' ) . ' AS a'
					. ' LEFT JOIN ' . $db->nameQuote( '#__k2_categories')  . ' AS c ON c.id = a.catid'
					. ' WHERE a.id' . '=' . $db->quote($cid);
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
			$query = 'SELECT `id` FROM ' . $db->nameQuote( '#__k2_items' ) . ' ORDER BY `id`';
		}
		else
		{
			if( is_array( $categories ) )
			{
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT `id` FROM ' . $db->nameQuote( '#__k2_items' ) . ' WHERE `catid` IN (' . $categories . ') ORDER BY `id`';
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
		$link = '';

		if( Komento::client() == 'site' )
		{
			$link = K2HelperRoute::getItemRoute($this->_item->id.':'.urlencode($this->_item->alias), $this->_item->catid.':'.urlencode($this->_item->category_alias));
			$link = urldecode(JRoute::_($link));
		} else {
			$link = 'index.php?option=com_k2&view=item&id=' . $this->_item->id . $this->_getItemId();
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

		if( !is_array( $selected ) )
		{
			$selected	= explode( ',' , $selected );
		}

		$html	= '';

		$db		= JFactory::getDbo();
		$query	= 'SELECT a.id, a.name AS title, a.parent AS parent_id, a.name, a.parent'
				. ' FROM `#__k2_categories` AS a'
				. ' WHERE a.trash = 0'
				. ' ORDER BY a.ordering';
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
		return 'itemCommentsAnchor';
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
		// $views = array('itemlist', 'categories', 'blogger', 'teamblog', 'featured', 'myblog');

		// return in_array(JRequest::getCmd('view'), $views);

		// always true for onK2CommentsCounter
		if( $this->_currentTrigger == 'onK2CommentsCounter' )
		{
			return true;
		}
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
		return JRequest::getCmd('view') == 'item';
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
		$this->_currentTrigger = $eventTrigger;

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
		if( $options['trigger'] == 'onK2CommentsCounter' )
		{
			// Try to integrate with K2's comment counter
			$model	= Komento::getModel( 'comments' );
			$count	= $model->getCount( $this->getComponentName(), $this->getContentId() );
			$article->numOfComments = $count;
		}

		if( $view == 'entry' && $options['trigger'] == 'onK2CommentsBlock' )
		{
			// Try to integrate with K2's comment counter
			$model	= Komento::getModel( 'comments' );
			$count	= $model->getCount( $this->getComponentName(), $this->getContentId() );
			$article->numOfComments = $count;

			return $html;
		}
	}

	private function _getItemId()
	{
		$menus = JApplication::getMenu('site');
		$component = JComponentHelper::getComponent('com_k2');

		if(K2_JVERSION=='16'){
			$items = $menus->getItems('component_id', $component->id);
		} else {
			$items = $menus->getItems('componentid', $component->id);
		}

		if( count($items) == 1 )
		{
			return '&Itemid=' . $items[0]->id;
		}

		$match = null;

		foreach ($items as $item)
		{
			if ((@$item->query['task'] == 'category') && (@$item->query['id'] == $this->_item->catid))
			{
				$match = $item;
			}
			else
			{
				if(!isset($item->K2Categories)) {
					if(K2_JVERSION == '15') {
						$menuparams = explode("\n", $item->params);
						foreach($menuparams as $param) {
							if(strpos($param, 'categories=')===0) {
								$array = explode('categories=', $param);
								$item->K2Categories = explode('|', $array[1]);
							}
						}
					} else {
						$menuparams = json_decode($item->params);
						$item->K2Categories = isset($menuparams->categories)? $menuparams->categories: array();
					}
				}
				if(isset($item->K2Categories) && is_array($item->K2Categories)) {
					foreach ($item->K2Categories as $catid)	{
						if ((@$item->query['view'] == 'itemlist') && (@$item->query['task'] == '') && (@(int)$catid == $id)) {
							$match = $item;
							break;
						}
					}
				}
			}
		}

		if( $match )
		{
			return '&Itemid=' . $item->id;
		}
		else
		{
			return '';
		}
	}
}
