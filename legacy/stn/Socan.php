<?php
    session_start();

$con = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'], $_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysqli_error() . ';  

	username=' . $_SESSION["username"]);
}
/*else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../login.php');}
	
    }
else{
	echo 'ERROR!';
}
*/
?>

<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../../css/altstyle.css" />
    <link rel="stylesheet" type="text/css" href="../js/jquery/css/smoothness/jquery-ui-1.10.0.custom.min.js"/>
    <script src="../js/jquery/js/jquery-2.0.3.min.js" type="text/javascript"></script>
    <script src="../js/jquery/js/jquery-ui-1.10.0.custom.js" type="text/javascript"></script>
<title>Socan Audits</title>
</head>
    <body>

    </body>
</html>
    <!--
	<div class="topbar">
           User: <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="#"><img src="../<?php echo $_SESSION['logo']?>" alt="logo" /></a>
	</div>
	<div id="top">
		<h2>Socan / Resound Audits</h2>
	</div>
	<div id="content">
		<form name="search" method="POST" action="p2ReqAd.php">
		<table border="0" class="tablecss">
			<tr>
				<th>
					Start Date
				</th>
				<th>
					End Date
				</th>
				<th>
					Artist
				</th>
				
			</tr>
			<tr><td>
				
			</td></tr>
		</table>
		
		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
				<input type="submit" value="Insert"/></form></td><td>
				</td>
				
			</tr>
		</table>
	</div>
	<div id="content">
			<h4>Existing Audits</h4>
			<table border="0">
				<tr>
					<th>
						<span title="Global Audit Number">GAN</span>
					</th>
					<th>
						<span title="Determines if the audit is enabled or not, a Disabled audit will not appear regardless of other settings">Status</span>
					</th>
					<th>
						<span title="Makes it required for artists to be entered for all entries that are not category 5 or 4">Artist</span>
					</th>
					<th>
						<span title="Makes it required for composer to be entered for all entries that are not category 5 or 4">Composer</span>
					</th>
					<th>
						<span title="Makes it required for Album to be entered for all entries that are not category 5 or 4">Album</span>
					</th>
					<th>
						<span title="Specifies the Start date of the audit">Start</span>
					</th>
					<th>
						<span title="Specifies the End date of the audit">End</span>
					</th>
					<th>
						<span title="In the event of a audit including after hours broadcasting (00:00 - 06:00) enabled these restrictions for these programmers">After Hours</span>
					</th>
				</tr>
				<?php
					$COMSQ = "select * from socan";
					//CHECK FOR XREF 
					
					//END CHECK
					if($COMS = mysqli_query($COMSQ)){
						while($COM = mysqli_fetch_array($COMS)){
							echo "<tr><td>";
							echo $COM['AuditId'];
							echo "<input type=\"radio\" name=\"edit\" value=\"".$COM['AuditId']."\"/></td><td>";
							if($COM['Enabled']=='0'){
								echo "<span style=\"background-color:lightblue;\">Disabled</span>";	
							}
							else{
								echo "<span style=\"background-color:lightgreen;\">Enabled</span>";
							}
							echo "</td><td>";
							if($COM['RQArtist']=='0'){
								echo "<span style=\"background-color:lightblue;\">Not Required</span>";	
							}
							else{
								echo "<span style=\"background-color:lightgreen;\">Required</span>";
							}
							echo "</td><td>";
							if($COM['RQComposer']=='0'){
								echo "<span style=\"background-color:lightblue;\">Not Required</span>";	
							}
							else{
								echo "<span style=\"background-color:lightgreen;\">Required</span>";
								
							}
							echo "</td><td>";
							if($COM['RQAlbum']=='0'){
								echo "<span style=\"background-color:lightblue;\">Not Required</span>";	
							}
							else{
								echo "<span style=\"background-color:lightgreen;\">Required</span>";
							}
							echo "</td><td>";
							echo $COM['start'];
							echo "</td><td>";
							echo $COM['end'];
							echo "</td><td>";
							echo $COM['RQAfterHr'];
							echo "</td><td>";
							$DAYS_SQLQ = "select * from addays where AdIdRef = '".$COM['RotationNum']."' ";
							if(!$DAYS_QU = mysql_query($DAYS_SQLQ)){
								echo mysql_error();
							}
							else{
								while($DAY = mysql_fetch_array($DAYS_QU)){
									echo $DAY['Day'] . ", ";
								}
							}
							echo "</td></tr>";
						}
					}
					else{
						echo "<tr><td>ERROR:".mysql_error()."</td></tr>";
					}
				?>
			</table>
	</div>
	<div id="foot">
		<table>
			<tr>
				<td>
				<input type="submit" value="Edit"/></form></td><td>
				<input type="button" value="Reset" onClick="window.location.reload()"></td><td>
				<input type="button" value="Menu" onclick="window.location.href='../masterpage.php'" />
				</td>
				<td style="width: 100%; text-align:right;"><img src="../images/mysqls.png" alt="MySQL Powered"/></td>
                <?php
                    $COM->close();
                ?>
			</tr>
		</table>
	</div>
    </form>
</body>
</html>-->
