<?php
   include("../TPSBIN/functions.php");

// Establish Session if does not exist (should not exist)
if(!isset($_SESSION)){
   sec_session_start();
   $DEBUG = FALSE;
}

// SET BASE REF
$_SESSION['BASE_REF']="";// should load from XML

// LOAD SERVERS
$dbxml = simplexml_load_file("../TPSBIN/XML/DBSETTINGS.xml");

?>
<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="description" content="Login for TPS Radio System">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="James Oliver">
        <!-- Latest compiled and minified CSS -->
        <!-- HOSTED -->
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

        <!-- Optional theme -->
        <!-- HOSTED -->
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
        <!--<link href=\"js/css/bootstrap.min.css\" rel=\"stylesheet\">-->

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src=\"https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js\"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        
    </head>
    <body role="document" style="">
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">TPS Radio System</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="/">Home</a></li>
            <li><a href="/TPSlogin">Login</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li class="dropdown-header">Nav header</li>
                <li><a href="#">Separated link</a></li>
                <li><a href="#">One more separated link</a></li>
              </ul>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
<?php

// using ldap bind [Get Credentials]

$postuser = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$postpass = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING);
$db_ID = filter_input(INPUT_POST, 'SRVID', FILTER_SANITIZE_STRING);
$DEST = filter_input(INPUT_POST, 'D', FILTER_SANITIZE_STRING);
//$dest = $_GET['d']?:'../';
$DEBUG = "";
if(isset($_POST['return'])){
    $des = $_POST['return'];
}
else{
    $des = 0;
}
$ORIGIN = $_SERVER['HTTP_REFERER'];
$_SESSION['LOGIN_SRC'] = $_SERVER['HTTP_REFERER'];

//DETERMINE AUTH TYPE
foreach( $dbxml->SERVER as $convars):
$DEBUG .= "Checking Entry [".(string)$convars->ID;
$DEBUG .= "]: ";
if((string)$convars->ID==$db_ID){
    $DEBUG .= "MATCH</br>";
	// SET Connection HOST
    if($convars->RESOLVE==="URL"){
        define("HOST",$convars->URL);
        $_SESSION['HOST']=$convars->URL;
    }
    else{
        define("HOST",$convars->IPV4);
        $_SESSION['HOST']=$convars->IPV4;
    }
    define("PORT",$convars->PORT);
    $_SESSION['DBPORT']=$convars->PORT;
    define("DBNAME",$convars->DATABASE);
    $_SESSION['DBNAME']=$convars->DATABASE;
    
    if((string)$convars->AUTH == strtoupper("LDAP")){
        // Load Auth Module LDAP
        include("LDAP_Auth.php");
        if(LDAP_AUTH($postuser, $postpass, $convars)){
            if($des==0){
                header("Location: $dest");
            }
            else{
                header("Location: $ORIGIN");
            }
        }
        else{
            echo "<div class=\"container\" style=\"margin-top: 30px;\"><div class=\"page-header\">LDAP Login Failed</div><p>Click <a href='$ORIGIN'>HERE</a> to return to login and try again</p></div>";
        }
    }
    else if((string)$convars->AUTH == strtoupper("MYSQL_DB")){
    	if(DB_AUTH($postuser, $postpass, $convars)){
            // Load Auth Module DB
            //include("DB_Auth.php");
    		if($des==0){
    			//header("Location: $dest");
                    header("Location: SecureLogin/Login.php");
                    
    		}
    		else{
    			header("Location: $ORIGIN");
                        //header("Location: SecureLogin/Login.php");
    		}
    	}
    	else{
    		echo "<hr/><br/><h2>MYSQL Login Failed</h2><br/>Click <a href='$ORIGIN'>HERE</a> to return to login and try again";
    	}
    }
    else if((string)$convars->AUTH == strtoupper("SECL")){
    	header("Location: SecureLogin/Login.php?q=".$convars->ID);
    }
    else{
        echo "<hr/><br/><h2>Login Failed</h2><br/><p>Could Not Determine Login Type";
        echo "<br>Please contact your system administrator for further details<br>";
        echo "</p>Click <a href='$ORIGIN'>HERE</a> to return to login and try again";
    }
	//$_SESSION['SRVPOST']=$db_ID;
}
else{
    $DEBUG .= " MISS</br>";
}
endforeach;

$DEBUG .= "<br/><br/>FAILED TO RESOLVE HOST. CHECK THAT SRVID IS BEING PASSED;<br/>RECEIVED:".$db_ID;
?>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <!--<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js\"></script>-->
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!--<script src=\"js/bootstrap/js/bootstrap.min.js\"></script>-->
    </body>
</html>

