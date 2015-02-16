<?php
//header('Content-type: text/json');
	//database access credentials.
	
	$host = "localhost";
	$user = "netsetin_pix";
        $pass = "Pix@123";
	$db = "netsetin_pixovent";
        //“netsetin_pix” now has privileges on the database “netsetin_pixovent”.
	//Open connection.
        $con = mysql_connect($host, $user, $pass) or die('fatal'); 
	//Check the connection is open.   
	if (!$con) 
        {
		die('Could not connect: ' . mysql_error());
	}
	//Connect to the database 
	mysql_select_db($db, $con) or die('error 0xFF');
        $URL = "http://netset.internetoffice.co.in/GUN/Pixovent";
        //date_default_timezone_set('Europe/London');
	//date_default_timezone_set('Australia/Adelaide');
	error_reporting(0);
?>
                            
                            