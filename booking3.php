<?
  if (((!isset($_POST['name'])) | (!isset($_POST['address1'])) | (!isset($_POST['address2'])) | (!isset($_POST['county'])) | (!isset($POST['postcode'])) | (!isset($_POST['class']))) == FALSE) {
header("Location:booking2.php?this=is2&missing=1&flight=" .$_POST['flight']);
} else {
require_once('Connections/Database.php');
mysql_select_db($database_Database, $Database);
$sql = "SELECT ".$_POST['class']." FROM ".$flighttable." WHERE number='".$_POST['flight']."'";
$row = mysql_fetch_row(mysql_query($sql));
$total = $_POST['adults'] + $_POST['children'] + $_POST['seniors'];
if ($total > $row[0]) {
header("Location:booking2.php?full=1&flight=" .$_POST['flight']);
}
else {
if (!ereg ("([0-9]{1,2})", $_POST['adults'], $unused)) {
header("Location:booking2.php?missing=1&flight=" .$flight);
}
if (!ereg ("([0-9]{1,2})", $_POST['seniors'], $unused)) {
header("Location:booking2.php?missing=1&flight=" .$flight);
}
if (!ereg ("([0-9]{1,2})", $_POST['children'], $unused)) {
header("Location:booking2.php?missing=1&flight=" .$flight);
} ?>
<HTML><HEAD><TITLE><?php echo $sysname1; ?></TITLE>
<META http-equiv=Content-Type content="text/html; charset=windows-1252">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<STYLE type=text/css>A {
	TEXT-DECORATION: none
}
BODY {
	FONT-FAMILY: verdana, arial, helvetica, sans-serif
}
TD {
	FONT-FAMILY: verdana, arial, helvetica, sans-serif
}
B {
	FONT-FAMILY: verdana, arial, helvetica, sans-serif
}
</STYLE>
</HEAD>
<BODY vLink=#6f6c81 link=#486591 bgcolor="#8FC5EC">

<TABLE width="927" border=0>
  <TBODY>
  <TR>
      <TD width="182"> 
        <TABLE border=0 align="center" cellPadding=0 cellSpacing=0>
          <TBODY>
            <TR> 
              <TD width=15 background="images/coininfg.gif" 
            height=15><IMG height=15 alt="" 
            src="images/space15_15.gif" width=15></TD>
              <TD background="images/inf.gif" height=15><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD>
              <TD width=15 background="images/coininfd.gif" 
            height=15><IMG height=15 alt="" 
            src="images/space15_15.gif" width=15></TD>
            </TR>
            <TR> 
              <TD width=15 background="images/g.gif"><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD>
              <TD noWrap align=left background="images/fond.gif"> <b><?php echo $sysname1; ?></b></TD>
              <TD width=15 background="images/d.gif"><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD>
            </TR>
            <TR> 
              <TD width=15 background="images/coinsupg.gif" 
            height=15><IMG height=15 alt="" 
            src="images/space15_15.gif" width=15></TD>
              <TD background="images/sup.gif" height=15><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD>
              <TD width=15 background="images/coinsupd.gif" 
            height=15><IMG height=15 alt="" 
            src="images/space15_15.gif" 
    width=15></TD>
            </TR>
          </TBODY>
        </TABLE></TD>
      <TD align=center width="731">&nbsp;</TD>
    </TR></TBODY></TABLE>
<TABLE cellSpacing=2 cellPadding=0 width="100%">
  <TBODY>
  <TR>
    <TD vAlign=top>
      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
        <TBODY>
        <TR>
          <TD width=15 background="images/coininfg.gif" 
            height=15><IMG height=15 alt="" 
            src="images/space15_15.gif" width=15></TD>
          <TD background="images/inf.gif" height=15><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD>
          <TD width=15 background="images/coininfd.gif" 
            height=15><IMG height=15 alt="" 
            src="images/space15_15.gif" width=15></TD></TR>
        <TR>
          <TD width=15 background="images/g.gif"><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD>
              <TD align=left width="100%" 
            background="images/fond.gif"><A 
            href="index.php"><b>::Main</b></A> 
                <TABLE>
              <TBODY>
              <TR>
                      <TD noWrap>&nbsp;<a href="booking.php">::Booking </a><BR>
                        &nbsp;<a href="details.php">::Flight Details</a> <BR>
                        &nbsp;<a href="admin/index.php">::Admin</a> <BR>
                        &nbsp;<a href="help.php">::Help</a> </TD>
                    </TR></TBODY></TABLE></TD>
          <TD width=15 background="images/d.gif"><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD></TR>
        <TR>
          <TD width=15 background="images/coinsupg.gif" 
            height=15><IMG height=15 alt="" 
            src="images/space15_15.gif" width=15></TD>
          <TD background="images/sup.gif" height=15><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD>
          <TD width=15 background="images/coinsupd.gif" 
            height=15><IMG height=15 alt="" 
            src="images/space15_15.gif" 
    width=15></TD></TR></TBODY></TABLE></TD>
    <TD width="100%">
      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
        <TBODY>
        <TR>
          <TD width=15 background="images/t-infg.gif" height=8><IMG 
            height=8 alt="" src="images/space15_15.gif" width=15></TD>
          <TD background="images/t-inf.gif" height=8><IMG height=8 
            alt="" src="images/space15_15.gif" width=15></TD>
          <TD width=15 background="images/t-infd.gif" height=8><IMG 
            height=8 alt="" src="images/space15_15.gif" 
        width=15></TD></TR>
        <TR>
          <TD width=15 background="images/t-g.gif"><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD>
          <TD align=middle width="100%" bgColor=#e7edfe>
            <p align="left"><font face="Arial, Helvetica, sans-serif" color="#777777"><b>About</b></font></p>
          </TD>
          <TD width=15 background="images/t-d.gif"><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD></TR>
        <TR height=1>
          <TD width=15 background="images/b-g.gif" height=1><IMG 
            height=1 alt="" src="images/space15_15.gif" width=15></TD>
          <TD bgColor=#777777 height=1><IMG height=1 
            src="images/space15_15.gif" width=15></TD>
          <TD width=15 background="images/b-d.gif" height=1><IMG 
            height=1 alt="" src="images/space15_15.gif" 
        width=15></TD></TR>
        <TR>
          <TD width=15 background="images/g.gif"><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD>
              <TD align=left width="100%" 
            background="images/fond.gif">This is the final stage of the booking. Before continuing you must be sure that this information is correct to ensure you travel stress free with us. When you are sure that it is correct, press confirm to continue.</TD>
          <TD width=15 background="images/d.gif"><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD></TR>
        <TR>
          <TD width=15 background="images/coinsupg.gif" 
            height=15><IMG height=15 alt="" 
            src="images/space15_15.gif" width=15></TD>
          <TD background="images/sup.gif" height=15><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD>
          <TD width=15 background="images/coinsupd.gif" 
            height=15><IMG height=15 alt="" 
            src="images/space15_15.gif" width=15></TD></TR></TBODY></TABLE>
      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
        <TBODY>
        <TR>
          <TD width=15 background="images/t-infg.gif" height=8><IMG 
            height=8 alt="" src="images/space15_15.gif" width=15></TD>
          <TD background="images/t-inf.gif" height=8><IMG height=8 
            alt="" src="images/space15_15.gif" width=15></TD>
          <TD width=15 background="images/t-infd.gif" height=8><IMG 
            height=8 alt="" src="images/space15_15.gif" 
        width=15></TD></TR>
        <TR>
          <TD width=15 background="images/t-g.gif"><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD>
          <TD align=middle width="100%" bgColor=#e7edfe>
            <p align="left"><font face="Arial, Helvetica, sans-serif" color="#777777"><b>Please Confirm</b></font></p>
          </TD>
          <TD width=15 background="images/t-d.gif"><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD></TR>
        <TR height=1>
          <TD width=15 background="images/b-g.gif" height=1><IMG 
            height=1 alt="" src="images/space15_15.gif" width=15></TD>
          <TD bgColor=#777777 height=1><IMG height=1 
            src="images/space15_15.gif" width=15></TD>
          <TD width=15 background="images/b-d.gif" height=1><IMG 
            height=1 alt="" src="images/space15_15.gif" 
        width=15></TD></TR>
        <TR>
          <TD width=15 background="images/g.gif"><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD>
              <TD align=left width="100%" 
            background="images/fond.gif"> 
Name: <?php echo $_POST['name'] ?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td width="65">Address: </td>
<td width="100%"><?php echo $_POST['address1']; ?></td>
</tr>
<tr>
<td width="63"></td>
<td width="100%"><?php echo $_POST['address2']; ?></td>
</tr>
<tr>
<td width="63"></td>
<td width="100%"><?php echo $_POST['county']; ?></td>
</tr>
<tr>
<td width="63"></td>
<td width="100%"><?php echo $_POST['postcode']; ?></td>
</tr>
</table>
<?
include ("Connections/Database.php");
mysql_select_db($database_Database, $Database);
$query = "SELECT * FROM ".$flighttable." WHERE number='" .$_POST['flight']."' LIMIT 1";
$temp = mysql_query($query, $Database) or die(mysql_error());
$temp2 = mysql_fetch_array($temp);
if (!ereg ("([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})", $temp2['datetime'], $regs)) {
    echo "Invalid date format";
}
if (!ereg ("([a-z]{4})([a-z]{1})", $_POST['class'], $what)) {
    echo "Invalid: class classification";
}
$totalcost = ($_POST['adults']*$temp2['price' .$what[2]])+($_POST['seniors']*($temp2['price' .$what[2]]-$temp2['senior']))+($_POST['children']*($temp2['price' .$what[2]]-$temp2['child']));
?>
<div align="center">
  <table border="0" cellpadding="0" cellspacing="0" width="80%">
    <tr>
      <td width="100%" colspan="2" bgcolor="#8FC5EC">Destination is <B><? echo $temp2['Dest'] ?></B> on
        flight number <B><? echo $_POST['flight']; ?></B> on <B><? echo "$regs[3]/$regs[2]/$regs[1]"; ?></B> at <B><? echo $regs[4] ?><? echo $regs[5] ?></B> hours.</td>
    </tr>
    <tr>
      <td width="50%" bgcolor="#8FC5EC">&nbsp;</td>
      <td width="50%" bgcolor="#8FC5EC">Totals</td>
    </tr>
    <tr>
      <td width="50%" bgcolor="#E7EDFE"><? if ($_POST['adults'] == 0) {
echo "0 x Adults";
} elseif ($_POST['adults'] == 1) {
echo "1 x Adult";
} else {
echo $_POST['adults'];
echo " x Adults"; } ?>
</td>
      <td width="50%" bgcolor="#E7EDFE">£<? echo $_POST['adults']*$temp2['price' .$what[2]] ?></td>
    </tr>
    <tr>
      <td width="50%" bgcolor="#E7EDFE"><? if ($_POST['seniors'] == 0) {
echo "0 x Seniors";
} elseif ($_POST['seniors'] == 1) {
echo "1 x Senior";
} else {
echo $_POST['seniors'];
echo " x Seniors"; } ?></td>
      <td width="50%" bgcolor="#E7EDFE">£<? echo $_POST['seniors']*($temp2['price' .$what[2]]-$temp2['senior']) ?></td>
    </tr>
    <tr>
      <td width="50%" bgcolor="#E7EDFE"><? if ($_POST['children'] == 0) {
echo "0 x Children";
} elseif ($_POST['children'] == 1) {
echo "1 x Child";
} else {
echo $_POST['children'];
echo " x Children"; } ?></td>
      <td width="50%" bgcolor="#E7EDFE">£<? echo $_POST['children']*($temp2['price' .$what[2]]-$temp2['child']) ?></td>
    </tr>
  </center>
  <tr>
    <td width="50%" bgcolor="#E7EDFE">
      <p align="right">Grand Total</td>
    <center>
    <td width="50%" bgcolor="#E7EDFE">£<? echo $totalcost ?></td>
    </tr>
  </table>
</div>
<form method="POST" action="booking4.php">
<input type="hidden" name="flight" value="<? echo $_POST['flight'] ?>">
<input type="hidden" name="name" value="<? echo $_POST['name'] ?>">
<input type="hidden" name="address1" value="<? echo $_POST['address1'] ?>">
<input type="hidden" name="address2" value="<? echo $_POST['address2'] ?>">
<input type="hidden" name="county" value="<? echo $_POST['county'] ?>">
<input type="hidden" name="postcode" value="<? echo $_POST['postcode'] ?>">
<input type="hidden" name="class" value="<? echo $_POST['class'] ?>">
<input type="hidden" name="totalseats" value="<? echo $total ?>">
<input type="hidden" name="totalcost" value="<? echo $totalcost ?>">
<input type="hidden" name="adults" value="<? echo $_POST['adults'] ?>">
<input type="hidden" name="seniors" value="<? echo $_POST['seniors'] ?>">
<input type="hidden" name="children" value="<? echo $_POST['children'] ?>">
<input type="submit" value="Confirm">
</form>
          <TD width=15 background="images/d.gif"><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD></TR>
        <TR>
          <TD width=15 background="images/coinsupg.gif" 
            height=15><IMG height=15 alt="" 
            src="images/space15_15.gif" width=15></TD>
          <TD background="images/sup.gif" height=15><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD>
          <TD width=15 background="images/coinsupd.gif" 
            height=15><IMG height=15 alt="" 
            src="images/space15_15.gif" width=15></TD></TR></TBODY></TABLE>
        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
          <TBODY>
            <TR> 
              <TD width=15 background="images/t-infg.gif" height=8><IMG 
            height=8 alt="" src="images/space15_15.gif" width=15></TD>
              <TD background="images/t-inf.gif" height=8><IMG height=8 
            alt="" src="images/space15_15.gif" width=15></TD>
              <TD width=15 background="images/t-infd.gif" height=8><IMG 
            height=8 alt="" src="images/space15_15.gif" 
        width=15></TD>
            </TR>
            <TR> 
              <TD width=15 background="images/t-g.gif"><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD>
              <TD align=middle width="100%" bgColor=#e7edfe><div align="left"><font face="Arial, Helvetica, sans-serif" color="#777777"><b>Sponsor</b></font></div></TD>
              <TD width=15 background="images/t-d.gif">&nbsp;</TD>
            </TR>
            <TR height=1> 
              <TD width=15 background="images/b-g.gif" height=1><IMG 
            height=1 alt="" src="images/space15_15.gif" width=15></TD>
              <TD bgColor=#777777 height=1><IMG height=1 
            src="images/space15_15.gif" width=15></TD>
              <TD width=15 background="images/b-d.gif" height=1><IMG 
            height=1 alt="" src="images/space15_15.gif" 
        width=15></TD>
            </TR>
            <TR> 
              <TD width=15 background="images/g.gif"><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD>
              <TD width="100%" align=center valign="top" 
            background="images/fond.gif"><p><?php include("advert.php") ?><FONT
            face="Arial, Helvetica, sans-serif"> </FONT></p>
                </TD>
              <TD width=15 background="images/d.gif"><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD>
            </TR>
            <TR> 
              <TD width=15 background="images/coinsupg.gif" 
            height=15><IMG height=15 alt="" 
            src="images/space15_15.gif" width=15></TD>
              <TD background="images/sup.gif" height=15><IMG height=15 
            alt="" src="images/space15_15.gif" width=15></TD>
              <TD width=15 background="images/coinsupd.gif" 
            height=15><IMG height=15 alt="" 
            src="images/space15_15.gif" 
    width=15></TD>
            </TR>
          </TBODY>
        </TABLE></TD>
    </TR></TBODY></TABLE><?php include("footer.php") ?></BODY></HTML>
<?php
}
}
?>
