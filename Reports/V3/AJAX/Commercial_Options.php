<?php
function url(){
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

// SET FRIEND OPTION
if(!isset($_POST['FRIEND'])){
		$FRIEND = '0';
}
elseif($_POST['FRIEND']='yes'){
	$FRIEND = '1';
}
else{
	$FRIEND = '1';
}

// SET Active Option
if(!isset($_POST['ACTIVE'])){
		$ACTIVE = '0';
}
elseif($_POST['ACTIVE']='yes'){
	$ACTIVE = '1';
}
else{
	$ACTIVE = '1';
}

// SET Statistics Option
if(!isset($_POST['STATISTICS'])){
		$STATISTICS = '0';
}
elseif($_POST['STATISTICS']='yes'){
	$STATISTICS = '1';
}
else{
	$STATISTICS = '1';
}

// SET Excel File Output Option
if(!isset($_POST['EXCEL'])){
		$EXCEL = FALSE;
}
elseif($_POST['EXCEL']='yes'){
	$EXCEL = TRUE;
}
else{
	$EXCEL = TRUE;
}
?>
<div style="float: left;">
	<span>Commercial Audit</span>
	<!--<form id="ComForm1" name="Commercial" method="get" action="<?php echo url(); ?>">-->
	<input type="hidden" id="commercialHidden" value="COM" name="RPT_TYPE">
	<span class="statictop-leftbar-nofloat">
		<label for="friends">Friends</label>
		<input id="friends" type="checkbox" <?php
		if($FRIEND){
			echo("checked");
		}
		?>
		name="FRIEND">
	</span>
	<span style="padding-left: 10px;">
		<label for="active">Active</label>
		<input id="active" type="checkbox" <?php
		if($ACTIVE){
			echo("checked");
		}
		?> name="ACTIVE">		
	</span>
	<span style="padding-left: 10px;">
		<label for="statistics">Statistics</label>
		<input id="statistics" disabled type="checkbox" <?php
		if($STATISTICS){
			echo("checked");
		}
		?> name="STATISTICS">
		<!--<input id="statisticshidden" type="hidden" value="no" name="STATISTICS">-->
	</span>
	<span style="padding-left: 10px;">
		<label for="excel">Excel</label>
		<input id="excel" disabled type="checkbox" <?php
		if($EXCEL){
			echo("checked");
		}
		?> name="EXCEL">
		<!--<input id="statisticshidden" type="hidden" value="no" name="STATISTICS">-->
	</span>
	<span style="padding-left: 5px;">
		<!--<label for="adname">Ad Name</label>-->
		<input id="adname" type="text" maxlength="30" value="<?php
			echo $_POST['AD_NAME'];
		?>" name="AD_NAME" placeholder="Advertisement Name"/>
	</span>
	<span style="padding-left: 5px;">
		<select name="ADID">
			<option value="REFINE">- Update Selection -</option>
			<option value="CURRENT">- Current Options -</option>
			<?php
				$querylink = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'], $_SESSION['DBNAME']);
				if(mysqli_connect_error()){
					echo "<option value='null'>- CONNECTION ERROR -</option>";
				}
				else{
					$SQL = "SELECT `AdName`,`AdId` FROM `Adverts` WHERE";
					// APPEND OPTIONS
					// APPEND FRIEND BOOL
					if($FRIEND){
						$SQL .= " `Friend` = '1' ";
					}
					else{
						$SQL .= " `Friend` = '0' ";
					}
					// APPEND ACTIVE BOOL
					if($ACTIVE){
						$SQL .= "AND `Active` = '1' ";
					}
					else{
						$SQL .= "AND `Active` = '0' ";
					}
					// APPEND NAME RESTRICTION
					$SQL .= " AND `AdName` LIKE '%".addslashes($_POST['AD_NAME'])."%'";
					if($options_result = mysqli_query($querylink,$SQL)){
						for($i=0;$i<mysqli_num_rows($options_result);$i++){
							$OPTIONS = mysqli_fetch_array($options_result, MYSQLI_ASSOC);
							echo "<option value='".$OPTIONS['AdId']."'>".htmlspecialchars($OPTIONS['AdName'])."</option>
							";
						}
					}
					else{
						echo "<option value='null'>".mysqli_error()."</option>";
					}
					if($DEBUG){
						echo "<option value='null'>$SQL</option>";
					}
				}
			?>
		</select>
	</span>
	<input type="submit"/>
	<!--</form>-->
</div>