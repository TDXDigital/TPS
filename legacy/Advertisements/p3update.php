<?php
session_start();
date_default_timezone_set($_SESSION['TimeZone']?:"UTC");
require_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."TPSBIN".DIRECTORY_SEPARATOR."functions.php";
require_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."TPSBIN".DIRECTORY_SEPARATOR."db_connect.php";
?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../../css/altstyle.css" />
<title>Commercial Management</title>
</head>
<html>
<body>
	<div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['fname'])); ?>
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
if(isset($_POST['AdIdx'])){
    $AdId = $mysqli->real_escape_string(filter_input(INPUT_POST, 'AdIdx'));
}
else if(isset($_POST['postval'])){
    $AdId = $mysqli->real_escape_string(filter_input(INPUT_POST, 'postval'));
}
else if(isset($_GET['resource'])){
    $AdId = $mysqli->real_escape_string(filter_input(INPUT_POST, 'resource'));
}
else{
    $mysqli->real_escape_string(filter_input(INPUT_POST, 'AdId'));
}
if(isset($_POST['changed'])){
    // UPDATE NAME
    $CHNA = "Update adverts SET AdName='" . addslashes($_POST['name']) . "' where AdId='" . $AdId . "' ";
    if(!$mysqli->query($CHNA)){
        if($mysqli->errno==1451)
        {
            echo "<span style=\"background-color:red; color:white;\"><strong>Error 1451</strong><br />Logs have been entered using this program name<br />
            You must change this program to inactive and enter a new show to change the name with the logs in the archive</span>";
        }
        else{
            echo $mysqli->errno . "<br />". $mysql->error;
        }
    }
    else{
        $AdName = filter_input(INPUT_POST, "name");
    }

    // UPDATE CATEGORY
    $CHCAT = "Update adverts SET Category='" . $_POST['category'] . "' where AdId='".$AdId."' ";
    if(!$mysqli->query($CHCAT)){
        if($mysqli->errno==1451)
        {
            echo "<span style=\"background-color:red; color:white;\"><strong>Error 1451</strong><br />Logs have been entered using this program name<br />
            You must change this program to inactive and enter a new show to change the name with the logs in the archive</span>";
        }
        else{
            echo $mysqli->errno . "<br />". $mysql->error;
        }
    }

    // UPDATE START DATE
    $CHSTA = "Update adverts SET StartDate='" . $_POST['start'] . "' where AdId='".$AdId."' ";
    if(!$mysqli->query($CHSTA)){
        if($mysqli->errno==1451)
        {
            echo "<span style=\"background-color:red; color:white;\"><strong>Error 1451</strong><br />Logs have been entered using this program name<br />
            You must change this program to inactive and enter a new show to change the name with the logs in the archive</span>";
        }
        else{
            echo $mysqli->errno . "<br />". $mysqli->error;
        }
    }

    // UPDATE STARTTIME
    $CHEND = "Update adverts SET EndDate='" . $_POST['end'] . "' where AdId='".$AdId."' ";
    if(!$mysqli->query($CHEND)){
        if($mysqli->errno==1451)
        {
            echo "<span style=\"background-color:red; color:white;\"><strong>Error 1451</strong><br />Logs have been entered using this program name<br />
            You must change this program to inactive and enter a new show to change the name with the logs in the archive</span>";
        }
        else{
            echo $mysqli->errno . "<br />". $mysqli->error;
        }
    }
    // UPDATE MAX PLAY
    //#############################################################
    // To Be Implemented Later [not in MySQL yet]


    // UPDATE LENGTH
    $CHLEN = "Update adverts SET Length='" . addslashes($_POST['Length']) . "' where AdId='" . $AdId . "' ";
    if(!$mysqli->query($CHLEN)){
        if($mysql->errno==1451)
        {
            echo "<span style=\"background-color:red; color:white;\"><strong>Error 1451</strong><br />Logs have been entered using this program name<br />
            You must change this program to inactive and enter a new show to change the name with the logs in the archive</span>";
        }
        else{
            echo $mysqli->errno . "<br />". $mysqli->error;
        }
    }

    // Update Friend Setting
    if(isset($_POST['Friend'])){
        $CHFRI = "Update adverts SET Friend='1' where AdId='" . $AdId . "' ";
    }
    else{
        $CHFRI = "Update adverts SET Friend='0' where AdId='" . $AdId . "' ";
    }
    if(!$mysqli->query($CHFRI)){
        if($mysqli->errno==1451)
        {
            echo "<span style=\"background-color:red; color:white;\"><strong>Error 1451</strong><br />Logs have been entered using this program name<br />
            You must change this program to inactive and enter a new show to change the name with the logs in the archive</span>";
        }
        else{
            echo $mysql->errno. "<br />". $mysqli->error;
        }
    }

    // Update Active Setting
    if(isset($_POST['Active'])){
        $CHACT = "Update adverts SET Active='1' where AdId='" . $AdId . "' ";
    }
    else{
        $CHACT = "Update adverts SET Active='0' where AdId='" . $AdId . "' ";
    }
    if(!$mysqli->query($CHACT)){
        if($mysqli->errno==1451)
        {
            echo "<span style=\"background-color:red; color:white;\"><strong>Error 1451</strong><br />Logs have been entered using this program name<br />
            You must change this program to inactive and enter a new show to change the name with the logs in the archive</span>";
        }
        else{
            echo $mysqli->errno . "<br />". $mysqli->error;
        }
    }

    //Set/Update XREF ( Cross Reference )
    //******************************//
    // If there is no XREF it must	//
    // be set to null!			//
    //******************************//

    if($_POST['XREF']==-1){
        $XRCEA = "Update adverts SET XREF = Null where AdId='".$AdId."' or XREF='".$AdId."' ";
        if(!$mysqli->query($XRCEA))
        {
            echo "ERROR: Could not clear XREF";
        }
    }
    else if (isset($_POST['XREF'])){
        $XRUP1 = "Update adverts SET XREF = '".addslashes($_POST['XREF'])."' where AdId='".$AdId."' ";
        $XRUP2 = "Update adverts SET XREF = '".$AdId."' where AdId='".addslashes($_POST['XREF'])."' ";
        $resultXref = $mysqli->query("select XREF from adverts where AdId='".addslashes($_POST['XREF'])."' and XREF IS NULL");
        if($resultXref->num_rows==0){
            echo "ERROR XREF Exists, cannot update without removal of old XREF. Only two commercials can be linked (bidirectional only)";
        }
        else{
            if($mysqli->query($XRUP1)){
                $mysqli->query($XRUP2);
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
$result = $mysqli->query($SQLA) or die($mysqli->error);
if($result->num_rows=="0"){
    echo '<tr><td colspan="100%" style="background-color:yellow;">';
    echo 'No Results Found';
    echo '</tr></td>';
    echo $SQLA;
}
else {
    $PRONAME = "";
    $CALLS = "";
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        echo "<form name=\"row\" action=\"p3update.php\" method=\"POST\"><tr><td>";
        echo "<input name=\"name\"  value=\"" . $row['AdName'] . "\" size=\"30\" maxlength=\"75\"/>";
        echo "<input name=\"namex\" value=\"" . $row['AdName'] . "\" hidden />";
        echo "</td><td>";
        echo "<input name=\"catx\" value=\"" . $row['Category'] . "\" hidden />";
        echo "<select name=\"category\"><option value=\"53\"";
        if ($row['Category'] == "53") {
            echo " selected ";
        }
        echo ">53, Sponsored Promotion</option>
        <OPTION value=\"52\"";
        if ($row['Category'] == "52") {
            echo " selected ";
        }
        echo ">52, Sponsor Indentification</OPTION>
        <OPTION VALUE=\"51\"";
        if ($row['Category'] == "51") {
            echo " selected ";
        }
        echo ">51, Commercial</OPTION>
        <option value=\"45\"";
        if ($row['Category'] == "45") {
            echo " selected ";
        }
        echo ">45, Show Promo</option>
        <option value=\"44\"";
        if ($row['Category'] == "44") {
            echo " selected ";
        }
        echo ">44, Programmer/Show ID</option>
        <option value=\"43\"";
        if ($row['Category'] == "43") {
            echo " selected ";
        }
        echo ">43, Station ID</option>
        <option value=\"12\"";
        if ($row['Category'] == "12") {
            echo " selected ";
        }
        echo ">12-P, PSA</option>
        </select>";
        echo "<input type=\"hidden\" name=\"AdIdx\" value=\"" . $row['AdId'] . "\" />";
        echo "</td><td>";
        echo "<input type=\"text\" name=\"Length\" value=\"" . $row['Length'] . "\" maxlength='4' size=\"5\" />";
        echo "<input type=\"text\" name=\"Lengthx\" value=\"" . $row['Length'] . "\" hidden />";
        echo "</td><td>";
        echo "<input type=\"date\" name=\"start\" ";
        echo "value=\"" . $row['StartDate'] . "\" ";
        echo "/>";
        echo "</td><td>";
        echo "<input type=\"date\" name=\"end\" ";
        echo "value=\"" . $row['EndDate'] . "\" ";
        echo "/>";
        echo "</td><td>";
        echo "<input type=\"checkbox\" name=\"Active\" ";
        if ($row['Active'] == 0) {
            echo " />";
        } else {
            echo "checked=\"1\" />";
        }
        echo "<input type=\"text\" name=\"Activex\" value=\"" . $row['Active'] . "\" hidden />";
        echo "</td><td>";
        echo "<input type=\"checkbox\" name=\"Friend\" ";
        if ($row['Friend'] == 0) {
            echo " />";
        } else {
            echo "checked=\"1\" />";
        }
        echo "<input type=\"text\" name=\"Friendx\" value=\"" . $row['Friend'] . "\" hidden />";

        $XREFSEL = "SELECT * FROM adverts WHERE AdId != '" . $AdId . "' AND Category='" .
            $mysqli->real_escape_string($row['Category']) . "' AND (XREF IS NULL OR XREF='" . $row['AdId'] .
            "' ) ORDER BY AdId";
        if (!$XREF = $mysqli->query($XREFSEL)) {
            $XRBUF = "<option>XREF ERROR: " . $mysqli->errno . " </br> " . $mysqli->error . "</option>";
        } else {
            if (isset($row['XREF'])) {
                $OREF = $row['XREF'];
            } else {
                $OREF = "-1";
            }
            $XRBUF = "<option value=\"-1\">None</option>";
            while ($XREFOP = $XREF->fetch_array(MYSQLI_ASSOC)) {
                $XRBUF .= "<option value=\"" . $XREFOP['AdId'] . "\" ";
                if ($XREFOP['AdId'] == $row['XREF']) {
                    $XRBUF .= " selected style=\"background-color:yellow; color:red;\" ";
                }
                $XRBUF .= ">" . $XREFOP['AdName'] . "</option>
                ";
            }
        }
        echo "<td><select name=\"XREF\" >" . $XRBUF . "</select></td>";
    }
}

?>
</table>
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
				<form method="POST" action="../../"><input type="submit" value="Menu"/></form>
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
