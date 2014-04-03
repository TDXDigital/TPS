<?php
    session_start();

$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	//die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . '; username=' . $_SESSION["username"]);
    header("location: /");
}
else if($con){
	if(!mysql_select_db($_SESSION['DBNAME'])){header('Location: /');}
	$GENRE = "SELECT * from GENRE order by genreid asc";
	$GENRES = mysql_query($GENRE,$con);
	$genop = "<OPTION VALUE=\"%\">Select Genre</option>";
	while ($genrerow=mysql_fetch_array($GENRES)) {
        $GENid=$genrerow["genreid"];
        $genop.="<OPTION VALUE=\"" . $GENid . "\">". $GENid ."</option>";
    }
	$djsql="SELECT * from DJ order by djname";
    $djresult=mysql_query($djsql,$con);

    $djoptions="<option value=0>Any</option>";//<OPTION VALUE=0>Choose</option>";
    while ($djrow=mysql_fetch_array($djresult)) {
        $Alias=$djrow["Alias"];
        $name=$djrow["djname"];
        $djoptions.="<OPTION VALUE=\"".$Alias."\">" . $name . "</option>";
    }
?>

<!DOCTYPE HTML>
<html>
<head>
	<script type="text/javascript">
	
	function urlencodephp (str) {
		  // http://kevin.vanzonneveld.net
		  // +   original by: Philip Peterson
		  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		  // +      input by: AJ
		  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		  // +   improved by: Brett Zamir (http://brett-zamir.me)
		  // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		  // +      input by: travc
		  // +      input by: Brett Zamir (http://brett-zamir.me)
		  // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		  // +   improved by: Lars Fischer
		  // +      input by: Ratheous
		  // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
		  // +   bugfixed by: Joris
		  // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
		  // %          note 1: This reflects PHP 5.3/6.0+ behavior
		  // %        note 2: Please be aware that this function expects to encode into UTF-8 encoded strings, as found on
		  // %        note 2: pages served as UTF-8
		  // *     example 1: urlencode('Kevin van Zonneveld!');
		  // *     returns 1: 'Kevin+van+Zonneveld%21'
		  // *     example 2: urlencode('http://kevin.vanzonneveld.net/');
		  // *     returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
		  // *     example 3: urlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
		  // *     returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'
		  str = (str + '').toString();
		
		  // Tilde should be allowed unescaped in future versions of PHP (as reflected below), but if you want to reflect current
		  // PHP behavior, you would need to add ".replace(/~/g, '%7E');" to the following.
  		return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
		}
		
		
		function getCallsign(value_name){
			document.getElementById("SM").disabled = true;
			if(value_name==0){
				document.getElementById("callbox").innerHTML="<option>Not Set</option>";
				document.getElementById("airtime").disabled=true;
	   			document.getElementById("airdate").disabled=true;
	   			document.getElementById("brType").disabled=true;
	   			document.getElementById("s2").style="";
	   			document.getElementById("s3").style="";
	   			document.getElementById("s4").style="";
	   			document.getElementById("s5").style="";
	   			document.getElementById("s6").style="";
	   			document.getElementById("s7").style="";
	   			document.getElementById("s1").style="background-color:#CCFFFF";
				return;
			}
			else{
				
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
	   						document.getElementById("callbox").innerHTML=xmlhttp.responseText;
	   						document.getElementById("SM").disabled = false;
	   						document.getElementById("airtime").disabled=false;
	   						document.getElementById("airdate").disabled=false;
	   						//document.getElementById("airtime").disabled=false;
	   						document.getElementById("brType").disabled=false;
	   						document.getElementById("s2").style="";
	   						document.getElementById("s3").style="";
	   						document.getElementById("s4").style="";
	   						document.getElementById("s5").style="";
	   						document.getElementById("s6").style="";
	   						document.getElementById("s3").style="background-color:#CCFFFF";
	   						//setTimeout(1000);
	   						document.getElementById("s3").style="";
	   						document.getElementById("s5").style="background-color:#CCFFFF";
	   						document.getElementById("s6").style="background-color:#CCFFFF";
	   						
	   					}
	   					/*else{
	   						//alert(xmlhttp.status+" "+xmlhttp.readyState); //Debug
	   					}*/
	   				}
	   			xmlhttp.open("GET","AJAX/getcallsign.php?n="+urlencodephp(value_name),true);
	   			xmlhttp.send();
	   			document.getElementById("s1").style="";
	   			document.getElementById("s2").style="background-color:#CCFFFF";
	   			//wait(1000);
	   			
			}
		}
		
		// from http://www.javascriptkit.com/javatutors/createelementcheck2.shtml
		var datefield=document.createElement("input")
		datefield.setAttribute("type", "date")
		if (datefield.type!="date"){ //if browser doesn't support input type="date", load files for jQuery UI Date Picker
        	document.write('<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />\n')
        	document.write('<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"><\/script>\n')
        	document.write('<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"><\/script>\n') 
    	}
    	
    	if (datefield.type!="date"){ //if browser doesn't support input type="date", initialize date picker widget:
    		jQuery(function($){ //on document.ready
        		$('#airdate').datepicker(); // for individual date boxes
    		})
		}
		
		function RecVer(val)
		{
			if(val>0){
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
	   						document.getElementById("prdate").value=xmlhttp.responseText;
	   						if(val==2){
	   							oldate = document.getElementById("airdate").value;
	   							document.getElementById("airdate").value="1973-01-01";
	   							document.getElementById("airdate").readonly="readonly";
	   							document.getElementById("s1").style="";
	   							document.getElementById("s2").style="";
	   							document.getElementById("s3").style="";
	   							document.getElementById("s4").style="background-color:#CCFFFF";
	   							document.getElementById("s5").style="";
	   							document.getElementById("s6").style="";
	   						}
	   						else{
	   							document.getElementById("airdate").readonly=false;
	   							if(oldate!=""){
	   								document.getElementById("airdate").value=oldate;
	   							}
	   							document.getElementById("s1").style="";
	   							document.getElementById("s2").style="";
	   							document.getElementById("s3").style="";
	   							document.getElementById("s4").style="background-color:#CCFFFF";
	   							document.getElementById("s5").style="background-color:#CCFFFF";
	   							document.getElementById("s6").style="background-color:#CCFFFF";
	   						}
	   					}
	   				}
	   			xmlhttp.open("GET","AJAX/date.php",true);
	   			xmlhttp.send();
	   			
				document.getElementById("prdate").required = false;
				document.getElementById("prdate").disabled = false;
				//document.getElementById("prdate").value = dateval;
				
			}
			else{
				
				document.getElementById("prdate").value = "";
				document.getElementById("prdate").required = true;
				document.getElementById("prdate").disabled = true;
				document.getElementById("airdate").disabled=false;
				document.getElementById("s1").style="";
	   			document.getElementById("s2").style="";
	   			document.getElementById("s3").style="";
	   			document.getElementById("s4").style="";
	   			document.getElementById("s5").style="background-color:#CCFFFF";
	   			document.getElementById("s6").style="background-color:#CCFFFF";
				if(oldate!=""){
	   				document.getElementById("airdate").value=oldate;
	   			}
			}
		}
	</script>
<link rel="stylesheet" type="text/css" href="../../altstyle.css" />
<title>New DPL</title>
</head>
<body>
	<div class="topbar">
           User: <?php echo(strtoupper($_SESSION['fname'])); ?>
    </div>
	<div id="header">
		<a href="#"><img src="../../images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
	</div>
	<div id="top">
		<h2>New Program Log</h2>
	</div>
	<div id="content">
		<form name="search" method="POST" action="../p2insertEP.php">
		<table border="0" class="tablecss">
			<tr>
				<th id="s1" style="background-color:#CCFFFF">
					Program
				</th>
				<th style="width: 80px" id="s2">
					Callsign
				</th>
				<th id="s3">
					Broadcast Type
				</th>
				<th id="s4">
					Record Date
				</th>
				<th id="s5">
					Air Date
				</th>
				<th id="s6">
					Air Time (Not Record)
				</th>
				<th style="width:325px" id="s7">
					Description
				</th>
				
			</tr>
			<tr>
				<td>
					<select required title="Show Name" name="program" id="shownamebox" onchange="getCallsign(this.form.program.value)">
					<?php
					//<input name="name" type="text" size="25%"/>
					$program = "select * from program where active='1' order by programname";
        			$prog=mysql_query($program,$con);
			        $options="<OPTION VALUE=0>Select Your Show [REQUIRED]</option>";
			        while ($row=mysql_fetch_array($prog)) {
			            $name=$row["programname"];
			//            $callsign=$row["callsign"];
			//            $alias=$row["Alias"];
			            $options.="<OPTION VALUE=\"".$name."\">".$name."</option>";
        				}
					echo $options;
					?>
					</select>
				</td>
				<td>
					<!--<input name="callsign" type="text" id="callbox_old" readonly="readonly" />-->
					<select id="callbox" name="callsign">
						<option value="0">None Set</option>
					</select>
				</td>
				<td>
					<select name="brType" id="brType" disabled onchange="RecVer(this.form.brType.value)">
						<!--<option value="-1">Select</option>-->
						<option value="0">Live to Air</option>
						<option value="1">Pre Record</option>
						<option value="2">Timeless</option>
					</select>
				</td>
				<td>
					<input name="prdate" type="date" id="prdate" disabled/>
					
				</td>
				<td>
					<input name="user_date" type="date" id="airdate" disabled value="<?php echo date('Y-m-d'); ?>"/>
				</td>
				<td>
					<input name="user_time" type="time" id="airtime" disabled value="<?php echo date('H') . ":00"; ?>"/>
				</td>
				<td>
					<input name="Description" style="width:99%" type="text" maxlength="90" /> 
				</td>
			</tr>
			<!--<tr><th>Description</th><td colspan="5"><input type="text" size="100%" name="description"/></td></tr>-->
		</table>
		
		</div>
	<div id="foot">
		<table>
			<tr>
				<td>
				<input type="submit" id="SM" value="Submit" disabled="disabled"/></form></td><td>
				<button onClick="window.location.reload()">Reset</button></td><td>
				<form method="POST" action="../../masterpage.php"><input type="submit" value="Menu"/></form>
				</td>
				<td style="width: 100%; text-align: right;"><img src="../../images/mysqls.png" alt="MySQL Powered"/></td>
			</tr>
		</table>
	</div>
	<div id="content">
			<h4>Help</h4>
		<span>If you are doing a PreRecord or Timeless you must select a Record Date. <br/> Callsign (Station) will be retrieved based on show selected</span>
		
	</div>
	
<?php

}
else{
	echo 'ERROR!';
}
?>
</body>
</html>