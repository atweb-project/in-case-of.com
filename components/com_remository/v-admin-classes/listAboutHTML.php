<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

class listAboutHTML extends remositoryAdminHTML {

	function aboutLine ($title, $value) {
		echo <<<ABOUT_LINE
		<tr>
			<td>
				$title
			</td>
			<td>
				$value
			</td>
		</tr>

ABOUT_LINE;

	}

	function view () {
		$this->formStart(_DOWN_ABOUT);
		echo '</table><table cellpadding="4" cellspacing="0" border="0" width="100%" class="">';
		echo <<<UNDER_HEADING

		<tr>
			<td colspan="2">
				<div class="remositoryblock">&nbsp;</div>
			</td>
		</tr>

UNDER_HEADING;
		$this->aboutLine(_DOWN_TITLE_ABOUT, _DOWN_ABOUT_DESCRIBE);
		$this->aboutLine(_DOWN_VERSION_ABOUT, _REMOSITORY_VERSION);
		$this->aboutLine(_DOWN_AUTHOR_ABOUT,'Martin Brampton');
		$this->aboutLine(_DOWN_WEBSITE_ABOUT,"<a href='http://www.remository.com'>www.remository.com</a>");
		$this->aboutLine(_DOWN_EMAIL_ABOUT,'martin@remository.com');
		$this->aboutLine(_DOWN_WEBSITE_ABOUT,"<a href='http://www.remository.com' target='_blank'>www.remository.com</a>");
		$this->aboutLine(' ',' ');
		$this->aboutLine('Traduction','Mih&agrave;ly Marti alias Sarki');
		$this->aboutLine(_DOWN_EMAIL_ABOUT,'info@sarki.ch');
		$this->aboutLine(_DOWN_WEBSITE_ABOUT,"<a href='http://www.sarki.ch' target='_blank'>www.sarki.ch</a>");
		$this->aboutLine('Pour','AFUJ - Association francophone des Utilisateurs de Joomla!');
		$this->aboutLine(_DOWN_WEBSITE_ABOUT,"<a href='http://www.afuj.fr' target='_blank'>www.afuj.fr</a>");
		$this->aboutLine(' ','<a href="http://www.afuj.fr" target="_blank"><img style="margin: 0; float: left; border: none" alt="Logo AFUJ" src="http://www.sarki.ch/joomla15/images/stories/mise_a_jour/logo_afuj_2010.jpg" height="113" width="276"></a>');
		echo <<<UNDER_LAST

		<tr>
			<td colspan="2">
				<div class="remositoryblock">&nbsp;</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="option" value="com_remository"/>
				<input type="hidden" name="repnum" value="$this->repnum" />
			</td>
		</tr>
		</table>
		</form>

UNDER_LAST;

		}
}

?>