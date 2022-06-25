<?php
$flighttable = "OABS_flights";
$customertable = "OABS_customers";
$usertable = "OABS_user";
$offerstable = "OABS_offers";
$adstable = "OABS_adverts";
$sysname1 = "NAME OF AIRLINE";
$maxRows_offers = 4; //The Maximum number of offers shown on the front page
$domain = "DOMAIN NAME"; //The domain where OABS is, e.g markit.k1z.com or www.google.co.uk

$hostname_Database = "localhost";
$database_Database = "NAME OF DATABASE";
$username_Database = "USERNAME REQUIRED TO CONNECT TO DATABASE";
$password_Database = "PASSWORD";

//Do not edit below here
$Database = mysql_connect($hostname_Database, $username_Database, $password_Database) or die(mysql_error());

$opts["hn"] = $hostname_Database;
$opts["un"] = $username_Database;
$opts["pw"] = $password_Database;
$opts["db"] = $database_Database;
$opts["tb"] = $flighttable;

?>
