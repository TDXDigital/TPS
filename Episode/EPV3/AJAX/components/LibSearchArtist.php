<?php
	session_start();
include_once "../../../../TPSBIN/functions.php";
include_once "../../../../TPSBIN/db_connect.php";

$search= filter_input(INPUT_POST, 'term',FILTER_SANITIZE_STRING);
	//$search = addslashes($_GET['term']);
	//$search = addslashes($_POST['term']); 
	$LIMIT = $_SESSION['AutoComLimit'];
	$ENABLE = $_SESSION['AutoComEnable'];
if($ENABLE!=TRUE){
	return 0;
}
//$con = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
/* check connection */
/*if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
else{*/
		$sql = "SELECT category,lower(artist) FROM song WHERE lower(artist) like '$search%' and category not like '1%' 
				and category not like '4%' and category not like '5%' group by lower(artist) limit $LIMIT;";
		$result = $mysqli->query($sql);
		$values = array();
		while($row = mysqli_fetch_assoc($result)){
			array_push($values,ucfirst($row['lower(artist)']));
		}
	    echo json_encode($values);
//}
