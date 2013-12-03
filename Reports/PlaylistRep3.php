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
	$SQL0 = "select count(playlistnumber), cancon, playlistnumber, artist, album ";
    if($_POST['verification']=="soundex"){
        $SQL_Ver="(SELECT count(songid) from song where ) AS Artist_Score";
    }
    else{
        $SQL_Ver="";
    }
    $SQL1 = " from song where date between '" . addslashes($_POST['from']) . "' and '" . $_POST['to'] . "' ";
	$SQL2 = "";
	if(isset($_POST['exempt'])){
		$EXEM = $_POST['exempt'];
		for($iex=0 ; $iex < sizeof($EXEM); ++$iex){
			$SQL2 .= "and programname!='" . addslashes($EXEM[$iex]) . "' "; 
		}
	}
	
	//INSERT EXCLUDE HERE
	$SQL3 = "group by playlistnumber order by count(playlistnumber) desc, playlistnumber asc";
	$SQLM = $SQL0 . $SQL_Ver . $SQL1 . $SQL2 . $SQL3; 
	
	$arr = mysql_query($SQLM) or die(mysql_error());
	$Resu = "";
	$chnum = 1;
	while ($row = mysql_fetch_array($arr)) {
		if($row['count(playlistnumber)']!=0){
            // CSS now handles alternate row color
			/*if($chnum%2){
				$Resu .= "<tr style=\"background-color: #DAFFFF;\">";
			}
			else{
				$Resu .= "<tr>";
			}*/
			/*$Resu .= "<tr><td align=center>" . $chnum . "</td><td align=center>". $row['playlistnumber'] . "</td><td align=center>" . $row['count(playlistnumber)'] . "</td>
			<td align=center >" . $row['artist'] . "</td><td align=center>" . $row['album'] . "</td></tr>";*/
            $TableVals .= ",
            [$chnum,".$row['playlistnumber'].",".$row['count(playlistnumber)'].",'".addslashes($row['artist'])."','".addslashes($row['album'])."','','']";
			++$chnum;
		}
	}
?>

<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../altstyle.css" />
    <link rel="stylesheet" type="text/css" href="../station/genres/genres.css"/>
<title>Charts</title>
    
    <!-- GOOGLE API TABLE-->
    <script type="text/javascript" src="//www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['table']});
    </script>
    <script type="text/javascript">
    function drawVisualization() {
      // Create and populate the data table.
      var data = google.visualization.arrayToDataTable([
        ['Chart Number', 'Playlist Number', 'Occurances', 'Artist', 'Album', 'V-A Score', 'Records']
        <?php
            echo $TableVals;
        ?>
      ]);
    
      // Create and draw the visualization.
      visualization = new google.visualization.Table(document.getElementById('table'));
      visualization.draw(data, {showRowNumber: false, allowHtml:true, alternatingRowStyle:true});
        //visualization.alternatingRowStyle(true);
    }
    

    google.setOnLoadCallback(drawVisualization);
    </script>
</head>
<body>
	<div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="../masterpage.php"><img src="../images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
	</div>
	<div id="top">
		<h2>Playlist Report</h2>
	</div>
	<div id="content">
        <div id="table"></div>
		<!--<table>
            <thead>
			    <tr>
				    <th style="width:5%">Chart Number</th>
				    <th style="width:10%">Playlist Number</th>
				    <th style="width:10%">Times Played</th>
				    <th style="width:37.5%">Artist</th>
				    <th style="width:37.5%">Album</th>
			    </tr>
            </thead>
            <tbody>
			    <?php echo $Resu; ?>
            </tbody>
		</table>-->
		</div>
	<div id="foot">
		<table>
            <tfoot>
			    <tr>
				    <td style="width:100%; text-align:left">
				    <input type="button" value="Search" onclick="window.location.href='PlaylistRep.php'">
				    <input type="button" value="Refresh" onClick="window.location.reload()">
				    <input type="button" onclick="window.location.href='../masterpage.php'" value="Menu"/>
				    <img style="float:right" src="../images/mysqls.png" alt="MySQL Powered"/></td>
			    </tr>
            </tfoot>
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