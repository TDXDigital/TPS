<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Update Complete</title>
    </head>
    <body>
        <div class="ui-state-highlight">
            <span>
                COMPLETED: <?php echo $_SESSION['COMPLETE']; ?>
            </span><br>
            <span>
                DUPLICATES (Skipped):<?php echo $_SESSION['DUPLICATE_COUNT']?>
            </span><br>
            <span>
                ERRORS (Omitted):<?php echo $_SESSION['ERROR_COUNT']?>
            </span><br>
            <span>
                TOTAL RECORDS:<?php echo $_SESSION['TOTAL']?>
            </span><br>
            <span>
                RUNTIME:<?php echo $_SESSION['EXEC_TIME']?>
            </span>
        </div>
    </body>
</html>
