<script>
	/*$(this).ready(function(){
		$('#collector').slideToggle();
	});
	$(this).unload(function(){
		
		$('#collector').slideToggle();
	})*/
	$( "#ColComTime" ).timespinner();
	/*$.ajaxStart(function(){
		$('#ads').hide();
	});*/

	//$('#ColComForm1').submit( 
	function subFormColCom(){
		var time = $("input#ColComTime").val();
		var adnum = $("#adbox option:selected").val();
		var DataString = 'adnum=' + adnum + '&time=' + time + '&title=' + adnum + '&type=51&artist=CKXU&album=ADVERTISEMENT'
		//alert("CALLED");
		$.ajax({
	  		url: "AJAX/components/PostSong.php",
	  		type: "POST",
	  		data: DataString,
	  		success: function(data) {
	    		if(data.length>0){
	    			growl(data);
  					closeSubmit();
	    		}
	    		else{
	    			closeSubmit();
	    		}
	  		},
	  		 error: function(data){
	  		 	//growl(data);
	  		 	growl("<p style='color: cyan; text-align: center'>Could not submit Commercial, Server responded with:<br/><br/>Error "+data.status+"</p>");
	  		 }
  		});
  		return false;
		//var lang = $("input#lang").val();
	}
	function player(){
		var datastring = "adnum="+$('ColComForm1#adbox').val();
		$.ajax({
			url: "Player/PlayerAJAX.php",
			type: "GET",
			date: datastring,
			success: function(data){
				$('#player').html(data);
				$('#player').show();
			},
			error: function(data){
				$('#player').html("A Error "+data.status+" has occured");
				$('#player').show();
				//$('#player').slideDown();
			}
		});
		return false;
	}
</script>
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
	$SETW = "1000px";
	
	// FETCH UNIVERSAL POST VALUES
	if(isset($_SESSION['program'])){
		$SHOW = addslashes($_SESSION['program']);
	}
	else{
		$SHOW = "NULL";
	}
	
	if(isset($_SESSION['time'])){
		$START = addslashes($_SESSION['time']);
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
}
else{
	echo 'ERROR!';
}?>
<div id="ads">
	<form id="ColComForm1" onsubmit="return subFormColCom();">
	<label for="adbox">Available Ads&nbsp;</label>
	<select name="adbox" id="adbox">
		<?php
		$REQAD_SQL = "SELECT adverts.*,adrotation.* FROM adrotation,addays,adverts 
		WHERE '".date('H:i').":00' BETWEEN adrotation.startTime AND adrotation.endTime 
		AND addays.AdIdRef=adrotation.RotationNum AND adrotation.AdId=adverts.AdId 
		AND addays.Day='".date('l')."' AND adverts.active='1' 
		AND '".date('Y-m-d')."' BETWEEN adverts.StartDate AND adverts.EndDate";
		
		$RQADSIDS = array();
		$REQAD = "";
		if(!$READS = mysql_query($REQAD_SQL))
		{
			$REQAD .= "<option value='-1'>ERROR - AdRotation</option>";
		}
		/*else if(mysql_num_rows($READS)==0){
			$REQAD .= "<option value='-1'>No Paid Commercials</option>";
		}*/
		else{
			while($PdAds=mysql_fetch_array($READS)){
				if($PdAds['Limit'] == NULL || $PdAds['Playcount'] < $PdAds['Limit']){
					// Check BlockLimit
					$CHECKBLIM = "SELECT count(song.songid), song.songid FROM adrotation,song WHERE adrotation.AdId='".$PdAds['AdId']."' 
					AND song.title='".$PdAds['AdName']."' and song.date='".$DATE."' and song.time 
					BETWEEN '".$PdAds['startTime']."' AND '".$PdAds['endTime']."' ";
					echo "<!-- SQL: ". $CHECKBLIM . " -->";
					$BL_lim_R = mysql_query($CHECKBLIM);
					$BL_lim = mysql_fetch_array($BL_lim_R);
					if(mysql_error()){
						echo "<option value='-3'>ERROR SQL</option>";
					}
					if($BL_lim['count(song.songid)']<$PdAds['BlockLimit']){
						//echo "<option value='-2'>BL_Lim:".$BL_lim['count(song.songid)']."</option>";
						$REQAD .= "<option value='".$PdAds['AdId']."'>".$PdAds['AdName']."</option>
						<!-- BlockLimit:".$PdAds['BlockLimit']."; BlockCount:".$BL_lim['count(song.songid)']."
						Adnum: ".$PdAds['AdId']."-->";
						array_push($RQADSIDS,$PdAds['AdId']);
					}
					else{
						echo "<!-- NC BlockLimit:".$PdAds['BlockLimit']."; BlockCount:".$BL_lim['count(song.songid)']."
						Adnum: ".$PdAds['AdId']."-->";
					}
				}
			}
			
		$ADIDS = array();
		if(isset($SPONS)){
			$ADOPT .= "<option value='".$SPONS['AdId']."'>".$SPONS['AdName']."</option>";
			array_push($ADIDS,$avadi['AdId']);
		}
		else{
			$selcom51 = "select * from adverts where Category='51' and '" . $_SESSION['date'] . "' between StartDate and EndDate and Friend='1' and Active='1' and Playcount=(select MIN(Playcount) from adverts where Category='51' and Active='1' and Friend='1') ";		
			if($comsav=mysql_query($selcom51)){
				$ADOPT = "";
				while($avadi = mysql_fetch_array($comsav)){
					$ADOPT .= "<option value=\"" . $avadi['AdId'] . "\">" . $avadi['AdName'] . "</option>";
					array_push($ADIDS,$avadi['AdId']);
				} 
			}
			else{
				$ADOPT = "<option value=\"-1\">ERROR - SQL Command</option>";
			}
		}
	}
	//echo "<option>Commercials</option>";
	if($REQAD==""){
		echo $ADOPT;
	}
	else{
		echo $REQAD;
	}
		
	?>
	</select>
	<input type="text" name="time" id="ColComTime" style="width: 80px;"value="<?php echo date("h:i A") ?>"/>
	<input type="submit" value="Submit"/>
	</form>
	<!--<form action="javascript:return false;" method="get" onsubmit="return player();">
		<input value="Load Ad File" type="submit"></button>
	</form>-->
</div>
