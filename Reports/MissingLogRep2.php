<?php
    session_start();

$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db("CKXU")){header('Location: /login.php');} 
    //$prosql="SELECT Program.* FROM Program LEFT JOIN Episode ON Program.programname = Episode.programname WHERE Episode.programname IS NULL and Episode.date between '".$_POST['from']."' and '".$_POST['to']."' and program.active='1' ";
    $prosql="SELECT Program.* From Program where active='1' and not exists (select Episode.programname from Episode where date between '".addslashes($_POST['from'])."' and '".addslashes($_POST['to'])."' and Episode.programname=Program.programname) order by Program.programname";
    if(!$proresult=mysql_query($prosql,$con)){
    	$ERRORM=mysql_error();
		//echo mysql_error();
    }
	else{
		//$prooptions="<span>".mysql_num_rows($proresult)."</span>";
	    //$prooptions.="<form action=\"/Reports/MissingLogRep3.php\" method=\"POST\">
	    //";
	    $prooptions="";
		$rownum=0;
	    while($data=mysql_fetch_array($proresult)){
	    	$prooptions.="<tr";
			if($rownum%2){
				$prooptions.=" style=\"background-color:yellow;\" ";
			}
	    	$prooptions.="><td>".$data['programname']."</td><td>".$data['length']."</td><td>N/A</td><td>".$data['active']."</td><td></td></tr>";
	    	++$rownum;
	    }
	}
?>

<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="/altstyle.css" />
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
		<a href="/masterpage.php"><img src="/images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
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
					<input type="text" hidden="true" name="from" value="<?php echo $_POST['from'] ?>" />
					<input type="text" hidden="true" name="to" value="<?php echo $_POST['to'] ?>" />
					<input type="text" hidden="true" name="limit" value="<?php echo $_POST['limit'] ?>" />
				<!--<input type="submit" value="Search"/></form></td><td>-->
				<input type="button" value="Reset" onClick="window.location.reload()"></td><td>
				<form method="POST" action="/masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td width="100%" align="right"><img src="/images/mysqls.png" alt="MySQL Powered"/></td>
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