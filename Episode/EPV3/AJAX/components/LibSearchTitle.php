<?php
	session_start();
	$search = addslashes($_GET['term']);
	$search = addslashes($_POST['term']); 
	$LIMIT = $_SESSION['AutoComLimit'];
	$ENABLE = $_SESSION['AutoComEnable'];
if($ENABLE!=TRUE){
	return 0;
}
$con = mysqli_connect('localhost',$_SESSION['usr'],$_SESSION['rpw'],"CKXU");
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
else{
		$sql = "SELECT category,lower(title) FROM song WHERE lower(title) like '$search%' and category not like '1%' 
				and category not like '4%' and category not like '5%' group by lower(title) 
				order by lower(title) asc limit $LIMIT;";
		$result = mysqli_query($con,$sql) or die("Critical Error");
		$values = array();
		while($row = mysqli_fetch_assoc($result)){
			array_push($values,ucfirst($row['lower(title)']));
		}
	    echo json_encode($values);
}
?>