<?php
include("LDAP_Auth.php");
include("DB_Auth.php");
// Establish Session
session_start();

// LOAD SERVERS
$dbxml = simplexml_load_file("../TPSBIN/XML/DBSETTINGS.xml");

// using ldap bind [Get Credentials]
$postuser  = $_POST['name'];// ldap rdn or dn
$postpass = $_POST['pass']; // associated password
$db_ID = $_POST['SRVID']; // Entity ID
$dest = $_GET['d'] = '../masterpage.php';
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
echo "Checking Entry [".(string)$convars->ID;
echo "]: ";
if((string)$convars->ID==$db_ID){
    echo "MATCH</br>";
	// SET Connection HOST
    if((string)$convars->AUTH == strtoupper("LDAP")){
        //$_SESSION['DBHOST'] = (string)$convars->IPV4;
        if(LDAP_AUTH($postuser, $postpass, $convars)){
            if($des==0){
                header("Location: $dest");
            }
            else{
                header("Location: $ORIGIN");
            }
	        //echo "200";
        }
        else{
	        //echo "<h3>Login Failed</h3><span><br/>You do not have DNS authenticated access<br/></span>";
	        //header("location: http://ckxuradio.su.uleth.ca/index.php/digital-program-logs?args=LoginFailedCode1");
	        //header("Location: $ORIGIN&auth=Access Denied Invalid Credentials");
	        //echo "Login Failed";
            echo "<hr/><br/><h2>Login Failed</h2><br/>Click <a href='$ORIGIN'>HERE</a> to return to login and try again";
        }
    }
    else if((string)$convard->AUTH == strtoupper("MYSQL_DB")){
    	if(DB_AUTH($postuser, $postpass, $convars)){
    		if($des==0){
    			header("Location: $dest");
    		}
    		else{
    			header("Location: $ORIGIN");
    		}
    	}
    	else{
    		echo "<hr/><br/><h2>Login Failed</h2><br/>Click <a href='$ORIGIN'>HERE</a> to return to login and try again";
    	}
    }
	//$_SESSION['SRVPOST']=$db_ID;
}
else{
    echo " MISS</br>";
}
endforeach;

echo "<br/><br/>FAILED TO RESOLVE HOST. CHECK THAT SRVID IS BEING PASSED;<br/>RECEIVED:".$db_ID;





?>

