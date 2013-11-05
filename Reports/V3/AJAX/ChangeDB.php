<?php
function ChangeDB($db_ID){
	$dbxml = simplexml_load_file("../../TPSBIN/XML/DBSETTINGS.xml");
	/*if($_SESSION['SRVPOST']==NULL){
		
	}
	else if($_SESSION['SRVPOST']!=$db_ID){*/
		foreach( $dbxml->SERVER as $convars):
            if((string)$convars->ID==$db_ID){
            	
				// SET Connection HOST
                if((string)$convars->RESOLVE == strtoupper("IPV4")){
                	$_SESSION['DBHOST'] = (string)$convars->IPV4;
				}
				else if((string)$convars->RESOLVE == strtoupper("URL")){
					$_SESSION['DBHOST'] = (string)$convars->URL;
				}
				else{
					echo("Unknown Connection Type:".(string)$convars->RESOLVE." Check XML");
					//return 1;
				}
				
				// SET Connection Type
				if((string)$convars->DBTYPE == "MySQL"){
					$_SESSION['CONTYPE'] = "MySQL";
				}
				else if((string)$convars->DBTYPE == "MySQLi"){
					$_SESSION['CONTYPE'] = "MySQLi";
				}
				else{
					echo("UNKNOWN Database Type :".(string)$convars->DBTYPE . " (Check XML)");
				}
				
				$_SESSION['SRVPOST']=$db_ID;
            }
        endforeach;
	//}
	
}
?>