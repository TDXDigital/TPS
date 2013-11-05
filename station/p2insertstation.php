<?php

session_start();

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);

mysql_select_db("CKXU") or die(mysql_error());
//echo "database selected";
if($_POST[callsign]!="" && $_POST[name]!=""){
  $sql = "insert into STATION (callsign,stationname,Designation,frequency,website,address,boothphone,directorphone) values ( '$_POST[callsign]' , '$_POST[name]' , '$_POST[designation]' , '$_POST[frequency]' , '$_POST[website]' , '$_POST[address]' , '$_POST[boothph]' , '$_POST[direcphone]' )";
  if(mysql_query($sql,$con))
  {
    echo "<h3>Station added</h3>";
  }
  else
  {
    $err = mysql_errno();
    if($err == 1062)
    {
      echo "<p>Station name " . $_POST[callsign] . " already exists</p>";
    }
    else
    {
      echo $err;
      echo mysql_error();
      echo $sql;
    }
  }
}
else
{
  echo "<br /><h1>Error</h1><hr /><h2>A Station Name and Callsign MUST be specified</h2><br/>";
}


echo "<a href=\"../masterpage.php\">Return to admin home</a>";

?>

