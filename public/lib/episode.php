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
        $this->program = $program;
        /**
         * this does duplicate the parent but It is likely more desirable 
         * than a detached child object
         */
        parent::__construct($this->program);
        $this->EpisodeID = $ID;
        $this->time = $time;
        $this->date = $date;
        $this->description = $description;
        $this->type = $type;
        $this->recordDate = $recordDate;
    }
    public function setDescription(){
        
    }
    
    public function date($date=Null){
        $this->date = $date;
        return $this->$date;
    }
    
    public function getEpisode(){
        if($this->needsUpdate){
            $stmt = $this->mysqli->prepare("SELECT "
                . "callsign, programname, date, starttime, type, "
                . "prerecorddate, description, IP_Created, totalspokentime, "
                . "`Lock`, EndStamp, LastAccess, endtime FROM episode "
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
                    $this->lastAccessTimestamp, $this->endTime);
            if($stmt->execute()){
                //$stmt->store_result();
                if($stmt->num_rows > 1){
                    throw new Exception(
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
        if(!($this->EpisodeID || $this->date || $this->time)){
            $this->log->error("Could not create episode, values missing");
            return False;
        }
        elseif($this->EpisodeID){
            return $this->EpisodeID;
        }
        if($stmt = $this->mysqli->prepare("insert into episode ("
                . "callsign, programname, date, starttime, type,"
                . "prerecorddate, description, IP_Created) values "
                . "( ?, ?, ?, ?, ?, ?, ? )")){
            $stmt->bind_param("sssssss",$this->callsign,$this->program->name,
                    $this->date,$this->time,$this->type,  $this->recordDate,
                    $this->description, $_SERVER['REMOTE_ADDR']);
            if($stmt->execute()){
                $this->EpisodeID=$this->mysqli->insert_id;
            }
            else{
                $this->log->error($this->mysqli->errno);
            }
            $stmt->close();
        }
        else{
            $this->log->error("Failed to create new episode"
                    . $this->mysqli->error);
            return false;
        }
    }
}
