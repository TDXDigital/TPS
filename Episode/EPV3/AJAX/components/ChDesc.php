<?php
    session_start();
	
	function to12hour($hour1){ 
		// 24-hour time to 12-hour time 
		return DATE("g:i a", STRTOTIME($hour1));
	}
	function to24hour($hour2){
		// 12-hour time to 24-hour time 
		return DATE("H:i", STRTOTIME($hour2));
	}
	
$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db("CKXU")){/*header('Location: /login.php');*/}	

	// GLOBAL SETTINGS
	//$SETW = "1350px";
	
	// FETCH UNIVERSAL POST VALUES
	if(isset($_SESSION['program'])){
		$SHOW = addslashes($_SESSION['program']);
	}
	else{
		$SHOW = "NULL";
	}
	
	if(isset($_SESSION['time'])){
		$START = addslashes(to24Hour($_SESSION['time']));
	}
	else{
		$START = "00:00:00";
	}
	
	if(isset($_SESSION['date'])){
		$DATE = addslashes($_SESSION['date']);
	}
	else{
		$DATE = date("Y-m-d");
	}
	
	if(isset($_SESSION['callsign'])){
		$CALL = addslashes($_SESSION['callsign']);
	}
	else{
		$CALL = "NULL";
	}
	
	$query = "SELECT description FROM episode where programname='".$SHOW."' and date='".$DATE."' and starttime='".$time."' and callsign='".$CALL."' ";
	$result = mysql_query($query);
	$desc = mysql_fetch_array($result);
	
	mysql_close($con);
	//$con.close();
}
?>

<!DOCTYPE HTML>
<html>
	<head>
		
	<script type="text/javascript">
		function SubajaxLoad(){
			//$('#domScratch').html($('#domFetch').html());
		  	//$.blockUI({ message: $('#domScratch'), overlayCSS: {backgroundColor:'#f5f5f5'} });
		  	var dataString = "data=";
		  	var URL = "AJAX/components/ComDesc.php";
		  	$.ajax({
		  		url: URL,
		  		data: dataString,
		  		type: "POST",
		  		beforeSend: function(){
		  			$('.blockOverlay').attr('title','Click to Cancel').click($.unblockUI);
				  	$('#subcon301').hide();
				  	$('#subcon302').show();
				  	$('#subcon303').slideUp();
		  		},
		  		success: function(data) {
		    		//$('#subcon302').html(data);
		    		$.unblockUI();
		    		//alert('Load was performed.');
		  		},
		  		error: function(data) {
		  			$('#subcon301').show();
		  			$('#subcon300').html("<p>A Error Was Returned <button style='float: right' onclick='Detail303()'>Detail</button></p>");
		  			$('#subcon303').html("Header Response: "+data.status);
		  			$('#subcon300').slideDown();
		  			$('#subcon302').hide();
		  		}
		  		});
		  	//$.blockUI();
		  	return false;
		  }
		  function Detail303(){
		  	$('#subcon303').slideToggle();
		  }
		  function CancelSubmit(){
		  	$('#subcon301').show();
		  	//$('#subcon300').html("User canceled send");
		  	$('#subcon300').show();
		  	$('#subcon302').hide();
		  	return false;
		  }
	</script>
	</head>
	<body>
		<div id="subcon300" style="display: none; text-align:center; color: red;">
			<p>Undefined Error</p>
		</div>
		<div id="subcon303" style="display: none; text-align:left; color: blue;">
			<hr>
			<p>No Detail Is Available</p>
		</div>
		<div id="subcon301">
		<h2>Change Description</h2>
		<form onsubmit="return SubajaxLoad();">
			<input id="CHDfm1Des" name="Description" type="text" style="width:99%"/>
			<input type="submit"/>
			<input type="button" value="Cancel" onclick="$.unblockUI();" />
		</form>
		</div>
		<div id="subcon302" style="display: none;">
			<p>Setting Description...</p>
			<input type="button" value="Cancel" onclick="CancelSubmit()" />
		</div>
	</body>
</html>
