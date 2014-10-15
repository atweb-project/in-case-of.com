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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.user.user');

/*
Using KomentoProfile

$profile	= Komento::getProfile();
$profile->load(42);
echo $profile->getAvatar();
echo $profile->getLink();

*/

class KomentoProfile extends JUser
{
	protected $profileName		= null;
	protected $profileAvatar	= null;
	protected $profileLink		= null;
	protected $profileUsername	= null;

	public function __construct($id = null)
	{
		if (empty($id))
		{
			$this->set( 'name',		JText::_( 'COM_KOMENTO_GUEST' ) );
			$this->set( 'username',	JText::_( 'COM_KOMENTO_GUEST' ) );
		}

		parent::__construct($id);
	}

	public static function getUser($id = null)
	{
		$juser	= JFactory::getUser($id);
		$id		= $juser->id;

		static $profiles = array();

		if (empty($profiles[$id]))
		{
			$profiles[$id]	= new KomentoProfile($id);
			if ($id != 0)
			{
				$profiles[$id]->load($id);
			}
		}

		return $profiles[$id];
	}

	// an overwrite of JUser
	public function load($id = null)
	{
		return parent::load($id);
	}

	public function isAdmin()
	{
		$isAdmin	= false;

		if(Komento::joomlaVersion() >= '1.6')
		{
			$isAdmin	= $this->authorise('core.admin');
		}
		else
		{
			$isAdmin	= $this->usertype == 'Super Administrator' || $this->usertype == 'Administrator' ? true : false;
		}

		return $isAdmin;
	}

	public function getName()
	{
		$config = Komento::getConfig();

		if( $config->get( 'name_type' ) == 'username' )
		{
			return $this->getUsername();
		}

		if (!$this->profileName)
		{
			$this->profileName	= $this->name;
		}

		return $this->profileName;
	}

	public function getUsername()
	{
		if (!$this->profileUsername)
		{
			$this->profileUsername	= $this->username;
		}

		return $this->profileUsername;
	}

	public function getAvatar( $email = '' )
	{
		static $avatar = array();

		$config = Komento::getConfig();
		$vendorName	= $config->get( 'layout_avatar_integration' );

		if( $vendorName == 'gravatar' && $email != '' )
		{
			if( !isset( $avatar[$email] ) )
			{
				$avatar[$email] = $this->getVendor()->getAvatar( $email );
			}

			$this->profileAvatar = $avatar[$email];
		}
		else
		{
			if (!$this->profileAvatar)
			{
				$this->profileAvatar	= $this->getVendor()->getAvatar();
			}
		}
		return $this->profileAvatar;
	}

	public function getProfileLink()
	{
		if (!$this->profileLink)
		{
			$this->profileLink	= $this->getVendor()->getLink();
		}

		return $this->profileLink;
	}

	public function getVendor( $name = '' )
	{
		static $vendors	= array();

		if (empty($vendor['name']))
		{
			$config		= Komento::getConfig();
			$preferred	= $config->get( 'layout_avatar_integration' );
			$vendorName	= $name ? $name : $preferred;

			require_once( KOMENTO_CLASSES . DS . 'profileVendors.php' );
			$classname	= 'KomentoProfile' . ucfirst($vendorName);
			$vendor		= new $classname($this);

			if (!$vendor->state )
			{
				$vendor	= $this->getVendor('default');
			}

			$vendors['name']	= $vendor;
		}

		return $vendors['name'];
	}

	public function allow( $action = '', $component = '' )
	{
		static $loaded = null;

		$component	= $component ? $component : Komento::getCurrentComponent();

		if (!$loaded)
		{
			require_once( KOMENTO_HELPERS . DS . 'acl.php' );
			$loaded = true;
		}

		return KomentoAclHelper::check( $action, $component, $this->id );
	}
}
