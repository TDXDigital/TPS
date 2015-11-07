<?php

// load library
require '../includes/phpexcel/php-excel.class.php';
require '../TPSBIN/functions.php';
require '../TPSBIN/db_connect.php';

sec_session_start();

$SQL_ALL="SELECT * FROM episode LEFT JOIN songs ON episode.programname=song.programname and episode.date=song.date and episode.starttime=song.starttime and episode.callsign=song.callsign
 WHERE episode.programname=\"$CALL\" and episode.date=\"$DATE\" and episode.starttime=\"$START\" and episode.programname=\"$PROGRAM\" order by song.songid";

// create a simple 2-dimensional array
$data = array(
        1 => array ('Category', 'Playlist'),
        array('Schwarz', 'Oliver'),
        array('Test', 'Peter')
        );

// generate file (constructor parameters are optional)
$xls = new Excel_XML('UTF-8', false, 'Exported Program');
$xls->addArray($data);
$xls->generateXML('my-test');

?>
