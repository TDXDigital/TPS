<?php
    session_start();
?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../altstyle.css" />
<title>DPL Administration</title>
</head>
<html>
<body>
	<div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="../masterpage.php"><img src="../images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
	</div>
	<div id="top">
		<h2>Edit Program Advanced Search</h2>
	</div>
	<div id="content">
		<table border="0" class="tablecss">
			<tr>
				<th width="10px">
					
				</th>
				<th width="290px">
					Program Name
				</th>
				<th width="100px">
					Genre
				</th>
				<th width="50px">
					Length
				</th>
				<th width="150px">
					Syndicate
				</th>
				<th width="300px">
					Hosts
				</th>
				<th width="100px">
					Callsign
				</th>
				<th width="100px">
					Active
				</th>
			</tr>

<?php

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db("CKXU")){header('Location: ../login.php');}
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
        $djoptions.="<OPTION VALUE=\"" . $Alias . "\">" . $name . "</option>";
    }
	$SQLA = "Select PROGRAM.* from PROGRAM where program.programname LIKE '%" . addslashes($_POST['name']) . "%' ";
	// build query
	if(isset($_POST['callsign'])){
		$SQLA .= "and program.callsign LIKE '%" . addslashes($_POST['callsign']) . "%' ";
	}
	/*if(isset($_POST['dj1'])){
		if($_POST['dj1']!='0'){
			$SQLA .= "and performs.Alias LIKE '" . addslashes($_POST['dj1']) . "' ";
		}
	}*/
	/*if(isset($_POST['dj2'])){
		if($_POST['dj2']!='0')
		{
			$SQLA .= "and performs.CoAlias LIKE '" . addslashes($_POST['dj2']) . "' ";
		}
	}*/
	if(isset($_POST['length'])){
		$SQLA .= "and program.length LIKE '%" . addslashes($_POST['length']) . "%' ";
	}
	if(isset($_POST['syndicate'])){
		$SQLA .= "and program.syndicatesource LIKE '%" . addslashes($_POST['syndicate']) . "%' ";
	}
	if(isset($_POST['genre'])){
		$SQLA .= "and program.genre LIKE '" . addslashes($_POST['genre']) . "' ";
	}
	$SQLA .= " order by programname";
	
	$result = mysql_query($SQLA) or die(mysql_error());
             if(mysql_num_rows($result)=="0"){
               echo '<tr><td colspan="100%" style="background-color:yellow;">';
               echo 'No Results Found';
               echo '</tr></td>';
			   echo $SQLA;
             }
             else{
             	
		//------------------------- START LOOP OF PROGRAMS ---------------------------------
		echo "<form name=\"row\" action=\"p3advupdate.php\" method=\"POST\">";
		$count = 0;
		if(mysql_num_rows($result)==1){
			$row = mysql_fetch_array($result);
				header("location: p3advupdate.php?resource=" . $row['programname'] . "@" . $row['callsign']);
		}
		else{
			while($row=mysql_fetch_array($result)) {
	        	$labelr="<label for=\"line".$count."\">".$row['programname']."</label>";      	
				/*echo "<form name=\"row\" action=\"/program/p3advupdate.php\" method=\"POST\"><tr>
						<td>";*/	
				echo "<tr";
				if($count%2){
					echo " style=\"background-color:#DAFFFF;\" ";
				}
				echo">
						<td>";
						echo "<input type=\"radio\" name=\"postval\" required=\"true\" id=\"line".$count."\" value=\"".$row['programname']."@&".$row['callsign']."\" /></td><td>";	
						++$count;
						
						$labelr.= "</td>
						<td>" . $row['genre']. "</td>
						<td>" . $row['length']. "</td>
						<td>" . $row['syndicatesource'] ."</td>
						<td>";
						//echo "<input name=\"syndicate\" value=\"" . $row['syndicatesource'] . "\" hidden />";
						$SQDJ = "select Alias from PERFORMS where programname=\"" . addslashes($row['programname']) . "\" and callsign=\"" . addslashes($row['callsign']) . "\"";
						if(!($perfres = mysql_query($SQDJ))){
							echo mysql_error();
						}
						else{
							$alias=mysql_fetch_array($perfres);				
							$labelr .= $alias['Alias'];
							while($alias=mysql_fetch_array($perfres)){
								$labelr .= ", " . $alias['Alias'];
							}
						}
						$labelr .= "</td>
						<td>".  $row['callsign'] . "</td>
						<td><input type=\"checkbox\" onclick=\"javascript: return false;\" ";
						
						if($row['active']!=0){
							$labelr .= "checked";
						}
						
						$labelr .= " />";
						
						$labelr .= "</td></tr>";
						
						
						echo $labelr;
						
					   }
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
					<input type="submit" value="Select" /></form></td><td>
					<form action="p1advupdate.php" method="POST">
				<input type="submit" value="Advanced Search"/></form></td><td>
					<form action="p1update.php" method="POST">
				<input type="submit" disabled="true" value="Standard Search"/></form></td><td>
				<input type="button" value="Reset" onClick="window.location.reload()"></td><td>
				<form method="POST" action="../masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td width="100%" align="right"><img src="../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>

</body>
</html>