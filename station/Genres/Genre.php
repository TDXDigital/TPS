<?php<?php
    session_start();
    $con = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
    $ERR[] = NULL;
    if(!$con){
        array_push($ERR,"<tr><td>E1</td><td>ERROR</td><td>DB NOT AVAILABLE</td><td>".$_SESSION['DBHOST']."</td><td>Check Login</td><td>Check server status</td><td>FATAL</td><td>ERROR</td><td>0001</td></tr>");
    }
    if(isset($_GET['genre'])){
        $UID_GET = addslashes($_GET['genre']);
        $SQL_FETCH = "SELECT * FROM genre WHERE UID='$UID_GET'";
        if($dat = mysqli_query($con,$SQL_FETCH)){
            while($row = $dat->fetch_object()){
                $UID = $row->UID;
                $NAME = htmlspecialchars($row->genreid);
                $CCNUM = $row->cancon;
                $CCPERC = floatval($row->canconperc)*100;
                $PLNUM = $row->playlist;
                $PLPERC = floatval($row->playlistperc)*100;
                $CCTYPE = $row->CCType;
                $PLTYPE = $row->PlType;
            }
        }
    }
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
                <form method="post" method="post" action="CommitGenre.php">
                    <!-- Do this through AJAX???-->
                <h2>Genre Settings</h2>
                <h3>Edit / Create</h3>
                    <fieldset>
                        <div class="left">
                            <label for="name">Name</label>
                            <br/><input type="text" required placeholder="Unique Name" id="name" name="name" value="<?php echo $NAME;?>"/>
                            <input type="hidden" value="<?php echo $UID?>" name="UID"/>
                        </div>
                        <div class="left">
                            <label for="station">Station</label>
                            <br/>
                            <select name="station" id="station">
                                <?php
                                    if($ERR[0]!=NULL){
                                        echo "<option value='NULL'>DB ERROR</option>";
                                    }
                                    else{
                                        $STN = "SELECT callsign AS csn, stationname AS name FROM `station`";
                                        if($result = mysqli_query($con,$STN)){
                                            while($SN = $result->fetch_object()){
                                                echo "<option value='".$SN->csn."'>".$SN->name."</option>";
                                            }
                                        }
                                        else{
                                            echo "<option value='NULL'>ERROR</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="left">
                            <label for="cangen">CanCon</label>
                            <br/><input type="number" id="cangen" min="0" name="cangen" placeholder="Defined Number" required value="<?php echo $CCNUM;?>"/>
                            <br/><input type="number" step="1" min="0" max="100" id="ccperc" name="canper" placeholder="Percent" required value="<?php echo $CCPERC;?>"/><label for="ccperc">%</label>
                            <br/><select name="cctype"><option value="1">Number</option><option value="0" selected>Percentage</option></select>
                        </div>
                        <div class="left">
                            <label for="Plgen">Playlist</label>
                            <br/><input type="number" id="Plgen" min="0" name="plgen" placeholder="Defined Number" required value="<?php echo $PLNUM?>"/>
                            <br/><input type="number" step="1" min="0" max="100" id="plperc" name="plperc" placeholder="Percent" required value="<?php echo $PLPERC;?>"/><label for="plperc">%</label>
                            <br/><select name="pltype"><option value="1">Number</option><option value="0" selected>Percentage</option></select>
                        </div>
                    </fieldset>
                <div>
                    <input type="submit" value="Save"/>
                    <input type="reset"/>
                    <input type="button" onclick="window.location.href='../../masterpage.php'" value="Cancel"/>
                </div>
                </form>
                
                <h3>Existing</h3>
                <form method="get">
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
                                <th>Station</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                
                                if($ERR[0]!=NULL){
                                   echo array_pop($ERR);
                                }
                                else{
                                   
                                   $QUERY = "SELECT genre.* FROM genre";
                                   //echo $QUERY;
                                   if($res = mysqli_query($con,$QUERY)){
                                       while($obj = $res->fetch_object()){
                                           echo "<tr><td><input type='radio' name='genre' value='".$obj->UID."'/></td>";
                                           echo "<td>".$obj->genreid."</td>";
                                           echo "<td>".$obj->cancon."</td>";
                                           echo "<td>".$obj->playlist."</td>";
                                           echo "<td>";
                                           echo floatval($obj->canconperc)*100;
                                           echo "%</td>";
                                           echo "<td>";
                                           echo floatval($obj->playlistperc)*100;
                                           echo "%</td>";
                                           echo "<td>";
                                           if($obj->CCType == 1){
                                               echo"Numeric";
                                           }
                                           else if($obj->CCType == 0){
                                               echo"Percentage";
                                           }
                                           else{
                                               echo "Undefined";
                                           }
                                           echo "</td>";
                                           echo "<td>";
                                           if($obj->PlType == 1){
                                               echo"Numeric";
                                           }
                                           else if($obj->PlType == 0){
                                               echo"Percentage";
                                           }
                                           else{
                                               echo "Undefined";
                                           }
                                           echo "</td>";
                                           echo "<td>".$obj->Station."</td>";
                                       }
                                   }
                                   else{
                                       echo "error: ".mysqli_error($con);
                                   }
                                   
                                }
                                /*for($i=0;$i<10;$i++){
                                    echo "<tr><td>$i</td><td>ERROR</td><td>DB NOT AVAILABLE</td><td>".$_SESSION['DBHOST']."</td><td>Check Login</td><td>Check server status</td>
                                    <td>FATAL</td><td>ERROR</td><td>0001</td></tr>";
                                }*/
                                //$con.close();
                                mysqli_close($con);

                            ?>
                        </tbody>
                    </table>
                    <div class="left;">
                        <input type="submit" value="edit" />
                        <input type="submit" value="Remove" disabled/>
                    </div>
                    </form>
            </div>
        </div>
    </body>
</html>
