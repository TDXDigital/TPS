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
	$num = $_POST['num'];
	$source = $_POST['source'];
	$change = $_POST['change'];
	$artist = $_POST['artist'];
	$album = $_POST['album'];
	$cancon = $_POST['cancon'];
	$delete = $_POST['del'];
	$limit = sizeof($num);
	
	/*echo sizeof($num) . "<br/>";
	echo sizeof($source). "<br/>";
	echo sizeof($change) . "<br/>";
	echo sizeof($artist) . "<br/>";
	echo sizeof($album) . "<br/>";
	echo sizeof($cancon) . "<br/>";*/
	$error = "";
	for($i = 0; $i < $limit ; $i++){
		//$error .= $source[$i] . "<br/>";
		if($source[$i]!=""){// if the song has a source number update it
			
		}
		else{ // otherwise create it
			$sql = "insert into playlist (number,Album,Artist,cancon) values ('" . $num[$i] . "','" . $album[$i] . "','" . $artist[$i] . "','" . $cancon[$i] . "')";
			if(!mysql_query($sql)){
				$error .= "(Error " . mysql_errno() . ") ";
				$error .= mysql_error() . "<br/>";
			}
		}
	}
	
	/* needs to follow update and delete in case user put changes into system,
	 * if changes are not done before delete the system could try and update a 
	 * playlist item that does not exist. 
	 */
	for($x = 0 ; $x < sizeof($delete) ; $x++){
		$sql = "delete from playlist where number = '" . $delete[$x] . "' ";
		if(!mysql_query($sql)){
			$error .= "(Error " . mysql_errno() . ") ";
			$error .= mysql_error() . "<br/>";
		}		
	}
	if($error==""){
		header('location: p1playlistmgr.php');
	}
	else{
		echo "<h1>Oh No! Some Error(s) Occured!</h1>";
		echo "<p>Error Descriptions:<br/>".$error."</p>";
		echo "<a href=\"p1playlistmgr.php\" >Return</a>";
	}
	
	
	mysql_close($con);
?>