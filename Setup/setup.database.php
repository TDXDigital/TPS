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
<form action='setup.vars.php' method="POST" name="db">
    <input type='hidden' name='e' value='<?php
        if(isset($_SESSION['max_page']) && is_numeric($_SESSION['max_page'])){
            echo $PAGES[$_SESSION['max_page']][0];
        }
        else{
            echo 'auth';
        }
    ?>'/>
    <input type='hidden' name='q' value='auth'/>
<div class="panel panel-primary">
    <div class="panel-heading">MySQL Database Settings</div>
    <div class="panel-body">
        <fieldset>
            <div class="input-group">
                <label for="hostname" class="input-group-addon">Host</label>
                <input id="hostname" class="form-control" type="text" name="host" required <?php
                       if(isset($_SESSION['host'])&&!is_null($_SESSION['host'])){
                           echo "value=\"".$_SESSION['host']."\" ";
                       }
                       else{
                           echo " value=\"localhost\" ";
                       }
                       
                       ?>/>
            </div>
            <br>
            <div class="input-group">
                <label for="port" class="input-group-addon">Port</label>
                <input id="port" class="form-control" type="number" name="port" required min="0" <?php
                       if(isset($_SESSION['port'])&&!is_null($_SESSION['port'])){
                           echo "value=\"".$_SESSION['port']."\" ";
                       }
                       else{
                           echo " value=\"3306\" ";
                       }
                       
                       ?>/>
            </div>
            <br>
            <div class="input-group">
                <label for="user" class="input-group-addon">Database</label>
                <input id="user" class="form-control" type="text" name="database" 
                       required value="<?php
                       if(isset($_SESSION['database'])&&!is_null($_SESSION['database'])){
                           echo $_SESSION['database'];
                       }
                       else{
                           // Generate fairly random db name
                           echo "TPS-".rand(0,2000);
                           if(rand(1,10)%2){
                               echo "tbr";
                           }
                           echo "_".rand(100,100000);
                       }
                       
                       ?>"/>
            </div>
            <br>
            <div class="input-group">
                <label for="dbuser" class="input-group-addon">Database User</label>
                <input id="dbuser" class="form-control" type="text" name="r" 
                       required <?php
                       if(isset($_SESSION['user'])&&!is_null($_SESSION['user'])){
                           echo "value=\"".$_SESSION['user']."\" ";
                       }
                       else{
                           echo " placeholder=\"root not recommended\" ";
                       }
                       
                       ?>/>
            </div>
            <br>
            <div class="input-group">
                <label for="password" class="input-group-addon">Password</label>
                <input id="password" class="form-control" type="password" name="d" 
                       required/>
            </div>
            
            
            
        </fieldset>
        <br>
        <input class="btn btn-default" type="submit" value="Test" disabled/>
        <input class="btn btn-default" type="submit" value="Next &raquo;"/>
        </form>
    </div>
</div>
</form>
    
