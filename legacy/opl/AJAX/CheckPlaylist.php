<?php

session_start();

//if($_SESSION['usr']=='user')
//{
  //header('location: login.php');
//}
$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
	if(!mysql_select_db("CKXU")){echo "Auth Error";} // or die("<h1>Error ".mysql_errno() ."</h1><br />check access (privileges) to the SQL server db CKXU for this user <br /><br /><hr />Error details:<br />" .mysql_error() . "<br /><br /><a href=login.php>Return</a>");
}
else{
	echo 'ERROR!';
}
	$num = addslashes($_GET['num']);
	$query = "Select * from playlist where number='".$num."'";
	if($from = $_GET['f']){
		if($limit = $_GET['l']){
			$query .= " limit " . $from . "," . $limit;
		}
	}
    $array_playlist = mysql_query($query);
	$i = 0;
	//echo "<table>";
	/*echo "<tr>
				<th width=\"10%\">Playlist #</th><th width=\"30%\">Artist</th><th width=\"30%\">Album</th><th width=\"10%\">CanCon</th><th width=\"20%\">link</th>
			</tr>";*/
	while($row = mysql_fetch_array($array_playlist)){
		//if($i%2){
			echo "<tr>";
		/*}
		else{
			echo "<tr style=\"background-color:blue\">";
		}
		*/
		
		echo "<td><input type=\"text\" style=\"width:99%;\" name=\"num[]\" value=\"" . $row['number'] . "\" /></td>";
		echo "<input type=\"hidden\" name=\"source[]\" value=\"" . $row['number'] . "\" />
			 <input type=\"hidden\" name=\"change[]\" value=\"false\">";
		echo "<td><input type=\"text\" style=\"width:99%;\" name=\"artist[]\" value=\"" . $row['Artist'] . "\" /></td>";
		echo "<td><input type=\"text\" style=\"width:99%;\" name=\"album[]\" value=\"" . $row['Album'] . "\" /></td>";
		echo "<td><select name=\"cancon[]\" style=\"width:99%;\">
				<option value=\"NC\" ></option>
				<option";
				if($row['cancon']=="CC"){
					echo " selected ";
				}
				echo " value=\"CC\" >Canadian</option>
				<option";
				if($row['cancon']=="LC"){
					echo " selected ";
				}
				echo " value=\"LC\" >Local</option>
				</select></td>";
		echo "<td><input type=\"text\" style=\"width:99%;\" name=\"year[]\" value=\"" . $row['year'] . "\" /></td>";
		echo "</tr>";
		$i++;
	}
	//echo "<table>";
	mysql_close($con);
?>