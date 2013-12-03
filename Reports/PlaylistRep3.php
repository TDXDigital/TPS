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
	if($_POST['verification']=="soundex"){
        $SQL0 = "select count(playlistnumber), cancon, playlistnumber as playlist, (SELECT artist from song where playlistnumber=playlist and date between '" . addslashes($_POST['from']) . "' and '" . addslashes($_POST['to']) . "' group by soundex(album) order by count(soundex(album)) desc limit 1) as artist,(SELECT album from song where playlistnumber=playlist and date between '" . addslashes($_POST['from']) . "' and '" . addslashes($_POST['to']) . "'  group by soundex(album) order by count(soundex(album)) desc limit 1) as album ";
    }
    else{
        $SQL0 = "select count(playlistnumber), cancon, playlistnumber as playlist, artist, album ";
    }
    /*if($_POST['verification']=="soundex"){
        $SQL_Ver=", ((SELECT playlistnumber, count(*) as count, (SELECT count(*) from song where ";
        if($_POST['FROM']!='' && $_POST['to']!=''){
            $SQL_Ver .= "song.date between '".addslashes($_POST['from'])."' and '".addslashes($_POST['to'])."' and playlistnumber=playlist) as total, soundex(artist) as sndx FROM song WHERE playlistnumber=playlist and song.date between '".addslashes($_POST['from'])."' and '".addslashes($_POST['to'])."' group by soundex(album) order by count desc limit 1)";
        }
        else{
            $SQL_Ver .= "playlistnumber=playlist) as total, soundex(artist) as sndx FROM song WHERE playlistnumber=playlist group by soundex(album) order by count desc limit 1))";
        }
        
    }
    else{*/
        $SQL_Ver=", soundex(album) ";
    //}
    $SQL1 = " from song where date between '" . addslashes($_POST['from']) . "' and '" . addslashes($_POST['to']) . "' ";
	$SQL2 = "";
	if(isset($_POST['exempt'])){
		$EXEM = $_POST['exempt'];
		for($iex=0 ; $iex < sizeof($EXEM); ++$iex){
			$SQL2 .= "and programname!='" . addslashes($EXEM[$iex]) . "' "; 
		}
	}
	
	//INSERT EXCLUDE HERE
	//$SQL3 = "group by playlistnumber order by count(playlistnumber) desc, playlistnumber asc";
    $SQL3 = "group by playlistnumber order by count(playlistnumber) desc, playlistnumber asc";
	$SQLM = $SQL0 . $SQL_Ver . $SQL1 . $SQL2 . $SQL3; 
    //echo $SQLM;
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
            /*
            SELECT playlistnumber, count(*) as count, (SELECT count(*) from song where song.date between '2013-11-01' and '2013-12-01' and playlistnumber='333') as total, soundex(artist) as sndx 
            FROM song WHERE playlistnumber='333' and song.date between '2013-11-01' and '2013-12-01' group by soundex(album) order by count desc limit 1;
            */
            if($_POST['verification']=="soundex"){
                if($_POST['from']!='' && $_POST['to']!=''){
                    $SNDX_SQL = "SELECT playlistnumber, count(*) as count, (SELECT count(*) from song where playlistnumber='".$row['playlist']."' and date between '".addslashes($_POST['from'])."' and '".addslashes($_POST['to'])."') as total, soundex(artist) as sndx FROM song WHERE playlistnumber='".addslashes($row['playlist'])."' and date between '".addslashes($_POST['from'])."' and '".addslashes($_POST['to'])."' group by soundex(album) order by count desc limit 1 ";
                }
                else{
                    $SNDX_SQL = "SELECT playlistnumber, count(*) as count, (SELECT count(*) from song where playlistnumber='".$row['playlist']."') as total, soundex(artist) as sndx FROM song WHERE playlistnumber='".addslashes($row['playlist'])."' group by soundex(album) order by count desc limit 1 ";
                }
                $soundex = mysql_query($SNDX_SQL) or die(mysql_error());
                $sound = mysql_fetch_array($soundex);
                $VERR = round(((floatval($sound['count'])/floatval($sound['total']))*100),2);
            }
            else{
                $VERR=NULL;
            }
            $TableVals .= ",
            [$chnum,".$row['playlist'].",".$row['count(playlistnumber)'].",'".addslashes($row['artist'])."','".addslashes($row['album'])."',$VERR,'<a href=\'../Reports/p2SongSearch.php?playlist=".$row['playlist']."&from=".$_POST['from']."&to=".$_POST['to']."\' target=\'_blank\'><span class=\"ui-icon ui-icon-circle-plus\">View</span></a>']";
			++$chnum;
		}
	}
?>

<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../altstyle.css" />
    <link rel="stylesheet" type="text/css" href="../station/genres/genres.css"/>
    <!--<link rel="stylesheet" type="text/css" href="../js/jquery/css/smoothness/jquery-ui-1.10.0.custom.min.css"/>-->
    <!--<style>
        .google-visualization-table-table {
            padding: 0 0 0 0;
        }
    </style>-->
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
        ['Chart', 'Playlist', 'Occurances', 'Artist', 'Album', 'Album\n Score', 'Records']
        <?php
            echo $TableVals;
        ?>
      ]);
    
      // Create and draw the visualization.
      visualization = new google.visualization.Table(document.getElementById('table'));
      visualization.draw(data, {showRowNumber: false, allowHtml:true, alternatingRowStyle:true, padding:0});
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