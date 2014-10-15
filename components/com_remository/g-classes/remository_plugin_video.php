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
	class bot_remositoryVideo extends aliroPlugin {

		public function remositoryVideoPlayer ($link) {
			$worker = new remository_plugin_video();
			return $worker->remositoryVideoPlayer ($this->params, $link);
		}

	}
}

class remository_plugin_video extends remositoryAddOnController {

	// The real search method - meant to be accessed only by the classes above
	// Parameters are not needed - coded only as a model for other plugins
	public function remositoryVideoPlayer ($botparams, $link) {
		return <<<VIDEO_PLAYER

		<!-- id=MediaPlayer1 -->
		<object id=mediaplayer1 type=application/x-oleobject
                  height="24" width="320"
                  classid="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6">
		<param name="url" value="$link">
		<param name="animationatstart" value="true">
		<param name="transparentatstart" value="true">
		<param name="autostart" value="false">
		<param name="showcontrols" value="true">

		<embed type="application/x-mplayer2" name="mediaplayer"  autostart="false" loop="false"  width="384" height="364"
       		src="$link" showcontrols="true">
		</embed>
		</object>

VIDEO_PLAYER;

	}

}
