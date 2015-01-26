<?php

include_once '../../TPSBIN/functions.php';
  //convert to function with var being passed to it.
  
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
          define("HOST",  constant('HOST') );
          define("USER",easy_decrypt(ENCRYPTION_KEY,(string)$server->USER));
          define("PASSWORD",easy_decrypt(ENCRYPTION_KEY,(string)$server->PASSWORD));
          define("DATABASE",(string)$server->DATABASE);
      }
  }
?>
