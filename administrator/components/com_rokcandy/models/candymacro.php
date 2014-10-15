<?php
/**
 * @version $Id$
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.modeladmin');

class RokCandyModelCandyMacro extends JModelAdmin
{
    protected function canDelete($record)
    {
        if (!empty($record->id)) {
            if ($record->published != -2) {
                return ;
            }
            $user = JFactory::getUser();
            return $user->authorise('core.delete', 'com_rokcandy.category.'.(int) $record->catid);
        }
    }

    protected function canEditState($record)
    {
        $user = JFactory::getUser();

        // Check against the category.
        if (!empty($record->catid)) {
            return $user->authorise('core.edit.state', 'com_rokcandy.category.'.(int) $record->catid);
        }
        // Default to component settings if category not known.
        else {
            return parent::canEditState($record);
        }
    }

    public function getTable($type = 'CandyMacro', $prefix = 'RokCandyTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        jimport('joomla.form.form');
        JForm::addFieldPath('JPATH_ADMINISTRATOR/components/com_rokcandy/models/fields');

        // Get the form.
        $form = $this->loadForm('com_rokcandy.candymacro', 'candymacro', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk)) {
            // Convert the params field to an array.
//            $registry = new JRegistry;
//            $registry->loadString($item->params);
//            $item->params = $registry->toArray();
        }

        return $item;
    }

    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_rokcandy.edit.candymacro.data', array());

        if (empty($data)) {
            $data = $this->getItem();

            // Prime some default values.
            if ($this->getState('candymacro.id') == 0) {
                $app = JFactory::getApplication();
                $data->set('catid', JRequest::getInt('catid', $app->getUserState('com_rokcandy.candymacros.filter.category_id')));
            }
        }

        return $data;
    }

    protected function prepareTable(&$table)
    {
        jimport('joomla.filter.output');

        if (empty($table->id)) {
            // Set the values

            // Set ordering to the last item if not set
            if (empty($table->ordering)) {
                $db = JFactory::getDbo();
                $db->setQuery('SELECT MAX(ordering) FROM #__rokcandy');
                $max = $db->loadResult();

                $table->ordering = $max+1;
            }
        }
    }

    protected function getReorderConditions($table)
    {
        $condition = array();
        $condition[] = 'catid = '.(int) $table->catid;

        return $condition;
    }
}