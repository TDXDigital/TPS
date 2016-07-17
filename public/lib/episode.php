<?php

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

namespace TPS;
/**
 * @abstract contains all functions and methods related to episodes
 * @version 1.0
 * @author James Oliver <support@ckxu.com>
 * @license https://raw.githubusercontent.com/TDXDigital/TPS/master/LICENSE MIT
 */

require_once 'program.php';
class episode extends program{
    private $needsUpdate = True;
    protected $program = null;
    protected $EpisodeID = null;
    protected $time = null;
    protected $date = null;
    protected $originIP = null;
    protected $type = 0;
    protected $recordDate = null;
    protected $description = null;
    protected $endTime = null;
    protected $locked = false;
    protected $totalSpokenTime = null;
    protected $finalizedTimestamp = null;
    protected $lastAccessTimestamp = null;
    protected $station;
    /**
     * Either ID or date, time must be provided
     * @global type $mysqli
     * @version 1.0
     */
    public function __construct(program &$program,
            $ID = NULL,
            $date = NULL,
            $time = NULL,
            $description = NULL,
            $type = NULL,
            $recordDate = NULL) {
        $this->EpisodeID = $ID;
        $this->time = $time;
        $this->date = $date;
        $this->description = $description;
        $this->type = $type;
        $this->recordDate = $recordDate;
        $this->program = $program;
        if($this->program) {
            $this->station = new \TPS\station($program->callsign);
        }
        else{
            $this->station = new \TPS\station($_SESSION['CALLSIGN']);
        }
        /**
         * this does duplicate the parent but It is likely more desirable
         * than a detached child object
         */
        parent::__construct($this->station, $program->programID);
    }
    private function dynamicUpdate($col, $val, $type='s'){
        if(!$this->exists()){
            throw new \Exception("Episode does not yet exist");
        }
        $stmt = $this->mysqli->prepare("Update episode set `$col`=? WHERE `episode`.`EpNum`=?");
        if($stmt === false){
            $this->log->error($this->mysqli->error,"getEpisode");
        }
        $stmt->bind_param("s$type", $val, $this->EpisodeID);
        if($stmt->execute()){
            return true;
        }
        else{
            throw new \Exception($this->mysqli->error, $this->mysqli->errno);
        }
    }

    public function setDescription($description){
        $this->dynamicUpdate('description', $description, "s");
        return $this->getEpisode();
    }

    protected function setEndTime($timeString="now"){
        if($timeString=="now"){
            $timeString = ("H:m");
        }
        $this->dynamicUpdate('endTime', $timeString, "s");
        return $this->getEpisode();
    }

    public function setPreRecordDate($dateString="now"){
        if($dateString=="now"){
            $dateString = ("Y-m-d");
        }
        $this->dynamicUpdate('prerecorddate', $dateString, "s");
        return $this->getEpisode();
    }

    public function setReviewedDate($dateString="now"){
        if($dateString=="now"){
            $dateString = ("Y-m-d");
        }
        $this->dynamicUpdate('Reviewed_Date', $dateString, "s");
        return $this->getEpisode();
    }

    public function setEndStamp($dateTime="now"){
        if($dateTime=="now"){
            $dateTime = gmdate("Y-m-d\TH:i:s\Z");;
        }
        $this->dynamicUpdate('EndStamp', $dateTime, "s");
        return $this->getEpisode();
    }

    public function setTotalSpokenTime($duration=0){
        if(is_double($duration) || is_integer($duration)){
            throw new \Exception("Invalid value $duration provided for double type storage");
        }
        $this->dynamicUpdate('totalSpokenTime', $duration, "d");
        return $this->getEpisode();
    }

    public function setScore($score=1){
        if(is_double($score) || is_integer($score)){
            throw new \Exception("Invalid value $score provided for double type storage");
        }
        $this->dynamicUpdate('score', $score, "d");
        return $this->getEpisode();
    }

    public function setGuests($guests=""){
        $this->dynamicUpdate('Guests', $guests, "s");
        return $this->getEpisode();
    }

    protected function setFinalizedIp($ip="none"){
        if($ip=="none") {
            $ip = filter_input(INPUT_SERVER, "REMOTE_ADDR");
            $HXFR = filter_input(INPUT_SERVER, "HTTP_X_FORWARDED_FOR");
            if (isset($HXFR) && $HXFR != $ip && $HXFR != '') {
                $this->log->debug("Episode " . $this->EpisodeID . " finalized via proxy address, reported "
                    . $HXFR);
            }
        }
        $this->dynamicUpdate('IP_Finalized', $ip, "s");
        // still return getEpisode as it will set the updated values in the object
        return $this->getEpisode();
    }

    public function finalizeEpisode(){
        // Get count of spoken minutes
        // set spoken time
        $this->setEndStamp();
        return $this->setFinalizedIp();
    }

    public function exists(){
        $count = 0;
        $stmt = $this->mysqli->prepare("SELECT count(*) as c from `episode` WHERE `EpNum`=?");
        if($stmt === false){
            $this->log->error($this->mysqli->error,"getEpisode");
        }
        $stmt->bind_param("i", $this->EpisodeID);
        $stmt->bind_result($count);
        if($stmt->execute()){
            $stmt->fetch();
            if ($count > 0){
                return true;
            }
        }
        return false;
    }

    public function date($date=Null){
        $this->date = $date;
        return $this->$date;
    }

    public function getEpisode(){
        if($this->needsUpdate && !is_null($this->callsign)){
            $stmt = $this->mysqli->prepare("SELECT "
                . "callsign, programname, date, starttime, type, "
                . "prerecorddate, description, IP_Created, totalspokentime, "
                . "`Lock`, EndStamp, LastAccess, endtime, EpNum FROM episode "
                . "WHERE `EpNum`=? or (`programname`=? and `date`=? and "
                . "`starttime`=? and `callsign`=?)");
            if($stmt === false){
                $this->log->error($this->mysqli->error,"getEpisode");
            }
            $stmt->bind_param("issss",$this->EpisodeID, $this->program->name,
                $this->date, $this->time, $this->callsign);
            $stmt->bind_result($this->callsign,$this->name,$this->date,
                    $this->time, $this->type, $this->recordDate,
                    $this->description, $this->originIP, $this->totalSpokenTime,
                    $this->locked, $this->finalizedTimestamp,
                    $this->lastAccessTimestamp, $this->endTime,
                    $this->EpisodeID);
            if($stmt->execute()){
                //$stmt->store_result();
                if($stmt->num_rows > 1){
                    throw new \Exception(
                            "Too episode many results, non unique results");
                }
                $stmt->fetch();
                // @todo return result in array
                // @todo implement updateEpisode public function
            }
            else{
                $this->log->error($this->mysqli->errno);
            }
            $stmt->close();
        }
        return array(
            "id" => $this->EpisodeID,
            "name" => $this->name,
            "date" => $this->date,
            "time" => $this->time,
            "type" => $this->type,
            "endTime" => $this->endTime,
            "callsign" => $this->callsign,
            "recordDate" => $this->recordDate,
            "description" => $this->description,
            "totalSpokenTime" => $this->totalSpokenTime,
        );
    }

    public function createEpisode(){
        if(!($this->EpisodeID || ($this->date && $this->time))){
            $this->log->error("Could not create episode, values missing");
            throw new \Exception(sprintf("Values are missing that are required, "
                    . "cannot create Episode (ID:%d, %s, %s) ",
                    (integer)$this->EpisodeID,(string)$this->date,
                    (string)$this->time));
        }
        elseif($this->EpisodeID){
            return $this->getEpisode($this->EpisodeID);
        }
        if($stmt = $this->mysqli->prepare("insert into episode ("
                . "callsign, programname, `date`, starttime, type,"
                . "prerecorddate, description, IP_Created) values "
                . "( ?, ?, ?, ?, ?, ?, ?, ?)")){
            $stmt->bind_param("ssssssss",$this->callsign,$this->program->name,
                    $this->date,$this->time,$this->type,  $this->recordDate,
                    $this->description, $_SERVER['REMOTE_ADDR']);
            if($stmt->execute()){
                $id = $this->mysqli->insert_id;
                $this->EpisodeID = $id;
                $this->log->info(sprintf("New Episode created %d", $id ));
            }
            else{
                $this->log->error($this->mysqli->errno);
            }
            $stmt->close();
            return $this->getEpisode();
        }
        else{
            $this->log->error("Failed to create new episode"
                    . $this->mysqli->error);
            return false;
        }
    }
}

class episodeType{
    public $live = 0;
    public $timeless = 1;
    public $preRecord = 2;
}

class episodeLock{
    public $unlocked = 0;
    public $finalized = 1;
    public $adminLock = 2;
    public $auditLock = 3;
}

class episodes extends station{
    protected $startDate = "1000-01-01";
    protected $endDate = "9999-12-31";
    protected $startTime = "00:00:00";
    protected $endTime = "24:00:00";
    protected $types = NULL;
    protected $type = NULL;

    public function __construct($callsign, $type=NULL, $startDate=NULL, $endDate=NULL, $startTime=NULL,
                                $endTime=NULL)
    {
        $this->types = new \TPS\episodeType();
        $this->type = $type?:[$this->types->live, $this->types->timeless, $this->types->preRecord];
        $this->startDate = $startDate?:$this->startDate;
        $this->endDate = $endDate?:$this->endDate;
        $this->startTime = $startTime?:$this->startTime;
        $this->endTime = $endTime?:$this->endTime;

        if(!(\TPS\TPS::validateIsoDate($this->startDate) && \TPS\TPS::validateIsoDate($this->endDate))){
            throw new \Exception("Invalid ISO8601 date provided [".$this->startDate.", ".$this->endDate." ]");
        }
        if(date($this->startDate)>date($this->endDate)){
            throw new \Exception($this->startDate." start date before end date ".$this->endDate);
        }

        parent::__construct($callsign);
    }

    public function getAllEpisodes(){
        $results = array();
        $stn = new \TPS\station($this->callsign);
        $stmt = $this->mysqli->prepare("SELECT EpNum, programname FROM episode WHERE callsign=? and ".
            "Type in (?) and `date` between ? and ? and starttime between ? and ? and ".
            "(endtime between ? and ? or endtime is NULL) order by `date` desc");
        $inList = implode(', ', $this->type);
        $stmt->bind_param("ssssssss",
            $this->callsign,
            $inList,
            $this->startDate,
            $this->endDate,
            $this->startTime,
            $this->endTime,
            $this->startTime,
            $this->endTime
        );
        $ident = 0;
        $programName = "";
        $stmt->bind_result($ident, $programName);
        if($stmt->execute()){
            while($stmt->fetch()){
                $results[$ident] = $programName;
            }
        }
        foreach ($results as $key=>&$val){
            $pgm = new \TPS\program($stn, \TPS\program::getId($this->callsign, $val));
            $val = new \TPS\episode($pgm, $key);
        }
        return $results;
    }
}
