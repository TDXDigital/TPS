<?php
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

      session_start();
/*
if($_SESSION['usr']=='user')
{
  header('location: login.php');
}*/
$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
	if(!mysql_select_db("CKXU")){header('Location: /login.php');} // or die("<h1>Error ".mysql_errno() ."</h1><br />check access (privileges) to the SQL server db CKXU for this user <br /><br /><hr />Error details:<br />" .mysql_error() . "<br /><br /><a href=login.php>Return</a>");
}
else{
	echo 'ERROR!';
}
?>

<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/css/phpstyle.css" />
<title>Unsupported Browser</title>
</head>
      <div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>
        <table border="0" align="center" width="1000">
        <tr><td width="1000" colspan="4">
                <img src="<?php print($_SESSION['logo']); ?>" alt="logo"/>
        </td></tr>
	<table border="0" width="1000" style="background-color:white;">
	<tr><td colspan="100%" width="1000" style="background-color:red; color:white;">
        <h2>ERROR - Unsupported Browser</h2>
        </td></tr>
        <tr height="50" valign="top" >
                  <td width="100%" colspan="3">
	            <?php
	              echo "<br/>Sorry but <strong>" . getBrowser();?>

	              </strong><br/>Is not supported by this site, please use one of the compatable browsers listed below:
	              <br/><br/>1) Opera 11 +<br/>2) Mozilla Firefox 11+<br/>3) Google Chrome 18+<br/>4) Internet Explorer 7+<br/>Thank You!<br/></br>
	              <?php echo "Detected Browser Information: ".$_SERVER['HTTP_USER_AGENT'] . "</br>";
				  echo get_browser(null,true);
	            ?>
	     </td></tr>
        <tr>
             <tr>
             <td colspan="100%" height="20">
             <hr/>
             </td>
             </tr>
             <tr style="font-align:left;">
             <td width="50">
             <form name="logout" action="/logout.php" method="POST">
                   <input type="submit" value="Logout">
             </form>
             </td>
             <td>
             <!--<form name="Return" action="/masterpage.php" method="POST">
                   <input type="submit" value="Return">
             </form>-->
             </td>
             <td colspan="3" width="100%"></td>
             <td style="text-align:right;" >
        <img src="/images/mysqls.png" alt="MySQL Powered">
        </td></tr>
        </table>
        </table>
</body>
</html>
