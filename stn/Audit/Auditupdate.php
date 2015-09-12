<?php
session_start();
$link = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if(!$link){
    header('location: '.$_SERVER["HTTP_REFERER"].'?e=Database%20connection%20error');
}
else{

    // FETCH DATA
    $UID   = $_POST['AuditID'];
    $DEL   = $_POST['delete'];
    $Description  = $_POST['Description'];
    //$OLDNAME = $_POST['C_OLD_NAME'];
    $RQArtist = $_POST['RQArtist'];
    $RQAlbum = $_POST['RQAlbum'];
    $RQComposer = $_POST['RQComposer'];//ENDED CODING HERE
    $PLA_N = $_POST['C_Playlist'];
    $PLA_P = $_POST['C_PlPerc'];
    $PLA_T = $_POST['C_PlType'];

    // CHECK FOR DATA VALIDITY
    if((count($UID)!=count($NAME))||(count($NAME)!=count($CAN_N))||(count($CAN_N)!=count($CAN_P))||(count($CAN_P)!=count($CAN_T))||(count($CAN_T)!=count($PLA_N))||(count($PLA_N)!=count($PLA_P))||(count($PLA_P)!=count($PLA_T))||(count($NAME)!=count($OLDNAME))){
        echo "UID:".count($UID)."</br>".var_dump(count($UID)!=count($NAME));
        echo "NAME:".count($NAME)."</br>".var_dump(count($NAME)!=count($CAN_N));
        echo "CANN:".count($CAN_N)."</br>".var_dump(count($CAN_N)!=count($CAN_P));;
        echo "CANP:".count($CAN_P)."</br>".var_dump(count($CAN_P)!=count($CAN_T));
        echo "CANT:".count($CAN_T)."</br>".var_dump(count($CAN_T)!=count($PLA_N));
        echo "PLAN:".count($PLA_N)."</br>".var_dump(count($PLA_N)!=count($PLA_P));
        echo "PLAP:".count($PLA_P)."</br>".var_dump(count($PLA_P)!=count($PLA_T));
        echo "PLAT:".count($PLA_T)."</br>";
        echo "DEL:".count($DEL)."</br>";
        //die("Data Integrity Error...");
        header('location:'.$_SERVER['HTTP_REFERER']."?e=Data%20Integrity%20Compromised");
    }
    else{
        echo "Data is Fine! POST Size:".count($UID);
        echo "</br><h2>START TRANSACTION</h2>";
        
        for($i=0;$i<count($UID);$i++){
            try{
                $link->autocommit(FALSE);
                echo "autocommit OFF;";
                // Start Transaction
                
                $link->query("START TRANSACTION");
                echo "Started Transaction";
                
                // Queries for transaction
                $link->query("UPDATE `program` SET genre='".addslashes($NAME[$i])."' WHERE genre='".addslashes($OLDNAME[$i])."'");
                $link->query("UPDATE `genre` SET genreid='".addslashes($NAME[$i])."', cancon='".addslashes($CAN_N[$i])."',canconperc='".addslashes($CAN_P[$i]/100)."',CCType='".addslashes($CAN_T[$i])."' 
                , playlist='".addslashes($PLA_N[$i])."', playlistperc='".addslashes($PLA_P[$i]/100)."', PlType='".addslashes($PLA_T[$i])."' WHERE genreid='".addslashes($OLDNAME[$i])."'");

                // Commit Transaction
                if(!$link->commit()){
                    $link->rollback();
                }
                $link->autocommit(TRUE);
                //echo "Transaction Complete<br/>";
            }
            catch (Exception $e){
                $link->rollback();
                //echo "Transaction Failed</br>";
            }
        }
        
    }
    for($x=0;$x<count($DEL);$x++){
        try{
            $link->autocommit(FALSE);
            echo "autocommit OFF;";
            // Start Transaction
                
            $link->query("START TRANSACTION");
            echo "Started Transaction";
                
            // Queries for transaction
            // if any programs have this genreid set to them set to the first available ID
            $link->query("UPDATE `program` SET genre=(SELECT genreid FROM genre where UID!='".addslashes($DEL[$x])."'limit 1) WHERE genre=(SELECT genreid FROM genre WHERE UID='".addslashes($DEL[$x])."')");
            $link->query("DELETE FROM genre WHERE UID='".$DEL[$x]."'");

            // Commit Transaction
            if(!$link->commit()){
                $link->rollback();
            }
            $link->autocommit(TRUE);
            //echo "Transaction Complete<br/>";
        }
        catch (Exception $e){
            $link->rollback();
            //echo "Transaction Failed</br>";
        }
    }
    // UPDATE ROWS VIA TRANSACTION
    /*
    while()
    $existing = $mysqli->query("SELECT genreid FROM genre where UID='")
    if()
    // Start Transaction
    $mysqli->autocommit(FALSE);

    //QUERIES FOR TRANSACTION
    $mysqli->query("UPDATE program ");*/

    // CLOSE DB CONNECTION
    $link->close();
    //header("location:".$_SERVER['HTTP_REFERER']."?R=Succesfully%20Updated");
    header("location:genre.php?r=Updated%20Succesfully");
}
?>