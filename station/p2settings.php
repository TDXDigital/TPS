<?php
    session_start();
	$UPDATE = FALSE;

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["usr"]);
}
else if($con){
	if(strtoupper($_SESSION['usr'])!='PROGRAM'){
			die('ERROR PROHIBITED ACCESS');
	}
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../login.php');}
		if(isset($_POST['name'])){
			if(!mysql_query("Update station SET stationname='".addslashes($_POST['name'])."' where callsign='".addslashes($_POST['call'])."' ")){
				echo "NAME ERROR: " . mysql_error() . "</br>";
			}
		}
		if(isset($_POST['desi'])){
			if(!mysql_query("Update station SET Designation='".addslashes($_POST['desi'])."' where callsign='".addslashes($_POST['call'])."' ")){
				echo "DESIGNAION ERROR: " . mysql_error() . "</br>";
			}
		}
		if(isset($_POST['freq'])){
			if(!mysql_query("Update station SET frequency='".addslashes($_POST['freq'])."' where callsign='".addslashes($_POST['call'])."' ")){
				echo "FREQUENCY ERROR: " . mysql_error() . "</br>";
			}
		}
		if(isset($_POST['dirp'])){
			if(!mysql_query("Update station SET directorphone='".addslashes($_POST['dirp'])."' where callsign='".addslashes($_POST['call'])."' ")){
				echo "DIR-PHONE ERROR: " . mysql_error() . "</br>";
			}
		}
		if(isset($_POST['oarp'])){
			if(!mysql_query("Update station SET boothphone='".addslashes($_POST['oarp'])."' where callsign='".addslashes($_POST['call'])."' ")){
				echo "BOOTH-PHONE ERROR: " . mysql_error() . "</br>";
			}
		}
		if(isset($_POST['webs'])){
			if(!mysql_query("Update station SET website='".addslashes($_POST['webs'])."' where callsign='".addslashes($_POST['call'])."' ")){
				echo "WEB URL ERROR: " . mysql_error() . "</br>";
			}
		}
		if(isset($_POST['addr'])){
			if(!mysql_query("Update station SET address='".addslashes($_POST['addr'])."' where callsign='".addslashes($_POST['call'])."' ")){
				echo "ADDRESS ERROR: " . mysql_error() . " [Update station SET address='".addslashes($_POST['addr'])."' where callsign='".addslashes($_POST['call'])."' ]</br>";
			}
		}
		if(isset($_POST['GPPL'])){
			if(!mysql_query("Update station SET ST_PLLG='".addslashes($_POST['GPPL'])."' where callsign='".addslashes($_POST['call'])."' ")){
				echo mysql_error();
			}
		}
		if(isset($_POST['DefaultSort'])){
			if(!mysql_query("Update station SET ST_DefaultSort='".addslashes($_POST['DefaultSort'])."' where callsign='".addslashes($_POST['call'])."' ")){
				echo mysql_error();
			}
		}
		if(isset($_POST['DisCon'])){
			if(!mysql_query("Update station SET ST_DispCount='".addslashes($_POST['DisCon'])."' where callsign='".addslashes($_POST['call'])."' ")){
				echo mysql_error();
			}
		}
		if(isset($_POST['WarnColor'])){
			if(!mysql_query("Update station SET ST_ColorFail='".addslashes($_POST['WarnColor'])."' where callsign='".addslashes($_POST['call'])."' ")){
				echo mysql_error();
			}
		}
		if(isset($_POST['PassColor'])){
			if(!mysql_query("Update station SET ST_ColorPass='".addslashes($_POST['PassColor'])."' where callsign='".addslashes($_POST['call'])."' ")){
				echo mysql_error();
			}
		}
		if(isset($_POST['NoteColor'])){
			if(!mysql_query("Update station SET ST_ColorNote='".addslashes($_POST['NoteColor'])."' where callsign='".addslashes($_POST['call'])."' ")){
				echo mysql_error();
			}
		}
		if(isset($_POST['MGRP'])){
			if(!mysql_query("Update station SET managerphone='".addslashes($_POST['MGRP'])."' where callsign='".addslashes($_POST['call'])."' ")){
				echo mysql_error();
			}
		}
		$UPDATE = TRUE;
    }
else{
	echo 'ERROR!';
}

?>

<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../altstyle.css" />
<title>Settings</title>
</head>
<html>
<body>
	<div class="topbar">
           User: <?php echo(strtoupper($_SESSION['usr'])); ?>
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
				$SQL = "SELECT * FROM station where callsign=\"".addslashes($_POST['call'])."\" ";
				$ST = mysql_fetch_array(mysql_query($SQL));	
				
				//SIX COLS
				if($UPDATE==TRUE){
					echo "<tr><td colspan='100%' style=\"background-color:".$ST['ST_ColorNote']."; text-align:center;\"><strong>Updates Commited at " . date("h:i:s") . "</strong></td></tr>";
				}
				echo "<tr><td colspan=\"100%\" style=\"background-color:#FFE4C4;\"><h4>General Station Information / Contact Information</h4></td></tr>";
				echo "<tr><td><label for=\"STC\"/>Callsign</label></td><td><input type=\"textbox\" id=\"STC\" name=\"call\" value=\"".$ST['callsign']."\" size=\"5\" readonly=\"readonly\" /></td>";
				echo "<td><label for=\"STN\"/>Common Name</label></td><td><input type=\"textbox\" id=\"STN\" name=\"name\" size=\"25\" value=\"".$ST['stationname']."\" /></td>";
				echo "<td><label for=\"FRQ\"/>Frequency</label></td><td><input type=\"textbox\" id=\"FRQ\" name=\"freq\" size=\"20\" value=\"".$ST['frequency']."\" /></td></tr>";
				
				echo "<tr><td><label for=\"DIR\"/>Director(s) Phone</label></td><td><input type=\"tel\" id=\"DIR\" name=\"dirp\" value=\"".$ST['directorphone']."\" size=\"15\" /></td>";
				echo "<td><label for=\"OAR\"/>On Air Request Line</label></td><td><input type=\"tel\" id=\"OAR\" name=\"oarp\" size=\"15\" value=\"".$ST['boothphone']."\" /></td>";
				echo "<td><label for=\"FRQ\"/>Manager Phone</label></td><td><input type=\"tel\" id=\"MAN\" name=\"MGRP\" size=\"15\" value=\"".$ST['managerphone']."\" /></td></tr>";
				
				echo "<tr><td><label for=\"DES\"/>Designation</label></td><td><input type=\"text\" id=\"DES\" name=\"desi\" value=\"".$ST['Designation']."\" size=\"15\" /></td>";
				echo "<td><label for=\"WEB\"/>Website</label></td><td><input type=\"tel\" id=\"WEB\" name=\"webs\" size=\"15\" value=\"".$ST['website']."\" /></td>";
				echo "</tr>";
				
				echo "<tr><td><label for=\"ADR\"/>Address</label></td><td colspan=\"5\"><input type=\"text\" id=\"ADR\" name=\"addr\" value=\"".$ST['address']."\" maxlength=\"98\" size=\"115\" /></td></tr>";
				
				echo "<tr><td colspan=\"100%\" style=\"background-color:#FFE4C4;\"><h4>Programming Settings / Defaults</h4></td></tr>";
				echo "<tr><td><label for=\"GPP\"/>Group Playlist</label></td><td><select id=\"GPP\" name=\"GPPL\">";
					if($ST['ST_PLLG']=="1"){
						echo "<option value='0'>No</option><option value='1' selected >Yes</option>";
					}
					else{
						echo "<option value='0' selected >No</option><option value='1'>Yes</option>";
					}
				echo "</select></td>";
				echo "<td><label for=\"DLO\"/>Default List Order</label></td><td><select id=\"DLO\" name=\"DefaultSort\">";
					if($ST['ST_DefaultSort']=="ASC"){
						echo "<option value='DESC'>Descending</option><option value='ASC' selected >Ascending</option>";
					}
					else{
						echo "<option value='DESC' selected >Descending</option><option value='ASC' >Ascending</option>";
					}
				echo "</select></td>";
				echo "<td><label for=\"DCN\"/>Display Counters</label></td><td><select id=\"DCN\" name=\"DisCon\">";
					if($ST['ST_DispCount']=="1"){
						echo "<option value='0'>No</option><option value='1' selected >Yes</option>";
					}
					else{
						echo "<option value='0' selected >No</option><option value='1'>Yes</option>";
					}
				echo "</select></td>";
				echo "</tr>";
				
				echo "<td><label for=\"CLN\"/>Warning Color</label></td><td><input id=\"CLW\" type=\"color\" name=\"WarnColor\" value=\"".$ST['ST_ColorFail']."\"/></td>";
				echo "<td><label for=\"CLN\"/>Pass Color</label></td><td><input id=\"CLP\" type=\"color\" name=\"PassColor\" value=\"".$ST['ST_ColorPass']."\"/></td>";
				echo "<td><label for=\"CLN\"/>Note/Sponsor Color</label></td><td><input id=\"CLN\" type=\"color\" name=\"NoteColor\" value=\"".$ST['ST_ColorNote']."\"/></td>";
				echo "</tr>";
				
			?>
		</table>
		
		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
				<input type="submit" value="Submit" /></form></td><td>
				<input type="button" value="Reset" onClick="document.forms[0].reset()"></td><td>
				<form method="POST" action="../masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td width="100%" align="right"><img src="../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>
	<div id="content">
			<h4>Help</h4>
		<span>Default Settings often are not modified by users, Be Careful!</span>
		
	</div>
</body>
</html>