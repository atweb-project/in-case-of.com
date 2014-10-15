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

		public function remositoryAudioPlayer ($link, $autostart=true, $smallplayer=false) {
			$worker = new remository_plugin_audio();
			return $worker->remositoryAudioPlayer ($this->params, $link, $autostart, $smallplayer);
		}

	}
}

class remository_plugin_audio extends remositoryAddOnController {

	// The real search method - meant to be accessed only by the classes above
	// Parameters are not needed - coded only as a model for other plugins
	public function remositoryAudioPlayer ($botparams, $link, $autostart=true, $smallplayer=false) {
		$interface = remositoryInterface::getInstance();
		$link = urlencode($link);
		$live_site = $interface->getCfg('live_site');
		$start = $autostart ? '1' : '0';
		if ($smallplayer) return <<<SMALL_AUDIO

		<!-- id=FlashMediaPlayer1 -->
		<object type="application/x-shockwave-flash" width="19" height="22" data="$live_site/components/com_remository/xspf_player/dewplayer-mini.swf">
			<param name="movie" value="$live_site/components/com_remository/xspf_player/dewplayer-mini.swf" />
			<param name="FlashVars" value="mp3=$link&amp;autoload=0&amp;autoplay=$start&amp;showslider=0&amp;width=25" />
			<param name="autoload" value="0" />
			<param name="wmode" value="transparent" />
		</object>

SMALL_AUDIO;

		else return <<<AUDIO_PLAYER

		<!-- id=FlashMediaPlayer1 -->
		<object type="application/x-shockwave-flash" width="200" height="20" data="$live_site/components/com_remository/xspf_player/dewplayer.swf">
		<param name="movie" value="$live_site/components/com_remository/xspf_player/dewplayer.swf" />
		<param name="wmode" value="transparent" />
		<param name="flashvars" value="mp3=$link&amp;autostart=$start&amp;showtime=1" />
		</object>

AUDIO_PLAYER;

	}

}
