<?php
include_once '../TPSBIN/functions.php';

if(!constant("HOST")){
    $dbxml = simplexml_load_file("../TPSBIN/XML/DBSETTINGS.xml");
    $SECL_TARGET = filter_input(INPUT_POST, 'D', FILTER_SANITIZE_STRING);
    if($SECL_TARGET==""){
        $SECL_TARGET=$dbxml->SERVER->ID;
    }
    else{
        
    }
    echo "DD:".$SECL_TARGET. ":DD";
    foreach( $dbxml->SERVER as $CONVAR_SECL):
        if((string)$CONVAR_SECL->ID==$SECL_TARGET){
            echo "MATCH";
            define("DBID", $CONVAR_SECL->ID);     // The host you want to connect to.
            define("HOST", $CONVAR_SECL->HOST);     // The host you want to connect to.
            define("USER", easy_decrypt(\ENCRYPTION_KEY, $CONVAR_SECL->USER));    // The database username. 
            define("PASSWORD", easy_decrypt(\ENCRYPTION_KEY, $CONVAR_SECL->PASSWORD));    // The database password. 
            define("DATABASE", $CONVAR_SECL->DATABASE);    // The database name.
        }
        echo "-DD:".$SECL_TARGET. ":DD:".$CONVAR_SECL->ID;
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