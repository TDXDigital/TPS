 <!--
Design by Bryant Smith
http://www.bryantsmith.com
http://www.aszx.net
email: templates [-at-] bryantsmith [-dot-] com
Released under Creative Commons Attribution 2.5 Generic.  In other words, do with it what you please; but please leave the link if youd be so kind :)

Name       : The Slant
Version    : 1.0
Released   : 2009-07-25
-->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="refresh" content="60" />
<link rel="stylesheet" type="text/css" href="/style.css" />
<title>CKXU Intranet Home</title>
</head>
<body>
    <div id="page">

        <div id="header">
        	<h1><a href="http://ckxu.com"><img src="/images/CKXU_Logo_TL.png" border=0 alt="CKXU 88.3 FM"/></a><br /></h1>
            <ul>
           	   	<li><a href="/index.php">Home</a></li>
               	<li><a href="login.php" >DPL</a></li>
                <li><a href="training/training.html" >Training</a></li>
                <li><a href="http://www.allmusic.com/" target="parent">AllMusic</a></li>
                <li><a href="http://142.66.48.57:8000/logged/" target="parent">Logged Shows</a></li>
            </ul>
        </div>

        <div id="main">

        	<div class="main_top">
            	<h1>Notice Board</h1>
            </div>

           	<div class="main_body">
       	        <!-- Search Google -->
                    <center>
                        <FORM method=GET action="http://www.google.com/search">
                        <input type=hidden name=ie value=UTF-8>
                        <input type=hidden name=oe value=UTF-8>
                        <TABLE bgcolor="#FFFFFF"><tr><td>
                        <A HREF="http://www.google.com/">
                        <IMG SRC="images/GoogleS.png"
                        border="0" ALT="Google" align="absmiddle"></A>
                        <INPUT TYPE=text name=q size=25 maxlength=255 value="">
                        <INPUT type=submit name=btnG VALUE="Google Search">
                        </td></tr></TABLE>
                        </FORM>
                 </center>
                 <!-- Search Google -->
                <br />

                <h2>Streaming Server information</h2>
                        <p>Current RDS: <a href="http://cent4.serverhostingcenter.com/tunein.php/zuomcwvv/tunein.pls" id="cc_stream_info_song">Loading...</a><br />
                           Stream title: <span id="cc_stream_info_title"></span><br />
                           Bit rate: <span id="cc_stream_info_bitrate"></span><br />
                           Current listeners: <span id="cc_stream_info_listeners"></span><br />
                           Maximum listeners: <span id="cc_stream_info_maxlisteners"></span><br />
                           Server status: <span id="cc_stream_info_server"></span><br />
                </p>
                <?php
                     $con = mysql_connect('localhost','postlist','example');
                     if($con){
                       //echo "<h2> News </h2>";
                       if(!mysql_select_db("posts"))
                       {
                         echo "<p style=\"background-color:red; font:white\">error seting database to retrieve posts</p>";;
                       }
                       else
                       {
                         $postlist = mysql_query("select * from news where Hidden='0' order by postnum DESC",$con);
                         while($row=mysql_fetch_array($postlist)){
                           echo "<h2>" . $row['Header'] . "</h2>";
                           echo "<p>" . $row['Content'] . "<br />";
                           echo "<sup>Author: " . $row['Author'] . "</sup></p>";
                         }
                       }
                     }
                     else{
                       echo "<p style=\"background-color:red; font:white\">error connecting to database to retrieve posts</p>";
                     }
                    mysql_close($con);
                ?>
            </div>
           	<div class="main_bottom"></div>

        </div>


        
        <div id="footer">
        <p>Copyright 2012 James Oliver. Permission Granted to CKXU 88.3 FM</p>
        </div>

        </div>
        
<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/system/streaminfo.js"></script>
<script language="javascript" type="text/javascript" src="http://cent4.serverhostingcenter.com/js.php/zuomcwvv/streaminfo/rnd0"></script>

</body>
</html>
