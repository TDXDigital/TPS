<script type="text/javascript">
	var edit = false;

	function SetRem(chk, ID , ROW , COUNT) {
		if(chk == true){
			document.getElementById(ID).style.background = 'red';	
		}
		else{
			//alert('UNCHECK')
			if(COUNT%2){
				document.getElementById(ID).style.background = '#DAFFFF';//'#F9F9AA';
			}
			else{
				document.getElementById(ID).style.background = 'white';
			}
		}
	}
	
	function SetEdit(Row){
		//alert(Row);
		//document.forms['general'].Row.checked="true";
		edit=true;
		document.getElementById(Row).checked="true";
	}
	
	function SetNote(ELID,EDI){
		//var VAL = document.getElementById(ELID).value;
		//alert(document.getElementById(ELID).value)
		document.getElementById(EDI).checked="true";
		var NOTE = prompt("Notes for individual song (90 char Max)", document.getElementById(ELID).value );
		if(NOTE != null){
			document.getElementById(ELID).value = NOTE;
		}			
	}
	
	$(function() {
	$('#collector').block({
		message: '<h3>Loading</h3>', 
        css: { border: '3px solid #a00' } 
	});
	
	function sendList(){
		$('#list').block();
		$.blockUI();
	}
	
});
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
	$result=mysql_query("SELECT * FROM program WHERE programname='{$SHOW}' and callsign='{$CALL}'",$con);
	$vars=mysql_fetch_array($result);
	$TitleFirst=FALSE;
}
else{
	echo 'ERROR!';
}
?>
<form accept-charset="UTF-8" method="POST" action="AJAX/p3update.php" onsubmit="sendlist()"> 
	<div id="subdiv" style="width: auto; text-align: right; margin-top: 10px;">
		<input type="submit" value="Save Edits"/><!--<button value="reset" disabled value="reset"/>--></button>
	</div>
<table border="0" class="tablecss">
    <tr>
				<th width="50px">
					<img src="Images/infoSmall.png" alt="Ch" />
				</th>
				<th width="200px">
					Type
				</th>
				<th width="75px">
					Playlist
				</th>
				<th width="75px">
					Spoken
				</th>
				<th width="75px">
					Time
				</th>
				<?php
				if($TitleFirst){
					echo "<th width=\"150px\">
								Title
							</th>
							<th width=\"150px\">
								Artist
							</th>";
				}
				else{
					echo "<th width=\"150px\">
								Artist
							</th>
							<th width=\"150px\">
								Title
							</th>";
				}
				?>
				
				<th width="150px">
					Album
				</th>
				<th width="150px">
					Composer
				</th>
				<th width="25px">CC</th>
				<th width="25px">Hit</th>
				<th width="25px">Ins</th>
				<th>Language</th>
				<th wifth="50px">Note</th>
				<th width="75px">
					<img src="Images/errorSmall.png" alt="Del" />
				</th>
			</tr>
			

<?php
	$FETSON = "SELECT * from SONG where programname='" . $SHOW . "' and date='" . $DATE . "' and starttime='" . $START . "' and callsign='" . $CALL ."' order by time ".$vars['displayorder'];
	//ho $FETSON; // sql qUEREY pRINT oUT
	
	//echo $FETSON; //DEBUG USE ONLY
	if(!$SONRES = mysql_query($FETSON))
	{
		echo "FETCH ERROR: Could not Fetch Songs performed, Server Returned (".mysql_errno().": ".mysql_error().") <br/><br/>SQL:";
		echo $FETSON;
	}
	else{
		$CONT = 0;
		while($SONGS = mysql_fetch_array($SONRES)){
			echo "<tr id=\"" . $SONGS['songid'] . "\"";
			if($CONT%2){
				echo " class='listrow' ";
			}
            else{
                echo " class='listrowalt' ";
            }
			echo" ><td><input type=\"text\" value=\"".$SONGS['songid']."\" hidden name=\"SNID[]\" /><input type=\"checkbox\" name=\"edit[]\" id=\"EDI".$CONT."\" value=\"" . $CONT . "\" title=\"Checked if row is modified\" onclick=\"javascript:return false\" /></td>";
			// CATEGORY HANDLER [TANSFERED]
			$OPT = "<td><select name=\"category[]\" "; 
			if($SONGS['category']=="51"){ //|| $SONGS['category']=="52" || $SONGS['category']=="53"))
				$OPT .= " disabled=\"disabled\" ><option>51 , Commercial</option></select><input type=\"hidden\" name=\"category[]\" value=\"".$SONGS['category']."\" /></td>";
				echo $OPT;
			}
			else{
				$OPT .= "onchange=\"SetEdit('EDI".$CONT."')\"  onclick=\"SetEdit('EDI".$CONT."')\">
				";
									$OPT .= "<OPTION value=\"53\"";
									if($SONGS['category']=="53")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">53, Sponsored Promotion</option>";
									
									$OPT .= "<OPTION value=\"52\"";
									if($SONGS['category']=="52")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">52, Sponsor Indentification</OPTION>";
									
									$OPT .= "<OPTION value=\"51\"";
									if($SONGS['category']=="51")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">51, Commercial</option>";
									
									$OPT .= "<OPTION value=\"45\"";
									if($SONGS['category']=="45")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= "> 45, Show Promo</option>";
									
									$OPT .= "<OPTION value=\"44\"";
									if($SONGS['category']=="44")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">44, Programmer/Show ID</option>";
									
									
									$OPT .= "<OPTION value=\"43\"";
									if($SONGS['category']=="43")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">43, Station ID</option>";
									
									$OPT .= "<OPTION value=\"42\"";
									if($SONGS['category']=="42")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">42, Tech Test</option>";
									
									$OPT .= "<OPTION value=\"41\"";
									if($SONGS['category']=="41")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">41, Themes</option>";
									
									/*$OPT .= "<OPTION value=\"40\"";
									if($list['category']=="40")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">40, Musical Production</option>";
									*/
									
									// CATEGORY 3 ---------------------------------------
									$OPT .= "<option value=\"36\"";
									if($SONGS['category']=="36")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">36, Experimental</option>";
									
	                                $OPT .= "<option value=\"35\"";
									if($SONGS['category']=="35")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">35, NonClassical Religious</option>";
									
	                                $OPT .= "<option value=\"34\"";
									if($SONGS['category']=="34")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">34, Jazz and Blues</option>";
									
	                                $OPT .= "<option value=\"33\"";
									if($SONGS['category']=="33")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">33, World/International</option>";
									
	                                $OPT .= "<option value=\"32\"";
									if($SONGS['category']=="32")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">32, Folk</option>";
									
	                                $OPT .= "<option value=\"31\"";
									if($SONGS['category']=="31")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">31, Concert</option>";
									
									// CATEGORY 2 ---------------------------------------
									if($SONGS['category']=="3"){
										$OPT .= "<OPTION value=\"3\" selected=\"true\" >3, Special Interest</option>";
									}
									
									$OPT .= "<OPTION value=\"24\"";
									if($SONGS['category']=="24")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">24, Easy Listening</option>";
									
									$OPT .= "<OPTION value=\"23\"";
									if($SONGS['category']=="23")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">23, Acoustic</option>";
									
									$OPT .= "<OPTION value=\"22\"";
									if($SONGS['category']=="22")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">22, Country</option>";
									
									$OPT .= "<OPTION value=\"21\"";
									if($SONGS['category']=="21")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">21, Pop, Rock and Dance</option>";
									
									if($SONGS['category']=="2"){
										$OPT .= "<OPTION value=\"2\" selected=\"true\" >2, Popular Music</option>";
									}
									
									$OPT .= "<OPTION value=\"12\"";
									if($SONGS['category']=="12")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">12, Spoken Word Other</option>";
									
									$OPT .= "<OPTION value=\"11\"";
									if($SONGS['category']=="11")
									{
										$OPT .= "selected=\"true\" ";
									}
									$OPT .= ">11, News</option>";
									
									$OPT .= "</select>";
									
	                                echo $OPT . "</td>";
	                  }
			
			echo "<td><input onfocus=\"SetEdit('EDI".$CONT."')\" type=\"text\" min=\"1\" size=\"1\" name=\"Playlist[]\" style=\"width: 30px\" value=\"" . $SONGS['playlistnumber'] . "\" /></td>";
			echo "<td><input onfocus=\"SetEdit('EDI".$CONT."')\"  type=\"text\" min=\"0\" size=\"1\" name=\"Spoken[]\" step=\"0.25\" style=\"width: 30px\" value=\"" . $SONGS['Spoken'] . "\" /></td>";
			$timeraw = $SONGS['time'];
			$time_12 = to12Hour($timeraw);
			echo "<td><input onfocus=\"SetEdit('EDI".$CONT."')\"  type=\"text\" size=\"5\" name=\"times[]\" style=\"width: 65px\" value=\"" . $time_12 ."\" /> </td>";
			if($TitleFirst==TRUE){
				echo "<td><input ";
				if($SONGS['category']=="51"){ //|| $SONGS['category']=="52" || $SONGS['category']=="53"))
					echo " readonly=\"readonly\" ";
				}
				echo "onfocus=\"SetEdit('EDI".$CONT."')\"   required type=\"text\" name=\"titles[]\" value=\"" . $SONGS['title'] . "\" maxlength=\"90\" /> </td>";
				echo "<td><input ";
				if($SONGS['category']=="51"){ //|| $SONGS['category']=="52" || $SONGS['category']=="53"))
					echo " readonly=\"readonly\" ";
				}
				echo "onfocus=\"SetEdit('EDI".$CONT."')\" type=\"text\" name=\"artists[]\" value=\"" . $SONGS['artist'] . "\" maxlength=\"90\" /> </td>";
			}
			else{
				echo "<td><input ";
				if($SONGS['category']=="51"){ //|| $SONGS['category']=="52" || $SONGS['category']=="53"))
					echo " readonly=\"readonly\" ";
				}
				echo "onfocus=\"SetEdit('EDI".$CONT."')\" type=\"text\" name=\"artists[]\" value=\"" . htmlspecialchars($SONGS['artist']) . "\" maxlength=\"90\" /> </td>";
				echo "<td><input ";
				if($SONGS['category']=="51"){ //|| $SONGS['category']=="52" || $SONGS['category']=="53"))
					echo " readonly=\"readonly\" ";
				}
				echo "onfocus=\"SetEdit('EDI".$CONT."')\"   required type=\"text\" name=\"titles[]\" value=\"" . htmlspecialchars($SONGS['title']) . "\" maxlength=\"90\" /> </td>";
			}
			echo "<td><input ";
				if($SONGS['category']=="51"){ //|| $SONGS['category']=="52" || $SONGS['category']=="53"))
					echo " readonly=\"readonly\" ";
				}
			echo "onfocus=\"SetEdit('EDI".$CONT."')\" type=\"text\" name=\"albums[]\" value=\"" . htmlspecialchars($SONGS['album']) . "\" maxlength=\"90\" /> </td>";
			echo "<td><input ";
			if($SONGS['category']=="51"){ //|| $SONGS['category']=="52" || $SONGS['category']=="53"))
				echo " readonly=\"readonly\" ";
			}
			echo "onchange=\"SetEdit('EDI".$CONT."')\" type=\"text\" name=\"composers[]\" value=\"" . $SONGS['composer'] . "\" maxlength=\"90\" /> </td>";
			echo "<input onfocus=\"SetEdit('EDI".$CONT.",EDI".$CONT."')\" type=\"text\" hidden name=\"note[]\" id=\"NTI".$CONT."\" value='".$SONGS['note']."' />";
			echo "<td><input onclick=\"SetEdit('EDI".$CONT."')\" onfocus=\"SetEdit('EDI".$CONT."')\" type=\"checkbox\" name=\"cc[]\" value='".$SONGS['songid']."' ";
			
			if( $SONGS['cancon'] == "1"){
				echo " checked ";
			}
			echo "/></td> ";
			echo "<td><input onclick=\"SetEdit('EDI".$CONT."')\" onfocus=\"SetEdit('EDI".$CONT."')\" type=\"checkbox\" name=\"hit[]\" value='".$SONGS['songid']."' ";
			if( $SONGS['hit'] == "1"){
				echo " checked ";
			}
			echo "/></td> ";
			echo "<td><input onclick=\"SetEdit('EDI".$CONT."')\" onfocus=\"SetEdit('EDI".$CONT."')\" type=\"checkbox\" name=\"ins[]\" value='".$SONGS['songid']."' ";
			if( $SONGS['instrumental'] == "1"){
				echo " checked ";
			}
			echo "/></td> ";
			$LANS = mysql_fetch_array(mysql_query("SELECT languageid from language where songid=\"" . $SONGS['songid'] . "\" "));
			echo "<td><input onclick=\"SetEdit('EDI".$CONT."')\" onfocus=\"SetEdit('EDI".$CONT."')\" style=\"width: auto;\" type=\"text\" name=\"language[]\" value=\"". $LANS['languageid'] . "\" size=\"10\" maxlength=\"40\" /></td>";
			echo "<td><input type=\"button\" value=\"";
				if($SONGS['note']!=''){
					echo 'Y';
				}
				else{
					echo "N";
				}
			echo "\" onclick=\"SetNote('NTI".$CONT."','EDI".$CONT."')\" /></td>";
			echo "<td><input type=\"checkbox\" value=\"".$SONGS['songid']."\" id=\"checkbox".$SONGS['songid']."\" name=\"remove[]\" onClick=\"SetRem(this.checked,".$SONGS['songid']." ,checkbox".$SONGS['songid'].",".$CONT.")\" /></td>";			
			echo "</tr>
			";
			++$CONT;
		}
		
	}
?>
</table>
</form>
<script>
	
</script>