<?php
/*
 * Remository Categories.
 */

if (basename(@$_SERVER['REQUEST_URI']) == basename(__FILE__)) die;

// Define CMS environment
require_once(JPATH_ROOT.'/components/com_remository/remository.interface.php');
// End of CMS environment definition

class JElementRemositorysort extends JoomlaElementParentClass {

   /**
    * Element type
    *
    * @access      public
    * @var         string
    */
    public $_name = 'remositorysort';
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
		$ordername = array ('zero', _DOWN_ID, _DOWN_FILE_TITLE_SORT, _DOWN_DOWNLOADS_SORT, _DOWN_SUB_DATE_SORT, _DOWN_SUB_ID_SORT, _DOWN_AUTHOR_ABOUT, _DOWN_RATING_TITLE);
		foreach ($ordername as $number=>$sortname) if ($number) $choices[] = JHTMLSelect::option($number, $sortname);
		// create the HTML list and return it (this sorts out the
		// selected option for us)
		return JHTMLSelect::genericList($choices, $control_name.'['.$name.']', $class, 'value', 'text', $value, $control_name.$name);
  }
}