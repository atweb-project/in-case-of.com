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

define ('_DOWN_NUMBER_STATS', '5');

class listStatsHTML extends remositoryAdminHTML {

	function statHeader ($title2, $title3) {
		$maintitle = _DOWN_TOP_TITLE;
		$number = _DOWN_NUMBER_STATS;
		$filetitle = _DOWN_FILE_TITLE_SORT;
		echo <<<STAT_HEAD

		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
		<tr>
			<th width="20%" align="left">
				<b>$maintitle $number $title2</b>
			</th>
			<th width="30%" align="left">$filetitle</th>
			<th width="30%" align="left">$title3</th>
		</tr>

STAT_HEAD;

	}

	function statEntry ($name, $number) {
		echo <<<STAT_LINE

		<tr>
			<td width="20%">
				&nbsp;
			</td>
			<td width="30%" align="left">
				$name
			</td>
			<td width="30%" align="left">
				$number
			</td>
		</tr>

STAT_LINE;

	}

	function view (&$downloads, &$ratings, &$votes, $files, $containers) {
		$this->formStart(_DOWN_STATS_TITLE.sprintf(" (%s %s, %s %s)",$files, _DOWN_FILES, $containers, _DOWN_CONTAINERS));
		echo "\n\t</table>";
		$this->statHeader(_DOWN_DOWNLOADS_SORT, _DOWN_DOWNLOADS_SORT);
		?>
		</table>
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
		<?php
		foreach ($downloads as $download) {
			$this->statEntry ($download->filetitle, $download->downloads);
		}
		echo '<tr><td>&nbsp;</td></tr></table>';
		$this->statHeader (_DOWN_RATED_TITLE, _DOWN_RATING_TITLE);
        for ($i=0, $n=count( $ratings ); $i < $n; $i++) {
			$rate=explode(",", $ratings[$i]);
			$this->statEntry ($rate[0], $rate[1]);
		}
		echo '<tr><td>&nbsp;</td></tr></table>';
		$this->statHeader (_DOWN_VOTED_ON, _DOWN_VOTES_TITLE);
		for ($i=0, $n=count( $votes ); $i < $n; $i++) {
			$vote=explode(",", $votes[$i]);
			$this->statEntry ($vote[0], $vote[1]);
		}
		echo <<<END_STATS

		<tr><td>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="option" value="com_remository"/>
		<input type="hidden" name="repnum" value="$this->repnum" />
		</td></tr>
		</table>
		</form>

END_STATS;

	}
}

?>