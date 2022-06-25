<?
$seatsprep['adults'] = $_POST['adults'];
$seatsprep['seniors'] = $_POST['seniors'];
$seatsprep['children'] = $_POST['children'];
$seats = join ("+",$seatsprep);
include ("Connections/Database.php");
mysql_select_db($database_Database, $Database);
$query = "INSERT INTO ".$customertable." (name, address1, address2, county, postcode, seats, number, class) VALUES ('".$_POST['name']."', '".$_POST['address1']."', '".$_POST['address2']."', '".$_POST['county']."', '".$_POST['postcode']."', '".$seats."', '".$_POST['flight']."', '".$_POST['class']."')";
mysql_query($query, $Database) or die(mysql_error());
$query = "SELECT passb, passf, passe, cost FROM ".$flighttable." WHERE number=" .$_POST['flight'];
$temp = mysql_query($query, $Database) or die(mysql_error());
$temp2 = mysql_fetch_array($temp);
$pass = $temp2[$_POST['class']]-($_POST['adults']+$_POST['seniors']+$_POST['children']);
$cost = $temp2['cost']-$_POST['totalcost'];
$query = "UPDATE ".$flighttable." SET ".$_POST['class']." = ".$pass." WHERE number=".$_POST['flight'];
mysql_query($query, $Database) or die(mysql_error());
$query = "UPDATE ".$flighttable." SET cost = $cost WHERE number=".$_POST['flight'];
mysql_query($query, $Database) or die(mysql_error());
header("Location:index.php?confirm=1");
?>
