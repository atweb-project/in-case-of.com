<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_komento' . DS . 'bootstrap.php' );

function KomentoBuildRoute( &$query )
{
	// Declare static variables.
	static $items;
	static $default;
	static $dashboard;
	static $profile;
	static $feed;

	// Initialise variables.
	$segments	= array();
	$konfig		= Komento::getKonfig();

	// Get the relevant menu items if not loaded.
	if (empty($items))
	{
		// Get all relevant menu items.
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$items	= $menu->getItems('component', 'com_komento');

		// Build an array of serialized query strings to menu item id mappings.
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			// Check to see if we have found the dashboard menu item.
			if (empty($dashboard) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'dashboard'))
			{
				$dashboard = $items[$i]->id;
			}

			// Check to see if we have found the profile menu item.
			if (empty($profile) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'profile'))
			{
				$profile = $items[$i]->id;
			}

			// Check to see if we have found the registration menu item.
			if (empty($feed) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'feed')) {
				$feed = $items[$i]->id;
			}
		}

		// Set the default menu item to use for com_users if possible.
		if ($dashboard) {
			$default = $dashboard;
		} elseif ($profile) {
			$default = $profile;
		} elseif ($feed) {
			$default = $feed;
		}
	}

	if (!empty($query['view']))
	{
		if( !isset( $query['Itemid'] ) )
		{
			$query['Itemid'] = $default;
		}

		switch ($query['view'])
		{
			case 'feed':
				if ($query['Itemid'] == $feed) {
					unset ($query['view']);
				}
				break;

			case 'profile':
				if ($query['Itemid'] == $profile) {
					unset ($query['view']);
				}
				// Only append the user id if not "me".
				$user = JFactory::getUser();
				if (!empty($query['id']) && ($query['id'] != $user->id)) {
					$segments[] = $query['id'];
				}
				unset ($query['id']);

				break;

			default:
			case 'dashboard':
				if (!empty($query['view'])) {
					$segments[] = $query['view'];
				}
				unset ($query['view']);
				if ($query['Itemid'] == $dashboard) {
					unset ($query['view']);
				}
				break;
		}
	}

	return $segments;
}

function KomentoParseRoute( &$segments )
{
	// Initialise variables.
	$vars	= array();
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();
	$count	= count($segments);

	// Only run routine if there are segments to parse.
	if( count($segments) < 1 )
	{
		return;
	}

	if (!isset($item))
	{
		$vars['view']	= $segments[0];
	}
	else
	{
		$vars['view']	= $item->query['view'];
	}

	if( $vars['view'] == 'profile' && $count > 0 )
	{
		// $userId		= array_pop($segments);
		// $user		= JFactory::getUser( $userId );
		// $vars['id']	= $user->id;

		$vars['id']	= array_pop($segments);
	}

	return $vars;
}

