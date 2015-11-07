<?php session_start(); ?>
<head>
<link rel="stylesheet" type="text/css" href="/phpstyle.css" />
<title>Audit</title>
</head>
<html>
<body>
  <div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>

      <table border="0" align="center" width="1000">
      <tr>
           <td align="center"><img src="/images/Ckxu_logo_PNG.png" alt="ckxu"/></td>
      </tr>
      <tr style="background-color:white;">
      <td>
  <?php
      //Mass Audit
      //The following page gathers the requested dates, the next page will display a log per page,
      //portorate printing, 8.5/11
      //for page break (printing) see 

	  //http://www.htmlgoodies.com/beyond/css/article.php/3470341/CSS-and-Printing.htm
      ?>

      <table align="left" border="0" height="100">
        <tr><td colspan="100%">
        <h2>Review Program Logs</h2>
        </td></tr>

        <tr><th colspan="3">
        Program Name [% is wildcard]
        </th><th>
        Date From [YYYY-MM-DD]
        </th><th>
        Date To [YYYY-MM-DD]
        </th><th width="10%">

        </th>
        </tr>
             <form name="selections" action="p2Audit.php" method="post" target="_blank">
             <tr>
             <td colspan="3">
                 <input name="program" type="text" size="30%" value="%"/>
             </td>
             <td>
                 <input name="from" type="date" size="30%"/>
             </td>
             <td>
                 <input name="to" type="date" size="30%"/>
             </td>
            <td>
                <input type="submit" value="Submit" />
                </form>
            </td>
            <td>
            </td>
        </tr>
        <tr height="5">
        <td colspan="100%">
        <hr />
        </td>
        </tr>
        <tr>
        <td>
        <form name="logout" action="/logout.php" method="POST">
              <input type="submit" value="Logout">
        </form>
        </td>
        <td>
        <form name="return" action="/masterpage.php" method="POST">
              <input type="submit" value="Return">
        </form>
        </td>
        <td>
        <form name="reset" action="/oep/p1Audit.php" method="POST">
              <input type="submit" value="Reset">
        </form>
        </td>
        <td>
        </td>
        <td>
        </td>
        </tr>
        </table>
</body>
</html>