<?php
// No direct access to this file
defined('_JEXEC') or die;

require_once(JPATH_SITE.'/components/com_remository/remository.interface.php');

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
/**
 * Remository Form Field class for the Remository component
 */
class JFormFieldRemository extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'Remository';
 
	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions() 
	{
		// Set autoloading for Remository
		// JALoader::getInstance()->jaAutoDiscover (_CMSAPI_ABSOLUTE_PATH.'/components/com_remository/classes');

		$messages = remositoryContainerManager::getInstance()->getCategories();
		$options = array();
		if ($messages)
		{
			foreach($messages as $message) 
			{
				$options[] = JHtml::_('select.option', $message->id, $message->name);
			}
		}
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}

class JFormFieldRemositorysort extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'Remository';
 
	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions() 
	{
		// Set autoloading for Remository
		// JALoader::getInstance()->jaAutoDiscover (_CMSAPI_ABSOLUTE_PATH.'/components/com_remository/classes');

		// Possible values are 1 = File ID, 2 = File title, 3 = Download count (descending), 
		// 4 = Submit date (descending), 5 = User Name (submitter), 6 = Author, 
		// 7 = rating (descending)
		$options[] = JHtml::_('select.option', 0, 'Use default');
		$options[] = JHtml::_('select.option', 1, 'File ID');
		$options[] = JHtml::_('select.option', 2, 'File Title');
		$options[] = JHtml::_('select.option', 3, 'Downloads');
		$options[] = JHtml::_('select.option', 4, 'Submit Date');
		$options[] = JHtml::_('select.option', 5, 'User Name');
		$options[] = JHtml::_('select.option', 6, 'Author');
		$options[] = JHtml::_('select.option', 7, 'Rating');
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
