<?php
    //Start Session
    session_start();

    //GET Session Number ??? <TODO>

    //Check for Session Based Credentials
    if(isset($_SESSION['usr'])){
        $USER = $_SESSION['usr'];
        $PASS = $_SESSION['rpw'];
        $DBNAME = $_SESSION['DBNAME'];
        $DBHOST = $_SESSION['DBHOST'];
    }
    else{
        // Could not get username, therefore login will fail
        // Prompt For New Login
        header("location: ".$_SERVER['REFERER']);
    }
    //GET Values from URL
    if(isset($_GET['sact'])){
        $ACT_ONLY = addslashes($_GET['sact']);
        switch ($_GET['sact'])
        {
            case '1':
            $ACT_ONLY = TRUE;
            break;
            default:
            $ACT_ONLY = FALSE;
        }
    }
    else{
        $ACT_ONLY = FALSE;
    }

    //Check for DB Access
    $db = new mysqli($DBHOST, $USER, $PASS, $DBNAME);

    if($db->connect_errno > 0){
        die('Unable to connect to database [' . $db->connect_error . ']');
    }
    else{ 
        echo "<!-- DB Connection Established  -->"; // Not Needed but used for debug
    }
    if($ACT_ONLY){
$sql_PGMS = <<<SQL
    SELECT *
    FROM program
    WHERE active = '1'
    ORDER BY programname ASC
SQL;
    }
    else{
$sql_PGMS = <<<SQL
    SELECT *
    FROM program
    ORDER BY programname ASC
SQL;
        
    }

if(!$pgm = $db->query($sql_PGMS)){
    die('There was an error running the query [' . $db->error . ']');
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Ad Comparison</title>
        <link href="/js/jquery/css/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" rel="stylesheet"/>
        <link href="/CSS/Advertisements/AdInfo/AdInfo.css" type="text/css" rel="stylesheet"/>
        <script src="/js/jquery/js/jquery-1.9.1.min.js" type="text/javascript"></script>
        <script src="/js/jquery/js/jquery-ui-1.10.0.custom.min.js" type="text/javascript"></script>
        <script src="/JS/Advertisements/AdInfo/AdInfo.js" type="text/javascript"></script>
        <script src="http://jqueryui.com/themeroller/themeswitchertool/" type="text/javascript"></script>
        <script type="text/javascript">
        $(document).ready(function(){
         $('#switcher').themeswitcher();
        });
        </script>
    </head>
    <body>
        <div id="switcher"></div>
        <div id="infobar">
            <form method="GET">
                <div class="inline">
                    <label for="DB">Database</label><br/>
                    <input type="text" disabled readonly="true" id="DBBOX" title="Database" value="<?php echo $DBNAME ?>"/>
                </div>
                <div class="inline">
                    <label for="ActiveOnly">Active Only</label><br/>
                    <input id="ActiveOnly" type="checkbox" name="sact" <?php if($ACT_ONLY){ echo "checked"; }?> />
                </div>
                <div class="inline">
                    <input type="submit" value="Refresh"/>
                </div>
                <div class="inlinebuffer">
                </div>
                </form>
        </div>
        <div id="settings">
        <form method="post">
                <div>
                    <label for="pgm">Program</label><br/>
                    <select id="pgm">
                        <option value="%" selected>All</option>
                        <?php
                            while($row = $pgm->fetch_assoc()){
                                echo "<option value='".$row['ProgramID']."'>".$row['programname']."</option>";
                            }
                        ?>
                    </select>
                </div>
            </form></div>
        <div id="results"></div>
        <div id="footer"></div>
    </body>
</html>
