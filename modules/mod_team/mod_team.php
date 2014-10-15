<?php
/**
 * @autor       Valentín García
 * @website     www.valentingarcia.com.mx
 * @package		Joomla.Site
 * @subpackage	mod_services
 * @copyright	Copyright (C) 2012 Valentín García. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

//require_once JPATH_ROOT.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php';

//vars
//$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));//suffix
$t_names = array(
	$params->get('name1'),
	$params->get('name2'),
	$params->get('name3'),
	$params->get('name4'),
	$params->get('name5'),
	$params->get('name6'),
	$params->get('name7'),
	$params->get('name8')
);
$t_jobs = array(
	$params->get('job1'),
	$params->get('job2'),
	$params->get('job3'),
	$params->get('job4'),
	$params->get('job5'),
	$params->get('job6'),
	$params->get('job7'),
	$params->get('job8')
);
$t_txts = array(
	$params->get('txt1'),
	$params->get('txt2'),
	$params->get('txt3'),
	$params->get('txt4'),
	$params->get('txt5'),
	$params->get('txt6'),
	$params->get('txt7'),
	$params->get('txt8')
);
$t_imgs = array(
	$params->get('img1'),
	$params->get('img2'),
	$params->get('img3'),
	$params->get('img4'),
	$params->get('img5'),
	$params->get('img6'),
	$params->get('img7'),
	$params->get('img8')
);
$t_socs = array(
	$params->get('soc1'),
	$params->get('soc2'),
	$params->get('soc3'),
	$params->get('soc4'),
	$params->get('soc5'),
	$params->get('soc6'),
	$params->get('soc7'),
	$params->get('soc8')
);

//output
echo '<div class="entry">
	<div class="team">';

		for( $vv = 0; $vv < 9; $vv++ ){//<--A1.
	
			if( $t_names[$vv] ){//<--A2.
	
				echo '<div class="member">
					<div class="avatar">';
						
						if( $t_socs[$vv]	){
							echo '<div class="overlay">
								<div class="social">' . $t_socs[$vv] . '</div>
							</div>';
						}
						
						//image
						if( $t_imgs[$vv]	){
							echo '<img src="' . JURI::base() . $t_imgs[$vv] . '" alt="' . $t_names[$vv] . '" border="0" />';
						}else{
							echo '<img src="' . JURI::base() . 'templates/vg_simplekey/images/default_avatar.jpg" alt="' . $t_names[$vv] . '" />';
						}
					
					echo '</div>';
					
					//name
					echo '<h2>' . $t_names[$vv] . '</h2>';
					
					//job
					if( $t_jobs[$vv] ){
						echo '<span>' . $t_jobs[$vv] . '</span>';
					}
					
					//txts
					if( $t_txts[$vv] ){
						echo '<p class="intro">' . $t_txts[$vv] . '</p>';
					}
					
				echo '</div>';
	
			}//.A2-->
	
		}//.A1-->

	echo '</div>
</div>';

?>
