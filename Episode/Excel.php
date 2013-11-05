<?php

// load library
require '/includes/phpexcel/php-excel.class.php';

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