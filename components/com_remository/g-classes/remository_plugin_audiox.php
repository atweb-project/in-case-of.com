<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006-9 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

if (defined('_ALIRO_IS_PRESENT')) {
	class bot_remositoryAudio extends aliroPlugin {

		public function remositoryAudioPlayer ($link, $title) {
			$worker = new remository_plugin_audio();
			return $worker->remositoryAudioPlayer ($this->params, $link, $title);
		}

	}
}

class remository_plugin_audio extends remositoryAddOnController {

	// The real search method - meant to be accessed only by the classes above
	// Parameters are not needed - coded only as a model for other plugins
	public function remositoryAudioPlayer ($botparams, $link, $title) {
		$interface = remositoryInterface::getInstance();
		$link = urlencode($link);
		$player_url = $interface->getCfg('live_site')."/components/com_remository/xspf_player/xspf_player_slim.swf?song_url=$link&amp;autoload=true&amp;autoplay=true&amp;song_title=".urlencode($title);
		?>
		<!-- id=FlashMediaPlayer1 -->
		<object type="application/x-shockwave-flash" width="400" height="40"
		data="<?php echo $player_url ?>">
		<param name="movie"
		value="<?php echo $player_url ?>" />
		</object>
		<?php
	}

}
