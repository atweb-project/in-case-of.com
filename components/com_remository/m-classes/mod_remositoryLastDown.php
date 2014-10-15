<?php

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/*********************Configuration*********************/
if (method_exists($params, 'get')) {
// Max number of entries to show
	$max = intval( $params->get( 'max', 15 ) );
// Style attributes for table formatting
	$tabstyle = $params->get( 'tabstyle', '' );
// 0 - show all logged events; 1 - show downloads; 2 - show uploads; 3 - show votes
	$showtype = intval( $params->get( 'showtype', 0 ) );
// Set to '1' to show guest downloads, set to 0 to not show them
	$showguests = intval( $params->get( 'showguests', 0 ) );
// URL string to prepend to IP of unregistered users
	$dnslookup = $params->get( 'dnslookup', '' );
}
else {
	$max = $params->max;
	if (!$max) $max = 15;
	$tabstyle = $params->tabstyle;
	if (!$tabstyle) $tabstyle = '';
	$showtype = $params->showtype;
	if (!is_numeric($showtype)) $showtype = 0;
	if (strlen($showtype)==0) $showtype = 0;
	$showguests = $params->showguests;
	if (!is_numeric($showguests)) $showguests = 0;
	if (strlen($showguests)==0) $showguests = 0;
	$dnslookup = $params->dnslookup;
	if (!$dnslookup) $dnslookup = '';
}
if (!is_numeric($max)) $max=15;
$max = max($max,1);

// If set to 1 record is shown in table. Setting start value here.
$showrecord=1;
/*******************************************************/

$content ="";

$query=("SELECT d.type, d.date, d.userid, d.fileid, d.ipaddress, u.name, f.filetitle, f.downloads"
."\n FROM #__users AS u"
."\n RIGHT JOIN #__downloads_log AS d ON d.userid = u.id"
."\n INNER JOIN #__downloads_files AS f ON d.fileid = f.id"
."\n ORDER BY d.date desc LIMIT $max");

$database->setQuery($query);

$meslist = $database->loadObjectList();

//show filtered users, events
$filter = "(";
if ($showguests==0) {
	$filter.="no guests";
} else {
	$filter.="all users";
}
$filter.="/";
if ($showtype==0) {
	$filter.="all events";
} elseif ($showtype==1) {
	$filter.="downloads only";
} elseif ($showtype==2) {
	$filter.="uploads only";
} elseif ($showtype==3) {
	$filter.="votes only";
}
$filter.=")";
?>

<table class="adminlist" <?php echo " style='".$tabstyle."'" ?>>
<tr>
	<th>Type</th>
	<th>Date</th>
	<th>Time</th>
	<th>File <span style="font-size:80%;"><?php echo $filter; ?></span></th>
	<th>Hits</th>
	<th>Initiated by</th>
</tr>

<?php
foreach ($meslist as $mes)
{
   $type=$mes->type;
   if ($showtype != $type) {
	  if ($showtype != 0) {
		$showrecord = 0;
	  }
   }
   switch ($type) {
	  case 1:
		$type="DL";
		break;
	  case 2:
		$type="UL";
		break;
	  case 3:
		$type="Vote";
		break;
	  default:
		$type="??";
   }
   $user=$mes->name;
   if (!$user) {
	  $user="Guest";
	  if ($showguests==0) {
		$showrecord=0;
	  }
   }
   if ($showrecord==1) {
	$file=$mes->filetitle;
	$fileid=$mes->fileid;
	$userid=$mes->userid;
	$hits=$mes->downloads;
	$ipaddress=$mes->ipaddress;
	$vis=$mes->date;
	$visit=strtotime($vis);
	$ddate = date("d.m.y",$visit);
	if ($ddate==date("d.m.y")) {
		$ddate="today";
	}
	$dtime = date("H:i",$visit);
	$content.="<tr><td>".$type."</td>"
		."\n <td>".$ddate."</td>"
		."\n <td>".$dtime."</td>"
		."\n <td><a href='?option=com_remository&task=editfile&cfid=".$fileid."'>".$file."</a></td>"
		."\n <td>".$hits."</td>";
	if ($userid==0) {
		if ($dnslookup=='') {
			$content.="<td>".$ipaddress."</td></tr>";
		} else {
			$content.="<td><a href=".$dnslookup.$ipaddress." target='_blank'>".$ipaddress."</a></td></tr>";
		}
	} else {
		$content.="<td><a href='?option=com_users&task=editA&id=".$userid."'>".$user."</a></td></tr>";
	}
   }
   $showrecord=1;
}
echo($content);
?>

</table>
