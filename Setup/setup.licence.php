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
<form action='?q=db' method="GET" name="lic">
    <input type='hidden' name='e' value='db'/>
    <input type='hidden' name='q' value='db'/>
<div class="panel panel-primary">
    <div class="panel-heading">Please read the following licenses required for this software.</div>
    <div class="panel-body">
        <p>
            <?php
            $lic = file_get_contents('../LICENSE');
            echo nl2br($lic);
            ?>
        </p>
        <input type="checkbox" required name='eula'/><span>I have read, understand, and agree to be bound by these licenses</span><br>
        <input type="submit" value="Accept"/>
        </form>
<button onclick="close(); return false;" value='Decline'>Decline</button>
    </div>
</div>
</form>
    
