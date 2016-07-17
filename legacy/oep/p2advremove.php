<?php
    session_start();
?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../../css/altstyle.css" />
<title>DPL Administration</title>
</head>
<html>
<body>
	<div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="../../masterpage.php"><img src="<?php print("../../".$_SESSION['logo']); ?>" alt="logo"/></a>
	</div>
	<div id="top">
		<h2>Remove Episode</h2>
	</div>
	<div id="content">
		<table border="0" class="tablecss">
			<tr>
				<th width="100px">
					Program Name
				</th>
				<th width="100px">
					Genre
				</th>
				<th width="100px">
					Length
				</th>
				<th width="100px">
					Syndicate
				</th>
				<th width="100px">
					Host
				</th>
				<th width="100px">
					Co-Host
				</th>
				<th width="100px">
					Date
				</th>
				<th width="100px">
					Time
				</th>
				<th width="100px">
					Callsign
				</th>
				<th width="100px">

				</th>
			</tr>

<?php

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../login.php');}
	$GENRE = "SELECT * from GENRE order by genreid asc";
	$GENRES = mysql_query($GENRE,$con);
	$genop = "<OPTION VALUE=\"%\">Select Genre</option>";
	while ($genrerow=mysql_fetch_array($GENRES)) {
        $GENid=$genrerow["genreid"];
        $genop.="<OPTION VALUE=\"" . $GENid . "\">". $GENid ."</option>";
    }
	$djsql="SELECT * from DJ order by djname";
    $djresult=mysql_query($djsql,$con);

    $djoptions="<option value=\"%\">Any Host</option><option value=\"0\">None</option>";//<OPTION VALUE=0>Choose</option>";
    while ($djrow=mysql_fetch_array($djresult)) {
        $Alias=$djrow["Alias"];
        $name=$djrow["djname"];
        $djoptions.="<OPTION VALUE=\"$Alias\">" . $name . "</option>";
    }
	if($_POST['name']!=""){
		$SQLA = "SELECT Episode.*, Program.genre, Program.active, Program.length, Program.syndicatesource
	 		FROM EPISODE, PROGRAM, PERFORMS where Episode.programname LIKE \"" . addslashes($_POST['name']) .  "\"";
	}
	else{
		$SQLA = "SELECT Episode.*, Program.genre, Program.active, Program.length, Program.syndicatesource
	 		FROM EPISODE, PROGRAM, PERFORMS where Episode.programname LIKE \"%\"";
	}
	if(isset($_POST['genre'])){
		if($_POST['genre'] != '0'){
			$SQLA .= " and program.genre=\"" . addslashes($_POST['genre']) . "\" ";
		}
	}
	if(isset($_POST['Prerec'])){
		$SQLA .= "and Episode.prerecorddate IS NOT NULL ";
	}
	/*if($_POST['dj1'] != '0'){
		$SQLA .= " and performs.Alias=\"". addslashes($_POST['dj1']) . "\" ";
	}
	if($_POST['dj2'] != '0'){
		$SQLA .= " and performs.CoAlias=\"" . addslashes($_POST['dj2']) . "\" ";
	}*/
	if($_POST['BGDate']!=""){
		if($_POST['ENDate'] != ""){
			$SQLA .= " and date between \"" . addslashes($_POST['BGDate']) . "\" and \"" . addslashes($_POST['ENDate']) . "\" ";
		}
		else{
			$SQLA .= " and date between \"" . addslashes($_POST['BGDate']) . "\" and \"2049-01-01\" ";
		}
	}
	else if($_POST['ENDate']!=""){
			$SQLA .= " and date between \"0001-01-01\" and \"" . addslashes($_POST['ENDate']) . "\" ";
	}
	if($_POST['BGTime']!=""){
		if($_POST['ENTime']!=""){
			$SQLA .= " and episode.starttime between \"" . addslashes($_POST['BGTime']) . "\" and \"" . addslashes($_POST['ENTime']) . "\" ";
		}
		else{
			$SQLA .= " and episode.starttime between \"" . addslashes($_POST['BGTime']) . "\" and \"24:00:00\" ";
		}
	}
	else if($_POST['ENTime']!=""){
			$SQLA .= " and time between \"00:00:00\" and \"" . addslashes($_POST['ENTime']) . "\" ";
	}
	if($_POST['length']!=""){
		$SQLA .= " and program.length LIKE \"" . addslashes($_POST['length']) . "\" ";
	}
	if($_POST['syndicate']!=""){
		$SQLA .= " and program.syndicatesource LIKE \"" . addslashes($_POST['syndicate']) . "\" and program.syndicatesource != '' ";
	}
	if($_POST['callsign']!=""){
		$SQLA .= " and program.callsign LIKE \"" . addslashes($_POST['callsign']) . "\" ";
	}
	if(isset($_POST['active'])){
		$SQLA .= " and program.active=\"1\"";
	}

	$SQLA .= " and Episode.programname=Program.programname and Episode.programname=performs.programname";

	//echo $SQLA;
	$result = mysql_query($SQLA) or die(mysql_error());
             if(mysql_num_rows($result)=="0"){
               echo '<tr><td colspan="100%" style="background-color:yellow;">';
               echo 'No Results Found';
               echo '</tr></td>';
			   //echo $SQLA;
             }
             else{

               while($row=mysql_fetch_array($result)) {
		/*echo "<form name=\"row\" action=\"/program/p3advupdate.php\" method=\"POST\"><tr>
				<td>";*/
		echo "<form name=\"row\" action=\"VEREM.php\" method=\"POST\"><tr>
				<td>";
				echo $row['programname'];
				echo "<input name=\"program\" value=\"" . $row['programname'] . "\" hidden />";
		echo "</td>
				<td>";
				echo $row['genre'];
				echo "<input name=\"genre\" value=\"" . $row['genre'] . "\" hidden />";
		echo "</td>
				<td>";
				echo $row['length'];
				echo "<input name=\"length\" value=\"" . $row['length'] . "\" hidden />";

		echo "</td>
				<td>";
				echo $row['syndicatesource'];
				echo "<input name=\"syndicate\" value=\"" . $row['syndicatesource'] . "\" hidden />";
		echo "</td>
				<td>";
				//echo $row['Alias'];
				//echo "<input name=\"dj1\" value=\"" . $row['Alias'] . "\" hidden />";

		echo "</td>
				<td>";
				//echo $row['CoAlias'];
				//	echo "<input name=\"dj2\" value=\"" . $row['CoAlias'] . "\" hidden />";
		echo "</td>
				<td>";
				echo $row['date'];
					echo "<input name=\"user_date\" value=\"" . $row['date'] . "\" hidden />";
		echo "</td>
				<td>";
				echo $row['starttime'];
					echo "<input name=\"user_time\" value=\"" . $row['starttime'] . "\" hidden />";
		echo "</td>
				<td>";
				echo $row['callsign'];
				echo "<input name=\"callsign\" value=\"" . $row['callsign'] . "\" hidden />";
		echo "</td><td><input type=\"submit\" value=\"select\"/> </td></tr></form>";
		echo "<tr><td colspan=\"100%\"><hr /></td></tr>";
			   }
		}

}
else{
	echo 'ERROR!';
}
?>
</table>

		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
					<form action="p1advremove.php" method="POST">
				<input type="submit" value="Return to Search"/></form></td><td>
				<input type="button" value="Refresh" onClick="window.location.reload()"></td><td>
				<form method="POST" action="../../masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td width="100%" align="right"><img src="../../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>

</body>
</html>
