<?php
/**
 * @package		Komento comment module
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

require_once( JPATH_ROOT . DS . DS . 'components' . DS . 'com_komento' . DS . 'helpers' . DS . 'helper.php' );
jimport('joomla.html.html');

if( Komento::joomlaVersion() >= 1.6) {
	jimport('joomla.form.formfield');
	class JFormFieldModal_KomentoComponent extends JFormField
	{
		var $type = 'Modal_KomentoComponent';

		function getInput()
		{
			JFactory::getDocument()->addStyleSheet( JURI::root() . 'administrator/components/com_komento/assets/css/module.css' );

			return JElementKomentoComponent::fetchElement( $this->name, $this->value, $this->element, $this->options['control']);
		}
	}
}

jimport('joomla.html.parameter.element');
class JElementKomentoComponent extends JElement
{
	var $_name = 'KomentoComponent';

	function fetchElement( $name, $value, &$node, $control_name )
	{
		$helper = Komento::getHelper( 'components' );
		$components = array_values( $helper->getAvailableComponents() );

		if( Komento::joomlaVersion() >= 1.6) {
			$fieldName = $name;
		} else {
			$fieldName = $control_name.'['.$name.']';
		}

		ob_start();
		?>
		<select name="<?php echo $fieldName;?>">
			<option value="all"<?php echo $value == 'all' ? ' selected="selected"' :'';?>>All</option>
		<?php foreach($components as $component) {
			$selected	= $component == $value ? ' selected="selected"' : ''; ?>
			<option value="<?php echo $component;?>"<?php echo $selected ;?>><?php echo JText::_( 'COM_KOMENTO_' . $component ); ?></option>
		<?php } ?>
		</select>
		<?php
		$html	= ob_get_contents();
		ob_end_clean();
		return $html;
	}
}
