<?php
    session_start();
date_default_timezone_set($_SESSION['TimeZone']?:"UTC");

require_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."TPSBIN".DIRECTORY_SEPARATOR."functions.php";
require_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."TPSBIN".DIRECTORY_SEPARATOR."db_connect.php";
?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../../css/altstyle.css" />
<title>Traffic Management</title>
</head>
<html>
<body>
	<div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['fname'])); ?>
    </div>
	<div id="header">
		<a href="../../"><img src="<?php print("../../".$_SESSION['logo']); ?>" alt="logo"/></a>
	</div>
	<div id="top">
		<h2>Edit Commercial / Promo</h2>
	</div>
	<div id="content">
		<table border="0" class="tablecss">
			<tr>
				<th width="10px">

				</th>
				<th width="300px">
					Ad Name / Advertiser
				</th>
				<th width="150px">
					Ad Number
				</th>
				<th width="50px">
					Length
				</th>
				<th width="150px">
					Language
				</th>
				<th width="100px">
					Category
				</th>
				<th width="100px">
					Active
				</th>
				<th width="100px">
					Friend
				</th>
			</tr>

<?php
	$SQLA = "Select adverts.* from adverts where adverts.AdName LIKE '%" . $mysqli->real_escape_string(
		filter_input(INPUT_POST, 'name')) . "%' ";
	// build query
	if(isset($_POST['category'])){
		$SQLA .= "and adverts.Category LIKE '%" . $mysqli->real_escape_string(filter_input(INPUT_POST, 'category'))."%' ";
	}
	if(isset($_POST['Active'])){
		$SQLA .= "and adverts.Active LIKE '%" . $mysqli->real_escape_string(filter_input(INPUT_POST, 'Active'))."%' ";
	}
	if(isset($_POST['adnum'])){
		$SQLA .= "and adverts.AdId LIKE '%" . $mysqli->real_escape_string(filter_input(INPUT_POST, 'adnum'))."%' ";
	}
	if(isset($_POST['Friend'])){
		$SQLA .= "and adverts.Friend LIKE '%" . $mysqli->real_escape_string(filter_input(INPUT_POST, 'Friend'))."%' ";
	}
	$SQLA .= " order by  Category desc, AdId";

	$result = $mysqli->query($SQLA) or die($mysqli->error);
    if((string)$result->num_rows=="0"){
        echo '<tr><td colspan="100%" style="background-color:yellow;">';
        echo 'No Results Found';
        echo '</tr></td>';
        echo $SQLA;
    }
    else{
		//------------------------- START LOOP OF adverts ---------------------------------
		echo "<form name=\"row\" action=\"p3update.php\" method=\"POST\">";
		$count = 0;
		if($result->num_rows==1){
			$row = $result->fetch_row();
				header("location: p3update.php?resource=" . $row['AdId'] );
		}
		else{
			while($row=$result->fetch_array(MYSQLI_ASSOC)) {
                $labelr = "<label for=\"line" . $count . "\">" . $row['AdName'] . "</label>";
                echo "<tr";
                if ($count % 2) {
                    echo " style=\"background-color:#DAFFFF;\" ";
                }
                echo ">
                <td>";
                echo "<input type=\"radio\" name=\"postval\" required=\"true\" id=\"line" . $count . "\" value=\"" . $row['AdId'] . "\" /></td><td>";
                ++$count;

                $labelr .= "</td>
                <td>" . $row['AdId'] . "</td>
                <td>" . $row['Length'] . "</td>
                <td>" . $row['Language'] . "</td>
                <td>" . $row['Category'] . "</td>
                <td><input type=\"checkbox\" onclick=\"javascript: return false;\" ";
                if ($row['Active'] != 0) {
                    $labelr .= "checked";
                }
                $labelr .= " />";
                $labelr .= "</td>
                <td><input type=\"checkbox\" onclick=\"javascript: return false;\" ";
                if ($row['Friend'] != 0) {
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
	<div id="foot">
		<table>
			<tr>
				<td>
					<input type="submit" value="Select" /></form></td><td>
					<form action="p1update.php" method="POST">
						<input type="submit" value="Search"/></form></td><td>
				<input type="button" value="Reset" onClick="window.location.reload()"></td><td>
				<form method="POST" action="../../masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td width="100%" align="right"><img src="../../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>

</body>
</html>
