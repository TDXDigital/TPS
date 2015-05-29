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
<form action='../logout.php' method="POST">
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
    <div class="panel-heading">Final Steps</div>
    <div class="panel-body">
        <p>Congratulations!<br>
            Your setup is almost complete.<br>
            <br>
            There are a few steps remaining for this beta software. <br>
        </p>
        <ol>
            <li><a href='https://github.com/TDXDigital/TPS/wiki/Creating-the-first-user#gui-steps' target="_user">Create first user</a></li>
            <li><a href='https://github.com/TDXDigital/TPS/wiki/Creating-the-first-user#database-changes' target="_Permissions">Assign administrator access</a></li>
            <li><a href='https://github.com/TDXDigital/TPS/wiki/Manual-User-Permissions' target="_Permissions">Create permissions</a></li>
            <li>Create Station</li>
            <li>Delete Setup Folder</li>
            <li>Celebrate!</li>
        </ol>
    </div>
</div>
    <div class="panel panel-primary">
        <div class="panel-heading">Error Reporting</div>
        <div class="panel-body">
            In the event that you have a feature request or bug please feel free
            to report it to github at 
            <a href="https://github.com/TDXDigital/TPS/issues">https://github.com/TDXDigital/TPS/issues</a>
            <br>
            <br>This is a open source platform and contribution is encouraged as long as it is aligned with
            the direction of the project of providing a semi automated logging solution for 
            non commercial radio stations. 
        </div>
    </div>
    <input class="btn btn-default" type="submit" value="Login &raquo;"/>
</form>
    
