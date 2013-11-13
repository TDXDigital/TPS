<?php<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Genres</title>
        <link href="../../altstyle.css" rel="stylesheet"/>
        <style>
            div.content{
                margin: auto;
                border: 1px,line, #000;
                display: inline-grid;
                max-width: 1200px;
                min-width: 300px;
                background-color: #fff;
            }  
            th{
                width: inherit;
            }         
            tr:nth-child(even) {
                background-color: #befdf9;
            }
            table{
                border-collapse:collapse;
                width: 100%;
            }
            tbody, thead, tr, td{
                border:1px solid blue;
                border-collapse:collapse;
                width: auto;
                text-align: center;
            }
            thead{
                background-color: #2266AA;
                color:white;
            }
            h1, h2, h3{
                text-align: left;
            }
        </style>
    </head>
    <body>
        <div class="topcontent">
            <img src="../../<?php echo $_SESSION['logo']?>" alt="logo"/>
            <br/>
            <!--<div><h2>Genre Modification</h2></div>-->
        </div>
        <div class="content">
            <div>
                <p></p>
                <!--<form method="post" method="post">-->
                    <!-- Do this through AJAX???-->
                <h2>Genre Settings</h2>
                <h3>Edit / Create</h3>
                    <fieldset>
                        <div class="left">
                            <label for="name">Name</label>
                            <br/><input type="text" id="name" name="name"/>
                        </div>
                        <div class="left">
                            <label for="cangen">CanCon</label>
                            <br/><input type="number" id="cangen" min="0" name="cangen" placeholder="Defined Number" required/>
                            <br/><input type="number" step="1" min="0" max="100" id="perc" name="canper" placeholder="Percent" required/><span>%</span>
                            <br/><select><option value="Number">Number</option><option value="percentage">Percentage</option></select>
                        </div>
                        <div class="left">
                            <label for="plgen">Playlist</label>
                            <br/><input type="number" id="plgen" name="plnum"/>
                        </div>

                    </fieldset>
                </form>
                
                <h3>Existing</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Name</th>
                                <th>CanCon</th>
                                <th>Playlist</th>
                                <th>CC Percentage</th>
                                <th>Pl Percentage</th>
                                <th>CC Type</th>
                                <th>PL Type</th>
                                <th>programs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $con = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
                                //if(!$con){
                                if(TRUE){
                                    echo "<tr><td>E1</td><td>ERROR</td><td>DB NOT AVAILABLE</td><td>".$_SESSION['DBHOST']."</td><td>Check Login</td><td>Check server status</td>
                                    <td>FATAL</td><td>ERROR</td><td>0001</td></tr>";
                                }
                                for($i=0;$i<10;$i++){
                                    echo "<tr><td>$i</td><td>ERROR</td><td>DB NOT AVAILABLE</td><td>".$_SESSION['DBHOST']."</td><td>Check Login</td><td>Check server status</td>
                                    <td>FATAL</td><td>ERROR</td><td>0001</td></tr>";
                                }
                                //$con.close();
                                mysqli_close($con);

                            ?>
                        </tbody>
                    </table>
            </div>
        </div>
    </body>
</html>
