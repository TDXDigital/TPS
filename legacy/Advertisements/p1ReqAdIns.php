<?php
    session_start();

$con = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysqli_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysqli_select_db($con, $_SESSION['DBNAME'])){header('Location: ../login.php');}
	
    }
else{
	echo 'ERROR!';
}
	$adoptions_list="";
	
 	$ADS_SQL="select * from adverts where active='1'";
	// Capture Ad Transfer
	if(isset($_POST['AdNum'])){
		$ADS_SQL .= " and AdId='".addslashes($_POST['AdNum']) . "' ";
		$AdNum = addslashes($_POST['AdNum']);
	}
	else if(isset($_GET['AD'])){
		$ADS_SQL .= " and AdId='".addslashes($_GET['AD']) . "' ";
		$AdNum = addslashes($_GET['AD']);
	}
 	if(!$ADS = mysqli_query($con, $ADS_SQL)){
 		$adoptions_list .= "<option value='-1'>ERROR</option>";
 	}
	else{
		while($adop = mysqli_fetch_array($ADS)){
			$adoptions_list .= "<option value=\"".$adop['AdId']."\">".$adop['AdName']."</option>";
		}
	}
?>

<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../../css/altstyle.css" />
<title>Ad Rotation Maintenace</title>
 <!-- One script tag loads all the required libraries! Do not specify any chart types in the autoload statement. -->
    <script type="text/javascript"
        src='https://www.google.com/jsapi?autoload={"modules":[{"name":"visualization","version":"1"}]}'>
    </script>
<script type='text/javascript'>
function loadXMLDoc()
{
var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    return xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","AJAX/getReqData.php",true);
xmlhttp.send();
}
</script>
<script type='text/javascript'>
      google.load('visualization', '1', {packages:['table']});
      google.setOnLoadCallback(drawTable);
      function drawTable() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Hour');
        data.addColumn('string', 'Sunday');
        data.addColumn('string', 'Monday');
        data.addColumn('string', 'Tuesday');
        data.addColumn('string', 'Wednesday');
        data.addColumn('string', 'Thursday');
        data.addColumn('string', 'Friday');
        data.addColumn('string', 'Saturday');
        //data.addColumn('boolean', 'Full Time Employee');
        /*data.addRows([
          ['Jsmes',  {v: 10000, f: '$10,000'}, true],
          ['Jim',   {v:8000,   f: '$8,000'},  false],
          ['Alice', {v: 12500, f: '$12,500'}, true],
          ['Bob',   {v: 7000,  f: '$7,000'},  true]
        ]);*/
       //data.addRows(24);
       /*for(var i=0; i < 24 ; i++){
       	//data.setCell(i, 0, i);
       	loadXMLDoc();
       }*/
      	//loadXMLDoc();
      	data.addRows([
      	<?php
      	$properties = "";
      	$adID = $AdNum;//$GET("ID");
	for($i=0;$i<24;$i++){
		echo "[";
		if($i>9){
				echo "'".$i.":00'";
			}
			else{
				echo "'0".$i.":00'";
			}
		for($x = 0;$x<7;$x++){
			switch ($x){
				case 0:
					$day = "Sunday";
					break;
				case 1:
					$day = "Monday";
					break;
				case 2:
					$day = "Tuesday";
					break;
				case 3:
					$day = "Wednesday";
					break;
				case 4:
					$day = "Thursday";
					break;
				case 5:
					$day = "Friday";
					break;
				case 6:
					$day = "Saturday";
					break;
			}
			$sql = "SELECT count(adrotation.AdId),adrotation.RotationNum FROM adrotation,addays WHERE adrotation.AdId='".$adID."' AND '";
			if($i>9){
				$sql.=$i;
			}
			else{
				$sql.="0".$i;
			}
			$sql .= ":00:00' BETWEEN adrotation.startTime AND adrotation.endTime AND addays.AdIdRef=adrotation.RotationNum AND addays.Day='".$day."' ";
			$result=mysqli_query($sql);
			if(mysqli_error()){
				echo mysqli_error();
			}
			$var = mysqli_fetch_array($result);
			if($var["count(adrotation.AdId)"]=="0"){
				echo ", null";
			}
			else{
				echo ", 'GLRN:".$var["RotationNum"]."'";
				$properties .= "";
			}
		}
		echo "]";
		if($i<23){
			echo ", 
	";
		}
	}
      	?>]);

        var table = new google.visualization.Table(document.getElementById('table_div'));
        table.draw(data, {showRowNumber: false});
      }
    </script>
</head>
<html>
<body>
	<div class="topbar">
           User: <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="#"><img src="<?php print("../../".$_SESSION['logo']); ?>" alt="logo"/></a>
	</div>
	<div id="top">
		<h2>New Requirement</h2>
	</div>
	<div id="content">
		<form name="search" method="POST" action="p2ReqAd.php">
		<table border="0" class="tablecss">
			<tr><td>
				<label for="adnum">Ad</label>
				<select name="adnum" id="adnum">
					<?php
					 	echo $adoptions_list;
					?>
				</select>
			</td><td>
				<label for="limit">Hourly Limit</label>
				<input type="number" name="limit" step="1.0" min="0" max="999" size="5" alt="10"  autofocus="true" name="limit" id="limit" value="1"/>
			</td><td>
				<label for="BLM">Block Limit</label>
				<input type="number" name="BLM" step="1.0" min="0" max="999" size="5" alt="10"  autofocus="true" name="limit" id="limit" value="1"/>
			</td><td>
				<label for="limit">Start Time</label>	
				<input type="time" name="start" id="Start" required="required" value="06:00"/>
			</td><td>
				<label for="limit">End Time</label>
				<input type="time" name="end" id="end" required="required" value="23:59"/>
			</td></tr></table><table><tr><th colspan="100%" align="left">Days</th><tr><td>
				<label for="Mo">M</label>
				<input type="checkbox" name="day[]" id="Mo" value="Monday"/>
			</td><td>
				<label for="Tu">T</label>
				<input type="checkbox" name="day[]" id="Tu" value="Tuesday"/>
			</td><td>
				<label for="We">W</label>
				<input type="checkbox" name="day[]" id="We" value="Wednesday"/>
			</td><td>
				<label for="Th">R</label>
				<input type="checkbox" name="day[]" id="Th" value="Thursday"/>
			</td><td>
				<label for="Fr">F</label>
				<input type="checkbox" name="day[]" id="Fr" value="Friday"/>
			</td><td>
				<label for="Sa">Sa</label>
				<input type="checkbox" name="day[]" id="Sa" value="Saturday"/>
			</td><td>
				<label for="Su">Su</label>
				<input type="checkbox" name="day[]" id="Su" value="Sunday"/>	
			</td></tr>
		</table>
		
		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
				<input type="submit" value="Submit"/></td><td>
				
				</td>
				
			</tr>
		</table>
	</div>
	<div id="content">
			<h4 style="width:100%; background-color: blue; color:white;">Existing Requirements</h4>
			<table border="0">
				<tr>
					<th>
						<span title="Global List Requirement Number">GLRN</span>
					</th>
					<th>
						Com #
					</th>
					<th>
						Start Time
					</th>
					<th>
						End Time
					</th>
					<th>
						Hourly Limit
					</th>
					<th>
						Block Limit
					</th>
					<th>
						Days
					</th>
					<th>
						Delete
					</th>
				</tr>
				<!-- list Ads already in system-->
				<?php
					$COMSQ = "select * from adrotation where AdId='".$AdNum."'";
					//CHECK FOR XREF 
					
					//END CHECK
					if($COMS = mysqli_query($con, $COMSQ)){
						while($COM = mysqli_fetch_array($COMS)){
							echo "<tr><td>";
							echo $COM['RotationNum'];
							echo "<input type=\"radio\" name=\"edit\" value=\"".$COM['RotationNum']."\"/></td><td>";
							echo $COM['AdId'];
							echo "</td><td>";
							echo $COM['startTime'];
							echo "</td><td>";
							echo $COM['endTime'];
							echo "</td><td>";
							echo $COM['HourlyLimit'];
							echo "</td><td>";
							echo $COM['BlockLimit'];
							echo "</td><td>";
							$DAYS_SQLQ = "select * from addays where AdIdRef = '".$COM['RotationNum']."' ";
							if(!$DAYS_QU = mysqli_query($con, $DAYS_SQLQ)){
								echo mysqli_error();
							}
							else{
								while($DAY = mysqli_fetch_array($DAYS_QU)){
									echo $DAY['Day'] . ", ";
								}
							}
							echo "</td><td>";
							echo "<input type='checkbox' name='delete[]' value='".$COM['RotationNum']."' />"; 
							echo "</td></tr>";
						}
					}
					else{
						echo "<tr><td>ERROR:".mysqli_error()."</td></tr>";
					}
				?>
			</table>
	</div>
	<div id='table_div' style="width:1000px; margin: auto;"></div>
	<div id="foot">
		<table>
			<tr>
				<td>
				<input type="submit" value="Edit"/></form></td><td>
				<input type="button" value="Reset" onClick="window.location.reload()"></td><td>
				<form method="POST" action="../../masterpage.php"><input type="submit" value="Menu"/>
				</form>
				</td>
				<td width="100%" align="right"><img src="../../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>
</body>
</html>
