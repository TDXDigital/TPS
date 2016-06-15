<?php session_start(); ?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../../css/phpstyle.css" />
<title>DPL Administration</title>
</head>
<html>
<body>
      <div class="topbar">
           <a class="right" href="../../logout.php"> Logout </a>Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>

      <table border="0" align="center" width="1000">
      <tr>
           <td align="center" colspan="2"><img src="<?php print("../../".$_SESSION['logo']); ?>" alt="logo"/></td>
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
        <h2>Update Program Log</h2>
        </td></tr>

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
             if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../login.php');}
             
             if($_POST['date']!=""){
             	$date = $_POST['date'];
             }
			 else{
			 	$date = '%';
			 }
			 
			 if($_POST['prerecord']!=""){
             	$pr = $_POST['prerecord'];
             }
			 else{
			 	$pr = '%';
			 }
			 
             $sql = "select * from Episode where date like '". $date ."'  and programname like '".$_POST['name']."'";

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
               echo '<form name="view" action="EPV2/p3update.php" method="POST"><tr><td>';
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
                   echo "<input type=\"submit\" value=\" Edit \" />";//$djrow['Alias'];
  
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
        <img src="../../images/mysqls.png" alt="MySQL"></td><td height="10" style="text-align:right">Internet Simulcast Server status: <span id="cc_stream_info_server"></span>
<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/system/streaminfo.js"></script>
<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/js.php/zuomcwvv/streaminfo/rnd0"></script>
</td></tr>
</table>
</body>
</html>
