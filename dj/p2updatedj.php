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
           <td align="center" colspan="2"><img src="../images/Ckxu_logo_PNG.png" alt="ckxu"/></td>
      </tr>
      <tr style="background-color:white;">
      <td colspan="2">
<?php

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
        ?>

        <table align="left" border="0" height="100">
        <tr><td colspan="5">
        <h2>Edit DJ</h2>
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
              echo "<tr><td colspan=\"100%\>
              <h3 width=\"100%\" style=\"background-color:yellow; color:black;\"><strong>NOTICE: Google Chrome has limited support on this site,<br />
              please launch or download a browser that supports HTML5</strong></h3>
              </td></tr>";
            //    header("location: originalhomepage.php");
            }
            else if(getBrowser()=="Mozilla Firefox") {
              echo "<tr><td colspan=\"100%\>
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
        Name
        </th><th width="200">
        On-Air Name
        </th><th width="200">
        Active
        </th><th width="250">
        Year Joined
        </th>
        </tr>
        <?php
             if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: /login.php');}
             if(isset($_POST['active'])){
               $ACTI = "1";
             }
             else{
               $ACTI = "0";
             }
             $sql = "select * from DJ where Alias like '" . $_POST['Alias'] . "' and djname like '".$_POST['djname']."' and active like '". $ACTI ."' and years like '". $_POST['years'] ."' ";
             $result = mysql_query($sql) or die(mysql_error());
             if(mysql_num_rows($result)=="0"){
               echo '<tr><td colspan="100%" style="background-color:yellow;">';
               echo 'No Results Found';
               echo '</tr></td>';
             }
             else{

               while($row=mysql_fetch_array($result)) {
               //begin form
               echo '<form name="removedj" action="p3updatedj.php" method="post">';

               //begin row
               echo '<tr><td>';
               //column 1
                   echo "<input name=\"djname\" type=\"text\" hidden=\"true\" size=\"30\" value=\"" . $row['djname'] . "\">";
                   echo $row['djname'];
                   //echo "<input name=\"namex\" hidden=\"true\" type=\"text\" value=\"" . $row['djname'] . "\">";

               echo '</td><td>';
               //column 2
                   //echo $row['callsign'];
                   echo "<input name=\"Alias\" type=\"text\" hidden=\"true\" value=\"" . $row['Alias'] . "\">";
                   echo $row['Alias'];
                   //echo "<input name=\"aliasx\" hidden=\"true\" type=\"text\" value=\"" . $row['Alias'] . "\">";

               echo '</td><td>';
               //column 3
                   echo "<input name=\"active\" type=\"text\" hidden=\"true\" size=\"15\" value=\"" . $row['active'] . "\">";
                   echo $row['active'];
                   //echo "<input name=\"activex\" hidden=\"true\" type=\"text\" value=\"" . $row['active'] . "\">";

               echo '</td><td>';
               //column 4
                   echo "<input name=\"years\" type=\"text\" hidden=\"true\" size=\"35\" value=\"" .$row['years'] . "\">";
                   echo $row['years'];
                   //echo "<input name=\"yearx\" hidden=\"true\" type=\"text\" value=\"" .$row['years'] . "\">";

               echo "</td><td><input type=\"submit\" value=\"Select\" /></form>";
               echo'</td></tr>';
               //end row
               }
             }
        echo '</table>';
}
else{
	echo 'ERROR!';
}
?>

<!-- adds horizontal line above return and logout button -->
		<table width="1000">
		<tr><td colspan="5"><hr /></td></tr>
		</table>
		
		<table align="left">
		<tr><td>
	   
        <?php
          if($_SESSION['usr']=='user')
          {
            echo "<form name=\"exit\" action=\"../VERLogout.php\" method=\"POST\">";
            echo "<input type=\"submit\" value=\"Exit\">";
            echo "</form>";
            echo "</td><td colspan=\"5\">";
  }
          else
          {
            echo "<form name=\"logout\" action=\"../logout.php\" method=\"POST\">";
			echo "<input type=\"submit\" value=\"Logout\"></form></td>";
            echo "<td><form name=\"main\" action=\"../masterpage.php\" method=\"POST\">";
            echo "<input type=\"submit\" value=\"Return\">";
            echo "</form></td>";
			echo "<img src=\"../images/mysqls.png\" alt=\"MySQL\" align=\"right\"></tr>";
          }
        ?>
		
        </table>

<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/system/streaminfo.js"></script>
<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/js.php/zuomcwvv/streaminfo/rnd0"></script>

</body>
</html>