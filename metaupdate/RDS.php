<?php
    
// TO get around serial issue: have com listener open as well as TCP port
// on change touch php file that will update db

$STORAGE = "";
// Implement Timestamp check against DB for switch changes

function SaveStorage(){
    echo "<h3>Saving Storage...</h3>";
    if (function_exists('file_put_contents')) {
        echo "prefered function exists, proceeding<br/>";
        file_put_contents("save.txt", $GLOBALS['STORAGE']);
        //file_put_contents("save.txt",$STORAGE['TPS']);
        echo "Storage saved to save.txt";
    }
    else{
        echo "<span style='color:red'>prefered function not supported, proceeding with alternative</span><br/>";
        fwrite("save.txt",$GLOBALS['STORAGE']);
        //fwrite("save.txt",$STORAGE['TPS']);
        echo "Storage saved to save.txt";
    }
}

function LoadStorage(){
    $GLOBALS['STORAGE'] = file_get_contents("save.txt");
}

function UpdateMeta(){

    $RDS = array(
        0 => array(
            'host'=>'hyperstream.local.ckxu.com',
            'port'=>8000,
            'password'=>'88.3ForLife',
            'sid'=>2
        ),
        1 => array(
            'host'=>'hyperstream.local.ckxu.com',
            'port'=>8000,
            'password'=>'88.3ForLife',
            'sid'=>3
        ),
        2 => array(
            'host'=>'cent4.serverhostingcenter.com',
            'port'=>8715,
            'password'=>'88.3ForLife',
            'sid'=>0
        )
    );

    // Source does not need as simple of iteration so being more descriptive
    $Source = array(
        'Logs'=>array(
            'address'=>'172.22.10.10',
            'user'=>'root',
            'password'=>'88.3ForLife',
            'database'=>'CKXU'
            ),
        'RadioDJ'=>array(
            'address'=>'172.22.10.50',
            'user'=>'root',
            'password'=>'88.3ForLife',
            'database'=>'radiodj164_2'
        )
    );

    // Connect to DBs
    echo "Establising Database Connections<br/>";
    $dbl = new mysqli($Source['Logs']['address'],$Source['Logs']['user'],$Source['Logs']['password'],$Source['Logs']['database']);
    $dba = new mysqli($Source['RadioDJ']['address'],$Source['RadioDJ']['user'],$Source['RadioDJ']['password'],$Source['RadioDJ']['database']);

    if ($dbl->connect_errno || $dba->connect_errno) {
        printf("Connect a error: %s\n", $dbl->connect_error);
        printf("Connect b error: %s\n", $dba->connect_error);
        return 0;
    }
    else{
        echo "Databases connected!<br/>";
    }

    // Get currently playing on RadioDJ
    $a_result = $dba->query("SELECT `date_played`, `artist`, `title`, `duration` FROM `history` WHERE `song_type` = 0 ORDER BY `date_played` DESC LIMIT 1");
    $rdj = $a_result->fetch_array();
    $data = $rdj['title'] . ' - ' . $rdj['artist'];


    // Get Current Program on air and song
    $l_result = $dbl->query("
SELECT episode.programname, song.title, song.artist, song.time, episode.starttime, ADDDATE(episode.starttime,INTERVAL (SELECT length FROM program where program.programname=episode.programname) minute) AS endtime
FROM episode
LEFT JOIN song on
song.programname = episode.programname
and song.date = episode.date
and song.starttime = episode.starttime
and (song.category like \"2%\" or song.category like \"3%\")
and song.time > ADDDATE(current_time(), INTERVAL -10 minute)
where date_format(episode.date, \"%Y-%m-%d\") = current_date()
and ADDDATE(episode.starttime,INTERVAL (SELECT length FROM program where program.programname=episode.programname)+10 minute) > current_time()
and episode.starttime < current_time()
order by song.time desc, episode.starttime desc limit 1;");
    $tps = $l_result->fetch_array();
    if(isset($tps['artist'])&&isset($tps['title'])){
        $data = $tps['title'] . " - " . $tps['artist'];
    }
    else if(isset($tps['programname'])){
        $data = "The ". $tps['programname'] . " Show";
    }

    // Update Medtadata servers if global is different

    if($GLOBALS['STORAGE'] != $data){
        echo $data."<br/><br/><h2>Updating Servers</h2>";
        echo "Metadata was: <span style='color:#005533'>".$GLOBALS['STORAGE']."</span><br/>";
        echo "Updating to:<span style='color:#000099'>".$data."</span><br/>";
        $GLOBALS['STORAGE'] = $data;
        foreach($RDS AS $Server){
            //for($count=0; $count < count($serv["host"]); $count++)
            //{
                $mysession = curl_init();
                if($Server['sid']!=0){
                    curl_setopt($mysession, CURLOPT_URL, "http://".$Server["host"].":".$Server["port"]."/admin.cgi?sid=".urlencode($Server['sid'])."&mode=updinfo&song=".rawurlencode(trim($data)));
                }
                else{
                    curl_setopt($mysession, CURLOPT_URL, "http://".$Server["host"].":".$Server["port"]."/admin.cgi?mode=updinfo&song=".rawurlencode(trim($data)));
                }
                curl_setopt($mysession, CURLOPT_HEADER, false);
                curl_setopt($mysession, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($mysession, CURLOPT_POST, false);
                curl_setopt($mysession, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($mysession, CURLOPT_USERPWD, "admin:".$Server["password"]);
                curl_setopt($mysession, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($mysession, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
                curl_setopt($mysession, CURLOPT_CONNECTTIMEOUT, 2);
                echo "Connecting to ".$Server['host']."<br/>";
                curl_exec($mysession);
                $http_status = curl_getinfo($mysession, CURLINFO_HTTP_CODE);
                curl_close($mysession);
                echo "Completed with code $http_status<br/>";
                $dbl->query("INSERT INTO `rds` (`rds_status`,`value`,`server`) values ($http_status,'".addslashes($data),"','".addslashes($Server['host'].':'.$Server['port'])."')");
                //echo "<p>INSERT INTO `rds` (`rds_status`,`value`,`server`) values ($http_status,'".addslashes($data),"','".addslashes($Server['host'].':'.$Server['port'])."')</p>";
        }
        echo "<strong>Updates Finished Disconnecting...</strong></br>";
    }
    else{
        echo "<span>No Updates Yet Disconnecting...</span></br>";
    }

    $dbl->close();
    $dba->close();

}

LoadStorage();
for($i=0;$i<5;$i++){
    UpdateMeta();
    sleep(10);
}
SaveStorage();
?>