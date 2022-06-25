<HTML>
<HEAD><TITLE>OABS Installation</TITLE></HEAD>
<?
if (empty($_POST['user']) || empty($_POST['pass'])) {
?>
<BODY>
Before installing please ensure that connections/database.php is correct. If you fill the form in incorrectly the password and username is available using phpMyAdmin or something similar.<BR>
Fill in form:<BR>
<form method="POST" action="install.php">
Admin Username: <input type="text" name="user" size="20"><BR>
Admin Password: <input type="password" name="pass" size="20"><BR>
<input type="submit" value="Submit" name="submit">
</form>
</body>
<?
} else {
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
$query = "CREATE TABLE `".$customertable."` (
  `name` text NOT NULL,
  `address1` text NOT NULL,
  `address2` text NOT NULL,
  `county` text NOT NULL,
  `postcode` text NOT NULL,
  `customer` tinyint(4) NOT NULL auto_increment,
  `seats` text NOT NULL,
  `number` tinyint(4) NOT NULL default '0',
  `class` text NOT NULL,
  PRIMARY KEY  (`customer`),
  UNIQUE KEY `customer` (`customer`)
) TYPE=MyISAM;";
mysql_query($query, $Database) or die(mysql_error());
$query = "CREATE TABLE `".$flighttable."` (
  `number` tinyint(4) NOT NULL auto_increment,
  `Dest` text NOT NULL,
  `datetime` timestamp(14) NOT NULL,
  `passb` int(4) NOT NULL default '0',
  `passf` int(4) NOT NULL default '0',
  `passe` int(4) NOT NULL default '0',
  `cost` int(10) NOT NULL default '0',
  `priceb` int(4) NOT NULL default '0',
  `pricef` int(4) NOT NULL default '0',
  `pricee` int(4) NOT NULL default '0',
  `senior` int(4) NOT NULL default '0',
  `child` int(4) NOT NULL default '0',
  PRIMARY KEY  (`number`),
  UNIQUE KEY `number` (`number`)
) TYPE=MyISAM;";
mysql_query($query, $Database) or die(mysql_error());
$query = "CREATE TABLE `".$offerstable."` (
  `no` tinyint(4) NOT NULL auto_increment,
  `offer` longtext NOT NULL,
  KEY `no` (`no`)
) TYPE=MyISAM;";
mysql_query($query, $Database) or die(mysql_error());
$query = "CREATE TABLE `".$usertable."` (
  `ID` smallint(3) NOT NULL auto_increment,
  `Name` varchar(30) NOT NULL default '',
  `Password` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `Name` (`Name`)
) TYPE=MyISAM;";
mysql_query($query, $Database) or die(mysql_error());
$query = "INSERT INTO ".$usertable." (`ID`, `Name`, `Password`) VALUES ('', '".$_POST['user']."', '".$_POST['pass']."')";
mysql_query($query, $Database) or die(mysql_error());
$query = "INSERT INTO ".$offerstable." (`no`, `offer`) VALUES ('', '[blank]')";
mysql_query($query, $Database) or die(mysql_error());
$query = "INSERT INTO ".$offerstable." (`no`, `offer`) VALUES ('', '[blank]')";
mysql_query($query, $Database) or die(mysql_error());
$query = "INSERT INTO ".$offerstable." (`no`, `offer`) VALUES ('', '[blank]')";
mysql_query($query, $Database) or die(mysql_error());
$query = "INSERT INTO ".$offerstable." (`no`, `offer`) VALUES ('', '[blank]')";
mysql_query($query, $Database) or die(mysql_error());
$query = "INSERT INTO `".$adstable."` ( `id` , `adcode` , `impcount` , `implimit` , `address` , `email` ) VALUES ('', '<img src=`http://markit.k1z.com/images/OABS-AD.gif` border=`0`>', '0', '5000', 'http://markit.k1z.com/oabs', 'mark-goodall@tiscali.co.uk');";
mysql_query($query, $Database) or die(mysql_error());
?>
<BR>If there are no errors above then the installation was sucessful! Now delete the file to prevent loss of data.
<?
}
?>
