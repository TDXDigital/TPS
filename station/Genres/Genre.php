<?php
    session_start();
    $con = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
    $ERR[] = NULL;
    if(!$con){
        array_push($ERR,"<tr><td>E1</td><td>ERROR</td><td>DB NOT AVAILABLE</td><td>".$_SESSION['DBHOST']."</td><td>Check Login</td><td>Check server status</td><td>FATAL</td><td>ERROR</td><td>0001</td></tr>");
    }
    /*if(isset($_GET['genre'])){
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
    }*/
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Genres</title>
        <link href="../../altstyle.css" rel="stylesheet"/>
        <link href="Genres.css" rel="stylesheet"/>
        <link href="../../js/jquery/css/ui-darkness/jquery-ui-1.10.0.custom.min.css" rel="stylesheet"/>
        <script src="../../js/jquery/js/jquery-2.0.3.min.js"></script>
        <script src="../../js/jquery/js/jquery-ui-1.10.0.custom.min.js"></script>
        <script src="Genre.js"></script>
    </head>
    <body>
        <div class="topcontent">
            <img src="../../<?php echo $_SESSION['logo']?>" alt="logo"/>
            <br/>
            <!--<div><h2>Genre Modification</h2></div>-->
        </div>
        <div class="content">
            <div>
                <form id="form1" method="post" method="post" action="CommitGenre.php">
                    <!-- Do this through AJAX???-->
                    <div id="Create">
                <h2>Genre Settings</h2>
                <h3>Edit / Create</h3>
                    <fieldset>
                        <div class="left">
                            <label for="name">Name</label>
                            <br/><input type="text" required placeholder="Unique Name" id="name" name="name"  value="<?php echo $NAME;?>"/>
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
                    <input type="submit" value="Create"/>
                    <input type="reset"/>
                    <input type="button" onclick="window.location.href='../../masterpage.php'" value="Cancel"/>
                </div>
                </div>
                </form>
                
<?php 
                    if(isset($_GET['e'])){
                        echo "<div id=\"error\" class=\"ui-state-error ui-corner-all\">";
                        echo "Error: ".$_GET['e'];
                        echo "</div>";
                    }
?>
                <div id="Edit">
                <h3>Existing</h3>
                <form id="form2" method="POST" action="genreupdate.php">
                    <table>
                        <thead>
                            <tr>
                                <th><span class="ui-icon ui-icon-trash"></span></th>
                                <th>Name</th>
                                <th>CanCon</th>
                                <th>Playlist</th>
                                <th>CC Percentage</th>
                                <th>Pl Percentage</th>
                                <th>CC Type</th>
                                <th>PL Type</th>
                                <th>Programs</th>
                                <th>Station</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                
                                if($ERR[0]!=NULL){
                                   echo array_pop($ERR);
                                }
                                else{
                                   
                                   $QUERY = "SELECT genre.*, (SELECT count(programname) FROM program WHERE program.genre=genre.genreid AND program.active='1') AS PGM_Count, (SELECT count(*) FROM program where program.active='1' group by callsign) AS Total, (SELECT PGM_Count / Total) AS Percent FROM genre";
                                   //echo $QUERY;
                                   if($res = mysqli_query($con,$QUERY)){
                                       while($obj = $res->fetch_object()){
                                           echo "<tr><td><input type='checkbox' onchange='EditOnly()' name='delete[]' value='".$obj->UID."'/>";
                                           echo "<input type='hidden' name='UID[]' value='".$obj->UID."'/></td>";
                                           echo "<td><input onchange='EditOnly()' type='text' min='0' max='900' name='C_Name[]' placeholder='Unique Name' title='Origional:".addslashes($obj->genreid)."' value='".addslashes($obj->genreid)."'/></td>";
                                           echo "<input type='hidden' name='C_OLD_NAME[]' value='".addslashes($obj->genreid)."' />";
                                           echo "<td><input onchange='EditOnly()' type='number' min='0' max='999' name='C_Cancon[]' placeholder='CC/Hr' value='".$obj->cancon."'/></td>";
                                           echo "<td><input onchange='EditOnly()' type='number' min='0' max='200' name='C_Playlist[]' placeholder='PL/Hr' value='".$obj->playlist."'/></td>";
                                           echo "<td><input onchange='EditOnly()' type='number' min='0' max='100' name='C_CCPerc[]' placeholder='CC %' value='";
                                           echo floatval($obj->canconperc)*100;
                                           echo "'/>%</td>";
                                           echo "<td><input onchange='EditOnly()' type='number' min='0' max='100' name='C_PlPerc[]' placeholder='PL %' value='";
                                           echo floatval($obj->playlistperc)*100;
                                           echo "'/>%</td>";
                                           echo "<td><select onchange='EditOnly()' name='C_CCType[]'>";
                                           if($obj->CCType == 1){
                                               echo"<option value='1' selected>Numeric</option><option value='0'>Percentage</option>";
                                           }
                                           else if($obj->CCType == 0){
                                               echo"<option value='1'>Numeric</option><option selected value='0'>Percentage</option>";
                                           }
                                           else{
                                               echo "<option value='1'>Numeric</option><option value='0'>Percentage</option><option selected style='color: red;' value='1'>Error / Reset to Percent</option>";
                                           }
                                           echo "</select></td>";
                                           echo "<td><select onchange='EditOnly()' name='C_PlType[]'>";
                                           if($obj->PlType == 1){
                                               echo"<option value='1' selected>Numeric</option><option value='0'>Percentage</option>";
                                           }
                                           else if($obj->PlType == 0){
                                               echo"<option value='1'>Numeric</option><option selected value='0'>Percentage</option>";
                                           }
                                           else{
                                               echo "<option value='1'>Numeric</option><option value='0'>Percentage</option><option selected style='color: red;' value='1'>Error / Reset to Percent</option>";
                                           }
                                           echo "</select></td>";
                                           echo "<td>".$obj->PGM_Count." (".($obj->Percent*100)."%)</td>";
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
                        <input type="submit" value="Save Changes" />
                        <input type="reset"/>
                        <input type="button" value="Cancel" onclick="window.location.href='../../masterpage.php'"/>
                    </div>
                    </form>
                 </div>
            </div>
        </div>
    </body>
</html>
