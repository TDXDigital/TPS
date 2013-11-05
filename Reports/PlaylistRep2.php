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
	$prosql="SELECT * from Episode where date between '" . addslashes($_POST['from']) . "' and '" . addslashes($_POST['to']) . "' group by programname order by programname, date, starttime"; 
    $proresult=mysql_query($prosql,$con);

    $prooptions="<form action=\"PlaylistRep3.php\" method=\"POST\">
    ";//<OPTION VALUE=0>Choose</option>";
    $CONTROL=0;
    while ($row=mysql_fetch_array($proresult)) {
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
			$prooptions.="<button type=\"button\" id=\"".$name."\" onclick=\"showsub('".addslashes($name)."')\">Expand</button></td></tr>";
			$prooptions .= "<tr class=\"" . addslashes($name) . "\" style=\"background-color:#FFEEAA; display:none; \"><th>Number of Playlist</th><th>songs</th><th>Exclude</th><th>Date</th><th>Time</th><th>End Time</th><th>View</th>";
			while($subrow=mysql_fetch_array($episodes)){
				$prooptions .= "<tr class=\"" . $name;
				if($SUBCON%2){
					$prooptions .= "\" style=\"background-color:#FFFF99; display:none;\" ";
				}
				else{
					$prooptions .= "\" style=\"background-color:#FFFFBB; display:none;\" ";
				}
				$prooptions .= "><td>";//Col for PL #
				
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
				$SQLSONG="select count(songid) from SONG where programname='".addslashes($subrow['programname'])."' and date='".$subrow['date']."' and starttime='".$subrow['starttime']."'";
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
				$prooptions .= $subrow['endtime'];
				$prooptions .= "</td><td>
				";	
		//##################### - View - ##########################
				$prooptions .="<button type=\"button\" onclick=\"javascript:quickview('../Episode/quickview.php?args=".addslashes($subrow['programname'])."@".$subrow['date']."@".$subrow['starttime']."@".$subrow['callsign']."')\">View</button>";
				$prooptions .= "</td></tr>
				";
				++$SUBCON;
			}
		}
		else{
		$prooptions.="<td align=center>" . $row['date'] . "</td><td align=center>" . $row['starttime'] . "</td><td align=center>" . $rowcount . "</td><td>";
		$subrow=mysql_fetch_array($episodes);
		$prooptions .="<button type=\"button\" onclick=\"javascript:quickview('../Episode/quickview.php?args=".addslashes($subrow['programname'])."@".$subrow['date']."@".$subrow['starttime']."@".$subrow['callsign']."')\">View</button>";				
        $prooptions.="</td></tr>
        ";	
		}
		++$CONTROL;
    }
	$prooptions .= "</tr>
	"
?>

<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../altstyle.css" />
<title>DPL Administration</title>
</head>
<html>
<body>
	<script>
	function showsub(element) {
		document.getElementById(element).disabled=true;
		var xyz = document.getElementsByClassName(element);
		for(var i = 0; i <xyz.length;i++){
			xyz[i].style.display="table-row";
		}
	} 
	
	function quickview(url){
		//use @ to differentiate
		newwindow=window.open(url,'name','height=800,width=800');
		if (window.focus) {newwindow.focus()}
		return false;		
	}
	</script>
	
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
		<table>
			<tr>
				<th colspan="2" width="30%">Program Name</th>
				<th width="10%">Exclude</th>
				<th width="10%">Date</th>
				<th width="30%">Time</th>
				<th width="30%">Logs</th>
				<th width="20%">Details</th>
			</tr>
			<?php echo $prooptions; ?>
		</table>
		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
					<input type="text" hidden="true" name="from" value="<?php echo $_POST['from'] ?>" />
					<input type="text" hidden="true" name="to" value="<?php echo $_POST['to'] ?>" />
					<input type="text" hidden="true" name="limit" value="<?php echo $_POST['limit'] ?>" />
				<input type="submit" value="Submit"/></form></td><td>
				<input type="button" value="Reset" onClick="window.location.reload()"></td><td>
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