<?php

session_start();

$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);

mysql_select_db("CKXU") or die(mysql_error());
echo "database selected";
if($_POST[callsign]!="" && $_POST[name]!=""){
  //$sql = "update STATION set stationname='$_POST[name]' , designation='$_POST[designation]' , frequency='$_POST[frequency]' , website='$_POST[website]' , address='$_POST[address]' , boothphone='$_POST[boothphone]' , directorphone='$_POST[directorphone]' WHERE callsign='$_POST[callsign]' ";
  $sql= “update STATION set stationname='” . $_POST[‘name’] . “' , designation='” . $_POST[‘designation’] . “' , frequency='” . $_POST[‘frequency’] . “' , website='” . $_POST[‘website’] . “' , address='” . $_POST[‘address’] . “' , boothphone='” . $_POST[‘boothphone’] . “' , directorphone='” . $_POST[‘directorphone’] . “' WHERE callsign='” . $_POST[‘callsign’]. “'”;
//  echo "update STATION set stationname='$_POST[name]' , designation='$_POST[designation]' , frequency='$_POST[frequency]' , website='$_POST[website]' , address='$_POST[address]' , boothphone='$_POST[boothphone]' , directorphone='$_POST[directorphone]' where callsign='$_POST[callsign]'";
  if(mysql_query($sql,$con))
  {
    echo "<h3> station added</h3>";
  }
  else
  {
    $err = mysql_errno();
    if($err == 1062)
    {
      echo "<p>Station name $_POST[callsign] already exists</p>";
      echo mysql_error();
    }
    else
    {
      echo $err;
      echo mysql_error();
    }
  }
}
else
{
  echo "<br /><h1>Error</h1><hr /><h2>A Station Name and Callsign MUST be specified</h2><br/>";
  echo "update STATION set callsign='$_POST[callsign]' , stationname='$_POST[name]' , designation='$_POST[designation]' , frequency='$_POST[frequency]' , website='$_POST[website]' , address='$_POST[address]' , boothphone='$_POST[boothphone]' , directorphone='$_POST[directorphone]' where callsign='='$_POST[callsign]'";
}


echo "<a href=\"masterpage.php\">Return</a> to admin home";

?>

