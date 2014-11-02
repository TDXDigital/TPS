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
<form action='setup.vars.php' method="POST" name="rev">
    <input type='hidden' name='e' value='<?php
        if(isset($_SESSION['max_page']) && is_numeric($_SESSION['max_page'])){
            echo $PAGES[$_SESSION['max_page']][0];
        }
        else{
            echo 'inst';
        }
    ?>'/>
    <input type='hidden' name='q' value='inst'/>
<div class="panel panel-primary">
    <div class="panel-heading">Corporate Settings</div>
    <div class="panel-body">
        <fieldset>
            <div class="row">
                <div class="col-lg-6">
                    <strong>Please Wait while your system is installed.</strong>
                    <div class="alert alert-warning" role="alert"><span class="glyphicon glyphicon-warning-sign"></span> IMPORTANT: DO NOT REFRESH YOUR BROWSER</div>
                  <br>
                    <div class="progress">
                        <div id="install_progress_bar" class="progress-bar progress-bar-striped active"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                            <span id="progress_status">Starting Installation</span><span class="dots">...</span>
                        </div>
                    </div>
                </div><!-- /.col-lg-3 -->
                <input class="btn btn-default" type="submit" value="Begin Installation" onclick="install_db();"/>
              </div>
        </fieldset>
        <br>
        <input class="btn btn-default" type="submit" value="Next &raquo;" disabled/>
        </form>
    </div>
</div>
<script src="install.js" type="text/javascript"></script>
