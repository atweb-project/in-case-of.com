<?php
/**
 * @version $Id$
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class RokCandyHelper {
    
    static function getMacros() {
        $params = JComponentHelper::getParams('com_rokcandy');
        $cache =  JFactory::getCache('com_rokcandy');
        
        if ($params->get('forcecache',0)==1) $cache->setCaching(true);
        $usermacros = $cache->call(array('RokCandyHelper','getUserMacros'));
        $overrides = RokCandyHelper::getTemplateOverrides();
    
        return array_merge($usermacros,$overrides);
    }
    
    static function getUserMacros() {
        $db	= JFactory::getDBO();
        $sql = 'SELECT * FROM #__rokcandy WHERE published=1';
        $db->setQuery($sql);
        $macros = $db->loadObjectList(); 
        
        $library = array();
        if (!empty($macros)) {
            foreach ($macros as $macro) {
                $library[trim($macro->macro)] = trim($macro->html);
            }
        }
        return $library;
    }
    
    
    static function getTemplateOverrides() {
        
        $params = JComponentHelper::getParams('com_rokcandy');
		$cache =  JFactory::getCache('com_rokcandy');
		if ($params->get('forcecache',0)==1) $cache->setCaching(true);
	    $library = $cache->call(array('RokCandyHelper','readIniFile'));

	    return $library;
    }

    static function readIniFile() {
        
        $app	= JFactory::getApplication();
        
        $template = $app->isAdmin() ? RokCandyHelper::getCurrentTemplate() : $app->getTemplate();
		$path = JPATH_SITE.DS."templates".DS.$template.DS."html".DS."com_rokcandy".DS."default.ini";
		
        $library = array();
        
        if (file_exists($path)) {
            jimport( 'joomla.filesystem.file' );
            $content = JFile::read($path);
            $data = explode("\n",$content);
            
            if (!empty($data)){
                foreach ($data as $line) {
                    //skip comments
                    if (strpos($line,"#")!==0 and trim($line)!="" ) {
                       $div = strpos($line,"]=");
                       $library[substr($line,0,$div+1)] = substr($line,$div+2);
                    }
                }
            }
    	}
		return $library;
    }

    static function getCurrentTemplate()
    {
        $cache = JFactory::getCache('com_rokcandy', '');
        if (!$templates = $cache->get('templates'))
        {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('id, home, template, params');
            $query->from('#__template_styles');
            $query->where('client_id = 0');

            $db->setQuery($query);
            $templates = $db->loadObjectList('id');
            foreach ($templates as &$template)
            {
                $registry = new JRegistry;
                $registry->loadJSON($template->params);
                $template->params = $registry;

                // Create home element
                if ($template->home == '1' && !isset($templates[0]))
                {
                    $templates[0] = clone $template;
                }
            }
            $cache->store($templates, 'templates');
        }

        $template = $templates[0];
        return $template->template;
    }

    public static function addSubmenu($vName)
    {
        JSubMenuHelper::addEntry(
            JText::_('COM_ROKCANDY_SUBMENU_MACROS'),
            'index.php?option=com_rokcandy&view=candymacros',
            $vName == 'candymacros'
        );
        JSubMenuHelper::addEntry(
            JText::_('COM_ROKCANDY_SUBMENU_CATEGORIES'),
            'index.php?option=com_categories&extension=com_rokcandy',
            $vName == 'categories'
        );

        if ($vName=='categories') {
            JToolBarHelper::title(
                JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE',JText::_('com_rokcandy')),
                'candymacros-categories');
        }
    }

	public static function getActions($categoryId = 0, $candymacroId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($categoryId)) {
			$assetName = 'com_rokcandy';
		}
		else {
			$assetName = 'com_rokcandy.category.'.(int) $categoryId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}
}