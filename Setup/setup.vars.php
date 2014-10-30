<?php

$DEBUG=FALSE;

include "../TPSBIN/functions.php";
if(is_session_started()===FALSE) { session_start(); }

/**
 * This page handles configuration for the 
 * Setup sequence. Stores all data, sanitizes
 * and controls workflow.
 *
 * @author James Oliver
 */

$PAGES = $_SESSION['PAGES'];

// Welcome
/* @var $max_page_usr type */
$max_page_usr =  \filter_input(INPUT_POST, 'q',\FILTER_SANITIZE_STRING);
/* @var $current_page type */
$current_page = \filter_input(INPUT_POST, 'e',\FILTER_SANITIZE_STRING);

// EULA
/* @var $eula_accepted type */
$eula_accepted = \filter_input(INPUT_POST, 'eula',\FILTER_SANITIZE_STRING);


// Database
/* @var $database type */
$database = \filter_input(INPUT_POST, 'database',\FILTER_SANITIZE_STRING);
/* @var $host type */
$host = \filter_input(INPUT_POST,'host',\FILTER_SANITIZE_STRING);
/* @var $port type */
$port = \filter_input(INPUT_POST, 'port',\FILTER_SANITIZE_NUMBER_INT);
/* @var $username type */
$username = \filter_input(INPUT_POST, 'r',\FILTER_SANITIZE_STRING);
/* @var $password type */
$password = \filter_input(INPUT_POST, 'd',  \FILTER_SANITIZE_STRING);


// Auth
/* @var $auid type */
$auid = \filter_input(INPUT_POST, 'uid', \FILTER_SANITIZE_STRING);
/* @var $sysuser type */
$sysuser = \filter_input(INPUT_POST, 'su', \FILTER_SANITIZE_STRING);
/* @var $authtype type */
$authtype = \filter_input(INPUT_POST, 'at', \FILTER_SANITIZE_STRING);
/* @var $LDAP_port type */
$LDAP_port = \filter_input(INPUT_POST, 'ldp',\FILTER_SANITIZE_STRING);
/* @var $LDAP_SERVER type */
$LDAP_server = \filter_input(INPUT_POST, 'lds',\FILTER_SANITIZE_STRING);
/* @var $LDAP_DN type */
$LDAP_DN = \filter_input(INPUT_POST, 'dn',\FILTER_SANITIZE_STRING);
/* @var $LDAP_domain type */
$LDAP_domain = \filter_input(INPUT_POST, 'domn',\FILTER_SANITIZE_STRING);
/* @var $LDAP_bind_u type */
$LDAP_bind_u = \filter_input(INPUT_POST, 'bndu',\FILTER_SANITIZE_STRING);
/* @var $LDAP_bind_p type */
$LDAP_bind_p = \filter_input(INPUT_POST, 'bndp',\FILTER_SANITIZE_STRING);

/*
 * Process Page related values
 */
$pagevars=[];
foreach($PAGES as $node){
    $pagevars[]=$node[0];
    
}
$page_max = array_search($max_page_usr, $pagevars);


if(!isset($_SESSION['max_page'])){
    $_SESSION['max_page']=0;
    echo "<br>Set Session max";
}
elseif($_SESSION['max_page']<$page_max){
    $_SESSION['max_page']=$page_max;
}
else{
    echo "<br>Session Exists";
    //echo $_SESSION['max_page'];
}
if($_SESSION['max_page']>$page_max){
    $_SESSION['max_page']=$page_max;
    echo "<br>set max_page to:".$page_max;
}
else{
    echo "<br>PAGE S:".$_SESSION['max_page']." --- R:".$page_max;
}


/*
 * Process EULA related entries and validate licence acceptance
 * 
 */

/*
if(isset($_SESSION['EULA_ACCEPTED'])&&isset($_SESSION['EULA'])){
    //EULA is good. (accepted not recorded) [will always fail]
}
else{*/
    if(isset($eula_accepted)&&$eula_accepted!=null){
        //$_SESSION['EULA_ACCEPTED']=date('Y-m-d');
        $_SESSION['EULA']=1;
        echo "SET EULA FLAG";
    }
    elseif($page_max>2 && !isset($_SESSION['EULA'])){
        header('location:?q=lic&m='.urlencode("You must accept the licence to procede with installation"));
    }
    else{
        echo "EULA Already Accepted";
        
    }
//}
//$_SESSION['EULA']=$eula_accepted;

/*
 * Database settings save to session
 */

if(!is_null($username)&&!is_null($password)){
    $_SESSION['port']=$port;
    $_SESSION['host']=$host;
    $_SESSION['database']=$database;
    $_SESSION['user']=$username;
    $_SESSION['password']=$password;
}

/*
 * Set Authentication Values
 */
if(!is_null($authtype)){
    echo "<br>Setting Auth:".$authtype;
    $_SESSION['authtype']=$authtype;
    if($authtype==="LDAP"){
        $_SESSION['ldap_port']=$LDAP_port;
        $_SESSION['ldap_server']=$LDAP_server;
        $_SESSION['ldap_dn']=$LDAP_DN;
        $_SESSION['ldap_domn']=$LDAP_domain;
        echo "<br><br>LDAP_Port:".$LDAP_port."<br>LDAP_Server:".$LDAP_server."<br>DN:".$LDAP_DN.
            "<br>Domain:".$LDAP_domain;
        
    }
}

/*
 * Handle Redirect (load next page)
 */
if($DEBUG){
    echo "<br>".$page_max;
    echo $max_page_usr;
    echo "<a href=\"./?q=".$_POST['q']."\">NEXT</a>";
    echo "<br><br>MPU:".$max_page_usr."<br>MP:".$page_max."<br>CP:".$current_page.
            "<br>EULA:".$eula_accepted.":".$_SESSION['EULA']."<br>DB:".$database."<br>UN:".$username.
        "<br>PW:".$password;
}
else{
    header('location: ./?q='.$_POST['q']);    
}