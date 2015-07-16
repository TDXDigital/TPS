<?php

// Use in the "Post-Receive URLs" section of your GitHub repo.

if ( isset($_POST['payload']) ) {
  shell_exec( 'cd /var/www/html/ckxu.uleth.ca/public_html/ && git fetch --all && git pull' );
}

?>hi