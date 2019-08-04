<?php
    /*if(!$mysqli){
        die("<p>Error: No Database Connection</p>");
    }
    if(isset($_GET['m'])){
        $message=urldecode($_GET['m']);
    }*/
    include implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), 'setup.common.php']);
    if(file_exists($xml_path)){
        http_response_code(403);
        $refusal = "<h1>403 Forbidden</h1><p>Your request cannot proceed as the"
                . " this server has already been configured.</p>";
        die($refusal);
    }
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
<form action='setup.vars.php' method="POST" name="complete">
    <input type='hidden' name='e' value='<?php
        if(isset($_SESSION['max_page']) && is_numeric($_SESSION['max_page'])){
            echo $PAGES[$_SESSION['max_page']][0];
        }
        else{
            echo 'complete';
        }
    ?>'/>
    <input type='hidden' name='q' value='complete'/>
<div class="panel panel-primary">
    <div class="panel-heading">Installation</div>
    <div class="panel-body">
        <fieldset>
            <div class="row">
                <div class="col-lg-6">
                    <strong>Please Wait while your system is installed.</strong>
                    <div class="alert alert-warning" role="alert"><span class="glyphicon glyphicon-warning-sign"></span> IMPORTANT: DO NOT REFRESH YOUR BROWSER</div>
                    <div id="complete" class="alert alert-success" role="alert"><span class="glyphicon glyphicon-check"></span><span id="completed"></span></div>
                  <br>
                    <div class="progress">
                        <div id="install_progress_bar" class="progress-bar progress-bar-striped active"  role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                            <span id="progress_status">Starting Installation</span><span class="dots">...</span>
                        </div>
                    </div>
                </div><!-- /.col-lg-3 -->
            </div>
            <div class="row">
                <!--<div class="col-lg-6">
                    <input class="btn btn-default" type="submit" value="Begin Installation" onclick="install_db(); return false"/>
                    <input class="btn btn-default" type="submit" value="Create Auth Login" onclick="install_xml(); return false"/>
                    <input class="btn btn-default" type="submit" value="Create Administrator" onclick="create_admin(); return false"/>
                    <input class="btn btn-default" type="submit" value="Perform Updates" onclick="perform_updates(); return false"/>
                </div>-->
            </div>
        </fieldset>
        <br>
        <input id="next" class="btn btn-default" type="submit" value="Next &raquo;" disabled/>
        </form>
    </div>
</div>
<script src="install.js" type="text/javascript"></script>
