<?php

/********************************* BASIC **************************************/

//------ logo ------//
if( $this->params->get( 'vg_logo' ) ){
	$vg_logo = $this->baseurl . '/' . $this->params->get( 'vg_logo' );
}else{
	$vg_logo = $this->baseurl . '/templates/' . $this->template . '/images/logo.png';
}

//------ logo hd ------//
if( $this->params->get( 'vg_logo_2x' ) ){
	$vg_logo_2x = $this->baseurl . '/' . $this->params->get( 'vg_logo_2x' );
}else{
	$vg_logo_2x = $this->baseurl . '/templates/' . $this->template . '/images/logo@2x.png';
}

//------ analytics ------//
$vg_analytics = $this->params->get( 'vg_analytics' );

//------ custom css ------//
$vg_css = $this->params->get( 'vg_css' );

//------ go top button ------//
$vg_gotop = $this->params->get( 'vg_gotop', 1 );

//------ loading mask ------//
$vg_loading = $this->params->get( 'vg_loading', 1 );

/********************************* ADVANCED ***********************************/

//------ menu one page ------//
$vg_menu_one_page = $this->params->get( 'vg_menu_one_page', 'mainmenu' );

//------ load active menu data ------//
$db = JFactory::getDBO();
$query = "SELECT * FROM #__menu WHERE menutype = '" . $vg_menu_one_page . "' AND type = 'url' AND published = 1 ORDER BY lft ASC";
	$db->setQuery( $query );
	$results = $db->loadObjectList();
	
	
/*

//------ fontlink ------//
$vg_fontlink = $this->params->get( 'vg_fontlink', 'http://fonts.googleapis.com/css?family=Open+Sans:400,600' );

//------ fontfamily ------//
$vg_fontfamily = $this->params->get( 'vg_fontfamily', '\'Open Sans\',Arial,sans-serif' );

//------ color ------//
$vg_color = $this->params->get( 'vg_color', '0,170,170' );
if( $vg_color == 'custom' ){
	//colorcustom
	$vg_colorcustom = $this->params->get( 'vg_colorcustom', '0,0,0' );
}else{
	//color
	$vg_colorcustom = $vg_color;
}
//custom color from url. EXAMPLE: ?vg_color=0,0,0
$vg_colorswitch = $this->params->get( 'vg_colorswitch', 1 );
if( $_GET['vg_color'] && $vg_colorswitch == 1 ){
	//colorcustom
	$vg_colorcustom = $_GET['vg_color'];
}
*/
?>