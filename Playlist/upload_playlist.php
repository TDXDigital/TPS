<?php
include_once "../TPSBIN/functions.php";
include_once "../includes/phpexcel/Classes/PHPExcel.php";
include_once "../TPSBIN/functions.php";
include_once "../TPSBIN/db_connect.php";
include "../includes/phpexcel/Classes/PHPExcel/IOFactory.php";
sec_session_start();

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

//  Loop through each row of the worksheet in turn
for ($row = 1; $row <= $highestRow; $row++){ 
    //  Read a row of data into an array
    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                    NULL,
                                    TRUE,
                                    FALSE);
    //  Insert row data array into your database of choice here
    /*echo "<tr>";
    echo "<td>".$rowData[0][0]."</td>";
    //print_r($rowData);
    echo "<td>".$rowData[0][1]."</td>";
    echo "<td>".$rowData[0][2]."</td>";
    echo "<td>".$rowData[0][3]."</td>";
    echo "<td>".$rowData[0][4]."</td>";
    echo "<td>".$rowData[0][5]."</td><td>";*/
    //echo "<td>".$rowData[1][0]."</td>";
    

    $stmt = $mysql->prepare("INSERT IGNORE INTO `library` (artist, album, datein, year) VALUES (?, ?, ?, ?)");
    $stmt->bindparam('sssi',$rowData[0][0],$rowData[0][1],$rowData[0][3],$rowData[0][4]); // LABEL REMOVED
    $stmt->execute();
    var_dump($stmt); // shows null values
    var_dump($stmt->errno); // note literal, displays value 
    //echo $stmt->error();
    printf("%d Row inserted.\n", $stmt->affected_rows);
    $stmt->close();
    //echo "</td></tr>";
}
//echo "</table>";
echo "Complete";

?>