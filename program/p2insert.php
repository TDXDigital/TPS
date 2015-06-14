<?php
    session_start();
    error_reporting(E_ERROR ^ E_DEPRECATED); // mysql is known to be deprecated
    
    require '../TPSBIN/functions.php';
    require '../TPSBIN/db_connect.php';
?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../phpstyle.css" />
<title>TPS Administration</title>
</head>
<html>
<body>
      <div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>

      <table border="0" align="center" width="1000">
      <tr>
           <td align="center" colspan="2"><img src="<?php echo '../'.$_SESSION['logo'];?>" alt="ckxu"/></td>
      </tr>
      <tr style="background-color:white;">
      <td colspan="2">
<?php

/*$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
        if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: /user/login');} // or die("<h1>Error ".mysql_errno() ."</h1><br />check access (privileges) to the SQL server db CKXU for this user <br /><br /><hr />Error details:<br />" .mysql_error() . "<br /><br /><a href=login.php>Return</a>");*/
        $sql="SELECT callsign, stationname from STATION order by callsign";
        if(!$mysqli->query($sql))
        {
          die("Critical Error, The referenced station does not exist in the database. please contact the DBA now!");
        }
        ?>

        <table align="left" border="0" height="100">
        <tr><td colspan="6">
        <h2>New Program</h2>
        </td></tr>

        <tr><th width="250" colspan="2">
        Program Name
        </th><th width="200">
        Station Name
        </th><th width="200">
        Length (min)
        </th><th width="250">
        Syndicate Source
        </th>
        <th width="100">
        On-Air Name
        </th>
        </tr>
             <tr>
             <td colspan="2">
                 <?php echo $_POST['pname']; ?>
             </td>
             <td>
                 <?php echo $_POST['callsign']; ?>
             </td>
             <td>
                 <?php echo $_POST['length']; ?>
             </td>
             <td>
                 <?php echo $_POST['syndicate'];?>
             </td>
             <td>
                 <?php echo $_POST['dj1'];?>
             </td>
        </tr>
        <tr>
            <td colspan="6" height="50">
                <?php
                   // MySQL Commands
                   
                   // sanatize inputs
                    $cname = filter_input(INPUT_POST,'pname',FILTER_SANITIZE_STRING);
                    //$cname = addslashes($_POST['pname']);
                    $ccallsign = addslashes($_POST['callsign']);
                    $clength = addslashes($_POST['length']);
                    $csyndicate = addslashes($_POST['syndicate']);
                    $cgenre = addslashes($_POST['genre']);
				   
					// this should be reduced to a single sanatized command
                   $sql = "insert into program (programname, callsign, length, syndicatesource, genre) values ('" . $cname . "' , '" . $ccallsign . "' , '" . $clength . "' , '" . $csyndicate . "' , '" . $cgenre . "')";
                   $performs = "insert into performs (callsign, programname, Alias) values ('" . $ccallsign . "' , '" . $cname . "' , '" . $_POST['dj1'] . "')";
				   
				   
                if($cname == ""){
                  echo '<h4 style="background-color:yellow;">Error: The program must have a name.<br />insert not attempted</h4>';
                }
                else if($_POST['dj1'] == ""){
                  echo '<h4 style="background-color:yellow;">Error: The program must have a DJ.<br />insert not attempted</h4>';
                }
                else{
                  if($mysqli->query($sql)){
                           if($mysqli->query($performs)){
                             //echo '<h5 style="background-color:lightgreen;">This Data was succesfully entered into the database</h5>';
                             header("location: p3advupdate.php?resource=" . $_POST['pname'] . "@" . $_POST['callsign']);
                           }
                           else{
                                echo '<h4 style="background-color:red; color:white;">This Data failed to be entered into the database</h4>';
                                echo '<p style="background-color:red; color:white;">Error Description: ' . $mysqli->error;
                                $mysqli->query("Delete from program where pname='" . $_POST['pname'] . "'",$con);
                           }
                  }
                  else{
                       echo '<h4 style="background-color:red; color:white;">This Data failed to be entered into the database</h4>';
                       echo '<p style="background-color:red; color:white;">Error Description: ' . $mysqli->error;
                  }
                }
                ?>
            </td>
        </tr>
        

        <?php
/*
}
else{
	echo 'ERROR!';
}*/

echo '<tr height="20"><td colspan="6" style="text-align:bottom;"><hr/></td></tr>';

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