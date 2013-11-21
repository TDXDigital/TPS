<?php
function LDAP_AUTH($user, $password, $xml_server) {
    if((string)$xml_server->ACTIVE == '0'){
        echo "<p>ERROR: Selected server has been disabled by an administrator</p>";
        die("<p>Click <a href='$ORIGIN'>Here</a> to return to login");
    }
    echo "<br/>";
    // Active Directory server
    $ldap_host = (string)$xml_server->LDP_SERVER;//"ldap://picard.local.ckxu.com/";

    // LDAP Port
    $ldap_port = (string)$xml_server->LDP_PORT;//636;

    // Active Directory DN
    $ldap_dn = (string)$xml_server->LDP_BASE_DN;//"CN=Users,DC=local,DC=ckxu,DC=com";

    // LOGO
    $logo = (string)$xml_server->LOGO_PATH;// images/Ckxu_logo_PNG.png

    // Active Directory user group
    $ldap_user_group = "WebUsers";

    // Active Directory manager group
    $ldap_manager_group = "WebAdmins";

    // Domain, for purposes of constructing $user
    $ldap_usr_dom = (string)$xml_server->LDP_DOMAIN;//"CKXU-FM";

    // connect to active directory
    echo "<span>SERVER: $ldap_host<br/></span>";
    if($ldap_port=='636'){
        echo "<span style='color:green'>USING LDAP OVER SSL<br/></span>";
    }
    elseif($ldap_port=='389'){
        echo "<span style='color:blue'>USING STANDARD LDAP<br/></span>";
    }
    else{
        echo "<span style='color:yellow; background-color: black;'>LDAP PORT UNKNOWN:$ldap_port<br/></span>";
    }
    echo "<span>Attempting LDAP Connection:</span>";
	try{
        if($ldap = ldap_connect($ldap_host,$ldap_port)){
            echo "<span style='color: green;'> [Connection Established]<br/></span>";
        }
        else{
            echo "<span style='color: red;'> [Connection Refused]<br/></span>";
        }
    }
    catch (Exception $e){
        die("<span style='color: red;'>TERMINAL CONNECTION ERROR - Cannot establish LDAP or LDAP over SSL connection!<br/></span>");
    }

    // Bind Account
    $bindUser = $user;//"admin";

    // Bind Password
    $bindpassword = $password;//"K1w1679";

    // verify user and password
    echo "<span>Attempting LDAP bind with $ldap_usr_dom\\$bindUser<br/></span>";
    echo "<span>Using DN:$ldap_dn<br/></span>";
    try{
        if($bind = @ldap_bind($ldap, $ldap_usr_dom . '\\' . $bindUser, $bindpassword)) {
	    echo "<span style='color: green;'>Bind Accepted with $ldap_usr_dom\\$bindUser<br/></span>";
            // valid
            // check presence in groups
            $filter = "(sAMAccountName=" . $user . ")";
            $attr = array("memberof");
            $result = ldap_search($ldap, $ldap_dn, $filter, $attr) or exit("<span>Domain Authentication Error - Check Domain</span>");
            $entries = ldap_get_entries($ldap, $result);
            ldap_unbind($ldap);
		    $nameLDAP = $entries[0]["displayname"];

            // check groups
            foreach($entries[0]['memberof'] as $grps) {
                // is manager, break loop
                if (strpos($grps, $ldap_manager_group)) { $access = 2; break; }

                // is user
                if (strpos($grps, $ldap_user_group)) { $access = 1; };
			
            }

            if ($access != 0) {
                // establish session variables
                if($access == 1){
            	    $_SESSION['usr'] = "program";
            	    $_SESSION['rpw'] = "pirateradio";
				    $_SESSION['access'] = $access;
				    //$_SESSION['name'] = "UNDEFINED USER";
                }
			    else if($access == 2){
				    $_SESSION['usr'] = "program";
            	    $_SESSION['rpw'] = "pirateradio";
				    $_SESSION['access'] = $access;
				    //$_SESSION['name'] = "UNDEFINED ADMIN";
			    }
			    $_SESSION['fname'] = "LDAP Authenticated User";//$nameLDAP;
                $_SESSION['DBNAME'] = (string)$xml_server->DATABASE;//"CKXU";
                if((string)$xml_server->RESOLVE == 'URL'){
                    $_SESSION['DBHOST'] = (string)$xml_server->URL;
                }
                else{
                    $_SESSION['DBHOST'] = (string)$xml_server->IPV4;
                }
                //$_SESSION['DBHOST'] = "172.22.100.25";
                $_SESSION['SRVPOST'] = (string)$xml_server->ID;//addslashes($_POST['SID']);
                $_SESSION['logo']=$logo;
			    $_SESSION['account'] = $user;
			    $_SESSION['AutoComLimit'] = 8;
			    $_SESSION['AutoComEnable'] = TRUE;
                return true;
            } else {
                // user has no rights
		    echo "Access Denied<br/>";
                return false;
            }

        } else {
            // invalid name or password
	    echo "<span style='color: red;'>Invalid Username or password using <span style='color: blue;'>$ldap_usr_dom\\$bindUser</span> with password ".
        isset($bindpassword)."<br/><br/></span>";
            return false;
        }
    }
    catch (Exception $e){
        die("could not bind to server... error thrown");
    }
}
?>
