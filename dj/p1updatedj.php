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
           <td align="center"><img src="../images/Ckxu_logo_PNG.png" alt="ckxu"/></td>
      </tr>
      <tr style="background-color:white;">
      <td>
<?php

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
        if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: /user/login');}

        $djsql="SELECT * from DJ order by djname";
        $djresult=mysql_query($djsql,$con);

        $djoptions="";
        while ($djrow=mysql_fetch_array($djresult)) {
            $Alias=$djrow["Alias"];
            $name=$djrow["djname"];
            $djoptions.="<OPTION VALUE=\"" . $Alias . "\">" . $name . "</option>";
        }
        ?>

        <table border="0" height="100" width="100%">
        <tr><td colspan="100%">
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
        <tr><th colspan="3">
        On-Air Name [% is wildcard]
        </th><th>
        Name
        </th><th>
        Active
        </th><th>
        Start Year
        </th>
        </tr>
             <form name="selections" action="p2updatedj.php" method="post">
             <tr>
             <td colspan="3">
                 <input name="Alias" type="text" size="40%" value="%"/>
             </td>
             <td>
                 <input name="djname" type="text" size="40%" value="%"/>
             </td>
             <td>
                 <input name="active" type="checkbox" checked/>
             </td>
             <td>
                 <input name="years" type="text" size="25%" value="%"/>
             </td>
            <td>
                <input type="submit" value="Search" />
                </form>
            </td>
        </tr>
        <tr><td colspan="100%"><hr /></td></tr>
        <tr>
        <th colspan="2">
        Direct Selection
        </th>
        </tr>
        <tr>
        <form name="Direct" action="p3updatedj.php" method="post">
              <td colspan="2">
                 <select name="Alias" size="1">
                         <?php echo $djoptions;?>
                 </select>
             </td>
             <td>
                <input type="submit" value="Edit" />
                </form>
            </td>
        </form>
        </tr>
        

        <?php

		}
		else{
		echo 'ERROR!';
		}
		?>

	<td colspan="100%"><hr></td>
		<tr><td>
	   
        <form name="logout" action="../logout.php" method="POST">
	<input type="submit" value="Logout"></form></td>
        <td><form name="main" action="../masterpage.php" method="POST">
        <input type="submit" value="Return">
        </form></td>
        <td colspan="4"></td>
	<td><img src="../images/mysqls.png" alt="MySQL" align="right"></td></tr>
	</table>	
        </table>

</body>
</html>