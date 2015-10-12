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
            
    function __construct($callsign=null) {
        parent::__construct();
        if(!is_null($callsign)){
            $this->setupParams($callsign);
        }
    }
    
    public function setupParams($callsign){
        $callsign = strtoupper($callsign);
        $this->setStation($callsign);
        $stations = $this->getStation($callsign);
        $params = $stations[$callsign];
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
        }
        else{
            return FALSE;
        }
    }
    
    public function getStation($callsign,$page=1,$maxResult=50){
        $this->sanitizePagination($page,$maxResult);
        if($con = $this->mysqli->prepare("SELECT stationname,Designation,"
                . "frequency,website,address,boothphone,directorphone,"
                . "ST_DefaultSort,ST_PLLG,ST_ForceComposer,ST_ForceArtist,"
                . "ST_ForceAlbum,ST_ColorFail,ST_ColorPass,ST_PLRG,"
                . "ST_DispCount,ST_ColorNote,managerphone,ST_ADSH,ST_PSAH,"
                . "timezone FROM station where callsign=? order by callsign "
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
                        "timezone"=>$timezone,
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
    
    /**
     * Changes Station TimeZone in DB and Class
     * @param type $tz
     * @return boolean
     */
    public function setStationTimeZone($tz){
        $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
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
        return $this->setPlaylistLiveGrouping("1");
    }
    
    public function playlistLiveGroupingOff(){
        return $this->setPlaylistLiveGrouping("0");
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
    
}
