<?php
    include_once '../TPSBIN/functions.php';
    include_once '../TPSBIN/db_connect.php';

/*
session_start();

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db("CKXU")){header('Location: ../login.php');} 
*/
?>
<!DOCTYPE HTML>
<head>
	<title>Playlist</title>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />
	<script src="http://code.jquery.com/jquery-1.8.2.js"></script>
    <script src="http://code.jquery.com/ui/1.9.0/jquery-ui.js"></script>
    <link rel="stylesheet" type="text/css" href="../altstyle.css" />
    
</head>
<html>
<body onload="load()">
	<script>
        var q=1,l=250;
        
        function update_qty(){
            var e = document.getElementById("qty");
            l = e.options[e.selectedIndex].text;
            load();
        }
        
        function loadnext(){
            if(q>0){
                q++
            }
            load();
        }
        function loadlast(){
            if(q>0){
                q--
            }
            load();
        }
        
	function load(){
		if(window.XMLHttpRequest)
			{// code for IE7+, Firefox, Chrome, Opera, Safari (www.w3Schools.com Source)
				xmlhttp=new XMLHttpRequest();
			}
			else
			{// code for IE6, IE5 (Not Supported)
   				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
   			}
   			xmlhttp.onreadystatechange=function()
   				{
   					if(xmlhttp.readyState==4 && xmlhttp.status==200){
   						document.getElementsByName("listCon")[0].innerHTML=xmlhttp.responseText;
   					}
   					else{
   						document.getElementsByName("listCon")[0].innerHTML="loading...";
   					}
   				}
   			xmlhttp.open("GET","AJAX/GetContent.php?p="+q+"&s="+l,true);
   			xmlhttp.send();
		document.getElementById("page").innerHTML="Page: "+q+"  | ";	
		}
                
	function CheckPlaylist(num){
		if(window.XMLHttpRequest)
			{// code for IE7+, Firefox, Chrome, Opera, Safari (www.w3Schools.com Source)
				xmlhttpcp=new XMLHttpRequest();
			}
			else
			{// code for IE6, IE5 (Not Supported)
   				xmlhttpcp=new ActiveXObject("Microsoft.XMLHTTP");
   			}
   			xmlhttpcp.onreadystatechange=function()
   				{
   					if(xmlhttpcp.readyState==4 && xmlhttp.status==200){
   						//document.getElementsByName("listCon")[0].innerHTML=xmlhttp.responseText;
   						if(xmlhttpcp.responseText!=""){
   							alert('This Playlist Already Exists!');
   						}
   					}
   					/*else{
   							alert('Return False 90');
   						}*/
   				}
   			xmlhttpcp.open("GET","AJAX/CheckPlaylist.php?num="+num,true);
   			xmlhttpcp.send();
			
		}
	function quickview(url){
		//use @ to differentiate
		newwindow=window.open(url,'name','height=800,width=800');
		if (window.focus) {newwindow.focus()}
		return false;		
	}
	</script>
	<div class="topbar">
           USER: <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="#"><img src="../<?php echo $_SESSION['logo'];?>" alt="Logo" /></a>
	</div>
	<div id="top">
		<h2>Playlist / Library Management</h2>
		
	</div>
	<div id="content" name="head">
		<!--<h3>New Playlist</h3>-->
		<form action="submitPlaylist.php" method="post" accept-charset="utf-8">
		<table>
            <thead>
			    <tr>
				    <th width="10%">Playlist #</th>
                    <th>Activate</th>
                    <th>Expires</th>
                    <th>Zone</th>
                    <th>Zone Number</th>
			    </tr>
            </thead>
            <tbody>
			<tr><td><input type="number" style="width:99%;" min="0" name="num" onchange="CheckPlaylist(this.form[0].num[].value)"/></td>
                <td><input type="date" style="width: 99%;" name="activate"/></td>
                <td><input type="date" style="width: 99%;" name="expire"/></td>
                <td><select name="ZoneCode"><optgroup label="Physical">
                    <option value="0">Primary</option>
                    <option value="1">Off Site</option>
                    <option value="2">Deep Archive</option>
                </optgroup>
                    <optgroup label="Digital">
                        <option value="10">Main Catalog</option>
                        <option value="11">Backup Catalog</option>
                    </optgroup>
                </select></td>
                <td><input type="number" min="0" step="1" name="ZoneNumber"/></td>
		<input type="hidden" name="source">
		<input type="hidden" name="change" value="false" >
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th width="25%" colspan="2">Artist</th><th width="25%" colspan="2">Album</th><th width="10%">CanCon</th><th width="10%">Label</th><th width="20%">Release Year</th>
                </tr>
            </thead>
            <tbody>
            <tr>
		<td colspan="2"><input type="text" style="width:99%;" name="artist" /></td>
		<td colspan="2"><input type="text" style="width:99%;" name="album" /></td>
		<td><select name="locale" style="width:99%;">
				<option value="International" >International</option>
				<option value="Country" >Canadian</option>
				<option value="Province" >Alberta</option>
				<option value="Local" >Local</option>
			</select></td>
			<td><select name="label" style="width:99%;">
                <?php
                    $conres = $mysqli->query("SELECT * FROM recordlabel order by Size, Name");
                    if($mysql->errno){
                        echo "<option>".mysql_error()."</option>";
                    }
                    while($row = mysqli_fetch_array($conres)){
                        $ID = $row['LabelNumber'];
                        $Name = $row['Name'];
                        echo "<option value='$ID' >".stripslashes($Name)."</option>";
                    }
                ?>
                <!--
				<option value="IL" >Indipendent</option>
				<option value="SL" >Generic Small</option>
				<option value="ML" >Generic Medium</option>
				<option value="LL" selected="selected">Generic Large</option>
                -->
			</select></td>
		<td><input type="number" style="width:99%;" min="0000" max="9999" step="1" name="year" /></td>
		</tr>
        </tbody>
            <tfoot>
			        <tr><td><input type="submit" value="submit"/></td></tr>
            </tfoot>
			</table>
			</form>
	</div>
	<div id="top">
		<h3>Edit</h3>
                <span><button value="NEXT" onclick="loadlast()"><< Previous</button>
                    <span id="page"></span>Display <select id="qty" onchange="update_qty()"><option value="100">100</option><option value="250" selected="selected">250</option><option value="500">500</option><option value="1000">1000</option></select>
                    <button value="NEXT" onclick="loadnext()">Next >></button></span>
	</div>
	<div id="content" name="listCon">
		<span>Error, Could not initiate AJAX</span><progress max="100"></progress>
	</div>
	
<?php
/*
}
else{
	echo 'ERROR!';
}
mysql_close($con);*/
?>
</body>
</html>