<?php

session_start();

//if($_SESSION['usr']=='user')
//{
  //header('location: login.php');
//}
$con = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (mysqli_connect_errno($con))
{
    //echo "Failed to connect to MySQL: " . mysqli_connect_error();
    header('location: p1playlistmgr.php?e=DBE');
}
	
	$source = $_POST['source'];
	$change = $_POST['change'];
    $delete = $_POST['del'];

	$artist = $_POST['artist'];
    $barcode = $_POST['Barcode'];
    $refcode = $_POST['Refcode'];
    $datein = $_POST['DateIn'];
    $year = $_POST['year'];
    $genre = $_POST['genre'];
	$album = $_POST['album'];
	$locale = $_POST['locale'];
	$status = $_POST['Status'];
    $label = $_POST['label'];
    $cancon = '1';
    if($locale=="International"){
        $cancon = '0';
    }

    
    
    /* Information Relating
     * to storage of Playlist
     */
    $PL_ZoneCode = $_POST['ZoneCode'];
    $PL_ZoneNum = $_POST['ZoneNumber'];
    $PL_SmallCode = $_POST['num'];
    $PL_Activate = $_POST['activate'];
    if(isset($_POST['expire']) && $_POST['expire']!=""){
        $PL_Expire = "'".addslashes($_POST['expire'])."'";
    }
    else{
        $PL_Expire = NULL;
    }
    //$PL_Expire = $_POST['expire'];

	//$limit = sizeof($num);
	
	/*echo sizeof($num) . "<br/>";
	echo sizeof($source). "<br/>";
	echo sizeof($change) . "<br/>";
	echo sizeof($artist) . "<br/>";
	echo sizeof($album) . "<br/>";
	echo sizeof($cancon) . "<br/>";*/
	$error = "";
	//for($i = 0; $i < $limit ; $i++){
		//$error .= $source[$i] . "<br/>";
		/*if($source[$i]!=""){// if the song has a source number update it
			
		}*/
		//else{ // otherwise create it

            // Set autocommit to off
            mysqli_query($con,"START TRANSACTION;");
            $rollback = FALSE;
            //mysqli_autocommit($con,FALSE);
            $playlistid = NULL;
            // Insert Values
            if($PL_SmallCode != "" || $PL_ZoneNum != "")
            {
                $INSPL = "INSERT INTO playlist (ZoneCode,ZoneNumber,SmallCode,Activate,Expire) VALUES ('$PL_ZoneCode','$PL_ZoneNum','$PL_SmallCode','$PL_Activate',$PL_Expire)";
                IF(!mysqli_query($con,$INSPL)){
                    $rollback = TRUE;
                    $error.="Rollback Occured, Transaction Failed on Playlist";
                }
                echo $INSPL;

                // USE ' ' around ID to allow for NULL if this is not executed
                $playlistid= "'".mysqli_insert_id($con)."'";
            }
            $Ins_Lib = "INSERT INTO library (artist,album,year,Locale,labelid,CanCon,playlistid) VALUES
            ('$artist','$album','$year','$locale','$label','$cancon',$playlistid)";
            echo $Ins_Lib;
            //if()
            if(!mysqli_query($con,$Ins_Lib))
            {
                $rollback=TRUE;
                $error.="Rollback Occured, Transaction Failed on library";
            }
            //mysqli_commit($con);
            if($rollback){
                mysqli_rollback($con);
            }
            else{
                mysqli_query($con,"COMMIT;");
            }
            /* 
            mysqli_query($con,"INSERT INTO Persons (FirstName,LastName,Age)
            VALUES ('Peter','Griffin',35)");
            mysqli_query($con,"INSERT INTO Persons (FirstName,LastName,Age) 
            VALUES ('Glenn','Quagmire',33)");
            */
			/*if(!mysqli_query($sql)){
				$error .= "(Error " . mysql_errno() . ") ";
				$error .= mysql_error() . "<br/>";
			}*/
		//}
	//}
	
	/* needs to follow update and delete in case user put changes into system,
	 * if changes are not done before delete the system could try and update a 
	 * playlist item that does not exist. 
	 */
     /*
	for($x = 0 ; $x < sizeof($delete) ; $x++){
		$sql = "delete from playlist where number = '" . $delete[$x] . "' ";
		if(!mysqli_query($con,$sql)){
			$error .= "(Error " . mysqli_errno($con) . ") ";
			$error .= mysqli_error($con) . "<br/>";
		}		
	}*/
    mysqli_close($con);
	if($error==""){
		header('location: p1playlistmgr.php');
	}
	else{
		/*echo "<h1>Oh No! Some Error(s) Occured!</h1>";
		echo "<p>Error Description(s):<br/>".$error."</p>";
		echo "<a href=\"p1playlistmgr.php\" >Return</a>";*/
        header('location: p1playlistmgr.php?e=Rollback');
	}
	
?>
