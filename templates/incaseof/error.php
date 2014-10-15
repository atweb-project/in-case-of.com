<?php
/**
 * @package		Joomla.Site
 * @subpackage	Templates.vg_simplekey
 * @copyright	Copyright (C) 2013 Valentín García - http://www.valentingarcia.com.mx - All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 
 // No direct access.
defined('_JEXEC') or die;

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
	var isLoad=1; //1 - Enable preloading; 0 - Disable preloading
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
	
	#vg-main-body h1{ font-family:'nexa_boldregular'; }
	#vg-main-body h2{ font-family:'nexa_lightregular'; }

	#primary-menu-container li.current-menu-item a { color: #f26c4f; }
	</style>
	<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/media-queries.css" type="text/css" rel="stylesheet" />
	<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/joomla.css" type="text/css" rel="stylesheet" />

</head>

<body class="home-component">

<div id="container-component">
	
	<section id="vg-main-body" class="page-area">
		<div class="wrapper vg-sk-404">
		
			<a title="404" class="vg-sk-404-logo" href="<?php echo $this->baseurl; ?>"></a>
			<h1>404</h1>
			<p>
				<?php echo JText::_('VG_SK_404'); ?>
				<br/>
				<a href="<?php echo $this->baseurl; ?>">&raquo; <?php echo Jtext::_('VG_SK_404_HOME'); ?></a> or <a href='javascript:history.go(-1)'>&raquo; <?php echo Jtext::_('VG_SK_404_RETURN'); ?></a>
			</p>
		
		</div>
	</section>
	
</div>

	</body>
</html>