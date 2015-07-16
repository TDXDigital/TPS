<?php

// Use in the "Post-Receive URLs" section of your GitHub repo.

if ( $_POST['payload'] ) {
  shell_exec( 'cd /var/www/ckxu.uleth.ca/public_html/ && git fetch --all && git pull' );
}

?>hi