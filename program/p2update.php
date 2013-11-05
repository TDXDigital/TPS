<?php session_start(); ?>
<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/phpstyle.css" />
<title>DPL Administration</title>
</head>
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

$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

username=' . $_SESSION["username"]);
	}
else if($con){
        ?>

        <table align="left" border="0" height="100">
        <tr><td colspan="100%">
        <h2>Edit Program</h2>
        </td></tr>

        <tr><th width="250" colspan="3">
        Program Name
        </th><th width="200">
        Callsign
        </th><th width="200">
        Length (min)
        </th><th width="250">
        Syndicate Source
        </th><th width="100">
        Host
        </th><th width="100">
        CoHost
        </th>
        </tr>
        <?php
             if(!mysql_select_db("CKXU")){header('Location: /login.php');}
             $callsql="SELECT callsign, stationname from STATION order by callsign";

// NEEDS MOVED INTO THE LINE TO DETERMINE IF IT IS A HOST
             $djsql="SELECT * from DJ order by djname";
             $djresult=mysql_query($djsql,$con);

             $djoptions="";//<OPTION VALUE=0>Choose</option>";
             while ($djrow=mysql_fetch_array($djresult)) {
                   $Alias=$djrow["Alias"];
                   $name=$djrow["djname"];
				   
                   $djoptions.="<OPTION VALUE=\"$Alias\" ";
				   if($Alias = $_POST['dj1']){
				   	$djoptions.=" selected=\"true\" ";
				   }
                   $djoptions.=">".$name."</option>";
             }
			 
			 $djresult=mysql_query($djsql,$con);
			 $codjoptions="<OPTION VALUE=0>None</option>";
             while ($djrow=mysql_fetch_array($djresult)) {
                   $Alias=$djrow["Alias"];
                   $name=$djrow["djname"];
				   
                   $djoptions.="<OPTION VALUE=\"$Alias\" ";
				   if($Alias = $_POST['dj2']){
				   	$djoptions.=" selected=\"true\" ";
				   }
                   $djoptions.=">".$name."</option>";
             }

             $sql = "select program.*, performs.Alias from program, performs where performs.Alias like '" . addslashes($_POST['dj1']) . "' and performs.callsign like '" . addslashes($_POST['callsign']) . "'and length like '" . addslashes($_POST['length']) . "' and syndicatesource like '" . addslashes($_POST['syndicate']) . "' and program.programname like '" . addslashes($_POST['name']) . "' and program.programname=performs.programname";

             $result = mysql_query($sql) or die(mysql_error());
             if(mysql_num_rows($result)=="0"){
               echo '<tr><td colspan="100%" style="background-color:yellow;">';
               echo 'No Results Found';
               echo '</tr></td>';
             }
             else{

               while($row=mysql_fetch_array($result)) {
               //begin form
               echo '<form name="removedj" action="/program/p3update.php" method="post">';

               //begin row
               echo '<tr><td colspan="3">';
               //column 1
                   echo "<input name=\"pname\" type=\"text\" size=\"30\" value=\"" . $row['programname'] . "\">";
                   echo "<input name=\"pnamex\" hidden=\"true\" type=\"text\" value=\"" . $row['programname'] . "\">";

               echo '</td><td>';
               //column 2
                   //echo $row['callsign'];
                   echo '<select name="callsign">';
                   $callcurrent = mysql_fetch_array(mysql_query("select stationname from STATION where callsign='" . addslashes($row['callsign']) . "'",$con));
                   //echo "<option value=\"" . $row['callsign'] . "\">" . $callcurrent['stationname'] . "</option>";
                   $calloptions="";//<OPTION VALUE=0>Choose</option>";
                   $callresult=mysql_query($callsql,$con);
                     while ($row2=mysql_fetch_array($callresult)) {
                           $name=$row2["stationname"];
                           $callsign=$row2["callsign"];
                           if($row2['callsign'] == $row['callsign'])
                           {
                             $calloptions.="<OPTION VALUE=\"".$callsign."\" selected>".$name."</option>";
                           }
                           else{
                                $calloptions.="<OPTION VALUE=\"".$callsign."\">".$name."</option>";
                           }
                     }

                         echo $calloptions;
                   echo '</select>';
                   echo "<input name=\"callsignx\" hidden=\"true\" type=\"text\" value=\"" . $row['callsign'] . "\">";

               echo '</td><td>';
               //column 3
                   echo "<input name=\"length\" type=\"text\" size=\"15\" value=\"" . $row['length']. "\">";
                   echo "<input name=\"lengthx\" hidden=\"true\" type=\"text\" value=\"" . $row['length'] . "\">";

               echo '</td><td>';
               //column 4
                   echo "<input name=\"syndicate\" type=\"text\" size=\"35\" value=\"" .$row['syndicatesource'] . "\">";
                   echo "<input name=\"syndicatex\" hidden=\"true\" type=\"text\" value=\"" .$row['syndicatesource'] . "\">";

               echo '</td><td>';
                   echo '<select name="dj1" >';
                   echo $djoptions;
                   echo '</select>';
                   echo "<input name=\"dj1x\" hidden=\"true\" type=\"text\" value=\"" . $row['Alias'] . "\">";
				   
				   echo '</td><td>';
                   echo '<select name="dj2" >';
                   echo $codjoptions;
                   echo '</select>';
                   echo "<input name=\"dj2x\" hidden=\"true\" type=\"text\" value=\"" . $row['Alias'] . "\">";

               echo "</td></tr><tr><td><input type=\"submit\" value=\"Update\" /></form>";
               echo'</td></tr>';
               //end row
               }
             }
}
else{
	echo 'ERROR!';
}

echo '<tr height="20"><td colspan="100%" style="text-align:bottom;"><hr/></td></tr>';

?>
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
        <form name="search" action="/program/p1update.php" method="POST">
        <input type="submit" value="Search"></form>
        </td>
        <td colspan="4"></td>
        <td style="text-align:right;">
        <img src="/images/mysqls.png" alt="MySQL Powered" />
        </td>
        </tr>
        
        </table>
        </td>
        </tr>
        </table>
</table>
</body>
</html>