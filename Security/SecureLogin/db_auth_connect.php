<?php

include_once '../../TPSBIN/functions.php';
  $var = filter_input(INPUT_POST, 'ID', FILTER_SANITIZE_STRING);
  
  $dbxml = simplexml_load_file("../../TPSBIN/XML/DBSETTINGS.xml");
  
  foreach ($dbxml->SERVER as $server){
      if((string)$server->ID === $var){
          if($server->RESOLVE === "URL")
          {
            define("HOST",$server->URL);
          }
          elseif($server->RESOLVE === "IPV4")
          {
            define("HOST",$server->IPV4);
          }
          else
          {
              if($server->URL!=""){
                  define("HOST",$server->URL);  
              }
              else{
                  define("HOST",$server->IPV4);
              }
          }
          define("DBHOST",  constant('HOST') );
          define("USER",easy_decrypt(ENCRYPTION_KEY,(string)$server->USER));
          define("PASSWORD",easy_decrypt(ENCRYPTION_KEY,(string)$server->PASSWORD));
          define('DBNAME',(string)$server->DATABASE);
      }
  }
?>
