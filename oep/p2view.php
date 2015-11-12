<?php session_start(); 


function getBrowser()
     {
         $u_agent = $_SERVER['HTTP_USER_AGENT'];
         $ub = '';
         if(preg_match('/MSIE/i',$u_agent))
         {
             $ub = "Internet Explorer";
         }
         elseif(preg_match('/Firefox/i',$u_agent))
         {
             $ub = "Mozilla Firefox";
         }
         elseif(preg_match('/Safari/i',$u_agent))
         {
             $ub = "Apple Safari";
         }
         elseif(preg_match('/Chrome/i',$u_agent))
         {
             $ub = "Google Chrome";
         }
         elseif(preg_match('/Flock/i',$u_agent))
          {
             $ub = "Flock";
         }
         elseif(preg_match('/Opera/i',$u_agent))
         {
             $ub = "Opera";
         }
         elseif(preg_match('/Netscape/i',$u_agent))
         {
             $ub = "Netscape";
         }
         return $ub;
     }

?>
<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../phpstyle.css" />
<title>DPL Administration</title>
</head>
<body>
      <div class="topbar">
           <a class="right" href="logout.php"> Logout </a>Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>

      <table border="0" align="center" width="1000">
      <tr>
           <td align="center" colspan="2"><img src="../images/Ckxu_logo_PNG.png" alt="ckxu"/></td>
      </tr>
      <tr style="background-color:white;">
      <td colspan="2">
<?php

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
        ?>

        <table align="left" border="0" height="100">
        <tr><td colspan="5">
        <h2>View Program Log</h2>
        </td></tr>
        <?php
             //echo $_SERVER['HTTP_USER_AGENT'] . "\n\n";
             $br = strtolower($_SERVER['HTTP_USER_AGENT']); // what browser they use.
            //echo $br;

            if(ereg("opera", $br)) {
              //echo 'Browser Supported';
            //    header("location: originalhomepage.php");
            echo "<!-- This browser has been verified to contain FULL SUPPORT for this page -->";
            }
            else if(ereg("chrome", $br)) {
              echo "<tr><td>
              <h3 width=\"100%\" style=\"background-color:yellow; color:black;\"><strong>NOTICE: Google Chrome has limited support on this site,<br />
              please launch or download a browser that supports HTML5</strong></h3>
              </td></tr>";
            //    header("location: originalhomepage.php");
            }
            else if(getBrowser()=="Mozilla Firefox") {
              echo "<tr><td>
              <h3 width=\"100%\" style=\"background-color:yellow; color:black;\"><strong>NOTICE: " . getBrowser() . " has limited support on this site,<br />
              please launch or download a browser that supports HTML5</strong></h3>
              </td></tr>";
            }
            else {
              header('Location: ../browserUnsupported.php');
              //  header("location: alteredhomepage.php");
            }
        ?>
        <tr><th width="250">
        Program Name
        </th><th width="100">
        Date
        </th><th width="100">
        PreRecord
        </th><th width="100">
        Air Time
        </th>
        <th width="250">
        Decription
        </th>
        </tr>
        <?php
             if(!mysql_select_db("CKXU")){header('Location: ../login.php');}
             $sql = "select * from Episode where date like '%".$_POST['date']."%'  and programname like '%".$_POST['name']."%' ";//and description like '".$_POST['description']."' and prerecorddate like '".$_POST['prerecord']."'";

             $result = mysql_query($sql) or die(mysql_error());
             if(mysql_num_rows($result)=="0"){
               echo '<tr><td colspan="100%" style="background-color:yellow;">';
               echo 'No Results Found';
               echo '</tr></td>';
               echo $sql;
               echo $result;
             }
             else{

               while($row=mysql_fetch_array($result)) {

               //begin row
               echo '<form name="view" action="UnViewEP.php" method="POST"><tr><td>';
               //column 1
                   echo $row['programname'];
                   echo '<input name="program" hidden="true" value="' . $row['programname'] . '" />';

               echo '</td><td>';
               //column 2
                   echo $row['date'];
                   echo '<input name="user_date" hidden="true" value="' . $row['date'] . '" />';

               echo '</td><td>';
               //column 3
                   echo $row['prerecorddate'];

               echo '</td><td>';
               //column 4
                   echo $row['starttime'];
                   echo '<input name="user_time" hidden="true" value="' . $row['starttime'] . '" />';

               echo '</td><td>';
               //column 5
                   echo $row['description'];
                   echo '<input name="callsign" hidden="true" value="' . $row['callsign'] . '" />';

               echo '</td><td>';
               //$djrow = mysql_fetch_array($djresult);
               //column 5

               //$djname = mysql_query("SELECT * from PERFORMS where Alias LIKE '" . $_POST['dj1'] . "' and callsign LIKE '" . $row['callsign'] . "' and programname LIKE '" . $row['programname'] . "'");
               //    $djrow = mysql_fetch_array($djname);
                   //echo $djname;
                   echo "<input type=\"submit\" value=\"View\" />";//$djrow['Alias'];
  
               echo'</td></tr></form>';
               //end row
               }
             }
        echo '</table>';



}
else{
	echo 'ERROR!';
}

echo '</tr><tr height="40" valign="bottom"><td colspan="2" style="text-align:bottom;"><hr/><a href="../masterpage.php"> Return to main Admin Page</a></td></tr>';

?>
        </td><tr>
        <!-- <hr />
        <a href="/logout.php" align='center' >Logout</a><br/> --><td height="10" style="text-align:left">
        <img src="../images/mysqls.png" alt="MySQL"></td><td height="10" style="text-align:right">Internet Simulcast Server status: <span id="cc_stream_info_server"></span>
<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/system/streaminfo.js"></script>
<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/js.php/zuomcwvv/streaminfo/rnd0"></script>
</td></tr>
</table>
</body>
</html>