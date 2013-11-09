<?php<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Expired Logs</title>
        <link href="../altstyle.css" rel="stylesheet"/>
    </head>
    <body>
        <div class="topcontent">
            <img src="../<?php echo $SESSION['logo']?>" alt="logo"/>
            <br/>
            <div><h2>Genre Modification</h2></div>
        </div>
        <div class="content">
            <div>
                <p>Please confirm settings for deletion of old logs</p>
                <form method="post" action="confirmexpire.php">
                    <!-- Do this through AJAX???-->
                    <fieldset>
                        <label for="timestamp_verify">Verify Timestamp</label>
                        <input name="timestamp_verify" id="timestamp_verify" type="checkbox"/>
                        <label for="soft" title="Only disabled/inactive programs will be removed">Soft clearance only</label>
                        <input name="soft_only" id="soft" type="checkbox" checked/>
                        <br/>
                        <label for="threshold">Threshold Date</label>
                        <input name="threshold" type="date" id="threshold" required value="<?php
                            //$date = strtotime("now -18 months");
                            $date2 = date("Y-m-d", strtotime("first day of this month -18 months"));
                            //$mo18 = date_format($date, 'Y-m-d');
                            //$mo18 = date($date, 'Y-m-d');
                            echo $date2."\" max=\"";
                            echo date("Y-m-d", strtotime("now - 12 months"));
                            ?>"><span style="color:red" title="Dates within one year are not selectible due to minimum reporting age">*</span>
                        <hr/>
                        <!--<button onclick="window.location.href='../../masterpage.php'">Cancel</button>-->
                        <a href="../masterpage.php">Cancel</a>
                        <input type="reset" value="Reset"/>
                        <input type="submit" value="Submit"/>
                    </fieldset>
                </form>
            </div>
        </div>
    </body>
</html>
