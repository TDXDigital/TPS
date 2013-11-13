<?php
    include("../../security/login.php");
    /*
function authenticate($user, $password) {
    // Active Directory server
    $ldap_host = "localhost";

    // Active Directory DN
    $ldap_dn = "CN=Users,DC=ckxu,DC=net";

    // Active Directory user group
    $ldap_user_group = "WebUsers";

    // Active Directory manager group
    $ldap_manager_group = "WebAdmins";

    // Domain, for purposes of constructing $user
    $ldap_usr_dom = "@ckxu.net";

    // connect to active directory
    $ldap = ldap_connect($ldap_host);

    // verify user and password
    if($bind = @ldap_bind($ldap, $user . $ldap_usr_dom, $password)) {
        // valid
        // check presence in groups
        $filter = "(sAMAccountName=" . $user . ")";
        $attr = array("memberof");
        $result = ldap_search($ldap, $ldap_dn, $filter, $attr) or header("location: ../../logs.php?auth=Access Denied - Invalid Credentials");
        $entries = ldap_get_entries($ldap, $result);
        ldap_unbind($ldap);
		$nameLDAP = $entries[0]['givenname'];

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
            	$_SESSION['usr'] = "user";
            	$_SESSION['rpw'] = "abuser";
				$_SESSION['access'] = $access;
				//$_SESSION['name'] = "UNDEFINED USER";
            }
			else if($access == 2){
				$_SESSION['usr'] = "program";
            	$_SESSION['rpw'] = "pirateradio";
				$_SESSION['access'] = $access;
				//$_SESSION['name'] = "UNDEFINED ADMIN";
			}
			$_SESSION['fname'] = $nameLDAP;
			$_SESSION['account'] = $user;
            return true;
        } else {
            // user has no rights
            return false;
        }

    } else {
        // invalid name or password
        return false;
    }
}

// Establish Session
session_start();

// using ldap bind [Get Credentials]
$postuser  = $_POST['name'];// ldap rdn or dn
$postpass = $_POST['pass']; // associated password
if(isset($_POST['return'])){
    $des = $_POST['return'];
}
else{
    $des = 0;
}
$ORIGIN = $_SERVER['HTTP_REFERER'];

if($postpass == "pledge1" && $postuser == "ckxu"){
	$postpass = "Pledge1";
}

if(authenticate($postuser, $postpass)){
    if($des==0){
        header('Location: /masterpage.php');
    }
    else{
        header("Location: $ORIGIN");
    }
	//echo "200";
}
else{
	//echo "<h3>Login Failed</h3><span><br/>You do not have DNS authenticated access<br/></span>";
	//header("location: http://ckxuradio.su.uleth.ca/index.php/digital-program-logs?args=LoginFailedCode1");
	header("Location: $ORIGIN?auth=Access Denied - Invalid Credentials");
	//echo "Login Failed";
}
*/
?>

