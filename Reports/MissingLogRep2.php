<?php
date_default_timezone_set("UTC");
    session_start();
require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."TPSBIN/functions.php";
require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."TPSBIN/db_connect.php";
$limit = filter_input(INPUT_POST, "limit")?:1000;
$from = filter_input(INPUT_POST, "from")?:strptime("-1 week");
$to = filter_input(INPUT_POST, "to")?:strtotime("today");

    $prosql="SELECT `program`.* From `program` where active='1' and not exists (select episode.programname from episode
 where date between '".addslashes($from)."' and '".addslashes($to)."' and 
 episode.programname=program.programname) order by program.programname";
    if(!$proresult=$mysqli->query($prosql)){
    	$ERRORM=$mysqli->error();
    }
	else{
	    $prooptions="";
		$rownum=0;
	    while($data=mysqli_fetch_array($proresult)){
	    	$prooptions.="<tr";
			if($rownum%2){
				$prooptions.=" style=\"background-color:yellow;\" ";
			}
	    	$prooptions.="><td>".$data['programname']."</td><td>".$data['length'].
				"</td><td>N/A</td><td>".$data['active']."</td><td></td></tr>";
	    	++$rownum;
	    }
	}
?>

<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../css/altstyle.css" />
<title>Missing Log Report</title>
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
		<img src="../<?php echo $_SESSION['logo'];?>" alt="logo" />
	</div>
	<div id="top">
		<h2>Missing Logs ['Alpha']</h2>
	</div>
	<div id="content">
		<table>
			<tr>
				<th width="400px">Program Name</th>
				<th width="100px">length (min)</th>
				<th width="500px">Programmers [INX]</th>
				<th width="100px">Active</th>
				<th width="inherit"></th>
				<!--<th width="10%">Exclude</th>-->
				<!--<th width="10%">Date</th>-->
				<!--<th width="30%">Time</th>-->
				<!--<th width="30%">Logs</th>-->
				<!--<th width="20%">Details</th>-->
			</tr>
			<?php
			if(!isset($ERRORM)){
				echo $prooptions;
			}
			else{
				echo "ERROR: ". $ERRORM;
			} ?>
		</table>
		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
					<input type="text" hidden="true" name="from" value="<?php echo $from ?>" />
					<input type="text" hidden="true" name="to" value="<?php echo $to ?>" />
					<input type="text" hidden="true" name="limit" value="<?php echo $limit ?>" />
				<input type="button" value="Reset" onClick="window.location.reload()"></td><td>
				<form method="POST" action="../"><input type="submit" value="Menu"/></form>
				</td>
				<td width="100%" align="right"><img src="../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>
</body>
</html>
