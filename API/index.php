<?php
require '../TPSBIN/Slim/Slim/Slim.php';
\Slim\Slim::registerAutoloader();

require '../TPSBIN/functions.php';
require '../TPSBIN/db_connect.php';
$db = $mysqli;
$app = new \Slim\Slim(array(
    'debug' => true
));
$app->get('/library/:artist/:album', function ($artist,$album) {
    echo "Hello, $artist , nothing found for $album";
});
$app->get('/library/:artist', function ($artist) {
    $db->prepare("SELECT datein,dateout,");
    echo "Hello, $artist";
});
$app->run();
