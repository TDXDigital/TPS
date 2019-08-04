<?php
    /*if(!$mysqli){
        die("<p>Error: No Database Connection</p>");
    }
    if(isset($_GET['m'])){
        $message=urldecode($_GET['m']);
    }*/
?>
<?php
    include implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), 'setup.common.php']);
    if(count(get_included_files()) ==1){
        http_response_code(403);
        $refusal = "<h1>403 Forbidden</h1><p>The requested resource cannot"
                . " be accessed directly</p>";
        die($refusal);
    }
    $message = filter_input(INPUT_GET, 'm' , FILTER_SANITIZE_STRING);
    if(isset($message)){
        echo "<div class=\"panel panel-success\">
    <div class=\"panel-heading\">Message Information</div>
        <div class=\"panel-body\">
            <span>$message</span>
        </div>
    </div>";
    }
?>
<form action='setup.vars.php' method="POST" name="auth">
    <input type='hidden' name='e' value='<?php
        if(isset($_SESSION['max_page']) && is_numeric($_SESSION['max_page'])){
            echo $PAGES[$_SESSION['max_page']][0];
        }
        else{
            echo 'settings';
        }
    ?>'/>
    <input type='hidden' name='q' value='settings'/>
<div class="panel panel-primary">
    <div class="panel-heading">Authentication Settings</div>
    <div class="panel-body">
        <fieldset>
            <div class="row">
                <div class="col-lg-6">
                  <div class="input-group">
                    <span class="input-group-addon">
                        <input id="LDAP_type" type="radio" name="at" value="LDAP"<?php
                            if(isset($_SESSION['authtype'])&&$_SESSION['authtype']==='LDAP'){
                                echo " checked ";
                            }
                            elseif(!isset($_SESSION['authtype'])){
                                echo " checked ";
                            }
                        ?>/>
                    </span>
                    <label  class="form-control" for="LDAP_type">LDAP / LDAP+SSL</label>
                  </div><!-- /input-group -->
                </div><!-- /.col-lg-6 -->
                <div class="col-lg-6">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <input id="SECL_type" type="radio" name="at" value="SECL"<?php
                            if(isset($_SESSION['authtype'])&&$_SESSION['authtype']==="SECL"){
                                echo " checked ";
                            }
                        ?>/>
                    </span>
                    <label  class="form-control" for="SECL_type">Database Secure Authentication</label>
                  </div><!-- /input-group -->
                </div><!-- /.col-lg-6 -->
              </div><!-- /.row -->
              
              <br>
              <div id="LDAP">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">LDAP / LDAP+SSL (LDAPS)</h3>
                    </div>
                    <div class="panel-body">
                        Please complete the following section if you are using a LDAP or LDAPS 
                        form of authentication. Currently manual manipulation of the LDAP_auth.php
                        module in the Security folder must be performed to set the binding username and password.
                        Anonymous binds are not supported at this time. 

                        Please add "WebUsers" and "WebAdmins" group to your LDAP or Active Directory. 
                        Assign "WebAdmins" to Administrators and "WebUsers" to your Users
                    </div>
                </div>
                <div class="input-group">
                    <label for="ld_port" class="input-group-addon">LDAP/LDAPS Port</label>
                    <input id="ld_port" class="form-control" type="number" name="ldp" min="1" <?php
                           if(isset($_SESSION['ldap_port'])&&!is_null($_SESSION['ldap_port'])){
                               echo "value=\"".$_SESSION['ldap_port']."\" ";
                           }
                           else{
                               echo " placeholder=\"636\" ";
                           }

                           ?>/>
                </div>
                <br>
                <div class="input-group">
                    <label for="ldap_user" class="input-group-addon">LDAP/LDAPS Server</label>
                    <input id="ldap_user" class="form-control" type="text" name="lds" 
                            <?php
                           if(isset($_SESSION['ldap_server'])&&!is_null($_SESSION['ldap_server'])){
                               echo " value=\"".$_SESSION['ldap_server']."\" ";
                           }
                           else{
                               echo " placeholder=\"ldap://ldap.server.local/ or ldaps://127.0.0.1/ \" ";
                           }

                           ?>"/>
                </div>
                <br>
                <div class="input-group">
                    <label for="ldap_dn" class="input-group-addon">Base DN</label>
                    <input id="ldap_dn" class="form-control" type="text" name="dn" 
                           <?php
                           if(isset($_SESSION['ldap_dn'])&&!is_null($_SESSION['ldap_dn'])){
                               echo "value=\"".$_SESSION['ldap_dn']."\" ";
                           }
                           else{
                               echo " placeholder=\"CN=Users,DC=forest,DC=consoto,DC=local\" ";
                           }

                           ?>/>
                </div>
                <br>
                <div class="input-group">
                    <label for="ldap_domn" class="input-group-addon">Domain</label>
                    <input id="ldap_domn" class="form-control" type="text" name="domn" 
                           <?php
                           if(isset($_SESSION['ldap_domn'])&&!is_null($_SESSION['ldap_domn'])){
                               echo "value=\"".$_SESSION['ldap_domn']."\" ";
                           }
                           else{
                               echo " placeholder=\"consoto\" ";
                           }

                           ?>/>
                </div>
              </div>
            <br>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Database Secure Authentication</h3>
                </div>
                <div class="panel-body">
                    Database Secure Authentication provides a independent method
                    of Authentication and Authorization for users. This may also
                    be refered to in the program as DBSL,SECL, or Database Secure Login.
                    <br><br>
                    <ul>
                        <li>Usernames may contain only digits, upper and lower case letters and underscores</li>
                        <li>Emails must have a valid email format</li>
                        <li>Passwords must be at least 6 characters long</li>
                        <li>Passwords must contain
                            <ul>
                                <li>At least one upper case letter (A..Z)</li>
                                <li>At least one lower case letter (a..z)</li>
                                <li>At least one number (0..9)</li>
                            </ul>
                        </li>
                        <li>Your password and confirmation must match exactly</li>
                    </ul>
                </div>
            </div>
              <div class="input-group">
                <label for="email" class="input-group-addon">Admin Email</label>
                <input  class="form-control" type="email" name="admail" <?php
                       if(isset($_SESSION['admin_email'])&&!is_null($_SESSION['admin_email'])){
                           echo "value=\"".$_SESSION['admin_email']."\" ";
                       }
                       else{
                           echo " placeholder=\"email@domain.com\" ";
                       }
                       
                       ?> id="email"/>
            </div>
            <br>
                <div class="input-group">
                    <label for="username" class="input-group-addon">Admin Username</label>
                    <input class="form-control" type="text" name="adun" id="username"<?php
                       if(isset($_SESSION['admin_username'])&&!is_null($_SESSION['admin_username'])){
                           echo "value=\"".$_SESSION['admin_username']."\" ";
                       }
                       else{
                           echo " placeholder=\"email@domain.com\" ";
                       }
                       
                       ?>/>
                </div>
                <div class="input-group">
                    <label for="password" class="input-group-addon">Admin Password</label>
                    <input class="form-control" type="password" name="adpw" id="password"/>
                </div>
            <br>
                <div class="input-group">
                    <label for="configmpwd" class="input-group-addon">Confirm Password</label>
                    <input class="form-control" type="password" name="adpwc" id="confirmpwd"/>
                </div>
              <br>
            
            
        </fieldset>
        <br>
        <input class="btn btn-default" type="submit" value="Test" disabled/>
        <input class="btn btn-default" type="submit" onclick="return regformhash(this.form,
                                   this.form.username,
                                   this.form.email,
                                   this.form.password,
                                   this.form.confirmpwd);" value="Next &raquo;"/>
    </div>
</div>
</form>
<script src="../TPSBIN/JS/forms.js"></script>
<script src="../TPSBIN/JS/sha512.js"></script>
