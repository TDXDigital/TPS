<?php
//"select * from adverts left join song on (adverts.AdName = song.title and song.category='51' and song.date between '2012-08-24' and '2012-08-31')"
    session_start();
    error_reporting(E_ERROR);

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: /login.php');}
	
    }
else{
	echo 'ERROR!';
}

$WEEKS = -52;
if(isset($_GET['w'])){
	$WEEKS = -addslashes($_GET['w']);
}
$DateDisplayFormat = "Y/m/d";
$show = "%";
?>

<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../altstyle.css" />
<title>Statistics</title>
<!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
         var data_Ad = google.visualization.arrayToDataTable([
          ['Week', 'Minutes(51)'],
        <?php
        $CONTROL = $WEEKS; // Weeks to retrieve
        $NEXT = $CONTROL+1;
        for($CONTROL; $CONTROL < 0 ; $CONTROL++){
	        $SQL_ADMin="select sum(length)/60 from song left join adverts on (adverts.AdName = song.title and song.category='51' and song.date between '".
	        date("Y-m-d",strtotime("yesterday ". $CONTROL ." weeks"))
	        ."' and '".
	        date("Y-m-d",strtotime("yesterday ". $NEXT++ ." weeks"))
	        ."')";
			//echo $SQL_ADMin;
			if(!$ADminArr=mysql_query($SQL_ADMin)){
				array_push($error,mysql_errno() . " - " . mysql_error());
				//echo mysql_errno();
				//$ADmin = mysql_fetch_array($ADminArr);
				echo "['".$CONTROL."', 0 ],
				";
			}
			else{
				$ADmin = mysql_fetch_array($ADminArr);
				echo "['".date($DateDisplayFormat,strtotime("yesterday ". $CONTROL ." weeks"))."', ".$ADmin['sum(length)/60']."],
				";
			}
		}
        ?>
		]);
        var options_Ad = {
          title: 'Weekly Advertising Minutes'
        };

		var data_CC = google.visualization.arrayToDataTable([
          ['Week', 'Category 2 Canadian Content Percentage', 'Category 3 Percentage' , 'Category 3 Canadian Content Percentage'],
        <?php
        $CONTROL = $WEEKS; // Weeks to retrieve
        $NEXT = $CONTROL+1;
        for($CONTROL; $CONTROL < 0 ; $CONTROL++){/*
        	$SQCount2="select count(songid) from song where category like '2%' and song.date between '".
	        date("Y-m-d",strtotime("yesterday ". $CONTROL ." weeks"))
	        ."' and '".
	        date("Y-m-d",strtotime("yesterday ". $NEXT ." weeks"))
	        ."'";
			$Count2 = mysql_fetch_array(mysql_query($SQCount2));
			
	        $SQCountCC2="select count(songid) from song where cancon='1' and category like '2%' and song.date between '".
	        date("Y-m-d",strtotime("yesterday ". $CONTROL ." weeks"))
	        ."' and '".
	        date("Y-m-d",strtotime("yesterday ". $NEXT ." weeks"))
	        ."'";
	        $CountCC2 = mysql_fetch_array(mysql_query($SQCountCC2));
			
			$SQCountCC3="select count(songid) from song where cancon='1' and category like '3%' and song.date between '".
	        date("Y-m-d",strtotime("yesterday ". $CONTROL ." weeks"))
	        ."' and '".
	        date("Y-m-d",strtotime("yesterday ". $NEXT ." weeks"))
	        ."'";
			$CountCC3 = mysql_fetch_array(mysql_query($SQCountCC3));
			
			$SQCount3="select count(songid) from song where category like '3%' and song.date between '".
	        date("Y-m-d",strtotime("yesterday ". $CONTROL ." weeks"))
	        ."' and '".
	        date("Y-m-d",strtotime("yesterday ". $NEXT ." weeks"))
	        ."'";
			$Count3 = mysql_fetch_array(mysql_query($SQCount3));
			
			$SQCountTotal="select count(songid) from song where category not like '5%' and category not like '4%' and song.date between '".
	        date("Y-m-d",strtotime("yesterday ". $CONTROL ." weeks"))
	        ."' and '".
	        date("Y-m-d",strtotime("yesterday ". $NEXT ." weeks"))
	        ."'";
			//echo $SQCountTotal;
			$CountTotal = mysql_fetch_array(mysql_query($SQCountTotal));
			
			$CCpercthree = $Count3['count(songid)']/$CountCC3['count(songid)'];
			$Percthree = $CountTotal['count(songid)']/$Count3['count(songid)'];
			$CCperctwo = $Count2['count(songid)'] / $countCC2['count(songid)'];
			*/
		
			$SQ_PER_TWO = "SELECT (SELECT count(songid) from song where cancon='1' and category like '2%' and song.date between '".
	        date("Y-m-d",strtotime("yesterday ". $CONTROL ." weeks"))
	        ."' and '".
	        date("Y-m-d",strtotime("yesterday ". $NEXT ." weeks"))
	        ."') / (SELECT count(songid) from song where category like '2%' and song.date between '".
	        date("Y-m-d",strtotime("yesterday ". $CONTROL ." weeks"))
	        ."' and '".
	        date("Y-m-d",strtotime("yesterday ". $NEXT ." weeks"))
	        ."')*100 AS result";
			
			$PER_TWO = mysql_fetch_array(mysql_query($SQ_PER_TWO));
			
			
			$SQ_PER_THREE = "SELECT (SELECT count(songid) from song where cancon='1' and category like '3%' and song.date between '".
	        date("Y-m-d",strtotime("yesterday ". $CONTROL ." weeks"))
	        ."' and '".
	        date("Y-m-d",strtotime("yesterday ". $NEXT ." weeks"))
	        ."') / (SELECT count(songid) from song where category like '3%' and song.date between '".
	        date("Y-m-d",strtotime("yesterday ". $CONTROL ." weeks"))
	        ."' and '".
	        date("Y-m-d",strtotime("yesterday ". $NEXT ." weeks"))
	        ."') * 100 AS result";
			
			$PER_THREE = mysql_fetch_array(mysql_query($SQ_PER_THREE));
			
			$SQ_TOT_THREE = "SELECT (SELECT count(songid) from song where category like '3%' and song.date between '".
	        date("Y-m-d",strtotime("yesterday ". $CONTROL ." weeks"))
	        ."' and '".
	        date("Y-m-d",strtotime("yesterday ". $NEXT ." weeks"))
	        ."') / (SELECT count(songid) from song where (category not like '5%' or category not like '4%' or category not like
			'1%' ) and song.date between '".
	        date("Y-m-d",strtotime("yesterday ". $CONTROL ." weeks"))
	        ."' and '".
	        date("Y-m-d",strtotime("yesterday ". $NEXT ." weeks"))
	        ."') * 100 AS result";
			
			$TOT_THREE = mysql_fetch_array(mysql_query($SQ_TOT_THREE));
			
			echo "['".date($DateDisplayFormat,strtotime("yesterday ". $CONTROL ." weeks"))."', ".$PER_TWO['result'].", ".$TOT_THREE['result']." , ".$PER_THREE['result']."],
			";
			$NEXT++;
			}
        ?>
		]);
        var options_CC = {
          title: 'Musical Category Statistics'
        };

        var chart_Ad = new google.visualization.LineChart(document.getElementById('AdMinutes_div'));
        chart_Ad.draw(data_Ad, options_Ad);
        
        var chart_CC = new google.visualization.LineChart(document.getElementById('CanCon_div'));
        chart_CC.draw(data_CC, options_CC);
      }
    </script>
</head>
<html>
<body>
	<?php
		if($_GET['m']=0){
			echo "<div id=\"AdMinutes_div\" style=\"width: 1000px; height: 300px; margin: 0 auto;\"></div>
    	<div id=\"CanCon_div\" style=\"width: 1000px; height: 500px; margin: 0 auto;\"></div>";
		} else {?>
		
	<div class="topbar">
           User: <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="#"><img src="../images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
	</div>
	<div id="top">
		<h2>Live Programming Statistics</h2>
	</div>
	<div id="content">
		<form name="search" method="POST" action="p2ReqAd.php">
		<table border="0" class="tablecss">
			<tr>
				<th colspan='2'>
					<?php
					echo "Reporting Period : $WEEKS Weeks as of yesterday";
					?>
				</th>
			</tr>
		</table>
		</div>
		<!--Div that will hold the pie chart-->
    	<div id="AdMinutes_div" style="width: 1000px; height: 300px; margin: 0 auto;"></div>
    	<div id="CanCon_div" style="width: 1000px; height: 500px; margin: 0 auto;"></div>
	<div id="foot">
		<table>
			<tr>
				<td>
				<!--<input type="submit" value="Update"/></form></td><td>-->
				<!--<input type="button" value="Refresh" onClick="window.location.reload()"></td><td>-->
				<input type="button" value="Menu" onclick="window.location.href='../masterpage.php'" />
				</td>
				<td width="100%" align="right"><img src="../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>
	<?php 	
		}
	?>
</body>
</html>