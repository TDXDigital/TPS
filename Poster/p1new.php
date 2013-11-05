<?php session_start(); ?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="/phpstyle.css" />
<title>Post Administration</title>
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
        ?>

        <table align="left" border="0" height="100">
        <tr><td colspan="7">
        <h2>New Post</h2>
        </td></tr>

        <tr><th colspan="2" width="30%">
        Post Header
        </th><th width="15%">
        Author
        </th><th width="50%">
        Content
        </th><th width="5%">
        Visible
        </th>
        </tr>
             <form name="newpost" action="/Poster/p2new.php" method="post">
             <tr valign="top">
             <td colspan="2">
                 <input name="header" type="text" size="40"/>
             </td>
             <td>
                 <input name="author" type="text" size="15"/>
             </td>
             <td>
                 <textarea name="content" rows="4" cols="58"></textarea>
             </td>
             <td>
                 <input name="active" type="checkbox" checked />
             </td>
            <td colspan="7">
                <input type="submit" value="Post" />
                </form>
            </td>
        </tr>
        

        <?php

}
else{
	echo 'ERROR!';
}

echo '<tr height="20"><td colspan="7" style="text-align:bottom;"><hr/></td></tr>';

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
        <td colspan="4"></td>
        <td style="text-align:right;">
        <img src="/images/mysqls.png" alt="MySQL Powered" />
        </td>
        </tr>
        
        </table>
        </td>
        </tr>
        </table>
</body>
</html>