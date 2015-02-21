<?php
	error_reporting(1);
	
	error_reporting(E_ALL);
ini_set('display_errors', 1);
    $host = "pixo.ciox5m125pj3.us-west-2.rds.amazonaws.com";
	$user = "pixou";
        $pass = "pixopass";
	$db = "pixodb";
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
        $URL = "http://52.0.15.16/";
        //date_default_timezone_set('Europe/London');
	//date_default_timezone_set('Australia/Adelaide');

?>
                            
                            
