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
                             maxlength="4" required placeholder="Letters and Numbers only" pattern="[A-Za-z0-9]{1,4}"
                    <?php
                        if(isset($_SESSION['callsign']) && is_string($_SESSION['callsign'])){
                            echo $_SESSION['callsign'];
                        }
                        else{
                        }
                    ?>/>
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
                <div class="col-lg-3">
                  <div class="input-group">
                    <span class="input-group-addon">
                        <label for="frequency"><span class="glyphicon glyphicon-signal"></span> Frequency</label>
                    </span>
                      <input name="frequency"  class="form-control" id="frequency" placeholder="102.9 FM" required/>
                  </div><!-- /input-group -->
                </div><!-- /.col-lg-3 -->
                <div class="col-lg-3">
                  <div class="input-group">
                    <span class="input-group-addon">
                        <label for="phone"><span class="glyphicon glyphicon-earphone"></span> Main Phone</label>
                    </span>
                      <input name="req_ph" type="tel" class="form-control" id="phone" required pattern="[0-9 ]{5,12}"/>
                  </div><!-- /input-group -->
                </div><!-- /.col-lg-3 -->
                <div class="col-lg-3">
                  <div class="input-group">
                    <span class="input-group-addon">
                        <label for="mgr_ph"><span class="glyphicon glyphicon-phone-alt"></span> Manager Phone</label>
                    </span>
                      <input name="mgr_ph" type="tel" class="form-control" id="mgr-ph" required pattern="[0-9 ]{5,12}"/>
                  </div><!-- /input-group -->
                </div><!-- /.col-lg-3 -->
                <div class="col-lg-3">
                  <div class="input-group">
                    <span class="input-group-addon">
                        <label for="pd_ph"><span class="glyphicon glyphicon-phone"></span> PD Phone</label>
                    </span>
                      <input name="pd_ph" type="tel" class="form-control" id="pd_ph" required pattern="[0-9 ]{5,12}"/>
                  </div><!-- /input-group -->
                </div><!-- /.col-lg-3 -->
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
                <div class="col-lg-3">
                  <div class="input-group">
                    <span class="input-group-addon">
                        <label for="logo"><span class="glyphicon glyphicon-picture"></span> Logo</label>
                    </span>
                      <input name="logo" type="file" class="form-control" id="logo"/>
                  </div><!-- /input-group -->
                </div><!-- /.col-lg-3 -->
                <div class="col-lg-3">
                  <div class="input-group">
                      <select class="selectpicker show-tick">
                          <optgroup label="NCRA/ANREC">
                            <option value="CC">Campus Community</option>
                            <option value="CM">Campus</option>
                            <option value="CO">Community</option>
                            <option value="CI">Instructional</option>
                            <option value="AO">Online Only</option>
                          <optgroup label="Commercial">
                          <option value="SC" >Commercial</option>
                          <optgroup label="Other">
                            <option value="OS">Specialty</option>
                            <option value="OO">Online Only</option>
                            <option value="OF">FCC Regulated</option>
                          </optgroup>
                      </select>
                  </div><!-- /input-group -->
                </div><!-- /.col-lg-3 -->
              </div>
        </fieldset>
        <br>
        <input class="btn btn-default" type="submit" value="Next &raquo;"/>
        </form>
    </div>
</div>
</form>
    
