<?php
    session_start();
	$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
	$friends = array();
	if (!$con){
		die("<h2>Error " . mysql_errno() . "</h2><p>Could not establish connection to database. Authentication failed</p>");
	}
	else{
		if(!mysql_select_db("CKXU")){
			die("<h2>Error " . mysql_errno() . "</h2><p>User Access Error. Database refused connection</p>");
		}
		$friendsql = "SELECT * FROM adverts WHERE Friend='1' and Active='1'";
		if(!$result = mysql_query($friendsql)){
			die("<h2>Error " . mysql_errno() . "</h2><p>Fetch Error. Could not generate list</h2>");
		}
		else{
			//echo mysql_num_rows($result);
			while($vary = mysql_fetch_array($result)){
				array_push($friends,$vary['AdName']);
			}
			if(sizeof($friends)<1){
				die("<h2>Error L1</h2><p>Array is empty</p>");
			}
			else{
				/*print_r($friends);
				echo sizeof($friends);*/
			}
		}		
	}
?>
<table>
	<tr><th>#</th><th>Friend Name</th></tr>
<?php
	$i = 1;
	while($fr = array_pop($friends)){
		echo "<tr><td>".$i."</td><td>".$fr."</td></tr>";
		$i++;
	}
?>
</table>