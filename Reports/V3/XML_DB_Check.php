<?php
    session_start();
    header("Content-type: text/xml");
    $HOST = $_SESSION['DBHOST'];
    $PORT = $_SESSION['PORT'];
    $DBNAME = $_SESSION['DBNAME'];
    $USER = $_SESSION['usr'];
    //$USER = "TESTFAKEUSER";
    $PASS = $_SESSION['rpw'];
	
	//$ERROR = FALSE;
	
	/* If server is not defined
	 * Return Error
	 * Do not form connectrion
	 */
	if($_SESSION['SRVPOST']=="NDEF000" || !isset($_SESSION['SRVPOST']))
				{
			echo "<?xml version=\"1.0\" standalone='yes'?>
	<CONNECTION>
	    <RESULT>
	        <PASS>0</PASS>
	        <ERROR>No Server Defined</ERROR>
	        <ERRNO>1001</ERRNO>
	        <USER>".$USER."</USER>
	        <DB>$HOST</DB>
	    </RESULT>
	</CONNECTION>";
		}
	else{
	    //Check for DB Access
	    //$db = new mysqli($HOST, $USER, $PASS, $DBNAME);
	    
	    /* ESTABLISH connection */
	    if($_SESSION['CONTYPE']=="MySQLi"){
	    	$link = mysqli_connect($HOST,$USER,$PASS,$DBNAME);
	    }
		else{
			$link = mysqli_connect($HOST,$USER,$PASS,$DBNAME);
		}
		
		/* CHECK Connection*/
		if(mysqli_connect_error()){
		if($_SESSION['CONTYPE']=="MySQLi"){
	    	echo "<?xml version=\"1.0\" standalone='yes'?>
<CONNECTION>
    <RESULT>
        <PASS>0</PASS>
        <ERROR>Connection Error</ERROR>
        <ERRNO>1002</ERRNO>
        <USER>".$USER."</USER>
        <DB>$HOST</DB>
    </RESULT>
</CONNECTION>";
			exit();
			}
	    }
		/*else{
			$link = mysqli_connect($HOST,$USER,$PASS,$DBNAME);
		}*/
		
		/* check if server is active*/
		if(mysqli_ping($link)){
			$ERROR = FALSE; 
		}
		else{
			$ERROR = TRUE;
		}
		
		
		/*if(mysqli_ping($db)){
			$ERROR = NULL;
		}
		else{
			$ERROR = mysqli_errno($db);
		}*/
	    if(!isset($USER)||!isset($DBNAME)){
	        	
	        echo "<?xml version=\"1.0\" standalone='yes'?>
	<CONNECTION>
	    <RESULT>
	        <PASS>0</PASS>
	        <ERROR>Please Login</ERROR>
	        <ERRNO>1000</ERRNO>
	        <USER>".$USER."</USER>
	        <DB>$HOST</DB>
	    </RESULT>
	</CONNECTION>";
	    }
		else if($ERROR == TRUE || mysqli_error($link)){ // $db->connect_errno > 0 && 
        echo "<?xml version=\"1.0\" standalone='yes'?>
<CONNECTION>
    <RESULT>
        <PASS>0</PASS>
        <ERROR>Access Denied</ERROR>
        <ERRNO>1003</ERRNO>
        <USER>".$USER."</USER>
        <DB>$HOST</DB>
    </RESULT>
</CONNECTION>";
    }
	    else{ 
	        echo "<?xml version=\"1.0\" standalone='yes'?>
	<CONNECTION>
	    <RESULT>
	        <PASS>1</PASS>
	        <ERROR>No Error</ERROR>
	        <ERRNO></ERRNO>
	        <USER>".$USER."</USER>
	        <DB>$HOST</DB>
	    </RESULT>
	</CONNECTION>";
	    }
	    mysqli_close($link);
    }
?>
