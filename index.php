<?php require_once('Connections/Database.php');
$pageNum_offers = 0;
if (isset($HTTP_GET_VARS['pageNum_offers'])) {
  $pageNum_offers = $HTTP_GET_VARS['pageNum_offers'];
}
$startRow_offers = $pageNum_offers * $maxRows_offers;

mysql_select_db($database_Database, $Database);
$query_offers = "SELECT offer FROM ".$offerstable." ORDER BY `no` DESC";
$query_limit_offers = sprintf("%s LIMIT %d, %d", $query_offers, $startRow_offers, $maxRows_offers);
$offers = mysql_query($query_limit_offers, $Database) or die(mysql_error());
$row_offers = mysql_fetch_array($offers,MYSQL_ASSOC);

if (isset($HTTP_GET_VARS['totalRows_offers'])) {
  $totalRows_offers = $HTTP_GET_VARS['totalRows_offers'];
} else {
  $all_offers = mysql_query($query_offers);
  $totalRows_offers = mysql_num_rows($all_offers);
}
$totalPages_offers = ceil($totalRows_offers/$maxRows_offers)-1;
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
            background="images/fond.gif">Welcome to <?php echo $sysname1; ?>. The friendly airline that gives you quality of service at a low, low price. We run many flights to all over the world. To see what flights we have on offer click on flight details on the menu on the left.</TD>
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
<?php if ($_GET['confirm'] == 1) { ?>
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
              <TD align=middle width="100%" bgColor=#e7edfe><div align="left"><font face="Arial, Helvetica, sans-serif" color="#777777"><b>Confirmation</b></font></div></TD>
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
            background="images/fond.gif"><FONT 
            face="Arial, Helvetica, sans-serif" color="red">Your Flight has been confirmed and the tickets are on their way.</FONT></TD>
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
        </TABLE>
<?php } ?>
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
            <p align="left"><font face="Arial, Helvetica, sans-serif" color="#777777"><b>Latest Offers!</b></font></p>
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
                <table border="0">
                  <?php do { ?>
                  <tr> 
                    <td><?php echo $row_offers['offer']; ?></td>
                  </tr>
                  <?php } while ($row_offers = mysql_fetch_array($offers,MYSQL_ASSOC)); ?>
                </table>
</TD>
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
mysql_free_result($offers);
?>
