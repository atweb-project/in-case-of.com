<?php
/**
 * @version     1.0.0
 * @package     com_easyorderexport
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author       <> - 
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
/**
 * Methods supporting a list of Easyorderexport records.
 */
class EasyorderexportModelsearches extends JModelAdmin
{

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                
            );
        }

        parent::__construct($config);
    }


	
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_easyorderexport.searches','searches');
		
		if (empty($form))
		{
			return false;
		}

		return $form;
	}
	
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_easyorderexport.edit.searches.data', array());
		if (empty($data)) 
		{
				$data = $this->getItem();
		}
		return $data;
	}
	
}