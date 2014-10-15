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

class listSupportHTML extends remositoryAdminHTML {

	function view () {
		$this->formStart(_DOWN_SUPPORT);
		echo <<<UNDER_HEADING

		<tr>
			<td colspan="2">
				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="option" value="com_remository"/>
				<input type="hidden" name="repnum" value="$this->repnum" />
			</td>
		</tr>
		</table>
		</form>

UNDER_HEADING;
		/* Ajout description support francophone - Sarki */
		echo <<<SUPPORT_TEXT
		<p></p>
		<div class="remositoryblock">&nbsp;</div>
		<div id="remositorysupport">
		<ul>
		<li>
		Traductions/adaptations francophone par <a href='http://www.sarki.ch/' target='_blank'>Mihàly Marti alias Sarki</a> pour <a href='http://www.joomla.fr/' target='_blank'>Joomla! FR</a> - Veuillez svp effectuer vos retours d'erreurs de traduction &agrave; l'adresse suivante : <a href="mailto:extensions@joomla.fr">extensions@joomla.fr</a>. Merci<br /> <br /> 
		</li>
		<li>
		There is a <a href='http://remository.com/forum/' target='_blank'>forum for Remository</a>
		that is intended to be a self help facility.  Please do not expected a guaranteed level of service
		from the developer of Remository in the forum.  If you are knowledgeable about Remository, please
		consider giving advice to others through the forum.
		</li>
		<li>
		A growing body of FAQ (frequently asked questions) are covered at
		<a href='http://www.remository.com/faq/' target='_blank'>the Remository web site.</a>
		Please check if the answer to your query is given in the FAQ before posting to the forum.
		</li>
		<li>
		If you have a question about Remository, Mambo or Joomla that may be amenable to a quick
		fix, then you have the option to subscribe to the Remository
		<a href='http://remository.com/view/problemfix/' target='_blank'>"Problemfix" service.</a>
		For a modest fee, your query is given priority treatment, and if possible will be
		quickly fixed.
		</li>
		<li>
		Remository is sophisticated technology.  For simple applications, you should be able to
		operate it without any special skills, beyond the care always needed with computer systems.
		If your requirement is commercial or likely to grow into a substantial repository, then
		you should consider purchasing a project review from the developers of Remository.  Cost
		varies according to the size of the project, but is likely to pay dividends in the long
		run through lower maintenance costs and a more reliable system.  To enquire about this
		service, please use the
		<a href='http://remository.com/contact/' target='_blank'>
		Remository "Contact Us" facility.</a>
		</li>
		<li>
		Again, if you are using Remository heavily or need to meet commercial standards of
		operation, please consider purchasing a support contract from the Remository developers.
		For an annual fee, you will receive priority response to email enquiries, advice on
		upgrades, and a bug fix service.  Price varies according to the size of the system.
		Again, further details are available from the
		<a href='http://remository.com/contact/' target='_blank'>
		Remository "Contact Us" facility.</a>
		</li>
		<li>
		While Remository is very flexible, it is still possible that it does not meet all of
		your requirements.  It can be customised by the expert developers of Remository, with
		prices based on a charge rate of 90 USD per hour.  To discuss your requirements, refer to the
		<a href='http://remository.com/contact/' target='_blank'>
		Remository "Contact Us" facility.</a>
		</li>
		<li>
		Developing and maintaining Remository takes a lot of time and a certain amount of
		money to provide a development environment.  If you are gaining financial benefit
		from your use of Remository, please consider making a donation to support the project.
		Click on the button below to make an immediate payment:
		</li>
		</ul>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<p>
		<input type="hidden" name="cmd" value="_s-xclick" />
		<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" />
		<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1" />
		<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----" />
		</p>
		</form>
		</div>
		<div class="remositoryblock">&nbsp;</div>

SUPPORT_TEXT;

	}
}

?>