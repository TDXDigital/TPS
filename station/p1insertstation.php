<?php
      session_start();

$con = mysql_connect(constant('HOST'),constant('USER'),constant('PASSWORD'),constant('DATABASE'));
/*if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../login.php');} // or die("<h1>Error ".mysql_errno() ."</h1><br />check access (privileges) to the SQL server db CKXU for this user <br /><br /><hr />Error details:<br />" .mysql_error() . "<br /><br /><a href=login.php>Return</a>");


}
else{
	echo 'ERROR!';
}*/
?>

<head>
<link rel="stylesheet" type="text/css" href="../phpstyle.css" />
<title>Station Insertion</title>
</head>
<html>
<body>
      <div class="topbar">
           <a class="right" href="../logout.php"> Logout </a>Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>
           <!--<img src="../images/Ckxu_logo_PNG.png" alt="ckxu login"/>-->

	<h2>Create new station</h2><br />
	<hr />
	<br />
	<h1>Program Log Administration</h1><br />
              <p>

             </p>
             <form name="form1" action="p2insertstation.php" method="post" >
                         callsign [4digit] <input name="callsign" type="text" size=4 /> <br/><br />
                         Station Name <input name="name" type="text" size=45/><br /><br />
                         frequency <input name="frequency" type="text" size=10/><br /><br />
                         address <input name="address" type="text" size=100/><br /><br />
                         booth phone <input name="boothph" type="text" size=45/><br /><br />
                         director phone <input name="direcphone" type="text" size=45/><br /><br />
                         designation <input name="designation" type="text" size=11/><br /><br />
                         website <input name="website" type="text" size=45/><br /><br />
                         <div style="margin-left:75px"><input type="submit" name="submit" value="Insert" />
             </div>
             <br />

	<hr />
        <!--<a href="logout.php" align='center' >Logout</a>--> <a href="../masterpage.php">Return</a><br/><p>
        <img src="../images/mysqls.png" alt="MySQL Powered"></span></p>
</body>
</html>
