<?php
$DEBUG="<span style='color:orange'>Imported LDAPS Auth Module<br/></span>";
if(!isset($_SESSION)){
    session_start();
}
function LDAP_AUTH($user, $password, $xml_server) {
    if(!extension_loaded('ldap')){
        error_log("ldap module not installed but requested by login");
        header($_SERVER['Login.html?err=No%20LDAP%20Support']);
    }
    $DEBUG="<span style='color:orange'>LOADED LDAP(S)<br/></span>";
    if((string)$xml_server->ACTIVE == '0'){
        $DEBUG .= "<p>ERROR: Selected server has been disabled by an administrator</p>";
        die("<p>Click <a href='$ORIGIN'>Here</a> to return to login");
    }
    $DEBUG .= "<br/>";
    // Active Directory server
    $ldap_host = (string)$xml_server->LDP_SERVER;

    // LDAP Port
    $ldap_port = (string)$xml_server->LDP_PORT;

    // Active Directory DN
    $ldap_dn = (string)$xml_server->LDP_BASE_DN;

    // LOGO
    $logo = (string)$xml_server->LOGO_PATH;

    // Menu LOGO (Small)
    $m_logo = (string)$xml_server->MENU_LOGO_PATH;

    //Authorization Settings
    //
    // Active Directory user group
    $ldap_user_group = "WebUsers"; //$xml_server->ldap_user; ?? or from DB

    // Active Directory manager group
    $ldap_manager_group = "WebAdmins"; //$xml_server->ldap_user; ?? or from DB

    // Domain, for purposes of constructing $user
    $ldap_usr_dom = (string)$xml_server->LDP_DOMAIN;

    // connect to active directory
    $DEBUG .= "<span>SERVER: $ldap_host<br/></span>";
    if($ldap_port=='636'){
        $DEBUG .= "<span style='color:green'>USING LDAP OVER SSL<br/></span>";
    }
    elseif($ldap_port=='389'){
        $DEBUG .= "<span style='color:blue'>USING STANDARD LDAP<br/></span>";
    }
    else{
        $DEBUG .= "<span style='color:yellow; background-color: black;'>LDAP PORT UNKNOWN:$ldap_port<br/></span>";
    }
    $DEBUG .= "<span>Attempting LDAP Connection:</span>";
    try{
        if($ldap = ldap_connect($ldap_host,$ldap_port)){
            $DEBUG .= "<span style='color: green;'> [Connection Established]<br/></span>";
        }
        else{
            $DEBUG .= "<span style='color: red;'> [Connection Refused]<br/></span>";
        }
    }
    catch (Exception $e){
        die("<span style='color: red;'>TERMINAL CONNECTION ERROR - Cannot establish LDAP or LDAP over SSL connection!<br/></span>");
    }

    // Bind Account
    $bindUser = $user;//easy_decrypt(ENCRYPTION_KEY,$xml_server->USER);//"admin";
    $DEBUG .= "<br>".$bindUser."<br>";

    // Bind Password
    $bindpassword = $password;//easy_decrypt(ENCRYPTION_KEY,$xml_server->PASSWORD);//"K1w1679";

    // verify user and password
    $DEBUG .= "<span>Attempting LDAP bind with $ldap_usr_dom\\$bindUser<br/></span>";
    $DEBUG .= "<span>Using DN:$ldap_dn<br/></span>";
    try{
        if($bind = @ldap_bind($ldap, $ldap_usr_dom . '\\' . $bindUser, $bindpassword)) {
	    $DEBUG .= "<span style='color: green;'>Bind Accepted with $ldap_usr_dom\\$bindUser<br/></span>";
            // valid
            // check presence in groups
            $filter = "(sAMAccountName=" . $user . ")";
            $attr = array("memberof");
            $result = ldap_search($ldap, $ldap_dn, $filter, $attr) or exit("<span>Domain Authentication Error - Check Domain</span>");
            $entries = ldap_get_entries($ldap, $result);
            ldap_unbind($ldap);
		    $nameLDAP = substr(
                            ldap_explode_dn($entries[0]["dn"],0)[0],3);

            // check groups
            foreach($entries[0]['memberof'] as $grps) {
                // is manager, break loop
                if (strpos($grps, $ldap_manager_group)) { $access = 2; break; }

                // is user
                if (strpos($grps, $ldap_user_group)) { $access = 1; };
			
            }
            echo "BASE QUERY: ".(string)$xml_server->USER."; ".easy_decrypt(ENCRYPTION_KEY,(string)$xml_server->USER)." [$access] ";
            if ($access != 0) {
                // establish session variables
                if($access == 1){
                    echo "<br>SETTING ACCESS LEVEL 1: ".(string)$xml_server->USER."; ".easy_decrypt(ENCRYPTION_KEY,(string)$xml_server->USER)."<br>";
            	    $_SESSION['usr'] = easy_decrypt(ENCRYPTION_KEY,(string)$xml_server->USER);//"program";
                    #define("USER",easy_decrypt(ENCRYPTION_KEY,(string)$xml_server->USER));
                    $_SESSION['rpw'] = easy_decrypt(ENCRYPTION_KEY,(string)$xml_server->PASSWORD);//"pirateradio";
                    #define("PASSWORD",easy_decrypt(ENCRYPTION_KEY,(string)$xml_server->PASSWORD));
                    $_SESSION['access'] = $access;
                    //$_SESSION['name'] = "UNDEFINED USER";
                }
                else if($access == 2){
                    echo "<br>SETTING ACCESS LEVEL 2: ".(string)$xml_server->USER."; ".easy_decrypt(ENCRYPTION_KEY,(string)$xml_server->USER)."<br>";
                    $_SESSION['usr'] = (string)easy_decrypt(ENCRYPTION_KEY,(string)$xml_server->USER);//"program";
                    #define("USER",(string)easy_decrypt(ENCRYPTION_KEY,(string)$xml_server->USER));
                    $_SESSION['rpw'] = (string)easy_decrypt(ENCRYPTION_KEY,(string)$xml_server->PASSWORD);//"pirateradio";
                    #define("PASSWORD",(string)easy_decrypt(ENCRYPTION_KEY,(string)$xml_server->PASSWORD));
                    $_SESSION['access'] = $access;
                    //$_SESSION['name'] = "UNDEFINED ADMIN";
                }
                $_SESSION['fname'] = $nameLDAP;//"LDAP Authenticated User";
                $_SESSION['DBNAME'] = (string)$xml_server->DATABASE;//"CKXU";
                if((string)$xml_server->RESOLVE == 'URL'){
                    $_SESSION['DBHOST'] = (string)$xml_server->URL;
                }
                else{
                    $_SESSION['DBHOST'] = (string)$xml_server->IPV4;
                }
                #define("HOST",(string)$_SESSION['DBHOST']);
                #echo "SET HOST = " . constant('HOST');
                #define('DBNAME',(string)$_SESSION['DBNAME']);
                #echo "SET DBNAME = " . constant('DBNAME');
                //$_SESSION['DBHOST'] = "172.22.100.25";
                $_SESSION['SRVPOST'] = (string)$xml_server->ID;//addslashes($_POST['SID']);
                $_SESSION['logo']=$logo;
                $_SESSION['m_logo']=$m_logo;
                $_SESSION['account'] = $user;
                $_SESSION['AutoComLimit'] = 8;
                $_SESSION['AutoComEnable'] = TRUE;
                $_SESSION['TimeZone']='UTC'; // this is just the default to be updated after login
                #echo $DEBUG;
                return true;
            } else {
                // user has no rights
		        $DEBUG .= "Access Denied<br/>";
                        #echo $DEBUG;
                return false;
            }

        } else {
            // invalid name or password
	    $DEBUG .= "<span style='color: red;'>Invalid Username or password using <span style='color: blue;'>$ldap_usr_dom\\$bindUser</span> with password ".
        isset($bindpassword)."<br/><br/></span>";
            return false;
        }
    }
    catch (Exception $e){
        error_log("Could not Bind LDAP server");
        die("could not bind to server... error thrown");
    }
}
?>
