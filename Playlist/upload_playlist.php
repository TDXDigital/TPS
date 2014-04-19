<?php
// THIS IS A BIG SCRIPT...
//ini_set('MAX_EXECUTION_TIME',9000); // Yes I know 2.5 Hrs, it can take a long time...
set_time_limit(9000);
$time_start = microtime(true); 
// START!
include_once "../TPSBIN/functions.php";
include_once "../includes/phpexcel/Classes/PHPExcel.php";
include_once "../TPSBIN/functions.php";
include_once "../TPSBIN/db_connect.php";
include "../includes/phpexcel/Classes/PHPExcel/IOFactory.php";
sec_session_start();

/*$_GET['START'];
$_GET['END'];*/

if ($_FILES["file"]["error"] > 0)
  {
  echo "Error: " . $_FILES["file"]["error"] . "<br>";
  }
else
  {
  echo "Upload: " . $_FILES["file"]["name"] . "<br>";
  echo "Type: " . $_FILES["file"]["type"] . "<br>";
  echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
  $inputFileName = $_FILES["file"]["tmp_name"];
  echo "Stored in: " . $inputFileName;
  }

  //  Read your Excel workbook
try {
    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($inputFileName);
} catch(Exception $e) {
    die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
}
//echo "Start EXCEL PRINT</br><table>";


//  Get worksheet dimensions
$sheet = $objPHPExcel->getSheet(2); 
$highestRow = $sheet->getHighestRow(); 
$highestColumn = $sheet->getHighestColumn();
echo "<pre>";
$result = array();
$result[0]=0;
$result[1]=0;
$result[2]=0;
$result[3]=0;

/*
$stmt = $mysqli->prepare("IF Not EXISTS (SELECT LabelNumber AS label FROM recordlabel WHERE Name=?)
BEGIN  
  INSERT INTO `recordlabel` (Name) VALUES (?);
  INSERT INTO `library` (artist, album, datein, year, labelid) VALUES (?,?,?,?,LAST_INSERT_ID());
END
ELSE
BEGIN
INSERT INTO `library` (artist, album, datein, year, labelid) VALUES (?,?,?,?,label)
END");*/
//Connosseurs of Porn	Dead Pets	Disappointing Promises	2014	2014-02-14
$recd = $mysqli->prepare("INSERT IGNORE INTO recordlabel (Name) VALUES (?);");
$stmt = $mysqli->prepare("INSERT INTO `library` (artist, album, datein, year, labelid) VALUES (?,?,?,?,?);");
//$label_stmt = $mysqli->prepare("");
$stmt->bind_param('ssssi',$artist,$album,$datein,$year,$labelid); // LABEL REMOVED
$recd->bind_param('s',$label);

//  Loop through each row of the worksheet in turn
if(isset($_POST['end'])){
    $END = $_POST['end'];
    /*if(is_numeric($END)){
        */
        $terminate = $END;
    /*}
    else{
        $terminate = $highestRow;
    }*/
}
else{
    $terminate = $highestRow;
}
if(isset($_POST['start'])){
    $START = $_POST['start'];
    //if(is_numeric($START)){
        $initiate = $START;
    /*}
    else{
        $initiate = 1;
    }*/
}
else{
    $initiate = 1;
}
if($initiate>$terminate){
    $terminate = $highestRow;
    $initiate = 1;
}
elseif($terminate>$highestRow){
    $terminate=$highestRow;
}
for ($row = $initiate; $row <= $terminate; $row++){ //50; $row++){//
    //  Read a row of data into an array
    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                    NULL,
                                    TRUE,
                                    FALSE);
    //  Insert row data array into your database of choice here

    if($rowData[0][0]!="ARTIST"){
        $result[0]++;
        $artist = $rowData[0][0];
        if(strtoupper($rowData[0][1])!="SR"&&strtoupper($rowData[0][1])!="ST"){
            $album_check = $rowData[0][1];
        }
        else{
            $album_check = $artist;   
        }
        // LABEL
        if(!empty($rowData[0][2])){
            $label = addslashes($rowData[0][2]);
        }
        else{
            $label = "Generic Large";
        }
        $recd->execute();
        $select_query = "SELECT LabelNumber AS label FROM recordlabel where Name='$label'";
        $result_sql = $mysqli->query($select_query);
        if($mysqli->errno){
            echo $mysqli->error;
        }
        else{
            $label_arr = $result_sql->fetch_array();
            $labelid=$label_arr['label'];
            /*$label_stmt->execute();
            $label_stmt->bind_result($label);*/
            $album = $album_check;
            //$album = $rowData[0][1];
            $UNIX_DATE = (($rowData[0][4] - 25569) * 86400);
            $datein = gmdate("Y-m-d",$UNIX_DATE);
            if(strpos($rowData[0][3],'?')!== false){
                $year = "0000";
            }
            elseif(empty($rowData[0][3])){
                $year = "0000";
            }
            else{
                $year = $rowData[0][3];
            }
            //var_dump($stmt);
            $stmt->execute();
            if($stmt->errno){
                if(strpos($stmt->error,'Duplicate entry') !== false){
                    $result[1]++;
                    $DUP.="Duplicate row #$row<br/>";
                }
                else{
                    $result[2]++;
                    $ERR.="Error: ". $stmt->error;
                    $ERR.= $select_query."<br>";
                    $ERR.= "[";
                    $ERR.= $datein.",";
                    $ERR.= $artist.",";
                    $ERR.= $album.",";
                    $ERR.= $year.",";
                    $ERR.= $labelid;
                    $ERR.= ']<br>';
                }

            }
            else{
                $result[3]++;
            }
        }
        //printf("<br> %d Row inserted.\n", $stmt->affected_rows);
    }
    else{
        echo "SKIP HEADER<br/>";
    }
    //echo "</td></tr>";
}
$recd->close();
$stmt->close();
$mysqli->close();
echo "</pre>";
echo "<h4 style='color:blue'>Inserted ".$result[3]." of ".$result[0]." rows with ".$result[1]." Duplicates and ".$result[2]." errors</h4>";
//echo "</table>";
//echo "Complete";
$time_end = microtime(true);

//dividing with 60 will give the execution time in minutes other wise seconds
$execution_time = ($time_end - $time_start)/60;

//execution time of the script
$_SESSION["EXEC_TIME"]=$execution_time;
$_SESSION['ERROR_COUNT']=$result[2];
$_SESSION['DUPLICATE_COUNT']=$result[1];
$_SESSION['TOTAL']=$result[0];
$_SESSION['COMPLETE']=$result[3];
header("location: summary.php");
?>