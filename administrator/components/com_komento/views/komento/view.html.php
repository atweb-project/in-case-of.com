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
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class KomentoViewKomento extends JView
{
	public function display($tpl = null)
	{
		//Load pane behavior
		jimport('joomla.html.pane');

		$slider		= JPane::getInstance( 'sliders' );

		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();

		$this->assignRef( 'slider', $slider );
		$this->assignRef( 'user', $user );
		$this->assignRef( 'document', $document );
		parent::display($tpl);
	}

	public function getTotalComments()
	{
		return Komento::getModel( 'comments', false )->getTotalComment();
	}

	public function addButton( $link, $image, $text, $description = '' , $newWindow = false, $acl = '' )
	{
		if( !empty( $acl ) && Komento::joomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('core.manage.' . $acl , 'com_komento') )
			{
				return '';
			}
		}

		$target		= $newWindow ? ' target="_blank"' : '';
?>
	<li>
		<a href="<?php echo $link;?>"<?php echo $target;?>>
			<img src="<?php echo JURI::root();?>administrator/components/com_komento/assets/images/cpanel/<?php echo $image;?>" width="32" />
			<span class="item-title"><?php echo $text;?></span>
		</a>
		<div class="item-description">
			<div class="tipsArrow"></div>
			<div class="tipsBody"><?php echo $description;?></div>
		</div>
	</li>
<?php
	}

	public function registerToolbar()
	{
		// Set the titlebar text
		JToolBarHelper::title( JText::_( 'COM_KOMENTO' ), 'home');

		if( Komento::joomlaVersion() >= '1.6' )
		{
			JToolBarHelper::preferences('com_komento');
		}
	}
}
