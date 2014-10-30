<?php

$DEBUG=FALSE;

include "../TPSBIN/functions.php";
if(is_session_started()===FALSE) { session_start(); }

/**
 * Description of setup
 *
 * @author James
 */
class setup {
    // sets
    public function setvars($vars) {
        
    }
    
    // gets variables from session
    public function getvars(){
        
    }
}

$PAGES = $_SESSION['PAGES'];

/* @var $max_page_usr type */
$max_page_usr =  \filter_input(INPUT_POST, 'e',\FILTER_SANITIZE_STRING);
/* @var $current_page type */
$current_page = \filter_input(INPUT_POST, 'q',\FILTER_SANITIZE_STRING);
/* @var $eula_accepted type */
$eula_accepted = \filter_input(INPUT_POST, 'eula',\FILTER_SANITIZE_STRING);
/* @var $database type */
$database = \filter_input(INPUT_POST, 'db',\FILTER_SANITIZE_STRING);
/* @var $host type */
$host = \filter_input(INPUT_POST,'host',\FILTER_SANITIZE_STRING);
/* @var $port type */
$port = \filter_input(INPUT_POST, 'port',\FILTER_SANITIZE_NUMBER_INT);
/* @var $username type */
$username = \filter_input(INPUT_POST, 'r',\FILTER_SANITIZE_STRING);
/* @var $password type */
$password = \filter_input(INPUT_POST, 'd',  \FILTER_SANITIZE_STRING);

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
    echo $_SESSION['max_page'];
}
if($_SESSION['max_page']>$page_max){
    $_SESSION['max_page']=$page_max;
    echo "<br>set max_page to:".$page_max;
}
else{
    echo "<br>".$_SESSION['max_page']." --- ".$page_max;
}

if(isset($_SESSION['EULA_ACCEPTED'])&&isset($_SESSION['EULA'])){
    //EULA is good.
}
else{
    if(isset($eula_accepted)&&$eula_accepted!=null){
        //$_SESSION['EULA_ACCEPTED']=date('Y-m-d');
        $_SESSION['EULA']=1;
        echo "SET EULA FLAG";
    }
    elseif($page_max>2){
        header('location:?q=lic&m='.  urlencode("You must accept the licence to procede with installation"));
    }
    else{
        echo "EULA Already Accepted??";
        
    }
}
//$_SESSION['EULA']=$eula_accepted;


echo "<br>".$page_max;

echo $max_page_usr;

//$_SESSION[]
/*
$chained = [];
$name = key($_POST);
foreach($_POST as $variable_name => $variable_val){
    //echo gettype($variable_val);
    if(gettype($variable_val)==="integer"||gettype($variable_val)==="string"||gettype($variable_val)==="double"){
        //echo $variable_name ." - ".$variable_val."<br>";
        $_SESSION[$variable_name]=$variable_val;
    }
}
*/
if($DEBUG){
echo "<a href=\"./?q=".$_POST['q']."\">NEXT</a>";
echo "<br><br>MPU:".$max_page_usr."<br>MP:".$page_max."<br>CP:".$current_page.
        "<br>EULA:".$eula_accepted.":".$_SESSION['EULA']."<br>DB:".$database."<br>UN:".$username.
        "<br>PW:".$password;
}
else{
    header('location: ./?q='.$_POST['q']);    
}

/*foreach($_POST as $var){
    $name = key($var);
    $value = $var[0];
    echo $name ." - ".$value."<br>";
}*/
