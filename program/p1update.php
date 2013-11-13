<?php session_start();

//---------------------------------------
//NOTICE: the SQL SERVER MUST HAVE THE
//ON UPDATE ACTION SET TO CASCADE!
//OTHERWISE THE UPDATE WILL FAIL
//
//THIS FOREIGN KEY RELATION THAT MUST
//BE ASSIGNED IS FOR PERFORMS->PROGRAM
//---------------------------------------

 ?>
<head>
<link rel="stylesheet" type="text/css" href="../phpstyle.css" />
<title>DPL Administration</title>
</head>
<html>
<body>
      <div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>

      <table border="0" align="center" width="1000">
      <tr>
           <td align="center"><img src="../<?php echo $_SESSION['logo'];?>" alt="logo"/></td>
      </tr>
      <tr style="background-color:white;">
      <td>
<?php

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

username=' . $_SESSION["username"]);
	}
else if($con){
        if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../login.php');} // or die("<h1>Error ".mysql_errno() ."</h1><br />check access (privileges) to the SQL server db CKXU for this user <br/><br /><hr />Error details:<br />" .mysql_error() . "<br /><br /><a href=login.php>Return</a>");
        $callsql="SELECT callsign, stationname from STATION order by callsign";
        $callresult=mysql_query($callsql,$con);

        $calloptions="<option value=%>Any Station</option>";
        while ($row=mysql_fetch_array($callresult)) {
            $name=$row["stationname"];
            $callsign=$row["callsign"];
            $calloptions.="<OPTION VALUE=\"$callsign\">".$name."</option>";
        }

        $djsql="SELECT * from DJ order by djname";
        $djresult=mysql_query($djsql,$con);

        $djoptions="<option value=\"%\">Any Host</option>";//<OPTION VALUE=0>Choose</option>";
        while ($djrow=mysql_fetch_array($djresult)) {
            $Alias=$djrow["Alias"];
            $name=$djrow["djname"];
            $djoptions.="<OPTION VALUE=\"$Alias\">" . $name . "</option>";
        }
        ?>

        <table align="left" border="0" height="100">
        <tr><td colspan="7">
        <h2>Edit Program * USE ADVANCED SEARCH*</h2>
        </td></tr>

        <tr><th width="25%" colspan="2">
        Program Name [% is wildcard]
        </th><th width="20%">
        Station Callsign
        </th><th width="20%">
        Length (min)
        </th><th width="25%">
        Syndicate Source
        </th><th width="10%">
        Host
        </th>
        </tr>
             <form name="selections" action="p2update.php" method="post">
             <tr>
             <td colspan="2">
                 <input name="name" type="text" size="30" value="%"/>
             </td>
             <td>
                 <select name="callsign">
                         <?php echo $calloptions;?>
                 </select>
             </td>
             <td>
                 <input name="length" type="text" size="15" value="%"/>
             </td>
             <td>
                 <input name="syndicate" type="text" size="35" value="%"/>
             </td>
             <td>
                 <select name="dj1">
                         <?php echo $djoptions;?>
                 </select>
             </td>
            <td colspan="7">
                <input type="submit" disabled="true" value="Search" />
                </form>
            </td>
        </tr>
  

        <?php

}
else{
	echo 'ERROR!';
}

echo '<tr height="20"><td colspan="7"><hr/></td></tr>';

?>
        <tr>
        <td>
        <form name="logout" action="../logout.php" method="POST">
              <input type="submit" value="Logout">
        </form>
        </td>
        <td>
        <form name="return" action="../masterpage.php" method="POST">
              <input type="submit" value="Return">
        </form>
        </td>
       <td>
       <form name="advanced" action="p1advupdate.php" method="get">
       	<input type="text" name="source" value="p1update.php" hidden />
       	<input type="submit" value="Advanced Search">
       </form>
       </td>
        <td colspan="3"></td>
        <td style="text-align:right;">
        <img src="../images/mysqls.png" alt="MySQL Powered" />
        </td>
        </tr>
        
        </table>
        </td>
        </tr>
        </table>
</body>
</html>