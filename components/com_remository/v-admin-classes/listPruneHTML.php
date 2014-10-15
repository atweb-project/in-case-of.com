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

class listPruneHTML extends remositoryAdminHTML {

	function view ($default) {
		$text = _DOWN_LOGFILE_CUTOFF_DATE;
		$activate = _DOWN_PRESS_SAVE_ACTIVATE;
	    $this->formStart(_DOWN_PRUNE_LOG);
		echo <<<PRUNE_HTML

		<tr>
			<td colspan="2">
				<div class="remositoryblock">&nbsp;</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<label for="startdate">$text</label>
				<input type="text" size="15" class="inputbox" id="startdate" name="startdate" value="$default"  />
				$activate
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="hidden" name="option" value="com_remository" />
				<input type="hidden" name="repnum" value="$this->repnum" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="act" value="prune" />
				<input type="hidden" name="boxchecked" value="0" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="remositoryblock">&nbsp;</div>
			</td>
		</tr>
		</table>
		</form>

PRUNE_HTML;

		}

}

?>