<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.utilities.date');

class KomentoDateHelper
{
	/*
	 * return the jdate with the correct specified timezone offset
	 * param : raw date string (date with no offset yet)
	 * return : JDate object
	 */
	public static function dateWithOffSet($str='')
	{
		$userTZ = KomentoDateHelper::getOffSet();
		$date	= new JDate($str);

		if( Komento::joomlaVersion() >= '1.6' )
		{
			$user		= JFactory::getUser();
			$config		= Komento::getConfig();
			$jConfig	= JFactory::getConfig();

			// temporary ignore the dst in joomla 1.6

			if($user->id != 0)
			{
				$userTZ	= $user->getParam('timezone');
			}

			if(empty($userTZ))
			{
				$userTZ	= $jConfig->get('offset');
			}

			$tmp = new DateTimeZone( $userTZ );
			$date->setTimeZone( $tmp );
		}
		else
		{
			$date->setOffset( $userTZ );
		}

		return $date;
	}

	public static function getDate($str='')
	{
		return KomentoDateHelper::dateWithOffSet($str);
	}

	public static function geRawUnixTimeOld($str='')
	{
		$tzoffset 	= KomentoDateHelper::getOffSet();
		$date 		= JFactory::getDate( $str );

		$newdate = mktime( ($date->toFormat('%H')  - $tzoffset),
							$date->toFormat('%M'),
							$date->toFormat('%S'),
							$date->toFormat('%m'),
							$date->toFormat('%d'),
							$date->toFormat('%Y'));
		return $newdate;
	}

	public static function getOffSet16($numberOnly = false)
	{
		jimport('joomla.form.formfield');

		$user		= JFactory::getUser();
		$config		= Komento::getConfig();
		$jConfig	= JFactory::getConfig();

		// temporary ignore the dst in joomla 1.6

		if($user->id != 0)
		{
			$userTZ	= $user->getParam('timezone');
		}

		if(empty($userTZ))
		{
			$userTZ	= $jConfig->get('offset');
		}

		if( $numberOnly )
		{
			$newTZ  	= new DateTimeZone($userTZ);
			$dateTime   = new DateTime( "now" , $newTZ );

			$offset		= $newTZ->getOffset( $dateTime ) / 60 / 60;
			return $offset;
		}
		else
		{
			//timezone string
			return $userTZ;
		}
	}

	public static function getOffSet( $numberOnly	= false )
	{
		if(Komento::joomlaVersion() >= '1.6')
		{
			//return a timezone object
			return KomentoDateHelper::getOffSet16($numberOnly);
		}

		$mainframe	= JFactory::getApplication();
		$user		= JFactory::getUser();
		$config		= Komento::getConfig();

		$userTZ		= '';
		$dstOffset	= $config->get('main_dstoffset', 0);


		if($user->id != 0)
		{
			$userTZ	= $user->getParam('timezone') + $dstOffset;
		}

		//if user did not set timezone, we use joomla one.
		if(empty($userTZ))
		{
			$userTZ	= $mainframe->getCfg('offset') + $dstOffset;
		}

		return $userTZ;
	}

	public static function enableDateTimePicker()
	{
		$document	= JFactory::getDocument();

		// load language for datetime picker
		$html = '
		<script type="text/javascript">
		/* Date Time Picker */
		var sJan			= "'.JText::_('JAN').'";
		var sFeb			= "'.JText::_('FEB').'";
		var sMar			= "'.JText::_('MAR').'";
		var sApr			= "'.JText::_('APR').'";
		var sMay			= "'.JText::_('MAY').'";
		var sJun			= "'.JText::_('JUN').'";
		var sJul			= "'.JText::_('JUL').'";
		var sAug			= "'.JText::_('AUG').'";
		var sSep			= "'.JText::_('SEP').'";
		var sOct			= "'.JText::_('OCT').'";
		var sNov			= "'.JText::_('NOV').'";
		var sDec			= "'.JText::_('DEC').'";
		var sAm				= "'.JText::_('AM').'";
		var sPm				= "'.JText::_('PM').'";
		var btnOK			= "'.JText::_('COM_KOMENTO_SAVE_BUTTON').'";
		var btnReset		= "'.JText::_('COM_KOMENTO_RESET').'";
		var btnCancel		= "'.JText::_('COM_KOMENTO_CANCEL').'";
		var sNever			= "'.JText::_('COM_KOMENTO_NEVER').'";
		</script>';

		$document->addCustomTag( $html );
	}

	public static function getLapsedTime( $time )
	{
		$now	= JFactory::getDate();
		$end	= JFactory::getDate( $time );
		$time	= $now->toUnix() - $end->toUnix();

		$tokens = array (
							31536000 	=> 'COM_KOMENTO_X_YEAR',
							2592000 	=> 'COM_KOMENTO_X_MONTH',
							604800 		=> 'COM_KOMENTO_X_WEEK',
							86400 		=> 'COM_KOMENTO_X_DAY',
							3600 		=> 'COM_KOMENTO_X_HOUR',
							60 			=> 'COM_KOMENTO_X_MINUTE',
							1 			=> 'COM_KOMENTO_X_SECOND'
						);

		foreach( $tokens as $unit => $key )
		{
			if ($time < $unit)
			{
				continue;
			}

			$units	= floor( $time / $unit );

			$string = $units > 1 ?  $key . 'S' : $key;
			$string = $string . '_AGO';

			$text   = JText::sprintf(strtoupper($string), $units);
			return $text;
		}

		return JText::_('COM_KOMENTO_ONE_SECOND_AGO');
	}

	public static function getDifference($time, $format = '')
	{
		if($time == '')
		{
			return 0;
		}

		$now	= JFactory::getDate();
		$end	= $time;
		$time	= $now->toUnix() - $end->toUnix();

		if($format)
		{
			$time = self::toFormat($time, $format);
		}

		return $time;
	}

	public static function toFormat($jdate, $format='%Y-%m-%d %H:%M:%S')
	{
		if(is_null($jdate))
		{
			$jdate  = new JDate();
		}

		if( Komento::joomlaVersion() >= '1.6' )
		{
			// There is no way to have cross version working, except for detecting % in the format
			if( JString::stristr( $format , '%') === false )
			{
				return $jdate->format( $format , true );
			}
			return $jdate->toFormat( $format, true );
		}
		return $jdate->toFormat( $format );
	}
}
