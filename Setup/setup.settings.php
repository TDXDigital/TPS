<?php
    /*if(!$mysqli){
        die("<p>Error: No Database Connection</p>");
    }
    if(isset($_GET['m'])){
        $message=urldecode($_GET['m']);
    }*/
?>
<?php
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
<form action='setup.vars.php' method="POST" name="settings">
    <input type='hidden' name='e' value='<?php
        if(isset($_SESSION['max_page']) && is_numeric($_SESSION['max_page'])){
            echo $PAGES[$_SESSION['max_page']][0];
        }
        else{
            echo 'rev';
        }
    ?>'/>
    <input type='hidden' name='q' value='rev'/>
<div class="panel panel-primary">
    <div class="panel-heading">Corporate Settings</div>
    <div class="panel-body">
        <fieldset>
            <div class="row">
                <div class="col-lg-6">
                  <div class="input-group">
                    <span class="input-group-addon">
                        <label for="callsign"><span class="glyphicon glyphicon-flash"></span> Callsign (4 characters)</label>
                    </span>
                      <input name="callsign" type="text" class="form-control" id="callsign" 
                             maxlength="4" required placeholder="Letters and Numbers only" pattern="[A-Za-z0-9]"/>
                  </div><!-- /input-group -->
                </div><!-- /.col-lg-6 -->
                <div class="col-lg-6">
                  <div class="input-group">
                    <span class="input-group-addon">
                        <label for="brand"><span class="glyphicon glyphicon-tint"></span> Name / Brand</label>
                    </span>
                      <input name="brand" type="text" class="form-control" id="brand" maxlength="20" required/>
                  </div><!-- /input-group -->
                </div><!-- /.col-lg-6 -->
              </div><!-- /.row -->
              <br>
              <div class="row">
                <div class="col-lg-6">
                  <div class="input-group">
                    <span class="input-group-addon">
                        <label for="website"><span class="glyphicon glyphicon-globe"></span> Website</label>
                    </span>
                      <input name="website" type="url" class="form-control" id="website" 
                              placeholder="must include http:// or https://"/>
                  </div><!-- /input-group -->
                </div><!-- /.col-lg-6 -->
                <div class="col-lg-6">
                  <div class="input-group">
                    <span class="input-group-addon">
                        <label for="brand"><span class="glyphicon glyphicon-tint"></span> Name / Brand</label>
                    </span>
                      <input name="brand"  class="form-control" id="brand" maxlength="20" required/>
                  </div><!-- /input-group -->
                </div><!-- /.col-lg-6 -->
              </div><!-- /.row -->
              <br>
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
                <label for="admail" class="input-group-addon">Admin Email</label>
                <input id="admail" class="form-control" type="text" name="admail" <?php
                       if(isset($_SESSION['admin_email'])&&!is_null($_SESSION['admin_email'])){
                           echo "value=\"".$_SESSION['admin_email']."\" ";
                       }
                       else{
                           echo " placeholder=\"email@domain.com\" ";
                       }
                       
                       ?>/>
            </div>
            <br>
              <div class="input-group">
                <label for="adpw" class="input-group-addon">Admin Password</label>
                <input id="adpw" class="form-control" type="password" name="adpw"/>
            </div>
              <br>
            
            
        </fieldset>
        <br>
        <input class="btn btn-default" type="submit" value="Test" disabled/>
        <input class="btn btn-default" type="submit" value="Next &raquo;"/>
        </form>
    </div>
</div>
</form>
    
