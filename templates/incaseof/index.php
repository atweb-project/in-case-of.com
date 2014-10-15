<?php
/**
 * @package    Joomla.Site
 * @subpackage  Templates.vg_simplekey
 * @copyright  Copyright (C) 2013 Valent�n Garc�a - http://www.valentingarcia.com.mx - All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
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

/****************************** IF DS DEPRECATED ******************************/

if( !defined('DS') ){
  define( 'DS',DIRECTORY_SEPARATOR );
}

/********************************* SITE DATA *********************************/

$app = JFactory::getApplication();
  $sitename = $app->getCfg('sitename');
  
  $itemid = JRequest::getVar('Itemid');
  $menu = &JSite::getMenu();
  $active = $menu->getItem($itemid);
  $params = $menu->getParams( $active->id );
  $pageclass = $params->get( 'pageclass_sfx' );
  
/****************************** MODULE POSITIONS ******************************/

require('includes/module_positions.php');

/********************************* PARAMS *************************************/

require('includes/template_params.php');

?>

<!doctype html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
  
  <jdoc:include type="head" />
  
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />

  <!-- css main -->
  <link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/style.css" rel="stylesheet" type="text/css" />
  
  <!-- virtue mart layout override -->
  <link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/vmsite-ltr.css" rel="stylesheet" type="text/css" />
  
  <!-- js -->
  <!--[if lt IE 9]>
  <script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/html5.js" type="text/javascript"></script>
  <![endif]-->
  <!--<script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery-1.8.3.min.js"></script>-->
  <script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jpreloader.min.js"></script>
  <script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.placeholder.js"></script>
  <script type="text/javascript">
  var isLoad=<?php echo $vg_loading; ?>; //1 - Enable preloading; 0 - Disable preloading
  var isMobile=0;
  //alert( navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry)/) );
  //alert(navigator.userAgent);
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
  
  <?php 
  //LOADING MASK
  if( $vg_loading == 0 ){ 
    echo 'body.home{ display:block; }'; 
  }
  ?>
  
  .vg-firstTitle{ font:160px/140px 'league_gothic'; }
  .vg-secondTitle{ font:72px/60px 'infinity'; }
  .vg-thirdTitle{ font:100px/80px 'league_gothic'; }
  .vg-fourthTitle{ font:36px/30px 'infinity'; }
  
  #vg-main-body h1, #vg-main-body-ajax h1, #vg-main-body-component h1{ font-family:'nexa_boldregular'; }
  #vg-main-body h2, #vg-main-body-ajax h2, #vg-main-body-component h2{ font-family:'nexa_lightregular'; }

  #primary-menu-container li.current-menu-item a { color: #ed1f24; }

  h1#site-logo a{
    background-image:url(<?php echo $vg_logo; ?>);
  }
  @media only screen and (-Webkit-min-device-pixel-ratio: 1.5), only screen and (-moz-min-device-pixel-ratio: 1.5), only screen and (-o-min-device-pixel-ratio: 3/2), only screen and (min-device-pixel-ratio: 1.5) {
    h1#site-logo a {
      /* background-image: url(<?php echo $vg_logo_2x; ?>);*/
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
<?php $this->setGenerator('In case of...break glass'); ?> 
</head>

<body class="home <?php echo $pageclass; ?>">

<div id="ajax-load">
  <div id="close">Close</div>
  <div id="ajax-content"></div>
</div>
<div class="ribbon">
	<div class="ribbonwrap">
		<a href="#handmadeone" style="cursor:pointer" class="biglink"> 
			<img src="images/hAND_MADE_LOGO.png" />
			<br>Unique & Hand Made!</a>
	</div>
</div>
<div style="display:none">
			<div id="handmadeone">
				<strong>In Case Of</strong> is proud to offer you one of a kind products. All products are of our design and they are crafted, finished, and painted by hand. As a result, every cabinet is unique and special because no units are the same. Each cabinet comes with serial number and a wax seal of authenticity.
Throughout the manufacturing and assembly process, all individual components as well as the product as a unit, are checked thoroughly for defects and/or deviations from our strict quality standards. 
			<br /><br /><img src="images/stories/virtuemart/product/craftman_v1.jpg" alt="handmade" />
			</div>
</div>

<header id="top">
  
  <?php if( $this->countModules('simplekey-top') )://START TOP ?>
  
  <div class="wrapper">
    
    <jdoc:include type="modules" name="simplekey-top" style="top" />
    
  </div>
  <?php endif;//END TOP ?>
  
  <nav id="primary-menu">
    <div class="wrapper">
      <h1 id="site-logo"><a href="index.php#top" title="<?php echo htmlspecialchars($sitename); ?>"></a></h1>
      <div id="primary-menu-container">
    
    <?php if( $this->countModules('simplekey-menu') )://START MAIN MENU ?>
    
      <jdoc:include type="modules" name="simplekey-menu" style="mymenu" />
    
    <?php else: ?>
      
        <p><span class="vg-alert-message"><?php echo JText::_('VG_SK_ALERT_MAIN_MENU'); ?></span></p>
      
    <?php endif;//END MAIN MENU ?>
      
      </div>
      
      <?php if( $this->countModules('simplekey-menu') )://START MOBILE MENU ?>
    
    <!--Mobile menu-->
    <div id="mobileMenu"></div>
      
    <?php endif;//END MOBILE MENU ?>
    
    </div>
  </nav>
  
  <?php if( $this->countModules('simplekey-slideshow') )://START SLIDESHOW ?>
  
  <jdoc:include type="modules" name="simplekey-slideshow" style="slideshow" />
  
  <?php endif;//END SLIDESHOW ?>
  
</header>

<div id="container">

  <?php if( $this->countModules('simplekey-maintop') )://START MAINTOP ?>
    
    <!-- maintop -->
    <section id="maintop" class="page-area">
      <div class="wrapper">
    
        <jdoc:include type="modules" name="simplekey-maintop" style="blockstop" />
    
      </div>
    </section>
    <!-- /maintop -->
  
  <?php endif;//END MAINTOP ?>
  
  <section id="vg-main-body" class="page-area">
    <div class="wrapper">
    
      <!-- mainbody -->
      <jdoc:include type="message" />
      <jdoc:include type="component" />
      <!-- mainbody -->
    
    </div>
  </section>
  
  <?php if( $this->countModules('simplekey-mainbottom') )://START MAINBOTTOM ?>
    
    <!-- maintop -->
    <section id="mainbottom" class="page-area">
      <div class="wrapper">
    
        <jdoc:include type="modules" name="simplekey-mainbottom" style="blockstop" />
    
      </div>
    </section>
    <!-- /maintop -->
  
  <?php endif;//END MAINBOTTOM ?>
  
  <?php if( $this->countModules('simplekey-onepagetop') )://START ONEPAGETOP ?>
    
    <!-- onepagetop -->
    <section id="onepagetop" class="page-area">
      <div class="wrapper">
    
        <jdoc:include type="modules" name="simplekey-onepagetop" style="blockstop" />
    
      </div>
    </section>
    <!-- /onepagetop -->
  
  <?php endif;//END ONEPAGETOP ?>
  
  <?php
  //just centinela
  $centinela = 2;
  
  //get every link with syntax '#link' from mainmenu (in db, table #_menu)
  foreach( $results as $result ) ://<--A1.
            
    //check if the link has '#', if not just ignoring
    if( strstr($result->link,'#section-') ){
              
      //split # from complete link. Example: '#link' turns into: 'link'.
      $link = explode( '#section-', $result->link );
      
      //params
      $menu_params = json_decode($result->params);

      //bg images. normal & HD
      if($menu_params->menu_image){
        $bg_hd_image = explode('.',$menu_params->menu_image);
      }else{
        $bg_hd_image = null;
      }
      echo '<style>
      #section-' . $link[1] . '{ background-image:url(' . JURI::base() . $menu_params->menu_image . '); }';
      
      //echo '/*' . JURI::base() . $bg_hd_image[0] . '@2x.' . $bg_hd_image[1] . '*/';
      
      //check if file HD exists (EXAMPLE: file@2x.jpg)      
      $vg_file_ = file_exists($bg_hd_image[0] . '@2x.' . $bg_hd_image[1]);
      if( $vg_file_ ){
        echo '@media only screen and (-Webkit-min-device-pixel-ratio: 1.5), only screen and (-moz-min-device-pixel-ratio: 1.5), only screen and (-o-min-device-pixel-ratio: 3/2), only screen and (min-device-pixel-ratio: 1.5) {
          #section-' . $link[1] . '{ background-image:url(' . JURI::base() . $bg_hd_image[0] . '@2x.' . $bg_hd_image[1] . '); }
        };';
      }
      
      echo '</style>';
      
      echo '<section id="section-' . $link[1] . '" class="page-area vg-section-image vg-color-' . $centinela . '">
        <div class="wrapper">';
        
          //title & subtitle
          if( $menu_params->menu_text == 1 || $result->note != '' ){//<-- B1.
          
            echo '<hgroup class="title ' . $menu_params->{'menu-anchor_css'} . '">';
          
              //title
              if($menu_params->menu_text == 1 ){
              
                if( $menu_params->{'menu-anchor_title'} != '' ){
                  echo '<h1><strong>' . $menu_params->{'menu-anchor_title'} . '</strong></h1>';
                }else{
                  echo '<h1><strong>' . $result->title . '</strong></h1>';
                }
                
              }
            
              //subtitle
              if( $result->note != '' ){ 
                echo '<p>' . $result->note . '</p>';
              }
            
            echo '</hgroup>';
          
          }//.B1 -->
    
          //generation a module position based in link. Example: link like '#section-link' converts into 'link' position
          echo '<jdoc:include type="modules" name="' . $link[1] . '" style="blocks" />';
        
          //advice the user that there is no modules for this position
          if( $this->countModules($link[1]) == 0 ){
            echo '<div class="vg-alert-modules">' . JText::_('VG_SK_MODULE_POSITION_ONEPAGE') . ' <strong>' . $link[1] . '</strong></div>';
          }
        
        echo '</div>
      </section>';
      
      //just switching centinela
      if( $centinela == 1 ){ $centinela = 2; }else{ $centinela = 1; }
    
    }
    
    
    
  endforeach;//.A1-->
  ?>
  
  <?php if( $this->countModules('simplekey-onepagebottom') )://START ONEPAGEBOTTOM ?>
    
    <!-- onepagebottom -->
    <section id="onepagebottom" class="page-area">
      <div class="wrapper">
    
        <jdoc:include type="modules" name="simplekey-onepagebottom" style="blockstop" />
    
      </div>
    </section>
    <!-- /onepagebottom -->
  
  <?php endif;//END BOTTOM ?>
  
</div>

<?php if( $this->countModules('simplekey-footerleft') or $this->countModules('simplekey-footerright') )://START FOOTER ?>

<footer id="footer">
  <div class="wrapper">
  
    <?php if( $this->countModules('simplekey-footerleft') )://START FOOTER LEFT ?>
    
    <!-- footer left -->
    <div class="footer-l">
    
      <jdoc:include type="modules" name="simplekey-footerleft" style="footer" />
    
    </div>
    <!-- /footer left -->
  
  <?php endif;//END FOOTER LEFT ?>
    
  <?php if( $this->countModules('simplekey-footerright') )://START FOOTER RIGHT ?>
    
    <!-- footer right -->
    <div class="footer-r">
    
      <jdoc:include type="modules" name="simplekey-footerright" style="footer" />
    
    </div>
    <!-- /footer right -->
  
  <?php endif;//END FOOTER RIGHT ?>
    
  </div>
</footer>

<?php endif;//END FOOTER ?>

<?php if( $vg_gotop == 1 ){ ?>
  <div id="backtoTop"></div>
<?php } ?>

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
  <script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.tweet.js"></script> 
  <script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.mobilemenu.js"></script>
  <script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.simplekey.js"></script> 
  <script type="text/javascript" src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/js/jquery.van.js"></script> 
  <script type="text/javascript">
  var pixel="images/pixel.gif";
  var loadimg="images/loader2.gif";
  
  /*mobile menu*/
  jQuery(document).ready(function($){
    $('#mobileMenu').html($('#primary-menu-container').html());
    //Atweb addon
    var vmCartModule = $('.vmCartModule').detach();
    vmCartModule.appendTo('#primary-menu');
    var $div = $('.vmCartModule');

    if ($div.length > 1) {
       $div.not(':last').remove()
    }//end of addon
    $('#mobileMenu').mobileMenu({
        defaultText: '<?php echo JText::_('VG_SK_LOADING_MOBILE_MENU'); ?>',
        className: 'select-menu',
        subMenuDash: '&nbsp;&nbsp;'
     });
     $(".select-menu").each(function(){  
      $(this).wrap('<div class="css3-selectbox">');      
     });
     $('#primary-menu-container li').each(function() {
      var i=1;
      if($(this).hasClass('none')) {
        $(this).remove();       
      }
    });
  });
  </script>
<?php // analytics ?>
<?php if( $vg_analytics ){//<--A4. ?>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("<?php echo $vg_analytics; ?>");
pageTracker._trackPageview();
} catch(err) {}
</script>

<?php }//A4.--> ?>

  </body>
</html>