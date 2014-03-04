<?php
//'hyperstream.local.ckxu.com','cent4.serverhostingcenter.com'
    
$RDS = array(
    0 => array(
        'host'=>'hyperstream.local.ckxu.com',
        'port'=>8000,
        'password'=>'88.3ForLife'
    ),
    1 => array(
        'host'=>'cent4.serverhostingcenter.com',
        'port'=>8715,
        'password'=>'88.3ForLife'
    )
);

$Source = array(
    'Logs'=>array(
        'address'=>'172.22.10.10',
        'user'=>'root',
        'password'=>'88.3ForLife'
        ),
    'RadioDJ'=>array(
        'address'=>'172.22.10.50',
        'user'=>'root',
        'password'=>'88.3ForLife'
    )
);



foreach($RDS AS $Server){
    echo $Server['Host'];
    echo $Server['port'];
    echo $Server['password'];
}


    //$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
?>