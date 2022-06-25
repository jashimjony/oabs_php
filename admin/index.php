<?
if(isset($_COOKIE['LoggedIn'])) {
header("Location: admin.php");
}
include ("../Connections/Database.php");
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
            background="../images/fond.gif"><p>Welcome.</p>
                <p>To login fill in the form below correctly...</p>
                <form method="post" action="login.php">
                  <font face="Arial, Helvetica, sans-serif"> Enter your User Name 
                  <input type="text" name="Name" size="20">
                  <br>
                  Enter you Password 
                  <input type="password" name="Password" size="20">
                  <br>
                  <input name="s" type="submit" id="s" value="Login">
                  </font>
</form>
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
      </TD>
    </TR></TBODY></TABLE><? include("../footer.php") ?></BODY></HTML>
