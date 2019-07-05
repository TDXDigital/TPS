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
    protected $weight = null;

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
                "SELECT programname, length, syndicatesource, genre, active, Airtime, CCX, PLX, HitLimit, SponsId, displayorder, Theme, Display_Order, Reviewable, last_review, weight FROM program WHERE ProgramID=? and callsign=?"
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
                    $this->lastReview, $this->weight
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
            "djs" => self::getDjByProgramName($this->name),
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
            "weight" => $this->weight
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

    public function createNewProgram($callsign, $progname, $length, $syndicateSource, $host, $genre, $weight, $active)
    {
        $insert_program = $this->mysqli->prepare("REPLACE into program (programname, callsign, length, syndicatesource, genre, weight, active) values (?,?,?,?,?,?,?)");
        $insert_program->bind_param('ssissdi',$progname,$callsign,$length,$syndicateSource,$genre, $weight,$active);
        if(!$insert_program->execute())
            return false;
        $insert_program->close();

        $performs = "REPLACE into performs (callsign, programname, Alias) values (?,?,?)";
        $insert_performs = $this->mysqli->prepare($performs);
        $insert_performs->bind_param('sss',$callsign,$progname,$host);
        if(!$insert_performs->execute())
            return false;
        $insert_performs->close();
        return true;   
    }

    /*
    * @abstract Replaces each string passed in with the mysqli_real_escape_string'd version
    * @param array $strings Array of strings to sanitize
    * @return N/A
    */
    public function sanitizeStrings(&$strings) {
	foreach ($strings as &$str)
	    $str = mysqli_real_escape_string($this->mysqli, $str);
    }

    /*
    * @abstract Updates the program and performs tables to match the passed-in information
    * @param int    $programID       Unique program id number
    * @param string $callsign        Callsign of program
    * @param string $programName     Name of program
    * @param bool   $active          Status of program - active/inactive
    * @param int    $length          Length of program in minutes
    * @param string $syndicateSource Syndicate source
    * @param string $genre           Genre of program
    * @param int    $hitLimit
    * @param string $displayOrderStr Display order ('desc')
    * @param int    $displayOrderNum Display order (0/1 - Title,Artist,Album or Artist,Album,Title
    * @param int    $theme           Theme number
    * @param float  $weight          Weight of the program
    * @param array  $hosts           Array of associative arrays for each host [['Alias'=><alias>, 'STdate'=><startDate>, 'ENdate'=><endDate>], ...]
    * @return N/A or a string stating the program name wanting to be change to is unavailable ('Name unavailable')
    */
    public function updateProgram($programID, $callsign, $programName, $active, $length, $syndicateSource, $genre, $hitLimit, $displayOrderStr,
				  $displayOrderNum, $theme, $weight, $hosts) {
	// Sanitize input strings
	$strings = [$callsign, $programName, $syndicateSource, $genre, $displayOrderStr];
	$this->sanitizeStrings($strings);

	// Get the program name that was listed before this update started
	$stmt = $this->mysqli->query("SELECT programname FROM program WHERE ProgramID=$programID");
	while($row = $stmt->fetch_array(MYSQLI_ASSOC))
	    $oldName = $row['programname'];
	$stmt->close();

	// Determine if the program name is being changed...
	if ($programName == $oldName) {
	    $nameToDelete = $programName;
	} else {
	    // Ensure the new name isn't already taken by another program
	    $programNameLower = strtolower($programName);
	    $stmt = $this->mysqli->query("SELECT programname FROM program WHERE LOWER(programname)='$programNameLower' AND callsign='$callsign'");
	    if ($stmt->num_rows > 0) {
	 	$stmt->close();
		return 'Name unavailable';
	    }
	    $stmt->close();
	    $nameToDelete = $oldName;
	}

	// Update the `program` table
	$this->mysqli->query("UPDATE program SET programname='$programName', length=$length, syndicatesource='$syndicateSource', genre='$genre', " .
			         "active=$active, HitLimit=$hitLimit, displayorder='$displayOrderStr', Theme=$theme, Display_Order=$displayOrderNum, " .
			         "weight=$weight WHERE ProgramID=$programID;");

	// Delete the old hosts info from the `performs` table
	$this->mysqli->query("DELETE FROM performs WHERE callsign='$callsign' AND programname='$programName';");

	// Add the updated hosts info into the `performs` table
	foreach ($hosts as $i => $host) {
	    $strings = [$host['Alias'], $host['STdate'], $host['ENdate']];
	    $this->sanitizeStrings($strings);
	    $this->mysqli->query("INSERT INTO performs (callsign, programname, Alias, STdate, ENdate) VALUES ('$callsign', '$programName', '" . 
				 $host['Alias'] . "', '" . $host['STdate'] . "', '" . $host['ENdate'] . "');");
	}

    }

    public function displayTable($filter)
    {
        
        $where = '';
        $table = 'program';
         
        // Table's primary key
        $primaryKey = 'programID';         
        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        
        $columns = array(
            array( 'db' => 'programID',   'dt' => 'programID' ),
            array( 'db' => 'programname', 'dt' => 'programname' ),
            array( 'db' => 'length', 'dt' => 'length' ),
            array( 'db' => 'syndicatesource', 'dt' => 'syndicatesource' ),
            array( 'db' => 'genre', 'dt' => 'genre' ),
            array( 'db' => 'active',  'dt' => 'active' ),
        );

        $prog_data = \SSP::complex($_GET, $this->db, $table, $primaryKey, $columns, null, $where);

        foreach($prog_data['data'] as &$program) {
            $program['host'] = $this->getDjByProgramName($program['programname']);
        }
        return json_encode($prog_data);
    }

    public function getDjByProgramName($programName)
    {
        $con = $this->mysqli->prepare("SELECT djname FROM dj JOIN performs WHERE dj.alias = performs.alias AND programname = ?;");
        $con->bind_param("s", $programName);
        $con->bind_result($djName);

        if($con->execute())
        {
            $con->fetch();
            $con->close();
            return $djName;
        }
        else
            return false;
    }
}
