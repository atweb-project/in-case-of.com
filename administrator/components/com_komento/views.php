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
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

class KomentoAdminView extends JView
{
	public function getModel( $name = null )
	{
		static $model = array();

		$name = preg_replace('/[^A-Z0-9_]/i', '', trim($name));

		if( !isset( $model[ $name ] ) )
		{
			$path	= KOMENTO_ADMIN_ROOT . DS . 'models' . DS . JString::strtolower( $name ) . '.php';

			jimport('joomla.filesystem.path');
			if ( !JFile::exists( $path ))
			{
				JError::raiseWarning( 0, 'Model file not found.' );
			}

			$modelClass		= 'KomentoModel' . ucfirst( $name );

			if( !class_exists( $modelClass ) )
				require_once( $path );


			$model[ $name ] = new $modelClass();
		}

		return $model[ $name ];
	}

	public function renderCheckbox( $configName , $state = '' )
	{
		if( $state == '' )
		{
			$config = Komento::getConfig();
			$state = $config->get( $configName, 0 );
		}

		ob_start();
	?>
		<label class="option-enable<?php echo $state == 1 ? ' selected' : '';?>"><span><?php echo JText::_( 'COM_KOMENTO_YES_OPTION' );?></span></label>
		<label class="option-disable<?php echo $state == 0 ? ' selected' : '';?>"><span><?php echo JText::_( 'COM_KOMENTO_NO_OPTION' ); ?></span></label>
		<input name="<?php echo $configName; ?>" value="<?php echo $state;?>" type="radio" id="<?php echo $configName; ?>" class="radiobox" checked="checked" />
	<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function renderDropdown( $configName, $state = '', $options )
	{
		if( $state == '' )
		{
			$config = Komento::getConfig();
			$state = $config->get( $configName, 0 );
		}

		return JHtml::_('select.genericlist', $options, $configName, 'size="1" class="inputbox"', 'value', 'text', $state, $configName );
	}

	public function renderInput( $configName, $state = '', $options = '' )
	{
		if( $state == '' )
		{
			$config = Komento::getConfig();
			$state = $config->get( $configName, '' );
		}

		$size = 5;
		$pretext = '';
		$posttext = '';
		$align = '';
		if( is_array( $options ) )
		{
			if( isset( $options['size'] ) )
			{
				$size = $options['size'];
			}

			if( isset( $options['pretext'] ) )
			{
				$pretext = $options['pretext'];
			}

			if( isset( $options['posttext'] ) )
			{
				$posttext = $options['posttext'];
			}

			if( isset( $options['align'] ) )
			{
				$align = $options['align'];
			}
		}
		else
		{
			if( $options != '' )
			{
				$size = $options;
			}
		}

		ob_start();
		?>
		<span class="small"><?php echo $pretext; ?></span><input type="text" class="inputbox" id="<?php echo $configName; ?>" name="<?php echo $configName; ?>" value="<?php echo $this->escape( $state ); ?>" size="<?php echo $size; ?>"<?php echo $align ? ' style="text-align:'.$align.';"' : ''; ?>/><span class="small"><?php echo $posttext; ?></span>
		<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function renderOption( $value, $text )
	{
		return JHtml::_( 'select.option', $value, JText::_( $text ) );
	}

	public function renderFilters( $options = array() , $value , $element )
	{
		ob_start();

		foreach( $options as $key => $val )
		{
		?>
		<a class="kmt-filter<?php echo $value == $key ? ' kmt-filter-active' : '';?>" href="javascript:void(0);" onclick="Foundry('#<?php echo $element;?>').val('<?php echo $key;?>');submitform();"><?php echo JText::_( $val ); ?></a>
		<?php
		}
		?>
		<input type="hidden" name="filter_type" id="filter_type" value="<?php echo $this->escape($value); ?>" />
		<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
