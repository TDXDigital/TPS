<?php
    session_start();
date_default_timezone_set($_SESSION['TimeZone']?:"UTC");

require_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."TPSBIN".DIRECTORY_SEPARATOR."functions.php";
require_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."TPSBIN".DIRECTORY_SEPARATOR."db_connect.php";
    $catop =  "<option value=\"53\">53, Sponsored Promotion</option>
	           <OPTION value=\"52\">52, Sponsor Indentification</OPTION>
	           <OPTION VALUE=\"51\" selected=\"true\">51, Commercial</OPTION>
	           <option value=\"45\">45, Show Promo</option>
	           <option value=\"44\">44, Programmer/Show ID</option>
	           <option value=\"43\">43, Station ID</option>
               <option value=\"12\">12P, PSA</option>";

	$POSTED = FALSE;
	if($_POST){
		$POSTED=TRUE;
		$INSad1 = "insert into adverts (";
		$INSad2 = ") values (";
		$append = false;
		if(isset($_POST['name'])){
			$append = TRUE;
			$INSad1 .= "AdName";
			$INSad2 .= "'".addslashes($_POST['name'])."'";
			$advertiser = $_POST['name'];
		}
		if(isset($_POST['category'])){
			if($append==TRUE){
				$INSad1 .= ", Category";
				$INSad2 .= ",'" . addslashes($_POST['category'])."'";
			}
			else{
				$append = TRUE;
				$INSad1 .= "category";
				$INSad2 .= "'".addslashes($_POST['category'])."'";
			}
		}
		if(isset($_POST['length'])){
			if($append==TRUE){
				$INSad1 .= ",Length";
				$INSad2 .= ",'" . addslashes($_POST['length'])."'";
			}
			else{
				$append = TRUE;
				$INSad1 .= "Length";
				$INSad2 .= "'".addslashes($_POST['length'])."'";
			}
			$length = $_POST['length'];
		}
		if(isset($_POST['language'])){
			if($append==TRUE){
				$INSad1 .= ", Language";
				$INSad2 .= ",'" . addslashes($_POST['language'])."'";
			}
			else{
				$append = TRUE;
				$INSad1 .= "Language";
				$INSad2 .= "'".addslashes($_POST['language'])."'";
			}
			$language = $_POST['language'];
		}
		if(isset($_POST['dstart'])){
			if($append==TRUE){
				$INSad1 .= ",StartDate";
				$INSad2 .= ",'" . addslashes($_POST['dstart'])."'";
			}
			else{
				$append = TRUE;
				$INSad1 .= "StartDate";
				$INSad2 .= "'".addslashes($_POST['dstart'])."'";
			}
			$startdate = $_POST['dstart'];
		}
		if($_POST['dend']!=""){
			if($append==TRUE){
				$INSad1 .= ",EndDate";
				$INSad2 .= ",'" . addslashes($_POST['dend'])."'";
			}
			else{
				$append = TRUE;
				$INSad1 .= "EndDate";
				$INSad2 .= "'".addslashes($_POST['dend'])."'";
			}
			$enddate = $_POST['dend'];
		}
		if(isset($_POST['active'])){
			if($append==TRUE){
				$INSad1 .= ", Active";
				$INSad2 .= ",'0'";
			}
			else{
				$append = TRUE;
				$INSad1 .= "Active";
				$INSad2 .= "'0'";
			}
			$active = TRUE;
		}
		else{
			if($append==TRUE){
				$INSad1 .= ",Active";
				$INSad2 .= ",'1'";
			}
			else{
				$append = TRUE;
				$INSad1 .= "Active";
				$INSad2 .= "'1'";
			}
			$active = false;
		}
		if(isset($_POST['Friend'])){
			if($append==TRUE){
				$INSad1 .= ", Friend";
				$INSad2 .= ",'1'";
			}
			else{
				$append = TRUE;
				$INSad1 .= "Friend";
				$INSad2 .= "'1'";
			}
			$friend = TRUE;
		}
		else{
			if($append==TRUE){
				$INSad1 .= ", Friend";
				$INSad2 .= ",'0'";
			}
			else{
				$append = TRUE;
				$INSad1 .= " Friend";
				$INSad2 .= "'0'";
			}
			$friend = false;
		}
		$result = $mysqli->query("select MIN(Playcount) from adverts where Category='".$mysqli->real_escape_string(
		    filter_input(INPUT_POST, 'category'))."'");
        $PLMIN = $result->fetch_array(MYSQLI_ASSOC);
		#$PLMIN = mysql_fetch_array(mysql_query("select MIN(Playcount) from adverts where Category='" . addslashes($_POST['category']) . "'"));
		$INSad = $INSad1 . $INSad2 . ")";
		if(!$inResult = $mysqli->query($INSad)){
			echo $mysqli->error;
			echo "<br/>";
			echo $INSad;
			$mysqli->insert_id;
		}
		else{
			$ADIDNUM=$mysqli->insert_id;
			if(!$mysqli->query("update adverts set Playcount=Playcount-".$PLMIN['MIN(Playcount)']." where Category='" .
                    $mysqli->real_escape_string(filter_input(INPUT_POST,"category")) . "'")){
				echo $mysqli->errno;
				echo "<br/>";
				echo $mysqli->error;
			}
		}
	}
?>

<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../../css/altstyle.css" />
<title>Advertisement Administration</title>
</head>
<html>
<body>
	<div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['fname'])); ?>
    </div>
	<div id="header">
		<a href="/"><img src="<?php print("../../".$_SESSION['logo']); ?>" alt="logo"/></a>
	</div>
	<div id="top">
		<h2>New Advertiser</h2>
	</div>
	<div id="content">
		<form name="search" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<table border="0" class="tablecss">
			<tr>
				<th>
					<a onclick='javascript: alert("Unique Identifier for a entered Advertisement. Generated by system")'>Ad Number</a>
				</th>
				<th>
					<a onclick='javascript: alert("Required Field\n\nDefines the name of the advertised, this is what programmers (DJs) will see when logging their show")'>Advertiser</a>
				</th>
				<th>
					<a onclick='javascript: alert("Required Field\n\nEnter the category that the advertisement falls within. If the ad is used within two categories there must be two separate entries")'>Category</a>
				</th>
				<th>
					<a onclick='javascript: alert("Required Field\n\nEnter the length of the Advertisement/Commercial in seconds \n\nRequired for Ad Time reporting")'>Length</a>
				</th>
				<th>
					<a onclick='javascript: alert("Defaults to English \n\nSpecifies the language that is used in the advertisement")'>Language</a>
				</th>
				<th>
					<a onclick='javascript: alert("Defaults to current Server Date \n\nEnter date you want this advertisement to become available \n\nNOTE: The ads will become available midnight of the selected date [00:00]")' >Start</a>
				</th>
				<th>
					<a onclick='javascript: alert("Leave blank to set no end date \n\nOtherwise enter date you want this advertisement end being available \n\nNOTE: The ads will run until midnight of the selected date [00:00]")' >End</a>
					<!--<a onclick='javascript: document.getElementByID("dends").selected=" "'>clear</a>-->

				</th>
				<th>
					<a onclick='javascript: alert("Defaults to English \n\nSpecifies if a advertisement is available for play (advertisement needs to be within available time and Active to be visible to programmers [DJs])")'>Active</a>
				</th>
				<th>
					<a onclick='javascript: alert("Defaults to English \n\nSpecifies if a advertisement is available for play (advertisement needs to be within available time and Active to be visible to programmers [DJs])")'>Friend</a>
				</th>
			</tr>
			<tr>
				<td>
					<input name="adnum" size="10" type="text" readonly="true" <?php if($POSTED==TRUE){
						echo " value=\"" . $ADIDNUM . "\" ";
					}
					else{
						echo " value=\"N/A\" ";
					}?> />
				</td>
				<td>
					<input name="name" type="text" size="25%" required="true" <?php
					if(isset($advertiser)){
						echo "value=\"".$advertiser."\" ";
					}
					?>/>
				</td>
				<td>
					<select name="category">
						<?php echo $catop;?>
					</select>
				</td>
				<td>
					<input name="length" type="number" maxlength="4" max="9999" min="0" size="8" value="30" required="true"/>
				</td>
				<td>
					<input name="language" type="text" maxlength="25" size="15" <?php
					if(isset($language)){
						echo "value=\"".$language."\" ";
					}
					else{
						echo "value=\"English\" ";
					}
					?>/>
				</td>
				<td>
					<input name="dstart" type="date" <?php
					if(isset($startdate)){
						echo "value=\"".$startdate."\" ";
					}
					else{
						echo "value=\"".date("Y-m-d")."\" ";
					}
					?> />
				</td>
				<td>
					<input name="dend" id="dends" type="date" <?php
					if(isset($enddate)){
						echo "value=\"".$enddate."\" ";
					}
					else{
						echo "value=\"\" ";
					}
					?>/>
				</td>
				<td>
					<input name="Active" type="checkbox" checked />
				</td>
				<td>
					<input name="Friend" type="checkbox" checked />
				</td>
			</tr>
		</table>

		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
				<?php if(!$POSTED){ echo "<input type=\"submit\" value=\"Submit\"/>";}?></form></td><td>
				<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"><input type="submit" value="Reset" /></form></td><td>
				<form method="POST" action="../../masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td width="100%" align="right"><img src="../../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>
	<div id="content">
			<h4>Help</h4>
		<span>Clicking the Title of the entry box will assist with a definition of the field as well if it is required and any defaults</span>

	</div>
</body>
</html>
