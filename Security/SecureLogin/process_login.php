<?php
//
//constant('HOST')=$_SESSION['DBHOST'];
include_once '../../TPSBIN/db_connect.php';
include_once '../../TPSBIN/functions.php';
 
sec_session_start(); // Our custom secure way of starting a PHP session.
 
if (isset($_POST['email'], $_POST['p'])) {
    $email = \filter_input(\INPUT_POST,'email');
    $password = \filter_input(\INPUT_POST, 'p');// The hashed password.
 
    if (login($email, $password, $mysqli) == true) {
        // Login success 
        header('Location: ../../masterpage.php');
    } else {
        // Login failed 
        header('Location: Login.php?error=Invalid Login');
    }
} else {
    // The correct POST variables were not sent to this page. 
    echo 'Invalid Request';
}

?>