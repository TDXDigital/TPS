<?php
date_default_timezone_set("UTC");
    session_start();

$con = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysqli_error($con) . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysqli_select_db($con,$_SESSION['DBNAME'])){header('Location: /user/login');}
?>

<!DOCTYPE HTML>
<html style="height: 100%">
<head>
<link rel="stylesheet" type="text/css" href="../css/altstyle.css" />
<title>Search</title>
</head>

<body style="height: 100%">
	<div class="topbar">
           USER: <?php echo(strtoupper($_SESSION['fname'])); ?>
    </div>
	<div id="header">
		<a href="../masterpage.php"><img src="<?php print("../".$_SESSION['logo']); ?>" alt="logo"/></a>
	</div>
	<div id="top">
		<h2>Song Detail</h2>
	</div>
	<div id="content">
		<form name="General" id="form1" action="p2SongSearch.php" method="POST">
            <div>
                <label>Report Type</label>
                <input type="radio" id="standard" name="option" value="Standard" checked="checked"/>
                <label for="standard">Standard</label>
                <input type="radio" id="single" name="option" value="Playlist"/>
                <label for="single">Playlist Only</label>
				<input type="radio" id="Multi" name="option" value="Exclusive"/>
                <label for="Multi">No Playlist</label>
            </div>
		<table>
            <thead>
			    <tr>
                    <th>Playlist</th>
                    <th>Selection Type</th>
                    <th>Date From</th>
                    <th>Date To</th>
                    <th>Program</th>
                    <th>CC</th>
                    <th>Hit</th>
                    <th>Ins</th>
                </tr>
            </thead>
            <tbody>
			<tr><td><!--<input type="number" min="0" name="Playlist" size="5" title="Playlist Number, may contain Wildcard"/>-->
				<input list="playlist" name="Playlist" size="6"/>
				<datalist id="playlist">
						<?php
							$result = mysqli_query($con, "select playlistnumber from song group by playlistnumber asc");
							if(mysqli_error($con)!='0'){
								echo "<option value='".mysqli_error($con)."' >
								";

							}
							while($row = mysqli_fetch_array($con, $con, $result)){
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
							$result = mysqli_query("select programname from program");
							if(mysqli_error($con)!='0'){
								echo "<option value='".mysqli_error($con)."' >
								";

							}
							while($row = mysqli_fetch_array($con, $result)){
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
			</tbody>
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
