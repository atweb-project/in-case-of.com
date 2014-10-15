<?php
/**
* @version              1.0.1
* @package              JLSecure My Site
* @copyright    Copyright (C) 2012 JomLand.com. All rights reserved.
* @link                 www.jomland.com
* @license              GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/
// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemJlsecuremysite extends JPlugin
{
        function onAfterInitialise()
        {
                $app    = JFactory::getApplication();
                if ($app->isAdmin()) {
                        $secure_key     = $this->params->get('secure_key', FALSE);
                        $secure_value   = $this->params->get('secure_value', FALSE);
                        $value          = JRequest::getString($secure_key, FALSE);
                        $session        = JFactory::getSession();
                        if ($value && $value===$secure_value) {
                                $session->set($secure_key,  $secure_value);
                                return TRUE;
                        } else { //check if session exists
                                $value          = $session->get($secure_key, FALSE);
                                if ($value && $value === $secure_value) {
                                        return TRUE;
                                }
                        }
                        $app->redirect(JURI::base() . '..');
                }
        }
}

