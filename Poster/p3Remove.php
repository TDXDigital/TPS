<?php
    session_start();

$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db("posts")){header('Location: /login.php');}
	$SQL = "Delete from news where postnum='" . $_POST['postnum'] . "'";
?>

<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="/altstyle.css" />
<title>DPL Administration</title>
</head>
<html>
<body>
	<div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="/masterpage.php"><img src="/images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
	</div>
	<div id="top">
		<h2>Remove Post</h2>
	</div>
	<div id="content">
		<p>
			<?php
				if(mysql_query($SQL)){
					echo "Removed Post";
				}
				else{
					echo mysql_error();
				}
			?>
		</p>
		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
				<button onclick="window.location.href='/Poster/p1Remove.php'" value="Remove">Search</button></td><td>
				<button onClick="window.location.reload()" value="Reset" >Reset</button></td><td>
				<button onclick="window.location.href='/masterpage.php'" value="menu">Menu</button>
				</td>
				<td width="100%" align="right"><img src="/images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>
	
<?php

}
else{
	echo 'ERROR!';
}
?>
</body>
</html>