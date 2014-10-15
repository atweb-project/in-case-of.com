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

class KomentoComvirtuemart extends KomentoExtension
{
	/**
	 * Parameters
	 *
	 * @var    mixed
	 */
	public $component = 'com_virtuemart';

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
		//JTable::addIncludePath( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_k2' . DS . 'tables' );

		$files = array();
		array_push( $files, JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS .'vmtable.php' );
		array_push( $files, JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS .'config.php' );

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
			defined('JPATH_VM_ADMINISTRATOR') or define('JPATH_VM_ADMINISTRATOR', JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart');

			JTable::addIncludePath( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_virtuemart' . DS . 'tables' );
			$product	= JTable::getInstance( 'Products', 'Table' );

			if( !$product->load($cid) )
			{
				return false;
			}

			$instances[$cid] = $product;
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
		return $this->_item->virtuemart_product_id;
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
			$query = 'SELECT `virtuemart_product_id` FROM ' . $db->nameQuote( '#__virtuemart_product_categories' ) . ' ORDER BY `virtuemart_product_id`';
		}
		else
		{
			if( is_array( $categories ) )
			{
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT `virtuemart_product_id` FROM ' . $db->nameQuote( '#__virtuemart_product_categories' ) . ' WHERE `virtuemart_category_id` IN (' . $categories . ') ORDER BY `virtuemart_product_id`';
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
		$db		= JFactory::getDbo();
		$query	= 'SELECT `virtuemart_category_id` FROM `#__virtuemart_product_categories` WHERE `virtuemart_product_id` = ' . $db->quote( $this->getContentId() );
		$db->setQuery( $query );
		$productCategory = $db->loadResult();

		$productCategory ? $productCategory : JRequest::getInt( 'virtuemart_category_id', 0 );

		$link	= 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$this->_item->virtuemart_product_id.'&virtuemart_category_id='.$productCategory;

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
		return $this->_item->created_by ? $this->_item->created_by : $this->_item->modified_by;
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
		$user	= JFactory::getUser( $this->getAuthorId() );
		return $user->name;
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
		$db	= JFactory::getDbo();
		$query	= 'SELECT `virtuemart_category_id` FROM `#__virtuemart_product_categories` WHERE `virtuemart_product_id` = ' . $db->quote( $this->getContentId() );
		$db->setQuery( $query );

		$productCategory = $db->loadResult();

		return $productCategory;
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
		$query	= 'SELECT c.`virtuemart_category_id` AS id, l.`category_name` AS title, cx.`category_parent_id` AS parent_id,'
				. ' l.`category_name` AS name, cx.`category_parent_id` AS parent'
				. ' FROM `#__virtuemart_categories_en_gb` l '
				. ' JOIN `#__virtuemart_categories` AS c using (`virtuemart_category_id`)'
				. ' LEFT JOIN `#__virtuemart_category_categories` AS cx ON l.`virtuemart_category_id` = cx.`category_child_id`'
				. ' ORDER BY c.`ordering`';
		$db	= JFactory::getDbo();
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
		$views = array('virtuemart', 'category');

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
		return JRequest::getCmd('view') == 'productdetails';
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
		if( !is_object($article) && !property_exists($article, 'id') )
		{
			return false;
		}

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
		// introtext, text, excerpt, intro, content
		if( $view == 'listing' )
		{
			return $html;
		}

		if( $view == 'entry' )
		{
			return $html;
		}
	}
}
