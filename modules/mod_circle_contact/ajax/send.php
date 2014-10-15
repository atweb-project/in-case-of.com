<?php
if( !empty($_REQUEST['email']) && filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL) && !empty($_REQUEST['contactName']) && !empty($_REQUEST['comments']) ){

		//$the_blogname   = $_REQUEST['subject'];
		$the_myemail 	= $_REQUEST['emailto1'] . '@' . $_REQUEST['emailto2'];
		$the_email 		= $_REQUEST['email'];
		//$the_phone 		= $_REQUEST['phone'];
		$the_name 		= $_REQUEST['contactName'];
		$the_message 	= $_REQUEST['comments'];

			$to      =  $the_myemail;
			$subject = "New Message from " . $the_name;
			$header  = 'MIME-Version: 1.0' . "\r\n";
			$header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$header .= 'From:'. $the_email  . " \r\n";
			$message = "
			<a href='mailto:$the_email'>$the_name</a> sent you a message: <br/><br />			
			$the_message <br /><br />";
			
			if(@mail($to,$the_name,$message,$header)) $send = true; else $send = false;

			if($send){
				echo 'OK';
			}else{
				echo 'ERROR'; 
			}

}
?>