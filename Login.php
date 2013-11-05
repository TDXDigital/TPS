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

<?php
      session_destroy();
	  echo "<span>This page will forward automatically</span>";
	  echo"<script>
				alert(\"Login Failed\");
				window.location.href=\"http://ckxuradio.su.uleth.ca/index.php/digital-program-logs\";
			</script>";
		//die('<a href="http://ckxuradio.su.uleth.ca/index.php/digital-program-logs" >Return to Login</a>');
	  //header('Location: /index.php/digital-program-logs');
/*
if(isset($_POST['submit']))
{
  $uname=$_POST['name'];
  $pass=$_POST['pass'];

  $salt = substr($uname, 0, 2);
  $encrypted_password = crypt($pass, $salt);
  $_SESSION['usr'] = $uname;
  $_SESSION['epw'] = $encrypted_password;
  $_SESSION['rpw'] = $pass;
  $con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
  if($con){
           //session_destroy();
           header('Location: /masterpage.php');
  }
  else{

     session_destroy();
     session_start();
     //header('Location: /login.php');
  }
}
*/
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/style.css" />
<title>CKXU Intranet Home</title>
</head>
<body>
    <div id="page">
        <div id="header">
        	<h1><img src="/images/CKXU_Logo_TL.png" border=0 alt="Program Log (DPL)"/></h1>
            <ul>
           	   	<li><a href="/index.php">Home</a></li>
               	<li><a href="/login.php">DPL</a></li>
                <li><a href="/training/training.html">Training</a></li>
                <li><a href="http://www.allmusic.com/" target="parent">AllMusic</a></li>
                <li><a href="http://142.66.48.57:8000/logged/" target="parent">Logged Shows</a></li>
            </ul>
        </div>
        <div id="main">
        	<div class="main_top">
            	<h1>Digital Program Log Login</h1>
            </div>
           	<div class="main_body" text-align="center">
           	<img align="right" src="/images/SQLT.png" height=150px border=0 alt="MySQL POWERED"/>
                   <!--<a href="http://ckxu.com"><img src="/images/CKXU_Logo_PNG.png" border=0 alt=" --- CKXU Logo --- "/></a>-->
           	   <br /><br />
           	   <div style="margin-left:10px">
                   <form name="form1" action="" method="post" >
                         username : <input name="name" type="text" /> <br/><br />
                          password :  <input name="pass" type="password" /><br /><br /></div>
                         <div style="margin-left:75px"><input type="submit" name="submit" value="submit" /></div>
                   <br/>
                   </div>
                   </p>
           	<div class="main_bottom"></div>
        </div>
        <div id="footer">
        <p>
        <a href="http://cent4.serverhostingcenter.com/start/zuomcwvv/" target=parent>Server status: <span id="cc_stream_info_server"></span><br /></a>
        </p>
        </div>
   </div>
</body>
</html>
