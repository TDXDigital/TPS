<?php
    session_start();

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: /user/login');} 
?>

<!DOCTYPE HTML>
<html style="height: 100%">
<head>
<link rel="stylesheet" type="text/css" href="../altstyle.css" />
<title>Search</title>
</head>

<body style="height: 100%">
	<div class="topbar">
           USER: <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="../masterpage.php"><img src="../images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
	</div>
	<div id="top">
		<h2>Song Detail</h2>
	</div>
	<div id="content">
		<form name="General" id="form1" action="p2SongSearch.php" method="POST">
		<table>
			<tr><label>Report Type</label><input type="radio" id="single" name="option" value="Single" checked="checked"><label for="single">Single Playlist</label>
				<input type="radio" id="Multi" name="option" value="Single" disabled="disabled"><label for="Multi">Multi Playlist</label></tr>
			<tr><th>Playlist</th><th>Selection Type</th><th>Date From</th><th>Date To</th><th>Program</th><th>CC</th><th>Hit</th><th>Ins</th></tr>
			<tr><td><!--<input type="number" min="0" name="Playlist" size="5" title="Playlist Number, may contain Wildcard"/>-->
				<input list="playlist" name="Playlist" size="6"/>
				<datalist id="playlist">
						<?php
							$result = mysql_query("select playlistnumber from song group by playlistnumber asc");
							if(mysql_error()!='0'){
								echo "<option value='".mysql_error()."' >
								";
								
							}
							while($row = mysql_fetch_array($result)){
								echo "<option value=\"" . $row['playlistnumber'] . "\">
								"; 
							}
						?>
					</datalist>
			</td>
				<td><select name="PLOpt">
					<option selected value="A">All Songs</option>
					<option value="P">Playlist Only</option>
					<option value="N">No Playlist</option>
				</select></td>
				<td>
					<input type="date" name="from" />
				</td>
				<td>
					<input type="date" name="to" />
				</td>
				<td>
					<input list="program" name="program" />
					<datalist id="program">
						<?php
							$result = mysql_query("select programname from program");
							if(mysql_error()!='0'){
								echo "<option value='".mysql_error()."' >
								";
								
							}
							while($row = mysql_fetch_array($result)){
								echo "<option value=\"" . $row['programname'] . "\">
								"; 
							}
						?>
					</datalist>
				</td>
				<td>
					<input type="checkbox" name="CC" value="CC" />
				</td>
				<td>
					<input type="checkbox" name="HIT" value="Hit" />
				</td>
				<td>
					<input type="checkbox" name="INS" value="Ins" />
				</td>
			</tr>
			</table><table><tr><th>Title</th><th>Artist</th><th>Album</th><th>Composer</th><th>Language</th><th>Category</th></tr>
			
				<tr><td>
					<input type="text" name="Title" />
				</td>
				<td>
					<input type="text" name="Artist" />
				</td>
				<td>
					<input type="text" name="Album" />
				</td>
				<td>
					<input type="text" name="Composer" />
				</td>
				<td>
					<input type="text" name="Language" />
				</td>
				<td  colspan="3" >
					<input type="text" name="Category" />
				</td>
			</tr>
			
		</table>
		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
				<input type="submit" value="Search"/></form></td><td>
				<input type="button" value="Reset" onClick="document.forms['General'].reset()"></td><td>
				<form method="POST" action="../masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td width="100%" align="right"><img src="../images/mysqls.png" alt="MySQL Powered"/></td>
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