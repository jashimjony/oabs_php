<HTML>
<HEAD><TITLE>OABS Upgrade</TITLE></HEAD>
<?
include ("Connections/Database.php");
mysql_select_db($database_Database, $Database);
$query = "CREATE TABLE `".$adstable."` (
  `id` int(4) NOT NULL auto_increment,
  `adcode` text NOT NULL,
  `impcount` int(10) NOT NULL default '0',
  `implimit` int(10) NOT NULL default '0',
  `address` text NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";
mysql_query($query, $Database) or die(mysql_error());
$query = "INSERT INTO `".$adstable."` ( `id` , `adcode` , `impcount` , `implimit` , `address` , `email` ) VALUES ('', '<img src=`http://markit.k1z.com/images/OABS-AD.gif` border=`0`>', '0', '5000', 'http://markit.k1z.com/oabs', 'mark-goodall@tiscali.co.uk')";
mysql_query($query, $Database) or die(mysql_error());
?>
<BR>If there are no errors above then the installation was sucessful! Now delete the file to prevent loss of data aswell as install.php.
