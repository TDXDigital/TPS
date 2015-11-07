<?php
	session_start();
	$search = addslashes($_GET['term']);
	$search = addslashes($_POST['term']); 
	$LIMIT = $_SESSION['AutoComLimit'];
	$ENABLE = $_SESSION['AutoComEnable'];
if($ENABLE!=TRUE){
	return 0;
}
$con = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
else{
		$sql = "SELECT category,lower(album),lower(artist) FROM song WHERE playlist like '%$search%' and category not like '1%' 
				and category not like '4%' and category not like '5%' group by lower(album) 
				order by lower(album) asc limit $LIMIT;";
		$result = mysqli_query($con,$sql) or die("Critical Error");
		$values = array();
		while($row = mysqli_fetch_assoc($result)){
			array_push($values,ucfirst($row['lower(album)']));
		}
	    echo json_encode($values);
}
?>