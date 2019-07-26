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

    //instanciate object by episode number
    public function setAttributes($epNum)
    {
        $this->EpisodeID = $epNum;

        $stmt = $this->mysqli->prepare("SELECT * FROM episode where EpNum = ?");
        $stmt->bind_param("s", $epNum);
        $stmt->execute();
        $result = $stmt->get_result();

        $episode = array();
        while($row = $result->fetch_assoc()){
            $episode = $row;
        }
        $stmt->free_result();
        $stmt->close();

        $this->time = $episode["starttime"];
        $this->date = $episode["date"];
        $this->description = $episode["description"];
        $this->type = $episode["Type"];
        $this->recordDate = $episode["prerecorddate"];
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

    /*
    * @author Derek Melchin
    * @abstract Return the tree of the playsheet insertion types
    * @return Array A mapping of playsheet insertion types to their child types and node category codes.
    */
    public function getInsertionTypes() {
	$tree = [
	 "Spoken" => ["General Spoken Word" => 1.2, "Disclaimer" => 1.2, "News" => 1.1, "Verbal Station ID" => 1.2],

	 "Music" => ["Pop/Alt category" => 2.1, 
		     "Acoustic version" => 2.3, 
		     "Folk/Roots category" => [
				"General folk roots" => 2.1,
				"Country" => 2.2,
				"Traditional folk" => 3.2,
				"Traditional blues" => 3.4
		     ], 
		     "Heavy category" => 2.1, 
		     "Electronic category" => 2.1, 
		     "World/Indigenous" => [
				"Traditional world/Non-English or French" => 3.3,
				"Canadian Indigenous" => 3.3
		     ],
		     "Jazz/Classical category" => [
				"Classical" => 3.1,
				"Jazz" => 3.4
		     ],
		     "Experimental category" => 3.6,
		     "Other category" => [
				"Religious" => 3.5,
				"Adult Contemporary" => 2.4
		     ]],

	 "Station ID" => ["Musical" => 4.3, "Verbal" => 1.2],

	 "PSA/Promo" => ["PSA" => 1.2, "Radio Show Promo" => 4.5],

	 "Ad" => ["Paid Ad/Friends Ad" => 5.1, "Sponsor Ad" => 5.4, "Sponsor ID" => 5.3],

	 "Radio show specific" => ["Theme" => 4.1, "Show/Programmer ID/Intro/Stinger" => 4.4], 

	 "Other" => ["Tech Test" => 4.2]
	];
	return $tree;
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


    public static function getEpisodeByEpNum($epNum)
    {
        $tmpstn = new station($_SESSION['CALLSIGN']);
        $stmt = $tmpstn->mysqli->prepare("SELECT "
                . "callsign, programname, date, starttime, type, "
                . "prerecorddate, description, IP_Created, totalspokentime, "
                . "`Lock`, EndStamp, LastAccess, endtime, EpNum FROM episode "
                . "WHERE `EpNum`=?");
        $stmt->bind_param("s",$epNum);
        if($stmt === false){
            $tmpstn->log->error($tmpstn->mysqli->error,"getEpisodeByEpNum");
        }
         $stmt->bind_result($callsign, $name, $date,
                $time, $type, $recordDate,
                $description, $originIP, $totalSpokenTime,
                $locked, $finalizedTimestamp,
                $lastAccessTimestamp, $endTime,
                $EpisodeID);
           
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


        return array(
            "id" => $EpisodeID,
            "name" => $name,
            "date" => $date,
            "time" => $time,
            "type" => $type,
            "endTime" => $endTime,
            "callsign" => $callsign,
            "recordDate" => $recordDate,
            "description" => $description,
            "totalSpokenTime" => $totalSpokenTime,
        );
    }

    public static function getSongByEpNum($epNum)
    {
        $tmpstn = new station($_SESSION['CALLSIGN']);
        $episode = self::getEpisodeByEpNum($epNum);
        $stmt = $tmpstn->mysqli->prepare("SELECT * FROM song where callsign = ? and programname = ? and date = ? and starttime = ?");
        $param = array($episode['callsign'], $episode['name'], $episode['date'], $episode['time']);

        $stmt->bind_param("ssss", ...$param);
        $stmt->execute();
        $result = $stmt->get_result();

        $songs = [];
        while($row = $result->fetch_assoc()){
            array_push($songs, $row);
        }
        $stmt->free_result();
        $stmt->close();

        return $songs;
    }


    //Everything is array except epNum
    public static function insertSongs($row, $epNum, $title, $album, $composer, $time, $artist, $cancon, $playlistNumber, $type, $category, $hit, $inst, $lang, $note=null, $spoken=null, $AdViolationFlag=null)
    {

        $tmpstn = new station($_SESSION['CALLSIGN']);
        $episode = self::getEpisodeByEpNum($epNum);

        $stmt = $tmpstn->mysqli->prepare("INSERT INTO `song` 
                                        (callsign, programname, date, starttime,
                                        instrumental, time, title, album, composer, 
                                        note, spoken, artist, cancon, 
                                        playlistnumber, type, AdViolationFlag, category,hit, language) VALUES 
                                        (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

        $songs = array();

         foreach ($row as $key => $value)
        {
            //if checkbox is checked, set value 1, 0 otherwise
            $cancon[$value] = array_key_exists($value, $cancon)? $cancon[$value]: 0;
            $hit[$value] = array_key_exists($value, $hit)? $hit[$value]: 0;
            $inst[$value] = array_key_exists($value, $inst)? $inst[$value]: 0;

            // if(strpos($time[$value], '~'))
            //     $time[$value] = substr($time[$value], 0, strpos($time[$value], '~'));

            $param = array( $episode['callsign'], $episode['name'], $episode['date'], $episode['time'],
                            $inst[$value], $time[$value], $title[$value], $album[$value], $composer[$value],
                                $note[$value], $spoken, $artist[$value], $cancon[$value], 
                                $playlistNumber[$value], $type[$value], $AdViolationFlag, $category[$value], $hit[$value], $lang[$value]);

            $stmt->bind_param("ssssisssssssiisiiis", ...$param);
            if(!$stmt->execute())
                $this->log->error($this->mysqli->errno);

            $songs[$key]["title"] = $title[$value];
            $songs[$key]["album"] = $album[$value];
            $songs[$key]["artist"] = $artist[$value];
            $songs[$key]["inst"] = $inst[$value];
            $songs[$key]["composer"] = $composer[$value];
            $songs[$key]["playlistNumber"] = $playlistNumber[$value];
            $songs[$key]["cancon"] = $cancon[$value];
            $songs[$key]["type"] = $type[$value];
            $songs[$key]["category"] = $category[$value];
            $songs[$key]["hit"] = $hit[$value];
            $songs[$key]["time"] = $time[$value];
            $songs[$key]["lang"] = $lang[$value];

        }
         $stmt->close();
         return $songs;
    }

 //Everything is array except epNum
    public static function updateSongs($row, $epNum, $title, $album, $composer, $time, $artist, $cancon, $playlistNumber, $type, $category, $hit, $inst, $lang, $note=null, $spoken=null, $AdViolationFlag=null)
    {

        $tmpstn = new station($_SESSION['CALLSIGN']);
        $episode = self::getEpisodeByEpNum($epNum);

        //first, delete all the songs from the episode
        $stmt = $tmpstn->mysqli->prepare("DELETE FROM `song` WHERE callsign = ? AND programname = ? AND date = ? AND startTime = ?");
        $param = array( $episode['callsign'], $episode['name'], $episode['date'], $episode['time']);
        $stmt->bind_param("ssss", ...$param);

        if(!$stmt->execute())
            $this->log->error($this->mysqli->errno);
        else
            self::insertSongs($row, $epNum, $title, $album, $composer, $time, $artist, $cancon, $playlistNumber, $type, $category, $hit, $inst, $lang, $note);
        $stmt->close();
    }

    public static function updateEndTime($epNum, $endTime)
    {
        $tmpstn = new station($_SESSION['CALLSIGN']);
        //first, delete all the songs from the episode
        $stmt = $tmpstn->mysqli->prepare("UPDATE episode SET endTime = ? WHERE EpNum = ?");
        $param = array( $endTime, $epNum);
        $stmt->bind_param("si", ...$param);

        if(!$stmt->execute())
            $this->log->error($this->mysqli->errno);
        $stmt->close();
    }
    public static function updateSpokenTime($epNum, $spokenTime)
    {

        $tmpstn = new station($_SESSION['CALLSIGN']);
        //first, delete all the songs from the episode
        $stmt = $tmpstn->mysqli->prepare("UPDATE episode SET totalspokentime = ? WHERE EpNum = ?");
        $param = array( $spokenTime, $epNum );
        $stmt->bind_param("ii", ...$param);
        
        if(!$stmt->execute())
        {
            $this->log->error($this->mysqli->errno);
        }
        $stmt->close();
    }

    public function getAdOptions($SPONS)
    {

        $REQAD_SQL = "SELECT adverts.*,adrotation.* FROM adrotation,addays,adverts WHERE '".
        date('H:i:s')."' BETWEEN adrotation.startTime AND adrotation.endTime AND addays.AdIdRef=".
        "adrotation.RotationNum AND adrotation.AdId=adverts.AdId AND addays.Day='".date('l').
        "' AND adverts.active='1' AND '".date('Y-m-d')."' BETWEEN adverts.StartDate AND ".
        "adverts.EndDate";
        $RQADSIDS = array();
        $ADIDS = array();
        $REQAD = "";
        if(!$READS = $this->mysqli->query($REQAD_SQL))
        {
            $REQAD .= "<option value='-1'>ERROR - AdRotation</option>";
        }
        else if(mysqli_num_rows($READS)==0){
            $REQAD .= "<option value='-1'>No Paid Commercials</option>";
        }
        else if(!isset($SPONS)){
            while($PdAds=$READS->fetch_array(MYSQLI_ASSOC)){
                if($PdAds['Limit'] == NULL || $PdAds['Playcount'] < $PdAds['Limit']){
                                // Check BlockLimit (BLIM)
                    $CHECKBLIM = "SELECT count(song.songid) FROM adrotation,song WHERE adrotation.AdId='".
                    addslashes($PdAds['AdId'])."' AND song.title='".addslashes($PdAds['AdName']).
                    "' and song.date='".addslashes($this->date)."' and song.time BETWEEN '".
                    addslashes($PdAds['startTime'])."' AND '".addslashes($PdAds['endTime'])."' ";
                    $BL_lim_R = $this->mysqli->query($CHECKBLIM);
                    $BL_lim = $BL_lim_R->fetch_array(MYSQLI_ASSOC);
                    if($this->mysqli->errno){
                        $REQAD .= "<option value='-3'>ERROR SQL</option>";
                    }
                    if($BL_lim['count(song.songid)']<$PdAds['BlockLimit']){
                                    //echo "<option value='-2'>BL_Lim:".$BL_lim['count(song.songid)']."</option>";
                        $REQAD .= "<option value='".$PdAds['AdId']."'>".$PdAds['AdName']."</option>";
                        array_push($RQADSIDS,$PdAds['AdId']);
                        array_push($ADIDS,$PdAds['AdId']);
                        $SQL_PL_AD = "INSERT INTO promptlog (EpNum,AdNum) VALUES (".
                        addslashes($ep_num).",".addslashes($PdAds['AdId']).")";
                        if(!$this->mysqli->query($SQL_PL_AD)){
                            $REQAD .= "<!-- ERROR: " . $this->mysqli->error . "-->";
                            error_log("TPS Error; Line 963: Could not perform SQL Query - ".$this->mysqli->error);
                        }
                        else{
                            $REQAD .= "<!-- Inserted into Log -->";
                        }
                    }
                }
            }
        }
        if(sizeof($RQADSIDS)>0){
            if($REQAD!=""&&!isset($SPONS)){

            }
            else if(isset($SPONS)){
                $REQAD = "<option>Sponsored Program</option>";
            }
            else{
                $REQAD = "<option>No Required Ads [E3]</option>";
            }
        }
        else{
            $REQAD = "<option>No Required Ads</option>";
        }

        // Friends Ads
        $ADOPT="";
        if(sizeof($RQADSIDS) > 0 && !isset($SPONS)){
            $ADOPT .= "<option>Paid Ad Required this hour [".sizeof($RQADSIDS)."]</option>";
        }
        else
        {
            if(isset($SPONS)){
                $ADOPT .= "<option value='".$SPONS['AdId']."'>".$SPONS['AdName']."</option>";
                array_push($ADIDS,$avadi['AdId']);
            }
            else{
                        //$selcom51 is origin
                $minplaysql51 = "select MIN(Playcount) from adverts where Category='51' ".
                "and Active='1' and Friend='1' and '".$this->mysqli->real_escape_string($this->date).
                "' between StartDate and EndDate";
                $advertResult = $this->mysqli->query($minplaysql51);
                if(!$minplay51Array = $advertResult->fetch_array(MYSQLI_ASSOC)){
                    $selcom51 = "select * from adverts where Category='51' and '".
                    $this->mysqli->real_escape_string($this->date)."' between StartDate and EndDate";
                }
                else{
                    $minplay51 = $minplay51Array['MIN(Playcount)'];
                    $selcom51 = "select * from adverts where Category='51' and '" .
                    addslashes($this->date) . "' between StartDate and EndDate and Friend='1' ".
                    "and Active='1' and Playcount='".$minplay51."' ";
                }
                $selspon = "select MIN(Playcount) from adverts where Category!='51' and '" .
                addslashes($this->date) . "' between EndDate and StartDate ";
                if($comsav = $this->mysqli->query($selcom51)){
                    $ADOPT = "";
                    while($avadi = $comsav->fetch_array(MYSQLI_ASSOC)){
                        $ADOPT .= "<option value=\"" . $avadi['AdId'] . "\">" . $avadi['AdName'] . "</option>";
                        array_push($ADIDS,$avadi['AdId']);
                    }
                }
                else{
                    $ADOPT = "<option value=\"-1\">ERROR - SQL Command</option>";
                }
            }
        }
        $result = array();
        $result['REQAD'] = $REQAD;
        $result['ADOPT'] = $ADOPT;
        $result['ADIDS'] = $ADIDS;
        $result['RQADSIDS'] = $RQADSIDS;
        return $result; 

    }

    public function getAllCommercials($ads)
    {
        $ADIDS = $ads['ADIDS'];
        $RQADSIDS = $ads['RQADSIDS'];
        $output = "";
       //<input type="text" name="title" id="title001" size="33" required="true" maxlength="45">
        $output .= "<select id=\"ADLis\" name=\"title\" class=\"adch form-control\" >";
        // $output .= "<option value=\"\" disabled selected>Select Commercial option</option>";

            $SLADS = "select * from adverts where Category='51' and '" .
                addslashes($this->date) . "' between StartDate and EndDate and Active='1' ".
                "order by AdName";
            if(!$SRZ = $this->mysqli->query($SLADS)){
                $output .= "<option value='0'>NO ADS AVAILABLE</option>";
            }
            else{
                $ADGR_AVAIL = array();
                $ADGR_REQUI = array();
                $ADGR_INVAL = array();
                while($ADZL = $SRZ->fetch_array(MYSQLI_ASSOC)){
                    $AVAIL=FALSE;
                    $REQUIRE=FALSE;
                    $TEMP = "<option value=\"" . $ADZL['AdId'] . "\" ";
                    if(in_array((int)$ADZL['AdId'], $ADIDS)){
                        $AVAIL = TRUE;
                        $TEMP .= " style=\"background-color:green; color:white\" ";
                    }
                    else if((int)in_array($ADZL['AdId'], $RQADSIDS)){
                        $REQUIRE = TRUE;
                        $TEMP .= " style=\"background-color:blue; color:white\" ";
                    }
                    $TEMP .= " >". $ADZL['AdName'] ."</option>";

                    if($REQUIRE){
                        array_push($ADGR_REQUI,$TEMP);
                        $output .= "<!-- Entered Require -->";
                    }
                    elseif($AVAIL){
                        array_push($ADGR_AVAIL,$TEMP);
                    }
                    else{
                        array_push($ADGR_INVAL,$TEMP);
                    }
                }
            }
        $output .= "<optgroup label=\"Required Advertisements";
            if(empty($ADGR_REQUI)){
                if(sizeof($ADGR_REQUI)<sizeof($RQADSIDS)){
                    $output .= " (DIFF-OVERRIDE) [".sizeof($ADGR_REQUI)."/".sizeof($RQADSIDS)."]\">";
                    $output .= $REQAD;
                    error_log("TPS Error, Could not account for required Adverts, possible code error values ".var_dump($RQADSIDS)." ");
                }
                else{
                    $output .= " (None) [".sizeof($ADGR_REQUI)."/".sizeof($RQADSIDS)."]\">";
                }
            }
            else{
                $output .= "\">";
                foreach ($ADGR_REQUI as $opt){
                    $output .= $opt;
                }
            }
        $output .= "</optgroup>";
        $output .= "<optgroup label=\"Available Advertisements";
            if(empty($ADGR_AVAIL)){
                $output .= " (None)\">";
            }
            else{
                $output .= "\">";
                foreach ($ADGR_AVAIL as $opt){
                    $output .= $opt;
                }
            }
        $output .= "</optgroup>";
        $output .= "<optgroup label=\"Invalid Advertisements";
            if(empty($ADGR_INVAL)){
                $output .= " (None)\">";
            }
            else{
                $output .= "\">";
                foreach ($ADGR_INVAL as $opt){
                    $output .= $opt;
                }
            }
        $output .= "</optgroup>";
        $output .= "</select>";

        return $output;
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

    public function displayTable($filter)
    {
        $where = '';
        $table = 'episode';
         
        // Table's primary key
        $primaryKey = 'EpNum';         
        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        
        $columns = array(
            array( 'db' => 'callsign',   'dt' => 'callsign' ),
            array( 'db' => 'programname', 'dt' => 'programname' ),
            array( 'db' => 'date', 'dt' => 'date' ),
            array( 'db' => 'prerecorddate', 'dt' => 'prerecorddate' ),
            array( 'db' => 'starttime', 'dt' => 'starttime' ),
            array( 'db' => 'EpNum', 'dt' => 'EpNum' ),
            array( 'db' => 'description', 'dt' => 'description' ),
        );

        $prog_data = \SSP::complex($_GET, $this->db, $table, $primaryKey, $columns, null, $where);

        // foreach($prog_data['data'] as &$program) {
        //     $program['host'] = $this->getDjByProgramName($program['programname']);
        // }
        return json_encode($prog_data);
    }

}
