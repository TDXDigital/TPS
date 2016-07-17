<?php
    session_start();
?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../../css/altstyle.css" />
<title>Commercial Management</title>
</head>
<html>
<body>
	<div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="../../masterpage.php"><img src="<?php print("../../".$_SESSION['logo']); ?>" alt="logo"/></a>
	</div>
	<div id="top">
		<h2>Edit Commercial / Promo</h2>
	</div>
	<div id="content">
		<table border="0" class="tablecss">
			<!--<tr><td colspan="100%"><h2>*** Work in Progress ***</h2></td></tr>-->

				<th width="100px">
					Ad Name
				</th>
				<th width="100px">
					Category
				</th>
				<?php

				if(false){
					echo"<th width=\"50\">
					Ad Id
					</th>";
				}
				?>
				<th width="75px">
					Seconds
				</th>
				<th width="150px">
					Start Date
				</th>
				<th width="150px">
					End Date
				</th>
				<th width="50px">
					Active
				</th>
				<th width="50px">
					Friend
				</th>
				<th width="300px">
					X Reference
				</th>
			</tr>


<?php

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db("CKXU")){header('Location: /login.php');}

	if(isset($_POST['AdIdx'])){
		$AdId = addslashes($_POST['AdIdx']);
	}
	else if(isset($_POST['postval'])){
		$AdId = addslashes($_POST['postval']);
	}
	else if(isset($_GET['resource'])){
		$AdId = addslashes($_GET['resource']);
	}
	else{
		$AdId = addslashes($_POST['AdId']);
	}


	if(isset($_POST['changed'])){
		// UPDATE NAME
			$CHNA = "Update adverts SET AdName='" . addslashes($_POST['name']) . "' where AdId='" . $AdId . "' ";
			if(!mysql_query($CHNA)){
				if(mysql_errno()==1451)
				{
					echo "<span style=\"background-color:red; color:white;\"><strong>Error 1451</strong><br />Logs have been entered using this program name<br />
					You must change this program to inactive and enter a new show to change the name with the logs in the archive</span>";
				}
				else{
					echo mysql_errno() . "<br />". mysql_error();
				}
			}
			else{
				$AdName = addslashes($_POST['AdName']);
			}

			// UPDATE CATEGORY
			$CHCAT = "Update adverts SET Category='" . $_POST['category'] . "' where AdId='".$AdId."' ";
			if(!mysql_query($CHCAT)){
				if(mysql_errno()==1451)
				{
					echo "<span style=\"background-color:red; color:white;\"><strong>Error 1451</strong><br />Logs have been entered using this program name<br />
					You must change this program to inactive and enter a new show to change the name with the logs in the archive</span>";
				}
				else{
					echo mysql_errno() . "<br />". mysql_error();
				}
			}

			// UPDATE START DATE
			$CHSTA = "Update adverts SET StartDate='" . $_POST['start'] . "' where AdId='".$AdId."' ";
			if(!mysql_query($CHSTA)){
				if(mysql_errno()==1451)
				{
					echo "<span style=\"background-color:red; color:white;\"><strong>Error 1451</strong><br />Logs have been entered using this program name<br />
					You must change this program to inactive and enter a new show to change the name with the logs in the archive</span>";
				}
				else{
					echo mysql_errno() . "<br />". mysql_error();
				}
			}

			// UPDATE STARTTIME
			$CHEND = "Update adverts SET EndDate='" . $_POST['end'] . "' where AdId='".$AdId."' ";
			if(!mysql_query($CHEND)){
				if(mysql_errno()==1451)
				{
					echo "<span style=\"background-color:red; color:white;\"><strong>Error 1451</strong><br />Logs have been entered using this program name<br />
					You must change this program to inactive and enter a new show to change the name with the logs in the archive</span>";
				}
				else{
					echo mysql_errno() . "<br />". mysql_error();
				}
			}
			// UPDATE MAX PLAY
			//#############################################################
			// To Be Implemented Later [not in MySQL yet]


			// UPDATE LENGTH
			$CHLEN = "Update adverts SET Length='" . addslashes($_POST['Length']) . "' where AdId='" . $AdId . "' ";
			if(!mysql_query($CHLEN)){
				if(mysql_errno()==1451)
				{
					echo "<span style=\"background-color:red; color:white;\"><strong>Error 1451</strong><br />Logs have been entered using this program name<br />
					You must change this program to inactive and enter a new show to change the name with the logs in the archive</span>";
				}
				else{
					echo mysql_errno() . "<br />". mysql_error();
				}
			}

			// Update Friend Setting
			if(isset($_POST['Friend'])){
				$CHFRI = "Update adverts SET Friend='1' where AdId='" . $AdId . "' ";
			}
			else{
				$CHFRI = "Update adverts SET Friend='0' where AdId='" . $AdId . "' ";
			}
			if(!mysql_query($CHFRI)){
				if(mysql_errno()==1451)
				{
					echo "<span style=\"background-color:red; color:white;\"><strong>Error 1451</strong><br />Logs have been entered using this program name<br />
					You must change this program to inactive and enter a new show to change the name with the logs in the archive</span>";
				}
				else{
					echo mysql_errno() . "<br />". mysql_error();
				}
			}

			// Update Active Setting
			if(isset($_POST['Active'])){
				$CHACT = "Update adverts SET Active='1' where AdId='" . $AdId . "' ";
			}
			else{
				$CHACT = "Update adverts SET Active='0' where AdId='" . $AdId . "' ";
			}
			if(!mysql_query($CHACT)){
				if(mysql_errno()==1451)
				{
					echo "<span style=\"background-color:red; color:white;\"><strong>Error 1451</strong><br />Logs have been entered using this program name<br />
					You must change this program to inactive and enter a new show to change the name with the logs in the archive</span>";
				}
				else{
					echo mysql_errno() . "<br />". mysql_error();
				}
			}

			//Set/Update XREF ( Cross Reference )
			//******************************//
			// If there is no XREF it must	//
			// be set to null!			//
			//******************************//

			if($_POST['XREF']==-1){
				$XRCEA = "Update adverts SET XREF = Null where AdId='".$AdId."' or XREF='".$AdId."' ";
				if(!mysql_query($XRCEA))
				{
					echo "ERROR: Could not clear XREF";
				}
			}
			else if (isset($_POST['XREF'])){
				$XRUP1 = "Update adverts SET XREF = '".addslashes($_POST['XREF'])."' where AdId='".$AdId."' ";
				$XRUP2 = "Update adverts SET XREF = '".$AdId."' where AdId='".addslashes($_POST['XREF'])."' ";
				if(mysql_num_rows(mysql_query("select XREF from adverts where AdId='".addslashes($_POST['XREF'])." and XREF IS NULL"))!=0){
					echo "ERROR XREF Exists, cannot update without removal of old XREF. Only two commercials can be linked (bidirectional only)";
				}
				else{
					if(mysql_query($XRUP1)){
						mysql_query($XRUP2);
					}
					else{
						echo "Error, Could not complete XREF update";
					}
				}
			}

	}

	$SQLA = "Select adverts.* from adverts where adverts.AdId LIKE '" . $AdId . "' ";

	// build query (simple version)
	if(isset($_POST['AdName'])){
		$SQLA .= "and adverts.AdName LIKE '" . $AdName . "' ";
	}
	$SQLA .= " order by AdId,Category";

	$result = mysql_query($SQLA) or die(mysql_error());
             if(mysql_num_rows($result)=="0"){
               echo '<tr><td colspan="100%" style="background-color:yellow;">';
               echo 'No Results Found';
               echo '</tr></td>';
			   echo $SQLA;
             }
             else{
	$PRONAME = "";
	$CALLS = "";
               while($row=mysql_fetch_array($result)) {
		/*echo "<form name=\"row\" action=\"/program/p3advupdate.php\" method=\"POST\"><tr>
				<td>";*/
		echo "<form name=\"row\" action=\"p3update.php\" method=\"POST\"><tr>
				<td>";
				echo "<input name=\"name\"  value=\"" . $row['AdName'] . "\" size=\"30\" maxlength=\"75\"/>";
				echo "<input name=\"namex\" value=\"" . $row['AdName'] . "\" hidden />";
		echo "</td>
				<td>";
				//echo $row['genre'];
				echo "<input name=\"catx\" value=\"" . $row['Category'] . "\" hidden />";
				echo "<select name=\"category\">
						<option value=\"53\"";
						if($row['Category']=="53"){
							echo " selected ";
						}
						echo ">53, Sponsored Promotion</option>
	           			<OPTION value=\"52\"";
						if($row['Category']=="52"){
							echo " selected ";
						}
						echo ">52, Sponsor Indentification</OPTION>
	           			<OPTION VALUE=\"51\"";
						if($row['Category']=="51"){
							echo " selected ";
						}
						echo ">51, Commercial</OPTION>
	           			<option value=\"45\"";
						if($row['Category']=="45"){
							echo " selected ";
						}
						echo ">45, Show Promo</option>
	           			<option value=\"44\"";
						if($row['Category']=="44"){
							echo " selected ";
						}
						echo ">44, Programmer/Show ID</option>
	           			<option value=\"43\"";
						if($row['Category']=="43"){
							echo " selected ";
						}
						echo ">43, Station ID</option>
						<option value=\"12\"";
						if($row['Category']=="12"){
							echo " selected ";
						}
						echo ">12-P, PSA</option>
	           		</select>";
				if(false){

					echo "</td>
							<td>";

							echo "<input type=\"text\" readonly name=\"AdId\" value=\"" . $row['AdId'] . "\"  size='6' />";
				}
				echo "<input type=\"hidden\" name=\"AdIdx\" value=\"" . $row['AdId'] . "\" />";
		echo "</td>
				<td>";

				echo "<input type=\"text\" name=\"Length\" value=\"" . $row['Length'] . "\" maxlength='4' size=\"5\" />";
				echo "<input type=\"text\" name=\"Lengthx\" value=\"" . $row['Length'] . "\" hidden />";

		echo "</td>
				<td>";
				echo "<input type=\"date\" name=\"start\" ";
					echo "value=\"" . $row['StartDate'] . "\" ";
				echo "/>";

		echo "</td>
				<td>";
				echo "<input type=\"date\" name=\"end\" ";
					echo "value=\"" . $row['EndDate'] . "\" ";
				echo "/>";

		echo "</td>
				<td>";
					//echo $row['active'];
					echo "<input type=\"checkbox\" name=\"Active\" ";
					if($row['Active']==0){
						echo " />";
					}
					else{
						echo "checked=\"1\" />";
					}

					echo "<input type=\"text\" name=\"Activex\" value=\"" . $row['Active'] . "\" hidden />";
		echo "</td>
				<td>";
				echo "<input type=\"checkbox\" name=\"Friend\" ";
					if($row['Friend']==0){
						echo " />";
					}
					else{
						echo "checked=\"1\" />";
					}

					echo "<input type=\"text\" name=\"Friendx\" value=\"" . $row['Friend'] . "\" hidden />";
		//echo "</td><td><input type=\"submit\" value=\"select\"/> </td></tr></form>";
		$PRONAME = $row['programname'];
		$CALLS = $row['callsign'];

		$XREFSEL = "select * from adverts where AdId != '" . $AdId . "' and Category='" . $row['Category'] . "' and (XREF IS NULL or XREF='".$row['AdId']."' ) order by AdId";
		if(!$XREF = mysql_query($XREFSEL)){
			$XRBUF = "<option>XREF ERROR: " . mysql_errno() . " </br> " . mysql_error()."</option>" ;
		}
		else{
			if(isset($row['XREF'])){
				$OREF = $row['XREF'];
			}
			else{
				$OREF = "-1";
			}
			$XRBUF = "<option value=\"-1\">None</option>";
			while($XREFOP=mysql_fetch_array($XREF)){
				$XRBUF .= "<option value=\"".$XREFOP['AdId']."\" ";
				if($XREFOP['AdId']==$row['XREF']){
					$XRBUF .= " selected style=\"background-color:yellow; color:red;\" ";
				}
				$XRBUF .= ">".$XREFOP['AdName']."</option>
				";
			}
		}
		echo "<td><select name=\"XREF\" >".$XRBUF."</select></td>";
			   }
		}

}
else{
	echo 'ERROR!';
}
?>
</table>
	<?php
		$sqlmaxpl = "select HitLimit, CCX, PLX, genre from program where programname=\"" . $PROGNAME . "\" and callsign=\"". $CALLS . "\" ";
			if(!$limits = mysql_fetch_array(mysql_query($sqlmaxpl))){
				echo mysql_error();
			}
		$sqlgenq2 = "select * from genre where genreid=\"" . $limits['genre'] .  "\" ";
			if(!$GENREQ = mysql_fetch_array(mysql_query($sqlgenq2))){
				echo mysql_error();
			}
	 ?>

		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
					<input name="changed" value="true" hidden="true" />
					<input type="submit" value="Submit Changes"></form></td><td>
					<form action="p1update.php" method="POST">
				<input type="submit" value="Search"/></form></td><td>
					<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
						<input type="text" hidden="true" value="<?php echo $AdId ?>" name="postval"/>
						<input type="submit" value="Reset" />
					</form></td><td>
				<form method="POST" action="../../masterpage.php"><input type="submit" value="Menu"/></form>
				</td><td>
					<form method="POST" action="p1ReqAdIns.php">
						<input type="hidden" name="AdNum" value="<?php  echo $AdId;?>" />
						<input type="submit" value="Requirements"/></form>
				</td>
				<td width="100%" align="right"><img src="../../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>

</body>
</html>
