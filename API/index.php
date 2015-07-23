<?php
//error_reporting(0);

//===============================
//   INCLUDES
//===============================
/*require '../TPSBIN/Slim/Slim/Slim.php';
\Slim\Slim::registerAutoloader();*/
require '../vendor/autoload.php';

//--------------------------
// DB and generic functions
//--------------------------
require '../TPSBIN/functions.php';
require '../TPSBIN/db_connect.php';

//---------------------
// API function includes
//---------------------
require 'LibraryAPI.php';


//========================
// MAIN EXECUTION
//========================
$app = new \Slim\Slim(array(
    'debug' => true
));

//----------------------------
// Library API
//----------------------------
$app->get('/library/:refcode', function ($refcode) {
    print json_encode(GetLibraryRefcode($refcode));
});
$app->get('/library/artist/:artist', function ($artist) {
    print json_encode(GetLibraryfull($artist));
});
$app->get('/library/:artist/:album', function ($artist,$album) {
    print json_encode(GetLibraryfull($artist,$album));
});
$app->get('/library/', function () {
    print json_encode(ListLibrary());
});
$view->run();

