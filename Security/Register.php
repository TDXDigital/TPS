<?php
include_once '../TPSBIN/functions.php';

if(!defined("HOST")||!defined("DATABASE")||!defined("USER")||!defined("PASSWORD")){
    $dbxml = simplexml_load_file("../TPSBIN/XML/DBSETTINGS.xml");
    $SECL_TARGET = filter_input(INPUT_POST, 'D', FILTER_SANITIZE_STRING);
    if($SECL_TARGET==""){
        $SECL_TARGET=$dbxml->SERVER->ID;
    }
    else{
        
    }
    echo "DT:".$SECL_TARGET. ":DT</br>";
    foreach( $dbxml->SERVER as $CONVAR_SECL):
        if((string)$CONVAR_SECL->ID==$SECL_TARGET){
            echo "MATCH";
            define("DBID", (string)$CONVAR_SECL->ID);     // The host you want to connect to.
            echo "ID:" . (string)$CONVAR_SECL->ID . "</br>";
            define("HOST", (string)$CONVAR_SECL->IPV4);     // The host you want to connect to.
            define("DATABASE", (string)$CONVAR_SECL->DATABASE);     // The host you want to connect to.
            echo "HOST:" . (string)$CONVAR_SECL->IPV4 . "</br>";
            echo "KEY:" .ENCRYPTION_KEY."</br>";
            define("USER", easy_decrypt(\ENCRYPTION_KEY, (string)$CONVAR_SECL->USER));    // The database username. 
            echo "USER:".easy_decrypt(ENCRYPTION_KEY, $CONVAR_SECL->USER)."</br>";
            define("PASSWORD", easy_decrypt(\ENCRYPTION_KEY, (string)$CONVAR_SECL->PASSWORD));    // The database password.             define("DATABASE", $CONVAR_SECL->DATABASE);    // The database name.
            echo "PASSWD:".easy_decrypt(ENCRYPTION_KEY, $CONVAR_SECL->PASSWORD)."</br>";
        }
        else{
            echo "NM:".$SECL_TARGET. ":NM:".$CONVAR_SECL->ID."</br>";
        }
        //var_dump($CONVAR_SECL);
    endforeach;
}

include_once '../TPSBIN/register.inc.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Secure Login: Registration Form</title>
        <script type="text/JavaScript" src="../TPSBIN/JS/sha512.js"></script> 
        <script type="text/JavaScript" src="../TPSBIN/JS/forms.js"></script>
        <link rel="stylesheet" href="../phpstyle.css" />
    </head>
    <body>
        <!-- Registration form to be output if the POST variables are not
        set or if the registration script caused an error. -->
        <h1>Register with us</h1>
        <?php
        if (!empty($error_msg)) {
            echo $error_msg;
        }
        ?>
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
        <form action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>" 
                method="post" 
                name="registration_form">
            Username: <input type='text' 
                name='username' 
                id='username' /><br>
            Email: <input type="text" name="email" id="email" /><br>
            Password: <input type="password"
                             name="password" 
                             id="password"/><br>
            Confirm password: <input type="password" 
                                     name="confirmpwd" 
                                     id="confirmpwd" /><br>
            <input type="button" 
                   value="Register" 
                   onclick="return regformhash(this.form,
                                   this.form.username,
                                   this.form.email,
                                   this.form.password,
                                   this.form.confirmpwd);" /> 
        </form>
        <p>Return to the <a href="Login.html">login page</a>.</p>
    </body>
</html>