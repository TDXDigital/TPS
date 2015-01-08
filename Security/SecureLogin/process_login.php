<?php

//
//constant('HOST')=$_SESSION['DBHOST'];
include_once 'db_auth_connect.php';
include_once '../../TPSBIN/db_connect.php';
include_once '../../TPSBIN/functions.php';

//need to set DB Values before this page.
 
sec_session_start(); // Our custom secure way of starting a PHP session.
 
if (isset($_POST['email'], $_POST['p'])) {
    $email = $_POST['email'];//filter_input(INPUT_POST,'email');
    $password = $_POST['p'];//filter_input(INPUT_POST, 'p');// The hashed password.
 
    if (login($email, $password, $mysqli) == true) {
        // Login success 
        header('Location: ../../masterpage.php');
    } else {
        // Login failed 
        header('Location: Login.php?error=Invalid Login');
        //echo $email." ".$password;
    }
} else {
    // The correct POST variables were not sent to this page. 
    echo 'Invalid Request';
}

?>
