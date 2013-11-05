<?php
    
/*
 * LDAP Configuration
 *
 */

 // Use LDAP
 $ldap = TRUE;

 //Over SSL
 $ldap_ssl = FALSE;

 //Bind Username
 $bind_username = "admin";
 
 // Active Directory server
 $ldap_host = "127.0.0.1";
 
 // LDAP Port
 $ldap_port = 389;
 
 // Active Directory DN
 $ldap_dn = "CN=Users,DC=ckxu,DC=net";
 
 // Active Directory user group
 $ldap_user_group = "WebUsers";
 
 // Active Directory manager group
 $ldap_manager_group = "WebAdmins";
 
 // Domain, for purposes of constructing $user
 $ldap_usr_dom = "CKXU";

?>