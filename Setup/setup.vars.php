<?php

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

$max_page_usr =  filter_input(INPUT_POST, 'e',FILTER_SANITIZE_STRING);
$current_page = filter_input(INPUT_POST, 'q',FILTER_SANITIZE_STRING);
$eula_accepted = filter_input(INPUT_POST, 'eula',FILTER_SANITIZE_STRING);
$database = filter_input(INPUT_POST, 'db',FILTER_SANITIZE_STRING);
$username = filter_input(INPUT_POST, 'r',FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'd',FILTER_SANITIZE_STRING);

$pagevars=[];
foreach($PAGES as $node){
    $pagevars[]=$node[0];
}
var_dump($pagevars);
$page_max = array_search($max_page_usr, $pagevars);

if(isset($_SESSION['max_page'])){
    $_SESSION['max_page']=0;
    echo "<br>Set Session max";
}
else{
    echo "<br>Session Exists";
}
if((int)$_SESSION['max_page']>(int)$page_max){
    $_SESSION['max_page']=$page_max;
    echo "<br>set max_page to:".$page_max;
}
else{
    echo "<br>".$_SESSION['max_page']." --- ".$page_max;
}


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

echo "<a href=\"./?q=".$_POST['q']."\">NEXT</a>";
//header('location: ./?q='.$_POST['q']);
/*foreach($_POST as $var){
    $name = key($var);
    $value = $var[0];
    echo $name ." - ".$value."<br>";
}*/
