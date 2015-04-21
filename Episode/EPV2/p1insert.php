<?php
    include_once "../../TPSBIN/functions.php";
    //include_once "../../TPSBIN/db_connect.php";
    sec_session_start();
    //session_start();
    
    date_default_timezone_set($_SESSION['TimeZone']);

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	//die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . '; username=' . $_SESSION["username"]);
    header("location: /");
}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: /');}
        //Set TimeZone
        
	$GENRE = "SELECT * from GENRE order by genreid asc";
	$GENRES = mysql_query($GENRE,$con);
	$genop = "<OPTION VALUE=\"%\">Select Genre</option>";
	while ($genrerow=mysql_fetch_array($GENRES)) {
        $GENid=$genrerow["genreid"];
        $genop.="<OPTION VALUE=\"" . $GENid . "\">". $GENid ."</option>";
    }
	$djsql="SELECT * from DJ order by djname";
    $djresult=mysql_query($djsql,$con);

    $djoptions="<option value=0>Any</option>";//<OPTION VALUE=0>Choose</option>";
    while ($djrow=mysql_fetch_array($djresult)) {
        $Alias=$djrow["Alias"];
        $name=$djrow["djname"];
        $djoptions.="<OPTION VALUE=\"".$Alias."\">" . $name . "</option>";
    }
?>

<!DOCTYPE HTML>
<html>
<head>
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />
    <link href="../../js/chosen.min.css" rel="stylesheet" type="text/css"/>
    
<link rel="stylesheet" type="text/css" href="../../altstyle.css" />
<title>New DPL</title>
</head>
<body>
	<div class="topbar">
           User: <?php echo(strtoupper($_SESSION['fname'])); ?>
    </div>
	<div id="header">
		<a href="#"><img src="../../images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
	</div>
	<div id="top">
		<h2>New Program Log</h2>
	</div>
	<div id="content">
		<form name="search" method="POST" action="../p2insertEP.php">
		<table border="0" class="tablecss">
			<tr>
				<th id="s1" style="background-color:#CCFFFF">
					Program
				</th>
				<th style="width: 80px" id="s2">
					Callsign
				</th>
				<th id="s3">
					Broadcast Type
				</th>
				<th id="s4">
					Record Date
				</th>
				<th id="s5">
					Air Date
				</th>
				<th id="s6">
					Air Time (Not Record)
				</th>
				<th style="width:325px" id="s7">
					Description
				</th>
				
			</tr>
			<tr>
				<td>
					<select required class="chosen-select" title="Show Name" name="program" id="shownamebox" onchange="getCallsign(this.form.program.value)">
					<?php
					//<input name="name" type="text" size="25%"/>
					$program = "select programname from program where active='1' order by programname";
        			$prog=mysql_query($program,$con);
			        $options="<OPTION VALUE=0>Select Your Show [REQUIRED]</option>";
			        while ($row=mysql_fetch_array($prog)) {
			            $name=$row["programname"];
			//            $callsign=$row["callsign"];
			//            $alias=$row["Alias"];
			            $options.="<OPTION VALUE=\"".addslashes($name)."\">".$name."</option>";
        				}
					echo $options;
					?>
					</select>
				</td>
				<td>
					<!--<input name="callsign" type="text" id="callbox_old" readonly="readonly" />-->
					<select id="callbox" name="callsign">
						<option value="0">None Set</option>
					</select>
				</td>
				<td>
					<select name="brType" id="brType" disabled onchange="RecVer(this.form.brType.value)">
						<!--<option value="-1">Select</option>-->
						<option value="0">Live to Air</option>
						<option value="1">Pre Record</option>
						<option value="2">Timeless</option>
					</select>
				</td>
				<td>
					<input name="prdate" type="date" id="prdate" disabled/>
					
				</td>
				<td>
					<input name="user_date" type="date" id="airdate" disabled value="<?php echo date('Y-m-d'); ?>"/>
				</td>
				<td>
					<input name="user_time" type="time" id="airtime" disabled value="<?php echo date('H') . ":00"; ?>"/>
				</td>
				<td>
					<input name="Description" style="width:99%" type="text" maxlength="90" /> 
				</td>
			</tr>
			<!--<tr><th>Description</th><td colspan="5"><input type="text" size="100%" name="description"/></td></tr>-->
		</table>
		
		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
				<input type="submit" id="SM" value="Submit" disabled="disabled"/></form></td><td>
				<button onClick="window.location.reload()">Reset</button></td><td>
				<form method="POST" action="../../masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td style="width: 100%; text-align: right;"><img src="../../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>
	<div id="content">
			<h4>Help</h4>
		<span>If you are doing a PreRecord or Timeless you must select a Record Date. <br/> Callsign (Station) will be retrieved based on show selected</span>
                <span>TimeZone: <?php echo date_default_timezone_get(); echo " : Stored(".$_SESSION['TimeZone'].")";?></span>
	</div>
	
<?php

}
else{
	echo 'ERROR!';
}
?>
    
    <script src="../../js/jquery/js/jquery-2.1.1.min.js"></script>
    <script src="../../js/chosen.jquery.min.js"></script>
    <script src="../../TPSBIN/JS/Episode/Create.js"></script>
</body>
</html>