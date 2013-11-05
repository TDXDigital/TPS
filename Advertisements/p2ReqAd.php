<?php
    session_start();

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../login.php');}
	
    }
else{
	echo 'ERROR!';
}
$ADN = addslashes($_POST['adnum']);
$LIM = addslashes($_POST['limit']);
$START = addslashes($_POST['start']);
$END = addslashes($_POST['end']);
$DAY = array_values($_POST['day']);
$BLM = addslashes($_POST['BLM']);
$DEL = array_values($_POST['delete']);

?>

<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../altstyle.css" />
<title>Ad Rotation Maintenace</title>
</head>
<html>
<body>
	<div class="topbar">
           User: <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="#"><img src="../images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
	</div>
	<div id="top">
		<h2>Ad Maintenance</h2>
	</div>
	<div id="content">
		<form name="search" method="POST" action="p2ReqAds.php">
		<table border="0" class="tablecss">
			<tr><td>
				<?php
                $ADDPASS = TRUE;
                $DELPASS = TRUE;
                $DELERR = FALSE;
                echo "<br/>ARRAY SIZE: " . sizeof($DAY);
			    echo "<br/>POST  SIZE: " . sizeof($_POST['day']);
                echo "<br/>ARRAY SIZE: " . sizeof($DEL);
			    echo "<br/>POST  SIZE: " . sizeof($_POST['delete']);
                if(sizeof($DAY)>0){
                    $ADDPASS = FALSE;
				    $ADU = "insert into adrotation (startTime,endTime,HourlyLimit,BlockLimit,AdId) values ('".$START."','".$END."','".$LIM."','".$BLM."','".$ADN."')";
				    if(mysql_query($ADU)){
					    echo "<br/>Completed PART 1/2";
					    /*
                        echo "<br/>ARRAY SIZE: " . sizeof($DAY);
					    echo "<br/>POST  SIZE: " . sizeof($_POST['day']);
                        */
					    $REFER = mysql_insert_id();
					    for($i=0;$i<sizeof($DAY);$i++){
						
						    $DayAdd = "insert into addays (AdIdRef,Day) values ('".$REFER."','".$DAY[$i]."')";
						    //echo $DayAdd . " - ". $DAY[$i];
						    if(!mysql_query($DayAdd)){
							    echo "<br/>".mysql_error();
						    }
					    }
					    echo "<br/>Completed PART 2/2";
					    echo "<br/>Succesful Addition of Ad Requirement";
					    $ADDPASS = TRUE;
					
				    }
				    else{
					    echo "FAILED [ ".mysql_error()." ]";
                        //header('Location: p1ReqAdIns.php?AD='.$ADN);
				    }
                }
                else
                {
                    $ADDPASS = TRUE;
                }
                $DELERR = FALSE;
                if(sizeof($DEL)>0)
                {
                    echo "<br/>Delete ad started";
                    for($i2=0;$i2<sizeof($DEL);$i2++){
                        $DelSQL1 = "DELETE FROM adrotation WHERE RotationNum = '".$DEL[$i2]."'";
                        if(!mysql_query($DelSQL1)){
                            $DELPASS = FALSE;
                            $DELERR = TRUE;
                        }
                        $DelSQL2 = "DELETE FROM addays WHERE AdIdRef = '".$DEL[$i2]."'";
                        if(!mysql_query($DelSQL2)){
                            $DELPASS = FALSE;
                            $DELERR = TRUE;
                        }
                        /*try{
                            $con->beginTransaction();
                            $con->query("DELETE FROM adrotation WHERE RotationNum = '".$DEL[$i2]."'");
                            $con->query("DELETE FROM addays WHERE AdIdRef = '".$DEL[$i2]."'");
                            $con->commit();
                            echo "DELETE FROM adrotation WHERE RotationNum = '".$DEL[$i2]."'";
                            echo "DELETE FROM addays WHERE AdIdRef = '".$DEL[$i2]."'";
                        }
                        catch (Exception $e){
                            $con->rollback();
                            echo 'Caught exception: ',  $e->getMessage(), "\n";
                        }*/
                        /*$DelSQL = "
                        DELETE FROM adrotation WHERE RotationNum = '".$DEL[$i2]."'; 
                        DELETE FROM addays WHERE AdIdRef = '".$DEL[$i2]."'
                        ";
                        echo $DelSQL;
                        if(!mysql_query($DelSQL)){
                            $DELPASS = FALSE;
                            $DELERR = TRUE;
                        }*/
                    }
                }
                if(!$DELERR){
                   $DELPASS = TRUE;     
                }

                if($DELPASS == TRUE && $ADDPASS == TRUE ){
                    echo "Add Requirement: $ADDPASS";
                    echo "Delete Requirement: $DELPASS";
                    header('Location: p1ReqAdIns.php?AD='.$ADN);
                }
                else{
                    echo "An Error Occured Please see the results below<br/>";
                    echo mysql_error();
                    echo "Add Requirement: $ADDPASS";
                    echo "Delete Requirement: $DELPASS";
                }
				?>
			</td></tr>
		</table>
		
		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
				<input type="submit" value="Insert"/></form></td><td>
				<input type="button" value="Reset" onClick="window.location.reload()"></td><td>
				<form method="POST" action="../masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td width="100%" align="right"><img src="/images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>
	<div id="content">
			<h4>Help</h4>
		<span>You can enter a % into the field to enter partial information. ie, if a show you 
			wanted to find was called "Best Show Ever" you can put "Best%" and the system will find all shows that begin with "Best", otherwise you can put %show% to
			find any shows that have "show" in the name or "%ever" for shows that end in "ever"</span>
		
	</div>
</body>
</html>