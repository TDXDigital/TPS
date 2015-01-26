<?php

include_once "../TPSBIN/functions.php";

sec_session_start();

include_once "../TPSBIN/db_connect.php";


//$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);

//mysql_select_db("CKXU") or die(mysql_error());
//echo "database selected";
if($_POST[callsign]!="" && $_POST[name]!=""){
  $sql = "insert into STATION (callsign,stationname,Designation,frequency,website,address,boothphone,directorphone) values ( '$_POST[callsign]' , '$_POST[name]' , '$_POST[designation]' , '$_POST[frequency]' , '$_POST[website]' , '$_POST[address]' , '$_POST[boothph]' , '$_POST[direcphone]' )";
  if($mysqli->query($sql))
  {
    echo "<h3>Station added</h3>";
  }
  else
  {
    $err = $mysqli->error;
    if($err == 1062)
    {
      echo "<p>Station name " . $_POST[callsign] . " already exists</p>";
    }
    else
    {
      echo $err;
      echo $mysqli->error;
      echo $sql;
    }
  }
}
else
{
  echo "<br /><h1>Error</h1><hr /><h2>A Station Name and Callsign MUST be specified</h2><br/>";
}

$mysqli->close();
echo "<a href=\"../masterpage.php\">Return to admin home</a>";

?>

