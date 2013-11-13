<?php
      session_start();

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: ../login.php');} // or die("<h1>Error ".mysql_errno() ."</h1><br />check access (privileges) to the SQL server db CKXU for this user <br /><br /><hr />Error details:<br />" .mysql_error() . "<br /><br /><a href=login.php>Return</a>");
        $sql="SELECT callsign, stationname from STATION order by callsign";
        $result=mysql_query($sql);

        $options="<OPTION VALUE=0>Choose</option>";
        while ($row=mysql_fetch_array($result)) {
            $name=$row["stationname"];
            $callsign=$row["callsign"];
            $options.="<OPTION VALUE=\"$callsign\">".$name."</option>";
        }

}
else{
	echo 'ERROR!';
}
?>

<head>
<link rel="stylesheet" type="text/css" href="../phpstyle.css" />
<title>Station Insertion</title>
</head>
<html>
<body>
      <div class="topbar">
           <a class="right" href="../logout.php"> Logout </a>Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>
           <img src="../<?php echo $_SESSION['logo'];?>" alt="Logo"/>

	<h2>Update Station Information</h2><br />
	<hr />
        <form action="dj/p2updatestation.php" name="callsign" method="POST">
        Select Station from the list: <select name="callsign">
        <?php echo $options;?>
        </select>
        <input type="submit" value="Submit" />
        </form>
	<br /><br /><br />

	<hr />
        <p><a href="../logout.php" align='center' >Logout</a> <a href="../masterpage.php">Return</a><br/></p><p>
        <img src="../images/mysqls.png" alt="MySQL Powered"> Stream Server status: <span id="cc_stream_info_server"></span></p>
<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/system/streaminfo.js"></script>
<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/js.php/zuomcwvv/streaminfo/rnd0"></script>
</body>
</html>
