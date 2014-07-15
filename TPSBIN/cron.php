<?php
    
    class TPS_Cron{
        
        // Set to overridde grading 
        // Null = DB Value for Genre or Program
        private grade_force_perc=NULL;
        private grade_force_countryreq=NULL // countryreg=CanCon or FCC Country Requirement
        private grade_force_playlist=NULL;
        private grade_force_spoken=NULL;

        function __contruct($username=NULL,$password=NULL,$database=NULL,$host=NULL,$port=3306){
            $mysqli = mysqli_connect()
            try
            {
                if (is_null($host) || is_null($database) || is_null($username) || is_null($password)) throw new Exception("Please specify the host, database, username and password!");

            }
            catch (Exception $e)
            {
                $this->error_message($e->getMessage());
            }
            return TRUE;
        }
        public function run_cron(){
            
        }
        public function install_cron(){
            
        }
        public function remove_cron(){
            
        }
        public function update_cron(){
            
        }
        private function mail_admin(){
            
        }
        private function mail_user(){
            
        }
        private function grade_episode($episode=NULL){
            $mysqli=
        }
    }

?>