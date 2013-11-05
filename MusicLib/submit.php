<script type="text/javascript" src="/js/jquery-1.7.2.js"></script>
<script>
<!--
	function myFunction(){
		//document.getElementById("table1").innerHTML="<img src="/images/GIF/Processing.gif" alt="Processing" />"
		alert("Notice: Uploading files can take some time please be patient as the system uploads, it is not frozen \n\n Upon Confirmation the files(s) will be sent");
	}
//-->
</script>

<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="/altstyle.css" />
<title>Digital File Submission</title>
</head>
<html>
	<body>
	<div id="header" >
		<a href="/masterpage.php"><img src="/images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
	</div>
	<div id="top">
		<h2>Digital Music Submission</h2>
	</div>		
	<div id="content">		
		<h4>File Limitations</h4><p>
		Please note the restrictions on files we accept for our digital library, these are to ensure the viability of the system in future years.<br/>
		<table border="1" style="text-align: center" id="table1" ><tr>
			<td colspan="100%">Accepted file types</td>
		</tr><tr>
			<th width="150px">Format</th><th width="150px">Low Threshold</th><th width="150px">ideal</th>
		</tr><tr>
			<td>MP3 (CBR)</td><td>192 kbps</td><td>320 kbps</td>
		</tr><tr>
			<td>MP3 (VBR)</td><td>V2</td><td>V0</td>
		</tr><tr>
			<td>FLAC</td><td>N/A</td><td>Level 8 <br />(16 bit, 44.1 kHz)</td>
		</tr></table>
	
		<br />
	 <form action="upload.php" name="uploader" method="post" enctype="multipart/form-data">
	 <label for="file">File #1:</label>
	 <input type="file" name="file[]" id="file" size="50"/><br/> 
	 <label for="file">File #2:</label>
	 <input type="file" name="file[]" id="file" size="50"/><br/>
	 <label for="file">File #3:</label> 
	 <input type="file" name="file[]" id="file" size="50"/><br/>
	 <label for="file">File #4:</label> 
	 <input type="file" name="file[]" id="file" size="50"/><br/>
	 <label for="file">File #5:</label> 
	 <input type="file" name="file[]" id="file" size="50"/><br/> 
	 <input type="submit" name="submit" value="Submit" onclick="myFunction()"/>
	 </form>
	</div>
			
 </body>
 </html>