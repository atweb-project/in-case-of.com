<?php
/**
 * @package		Joomla.Site
 * @subpackage	Templates.vg_simplekey
 * @copyright	Copyright (C) 2013 Valentín García - http://www.valentingarcia.com.mx - All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 
 // No direct access.
defined('_JEXEC') or die;

//REMOVE MOOTOOLS AND LOAD JQUERY
/*$headers = array(
    'scripts' => array(
        $this->baseurl . "/templates/" . $this->template . "/js/" . "jquery.js" => array(
        'mime' => 'text/javascript',
        'defer' => NULL,
        'async' => NULL)
    ),
    'script' => array(
        'text/javascript' => '' //remove mootools javascript or add custom javascript
    )
);
$this->setHeadData($headers);*/

/********************************* SITE DATA *********************************/

$app = JFactory::getApplication();
	$sitename = $app->getCfg('sitename');
	
/****************************** MODULE POSITIONS ******************************/

require('includes/module_positions.php');

/********************************* PARAMS *************************************/

require('includes/template_params.php');

?>

<!doctype html>
<html lang="en-US">
<head>
	
	<jdoc:include type="head" />
	
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />

	<!-- css main -->
	<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/style.css" rel="stylesheet" type="text/css" />
	
	<!-- js -->
	<!--[if lt IE 9]>
	<script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js" type="text/javascript"></script>
	<![endif]-->
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jpreloader.min.js"></script>
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.placeholder.js"></script>
	<script type="text/javascript">
	var isLoad=<?php echo $vg_loading; ?>; //1 - Enable preloading; 0 - Disable preloading
	var isMobile=0;
	</script>
	
	<!-- css more -->
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/shortcodes.css" type="text/css" media="all" />
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/FlexSlider/flexslider.css" type="text/css" media="all" />
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/colorbox/colorbox.css" type="text/css" media="all" />
	
	<!-- fonts -->
	<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/fonts.css?family=league+gothic&subset=latin,latin-ext" rel="stylesheet" type="text/css">
	<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/fonts.css?family=infinity&subset=latin,latin-ext" rel="stylesheet" type="text/css">
	<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/fonts.css?family=nexa+lightregular&subset=latin,latin-ext" rel="stylesheet" type="text/css">
	<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/fonts.css?family=nexa+boldregular&subset=latin,latin-ext" rel="stylesheet" type="text/css">
	
	<style type="text/css">
	.vg-firstTitle{ font:160px/140px 'league_gothic'; }
	.vg-secondTitle{ font:72px/60px 'infinity'; }
	.vg-thirdTitle{ font:100px/80px 'league_gothic'; }
	.vg-fourthTitle{ font:36px/30px 'infinity'; }
	
	#vg-main-body h1, #vg-main-body-ajax h1, #vg-main-body-component h1{ font-family:'nexa_boldregular'; }
	#vg-main-body h2, #vg-main-body-ajax h2, #vg-main-body-component h2{ font-family:'nexa_lightregular'; }

	#primary-menu-container li.current-menu-item a { color: #f26c4f; }

	h1#site-logo a{
		background-image:url(<?php echo $vg_logo; ?>);
	}
	@media only screen and (-Webkit-min-device-pixel-ratio: 1.5), only screen and (-moz-min-device-pixel-ratio: 1.5), only screen and (-o-min-device-pixel-ratio: 3/2), only screen and (min-device-pixel-ratio: 1.5) {
		h1#site-logo a {
			background-image: url(<?php echo $vg_logo_2x; ?>);
			background-size: auto 69px;
		}
	}
	@media only screen and (max-width: 480px) {
		h1#site-logo {
			display: none;
		}
	}
	#featured .slide_content {
		margin-top: 280px;
	}
	@-moz-document url-prefix() {
		#featured .slide_bg {
			margin-top:0px;
		}
	}
	</style>
	<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/media-queries.css" type="text/css" rel="stylesheet" />
	<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/joomla.css" type="text/css" rel="stylesheet" />
	<style><?php echo $vg_css; ?></style>

</head>

<body class="home-component">

<div id="container-component">
	
	<section id="vg-main-body-component" class="page-area">
		<div class="wrapper">
		
			<!-- mainbody -->
			<jdoc:include type="message" />
			<jdoc:include type="component" />
			<!-- mainbody -->
		
		</div>
	</section>
	
</div>


	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/retina.js"></script> 
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.hoverIntent.js"></script> 
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.scrollTo-1.4.3.1-min.js"></script> 
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.localscroll-1.2.7-min.js"></script> 
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.nicescroll.min.js"></script> 
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.sticky.js"></script> 
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.lazyload.min.js"></script> 
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/FlexSlider/jquery.flexslider-min.js"></script> 
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/colorbox/jquery.colorbox.js"></script> 
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.isotope.min.js"></script> 
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.contact-form.js"></script> 
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.tweet.js"></script> 
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.simplekey.js"></script> 
	<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.van.js"></script> 
	<script type="text/javascript">
	var pixel="functions/images/pixel.gif";
	var loadimg="functions/images/loader2.gif";
	</script>

	</body>
</html>