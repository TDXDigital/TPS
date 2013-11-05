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
	$SQL = "SELECT * from news where postnum LIKE '%' ";
	if($_POST['header']!=""){
		$SQL .= " and Header LIKE '" . $_POST['header'] . "' ";
	}
	if($_POST['Author']!=""){
		$SQL .= "and Author LIKE '" . $_POST['Author'] . "' ";
	}
	if($_POST['content']!=""){
		$SQL .= "and Content LIKE '" . $_POST['content'] . "' ";
	}
	if(isset($_POST['Visible'])){
		$SQL .= "and Hidden='0' ";
	}
	else{
			$SQL .= "and Hidden='1' ";
	}
	
	$RESULT = mysql_query($SQL);
	
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
		<table border="0" class="tablecss">
			<tr>
				<th>
					Header
				</th>
				<th>
					Author
				</th>
				<th>
					Content
				</th>
				<th>
					Visible
				</th>
			</tr>
		<?php
			while($ROW = mysql_fetch_array($RESULT)){
				echo "<form action=\"/Poster/p3Remove.php\" method=\"POST\"><tr><td>";
				echo $ROW['Header'];
				echo "</td><td>";
				echo $ROW['Author'];
				echo "</td><td>";
				echo $ROW['Content'];
				echo "</td><td>";
				echo "<input type=\"checkbox\" disabled=\"true\" checked=\"";
				if(isset($ROW['Visible'])){
					echo "0\" />";
				}
				else{
					echo "1\" />";
				}
				echo "<input type=\"text\" name=\"postnum\" hidden=\"true\" value=\"" . $ROW['postnum'] . "\" />";
				echo "</td><td><input type=\"submit\" value=\"Select\" /></td><tr></form>";
				
			}
		?>
		</table>
		
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