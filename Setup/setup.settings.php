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
        </fieldset>
        <br>
        <input class="btn btn-default" type="submit" value="Next &raquo;"/>
        </form>
    </div>
</div>
</form>
    
