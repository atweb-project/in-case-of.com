<?php
/*
 * Remository Categories.
 */

if (basename(@$_SERVER['REQUEST_URI']) == basename(__FILE__)) die;

// Define CMS environment
require_once (JPATH_ROOT.'/components/com_remository/remository.interface.php');
// End of CMS environment definition

class JElementRemositorycats extends JoomlaElementParentClass {

   /**
    * Element type
    *
    * @access      public
    * @var         string
    */
    public $_name = 'remositorycats';
    /**
      * Gets an HTML rendered string of the element
      *
      * @param string Name of the form element
      * @param string Value
      * @param JSimpleXMLElement XML node in which the element is defined
      * @param string Control set name, normally params
      */
	public function fetchElement ($name, $value, $node, $control_name) {
		// get the CSS Style from the XML node class attribute
		// $class = empty($node->attributes('class')) ? 'class="inputbox"' : 'class="'.$node->attributes('class').'"';
		$class = 'class="inputbox"';
		// prepare an array for the options
		remositoryInterface::getInstance()->loadLanguageFile();
		$choices[] = JHTMLSelect::option(0, _DOWN_ALL_CATEGORIES);
		$user = new remositoryUser();
		$categories = remositoryContainerManager::getInstance()->getVisibleCategories($user);
		foreach ($categories as $category) {
			$choices[] = JHTMLSelect::option($category->id, $category->name);
		}
		// create the HTML list and return it (this sorts out the
		// selected option for us)
		return JHTMLSelect::genericList($choices, $control_name.'['.$name.']', $class, 'value', 'text', $value, $control_name.$name);
  }
}