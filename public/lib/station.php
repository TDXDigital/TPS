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

require_once 'logger.php';
require_once 'genre.php';
require_once 'tps.php';
class station extends TPS{
    protected $callsign = null;
    protected $stationName = null;
    protected $stationDesignation = null;
    protected $stationFrequency = null;
    protected $stationPhoneDirector = null;
    protected $stationPhoneManager = null;
    protected $stationPhoneRequest = null;
    protected $stationAddress = null;
    protected $stationWebsite = null;
    protected $DefaultSortOrder = 'asc';
    protected $GroupPlaylist = False;
    protected $GroupPlaylistReporting = False;
    #https://bootstrapbay.com/blog/bootstrap-ui-kit/
    protected $colorFail = "#FC6E51";
    protected $colorWarning = "#FFCE54";
    protected $colorPass = "#A0D468";
    protected $colorNote = "#4FC1E9";
    protected $ProgramCounters = False;
    protected $forceComposer = False;
    protected $forceArtist = False;
    protected $forceAlbum = False;
    protected $perHourTraffic = null;
    protected $perHourPSA = null;
    protected $timezone = "UTC";
    public $log = null;
    public $genres = null;

    function __construct($callsign=null) {
        parent::__construct();
        if(!is_null($callsign)){
            $this->setupParams($callsign);
        }
        if(isset($_SESSION['account'])){
            $username=$_SESSION['account'];
        }
        else{
            $username="AnonamousUser";
        }
        $this->log = new \TPS\logger($username);
        $this->genres = new \TPS\genre($this->callsign);
    }

    /*
    * @abstract Convert the datetime data stored in the database using `NOW()` to the station's local timezone
    * @param string $serverTime The datetime stored in the database using a simple `NOW()` statement
    * @return string The $serverTime arugement converted to the stations local timezone
    */
    public function getTimeFromServerTime($serverTime) {
    if(PHP_OS != 'WINNT')
    {
        $serverTZCode = strtolower(system('date +"%Z"'));
        $serverTZId = \DateTimeZone::listAbbreviations(\DateTimeZone::ALL)[$serverTZCode][0]['timezone_id'];
        $stmt = $this->mysqli->query("SELECT CONVERT_TZ('" . $serverTime . "','" . $serverTZId . 
                     "',(SELECT timezone FROM station WHERE callsign='" . $_SESSION['CALLSIGN'] . "')) as time;");
        
    }
    else
    {
        $stmt = $this->mysqli->query("SELECT now() as time;");   
    }
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC))
            return $row['time'];
	throw new Exception("Error while converting server time to station time.");
    }


    /*
    * @abstract Get time zone code of the station
    * @return str  Time zone code of the station
    */
    public function getTimeZoneCode() {
        $stmt = $this->mysqli->prepare("SELECT timezone FROM station WHERE callsign=?;");
        $stmt->bind_param('s',$this->callsign);
        $stmt->bind_result($stationTZ);
        if($stmt->execute()) {
            while($stmt->fetch())
                foreach (\DateTimeZone::listAbbreviations(\DateTimeZone::ALL) as $tzCode => $zones) {
		    $tzCode = strtoupper($tzCode);
		    foreach ($zones as $zone)
		        if ($zone['timezone_id'] == $stationTZ)
		            try {
				date_default_timezone_set($tzCode);
				return $tzCode;
			    } catch (\Exception $e) {
				continue;
			    }
		}
            $stmt->close();
        }
    }

    public function getAlertProviders(){
        $con = $this->mysqli->prepare(
                "SELECT id, provider, url, logo, locations, active, area FROM"
                . " emergencyalertsettings WHERE station=?");
        $con->bind_param('s',$this->callsign);
        $con->bind_result($id, $provider, $url, $logo,
                $locations, $active, $area);
        $result = array();
        if($con->execute()){
            while($con->fetch()){
                $result[$provider] = array(
                    "feed" => $url,
                    "logo" => $logo,
                    "locations" => $locations,
                    "active" => $active,
                    "area" => $area,
                );
            }
            $con->close();
        }
        return $result;
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
        if(!is_null($this->callsign)){
            return $this->setupParams($this->callsign);
        }
        return True;
    }

    public function setupParams($callsign){
        $callsign = strtoupper($callsign);

        $this->callsign = $callsign;
        $this->setStation($callsign);
        $stations = $this->getStation($callsign);
        try {
            $params = $stations[$callsign];
        } catch (Exception $exc) {
            return FALSE;
        }
        if(sizeof($params)>0){
            # set params
            $this->stationName = $params["name"];
            $this->stationDesignation = $params["designation"];
            $this->stationFrequency = $params["frequency"];
            $this->stationWebsite = $params["website"];
            $this->stationAddress = $params["address"];
            $this->stationPhoneDirector = $params["phone"]['director'];
            $this->stationPhoneRequest = $params["phone"]["main"];
            $this->stationPhoneManager = $params['phone']['manager'];
            $this->DefaultSortOrder = $params['defaultSort'];
            $this->colorPass = $params["colorPass"];
            $this->colorNote = $params["colorNote"];
            $this->colorFail = $params["colorFail"];

            if($params['displayCounters'] == 1){
                $this->ProgramCounters = True;}
            else {$this->ProgramCounters = False;}

            if($params["groupPlaylistProgramming"] == 1){
                $this->GroupPlaylist = True;}
            else {$this->GroupPlaylist = False;}

            if($params["groupPlaylistReporting"] == 1){
                $this->GroupPlaylistReporting = True;}
            else {$this->GroupPlaylistReporting = False;}

            if($params["forceComposer"] == 1){
                $this->forceComposer = True;}
            else {$this->forceComposer = False;}

            if($params["forceArtist"] == 1){
                $this->forceArtist = True;}
            else {$this->forceArtist = False;}

            if($params["forceAlbum"] == 1){
                $this->forceAlbum = True;}
            else {$this->forceAlbum = False;}

            $this->perHourTraffic = $params["perHourTraffic"];
            $this->perHourPSA = $params["perHourPSAs"];
            $this->timezone = $params['timezone'];
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

    public function getCallsign(){
        return $this->callsign;
    }

    public function getStation($callsign,$page=1,$maxResult=50){
        $this->sanitizePagination($page,$maxResult);
        if($con = $this->mysqli->prepare("SELECT stationname,Designation,"
                . "frequency,website,address,boothphone,directorphone,"
                . "ST_DefaultSort,ST_PLLG,ST_ForceComposer,ST_ForceArtist,"
                . "ST_ForceAlbum,ST_ColorFail,ST_ColorPass,ST_PLRG,"
                . "ST_DispCount,ST_ColorNote,managerphone,ST_ADSH,ST_PSAH,"
                . "timezone "
		. "FROM station where callsign=? order by callsign "
                . "limit ?,?;")){
            $con->bind_param('sii',$callsign,$page,$maxResult);
            if($con->execute()){
                $con->bind_result($stationname, $Designation, $frequency,
                      $website, $address, $boothphone, $directorphone,
                      $DefaultSort, $PLLG, $ForceComposer, $ForceArtist,
                      $ForceAlbum, $ColorFail, $ColorPass, $PLRG, $DispCount,
                      $ColorNote, $ManagerPhone, $ADSH, $PSAH, $timezone
                    );
                if($con->num_rows>1){
                    trigger_error(
                            "Multiple stations returned for unique key:"
                            . $callsign, E_USER_ERROR);
                }
                $result = array();
                while($con->fetch()){
                    $result[$callsign] = array(
                        "name"=>$stationname,
                        "designation"=>$Designation,
                        "frequency"=>$frequency,
                        "website"=>$website,
                        "address"=>$address,
                        "phone"=>array(
                            "main"=>$boothphone,
                            "manager"=>$ManagerPhone,
                            "director"=>$directorphone,
                        ),
                        "defaultSort"=>$DefaultSort,
                        "groupPlaylistProgramming"=>$PLLG,
                        "groupPlaylistReporting"=>$PLRG,
                        "forceComposer"=>$ForceComposer,
                        "forceArtist"=>$ForceArtist,
                        "forceAlbum"=>$ForceAlbum,
                        "displayCounters"=>$DispCount,
                        "colorPass"=>$ColorPass,
                        "colorFail"=>$ColorFail,
                        "colorNote"=>$ColorNote,
                        "perHourTraffic"=>$ADSH,
                        "perHourPSAs"=>$PSAH,
                        "timezone"=>$timezone
                    );
                }
                return $result;
            }
            else{return false;}
        }
        else{
            trigger_error($this->mysqli->error, E_USER_ERROR);
            return false;
        }
    }

    public function setStation($callsign){
        $callsign = strtoupper($callsign);
        $stations = $this->getStations();
        if(array_key_exists($callsign, $stations)){
            $this->callsign = $callsign;
            return $this->callsign;
        }
        else{
            return False;
        }
    }
    /**
     * changes the station name
     * @param type $name
     * @return boolean
     */
    public function setStationName($name){
        $con = $this->mysqli->prepare(
                "Update station SET stationname=? where callsign=?");
        $con->bind_param('ss',$name,$this->callsign);
        if($con->execute()){
            $this->stationName = $name;
            return true;
        }
        else{return false;}
    }

    /**
     * Changes Station Designation in DB and Class
     * @param type $Designation
     * @return boolean
     */
    public function setStationDesignation($Designation){
        $con = $this->mysqli->prepare(
                "Update station SET Designation=? where callsign=?");
        $con->bind_param('ss',$Designation,$this->callsign);
        if($con->execute()){
            $this->stationDesignation = $Designation;
            return true;
        }
        else{return false;}
    }

    /**
     * Changes Station Frequency in DB and Class
     * @param type $frequency
     * @return boolean
     */
    public function setStationFrequency($frequency){
        $con = $this->mysqli->prepare(
                "Update station SET frequency=? where callsign=?");
        $con->bind_param('ss',$frequency,$this->callsign);
        if($con->execute()){
            $this->stationFrequency = $frequency;
            return true;
        }
        else{return false;}
    }

    /**
     * Changes Director Phone in DB and Class
     * @param type $phone
     * @return boolean
     */
    public function setStationPhoneDirector($phone){
        $con = $this->mysqli->prepare(
                "Update station SET directorphone=? where callsign=?");
        $con->bind_param('ss',$phone,$this->callsign);
        if($con->execute()){
            $this->stationPhoneDirector = $phone;
            return true;
        }
        else{return false;}
    }

    /**
     * Changes Request Line number in DB and Class
     * @param type $phone
     * @return boolean
     */
    public function setStationPhoneRequest($phone){
        $con = $this->mysqli->prepare(
                "Update station SET boothphone=? where callsign=?");
        $con->bind_param('ss',$phone,$this->callsign);
        if($con->execute()){
            $this->stationPhoneRequest = $phone;
            return true;
        }
        else{return false;}
    }

    /**
     * Changes Station Manager Phone in DB and Class
     * @param type $phone
     * @return boolean
     */
    public function setStationPhoneManager($phone){
        $con = $this->mysqli->prepare(
                "Update station SET managerphone=? where callsign=?");
        $con->bind_param('ss',$phone,$this->callsign);
        if($con->execute()){
            $this->stationPhoneManager = $phone;
            return true;
        }
        else{return false;}
    }

    /**
     * Changes Station website in DB and Class
     * @param type $url
     * @return boolean
     */
    public function setStationWebsite($url){
        $con = $this->mysqli->prepare(
                "Update station SET website=? where callsign=?");
        $con->bind_param('ss',$url,$this->callsign);
        if($con->execute()){
            $this->stationWebsite = $url;
            return true;
        }
        else{return false;}
    }

    /**
     * Changes Station Address in DB and Class
     * @param type $address
     * @return boolean
     */
    public function setStationAddress($address){
        $con = $this->mysqli->prepare(
                "Update station SET address=? where callsign=?");
        $con->bind_param('ss',$address,$this->callsign);
        if($con->execute()){
            $this->stationAddress = $address;
            return true;
        }
        else{return false;}
    }

    public function hourlyTraffic(){
        return $this->perHourTraffic;
    }

    public function setHourlyTraffic($hourly) {
        $con = $this->mysqli->prepare(
                "Update station SET ST_ADSH=? where callsign=?");
        $con->bind_param('ss',$hourly,$this->callsign);
        if($con->execute()){
            $this->perHourTraffic = $hourly;
            return true;
        }
        else{return false;}
    }

    public function hourlyPSA(){
        return $this->perHourPSA;
    }

    public function setHourlyPSA($hourly) {
        $con = $this->mysqli->prepare(
                "Update station SET ST_PSAH=? where callsign=?");
        $con->bind_param('ss',$hourly,$this->callsign);
        if($con->execute()){
            $this->perHourPSA = $hourly;
            return true;
        }
        else{return false;}
    }

    /**
     * Changes Station TimeZone in DB and Class
     * @param type $tz
     * @return boolean
     */
    public function setStationTimeZone($tz){
        $tzlist = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        if(!in_array($tz, $tzlist)){
            trigger_error(
                    "TimeZone $tz not found in TimeZone list", E_USER_WARNING);
        }
        $con = $this->mysqli->prepare(
                "Update station SET timezone=? where callsign=?");
        $con->bind_param('ss',$tz,$this->callsign);
        if($con->execute()){
            $this->stationTimeZone = $tz;
            return true;
        }
        else{return false;}
    }

    /*
    * @abstract Changes station's host probation period days
    * @param int $days The number of days
    * @return boolean
    */
    public function setHostProbationDays($days) {
        $stmt = $this->mysqli->query("SHOW COLUMNS FROM `station` WHERE Field = 'hostProbationPeriodDays';");
        if ($stmt->num_rows == 0)
            return false;

        $con = $this->mysqli->prepare(
                "Update station SET hostProbationPeriodDays=? where callsign=?");
        $con->bind_param('is',$days,$this->callsign);
        if($con->execute()){
            $this->hostProbationDays = $days;
            return true;
        }
        else{return false;}
    }

    /**
    * @abstract Retrieve the station's host probation day period
    * @return int/bool The number of days a how for the station is on probation during registration.
    */
    public function getHostProbationDays() {
        $stmt = $this->mysqli->query("SHOW COLUMNS FROM `station` WHERE Field = 'hostProbationPeriodDays';");
        if ($stmt->num_rows == 0)
            return false;

        $con = $this->mysqli->prepare("SELECT hostProbationPeriodDays FROM station WHERE callsign=?");
        $con->bind_param('s',$this->callsign);
        $con->bind_result($probationPeriodDays);
        if($con->execute() && $con->fetch())
            return $probationPeriodDays;
        return false;
    }

    /*
    * @abstract Changes station's host probation weight multiplier
    * @param float $weight The weight of the station
    * @return boolean
    */
    public function setHostProbationWeight($weight) {
        $stmt = $this->mysqli->query("SHOW COLUMNS FROM `station` WHERE Field = 'hostProbationWeightMultiplier';");
        if ($stmt->num_rows == 0)
            return false;

        $con = $this->mysqli->prepare(
                "Update station SET hostProbationWeightMultiplier=? where callsign=?");
        $con->bind_param('ds',$weight,$this->callsign);
        if($con->execute()){
            $this->hostProbationWeight = $weight;
            return true;
        }
        else{return false;}
    }

    /**
    * @abstract Retrieve the station's host probation day period
    * @return int/bool The number of days a how for the station is on probation during registration.
    */
    public function getHostProbationWeight() {
        $stmt = $this->mysqli->query("SHOW COLUMNS FROM `station` WHERE Field = 'hostProbationWeightMultiplier';");
        if ($stmt->num_rows == 0)
            return false;

        $con = $this->mysqli->prepare("SELECT hostProbationWeightMultiplier FROM station WHERE callsign=?");
        $con->bind_param('s',$this->callsign);
        $con->bind_result($probationWeightMultiplier);
        if($con->execute() && $con->fetch())
            return $probationWeightMultiplier;
        return false;
    }

    private function setPlaylistLiveGrouping($gp){
        assert(in_array($gp, ['1','0',1,0,True,False]),'invalid paramater');
        if(is_bool($gp) || is_int($gp)){
            $gp = "".(int)$gp."";
        }
        $con = $this->mysqli->prepare(
                "Update station SET ST_PLLG=? where callsign=?");
        $con->bind_param('ss',$gp,$this->callsign);
        if($con->execute()){
            if($gp == '1'){
                $this->GroupPlaylist = True;
            }
            else{
                $this->GroupPlaylist = False;
            }
            return true;
        }
        else{return false;}
    }

    public function playlistLiveGroupingOn(){
        $result = $this->setPlaylistLiveGrouping("1");
        $this->log->info("Playlist Live Grouping enabled",
                $result?"pass":"fail");
        return $result;
    }

    public function playlistLiveGroupingOff(){
        $result = $this->setPlaylistLiveGrouping("0");
        $this->log->info("Playlist Live Grouping disabled",
                $result?"pass":"fail");
        return $result;
    }

    public function playlistLiveGrouping(){
        return $this->GroupPlaylist;
    }

    public function togglePlaylistLiveGrouping(){
        if($this->playlistLiveGrouping()){
            return $this->playlistLiveGroupingOff();
        }
        else{
            return $this->playlistLiveGroupingOn();
        }
    }

    private function setPlaylistReportingGrouping($gp){
        assert(in_array($gp, ['1','0',1,0,True,False]),'invalid paramater');
        if(is_bool($gp) || is_int($gp)){
            $gp = "".(int)$gp."";
        }
        $con = $this->mysqli->prepare(
                "Update station SET ST_PLRG=? where callsign=?");
        $con->bind_param('ss',$gp,$this->callsign);
        if($con->execute()){
            if($gp == '1'){
                $this->GroupPlaylistReporting = True;
            }
            else{
                $this->GroupPlaylistReporting = False;
            }
            return true;
        }
        else{return false;}
    }

    public function PlaylistReportingGroupingOn(){
        return $this->setPlaylistReportingGrouping("1");
    }

    public function PlaylistReportingGroupingOff(){
        return $this->setPlaylistReportingGrouping("0");
    }

    public function PlaylistReportingGrouping(){
        return $this->GroupPlaylistReporting;
    }

    public function togglePlaylistReportingGrouping(){
        if($this->PlaylistReportingGrouping()){
            return $this->PlaylistReportingGroupingOff();
        }
        else{
            return $this->PlaylistReportingGroupingOn();
        }
    }

    public function setDefaultSortOrder($SortOrder){
        $con = $this->mysqli->prepare(
                "Update station SET ST_DefaultSort=? where callsign=?");
        $con->bind_param('ss',$SortOrder,$this->callsign);
        if($con->execute()){
            $this->DefaultSortOrder = $SortOrder;
            return true;
        }
        else{return false;}
    }

    private function setProgramCounters($SortOrder){
        $con = $this->mysqli->prepare(
                "Update station SET ST_DispCount=? where callsign=?");
        $con->bind_param('ss',$SortOrder,$this->callsign);
        if($con->execute()){
            return true;
        }
        else{return false;}
    }

    public function programCountersOn(){
        if($this->setProgramCounters("1")){
            $this->ProgramCounters = True;
            return true;
        }
        else{return false;}
    }

    public function programCountersOff(){
        if($this->setProgramCounters("0")){
            $this->ProgramCounters = False;
            return true;
        }
        else{return false;}
    }

    public function programCounters(){
        return $this->ProgramCounters;
    }

    public function toggleProgramCounters(){
        if($this->programCounters()){
            return $this->programCountersOff();
        }
        else{
            return $this->programCountersOn();
        }
    }

    private function setForceComposer($value){
        $con = $this->mysqli->prepare(
                "Update station SET ST_ForceComposer=? where callsign=?");
        $con->bind_param('ss',$value,$this->callsign);
        if($con->execute()){
            return true;
        }
        else{return false;}
    }

    public function forceComposerOn(){
        if($this->setForceComposer("1")){
            $this->forceComposer = True;
            return true;
        }
        else{return false;}
    }

    public function forceComposerOff(){
        if($this->setForceComposer("0")){
            $this->forceComposer = True;
            return true;
        }
        else{return false;}
    }

    public function forceComposer(){
        return $this->forceComposer;
    }

    public function toggleForceComposer(){
        if($this->forceComposer()){
            return $this->forceComposerOff();
        }
        else{
            return $this->forceComposerOn();
        }
    }

    private function setForceArtist($value){
        $con = $this->mysqli->prepare(
                "Update station SET ST_ForceArtist=? where callsign=?");
        $con->bind_param('ss',$value,$this->callsign);
        if($con->execute()){
            return true;
        }
        else{return false;}
    }

    public function forceArtistOn(){
        if($this->setForceArtist("1")){
            $this->forceArtist = True;
            return true;
        }
        else{return false;}
    }

    public function forceArtistOff(){
        if($this->setForceArtist("0")){
            $this->forceArtist = True;
            return true;
        }
        else{return false;}
    }

    public function forceArtist(){
        return $this->forceArtist;
    }

    public function toggleForceArtist(){
        if($this->forceArtist()){
            return $this->forceArtistOff();
        }
        else{
            return $this->forceArtistOn();
        }
    }

    private function setForceAlbum($value){
        $con = $this->mysqli->prepare(
                "Update station SET ST_ForceAlbum=? where callsign=?");
        $con->bind_param('ss',$value,$this->callsign);
        if($con->execute()){
            return true;
        }
        else{return false;}
    }

    public function forceAlbumOn(){
        if($this->setForceAlbum("1")){
            $this->forceAlbum = True;
            return true;
        }
        else{return false;}
    }

    public function forceAlbumOff(){
        if($this->setForceAlbum("0")){
            $this->forceAlbum = True;
            return true;
        }
        else{return false;}
    }

    public function forceAlbum(){
        return $this->forceAlbum;
    }

    public function toggleForceAlbum(){
        if($this->forceAlbum()){
            return $this->forceAlbumOff();
        }
        else{
            return $this->forceAlbumOn();
        }
    }

    public function getAllProgramIds($active = TRUE){
        /**
         * gets the ID associated with a program
         */
        $result = array();
        $progam = null;
        $con = $this->mysqli->prepare(
                "SELECT ProgramID, programName FROM program where callsign=? and active=?");
        $iactive = (int)$active;
        $vcallsign = $this->callsign;
        $con->bind_param('si',$vcallsign,$iactive);
        $con->bind_result($progamID, $progName);
        if($con->execute()){
            while($con->fetch()){
                array_push($result,$progamID);
            }
            return $result;
        }
        else{return false;}
    }
    public function getAllPrograms($active = TRUE)
    {
        $result = array();
        $progam = null;
        $con = $this->mysqli->prepare(
                "SELECT ProgramID, programName, Airtime FROM program where callsign=? and active=?");
        $iactive = (int)$active;
        $vcallsign = $this->callsign;
        $con->bind_param('si',$vcallsign,$iactive);
        $con->bind_result($id, $name, $time);
        if($con->execute()){
            while($con->fetch()){
                $result[$id] = array
                (
                    "name" =>$name,
                    "time" =>$time
                );
            }
            return $result;
        }
        else{return false;}
    }

    public static function create($callsign, $name, $designation,
            $frequency, $website, $address, $mainPhone, $mgrPhone){
        $tps = new \TPS\TPS();
        $con = $tps->mysqli->prepare(
                "insert into `station` (callsign,stationname,Designation,"
                . "frequency,website,address,boothphone,directorphone) "
                . "values ( ?, ?, ?, ?, ?, ?, ?, ?)"
                );
        if($con===false){
            trigger_error($tps->mysqli->error,E_USER_ERROR);
        }
        $con->bind_param("ssssssss", $callsign, $name, $designation,
                $frequency, $website, $address, $mainPhone, $mgrPhone);
        $con->execute();
        if($con === false){
            trigger_error($tps->mysqli->error,E_USER_ERROR);
        }
        $result = $con->insert_id;
        $con->close();
        return $result;
    }

    public function login($email, $password, $server) {
        // Using prepared statements means that SQL injection is not possible.
        if ($stmt = $this->mysqli->prepare("SELECT id, username, password, salt, access
            FROM members
           WHERE email = ?
            LIMIT 1")) {
            $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
            $stmt->execute();    // Execute the prepared query.
            $stmt->store_result();

            // get variables from result.
            $stmt->bind_result($user_id, $username, $db_password, $salt, $access);
            $stmt->fetch();

            // hash the password with the unique salt.
            $password = hash('sha512', $password . $salt);
            echo 'Pass: '. $password . '<br>';
            echo 'Dbpa: '. $db_password;

            if ($stmt->num_rows == 1) {
                // If the user exists we check if the account is locked
                // from too many login attempts

                if ($this->checkbrute($user_id)) {
                    // Account is locked
                    // Send an email to user saying their account is locked
                    return false;
                } else {
                    // Check if the password in the database matches
                    // the password the user submitted.
                    if ($db_password == $password) {
                        // Password is correct!
                        // Get the user-agent string of the user.
                        $user_browser = $_SERVER['HTTP_USER_AGENT'];
                        // XSS protection as we might print this value
                        $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                        $_SESSION['user_id'] = $user_id;
                        // XSS protection as we might print this value
                        $username = preg_replace("/[^a-zA-Z0-9_\-]+/",
                                                                    "",
                                                                    $username);
                        $_SESSION['username'] = $username;
                        $_SESSION['login_string'] = hash('sha512',
                                  $password . $user_browser);
                        $_SESSION['account'] = $username;
                        $_SESSION['userId'] = $user_id;
                        $_SESSION['access'] = $access;
                        $_SESSION['AutoComLimit'] = 8;
                        $_SESSION['AutoComEnable'] = TRUE;
                        $_SESSION['TimeZone']='UTC'; // this is just the default to be updated after login
                        if($server){
                            $_SESSION['usr'] = easy_decrypt(ENCRYPTION_KEY,(string)$server->USER);
                            $_SESSION['rpw'] = easy_decrypt(ENCRYPTION_KEY,(string)$server->PASSWORD);
                            $_SESSION['DBNAME'] = (string)$server->DATABASE;
                            if((string)$server->RESOLVE == 'URL'){
                                $_SESSION['DBHOST'] = (string)$server->URL;
                            }
                            else{
                                $_SESSION['DBHOST'] = (string)$server->IPV4;
                            }
                            $_SESSION['SRVPOST'] = (string)$server->ID;
                        }
                        else{
                            $_SESSION['DBHOST'] = HOST;
                            $_SESSION['usr'] = USER;
                            $_SESSION['rpw'] = PASSWORD;
                            $_SESSION['DBNAME'] = DATABASE;
                            $_SESSION['SRVPOST'] = 'SECL';
                        }
                        $_SESSION['fname'] = $username;
                        $_SESSION['logo'] = 'images/Ckxu_logo_2.png';
                        $_SESSION['m_logo']=$_SESSION['logo'];
                        // Login successful.
                        $_SESSION['CALLSIGN'] = $this->callsign;
                        return true;
                    } else {
                        // Password is not correct
                        // We record this attempt in the database
                        //
                        // needs prepare then execute without mysqlnd
                        $now = time();
                        $this->mysqli->query("INSERT INTO login_attempts(user_id, time)
                                        VALUES ('$user_id', '$now')");
                        return false;
                    }
                }
            } else {
                // No user exists.
                return false;
            }
        }
        else{
         // failed to connect
         return false;
        }
    }

    private function checkbrute($user_id) {
        // Get timestamp of current time
        $now = time();

        // All login attempts are counted from the past 2 hours.
        $valid_attempts = ceil($now - (2 * 60 * 60));

        if ($stmt = $this->mysqli->prepare("SELECT time 
                                 FROM login_attempts
                                 WHERE user_id = ? 
                                AND time > ?")) {
            $stmt->bind_param('ii', $user_id, $valid_attempts);

            // Execute the prepared query.
            $stmt->execute();
            $stmt->store_result();

            // If there have been more than 5 failed logins
            if ($stmt->num_rows > 5) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function registerUser($username, $email, $password){
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $username = filter_var($username, FILTER_SANITIZE_STRING);
        $password = filter_var($password,FILTER_SANITIZE_STRING);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Not a valid email
            $error_msg .= '<p class="error">The email address you entered is not valid</p>';
        }
        if (strlen($password) != 128) {
            // The hashed pwd should be 128 characters long.
            // If it's not, something really odd has happened
            $error_msg .= '<p class="error">Invalid password configuration.</p>';
        }

        // Username validity and password validity have been checked client side.
        // This should should be adequate as nobody gains any advantage from
        // breaking these rules.
        //

        $prep_stmt = "SELECT id FROM members WHERE email = ? LIMIT 1";
        $stmt = $this->mysqli->prepare($prep_stmt);

        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // A user with this email address already exists
                $error_msg .= '<p class="error">A user with this email address already exists.</p>';
            }
        } else {
            $error_msg .= '<p class="error">Database error</p>';
            $error_msg .= $this->mysqli->error;
        }

        // TODO:
        // We'll also have to account for the situation where the user doesn't have
        // rights to do registration, by checking what type of user is attempting to
        // perform the operation.

        if (empty($error_msg)) {
            // Create a random salt
            $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));

            // Create salted password
            $password = hash('sha512', $password . $random_salt);

            // Insert the new user into the database
            if ($insert_stmt = $mysqli->prepare("INSERT INTO members (username, email, password, salt) VALUES (?, ?, ?, ?)")) {
                $insert_stmt->bind_param('ssss', $username, $email, $password, $random_salt);
                // Execute the prepared query.
                if (! $insert_stmt->execute()) {
                    return $error_msg;
                }
            }
            return True;
        }
        else{
            return $error_msg;
        }
    }

    public function getHosts()
    {
        $con = $this->mysqli->prepare("SELECT alias, djname from `dj` order by djname");
        $con->bind_result($alias, $name);
        $result = array();

        if($con->execute())
        {
            while($con->fetch()){
                $result[$alias]=$name;
            }
            $con->close();
            return $result;
        }
        else
            return false;
    
    }
}
