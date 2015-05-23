<?php

//
//constant('HOST')=$_SESSION['DBHOST'];
// set connection paramaters

// support functions
include_once '../../TPSBIN/functions.php';

error_reporting(0);
//need to set DB Values before this page.
 
sec_session_start(); // Our custom secure way of starting a PHP session.

$var = filter_input(INPUT_POST, 'ID', FILTER_SANITIZE_STRING);
$dbxml = simplexml_load_file("../../TPSBIN/XML/DBSETTINGS.xml");

if(!set_db_params($dbxml,$var)){
 die("Could not set Database Parameters, Invalid or missing target");
}

//include_once 'db_auth_connect.php';


// establish connection
include_once '../../TPSBIN/db_connect.php';
 
if (isset($_POST['email'], $_POST['p'])) {
    $email = $_POST['email'];//filter_input(INPUT_POST,'email');
    $password = $_POST['p'];//filter_input(INPUT_POST, 'p');// The hashed password.
 
    if (login($email, $password, $mysqli) == true) {
        $_SESSION['TimeZone']='UTC';
        // Login success         
        header('Location: ../../');
    } else {
        // Login failed 
        // Destroy Session
        
        //runkit_constant_remove("HOST");
        //runkit_constant_remove("USER");
        //runkit_constant_remove("PASSWORD");
        header("Location: Login.php?error=Invalid Login&q=$var");
        //echo $email." ".$password;
    }
} else {
    // The correct POST variables were not sent to this page. 
    echo 'Invalid Request';
}

?>
