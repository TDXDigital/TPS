<?php
date_default_timezone_set('UTC');
    session_start();

$con = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysqli_select_db($con, $_SESSION['DBNAME'])){header('Location: /user/login');}
	$prosql="SELECT * from Episode where date between '" . addslashes($_POST['from']) . "' and '" . addslashes($_POST['to']) . "' group by programname order by programname, date, starttime";
    $proresult=mysqli_query($con, $prosql);

    $prooptions="
    ";//<OPTION VALUE=0>Choose</option>";
    $CONTROL=0;
    while ($row=mysqli_fetch_array($proresult)) {
        $name=$row["programname"];
		//$entries = mysql_query("Select count(songid) from song where programname='" . addslashes($name) . "' and date='" . $row['date'] . "' and starttime='" . $row['starttime'] . "' group by programname") or die(mysql_error());
		$entries = mysql_query("Select count(programname) from episode where programname='". addslashes($name) . "' and date between '" . addslashes($_POST['from']) . "' and '" . addslashes($_POST['to']) . "' ");
		if(mysql_num_rows($entries)!=0){
			$rowprecount = mysql_fetch_array($entries);
			$rowcount = $rowprecount['count(programname)'];
		}
		else{
			$rowcount = '';
		}
        $prooptions.="<tr ";
		if($CONTROL%2){
			 $prooptions .= " style=\"background-color:#DAFFFF; \" ";
		}
        $prooptions.="><td colspan=\"2\" >". $name ."</td><td><input type=\"checkbox\" name=\"exempt[]\" value=\"". addslashes($name) ."\"></td>";

		$episodes=mysql_query("select * from episode where programname='" . addslashes($name) . "' and date between '" . addslashes($_POST['from']) . "' and '" . addslashes($_POST['to']) . "' ");

		$SUBCON=0;
		if(mysql_num_rows($episodes)>1){
			$prooptions.="<td align=center> </td><td align=center>   </td><td align=center>" . $rowcount . "</td><td>";
			$prooptions.="<button type=\"button\" id=\"".$name."\" onclick=\"showsub('".addslashes($name)."')\">Show/Hide</button></td></tr>";
			$prooptions .= "<tr class=\"" . addslashes($name) . "\" style=\"background-color:#FFEEAA; display:none; \"><th>Number of Playlist</th><th>songs</th><th>Exclude</th><th>Date</th><th>Time</th><th>End Time</th><th>View</th>";
			while($subrow=mysql_fetch_array($episodes)){
				$prooptions .= "<tr class=\"" . $name;
				if($SUBCON%2){
					$prooptions .= "\" style=\"background-color:#FFFF99; display:none;\" ";
				}
				else{
					$prooptions .= "\" style=\"background-color:#FFFFBB; display:none;\" ";
				}
				$prooptions .= "\"><td>";//Col for PL #

		//##################### - Playlist Count - ##########################
				$SQLPLAY="select count(playlistnumber) from SONG where programname='".addslashes($subrow['programname'])."' and date='".$subrow['date']."' and starttime='".$subrow['starttime']."' and playlistnumber IS NOT NULL";
				if($subplay = mysql_fetch_array(mysql_query($SQLPLAY)))
				{
					$prooptions .= $subplay["count(playlistnumber)"];
				}
				else
				{
					$prooptions .= mysql_error();
				}
				$prooptions .= "</td><td>
				";

		//##################### - Song Count - ##########################
				$SQLSONG="select count(songid) from SONG where category NOT LIKE '1%' and category NOT LIKE '4%' and category NOT LIKE '5%' and programname='".addslashes($subrow['programname'])."' and date='".$subrow['date']."' and starttime='".$subrow['starttime']."'";
				if($subsong = mysql_fetch_array(mysql_query($SQLSONG)))
				{
					$prooptions .= $subsong["count(songid)"];
				}
				else
				{
					$prooptions .= mysql_error();
				}
				$prooptions .= "</td><td>
				";


		//##################### - Exclude - ##########################
				$prooptions .= "<input type=\"checkbox\" name=\"exclude[]\" value=\"".addslashes($subrow['programname'])."@".addslashes($subrow['date'])."@".addslashes($subrow['starttime'])."\" />";
				$prooptions .= "</td><td>
				";

		//##################### - Date - ##########################
				$prooptions .= $subrow['date'];
				$prooptions .= "</td><td>
				";
		//##################### - Time - ##########################
				$prooptions .= $subrow['starttime'];
				$prooptions .= "</td><td>
				";
		//##################### - finalized - ##########################
				if($subrow['endtime']==''){
				    $prooptions .= "Not Finalized";
				}else{
				    $prooptions .= $subrow['endtime'];
				}
				$prooptions .= "</td><td>
				";
		//##################### - View - ##########################
				$prooptions .="<button type=\"button\" onclick=\"javascript:quickview('../legacy/oep/quickview.php?args=".addslashes($subrow['programname'])."@".$subrow['date']."@".$subrow['starttime']."@".$subrow['callsign']."')\">View</button>";
				$prooptions .= "</td></tr>
				";
				++$SUBCON;
			}
		}
		else{
		$prooptions.="<td align=center>" . $row['date'] . "</td><td align=center>" . $row['starttime'] . "</td><td align=center>" . $rowcount . "</td><td>";
		$subrow=mysql_fetch_array($episodes);
		$prooptions .="<button type=\"button\" onclick=\"javascript:quickview('../legacy/oep/quickview.php?args=".addslashes($subrow['programname'])."@".$subrow['date']."@".$subrow['starttime']."@".$subrow['callsign']."')\">View</button>";
        $prooptions.="</td></tr>
        ";
		}
		++$CONTROL;
    }
	$prooptions .= "</tr>
	"
?>

<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../css/altstyle.css" />
    <link rel="stylesheet" type="text/css" href="../js/jquery/css/smoothness/jquery-ui-1.10.0.custom.min.css"/>
    <script src="../js/jquery/js/jquery-2.0.3.min.js" type="text/javascript"></script>
    <script src="../js/jquery/js/jquery-ui-1.10.0.custom.min.js" type="text/javascript"></script>
    <!--<link rel="stylesheet" href="../station/Genres/Genres.css" />-->
    <script>
     function showsub(element) {
         //document.getElementById(element).display=true;
         var xyz = document.getElementsByClassName(element);
         for (var i = 0; i < xyz.length; i++) {
             if (xyz[i].style.display == "none") {
                 xyz[i].style.display = "table-row";
             }
             else{
                 xyz[i].style.display = "none";
             }
         }
     }
     /*function hidesub(element) {
     //document.getElementById(element).disabled=true;
     var xyz = document.getElementsByClassName(element);
     for(var i = 0; i <xyz.length;i++){
     xyz[i].style.display="none";
     }
     } */
     function quickview(url) {
         //use @ to differentiate
         newwindow = window.open(url, 'name', 'height=800,width=800');
         if (window.focus) { newwindow.focus() }
         return false;
     }
	</script>
<title>DPL Administration</title>
</head>
<body>
	<div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="../masterpage.php"><img src="<?php print("../".$_SESSION['logo']); ?>" alt="logo"/></a>
	</div>
	<div id="top">
		<h2>Playlist Report</h2>
	</div>
	<div id="content">
        <form action="PlaylistRep3.php" method="POST">
		<table>
            <thead>
                <tr>
                    <th><label for="verification">Verification</label></th><th colspan="2"></th>
                    <th><label for="from">Start</label></th>
                    <th><label for="to">To</label></th>
                    <th><label for="limit">Limit</label></th>

                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2"><input type="text" readonly="readonly" name="verification" value="<?php echo $_POST['verification']?>"/></td>
                    <td></td>
                    <td><input type="text" readonly="readonly" name="from" value="<?php echo $_POST['from'] ?>" /></td>
				    <td><input type="text" readonly="readonly" name="to" value="<?php echo $_POST['to'] ?>" /></td>
				    <td><input type="text" readonly="readonly" name="limit" value="<?php echo $_POST['limit'] ?>" /></td>
                </tr>
            </tbody>
			<thead>
                <tr>
				    <th colspan="2" style="width:30%">Program Name</th>
				    <th style="width:10%">Exclude</th>
				    <th style="width:10%">Date</th>
				    <th style="width:30%">Time</th>
				    <th style="width:30%">Logs</th>
				    <th style="width:20%">Details</th>
                </tr>
			</thead>
			<?php echo $prooptions; ?>
		</table>
		</div>
	<div id="foot">
		<table>
			<tr>
				<td>

				<input type="submit" value="Submit"/></form></td><td>
				<input type="button" value="Reset" onClick="window.location.reload()"></td><td>
				<input type="button" value="Menu" onClick="window.location.href='../masterpage.php'"/></form>
				</td>
				<td style="width:100%; text-align:right"><img src="../images/mysqls.png" alt="MySQL Powered"/></td>
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
