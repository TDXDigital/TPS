<?php

// Use in the "Post-Receive URLs" section of your GitHub repo.

if ( isset($_POST['payload']) ) {
    shell_exec( 'cd /var/www/html/ckxu.uleth.ca/public_html/ && git fetch --all && git pull' );
    $headers = array();
    foreach($_SERVER as $key => $value) {
        if (substr($key, 0, 5) <> 'X-Git') {
            continue;
        }
        $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
        $headers[$header] = $value;
    }
    error_log("Recieved GitHub push with ID ".$headers['X-Github-Delivery'].
            " RE:".$headers['X-Github-Event']);
}

?>hi