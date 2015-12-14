<?php
if(file_exists("../TPSBIN/XML/DBSETTINGS.xml")){
    http_response_code(403);
    $refusal = "<h1>403 Forbidden</h1><p>Your request cannot proceed as the"
            . " this server has already been configured.</p>";
    die($refusal);
}
if(!extension_loaded('mysqli')||!extension_loaded('mysql')){
    die(http_response_code(500));
}

if(!isset($_SESSION)){
    session_start();
}

/* 
 * http://stackoverflow.com/questions/3755952/save-array-as-xml
 * 
 * Generates XML Login Script for Dynamic Login.
 */

/*
 * DBTYPE used to be used for connections to MSSQL Servers, now deprecated.
 */


include_once '../TPSBIN/functions.php';

$URR=$_SESSION['user'];
$PDR=$_SESSION['password'];

$USR=easy_crypt(ENCRYPTION_KEY,$URR);
$PWD=easy_crypt(ENCRYPTION_KEY,$PDR);
        
error_reporting(0);
$SERV = [
    "ID"=>  $_SESSION['callsign'] . rand(0, 999),
    "NAME"=> $_SESSION['brand'],
    "LOGO_PATH"=>"images/logo.png",
    "MENU_LOGO_PATH"=>"images/logo.png",
    "ACTIVE"=>1,
    "RESOLVE"=>"URL",
    "URL"=>$_SESSION['host'],
    "IPV4"=>$_SESSION['host'],
    "PORT"=>$_SESSION['port'],
    "DBTYPE"=>"MySQL",
    "DATABASE"=>$_SESSION['database'],
    "AUTH"=>$_SESSION['authtype'],
    "LDP_PORT"=>$_SESSION['ldap_port'],
    "LDP_SERVER"=>$_SESSION['ldap_server'],
    "LDP_BASE_DN"=>$_SESSION['ldap_dn'],
    "LDP_DOMAIN"=>$_SESSION['ldp_domain'],
    "USER"=>$USR,
    "PASSWORD"=>$PWD
    
];

// save
$doc = new DOMDocument('1.0');
$doc->formatOutput = true;
$root = $doc->createElement('SERVERS');
$root = $doc->appendChild($root);
$server = $doc->createElement('SERVER');
$root->appendChild($server);
foreach($SERV as $key=>$value)
{
   $em = $doc->createElement($key);       
   $text = $doc->createTextNode($value);
   $em->appendChild($text);
   $server->appendChild($em);

}
//if(function(){
if($doc->save('../TPSBIN/XML/DBSETTINGS.xml')){
    if(chmod('../TPSBIN/XML/DBSETTINGS.xml',0600)){
        print json_encode(array("status"=>"Complete"));#,"value"=>$SERV));
    }
    else{
        print json_encode(array("status"=>"warning","value"=>"could not change permissions"));
    }
}
else{
    http_response_code(500);
    print json_encode(array("status"=>"Fail"));#,"value"=>$SERV));
}
    
/*})
{*/
    
/*}
else{
    print json_encode(array("status"=>"Failed"));
}*/

// load
/*
 $arr = array();
 $doc = new DOMDocument();
 $doc->load('file.xml');
 $root = $doc->getElementsByTagName('root')->items[0];
 foreach($root->childNodes as $item) 
 { 
   $arr[$item->nodeName] = $item->nodeValue;
 }
 * */
