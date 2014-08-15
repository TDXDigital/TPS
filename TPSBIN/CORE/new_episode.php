<div class="jumbotron">
    <div class="container">
        <h1>New Episode</h1>
    </div>
    
</div>
<?php
    $base = $_SESSION['BASE_REF'];
    $logo = $_SESSION['m_logo']?: $_SESSION['logo'];
    $dbname= $_SESSION['DBNAME']; // NEEDS COMPANY HEAD TO ALLOW SELECTING MULTIPLE CALLSIGNS (This is not right)
    $access=$_SESSION['access'];
    $opened_db=FALSE;

if(!$mysqli){
    $opened_db=TRUE;
    require_once "TPSBIN/functions.php";
    require_once "TPSBIN/db_connect.php";
}
// CONNECT TO DB

// QUERY "Permissions
if($stmt = $mysqli->prepare("SELECT programname FROM program WHERE callsign=? and active=1")){
    // Bind DBNAME and access
    $stmt->bind_param("s",$dbname);
    //query
    $stmt->execute();
    //

    $program=$stmt->get_result();
    //$program=$perm_arr->fetch_array();
    //$stmt->bind_result($program[]);// not optimal
        
    //$stmt->fetch();
    $stmt->close();
    //error_log($permissions[0]);
}
else{
    die("<h3>error</h3><p>".$mysqli->error."</p>");
    //die('<a href=\'login\'>login</a>');
}

if($opened_db===TRUE){
    $mysqli->close();
}
    
?>
<form method="post" action="Episode/p2insertEP.php">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <h2>Program</h2>
                <select required name="program"><option>Select Your Show [REQUIRED]</option>
                    <?php
                        //while($program->)
                        foreach ($program as $row){
                            print("<option value='".$row['programname']."'>".$row['programname']."</option>");
                        }
                    ?>
                </select>
                <input type="hidden" name="callsign" value="CKXU"><!--should not be defaulted-->
            </div>
            <div class="col-md-3">
                <h2>Broadcast Type</h2>
                <select required name="brType">
                    <option value="0">Live to Air</option>
                    <option value="1">Pre-Record</option>
                    <option value="2">Timeless</option>
                </select>
                <input type="hidden" value="CKXU"><!--should not be defaulted-->
            </div>
            <div class="col-md-2">
                <h2>Record Date</h2>
                <input name="prdate" type="date"><!--should not be defaulted-->
            </div>
            <div class="col-md-2">
                <h2>Air Date</h2>
                <input name="user_date" type="date"><!--should not be defaulted-->
            </div>
            <div class="col-md-2">
                <h2>Air Time</h2>
                <input name="user_time" type="time"><!--should not be defaulted-->
            </div>
            <div class="col-md-6">
                <h2>Comment</h2>
                <input name="Description" type="text" maxlength="90"><!--should not be defaulted-->
            </div>
        </div>
</div>
</form>
<footer>
    <p>&copy; TDX Digital <?php echo date('Y');?></p>
</footer>