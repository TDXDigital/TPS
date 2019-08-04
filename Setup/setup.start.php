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
<form action='setup.vars.php' method="POST">
    <input type='hidden' name='e' value='<?php
        if(isset($_SESSION['max_page']) && is_numeric($_SESSION['max_page'])){
            echo $PAGES[$_SESSION['max_page']][0];
        }
        else{
            echo 'lic';
        }
    ?>'/>
    <input type='hidden' name='q' value='lic'/>
<div class="panel panel-primary">
    <div class="panel-heading">Welcome to TPS Broadcast!</div>
    <div class="panel-body">
        <p>This suite is designed for and 
            developed by Canadian Campus Community Radio Station 
            <a href="http://www.ckxu.com/" target="CKXU">CKXU-FM</a><br>
            During this installation you will need:
        </p>
        <ol>
            <li>Your Database Configuration</li>
            <li>Authentication Information</li>
            <li>Station Information and Requirements</li>
        </ol>
        <p>
            Let's Begin!
        </p>
        <input class="btn btn-default" type="submit" value="Next &raquo;"/>
    </div>
</div>
</form>
    
