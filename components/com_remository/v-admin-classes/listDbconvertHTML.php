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
* Traduction FR par Mihàly Marti alias Sarki
*/

class listDbconvertHTML extends remositoryAdminHTML {

	function view () {
		echo <<<CONFIRM_DBCONVERT

		<table class="adminheading">
			<tr>
				<th>Conversion des tables de Remository ant&eacute;rieur &agrave; 3.20</th>
			</tr>
        </table>
		<p>
		Si les tables de la base de donn&eacute;es de votre version de Remository sont ant&eacute;rieurs &agrave; celles de la version 3.20,
		cette option les convertira en tables compatibles avec cette version.
		</p>
		<p>
		ATTENTION CETTE FONCTION SUPPRIME TOUTES LES TABLES DES VERSIONS SUPERIEURES A LA 3.20 !
		</p>
		<p>
		Appliquez cette conversion avant de remplir les tables de cette version, sinon vous risquez de perdre vos donn&eacute;es
		</p>
		<form action="{$this->interface->indexFileName()}" method="post" name="adminForm">
		<div>
			<input type="hidden" name="confirm" value="confirm" />
			<input type="submit" value="Convertir" />
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="option" value="com_remository"/>
			<input type="hidden" name="repnum" value="$this->repnum" />
			<input type="hidden" name="act" value="dbconvert"/>
		</div>
		</form>

CONFIRM_DBCONVERT;

	}
}

?>