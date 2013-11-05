<?php
    session_start();
?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../altstyle.css" />
<title>Commercial Management</title>
</head>
<html>
<body>
	<div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="../masterpage.php"><img src="../images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
	</div>
	<div id="top">
		<h2>Edit Commercial / Promo</h2>
	</div>
	<div id="content">
		<table border="0" class="tablecss">
			<tr>
				<th width="10px">
					
				</th>
				<th width="300px">
					Ad Name / Advertiser
				</th>
				<th width="150px">
					Ad Number
				</th>
				<th width="50px">
					Length
				</th>
				<th width="150px">
					Language
				</th>
				<th width="100px">
					Category
				</th>
				<th width="100px">
					Active
				</th>
				<th width="100px">
					Friend
				</th>
			</tr>

<?php

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../masterpage.php?error=ndbs');}
	$SQLA = "Select adverts.* from adverts where adverts.AdName LIKE '%" . addslashes($_POST['name']) . "%' ";
	// build query
	if(isset($_POST['category'])){
		$SQLA .= "and adverts.Category LIKE '%" . addslashes($_POST['category']) . "%' ";
	}
	if(isset($_POST['Active'])){
		$SQLA .= "and adverts.Active LIKE '%" . addslashes($_POST['Active']) . "%' ";
	}
	if(isset($_POST['adnum'])){
		$SQLA .= "and adverts.AdId LIKE '%" . addslashes($_POST['adnum']) . "%' ";
	}
	if(isset($_POST['Friend'])){
		$SQLA .= "and adverts.Friend LIKE '%" . addslashes($_POST['Friend']) . "%' ";
	}
	$SQLA .= " order by  Category desc, AdId";
	
	$result = mysql_query($SQLA) or die(mysql_error());
             if(mysql_num_rows($result)=="0"){
               echo '<tr><td colspan="100%" style="background-color:yellow;">';
               echo 'No Results Found';
               echo '</tr></td>';
			   echo $SQLA;
             }
             else{
             	
		//------------------------- START LOOP OF adverts ---------------------------------
		echo "<form name=\"row\" action=\"p3update.php\" method=\"POST\">";
		$count = 0;
		if(mysql_num_rows($result)==1){
			$row = mysql_fetch_array($result);
				header("location: p3update.php?resource=" . $row['AdId'] );
		}
		else{
			while($row=mysql_fetch_array($result)) {
	        	$labelr="<label for=\"line".$count."\">".$row['AdName']."</label>";      	
				/*echo "<form name=\"row\" action=\"/program/p3advupdate.php\" method=\"POST\"><tr>
						<td>";*/	
				echo "<tr";
				if($count%2){
					echo " style=\"background-color:#DAFFFF;\" ";
				}
				echo">
						<td>";
						echo "<input type=\"radio\" name=\"postval\" required=\"true\" id=\"line".$count."\" value=\"".$row['AdId']."\" /></td><td>";	
						++$count;
						
						$labelr.= "</td>
						<td>" . $row['AdId']. "</td>
						<td>" . $row['Length']. "</td>
						<td>" . $row['Language'] ."</td>
						<td>".  $row['Category'] . "</td>
						<td><input type=\"checkbox\" onclick=\"javascript: return false;\" ";
						
						if($row['Active']!=0){
							$labelr .= "checked";
						}
						
						$labelr .= " />";
						//";
						//echo "<input name=\"syndicate\" value=\"" . $row['syndicatesource'] . "\" hidden />";
						/*$SQDJ = "select Alias from PERFORMS where programname=\"" . addslashes($row['programname']) . "\" and callsign=\"" . addslashes($row['callsign']) . "\"";
						if(!($perfres = mysql_query($SQDJ))){
							echo mysql_error();
						}
						else{
							$alias=mysql_fetch_array($perfres);				
							$labelr .= $alias['Alias'];
							while($alias=mysql_fetch_array($perfres)){
								$labelr .= ", " . $alias['Alias'];
							}
						}*/
						
						$labelr .= "</td>
						<td><input type=\"checkbox\" onclick=\"javascript: return false;\" ";
						
						if($row['Friend']!=0){
							$labelr .= "checked";
						}
						
						$labelr .= " />";
						
						$labelr .= "</td></tr>";
						
						
						echo $labelr;
						
					   }
				}
		}

}
else{
	echo 'ERROR!';
}
?>
</table>
		
		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
					<input type="submit" value="Select" /></form></td><td>
					<form action="p1update.php" method="POST">
						<input type="submit" value="Search"/></form></td><td>
				<input type="button" value="Reset" onClick="window.location.reload()"></td><td>
				<form method="POST" action="../masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td width="100%" align="right"><img src="../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>

</body>
</html>