<?php
    session_start();

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: /user/login');} 
		
		if(!isset($_POST['PAGIN'])){
			$PAGIN = 50;
		}
        else if(isset($_GET['PAGIN']))
        {
            $PAGIN = addslashes($_GET['PAGIN']);
        }
		else{
			$PAGIN = addslashes($_POST['PAGIN']);
		}
		
		if(!isset($_POST['page'])){
			$pagenum = 1;
		}
        else if(isset($_GET['page'])){
            $pagenum = addslashes($_GET['page']);
        }
		else{
			$pagenum = addslashes($_POST['page']);
		}
        // Get POST or GET Values, have POST take priority
        if($_POST['Playlist']!=''){
            $playlist = addslashes($_POST['Playlist']);
        }
        elseif(isset($_GET['playlist'])){
            $playlist = addslashes($_GET['playlist']);
        }
        else{
            $playlist="";
        }
        if($_POST['from']!=''){
            $from = addslashes($_POST['from']);
        }
        elseif(isset($_GET['from'])){
            $from = addslashes($_GET['from']);
        }
        else{
            $from="";
        }
        if($_POST['to']!=''){
            $to = addslashes($_POST['to']);
        }
        elseif(isset($_GET['to'])){
            $to = addslashes($_GET['to']);
        }
        else{
            $to="";
        }
		//echo $pagenum;
        //echo $PAGIN;
		$SQL = "SELECT * from song";
		$SQLC = "SELECT count(songid) from song";
		
		$SQLBUFF = "";
		$CONT = false;
		
		
		if($playlist!="")
		{
			$CONT = true;
			$SQLBUFF.=" playlistnumber LIKE '$playlist' ";
		}
		
		if($_POST['Artist']!="")
		{
			if($CONT == true){
				$SQLBUFF .= " and ";
			}
			else{
				$CONT = true;
			}
			$SQLBUFF.=" artist like '%".addslashes($_POST['Artist'])."%' ";
		}
		
		if($_POST['Album']!="")
		{
			if($CONT == true){
				$SQLBUFF .= " and ";
			}
			else{
				$CONT = true;
			}
			$SQLBUFF.=" album like '%".addslashes($_POST['Album'])."%' ";
		}
		
		if($_POST['Category']!="")
		{
			if($CONT == true){
				$SQLBUFF .= " and ";
			}
			else{
				$CONT = true;
			}
			$SQLBUFF.=" category like '%".addslashes($_POST['Category'])."%' ";
		}
		
		if($_POST['Title']!="")
		{
			if($CONT == true){
				$SQLBUFF .= " and ";
			}
			else{
				$CONT = true;
			}
			$SQLBUFF.=" title like '%".addslashes($_POST['Title'])."%' ";
		}

        // HANDLE DATE RESTRICTIONS
		if($from!="" && $to!="")
		{
			if($CONT == true){
				$SQLBUFF .= " and ";
			}
			else{
				$CONT = true;
			}
			$SQLBUFF.=" date between '$from' and '$to' ";
		}
        elseif($from!="" && $to==""){
            if($CONT == true){
				$SQLBUFF .= " and ";
			}
			else{
				$CONT = true;
			}
			$SQLBUFF.=" date >= '$from'  ";
        }
        elseif($to!=""){
            if($CONT == true){
				$SQLBUFF .= " and ";
			}
			else{
				$CONT = true;
			}
			$SQLBUFF.=" date <= '$to' ";
        }

		if($_POST['option']=="Playlist")
		{
			if($CONT == true){
				$SQLBUFF .= " and ";
			}
			else{
				$CONT = true;
			}
			$SQLBUFF.=" playlistnumber is not NULL ";
		}
		else if ($_POST['option']=="Exclusive"){
			if($CONT == true){
				$SQLBUFF .= " and ";
			}
			else{
				$CONT = true;
			}
			$SQLBUFF.=" playlistnumber is NULL ";
		}
		// Does not need else, if standard, then return both
		if($CONT == true){
			$SQL .= " where " . $SQLBUFF ;
			$SQLC .= " where " . $SQLBUFF;
		}
		
		
		//echo $SQLC;
		$numrow = mysql_fetch_array(mysql_query($SQLC));
		//echo $numrow['count(songid)'];
		$last = ceil($numrow['count(songid)']/$PAGIN);
        if($last==0){
            $last=1;
        }

		if($pagenum>$last){
            $pagenum=$last;
		    
		}
        $SQL .= " limit " . ($pagenum-1) * $PAGIN . "," . $PAGIN ;

		$data = mysql_query($SQL);
?>

<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../altstyle.css" />
<title>Search</title>
</head>
<body class="hasstatictop">
	<script>
	function quickview(url){
		//use @ to differentiate
		newwindow=window.open(url,'name','height=800,width=800');
		if (window.focus) {newwindow.focus()}
		return false;		
	}
	</script>
	<div class="statictop">
           <span title="User Name" style="float: left; margin: 1px 3px 1px 3px;">Welcome <?php echo(strtolower($_SESSION['account'])); ?></span>
        <span title="Pages" style="float: next">
<?php /*
if(($pagenum - 2) > 0){
    echo "<a href='?page=".($pagenum-2)."&pagin=$PAGIN'>".($pagenum-2)."</a>&nbsp";
}
if(($pagenum - 1) > 0){
    echo "<a href='?page=".($pagenum-1)."&pagin=$PAGIN'>".($pagenum-1)."</a>&nbsp";
}*/
 echo $pagenum ." / ".$last; 
 /*
if(($pagenum + 1) < $last){
    echo "&nbsp<a href='?page=".($pagenum+1)."&pagin=$PAGIN'>".($pagenum+1)."</a>&nbsp";
}
if(($pagenum + 2) < $last){
    echo "<a href='?page=".($pagenum+2)."&PAGIN=$PAGIN'>".($pagenum+2)."</a>";
}*/
?>
        </span>
        <div title="Jump to Page" style="float: right">
            <form method="POST" action="p2SongSearch.php">
                <label for="pagenum">Page Number</label>
                <input type="number" min="1" max="<?php echo $last;?>" id="pagenum" name="page" value="<?php echo $pagenum?>" />
                <label for="PAGIN">Results Per Page</label>
                <select name="PAGIN" id="PAGIN">
                    <option value="15" <?php if($PAGIN=='15'){echo "selected";}?>>15</option>
                    <option value="30" <?php if($PAGIN=='30'){echo "selected";}?>>30</option>
                    <option value="50" <?php if($PAGIN=='50'){echo "selected";}?>>50</option>
                    <option value="100"<?php if($PAGIN=='100'){echo "selected";}?>>100</option>
                    <option value="1000"<?php if($PAGIN=='1000'){echo "selected";}?>>1000</option>
                </select>
                <?php
                    // IF THERE HAS BEEN OTHER VARIABLES SET PASS THEM ALONG
                    //CHECK FOR VARIABLE, IF isset() = true echo hidden field
                    // Could be done with a array of names and loop (I know this is not best)
                    if(isset($_POST['Playlist'])){
                        echo "<input type='hidden' value='".$_POST['Playlist']."' name='Playlist'/>";
                    }
                    if(isset($_POST['Artist'])){
                        echo "<input type='hidden' value='".$_POST['Artist']."' name='Artist'/>";
                    }
                    if(isset($_POST['Album'])){
                        echo "<input type='hidden' value='".$_POST['Album']."' name='Album'/>";
                    }
                    if(isset($_POST['Title'])){
                        echo "<input type='hidden' value='".$_POST['Title']."' name='Title'/>";
                    }
                    if(isset($_POST['Composer'])){
                        echo "<input type='hidden' value='".$_POST['Composer']."' name='Composer'/>";
                    }
                    if(isset($_POST['Language'])){
                        echo "<input type='hidden' value='".$_POST['Language']."' name='Language'/>";
                    }
                    if(isset($_POST['Category'])){
                        echo "<input type='hidden' value='".$_POST['Category']."' name='Category'/>";
                    }
                    if(isset($_POST['program'])){
                        echo "<input type='hidden' value='".$_POST['program']."' name='program'/>";
                    }
                    if(isset($_POST['option'])){
                        echo "<input type='hidden' value='".$_POST['option']."' name='option'/>";
                    }
                    if(isset($_POST['CC'])){
                        echo "<input type='hidden' value='".$_POST['CC']."' name='CC'/>";
                    }
                    if(isset($_POST['Hit'])){
                        echo "<input type='hidden' value='".$_POST['Hit']."' name='Hit'/>";
                    }
                    if(isset($_POST['Ins'])){
                        echo "<input type='hidden' value='".$_POST['Ins']."' name='Ins'/>";
                    }
                    if(isset($_POST['from'])){
                        echo "<input type='hidden' value='".$_POST['from']."' name='from'/>";
                    }
                    if(isset($_POST['to'])){
                        echo "<input type='hidden' value='".$_POST['to']."' name='to'/>";
                    }
                ?>
                <input type="submit" value="GO!" />
            </form>
        </div>
    </div>
	<div id="header">
		<a href="../masterpage.php"><img src="../images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
	</div>
	<div id="top">
		<h2>Search Results - Song</h2>
	</div>
	<div id="content">
<?php
    if(mysql_error()){
        echo "<div style='width:100%;'>Error ".mysql_errno()." ".mysql_error()."</div>";
        echo "<div style='width:100%;'>Query: ".$SQL."</div>";
    }
    //echo "<div style='width:100%;'>Query: ".$SQL."</div>";
		echo "<table><tr><th>Playlist</th><th>Time</th><th>Title</th><th>Artist</th><th>Album</th><th>CC</th><th>Hit</th><th>Ins</th><th>Log</th></tr>";
		$ROW = 0;
		while($row = mysql_fetch_array($data)){
			echo "<tr";
			if($ROW%2){
				echo " style='background-color:#DAFFFF;' ";
			} 
			$ROW++;
			echo "><td>". $row['playlistnumber'] . "</td><td>" . $row['time'] . "</td><td>" . $row['title'] . "</td><td>";
			echo $row['artist'] . "</td><td>" . $row['album'] . "</td><td>".$row['cancon']."</td><td>".$row['hit']."</td><td>".$row['instrumental']."</td><td>";
			echo "<button type=\"button\" onclick=\"javascript:quickview('../Episode/quickview.php?args=".$row['programname']."@".$row['date']."@".$row['starttime']."@".$row['callsign']."')\">View</button>
			<button type=\"button\" href=\"../Episode/EPV3/logs.php?p=".$row['programname']."&t=".$row['starttime']."&d=".$argc['date']."&c=".$row['callsign']."\" onclick=\"javascript:window.open('../Episode/EPV3/logs.php?p=".$row['programname']."&t=".$row['starttime']."&d=".$argc['date']."&c=".$row['callsign']."','popUpWindow','height=800,width=1350,left=10,top=10,,scrollbars=yes,menubar=no'); return false;\">Modify</button>
			</td></tr>"; 
		}
		echo "</table>";
		?>
		</div>
	<div id="foot">
		<table>
			<tr>
				<td colspan="100%">
					<?php
						echo "Pages : " . $last;
					?>
				</td>
			</tr>
			<tr>
				<td>
				<input type="button" value="Search Settings" onClick="window.location.href='p1SongSearch.php'"></td><td>
				<input type="button" value="Reset" onClick="document.forms['General'].reset()"></td><td>
				<form method="POST" action="../masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td style="width:100%; text-align:right;"><img src="../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>
	
<?php

}
else{
	echo 'ERROR!';
}
?>
</body>
</html>