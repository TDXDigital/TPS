<?php session_start(); ?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="/phpstyle.css" />
<title>DPL Administration</title>
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

$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
        if(!mysql_select_db("posts")){header('Location: /login.php');}
         // end PHP Header
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
                      if(isset($_POST['header']))
                      {
                         if($_POST['header']==""){
                           echo 'Error</td><td colspan="3">The DJ Must Have a Name, only Chuck Norris can be without a name because he would break the server!</td>';
                         }
                         else
                         {
                           echo $_POST['header'];
                           echo '</td><td colspan="3">';
                           if(isset($_POST['author'])){
                             if($_POST['author']!="")
                             {
                               $AUTHOR = $_POST['author'];
                             }
                             else
                             {
                               $AUTHOR = "CKXU";
                             }
                           }
                           else{
                             $AUTHOR = "CKXU";
                           }
                           echo $AUTHOR;
                           echo '</td><td>';
                           if(isset($_POST['content']))
                           {
                             $DESC = $_POST['content'];
                           }
                           else
                           {
                             $DESC = "<i>No Content Provied</i>";
                           }
                           echo $DESC;
                           echo '</td><td>';
                           if(isset($_POST['active'])){
                             $ACTIVE = '0';
                             echo 'yes';
                           }
                           else{
                             $ACTIVE = '1';
                             echo 'no';
                           }
                           echo '</td></tr>';
                           if(mysql_query("insert into news (header, author, hidden, content) values ('" . $_POST['header'] . "', '" . $AUTHOR . "', '" . $ACTIVE . "', '" . $DESC . "' )"))
                           {
                             echo '<tr ><td colspan="100%" style="background-color:lightgreen;">Posted succesfully!</td></tr>';
                           }
                           else
                           {
                             echo '<tr ><td colspan="1" style="background-color:red;"' . mysql_errno() . '</td>';
                             echo '<td colspan="100%" style="background-color:red; color:white;">The post could not be added to the server, please check your values<br/>'.mysql_error().'</td></tr>';
                           }
                         }
                      }
else {
	echo "NO DATA";
}

                 ?>
        </tr>
        <tr><td colspan="100%"><hr /></td></tr>
        
        <tr>
        <td>
	         <form name="logout" action="/logout.php" method="POST">
		<input type="submit" value="Logout"></form></td>
       <td><form name="main" action="/masterpage.php" method="POST">
        <input type="submit" value="Return">
      </form></td>
            <td colspan="2">
                <form name="newdj" action="/Poster/p1new.php" method="POST">
                <input type="submit" value="Add Another Post" />
                </form>
            </td>
            <td colspan="2"></td>
		<td><img src="/images/mysqls.png" alt="MySQL" align="right"></td>
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