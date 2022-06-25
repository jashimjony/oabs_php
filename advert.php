<?
// MySQL Driven symple advert system by Markit 2K3
require_once('Connections/Database.php');
mysql_select_db($database_Database, $Database);
srand ((double) microtime() * 1000000);

IF (empty($_GET['action'])) {
$query = "SELECT id FROM ".$adstable;
$result = mysql_query($query, $Database) or die(mysql_error());
$id = mysql_num_rows($result);
$randomnumber = rand(0,$id - 1);
$query = "SELECT * FROM ".$adstable." ORDER By id Asc LIMIT ".$randomnumber.",1";
$result = mysql_query($query, $Database) or die(mysql_error());
$id = mysql_fetch_array($result);
echo "<a href='".$id[address]."'>".$id[adcode]."</a>";
$impcount = $id[impcount] + 1;
$query = "UPDATE ".$adstable." SET impcount=".$impcount." WHERE id=".$id[id];
$result = mysql_query($query, $Database) or die(mysql_error());
if ($impcount == $id[implimit]) {
$query = "DELETE FROM ".$adstable." WHERE id=".$id[id];
$result = mysql_query($query, $Database) or die(mysql_error());
mail( $id[email], "Your Advert at ".$sysname1, "This is a message to inform you that your advert has reached the impression count you bought (".$id[implimit]."). The advert has been taken down and will no longer be displayed. If you want the advert put up again please contact us.", "From: No-reply@OABS" );
}

} elseif ($_GET['action'] == "admin") {

if(!isset($_COOKIE['LoggedIn'])) die("You are not logged in!");
?>
<h3>Advert Admin</h3>
<p><a href="advert.php?action=add">Add Advert</a></p>
<p>Stats: -</p>
<?
$query = "SELECT * FROM ".$adstable;
$result = mysql_query($query, $Database) or die(mysql_error());
?>
<table border="0" cellpadding="0" cellspacing="0" width="800"><tr>
<td>Web Address</td>
<td>Advert Code</td>
<td>Impressions Counted</td>
<td>Impressions Limit</td>
<td>Email</td></tr>
<? do { ?>
<tr>
      <td><a href="<? echo $array['address']; ?>"><? echo $array['address']; ?></a></td>
      <td><? echo $array['adcode']; ?></td>
      <td><? echo $array['impcount']; ?></td>
      <td><? echo $array['implimit']; ?></td>
      <td><a href="mailto:<? echo $array['email']; ?>"><? echo $array['email']; ?></a></td>
</tr>
<?
} while ($array = mysql_fetch_array($result,MYSQL_ASSOC));
?>
</table>
<?

} elseif ($_GET['action'] == "add") {

if(!isset($_COOKIE['LoggedIn'])) die("You are not logged in!");

echo "this is under construction, you can use phpMyAdmin or a similar system instead though.";

} elseif ($_GET['action'] == "get") {

echo "Adverts cost money and this is under construction."; 

}
?>
