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
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class KomentoViewMigrators extends JView
{
	protected $ajax;
	protected $model;
	protected $kmtmodel;
	protected $component;
	protected $cid;

	function __construct()
	{
		$this->ajax = Komento::getHelper( 'ajax' );
		$this->model = Komento::getModel( 'migrators', true )->getMigrator( JRequest::getString( 'type' ) );
		$this->kmtmodel = Komento::getModel( 'comments' );
	}

	function getMigrator()
	{
		$type = JRequest::getString( 'type' );
		$function = JRequest::getString( 'function' );

		$classname = 'KomentoViewMigrator' . $type;

		$class = new $classname();

		return $class->$function();
	}
}

class KomentoViewMigratorSliComments extends KomentoViewMigrators
{
	function getStatistic()
	{
		$this->ajax->log( 'Getting statistics' );

		$params = JRequest::getVar( 'params' );
		$categories = $params['categories'];

		$options = array( 'categories' => $categories );

		$cids = $this->model->getUniquePostId( $options );
		$totalComments = $this->model->getCommentCount( array( 'article_id' => $cids ) );

		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_TOTAL_ARTICLES' ) . ': ' . count( $cids ) );
		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_TOTAL_COMMENTS' ) . ': ' . $totalComments );

		$this->ajax->success( $cids, $totalComments );
		$this->ajax->send();
	}

	function migrate()
	{
		$params = JRequest::getVar( 'params' );
		$this->component = 'com_content';
		$this->model->component = 'com_content';
		$this->cid = $params['cid'];
		$this->model->cid = $params['cid'];

		$this->model->publishingState = $params['publishingState'];

		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_COMPONENT' ) . ': ' . $this->component );
		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_ARTICLE_ID' ) . ': ' . $this->cid );

		$options = array(
			'article_id'	=> $this->cid
		);

		$parents = $this->model->getComments( $options );

		$break = 0;

		foreach( $parents as $parent )
		{
			$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_PARENT' ) . ': ' . $parent->id );

			$komentoInsertNode = false;

			if( $break == 0 )
			{
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_CHECKING_FOR_INSERTION_NODE' ) );
				$komentoInsertNode = $this->model->getKomentoInsertNode( $parent->created );
			}

			$base = 1;

			if( $break == 0 && $komentoInsertNode )
			{
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTION_NODE_FOUND' ) . ': ' . $komentoInsertNode->id );
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTING_COMMENTS' ) );

				$base = $komentoInsertNode->lft;
				$diff = 2;

				// $totalChilds = $this->model->getCommentCount( array( 'object_group' => $this->component, 'object_id' => $this->cid, 'thread_id' => $parent->id ) );

				$this->model->pushKomentoComment( $base, $diff );
			}
			else
			{
				if( $break == 0 )
				{
					$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTION_NODE_NOT_FOUND' ) );
				}

				// all comments in EB are later than kmt comments
				// set break == 1, this means all subsequent parents does not need to check insert node
				$break = 1;

				$komentoLatestComment = $this->kmtmodel->getLatestComment( $this->component, $this->cid );

				if( $komentoLatestComment )
				{
					// get the last rgt in kmt and append EB comments
					$base = $komentoLatestComment->rgt + 1;
				}
			}

			// reset it to parent_id = 0 since this section is all parent comment
			$parent->parent_id = 0;
			$parent->lft = $base;
			$parent->rgt = $base + 1;
			$new = $this->model->save( $parent );

			if( $new === false )
			{
				$this->ajax->fail( 'save:' . $parent->id );
				$this->ajax->send();
				return false;
			}
		}

		$count = $this->model->getCommentCount( array( 'itemID' => $this->cid ) );
		$this->ajax->update( $count );
		$this->ajax->success();
		$this->ajax->send();
	}
}

class KomentoViewMigratorK2 extends KomentoViewMigrators
{
	function clearComments()
	{
		$db = JFactory::getDBO();
		$db->setQuery( 'DELETE FROM `#__komento_comments` WHERE `component` = ' . $db->quote( 'com_k2' ) );
		$db->query();

		$this->ajax->success();
		$this->ajax->send();
	}

	function getStatistic()
	{
		$this->ajax->log( 'Getting statistics' );

		$params = JRequest::getVar( 'params' );
		$categories = $params['categories'];

		$options = array( 'categories' => $categories );

		$cids = $this->model->getUniquePostId( $options );
		$totalComments = $this->model->getCommentCount( array( 'itemID' => $cids ) );

		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_TOTAL_ARTICLES' ) . ': ' . count( $cids ) );
		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_TOTAL_COMMENTS' ) . ': ' . $totalComments );

		$this->ajax->success( $cids, $totalComments );
		$this->ajax->send();
	}

	function migrate()
	{
		$params = JRequest::getVar( 'params' );
		$this->component = 'com_k2';
		$this->model->component = 'com_k2';
		$this->cid = $params['cid'];
		$this->model->cid = $params['cid'];

		$this->model->publishingState = $params['publishingState'];

		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_COMPONENT' ) . ': ' . $this->component );
		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_ARTICLE_ID' ) . ': ' . $this->cid );

		$options = array(
			'itemID'	=> $this->cid
		);

		$parents = $this->model->getComments( $options );

		$break = 0;

		foreach( $parents as $parent )
		{
			$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_PARENT' ) . ': ' . $parent->id );

			$komentoInsertNode = false;

			if( $break == 0 )
			{
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_CHECKING_FOR_INSERTION_NODE' ) );
				$komentoInsertNode = $this->model->getKomentoInsertNode( $parent->commentDate );
			}

			$base = 1;

			if( $break == 0 && $komentoInsertNode )
			{
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTION_NODE_FOUND' ) . ': ' . $komentoInsertNode->id );
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTING_COMMENTS' ) );

				$base = $komentoInsertNode->lft;
				$diff = 2;

				// $totalChilds = $this->model->getCommentCount( array( 'object_group' => $this->component, 'object_id' => $this->cid, 'thread_id' => $parent->id ) );

				$this->model->pushKomentoComment( $base, $diff );
			}
			else
			{
				if( $break == 0 )
				{
					$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTION_NODE_NOT_FOUND' ) );
				}

				// all comments in EB are later than kmt comments
				// set break == 1, this means all subsequent parents does not need to check insert node
				$break = 1;

				$komentoLatestComment = $this->kmtmodel->getLatestComment( $this->component, $this->cid );

				if( $komentoLatestComment )
				{
					// get the last rgt in kmt and append EB comments
					$base = $komentoLatestComment->rgt + 1;
				}
			}

			// reset it to parent_id = 0 since this section is all parent comment
			$parent->parent_id = 0;
			$parent->lft = $base;
			$parent->rgt = $base + 1;
			$new = $this->model->save( $parent );

			if( $new === false )
			{
				$this->ajax->fail( 'save:' . $parent->id );
				$this->ajax->send();
				return false;
			}
		}

		$count = $this->model->getCommentCount( array( 'itemID' => $this->cid ) );
		$this->ajax->update( $count );
		$this->ajax->success();
		$this->ajax->send();
	}
}

class KomentoViewMigratorZoo extends KomentoViewMigrators
{
	function clearComments()
	{
		$db = JFactory::getDBO();
		$db->setQuery( 'DELETE FROM `#__komento_comments` WHERE `component` = ' . $db->quote( 'com_zoo' ) );
		$db->query();

		$this->ajax->success();
		$this->ajax->send();
	}

	function getStatistic()
	{
		$this->ajax->log( 'Getting statistics' );

		$params = JRequest::getVar( 'params' );
		$categories = $params['categories'];

		$options = array( 'categories' => $categories );

		$cids = $this->model->getUniquePostId( $options );
		$totalComments = $this->model->getCommentCount( array( 'item_id' => $cids ) );

		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_TOTAL_ARTICLES' ) . ': ' . count( $cids ) );
		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_TOTAL_COMMENTS' ) . ': ' . $totalComments );

		$this->ajax->success( $cids, $totalComments );
		$this->ajax->send();
	}

	function migrate()
	{
		$params = JRequest::getVar( 'params' );
		$this->component = 'com_zoo';
		$this->model->component = 'com_zoo';
		$this->cid = $params['cid'];
		$this->model->cid = $params['cid'];

		$this->model->publishingState = $params['publishingState'];

		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_COMPONENT' ) . ': ' . $this->component );
		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_ARTICLE_ID' ) . ': ' . $this->cid );

		$options = array(
			'parent_id'	=> 0,
			'item_id'	=> $this->cid
		);

		$parents = $this->model->getComments( $options );

		$break = 0;

		foreach( $parents as $parent )
		{
			$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_PARENT' ) . ': ' . $parent->id );

			$komentoInsertNode = false;

			if( $break == 0 )
			{
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_CHECKING_FOR_INSERTION_NODE' ) );
				$komentoInsertNode = $this->model->getKomentoInsertNode( $parent->created );
			}

			$base = 1;

			if( $break == 0 && $komentoInsertNode )
			{
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTION_NODE_FOUND' ) . ': ' . $komentoInsertNode->id );
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTING_COMMENTS' ) );

				$base = $komentoInsertNode->lft;
				$diff = 2;

				// $totalChilds = $this->model->getCommentCount( array( 'object_group' => $this->component, 'object_id' => $this->cid, 'thread_id' => $parent->id ) );

				$this->model->pushKomentoComment( $base, $diff );
			}
			else
			{
				if( $break == 0 )
				{
					$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTION_NODE_NOT_FOUND' ) );
				}

				// all comments in EB are later than kmt comments
				// set break == 1, this means all subsequent parents does not need to check insert node
				$break = 1;

				$komentoLatestComment = $this->kmtmodel->getLatestComment( $this->component, $this->cid );

				if( $komentoLatestComment )
				{
					// get the last rgt in kmt and append EB comments
					$base = $komentoLatestComment->rgt + 1;
				}
			}

			// reset it to parent_id = 0 since this section is all parent comment
			$parent->parent_id = 0;
			$parent->lft = $base;
			$parent->rgt = $base + 1;
			$new = $this->model->save( $parent );

			if( $new === false )
			{
				$this->ajax->fail( 'save:' . $parent->id );
				$this->ajax->send();
				return false;
			}

			if( $this->saveChildren( $parent->id, $new->id ) === false )
			{
				$this->ajax->fail( 'savechildren:' . $parent->id );
				$this->ajax->send();
				return false;
			}
		}

		$count = $this->model->getCommentCount( array( 'item_id' => $this->cid ) );
		$this->ajax->update( $count );
		$this->ajax->success();
		$this->ajax->send();
	}

	private function saveChildren( $oldid, $newid )
	{
		$options = array(
			'parent_id'	=> $oldid,
			'item_id'	=> $this->cid
		);

		$children = $this->model->getComments( $options );

		foreach( $children as $child )
		{
			// populate child comment's lft rgt
			$child = $this->model->populateChildBoundaries( $child, $newid );
			$child->parent_id = $newid;

			$new = $this->model->save( $child );

			if( $new === false )
			{
				$this->ajax->fail( 'save:' . $child->id );
				$this->ajax->send();
				return false;
			}

			if( $this->saveChildren( $child->id, $new->id ) === false )
			{
				$this->ajax->fail( 'savechildren:' . $child->id );
				$this->ajax->send();
				return false;
			}
		}

		return true;
	}
}

class KomentoViewMigratorCJComment extends KomentoViewMigrators
{
	function clearComments()
	{
		$db = JFactory::getDBO();
		$db->setQuery( 'DELETE FROM `#__komento_comments`' );
		$db->query();

		$this->ajax->success();
		$this->ajax->send();
	}

	function getStatistic()
	{
		$this->ajax->log( 'Getting statistics' );

		$params = JRequest::getVar( 'params' );

		$components = $params['components'];
		$cids = array();
		$totalCids = 0;
		$totalComments = 0;

		foreach( $components as $component )
		{
			$options = array( 'component' => $component );

			$tmp = $this->model->getUniquePostId( $options );
			$totalCids += count( $tmp );
			$totalComments += $this->model->getCommentCount( $options );

			foreach( $tmp as $t )
			{
				$cids[] = array( 'component' => $component, 'cid' => $t );
			}
		}

		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_TOTAL_COMPONENTS' ) . ': ' . count( $components ) );
		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_TOTAL_ARTICLES' ) . ': ' . $totalCids );
		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_TOTAL_COMMENTS' ) . ': ' . $totalComments );

		$this->ajax->success( $cids, $totalComments );
		$this->ajax->send();
	}

	function migrate()
	{
		$params = JRequest::getVar( 'params' );
		$this->component = $params['component'];
		$this->model->component = $params['component'];
		$this->cid = $params['cid'];
		$this->model->cid = $params['cid'];

		$this->model->publishingState = $params['publishingState'];

		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_COMPONENT' ) . ': ' . $this->component );
		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_ARTICLE_ID' ) . ': ' . $this->cid );

		$options = array(
			'parentid'	=> 0,
			'component'	=> $this->component,
			'contentid'	=> $this->cid
		);

		$parents = $this->model->getComments( $options );

		$break = 0;

		foreach( $parents as $parent )
		{
			$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_PARENT' ) . ': ' . $parent->id );

			$komentoInsertNode = false;

			if( $break == 0 )
			{
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_CHECKING_FOR_INSERTION_NODE' ) );
				$komentoInsertNode = $this->model->getKomentoInsertNode( $parent->date );
			}

			$base = 1;

			if( $break == 0 && $komentoInsertNode )
			{
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTION_NODE_FOUND' ) . ': ' . $komentoInsertNode->id );
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTING_COMMENTS' ) );

				$base = $komentoInsertNode->lft;
				$diff = 2;

				// $totalChilds = $this->model->getCommentCount( array( 'object_group' => $this->component, 'object_id' => $this->cid, 'thread_id' => $parent->id ) );

				$this->model->pushKomentoComment( $base, $diff );
			}
			else
			{
				if( $break == 0 )
				{
					$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTION_NODE_NOT_FOUND' ) );
				}

				// all comments in EB are later than kmt comments
				// set break == 1, this means all subsequent parents does not need to check insert node
				$break = 1;

				$komentoLatestComment = $this->kmtmodel->getLatestComment( $this->component, $this->cid );

				if( $komentoLatestComment )
				{
					// get the last rgt in kmt and append EB comments
					$base = $komentoLatestComment->rgt + 1;
				}
			}

			// reset it to parent_id = 0 since this section is all parent comment
			$parent->parentid = 0;
			$parent->lft = $base;
			$parent->rgt = $base + 1;
			$new = $this->model->save( $parent );

			if( $new === false )
			{
				$this->ajax->fail( 'save:' . $parent->id );
				$this->ajax->send();
				return false;
			}

			if( $this->saveChildren( $parent->id, $new->id ) === false )
			{
				$this->ajax->fail( 'savechildren:' . $parent->id );
				$this->ajax->send();
				return false;
			}
		}

		$count = $this->model->getCommentCount( array( 'component' => $this->component, 'contentid' => $this->cid ) );
		$this->ajax->update( $count );
		$this->ajax->success();
		$this->ajax->send();
	}

	private function saveChildren( $oldid, $newid )
	{
		$options = array(
			'parentid'	=> $oldid,
			'component'	=> $this->component,
			'contentid'	=> $this->cid
		);

		$children = $this->model->getComments( $options );

		foreach( $children as $child )
		{
			// populate child comment's lft rgt
			$child = $this->model->populateChildBoundaries( $child, $newid );
			$child->parentid = $newid;

			$new = $this->model->save( $child );

			if( $new === false )
			{
				$this->ajax->fail( 'save:' . $child->id );
				$this->ajax->send();
				return false;
			}

			if( $this->saveChildren( $child->id, $new->id ) === false )
			{
				$this->ajax->fail( 'savechildren:' . $child->id );
				$this->ajax->send();
				return false;
			}
		}

		return true;
	}
}

class KomentoViewMigratorJAComment extends KomentoViewMigrators
{
	function clearComments()
	{
		$db = JFactory::getDBO();
		$db->setQuery( 'DELETE FROM `#__komento_comments`' );
		$db->query();

		$this->ajax->success();
		$this->ajax->send();
	}

	function getStatistic()
	{
		$this->ajax->log( 'Getting statistics' );

		$params = JRequest::getVar( 'params' );

		$components = $params['components'];
		$cids = array();
		$totalCids = 0;
		$totalComments = 0;

		foreach( $components as $component )
		{
			$options = array( 'option' => $component );

			$tmp = $this->model->getUniquePostId( $options );
			$totalCids += count( $tmp );
			$totalComments += $this->model->getCommentCount( $options );

			foreach( $tmp as $t )
			{
				$cids[] = array( 'component' => $component, 'cid' => $t );
			}
		}

		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_TOTAL_COMPONENTS' ) . ': ' . count( $components ) );
		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_TOTAL_ARTICLES' ) . ': ' . $totalCids );
		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_TOTAL_COMMENTS' ) . ': ' . $totalComments );

		$this->ajax->success( $cids, $totalComments );
		$this->ajax->send();
	}

	function migrate()
	{
		$params = JRequest::getVar( 'params' );
		$this->component = $params['component'];
		$this->model->component = $params['component'];
		$this->cid = $params['cid'];
		$this->model->cid = $params['cid'];

		$this->model->publishingState = $params['publishingState'];

		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_COMPONENT' ) . ': ' . $this->component );
		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_ARTICLE_ID' ) . ': ' . $this->cid );

		$options = array(
			'parentid'	=> 0,
			'option'	=> $this->component,
			'contentid'	=> $this->cid
		);

		$parents = $this->model->getComments( $options );

		$break = 0;

		foreach( $parents as $parent )
		{
			$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_PARENT' ) . ': ' . $parent->id );

			$komentoInsertNode = false;

			if( $break == 0 )
			{
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_CHECKING_FOR_INSERTION_NODE' ) );
				$komentoInsertNode = $this->model->getKomentoInsertNode( $parent->date );
			}

			$base = 1;

			if( $break == 0 && $komentoInsertNode )
			{
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTION_NODE_FOUND' ) . ': ' . $komentoInsertNode->id );
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTING_COMMENTS' ) );

				$base = $komentoInsertNode->lft;
				$diff = 2;

				// $totalChilds = $this->model->getCommentCount( array( 'object_group' => $this->component, 'object_id' => $this->cid, 'thread_id' => $parent->id ) );

				$this->model->pushKomentoComment( $base, $diff );
			}
			else
			{
				if( $break == 0 )
				{
					$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTION_NODE_NOT_FOUND' ) );
				}

				// all comments in EB are later than kmt comments
				// set break == 1, this means all subsequent parents does not need to check insert node
				$break = 1;

				$komentoLatestComment = $this->kmtmodel->getLatestComment( $this->component, $this->cid );

				if( $komentoLatestComment )
				{
					// get the last rgt in kmt and append EB comments
					$base = $komentoLatestComment->rgt + 1;
				}
			}

			// reset it to parent_id = 0 since this section is all parent comment
			$parent->parentid = 0;
			$parent->lft = $base;
			$parent->rgt = $base + 1;
			$new = $this->model->save( $parent );

			if( $new === false )
			{
				$this->ajax->fail( 'save:' . $parent->id );
				$this->ajax->send();
				return false;
			}

			if( $this->saveChildren( $parent->id, $new->id ) === false )
			{
				$this->ajax->fail( 'savechildren:' . $parent->id );
				$this->ajax->send();
				return false;
			}
		}

		$count = $this->model->getCommentCount( array( 'object_group' => $this->component, 'object_id' => $this->cid ) );
		$this->ajax->update( $count );
		$this->ajax->success();
		$this->ajax->send();
	}

	private function saveChildren( $oldid, $newid )
	{
		$options = array(
			'parentid'	=> $oldid,
			'option'	=> $this->component,
			'contentid'	=> $this->cid
		);

		$children = $this->model->getComments( $options );

		foreach( $children as $child )
		{
			// populate child comment's lft rgt
			$child = $this->model->populateChildBoundaries( $child, $newid );
			$child->parentid = $newid;

			$new = $this->model->save( $child );

			if( $new === false )
			{
				$this->ajax->fail( 'save:' . $child->id );
				$this->ajax->send();
				return false;
			}

			if( $this->saveChildren( $child->id, $new->id ) === false )
			{
				$this->ajax->fail( 'savechildren:' . $child->id );
				$this->ajax->send();
				return false;
			}
		}

		return true;
	}
}

class KomentoViewMigratorJComments extends KomentoViewMigrators
{
	function getStatistic()
	{
		$this->ajax->log( 'Getting statistics' );

		$params = JRequest::getVar( 'params' );

		$components = $params['components'];
		$cids = array();
		$totalCids = 0;
		$totalComments = 0;

		foreach( $components as $component )
		{
			$options = array( 'object_group' => $component );

			$tmp = $this->model->getUniquePostId( $options );
			$totalCids += count( $tmp );
			$totalComments += $this->model->getCommentCount( $options );

			foreach( $tmp as $t )
			{
				$cids[] = array( 'component' => $component, 'cid' => $t );
			}
		}

		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_TOTAL_COMPONENTS' ) . ': ' . count( $components ) );
		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_TOTAL_ARTICLES' ) . ': ' . $totalCids );
		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_TOTAL_COMMENTS' ) . ': ' . $totalComments );

		$this->ajax->success( $cids, $totalComments );
		$this->ajax->send();
	}

	function migrate()
	{
		$params = JRequest::getVar( 'params' );
		$this->component = $params['component'];
		$this->model->component = $params['component'];
		$this->cid = $params['cid'];
		$this->model->cid = $params['cid'];

		$this->migrateLikes = $params['migrateLikes'];
		$this->model->publishingState = $params['publishingState'];

		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_COMPONENT' ) . ': ' . $this->component );
		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_ARTICLE_ID' ) . ': ' . $this->cid );

		$options = array(
			'parent'		=> 0,
			'object_group'	=> $this->component,
			'object_id'		=> $this->cid
		);

		$parents = $this->model->getComments( $options );

		$break = 0;

		foreach( $parents as $parent )
		{
			$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_PARENT' ) . ': ' . $parent->id );

			$komentoInsertNode = false;

			if( $break == 0 )
			{
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_CHECKING_FOR_INSERTION_NODE' ) );
				$komentoInsertNode = $this->model->getKomentoInsertNode( $parent->date );
			}

			$base = 1;

			if( $break == 0 && $komentoInsertNode )
			{
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTION_NODE_FOUND' ) . ': ' . $komentoInsertNode->id );
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTING_COMMENTS' ) );

				$base = $komentoInsertNode->lft;
				$diff = 2;

				$this->model->pushKomentoComment( $base, $diff );
			}
			else
			{
				if( $break == 0 )
				{
					$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTION_NODE_NOT_FOUND' ) );
				}

				// all comments in EB are later than kmt comments
				// set break == 1, this means all subsequent parents does not need to check insert node
				$break = 1;

				$komentoLatestComment = $this->kmtmodel->getLatestComment( $this->component, $this->cid );

				if( $komentoLatestComment )
				{
					// get the last rgt in kmt and append EB comments
					$base = $komentoLatestComment->rgt + 1;
				}
			}

			// reset it to parent_id = 0 since this section is all parent comment
			$parent->parent = 0;
			$parent->lft = $base;
			$parent->rgt = $base + 1;
			$new = $this->model->save( $parent );

			if( $new === false )
			{
				$this->ajax->fail( 'save:' . $parent->id );
				$this->ajax->send();
				return false;
			}

			if( $this->migrateLikes )
			{
				if( $this->model->saveLikes( $parent->id, $new->id ) === false )
				{
					$this->ajax->fail( 'savelikes:' . $parent->id );
					$this->ajax->send();
					return false;
				}
			}

			if( $this->saveChildren( $parent->id, $new->id ) === false )
			{
				$this->ajax->fail( 'savechildren:' . $parent->id );
				$this->ajax->send();
				return false;
			}
		}

		$count = $this->model->getCommentCount( array( 'object_group' => $this->component, 'object_id' => $this->cid ) );
		$this->ajax->update( $count );
		$this->ajax->success();
		$this->ajax->send();
	}

	private function saveChildren( $oldid, $newid )
	{
		$options = array(
			'parent'		=> $oldid,
			'object_group'	=> $this->component,
			'object_id'		=> $this->cid
		);

		$children = $this->model->getComments( $options );

		foreach( $children as $child )
		{
			// populate child comment's lft rgt
			$child = $this->model->populateChildBoundaries( $child, $newid );
			$child->parent = $newid;

			$new = $this->model->save( $child );

			if( $new === false )
			{
				$this->ajax->fail( 'save:' . $child->id );
				$this->ajax->send();
				return false;
			}

			if( $this->migrateLikes )
			{
				if( $this->model->saveLikes( $child->id, $new->id ) === false )
				{
					$this->ajax->fail( 'savelikes:' . $child->id );
					$this->ajax->send();
					return false;
				}
			}

			if( $this->saveChildren( $child->id, $new->id ) === false )
			{
				$this->ajax->fail( 'savechildren:' . $child->id );
				$this->ajax->send();
				return false;
			}
		}

		return true;
	}
}

class KomentoViewMigratorEasyBlog extends KomentoViewMigrators
{
	function getStatistic()
	{
		$this->ajax->log( 'Getting statistics' );

		$params = JRequest::getVar( 'params' );
		$categories = $params['categories'];

		$options = array( 'categories' => $categories );

		$cids = $this->model->getUniquePostId( $options );
		$totalComments = $this->model->getCommentCount( array( 'post_id' => $cids ) );

		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_TOTAL_ARTICLES' ) . ': ' . count( $cids ) );
		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_TOTAL_COMMENTS' ) . ': ' . $totalComments );

		$this->ajax->success( $cids, $totalComments );
		$this->ajax->send();
	}

	function migrate()
	{
		$params = JRequest::getVar( 'params', '' );
		$this->component = 'com_easyblog';
		$this->model->component = 'com_easyblog';
		$this->cid = $params['cid'];
		$this->model->cid = $params['cid'];

		$this->migrateLikes = $params['migrateLikes'];
		$this->model->publishingState = $params['publishingState'];

		$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_ARTICLE_ID' ) . ': ' . $this->cid );

		$parents = $this->model->getComments( array( 'post_id' => $this->cid, 'depth' => 0 ) );

		$break = 0;

		foreach( $parents as $parent )
		{
			$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_MIGRATING_PARENT' ) . ': ' . $parent->id );

			$komentoInsertNode = false;

			if( $break == 0 )
			{
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_CHECKING_FOR_INSERTION_NODE' ) );
				$komentoInsertNode = $this->model->getKomentoInsertNode( $parent->created );
			}

			$relDiff = 0;
			$base = 1;

			if( $break == 0 && $komentoInsertNode )
			{
				$base = $komentoInsertNode->lft;
				$diff = $parent->rgt - $parent->lft + 1;

				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTION_NODE_FOUND' ) . ': ' . $komentoInsertNode->id );
				$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTING_COMMENTS' ) );

				$this->model->pushKomentoComment( $base, $diff );
			}
			else
			{
				if( $break == 0 )
				{
					$this->ajax->log( JText::_( 'COM_KOMENTO_MIGRATORS_LOG_INSERTION_NODE_NOT_FOUND' ) );
				}

				// all comments in EB are later than kmt comments
				// set break == 1, this means all subsequent parents does not need to check insert node
				$break = 1;

				$komentoLatestComment = $this->kmtmodel->getLatestComment( $this->component, $this->cid );

				if( $komentoLatestComment )
				{
					// get the last rgt in kmt and append EB comments
					$base = $komentoLatestComment->rgt + 1;
				}
			}

			// calculate relative difference based on insertion node's base
			$relDiff = $base - $parent->lft;

			// reset it to parent_id = 0 since this section is all parent comment
			$parent->parent_id = 0;
			$parent->lft += $relDiff;
			$parent->rgt += $relDiff;
			$new = $this->model->save( $parent );

			if( $new === false )
			{
				$this->ajax->fail( 'save:' . $parent->id );
				$this->ajax->send();
				return false;
			}

			if( $this->migrateLikes )
			{
				if( $this->model->saveLikes( $parent->id, $new->id ) === false )
				{
					$this->ajax->fail( 'savelikes:' . $parent->id );
					$this->ajax->send();
					return false;
				}
			}

			if( $this->saveChildren( $parent->id, $new->id, $relDiff ) === false )
			{
				$this->ajax->fail( 'savechildren:' . $parent->id );
				$this->ajax->send();
				return false;
			}
		}

		$count = $this->model->getCommentCount( array( 'post_id' => $this->cid ) );
		$this->ajax->update( $count );
		$this->ajax->success();
		$this->ajax->send();
	}

	private function saveChildren( $ebid, $kmtid, $relDiff )
	{
		$children = $this->model->getChildren( $ebid );

		foreach( $children as $child )
		{
			$child->lft += $relDiff;
			$child->rgt += $relDiff;
			$child->parent_id = $kmtid;

			$new = $this->model->save( $child );

			if( $new === false )
			{
				$this->ajax->fail( 'save:' . $child->id);
				$this->ajax->send();
				return false;
			}

			if( $this->migrateLikes )
			{
				if( $this->model->saveLikes( $child->id, $new->id ) === false )
				{
					$this->ajax->fail( 'savelikes:' . $child->id );
					$this->ajax->send();
					return false;
				}
			}

			if( $this->saveChildren( $child->id, $new->id, $relDiff ) === false )
			{
				$this->ajax->fail( 'savechildren:' . $child->id );
				$this->ajax->send();
				return false;
			}
		}

		return true;
	}

	function clearComments()
	{
		$db = JFactory::getDBO();
		$db->setQuery( 'DELETE FROM `#__komento_comments` WHERE `component` = ' . $db->quote( 'com_easyblog' ) );
		$db->query();

		$this->ajax->success();
		$this->ajax->send();
	}
}
