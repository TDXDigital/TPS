<?php<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Expired Logs</title>
        <link href="../../altstyle.css" rel="stylesheet"/>
    </head>
    <body>
        <div class="topcontent">
            <img src="../../<?php echo $SESSION['logo']?>" alt="logo"/>
            <br/>
            <div><h2>Genre Modification</h2></div>
        </div>
        <div class="content">
            <div>
                <p></p>
                <!--<form method="post" method="post">-->
                    <!-- Do this through AJAX???-->
                    <fieldset>
                        <span>Genre Settings</span><br/>
                        <div class="left">
                            <label for="name">Name</label>
                            <br/><input type="text" id="name" name="name"/>
                        </div>
                        <div class="left">
                            <label for="cangen">CanCon</label>
                            <br/><input type="text" id="cangen" name="cangen"/>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </body>
</html>
