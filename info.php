<HTML><HEAD><TITLE><?php
include ("Connections/Database.php");
echo $sysname1; ?></TITLE>
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
            <p align="left"><font face="Arial, Helvetica, sans-serif" color="#777777"><b>Flight Details - View Flight Number <?php echo $_GET['flight'] ?></b></font></p>
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
            background="images/fond.gif">You have selected to find out information of the flight numbered <?php echo $_GET['flight'] ?>. The available information is listed below.</TD>
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
            <p align="left"><font face="Arial, Helvetica, sans-serif" color="#777777"><b>Flight Details</b></font></p>
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
            background="images/fond.gif"><FONT 
            face="Arial, Helvetica, sans-serif">
<?
mysql_select_db($database_Database, $Database);
$query = "SELECT Dest, datetime, priceb, pricef, pricee, senior, child FROM ".$flighttable." WHERE number='" .$_GET['flight'] ."' LIMIT 1";
$temp = mysql_query($query, $Database) or die(mysql_error());
$temptwo = mysql_fetch_array($temp);
?>
Destination: - <? echo $temptwo['Dest'] ?><BR>
Date: - <?php if (ereg ("([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})", $temptwo['datetime'], $regs)) {
    echo "$regs[3]/$regs[2]/$regs[1]";
} else {
    echo "Invalid date format";
}
?><BR>
Time: - <? echo "$regs[4]$regs[5]" ?>
<BR><BR><B>Seat Costs</B>
<BR>Business Class: - £<? echo $temptwo['priceb'] ?>
<BR>First Class: - £<? echo $temptwo['pricef'] ?>
<BR>Economy Class: - £<? echo $temptwo['pricee'] ?>
<BR>
<BR>Discount for Seniors: - £<? echo $temptwo['senior'] ?>
<BR>Discount for Children: - £<? echo $temptwo['child'] ?>
</FONT>
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
            background="images/fond.gif"><p><? include("advert.php") ?><FONT 
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
    </TR></TBODY></TABLE><? include("footer.php") ?></BODY></HTML>
