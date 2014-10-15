<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006-12 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $database, $my;

$count = $params->get( 'count', 5 );
$linkcomt = $params->get( 'linkcomt', yes );
$showuser = $params->get( 'showuser', yes );
$cbint = $params->get( 'cbint', no );
$date_format = $params->get( 'dateformat', M.d );
$diconsize = $params->get( 'diconsize', 16 );

$database->setQuery("SELECT id, (CASE menutype WHEN 'mainmenu' THEN 1 WHEN 'topmenu' THEN 2 WHEN 'othermenu' THEN 3 ELSE 99 END) menorder"
." FROM #__menu WHERE link = 'index.php?option=com_remository' AND published=1 ORDER BY menorder");
$Itemid = $database->LoadResult();

$query = "SELECT #__downloads_reviews.itemid, #__downloads_reviews.userid, #__downloads_reviews.comment, #__downloads_reviews.date, #__users.username"
	. "\n FROM #__downloads_reviews"
	. "\n LEFT JOIN #__users ON #__users.id = #__downloads_reviews.userid"
	. "\n ORDER BY date DESC"
	. "\n LIMIT $count"
	;
$database->setQuery($query);
$rows = $database->loadObjectList();
echo $database->getErrorMsg();

if ($diconsize) $dateurl = "<img src='components/com_remository/images/calendar.gif' width='$diconsize' height='$diconsize' alt='Date icon' />";
else $dateurl = '';
			
foreach($rows as $row) {
	$datetext = date($date_format, strtotime($row->date));
	if ($cbint == 'yes') $commenter = "<a href='index.php?option=com_comprofiler&task=userProfile&user=$row->userid'>$row->username</a>";
	else $commenter = "$row->username";
	if ($linkcomt == 'yes') echo '<div><a href="'.sefRelToAbs('index.php?option=com_remository&Itemid='.$Itemid.'&func=fileinfo&id='.$row->itemid).'">'.$row->comment.'</a></div>';
	else echo '<div>'.$row->comment.'</div>';
	if ($showuser == 'yes') echo '<div>Posted by:&nbsp;'.$commenter.'</div>';
	if (strtolower($date_format) != 'none') echo "<div>".$dateurl.$datetext.'</div>';
	echo "<p></p>";
}