<?php
// Login that works v20394.4
include("../Connections/Database.php");
mysql_select_db($database_Database);
$sql = "SELECT * FROM ".$usertable." WHERE Name = '".$_POST['Name']."' AND Password = '".$_POST['Password']."' LIMIT 1";
$num = mysql_num_rows(mysql_query($sql));
if($num == 1) {
    setcookie("LoggedIn", $_POST['Name'], time()+(3600 * 24),"/",$domain);
    header("Location: admin.php"); 
}
else {
    header("Location:index.php");
}
?>

