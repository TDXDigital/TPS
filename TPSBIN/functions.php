<?php
#if(!function_exists('CheckUpdateStatus')){
    # not elegant but prevents multiple includes

    //namespace TPS;
    date_default_timezone_set('UTC');

    //error_reporting(0);
    define("ENCRYPTION_KEY", "!@#$%^&*");

    function findHOME($i,$path, $max,$file){
        if(file_exists($path.DIRECTORY_SEPARATOR.$file)){
            return $path;
        }
        elseif($i>$max){
            return false;
        }
        else{
            $i++;
            return findHOME($i, dirname($path), $max, "CONFIG.php");
        }
    }

    $TPSBIN = dirname(__FILE__);#dirname(findHOME(0,__DIR__,10,"functions.php"));

    /*
     *
     *
     */
    $rep_level = error_reporting();
    //error_reporting(0);
    function absolute_include($file,$PHP_SELF)
    {
        global $TPSBIN;
        include_once $TPSBIN.DIRECTORY_SEPARATOR.$file;
    }

    try{
        $configFile = dirname(__FILE__).DIRECTORY_SEPARATOR."CONFIG.php";
        if(file_exists($configFile)){
            include $configFile;
        }
        #absolute_include('CONFIG.php',__DIR__);
    }
    catch (Exception $e){
        #error_log($e->getMessage()."; setting timezone to UTC as failback");
        date_default_timezone_set('UTC');
    }

    if(isset($timezone)){
        date_default_timezone_set($timezone);
    }



    /**
     * Sets connection parameters for SECL logn (DB)
     */
     function set_db_params($dbxml,$target){
          $assigned=false;
          foreach ($dbxml->SERVER as $server){
              if((string)$server->ID === $target){
                  if($server->RESOLVE === "URL")
                  {
                    define("DBHOST",$server->URL);
                  }
                  elseif($server->RESOLVE === "IPV4")
                  {
                    define("DBHOST",$server->IPV4);
                  }
                  else
                  {
                      if($server->URL!=""){
                          define("DBHOST",$server->URL);
                      }
                      else{
                          define("DBHOST",$server->IPV4);
                      }
                  }
                  //define("HOST",  constant('HOST') );
                  define("USER",easy_decrypt(ENCRYPTION_KEY,(string)$server->USER));
                  define("PASSWORD",easy_decrypt(ENCRYPTION_KEY,(string)$server->PASSWORD));
                  define("DATABASE",(string)$server->DATABASE);
                  $assigned=true;
              }
          }
        return $assigned;
    }

    /**
     * Returns an encrypted & utf8-encoded
     */
    function encrypt($pure_string, $encryption_key) {
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $encryption_key, utf8_encode($pure_string), MCRYPT_MODE_ECB, $iv);
        return $encrypted_string;
    }

    /**
     * Returns decrypted original string
     */
    function decrypt($encrypted_string, $encryption_key) {
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $encryption_key, $encrypted_string, MCRYPT_MODE_ECB, $iv);
        return $decrypted_string;
    }

   /*
    * @abstract Encryt/decrypt a string
    * @param $action str  'encrypt'/'decrypt'
    * @param @string str  String to encrypt/decrypt
    * Source: https://gist.github.com/joashp/a1ae9cb30fa533f4ad94
    */
    function encrypt_decrypt($action, $string) {
        $output = false;

        $encrypt_method = "AES-256-CBC";

        $secretKeyPath = __DIR__.DIRECTORY_SEPARATOR."XML".DIRECTORY_SEPARATOR."encryptKey.txt";
        if (!file_exists($secretKeyPath)) {
            $myfile = fopen($secretKeyPath, "w");
            $secret_key = md5(microtime().rand());
            fwrite($myfile, $secret_key);
            fclose($myfile);
        } else {
            $myfile = fopen($secretKeyPath, "r");
            $secret_key = fgets($myfile);
            fclose($myfile);
        }

        $secretIVPath = __DIR__.DIRECTORY_SEPARATOR."XML".DIRECTORY_SEPARATOR."encryptIV.txt";
        if (!file_exists($secretIVPath)) {
            $myfile = fopen($secretIVPath, "w");
            $secret_iv = md5(microtime().rand());
            fwrite($myfile, $secret_iv);
            fclose($myfile);
        } else {
            $myfile = fopen($secretIVPath, "r");
            $secret_iv = fgets($myfile);
            fclose($myfile);
        }

        // hash
        $key = hash('sha256', $secret_key);
    
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    function easy_crypt($ekey,$value){
        // return encrypt_decrypt('encrypt', $value);

        $encrypted=base64_encode(
                mcrypt_encrypt(
                        MCRYPT_RIJNDAEL_256,
                        md5($ekey),
                        $value,
                        MCRYPT_MODE_CBC,
                        md5(md5($ekey))
                        )
                );
        return $encrypted;

    }

    function easy_decrypt($ekey,$encr_string){
        // return encrypt_decrypt('decrypt', $encr_string);

        $decrypted=rtrim(
                mcrypt_decrypt(
                        MCRYPT_RIJNDAEL_256,
                        md5($ekey),
                        base64_decode($encr_string),
                        MCRYPT_MODE_CBC,
                        md5(md5($ekey))),
                "\0");
        return $decrypted;

    }

    /**
    * @return bool
    */
    function is_session_started()
    {
        if ( php_sapi_name() !== 'cli' ) {
            if ( version_compare(phpversion(), '5.4.0', '>=') ) {
                return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
            } else {
                return session_id() === '' ? FALSE : TRUE;
            }
        }
        return FALSE;
    }

    include_once 'psl-config.php';
    // As functions.php is not included
    function sec_session_start() {
        if(!isset($_SESSION)){
            session_start();
        }
    /*
        $session_name = 'sec_session_id';   // Set a custom session name
        $secure = SECURE;
        // This stops JavaScript being able to access the session id.
        $httponly = true;
        // Forces sessions to only use cookies.
        if (ini_set('session.use_only_cookies', 1) === FALSE) {
            header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
            exit();
        }
        // Gets current cookies params.
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params($cookieParams["lifetime"],
            $cookieParams["path"],
            $cookieParams["domain"],
            $secure,
            $httponly);
        // Sets the session name to the one set above.
        session_name($session_name);
        session_start();            // Start the PHP session
        session_regenerate_id();    // regenerated the session, delete the old one. */
    }

    function login($email, $password, $mysqli=NULL, $callsign=NULL, $server=NULL) {


        //for dev purpose
        // return true;


        // Using prepared statements means that SQL injection is not possible.
        if($callsign){
            $station = new \TPS\station($callsign);
            return $station->login($email, $password, $server);
        }
        if ($stmt = $mysqli->prepare("SELECT id, username, password, salt, access
            FROM members
           WHERE email = ?
            LIMIT 1")) {
            $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
            $stmt->execute();    // Execute the prepared query.
            $stmt->store_result();

            // get variables from result.
            $stmt->bind_result($user_id, $username, $db_password, $salt, $access);
            $stmt->fetch();

            // hash the password with the unique salt.
            $password = hash('sha512', $password . $salt);
            if ($stmt->num_rows == 1) {
                // If the user exists we check if the account is locked
                // from too many login attempts

                if (checkbrute($user_id, $mysqli) == true) {
                    // Account is locked
                    // Send an email to user saying their account is locked
                    return false;
                } else {
                    // Check if the password in the database matches
                    // the password the user submitted.
                    if ($db_password == $password) {
                        // Password is correct!
                        // Get the user-agent string of the user.
                        $user_browser = $_SERVER['HTTP_USER_AGENT'];
                        // XSS protection as we might print this value
                        $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                        $_SESSION['user_id'] = $user_id;
                        // XSS protection as we might print this value
                        $username = preg_replace("/[^a-zA-Z0-9_\-]+/",
                                                                    "",
                                                                    $username);
                        $_SESSION['username'] = $username;
                        $_SESSION['login_string'] = hash('sha512',
                                  $password . $user_browser);
                        $_SESSION['account'] = $username;
                        $_SESSION['access'] = $access;
                        $_SESSION['account'] = $username;
                        $_SESSION['AutoComLimit'] = 8;
                        $_SESSION['AutoComEnable'] = TRUE;
                        $_SESSION['TimeZone']='UTC'; // this is just the default to be updated after login
                        if($server){
                            $_SESSION['usr'] = easy_decrypt(ENCRYPTION_KEY,(string)$server->USER);
                            $_SESSION['rpw'] = easy_decrypt(ENCRYPTION_KEY,(string)$server->PASSWORD);
                            $_SESSION['DBNAME'] = (string)$server->DATABASE;
                            if((string)$server->RESOLVE == 'URL'){
                                $_SESSION['DBHOST'] = (string)$server->URL;
                            }
                            else{
                                $_SESSION['DBHOST'] = (string)$server->IPV4;
                            }
                            $_SESSION['SRVPOST'] = (string)$server->ID;
                        }
                        else{
                            $_SESSION['DBHOST'] = HOST;
                            $_SESSION['usr'] = USER;
                            $_SESSION['rpw'] = PASSWORD;
                            $_SESSION['DBNAME'] = DATABASE;
                            $_SESSION['SRVPOST'] = 'SECL';
                        }
                        $_SESSION['fname'] = $user_id;
                        $_SESSION['logo'] = 'images/Ckxu_logo_2.png';
                        $_SESSION['m_logo']=$_SESSION['logo'];
                        // Login successful.

                        $stmt2=$mysqli->prepare("SELECT callsign FROM station LIMIT 1");
                        $stmt2->execute();
                        $stmt2->bind_result($stn_callsign);
                        $stmt2->fetch();

                        if(!is_null($stn_callsign)){
                            $_SESSION['CALLSIGN'] = $stn_callsign;
                        }
                        else{
                            error_log("ERROR: No Stations returned for user $user_id; returned ".$stn_callsign);
                        }

                        return true;
                    } else {
                        // Password is not correct
                        // We record this attempt in the database
                        //
                        // needs prepare then execute without mysqlnd
                        //$now = time();
                        /*$stmt->query("INSERT INTO login_attempts(user_id, time)
                                        VALUES ('$user_id', '$now')");*/
                        return false;
                    }
                }
            } else {
                // No user exists.
                return false;
            }
        }
        else{
         // failed to connect
         return false;
        }
    }

    function login_check($mysqli) {
        // Check if all session variables are set
        if (isset($_SESSION['user_id'],
                            $_SESSION['username'],
                            $_SESSION['login_string'])) {

            $user_id = $_SESSION['user_id'];
            $login_string = $_SESSION['login_string'];
            $username = $_SESSION['username'];

            // Get the user-agent string of the user.
            $user_browser = $_SERVER['HTTP_USER_AGENT'];

            if ($stmt = $mysqli->prepare("SELECT password 
                                          FROM members 
                                          WHERE id = ? LIMIT 1")) {
                // Bind "$user_id" to parameter.
                $stmt->bind_param('i', $user_id);
                $stmt->execute();   // Execute the prepared query.
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    // If the user exists get variables from result.
                    $stmt->bind_result($password);
                    $stmt->fetch();
                    $login_check = hash('sha512', $password . $user_browser);

                    if ($login_check == $login_string) {
                        // Logged In!!!!
                        return true;
                    } else {
                        // Not logged in
                        return false;
                    }
                } else {
                    // Not logged in
                    return false;
                }
            } else {
                // Not logged in
                return false;
            }
        } else {
            // Not logged in
            return false;
        }
    }

    function checkbrute($user_id, $mysqli) {
        // Get timestamp of current time
        $now = time();

        // All login attempts are counted from the past 2 hours.
        $valid_attempts = $now - (2 * 60 * 60);

        if ($stmt = $mysqli->prepare("SELECT time 
                                 FROM login_attempts <code><pre>
                                 WHERE user_id = ? 
                                AND time > '$valid_attempts'")) {
            $stmt->bind_param('i', $user_id);

            // Execute the prepared query.
            $stmt->execute();
            $stmt->store_result();

            // If there have been more than 5 failed logins
            if ($stmt->num_rows > 5) {
                return true;
            } else {
                return false;
            }
        }
    }

    function esc_url($url) {

        if ('' == $url) {
            return $url;
        }

        $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

        $strip = array('%0d', '%0a', '%0D', '%0A');
        $url = (string) $url;

        $count = 1;
        while ($count) {
            $url = str_replace($strip, '', $url, $count);
        }

        $url = str_replace(';//', '://', $url);

        $url = htmlentities($url);

        $url = str_replace('&amp;', '&#038;', $url);
        $url = str_replace("'", '&#039;', $url);

        if ($url[0] !== '/') {
            // We're only interested in relative links from $_SERVER['PHP_SELF']
            return '';
        } else {
            return $url;
        }
    }

    function CheckUpdateStatus($current,$db, $r_type){
        /*
         * Gets status of current installation
         */
        // get current settings
        $base = $_SESSION['BASE_REF']?:"";
        require $base.'/CONFIG.php';

        // Prepare Data
        if(is_string($current)){
            $current = intval($current);
        }
        if(is_string($db)){
            $db = intval($db);
        }
        $result_type = strtoupper($r_type);
        unset($r_type);
        $result = [];

        // perform checks
        if($current === $dbBuild){
            $result += ["database"=>True];
        }
        else{
            $result += ["database"=>False];
        }

        if($current === $codeBuild){
            $result += ["code"=>True];
        }
        else{
            $result += ["code"=>False];
        }

        $patches = [];

        $result += ["patches"=>$patches];

        // return result is specified format
        if($result_type==="JSON"){
            return json_encode($result);
        }
        else{
            return $result;
        }
    }
#}

/**
 * Case in-sensitive array_search() with partial matches
 * https://gist.github.com/branneman/951847
 *
 * @param string $needle   The string to search for.
 * @param array  $haystack The array to search in.
 *
 * @author Bran van der Meer <branmovic@gmail.com>
 * @since 29-01-2010
 */
function array_find($needle, array $haystack)
{
    foreach ($haystack as $key => $value) {
        if (false !== stripos($value, $needle)) {
            return $key;
        }
    }
    return false;
}
