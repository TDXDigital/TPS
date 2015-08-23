<?php
    session_start();
    
    require '../TPSBIN/functions.php';
    require '../TPSBIN/db_connect.php';
?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../altstyle.css" />
<title>TPS Administration</title>
</head>
<html>
<body>
	<div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['fname'])); ?>
    </div>
	<div id="header">
		<a href="../masterpage.php"><img src="<?php print "../".$_SESSION['logo']; ?>" alt="Logo" /></a>
	</div>
	<div id="top">
		<h2>Edit Program Advanced Search</h2>
	</div>
	<div id="content">
		<table border="0" class="tablecss">
			<tr>
				<th width="10px">
					
				</th>
				<th width="290px">
					Program Name
				</th>
				<th width="100px">
					Genre
				</th>
				<th width="50px">
					Length
				</th>
				<th width="150px">
					Syndicate
				</th>
				<th width="300px">
					Hosts
				</th>
				<th width="100px">
					Callsign
				</th>
				<th width="100px">
					Active
				</th>
			</tr>

<?php
    $GENRE = "SELECT * from GENRE order by genreid asc";
    $GENRES = $mysqli->query($GENRE);
    $genop = "<OPTION VALUE=\"%\">Select Genre</option>";
    while ($genrerow=$GENRES->fetch_array(MYSQLI_ASSOC)) {
    $GENid=$genrerow["genreid"];
    $genop.="<OPTION VALUE=\"" . $GENid . "\">". $GENid ."</option>";
    }
    $djsql="SELECT * from DJ order by djname";
    $djresult=$mysqli->query($djsql);

    $djoptions="<option value=\"%\">Any Host</option><option value=\"0\">None</option>";//<OPTION VALUE=0>Choose</option>";
    while ($djrow=$djresult->fetch_array()) {
        $Alias=$djrow["Alias"];
        $name=$djrow["djname"];
        $djoptions.="<OPTION VALUE=\"" . $Alias . "\">" . $name . "</option>";
    }
    $pgm_name = filter_input(INPUT_POST,'name',FILTER_SANITIZE_STRING);
    $SQLA = "Select PROGRAM.* from PROGRAM where program.programname LIKE '%$pgm_name%' ";
    // build query
    if(isset($_POST['callsign'])){
            $SQLA .= "and program.callsign LIKE '%" . addslashes($_POST['callsign']) . "%' ";
    }
    /*if(isset($_POST['dj1'])){
            if($_POST['dj1']!='0'){
                    $SQLA .= "and performs.Alias LIKE '" . addslashes($_POST['dj1']) . "' ";
            }
    }*/
    /*if(isset($_POST['dj2'])){
            if($_POST['dj2']!='0')
            {
                    $SQLA .= "and performs.CoAlias LIKE '" . addslashes($_POST['dj2']) . "' ";
            }
    }*/
    if(isset($_POST['length'])){
            $SQLA .= "and program.length LIKE '%" . addslashes($_POST['length']) . "%' ";
    }
    if(isset($_POST['syndicate'])){
            $SQLA .= "and program.syndicatesource LIKE '%" . addslashes($_POST['syndicate']) . "%' ";
    }
    if(isset($_POST['genre'])){
            $SQLA .= "and program.genre LIKE '" . addslashes($_POST['genre']) . "' ";
    }
    $SQLA .= " order by active DESC,programname";

    $result = $mysqli->query($SQLA) or die($mysqli->error);
    if($result->num_rows==0){
      echo '<tr><td colspan="100%" style="background-color:yellow;">';
      echo 'No Results Found';
      echo '</tr></td>';
                  echo $SQLA;
    }
    else{

        //------------------------- START LOOP OF PROGRAMS ---------------------------------
        echo "<form name=\"row\" action=\"p3advupdate.php\" method=\"POST\">";
        $count = 0;
        if($result->num_rows==1){
                $row = $result->fetch_array(MYSQL_ASSOC);
                        header("location: p3advupdate.php?resource=" . $row['programname'] . "@" . $row['callsign']);
        }
        else{
            while($row=$result->fetch_array(MYSQL_ASSOC)) {
                $labelr="<label for=\"line".$count."\">".$row['programname']."</label>";      	
                echo "<tr";
                if($count%2){
                        echo " style=\"background-color:#DAFFFF;\" ";
                }
                echo">
                <td>";
                echo "<input type=\"radio\" name=\"postval\" required=\"true\" id=\"line".$count."\" value=\"".$row['programname']."@&".$row['callsign']."\" /></td><td>";	
                ++$count;

                $labelr.= "</td>
                <td>" . $row['genre']. "</td>
                <td>" . $row['length']. "</td>
                <td>" . $row['syndicatesource'] ."</td>
                <td>";
                //echo "<input name=\"syndicate\" value=\"" . $row['syndicatesource'] . "\" hidden />";
                $SQDJ = "select Alias from PERFORMS where programname=\"" . addslashes($row['programname']) . "\" and callsign=\"" . addslashes($row['callsign']) . "\"";
                if(!($perfres = $mysqli->query($SQDJ))){
                        echo $mysqli->error;
                }
                else{
                    $alias=$perfres->fetch_array(MYSQLI_ASSOC);				
                    $labelr .= $alias['Alias'];
                    while($alias=$perfres->fetch_array(MYSQLI_ASSOC)){
                            $labelr .= ", " . $alias['Alias'];
                    }
                }
                $labelr .= "</td>
                <td>".  $row['callsign'] . "</td>
                <td><input type=\"checkbox\" onclick=\"javascript: return false;\" ";
                if($row['active']!=0){
                    $labelr .= "checked";
                }
                $labelr .= " />";
                $labelr .= "</td></tr>";
                echo $labelr;
            }
        }
    }
?>
</table>
		
		</div>
    <div style="height: 90px;">&nbsp;</div>
	<div id="foot"  style="bottom: 0; position: fixed; height: 50px; width: 100% ">
		<table>
			<tr>
				<td>
					<input type="submit" value="Select" /></form></td><td>
				<input type="button" value="Reset" onClick="window.location.reload()"></td><td>
                <form action="p2advupdate.php" method="POST">
                    <input type="text" name="name" placeholder="Program Name"/></td><td>
				    <input type="submit" value="Quick Search"/>
                </form></td><td>
				<form method="POST" action="../masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td width="100%" align="right"><img src="../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>

</body>
</html>
