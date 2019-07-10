<?php

namespace TPS;
/*
 * The MIT License
 *
 * Copyright 2015 James Oliver <support@ckxu.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.
        '../../TPSBIN'.DIRECTORY_SEPARATOR."functions.php";

class TPS{
    protected $mysqli;
    protected $db; //USE PDO database
    protected $mysqliDriver;
    protected $username;
    private $requirePDO;
    private $settingsPath;
    private $settingsTarget;
    private $databaseName;
    private $databaseHost;

    /**
     * Return an array with connection information for a specified server
     * @access private
     * @param string $target target serverId
     * @param string $xmlpath path to DBSETTINGS.XML
     * @return boolean
     */
    private function getDatabaseConfig($target=NULL,$xmlpath=NULL){
        if($xmlpath === NULL){
            $xmlpath = dirname(__FILE__).DIRECTORY_SEPARATOR."../../TPSBIN"
                    .DIRECTORY_SEPARATOR."XML"
                    .DIRECTORY_SEPARATOR."DBSETTINGS.xml";
        }
        if(!file_exists($xmlpath)){
            $xmlpath = get_include_path() . $xmlpath;
        }
        try{
            if(file_exists($xmlpath))
            {
                $dbxml = simplexml_load_file($xmlpath);
            }
            else{
                return FALSE;
            }
        } catch (Exception $ex) {
            error_log($ex);
            return FALSE;
        }
        if(is_null($target)){
            // Get the default (first) server
            foreach ($dbxml->SERVER as $server){
                if(strtolower($server->ACTIVE) == "true"
                        || $server->ACTIVE == '1'){
                    $target = (string)$server->ID;
                    break;
                }
            }
        }
        $database=False;
        foreach ($dbxml->SERVER as $server){
            if((string)$server->ID === $target){
                if($server->RESOLVE === "URL")
                {
                  $database["DBHOST"] = $server->URL;
                }
                elseif($server->RESOLVE === "IPV4")
                {
                  $database["DBHOST"] = $server->IPV4;
                }
                else
                {
                    if($server->URL!=""){
                        $database["DBHOST"] = $server->URL;
                    }
                    else{
                        $database["DBHOST"] = $server->IPV4;
                    }
                }
                $database["USER"] = easy_decrypt(
                        ENCRYPTION_KEY,(string)$server->USER);
                $database["PASSWORD"] = easy_decrypt(
                        ENCRYPTION_KEY,(string)$server->PASSWORD);
                $database["DATABASE"] = (string)$server->DATABASE;
                $database["TYPE"] = $server->DBTYPE;
            }
        }
        return $database;
    }

    /**
     * used to determine database query limits based on provided pageination
     * page number and maxResult.
     * Note: functon will modify input values
     * @access public
     * @param int $pagination current page index
     * @param int $maxResult number of items to in response
     */
    public static function sanitizePagination(&$pagination,&$maxResult){
        if( !is_int($maxResult) || $maxResult > 1000 || $maxResult<1):
            $maxResult = 1000;
        endif;
        if( !is_int($pagination) || $pagination<1):
            $pagination = 1;
        endif;
        $floor = abs(($pagination*$maxResult))-($maxResult+1);
        $ceil = abs($maxResult);#abs(($pagination*$maxResult));
        // Simply for security. should never happen
        if ($floor < 0):
            $floor=0;
        endif;
        if($ceil < 0):
            $ceil = abs($ceil);
        endif;
        $pagination = $floor;
        $maxResult = $ceil;
    }

    /**
     * Check that param $date is formatted correctly to conform with ISO8601
     * date should not be datetime, just date.
     * @param string $date
     * @return bool
     */
    public static function validateIsoDate($date)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') == $date;
    }

    /**
     * @param string $tablename the table to return information about
     */
    public function listDatabaseColumns($tablename){
        $result = array();
        if($stmt = $this->mysqli->prepare("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE "
                . "`TABLE_SCHEMA` = ? AND `TABLE_NAME` = ?")){
            $stmt->bind_param('ss', $this->databaseName, $tablename);
            $stmt->execute();
            $colName = "";
            $stmt->bind_result($colName);
            while($stmt->fetch()){
                array_push($result, $colName);
            }
            $stmt->close();
        }
        else{
            //$result=["error"=>$mysqli->error];
            error_log($this->mysqli->error);
            throw new \Exception("failed to retrieve database tables");
        }
        return $result;

    }

    /**
     * Setup database connections if needed
     * @global type $mysqli
     * @global \TPS\type $pdo
     */
    private function setupDatabaseConn(){
        global $mysqli;
        global $pdo;
        $settingsPath = $this->settingsPath;
        $settingsTarget = $this->settingsTarget;
        $mysqli=$mysqli?:$GLOBALS['mysqli'];
        $pdo=$pdo?:$GLOBALS['pdo'];
        $dev = getenv("PdoInMemory");
        if(!($this->requirePDO && is_object($pdo)) && !($mysqli || $pdo)){
            // Establish DB connection
            $database = NULL;
            if($database = $this->getDatabaseConfig($settingsTarget,
                    $settingsPath)){
                if($database['TYPE']=="PDO"){
                    $this->requirePDO = TRUE;
                }
                $databaseHost = $database['DBHOST'];
                $databaseName = $database['DATABASE'];
                if($this->requirePDO){
                    $this->db = new \PDO(
                            "mysql:host=$databaseHost;dbname=$databaseName",
                        $database['USER'], $database['PASSWORD'], array(
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                        ));
                    $this->mysqli = $this->db;
                }
                else{
                    try{
                        $this->db = new \PDO(
                            "mysql:host=$databaseHost;dbname=$databaseName",
                            $database['USER'], $database['PASSWORD'],array(
                                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                            ));
                    } catch (\PDOException $ex) {
                        error_log($ex->getMessage());
                        $this->db = NULL;
                    }
                    $this->mysqli = new \mysqli(
                        $databaseHost,
                        $database['USER'],
                        $database['PASSWORD'],
                        $databaseName
                        );
                }
                if($this->mysqli instanceof \mysqli &&
                        !$this->mysqli->connect_error){
                    $GLOBALS['mysqli'] = $mysqli?:$this->mysqli;
                }
                if(!$this->db->errorCode()){
                    $GLOBALS['pdo'] = $pdo?:$this->db;
                }
                $this->databaseName = $databaseName;
                $this->databaseHost = $databaseHost;
            }
            else{
                $this->mysqli = $mysqli?:NULL;
                $this->db = $pdo?:NULL;
            }
        }
        else{
            if($this->requirePDO){
                $mysqli = $pdo;
            }

            $database = $this->getDatabaseConfig($settingsTarget, $settingsPath);
            $this->databaseName = $database['DATABASE'];
            $this->databaseHost = $database['DBHOST'];

            $this->mysqli = $mysqli?:$pdo;
            $this->db = $pdo;
        }
        if($dev && !$pdo){
            $pdo = new \PDO(
                'sqlite::memory:',
                null,
                null,
                array(\PDO::ATTR_PERSISTENT => true)
            );
            $this->db = $pdo;
            $this->mysqli = $mysqli?:$pdo;
        }
    }

    /**
     *
     * @param bool $enableDbReporting enables reporting to database, otherwise
     * report to php error_log
     * @param bool $requirePDO Force system to use PDO (Functions witch require
     * MySQLi will fail)
     * @param string $settingsTarget ServerID
     * @param string $settingsPath Path to settings file
     */
    public function __construct($enableDbReporting=FALSE, $requirePDO=FALSE,
            $settingsTarget=NULL, $settingsPath=NULL) {
        if(is_null($settingsTarget) &&
                (isset($_REQUEST["SRVID"]) || isset($_SESSION["SRVPOST"]))){
            try{
                // Try and get the SERVERID if it is not provided
                // INPUT_REQUEST not yet implemented, use superglobal
                //$settingsTarget = filter_input(INPUT_REQUEST, "SRVID");
                if(isset($_REQUEST["SRVID"])){
                    $settingsTarget = $_REQUEST["SRVID"];
                }
                if(isset($_SESSION["SRVPOST"])){
                    $settingsTarget = $_SESSION["SRVPOST"];
                }
            } catch (\Exception $ex) {
                $settingsTarget = NULL;
            }
        }
        $this->settingsPath = $settingsPath;
        $this->settingsTarget = $settingsTarget;
        $this->requirePDO = $requirePDO;
        $this->setupDatabaseConn();
        if(!$this->mysqliDriver){
            $this->mysqliDriver = new \mysqli_driver();
            if($enableDbReporting){
                $this->mysqliDriver->report_mode = MYSQLI_REPORT_ALL;
            }
            else{
                $this->mysqliDriver->report_mode = MYSQLI_REPORT_ERROR;
            }
        }
    }

    /**
     * The database connections cannot be serialized, therefore we need to
     * strip them from the serializers input. removes mysqli and db[PDO]
     *
     * @return null
     */
    public function __sleep() {
        return array_diff(array_keys(get_object_vars($this)),
                array('db', 'mysqli'));
    }

    /**
     * restores database connection aster serialization
     */
    public function __wakeup(){
        $this->setupDatabaseConn();
    }

    /**
     * Update the parent if one exists
     * for the TPS class, there is no parent, instead just update self
     * @return bool
     */
    protected function updateParent(){
        /**
         * Nothing to update, start updating children
         */
        return $this->update();
    }

    /**
     * perform required update to settings
     * @return boolean
     */
    private function update(){
        return TRUE;
    }

    /**
     * Get a list of stations and return each station within an associative
     * array, array key is Callsign of Station
     * @todo convert to PDO function
     * @author James Oliver <j.oliver@ckxu.com>
     * @global type $pdo
     * @return boolean
     */
    public function getStations(){
        global $pdo;
        $callsign = null;
        $name = null;
        if($con = $this->mysqli->prepare(
                "SELECT callsign, stationname FROM station")){
            $stations = array();
            $con->execute();
            $con->bind_result($callsign,$name);
            while($con->fetch())
            {
                $stations[$callsign]=$name;
            }
            return $stations;
        }
        else{
            return false;
        }

    }
}
