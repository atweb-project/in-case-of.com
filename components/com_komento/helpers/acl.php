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

class KomentoACLHelper
{
	protected static $rules = array();

	public static function allow( $type, $comment = '', $component = '', $cid = '' )
	{
		// for complicated acl situations
		// $type = ['edit', 'delete', 'publish', 'unpublish'];

		if( !empty( $comment ) && ( empty( $component ) || empty( $cid ) ) )
		{
			if( !is_object( $comment ) )
			{
				$comment = Komento::getComment( $comment );
			}

			$component = $comment->component;
			$cid = $comment->cid;
		}

		if( empty( $component ) || empty( $cid ) )
		{
			return false;
		}

		$profile		= Komento::getProfile();
		$application	= Komento::loadApplication( $component )->load( $cid );

		switch( $type )
		{
			case 'edit':
				if( $profile->allow( 'edit_all_comment' ) || ( $profile->id == $application->getAuthorId() && $profile->allow( 'author_edit_comment' ) ) || ( $profile->id == $comment->created_by && $profile->allow( 'edit_own_comment' ) ) )
				{
					return true;
				}
				break;
			case 'delete':
				if( $profile->allow( 'delete_all_comment' ) || ( $profile->id == $application->getAuthorId() && $profile->allow( 'author_delete_comment' ) ) || ( $profile->id == $comment->created_by && $profile->allow( 'delete_own_comment' ) ) )
				{
					return true;
				}
				break;
			case 'publish':
				if( $profile->allow( 'publish_all_comment' ) || ( $profile->id == $application->getAuthorId() && $profile->allow( 'author_publish_comment' ) ) )
				{
					return true;
				}
				break;
			case 'unpublish':
				if( $profile->allow( 'unpublish_all_comment' ) || ( $profile->id == $application->getAuthorId() && $profile->allow( 'author_unpublish_comment' ) ) )
				{
					return true;
				}
				break;
			case 'stick':
				if( $profile->allow( 'stick_comment' ) )
				{
					return true;
				}
				break;
			case 'like':
				if( $profile->allow( 'like_comment' ) )
				{
					return true;
				}
				break;
			case 'report':
				if( $profile->allow( 'report_comment' ) )
				{
					return true;
				}
				break;
		}

		return false;
	}

	public static function check( $action, $component = 'com_content', $userId )
	{
		$userId		= (int) $userId;
		$action		= strtolower(preg_replace('#[\s\-]+#', '.', trim($action)));
		$component	= strtolower(preg_replace('#[\s\-]+#', '.', trim($component)));
		$signature	= serialize(array($userId, $component));
		$result		= false;

		if (empty( self::$rules[$signature] ))
		{
			self::$rules[$signature] = self::getRules( $userId, $component );
		}

		if (isset(self::$rules[$signature][$action]))
		{
			$result = (boolean) self::$rules[$signature][$action];
		}

		return $result;
	}

	public static function getRules( $userId, $component = 'com_content' )
	{
		$signature	= serialize(array($userId, $component));

		if (empty( self::$rules[$signature] ))
		{
			$model	= Komento::getModel( 'acl' );
			$data	= array();

			// check user group specific rules
			//$identities = JAccess::getGroupsByUser($userId);
			$identities = Komento::getGroupsByUser($userId);

			foreach ($identities as $identity)
			{
				$data[]	= $model->getAclObject( $identity, 'usergroup', $component );
			}

			// check user specific rules
			$data[] = $model->getAclObject( $userId, 'user', $component );

			// remove empty set
			foreach ($data as $key => $value) {
				if (empty($value))
				{
					unset($data[$key]);
				}
			}

			self::$rules[$signature] = self::merge($data);
		}

		return self::$rules[$signature];
	}

	private static function merge( $data )
	{
		$result	= array();

		if (is_array($data))
		{
			foreach ($data as $row)
			{
				if (is_object($row))
				{
					$actions = json_decode($row->rules);

					if (is_array($actions))
					{
						foreach ($actions as $action)
						{
							if (isset($result[$action->name]))
							{
								if ($result[$action->name] !== 0)
								{
									$result[$action->name] = $action->value;
								}
							}
							else
							{
								$result[$action->name] = $action->value;
							}
						}
					}
				}
			}
		}

		return $result;
	}

	public static function install_16()
	{

	}

	public static function install_15()
	{
		$backendModel	= Komento::getModel( 'Acl', true );
		$backendModel->updateUserGroups( 'com_content' );
	}

	public static function getDefault()
	{
		$rules		= array();
		$xmlFile	= KOMENTO_ROOT . DS . 'rules.xml';

		if (JFile::exists($xmlFile))
		{
			$xml	= simplexml_load_file($xmlFile);

			foreach ($xml->rules->rule as $child)
			{
				$rules[] = $child;
			}
		}
	}
}


class KomentoRules
{
	protected $id		= null;
	protected $name		= null;
	protected $group	= null;
	protected $rules	= null;

	public function __construct( $user )
	{
		$this->rules = new KomentoRules();

		if (empty($user->id))
		{
			$this->id 		= '0';
			$this->name 	= 'guest';
			$this->group	= 'none';
		}
		else
		{
			$this->id		= $user->id;
			$this->name		= $user->name;
			$this->group	= $user->usertype;
		}
	}
}


class KomentoRule
{
	protected $data = array();

	public function __construct()
	{

	}

	public function allow()
	{
		// by default, deny implicitly
		$result = null;

		// some logics here
		// ...

		return $result;
	}
}
