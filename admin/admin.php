<?php
if(!isset($_COOKIE['LoggedIn'])) die("You are not logged in!");
require_once('../Connections/Database.php'); ?>
<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

$editFormAction = $HTTP_SERVER_VARS['PHP_SELF'];
if (isset($HTTP_SERVER_VARS['QUERY_STRING'])) {
  $editFormAction .= "?" . $HTTP_SERVER_VARS['QUERY_STRING'];
}

if ((isset($HTTP_POST_VARS["MM_insert"])) && ($HTTP_POST_VARS["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO ".$offerstable." (offer) VALUES (%s)",
                       GetSQLValueString($HTTP_POST_VARS['offer'], "text"));

  mysql_select_db($database_Database, $Database);
  $Result1 = mysql_query($insertSQL, $Database) or die(mysql_error());

  $insertGoTo = "admin.php";
  if (isset($HTTP_SERVER_VARS['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $HTTP_SERVER_VARS['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($HTTP_POST_VARS["MM_insert"])) && ($HTTP_POST_VARS["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO advert (`advert code`) VALUES (%s)",
                       GetSQLValueString($HTTP_POST_VARS['advert_code'], "text"));

  mysql_select_db($database_Database, $Database);
  $Result1 = mysql_query($insertSQL, $Database) or die(mysql_error());

  $insertGoTo = "index.php";
  if (isset($HTTP_SERVER_VARS['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $HTTP_SERVER_VARS['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
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
              <TD width=15 background="../images/coininfg.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" width=15></TD>
              <TD background="../images/inf.gif" height=15><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
              <TD width=15 background="../images/coininfd.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" width=15></TD>
            </TR>
            <TR> 
              <TD width=15 background="../images/g.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
              <TD noWrap align=left background="../images/fond.gif"> <b><?php echo $sysname1; ?></b></TD>
              <TD width=15 background="../images/d.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
            </TR>
            <TR> 
              <TD width=15 background="../images/coinsupg.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" width=15></TD>
              <TD background="../images/sup.gif" height=15><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
              <TD width=15 background="../images/coinsupd.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" 
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
          <TD width=15 background="../images/coininfg.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" width=15></TD>
          <TD background="../images/inf.gif" height=15><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
          <TD width=15 background="../images/coininfd.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" width=15></TD></TR>
        <TR>
          <TD width=15 background="../images/g.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
              <TD align=left width="100%" 
            background="../images/fond.gif"><A 
            href="../index.php"><b>::Main</b></A> 
                <TABLE>
              <TBODY>
              <TR>
                      <TD noWrap>&nbsp;<a href="../booking.php">::Booking </a><BR>
                        &nbsp;<a href="../details.php">::Flight Details</a> <BR>
                        &nbsp;<a href="index.php">::Admin</a> <BR>
                        &nbsp;<a href="../help.php">::Help</a> </TD>
                    </TR></TBODY></TABLE></TD>
          <TD width=15 background="../images/d.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD></TR>
        <TR>
          <TD width=15 background="../images/coinsupg.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" width=15></TD>
          <TD background="../images/sup.gif" height=15><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
          <TD width=15 background="../images/coinsupd.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" 
    width=15></TD></TR></TBODY></TABLE></TD>
    <TD width="100%">
      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
        <TBODY>
        <TR>
          <TD width=15 background="../images/t-infg.gif" height=8><IMG 
            height=8 alt="" src="../images/space15_15.gif" width=15></TD>
          <TD background="../images/t-inf.gif" height=8><IMG height=8 
            alt="" src="../images/space15_15.gif" width=15></TD>
          <TD width=15 background="../images/t-infd.gif" height=8><IMG 
            height=8 alt="" src="../images/space15_15.gif" 
        width=15></TD></TR>
        <TR>
          <TD width=15 background="../images/t-g.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
          <TD align=middle width="100%" bgColor=#e7edfe>
            <p align="left"><font face="Arial, Helvetica, sans-serif" color="#777777"><b>About</b></font></p>
          </TD>
          <TD width=15 background="../images/t-d.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD></TR>
        <TR height=1>
          <TD width=15 background="../images/b-g.gif" height=1><IMG 
            height=1 alt="" src="../images/space15_15.gif" width=15></TD>
          <TD bgColor=#777777 height=1><IMG height=1 
            src="../images/space15_15.gif" width=15></TD>
          <TD width=15 background="../images/b-d.gif" height=1><IMG 
            height=1 alt="" src="../images/space15_15.gif" 
        width=15></TD></TR>
        <TR>
          <TD width=15 background="../images/g.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
              <TD align=left width="100%" 
            background="../images/fond.gif">Welcome <?php echo $_COOKIE['LoggedIn']; ?><BR>
                You have logged in successfully. You can now change settings below. Click <a href="logout.php">here</a> to logout.</TD>
          <TD width=15 background="../images/d.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD></TR>
        <TR>
          <TD width=15 background="../images/coinsupg.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" width=15></TD>
          <TD background="../images/sup.gif" height=15><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
          <TD width=15 background="../images/coinsupd.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" width=15></TD></TR></TBODY></TABLE>
      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
        <TBODY>
        <TR>
          <TD width=15 background="../images/t-infg.gif" height=8><IMG 
            height=8 alt="" src="../images/space15_15.gif" width=15></TD>
          <TD background="../images/t-inf.gif" height=8><IMG height=8 
            alt="" src="../images/space15_15.gif" width=15></TD>
          <TD width=15 background="../images/t-infd.gif" height=8><IMG 
            height=8 alt="" src="../images/space15_15.gif" 
        width=15></TD></TR>
        <TR>
          <TD width=15 background="../images/t-g.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
          <TD align=middle width="100%" bgColor=#e7edfe>
            <p align="left"><font face="Arial, Helvetica, sans-serif" color="#777777"><b>Add 
                  Latest Offer to front page of site</b></font></p>
          </TD>
          <TD width=15 background="../images/t-d.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD></TR>
        <TR height=1>
          <TD width=15 background="../images/b-g.gif" height=1><IMG 
            height=1 alt="" src="../images/space15_15.gif" width=15></TD>
          <TD bgColor=#777777 height=1><IMG height=1 
            src="../images/space15_15.gif" width=15></TD>
          <TD width=15 background="../images/b-d.gif" height=1><IMG 
            height=1 alt="" src="../images/space15_15.gif" 
        width=15></TD></TR>
        <TR>
          <TD width=15 background="../images/g.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
              <TD align=left width="100%" 
            background="../images/fond.gif"> 
                  <p>Only the last <?php echo $maxRows_offers; ?> Offers are displayed on the main page</p>
                <form method="post" name="form1" action="<?php echo $editFormAction; ?>">
                  
                  <table width="289" align="left">
                    <tr valign="baseline"> 
                      <td width="117" align="right" nowrap>New Offer: -</td>
                      <td width="160"><input type="text" name="offer" value="" size="60"></td>
                    </tr>
                    <tr valign="baseline"> 
                      <td nowrap align="right">&nbsp;</td>
                      <td><input type="submit" value="Add"></td>
                    </tr>
                  </table>
                    <input type="hidden" name="MM_insert" value="form1">
                </form>
                <p><BR>
                </p>
                </TD>
          <TD width=15 background="../images/d.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD></TR>
        <TR>
          <TD width=15 background="../images/coinsupg.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" width=15></TD>
          <TD background="../images/sup.gif" height=15><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
          <TD width=15 background="../images/coinsupd.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" width=15></TD></TR></TBODY></TABLE>
      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
        <TBODY>
        <TR>
          <TD width=15 background="../images/t-infg.gif" height=8><IMG 
            height=8 alt="" src="../images/space15_15.gif" width=15></TD>
          <TD background="../images/t-inf.gif" height=8><IMG height=8 
            alt="" src="../images/space15_15.gif" width=15></TD>
          <TD width=15 background="../images/t-infd.gif" height=8><IMG 
            height=8 alt="" src="../images/space15_15.gif" 
        width=15></TD></TR>
        <TR>
          <TD width=15 background="../images/t-g.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
          <TD align=middle width="100%" bgColor=#e7edfe>
            <p align="left"><font face="Arial, Helvetica, sans-serif" color="#777777"><b>Add/Delete/View Flights</b></font></p>
          </TD>
          <TD width=15 background="../images/t-d.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD></TR>
        <TR height=1>
          <TD width=15 background="../images/b-g.gif" height=1><IMG 
            height=1 alt="" src="../images/space15_15.gif" width=15></TD>
          <TD bgColor=#777777 height=1><IMG height=1 
            src="../images/space15_15.gif" width=15></TD>
          <TD width=15 background="../images/b-d.gif" height=1><IMG 
            height=1 alt="" src="../images/space15_15.gif" 
        width=15></TD></TR>
        <TR>
          <TD width=15 background="../images/g.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
              <TD align=left width="100%" 
            background="../images/fond.gif"><? include("flights.php") ?></TD>
          <TD width=15 background="../images/d.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD></TR>
        <TR>
          <TD width=15 background="../images/coinsupg.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" width=15></TD>
          <TD background="../images/sup.gif" height=15><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
          <TD width=15 background="../images/coinsupd.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" width=15></TD></TR></TBODY></TABLE>
      <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
        <TBODY>
        <TR>
          <TD width=15 background="../images/t-infg.gif" height=8><IMG 
            height=8 alt="" src="../images/space15_15.gif" width=15></TD>
          <TD background="../images/t-inf.gif" height=8><IMG height=8 
            alt="" src="../images/space15_15.gif" width=15></TD>
          <TD width=15 background="../images/t-infd.gif" height=8><IMG 
            height=8 alt="" src="../images/space15_15.gif" 
        width=15></TD></TR>
        <TR>
          <TD width=15 background="../images/t-g.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
          <TD align=middle width="100%" bgColor=#e7edfe>
            <p align="left"><font face="Arial, Helvetica, sans-serif" color="#777777"><b>Customer Maintainance</b></font></p>
          </TD>
          <TD width=15 background="../images/t-d.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD></TR>
        <TR height=1>
          <TD width=15 background="../images/b-g.gif" height=1><IMG 
            height=1 alt="" src="../images/space15_15.gif" width=15></TD>
          <TD bgColor=#777777 height=1><IMG height=1 
            src="../images/space15_15.gif" width=15></TD>
          <TD width=15 background="../images/b-d.gif" height=1><IMG 
            height=1 alt="" src="../images/space15_15.gif" 
        width=15></TD></TR>
        <TR>
          <TD width=15 background="../images/g.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
              <TD align=left width="100%" 
            background="../images/fond.gif">Because this information is sensitive it is displayed on a separate page. You should only use customer maintainance to remove old customers from flights that have departed. This is a good idea if the system slows when the database becomes too big - <a href="customers.php">View</A></TD>
          <TD width=15 background="../images/d.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD></TR>
        <TR>
          <TD width=15 background="../images/coinsupg.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" width=15></TD>
          <TD background="../images/sup.gif" height=15><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
          <TD width=15 background="../images/coinsupd.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" width=15></TD></TR></TBODY></TABLE>

		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
          <TBODY>
            <TR> 
              <TD width=15 background="../images/t-infg.gif" height=8><IMG 
            height=8 alt="" src="../images/space15_15.gif" width=15></TD>
              <TD background="../images/t-inf.gif" height=8><IMG height=8 
            alt="" src="../images/space15_15.gif" width=15></TD>
              <TD width=15 background="../images/t-infd.gif" height=8><IMG 
            height=8 alt="" src="../images/space15_15.gif" 
        width=15></TD>
            </TR>
            <TR> 
              <TD width=15 background="../images/t-g.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
              <TD align=middle width="100%" bgColor=#e7edfe><div align="left"><font face="Arial, Helvetica, sans-serif" color="#777777"><b>Advertisement</b></font></div></TD>
              <TD width=15 background="../images/t-d.gif">&nbsp;</TD>
            </TR>
            <TR height=1> 
              <TD width=15 background="../images/b-g.gif" height=1><IMG 
            height=1 alt="" src="../images/space15_15.gif" width=15></TD>
              <TD bgColor=#777777 height=1><IMG height=1 
            src="../images/space15_15.gif" width=15></TD>
              <TD width=15 background="../images/b-d.gif" height=1><IMG 
            height=1 alt="" src="../images/space15_15.gif" 
        width=15></TD>
            </TR>
            <TR> 
              <TD width=15 background="../images/g.gif"><p><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></p>
                </TD>
              <TD width="100%" align=center valign="top" 
            background="../images/fond.gif">
                  <p align="left">To view and add adverts to the system click <a href="../advert.php?action=admin">here</a></p>
              </TD>
              <TD width=15 background="../images/d.gif"><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
            </TR>
            <TR> 
              <TD width=15 background="../images/coinsupg.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" width=15></TD>
              <TD background="../images/sup.gif" height=15><IMG height=15 
            alt="" src="../images/space15_15.gif" width=15></TD>
              <TD width=15 background="../images/coinsupd.gif" 
            height=15><IMG height=15 alt="" 
            src="../images/space15_15.gif" 
    width=15></TD>
            </TR>
          </TBODY>
        </TABLE></TD>
    </TR></TBODY></TABLE><? include("../footer.php") ?></BODY></HTML>
