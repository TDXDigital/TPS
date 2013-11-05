<?php
	session_start();
	$usr = addslashes($_POST['name']);
	$PASS = addslashes($_POST['pass']);
	
	//http://pbeblog.wordpress.com/2008/02/12/secure-hashes-in-php-using-salt/
	$SALT = "a764e53e4eb046cccded63360b9ff439";
	$SALTPASS = md5($PASS.$SALT);
	
	if($con = mysql_connect('localhost',$usr,$PASS))
	{
		if(isset($_SESSION)){
			echo"<script>
			Alert(\"HTTPS Login Verified\");
			</script>
			";
			header('location: /masterpage.php');
		}
		//echo 'Login!';
		$_SESSION['usr'] = $usr;
		$_SESSION['rpw'] = $PASS;
		header('Location: http://ckxuradio.su.uleth.ca/masterpage.php');
		
	}
	else{
		
		if(!$con = mysql_connect('localhost','DGLLogger','CKXU2012'))
		{
			die("Login Error");
		}
		else{
			echo"<script>
				Alert(\"Login Failed\");
			</script>";
			header('Location: http://ckxuradio.su.uleth.ca/index.php/digital-program-logs');
		}
		//header('Location: /');
		
	}
	
		//header('Location: /');
	
?>