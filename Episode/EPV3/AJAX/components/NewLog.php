<?php
function to12hour($hour1){ 
	// 24-hour time to 12-hour time 
	return DATE("g:i a", STRTOTIME($hour1));
}
function to24hour($hour2){
	// 12-hour time to 24-hour time 
	return DATE("H:i", STRTOTIME($hour2));
}

	session_start();
	
	if(isset($_SESSION['callsign'])){
		$callsign = addslashes($_SESSION['callsign']);
	}
	else if(isset($_GET['callsign'])){
		$callsign = addslashes($_GET['callsign']);
	}
	else{
		$callsign = "CKXU";
	}
	
	$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
	$friends = array();
	if (!$con){
		die("<h2>Error " . mysql_errno() . "</h2><p>Could not establish connection to database. Authentication failed</p>");
	}
	else{
		if(!mysql_select_db("CKXU")){
			die("<h2>Error " . mysql_errno() . "</h2><p>User Access Error. Database refused connection</p>");
		}
		else{
			$programs = "SELECT programname FROM program WHERE active='1' and callsign='".$callsign."' order by programname asc";
			if(!$pgmlist = mysql_query($programs)){
				$fail=TRUE;
			}
			else{
				$fail=FALSE;
			}
			
		}
	}
	
?>
<!DOCTYPE HTML>
<head>
	
</head>
<html>
	<body>
		<div id="new100">
			<form action="NewLog_submit" method="get" accept-charset="utf-8">
				<p>
					<h3>New Program Log</h3>
				<select name="program" class="combobox">
					<?php
						if($fail==TRUE){
							echo "<option value='Error909'>SQL Error</option>";
							echo "<option value='Error909'>".mysql_error()."</option>";
						}
						else{
							echo "<option>Select Show</option>";
							while($row = mysql_fetch_array($pgmlist)){
								echo "<option value='".$row['programname']."'>".$row['programname']."</option>
								";
							}
						}
					?>
				</select>
				
				<input type="text" value="<?php echo date("h:i A") ?>" style="width:65px" name="pgm_time" id="pgm_time" title="Start Time" />
				<br/>
				<input type="text" value="<?php echo date("Y-m-d")?>" style="" name="pgm_date" id="id_date" title="Air Date" />
				</p>
		</div>
			<div style="text-align: right"><br/><hr><button onclick="javascript:$.unblockUI()" style="float: left">Cancel</button><input type="submit" value="Create"/></div>
		</form>
	</body>
</html>
<script>
	$(function(){
		$('#pgm_time').timespinner();
		$('#id_date' ).datepicker({
		      defaultDate: "today",
		      changeMonth: true,
		      changeYear: true,
		      dateFormat: "yy-mm-dd",
		      showButtonPanel: true,
		      numberOfMonths: 1,
		    });
	});
</script>