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
        <title> Audits</title>
        <link href="../../altstyle.css" rel="stylesheet"/>
        <link href="Audit.css" rel="stylesheet"/>
        <link href="../../js/jquery/css/ui-darkness/jquery-ui-1.10.0.custom.min.css" rel="stylesheet"/>
        <script src="../../js/jquery/js/jquery-2.0.3.min.js"></script>
        <script src="../../js/jquery/js/jquery-ui-1.10.0.custom.min.js"></script>
        <script src="Audit.js"></script>
    </head>
    <body>
        <div class="topcontent">
            <img src="../../<?php echo $_SESSION['logo']?>" alt="logo"/>
            <br/>
            <!--<div><h2>Genre Modification</h2></div>-->
        </div>
        <div class="content">
            <div>
                <form id="form1" method="post" method="post" action="CommitAudit.php">
                    <!-- Do this through AJAX???-->
                    <div id="Create">
                <h2>Audits and Traffic Enforcement</h2>
                <h3>Edit / Create</h3>
                    <fieldset>
                        <div class="left">
                            <label for="name">Description</label>
                            <br/><input type="text" placeholder="Description (Optional)" id="name" name="description" />
                            <input type="hidden" value="<?php echo $UID?>" name="AuditID"/>
                        </div>
                        <div class="left">
                            <label for="prompt">Notify</label>
                            <br/><input type="checkbox" id="prompt" name="prompt" checked/>
                        </div>
                        <div class="left">
                            <label for="prompt">Enabled</label>
                            <br/><input type="checkbox" id="enabled" name="enabled" checked/>
                        </div>
                        <div class="left">
                            <label for="prompt">Lead Time</label>
                            <br/><input type="checkbox" disabled id="prompt" name="prompt" checked/>
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
                            <fieldset>
                            <label for="cangen">Require</label>
                                <br/><input type="checkbox" id="Artist" min="0" name="Artist" /><label for="Artist">Artist</label>
                                <br/><input type="checkbox" id="Album" min="0" name="Album" /><label for="Album">Album</label>
                                <br/><input type="checkbox" id="Composer" min="0" name="Composer" /><label for="Composer">Composer</label>
                            </fieldset>
                        </div>
                        <div class="left">
                            <fieldset>
                                <label for="Plgen">Dates</label>
                                <br/><label for="start">Start</label><input type="date" id="start" name="start" required/>
                                <br/><label for="end">End&nbsp;</label><input type="date" id="end" name="end" required/>
                            </fieldset>
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
                <form id="form2" method="POST" action="Auditupdate.php">
                    <table>
                        <thead>
                            <tr>
                                <th><span class="ui-icon ui-icon-trash"></span></th>
                                <th>Description</th>
                                <th>Enabled</th>
                                <th>Require<br/>Artist</th>
                                <th>Require<br/>Album</th>
                                <th>Require<br/>Composer</th>
                                <th>Date<br/>Start</th>
                                <th>Date<br/>End</th>
                                <th>Display<br/>Prompt</th>
                                <th>Station</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                
                                if($ERR[0]!=NULL){
                                   echo array_pop($ERR);
                                }
                                else{
                                   
                                   $QUERY = "SELECT * FROM socan order by Enabled, StationID, end, start";
                                   //echo $QUERY;
                                   if($res = mysqli_query($con,$QUERY)){
                                       while($obj = $res->fetch_object()){
                                           echo "<tr><td><input type='checkbox' onchange='EditOnly()' name='delete[]' value='".$obj->AuditId."'/>";
                                           echo "<input type='hidden' name='AuditID[]' value='".$obj->AuditId."'/></td>";
                                           echo "<td><input onchange='EditOnly()' type='text' name='Description[]' placeholder='Unique Name' title='Origional:".addslashes($obj->Description)."' value='".addslashes($obj->Description)."'/></td>";
                                           echo "<td><input onchange='EditOnly()' type='number' required min='0' max='1' name='Enabled[]' placeholder='CC/Hr' value='".$obj->Enabled."'/></td>";
                                           echo "<td><input onchange='EditOnly()' type='number' required min='0' max='1' name='RQArtist[]' placeholder='PL/Hr' value='".$obj->RQArtist."'/></td>";
                                           echo "<td><input onchange='EditOnly()' type='number' required min='0' max='1' name='RQAlbum[]' placeholder='CC %' value='";
                                           echo $obj->RQAlbum;
                                           echo "'/></td>";
                                           echo "<td><input onchange='EditOnly()' type='number' min='0' max='1' name='RQComposer[]' placeholder='PL %' value='";
                                           echo $obj->RQComposer;
                                           echo "'/></td>";
                                           echo "<td><input onchange='EditOnly()' type='date' required name='Start[]' placeholder='PL/Hr' value='".$obj->start."'/></td>";
                                           echo "<td><input onchange='EditOnly()' type='date' required name='End[]' placeholder='PL/Hr' value='".$obj->end."'/></td>";
                                           echo "<td><input onchange='EditOnly()' type='number' required min='0' max='1' name='ShowPrompt[]' placeholder='PL/Hr' value='".$obj->ShowPrompt."'/></td>";
                                           echo "<td>".$obj->StationID."</td>";
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
