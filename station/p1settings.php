<?php
include"../TPSBIN/functions.php";
include"../TPSBIN/db_connect.php";
/*
$con = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysqli_select_db($_SESSION['DBNAME'])){header('Location: ../login.php');}
	
    }
else{
	echo 'ERROR!';
}
*/
?>

<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../altstyle.css" />
<title>Settings</title>
</head>
<html>
<body>
	<div class="topbar">
           User:, <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="../masterpage.php"><img src="../images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
	</div>
	<div id="top">
		<h2>Edit Settings and Information</h2>
	</div>
	<div id="content">
		<form name="search" method="POST" action="p2settings.php">
		<table border="0" class="tablecss">
			<?php
				$SQL = "SELECT * FROM station";
				$STATIONS = $mysqli->query($SQL);
				$i = 0;
                                if($STATIONS->num_rows==0){
                                    echo "<tr><td colspan='5'><span><strong style='color:Red;'>ERROR NO STATION CREATED: </strong>"
                                    . "<a href='p1insertstation.php'> Please create a station</a></span></td></tr>";
                                }
				echo "<tr><th>Station Name</th><th>Callsign</th><th>Designation</th><th>Frequencies</th><th>Address</th></tr>";
				while($ST = $STATIONS->fetch_array()){
					echo "<tr ";
					if($i%2){
						echo " style = \"background-colod:lightblue\" />";
					}
					else{
						echo "/>";
					}
					echo"<td><input required type=\"radio\" id=\"".$i."\" name=\"call\" value=\"".$ST['callsign'];
					if($i==0){
						echo "\" checked=\"checked\" />";
					}
					else{
						echo "\"/>";
					}
					echo"<label for=\"".$i."\" >".$ST['stationname']."</label></td><td>".$ST['callsign']."</td><td>".$ST['Designation']."</td><td>".$ST['frequency']."</td><td>".$ST['address'];
					++$i;
				}
				if($i==1){
					echo "<script>
						document.forms[\"search\"].submit();
					</script>";
				}
			?>
		</table>
		
		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
				<input type="submit" value="Select"/></form></td><td>
				<input type="button" value="Reset" onClick="window.location.reload()"></td><td>
				<form method="POST" action="../masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td width="100%" align="right"><img src="../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>
	<div id="content">
			<h4>Help</h4>
		<span>You can enter a % into the field to enter partial information. ie, if a show you 
			wanted to find was called "Best Show Ever" you can put "Best%" and the system will find all shows that begin with "Best", otherwise you can put %show% to
			find any shows that have "show" in the name or "%ever" for shows that end in "ever"</span>
		
	</div>
</body>
</html>