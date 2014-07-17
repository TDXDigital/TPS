<?php
    
    class TPS_Cron{
        
        // Set to overridde grading 
        // Null = DB Value for Genre or Program
        private $grade_force_perc=NULL;
        private $grade_force_countryreq=NULL // countryreg=CanCon or FCC Country Requirement
        private $grade_force_playlist=NULL;
        private $grade_force_spoken=NULL;
        private $connected=FALSE;

        function __contruct($username=NULL,$password=NULL,$database=NULL,$host=NULL,$port=3306){
            try
            {
                // check if values given
                if (is_null($host) || is_null($database) || is_null($username) || is_null($password)) throw new Exception("Please specify the host, database, username and password!");

                // perform connection if given
                $mysqli = mysqli_connect($host,$username,$password,$database,$port);

                // if connection fails throw error, can not continue
                if($mysqli->connect_error){
                    throw new Exception("Please verify connection paramaters, ".$mysqli->connect_error);
                }
                else{
                    $this->connected=TRUE;
                }
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
        static public function grade_episode($episode=NULL,$new_only=TRUE){
            if(!$this->connected){
                throw new Exception ("Connection is closed, please initialize the connection (__contruct)");
            }
            if(is_null($episode)){
                // when a Null param is given, assume all are wanted
                if($new_only===TRUE){
                    
                }
                else{
                    // NOTE: Used Arbitrary date of jan 1 2014
                    $query_all_force = "SELECT episode.*,genre.* FROM genre,episode left join program on episode.programname=program.programname where genre.genreid=program.genre and episode.date>\"2014-0-01\" order by EpNum ASC;"
                    $result = $mysqli->query($query_all_force);

                    // get song and traffic information for the episode
                    $song_stmt = $mysqli->prepare("SELECT * FROM song left join trafficaudit on song.songid=trafficaudit.songid WHERE `song`.`callsign`=? and `song`.`programname`=? and `song`.`date`=? and `song`.`starttime`=? ");
                    foreach ( $result as $episode ){
                        // get songs for episode
                        $song_stmt->bind_param('ssss',$episode['callsign'],$episode['programname'],$episode['date'],$episode['starttime']);
                        /* grading weights
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
                                --> checks against Promptlog, timestamp and "time" of play.
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
                        $songs = $song_stmt->query();
                    }
                }
            }
            else if (is_numeric($episode)){
                // otherwise grade only given

            }
            else{
                throw new Exception ("Non Numeric param given for episode number, give numeric or NULL");
            }
        }
    }

?>