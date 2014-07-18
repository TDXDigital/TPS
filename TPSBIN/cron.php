<?php
    
    include_once "functions.php";
    if(!isset($_SESSION)){
        sec_session_start();
    }

    class TPS_Cron{
        
        // Set to overridde grading 
        // Null = DB Value for Genre or Program
        private $grade_force_perc=NULL;
        private $grade_force_countryreq=NULL; // countryreg=CanCon or FCC Country Requirement
        private $grade_force_playlist=NULL;
        private $grade_force_spoken=NULL;
        private $connected=FALSE;
        //public $mysqli=NULL;

        function __construct($username=NULL,$password=NULL,$database=NULL,$host=NULL,$port=3306){
            try
            {
                // check if values given
                //if (is_null($host) || is_null($database) || is_null($username) || is_null($password)) throw new Exception("Please specify the host, database, username and password!");
                //echo "initialized";
                /*include_once "db_connect.php";
                if($mysqli->connect_error){
                    echo $mysqli->connect_error;
                }
                else{
                    echo "connection established";
                }*/
                // perform connection if given
                /*$mysqli = mysqli_connect($host,$username,$password,$database,$port);

                // if connection fails throw error, can not continue
                if($mysqli->connect_error){
                    throw new Exception("Please verify connection paramaters, ".$mysqli->connect_error);
                }
                else{
                    $this->connected=TRUE;
                }*/
                // connection created and params set (so far)...

            }
            catch (Exception $e)
            {
                $this->error_message($e->getMessage());
            }
            return TRUE;
        }
        static public function run_cron(){
            
        }
        static public function install_cron(){
            
        }
        static public function remove_cron(){
            
        }
        static public function update_cron(){
            
        }
        static public function close_cron(){
            $mysqli->close();
            $this->close();
        }
        static private function mail_admin(){
            
        }
        static private function mail_user(){
            
        }
        static public function update_switch(){
            
        }
        static public function grade_episode($episode_num=NULL,$new_only=TRUE){
            include_once "db_connect.php";
            /*if(!$mysqli->connected){
                throw new Exception ("Connection is closed, please initialize the connection (__contruct)");
            }*/
            //if(is_null($episode)){
                // when a Null param is given, assume all are wanted
                if($new_only===TRUE){
                    
                }
                else{
                    echo "<style>
                    table{
                        width: 100%;
                    }
                    table, th, td {
                       border: 1px solid black;
                       
                    }</style>
                    <br>performing grade, connection established";
                    // NOTE: Used Arbitrary date of jan 1 2014
                    $query_all_force = "SELECT episode.*,genre.* FROM genre,episode left join program on episode.programname=program.programname where genre.genreid=program.genre and episode.date>\"2014-0-01\" and episode.EpNum like '%".$episode_num."%' order by EpNum ASC;";
                    $result = $mysqli->query($query_all_force);

                    // get song and traffic information for the episode
                    $song_stmt = $mysqli->stmt_init();
                    $song_stmt->prepare("SELECT * FROM song left join trafficaudit on song.songid=trafficaudit.songid WHERE `song`.`callsign`=? and `song`.`programname`=? and `song`.`date`=? and `song`.`starttime`=? ");
                    $song_stmt->bind_param('ssss',$episode_call,$episode_name,$episode_date,$episode_start);
                    foreach ( $result as $episode ){
                        echo "<br><br><span style='width:100%; text-align:center;'>RAW DATA</span><br>";
                        echo "<table><thead>
                        <th>callsign</th><th>pgm</th><th>date</th><th>st_time</th><th>end_time</th><th>prerec</th><th>ttl_spkn</th><th>desc</th><th>Lock</th><th>Type</th><th>EPN</th><th>Guests</th><th>ENDStamp</th><th>LastAccess</th><th>score</th><th>Rvd_Date</th><th>genre</th><th>CC_R</th><th>PL_R</th><th>CCP_R</th><th>PLP_R</th><th>G-UID</th><th>PlType</th><th>CcType</th><th>gcall</th></tr>
                        </thead><tbody>
                        ";
                        foreach ($episode as $p)
                        {
                            print "<td>$p</td>";
                        }
                        echo "</tr></tbody></table><br>";
                        echo "<table><thead>
                        <th>songid</th><th>callsign</th><th>program</th><th>pgm_date</th><th>pgm_time</th><th>ins</th><th>s_time</th><th>album</th><th>title</th><th>artist</th><th>cancon</th><th>pl</th><th>Cat</th><th>hit</th><th>spkn</th><th>comp.</th><th>note</th><th>AdViol</th><th>bcd</th><th>TS</th><th>RCD</th><th>TRAid</th><th>TRAsid</th><th>TRAadv</th><th>TRAsT</th><th>TRAeT</th><th>TRAc</th>
                        </thead><tbody>";
                        // get songs for episode
                        $episode_call = $episode['callsign'];
                        $episode_name = $episode['programname'];
                        $episode_date = $episode['date'];
                        $episode_start = $episode['starttime'];
                        /*grading weights
                        Logging Requirements (100%)
                            ->required Ads (40%)
                                --> each ad missing will result in proportional decrease in score from requirement (3/4 ads = 30%,potential: 40%)
                                --> additional ads (traffic) will result in a proportional score decrease (6/4 ads = 20%,potential: 40%)
                                --> unprompted (violated) traffic will not count toward score and decrement score (3/4 ads with one violation = 20%, potential 40%)
                            -> Promptlog (10%)
                                --> starts at 10% automatically
                                --> ads must be accounted for, each missing ad decreases score by proportional percentage (1/2 prompted ads = 5%, potential 10%)
                                    ---> condition if cannot link promptlog id with adid from song table 
                            -> PSA / Promo (10%)
                                --> Proportional score of requirements set.
                            -> Timestamp Verification (15%)
                                --> checks against Promptlog, timestamp and \"time\" of play.
                                --> must be within 30 minutes for 1/2 score on proportional score, 10 min for full score
                            -> TOH (10%)
                                --> must be synced with top of Hour and logged as 43 or 12
                            -> Finalization/Length (5%)
                                --> must be finalized within allotted time frame, 10 min buffer given, extra time decreases score based on percentage of extra time
                                    ---> 60 min show permitted 70 min, performs 120 minutes = 83.3% decrease in score (0.84% given as score of potential 5%)
                                --> 5 min underrun and latestart buffer given, Time under target results in score decrease proportional to program length offset by buffers.
                                --> no finalization = -5% overall score decrease (0.0%/5%)
                                    --> can combine with overrun and result in negative score to minimum of 0% overall score total
                            -> song requirements (10%)
                                --> 5% associated with Country Content requirements (proportional grading on genre requirements
                                --> 2.5% associated with playlist requirements (set in genre or program)
                                --> 2.5% associated with Spoken Time (requires percentages set in genre or 5% of program spoken)
                                    --> time calculation uses program length or episode length, whichever is higher
                        Hits (-10%)
                            -> violating Hitlimit generates -10% score on program to minimum of zero overall score (0%)
                            -> no positive score givcen for being at or under limit.
                        */
//                        $song_stmt->bind_result()
                        $song_stmt->execute();
                        $data = $song_stmt->get_result();
                        while($row = $data->fetch_array(MYSQLI_NUM))
                        {
                            echo"<tr>";
                            foreach ($row as $r)
                            {
                                print "<td>$r</td>";
                            }
                            print "</tr>";
                        }
                        echo "</tbody><tfoot></tfoot></table>";
                        
                    }
                }
            /*}
            else if (is_numeric($episode)){
                // otherwise grade only given

            }
            else{
                throw new Exception ("Non Numeric param given for episode number, give numeric or NULL");
            }*/
        }
    }

?>