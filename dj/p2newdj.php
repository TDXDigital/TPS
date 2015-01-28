<?php session_start();
 $DEBUG = TRUE;
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
        if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../login.php');}
         // end PHP Header
         if($DEBUG){
             echo "<p>DBNAME: ".$_SESSION['DBNAME']."<br/>DBHOST:".$_SESSION['DBHOST']."</p>";
         }
        ?>
        <table align="left" border="0" height="100">
        <tr><td colspan="100%">
        <h2>New DJ</h2>
        </td></tr>
        <tr><th colspan="3" width="30%">
        Name 
        </th><th width="30%">
        On-Air Name
        </th><th width="30%">
        Year Joined
        </th><th width="100%">
        Active
        </th>
        </tr>
             <tr>
             <td>
                 <?php
                      if(isset($_POST['name']))
                      {
                         if($_POST['name']==""){
                           echo 'Error</td><td colspan="3">The DJ Must Have a Name, only Chuck Norris can be without a name because he would break the server!</td>';
                         }
                         else
                         {
                           echo $_POST['name'];
                           echo '</td><td colspan="3">';
                           if(isset($_POST['alias'])){
                             if($_POST['alias']!="")
                             {
                               $ALIAS = $_POST['alias'];
                             }
                             else
                             {
                               $ALIAS = $_POST['name'];
                             }
                           }
                           else{
                             $ALIAS = $_POST['name'];
                           }
                           echo $ALIAS;
                           echo '</td><td>';
                           if(isset($_POST['year']))
                           {
                             $YEAR = $_POST['year'];
                           }
                           else
                           {
                             $YEAR = '0';
                           }
                           echo $YEAR;
                           echo '</td><td>';
                           if(isset($_POST['active'])){
                             $ACTIVE = '1';
                             echo 'yes';
                           }
                           else{
                             $ACTIVE = '0';
                             echo 'no';
                           }
                           echo '</td></tr>';
                           if(mysql_query("insert into DJ (djname, alias, active, years ) values ('" . addslashes($_POST['name']) . "', '" . addslashes($ALIAS) . "', '" . $ACTIVE . "', '" . addslashes($YEAR) . "' )"))
                           {
                             echo '<tr ><td colspan="100%" style="background-color:lightgreen;">DJ Added to database succesfully!</td></tr>';
                           }
                           else
                           {
                             echo '<tr ><td colspan="1" style="background-color:red;"' . mysql_errno() . '</td>';
                             echo '<td colspan="100%" style="background-color:red; color:white;">The DJ could not be added to the server, please check your values<br>Error:'.mysql_error().'</td></tr>';
                           }
                         }
                      }

                 ?>
        </tr>
        <tr><td colspan="100%"><hr /></td></tr>
        
        <tr>
        <td>
	         <form name="logout" action="../logout.php" method="POST">
		<input type="submit" value="Logout"></form></td>
       <td><form name="main" action="../masterpage.php" method="POST">
        <input type="submit" value="Return">
      </form></td>
            <td colspan="2">
                <form name="newdj" action="p1newdj.php" method="POST">
                <input type="submit" value="Add Another DJ" />
                </form>
            </td>
            <td colspan="2"></td>
		<td><img src="../images/mysqls.png" alt="MySQL" align="right"></td>
        </tr>
        </table>

        <?php

}
else{
	echo 'ERROR!';
}

?>


		</table>
		
<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/system/streaminfo.js"></script>
<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/js.php/zuomcwvv/streaminfo/rnd0"></script>
</body>
</html>
