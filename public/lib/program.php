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
 * @abstract contains all functions and methods related to reviews
 * @version 1.0
 * @author James Oliver <support@ckxu.com>
 * @license https://raw.githubusercontent.com/TDXDigital/TPS/master/LICENSE MIT
 */

require_once 'station.php';
class program extends station{
    private $station = null;
    protected $name = null;
    protected $djs = null;
    protected $genre = null;
    protected $length = null;
    protected $syndicateSource = null;
    protected $active = null;
    protected $airTime = null;
    protected $govReqOverride = false;
    protected $playlistOverride = false;
    protected $hitLimit = null;
    protected $sponsor = false;
    protected $theme = null;
    protected $programID = null;
    protected $displayOrder = false;
    protected $reviewable = null;
    protected $lastReview = null;

    private $data = array();
    /**
     *
     * @global type $mysqli
     * @version 1.0
     */
    public function __construct(station &$station, $Id = NULL) {
        $this->station = $station;
        /**
         * this does duplicate the parent but It is likely more desirable
         * than a detached child object
         */
        parent::__construct($this->station->callsign);
        $this->programID = $Id;
        $this->update();
    }

    public function updateParent(){
        if(parent::updateParent()){
            return $this->update();
        }
        else{
            $trace = debug_backtrace();
            trigger_error(
                'Undefined property via __get(): ' .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_NOTICE);
            return False;
        }
    }

    public function update(){
        if(!$this->programID){
            return False;
        }
        $con = $this->mysqli->prepare(
                "SELECT programname, length, syndicatesource, genre, active, Airtime, CCX, PLX, HitLimit, SponsId, displayorder, Theme, Display_Order, Reviewable, last_review FROM program WHERE ProgramID=? and callsign=?"
            );
        if($this->mysqli->error){
            die($this->mysqli->error);
        }
        if($con){
            $con->bind_param("ss",$this->programID,$this->callsign);
            $con->bind_result(
                    $this->name, $this->length, $this->syndicateSource,
                    $this->genre, $this->active, $this->airTime,
                    $this->govReqOverride, $this->playlistOverride,
                    $this->hitLimit, $this->sponsor, $this->displayOrder,
                    $this->theme, $this->displayOrder, $this->reviewable,
                    $this->lastReview
                    );
            $con->execute();
            $con->fetch();
            return True;
        }
        else{
            return False;
        }
    }

    /**
     * @abstract values associated with the program, not stored in Database
     * only in memory. will be destroyed on object destruction, not passed to
     * child objects (episode)
     * @param type $name
     * @param type $value
     */
    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function __get($name){
        if(array_key_exists($name, $this->data)){
            return $this->data[$name];
        }
        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    public function __isset($name){
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        echo "Unsetting '$name'\n";
        unset($this->data[$name]);
    }

    public function getValues(){
        return array(
            "callsign" => $this->callsign,
            "name" => $this->name,
            "djs" => $this->djs,
            "genre" => $this->genre,
            "length" => $this->length,
            "syndicateSource" => $this->syndicateSource,
            "active" => $this->active,
            "airTime" => $this->airTime,
            "govReqOverride" => $this->govReqOverride,
            "playlistOverride" => $this->playlistOverride,
            "hitLimit" => $this->hitLimit,
            "sponsor" => $this->sponsor,
            "theme" => $this->theme,
            "programID" => $this->programID,
            "displayOrder" => $this->displayOrder,
            "reviewable" => $this->reviewable,
            "lastReview" => $this->lastReview,
        );
    }

    public static function withCallsign($callsign, $id){
        $tmpstn = new station($callsign);
        $instance = new self($tmpstn, $id);
        return $instance;
    }

    public static function getId($callsign, $name){
        $callsign = $callsign;
        $name = $name;
        $result = null;
        $tmpstn = new station($callsign);
        $con = $tmpstn->mysqli->prepare(
                    "SELECT ProgramID FROM program where "
                . "programname=? and callsign=?"
                );
        if($con === false){
            $tmpstn->log->debug("Error Occured in getting ID");
        }
        $con->bind_param('ss',$name,$tmpstn->callsign);
        $con->bind_result($result);
        $con->execute();
        $con->fetch();
        return $result;
    }

    public static function getName($callsign, $id){
        $callsign = $callsign;
        $id = $id;
        $result = null;
        $tmpstn = new station($callsign);
        $con = $tmpstn->mysqli->prepare(
                    "SELECT programname FROM program where "
                . "ProgramID=? and callsign=?"
                );
        $con->bind_param('ss',$id,$tmpstn->callsign);
        $con->bind_result($result);
        $con->execute();
        return $result;
    }

    public static function withId(station &$station, $id){
        $instance = new self($station,$id);
        return $instance;
    }

    public function getEpisodes(){
        /**
         * gets all episode objects for this program
         */
        $result = array();
        $episodes = $this->getEpisodeIds();
        if($episodes){
            foreach ($episodes as $Id):
                $episode = $this->getEpisode($Id);
                array_push($result, $episode);
            endforeach;
            return $result;
        }
        else{return false;}
    }

    public function getEpisodeIds(){
        /**
         * gets and array with all episode ids for this program
         */
        $result = array();
        $progam = null;
        $con = $this->mysqli->prepare(
                "SELECT EpNum FROM episode where programName=? and callsign=?");
        $con->bind_param('ss',$this->name,$this->callsign);
        $con->bind_result($progam);
        if($con->execute()){
            while($con->fetch()){
                array_push($result, $progam);
            }
            return $result;
        }
        else{return false;}
    }

    public function episodeCount(){
        /**
         * gets and array with all episode ids for this program
         */
        if($ids = $this->getEpisodeIds()){
            return sizeof($ids);
        }
        else{return false;}
    }

    public function getProgram($station = null, $Id){
        $station = $station?:$this;
        return \TPS\program::withID($station,$Id);
    }

    public function getProgramId(){
        /**
         * gets the ID associated with a program
         */
        $progam = null;
        $con = $this->mysqli->prepare(
                "SELECT ProgramID FROM program where programname=? and callsign=?");
        $con->bind_param('ss',$this->name,$this->callsign);
        $con->bind_result($progam);
        if($con->execute()){
            return $progam;
        }
        else{return false;}
    }

    public function getProgramGenre()
    {
        $con = $this->mysqli->prepare("SELECT genreid from `genre` order by genreid asc");
        $con->bind_result($genreid);
        $result = array();

        if($con->execute())
        {
            while($con->fetch()){
                array_push($result, $genreid);
            }
            $con->close();
            return $result;
        }
        else
            return false;
    }

    public function createNewProgram($callsign, $progname, $length, $syndicateSource, $host, $genre, $weight)
    {


        $insert_program = $this->mysqli->prepare("insert into program (programname, callsign, length, syndicatesource, genre, weight) values (?,?,?,?,?,?)");
        $insert_program->bind_param('ssissi',$progname,$callsign,$length,$syndicateSource,$genre, $weight);
        if!($insert_program->execute())
            return false;
        $insert_program->close();

        $performs = "insert into performs (callsign, programname, Alias) values (?,?,?)";
        $insert_performs = $this->mysqli->prepare($performs);
        $insert_performs->bind_param('sss',$callsign,$progname,$host);
        if(!$insert_performs->execute())
            return false;
        $insert_performs->close();
        return true;   
    }
}
