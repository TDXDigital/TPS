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

require 'logger.php';
require 'station.php';
class alert{
    public $polygon=array();
    public $title=array();
    public $provider="";
    public $updated="";
    public $image="";
    public $active = null;
    public $text=array();
    public $alertAuthority=array();
    public $expires="";
    public $id="";
    public $severity=""; //Extreme, Severe, Moderate, Minor, Unknown
    public $severityLevel=Null;
    public $areas=array();
    public $href;
    public $language = array();
    public $category; // Geo, Met, Safety, Rescue, Fire, Health, Env, Transport, Infra, CBRNE, Other
    public $certainty; //Observed, Likely, Possible, Unlikely, Unknown
    public $eventCode;
    public $type;
    public $status;
    public $urgency; // Future, Expected, Unknown, Past, Immediate, Unknown
    protected $exactMatchLocation = False;

    public function __construct() {
        $this->expires = new \DateTime();
        $this->updated = new \DateTime();
    }

    public function exactMatchLocation($bool){
        assert(is_bool($bool), "non boolean value provided");
        $this->exactMatchLocation = $bool;
    }

    public function image($value=null){
        if($value){
            $this->image = $value;
        }
        return $this->image;
    }

    public function alertAuthority($value=null){
        if($value){
            if(is_array($value)){
                $value = array_pop($value);
            }
            array_push($this->alertAuthority,$value);
            #$this->title = $value;
        }
        return $this->alertAuthority;
    }

    public function areas($value=null){
        if($value){
            $this->areas .= $value;
        }
        return $this->areas;
    }

    public function expires($value=null){
        if($value){
            $this->expires = $value;
        }
        return $this->expires;
    }

    public function name($value=null){
        if($value){
            $this->name = $value;
        }
        return $this->name;
    }

    public function title($value=null){
        if($value){
            if(is_array($value)){
                $value = array_pop($value);
            }
            array_push($this->title,$value);
            #$this->title = $value;
        }
        return $this->title;
    }

    public function language($value=null){
        if($value){
            if(is_array($value)){
                $value = array_pop($value);
            }
            array_push($this->language,$value);
            #$this->title = $value;
        }
        return $this->language;
    }

    public function updated($value=null){
        if($value){
            $this->updated = $value;
        }
        return $this->updated;
    }

    public function text($value=null){
        if($value){
            if(is_array($value)){
                $value = array_pop($value);
            }
            array_push($this->text,$value);
            #$this->title = $value;
        }
        return $this->text;
    }

    public function id($value=null){
        if($value){
            $this->id = $value;
        }
        return $this->id;
    }

    protected function setActive(){
        $this->active = true;
        if(preg_match("/(?<=\s)(in effect)/", end($this->title))){
            $this->active = true;
        }
        elseif (preg_match("/(?<=\s)(ended)/", end($this->title))) {
            $this->active = false;
        }
        if(strtolower($this->urgency) == "past"){
            $this->active = false;
        }
    }
}

class atomAlert extends alert{

    protected function setAlertAlertSeverity(){
        $result = "Warning";
        if($this->severity){
            return $this->severity;
        }
        $term = end($this->title);
        if(strpos($term,'Test') !== FALSE){
            $result = "Test";
        }
        elseif(strpos($term,'information')!==FALSE
            || strpos($term,"advisory")!==FALSE
            || strpos($term,"special weather")!==FALSE
            || strpos($term,"bulletin météorologique spécial")!==FALSE){
            $result = "Information";
        }
        elseif(strpos($term, 'watch')!==FALSE){
            $result = "Watch";
        }
        // Otherwise return Warning (Unknown reason to downgrade)

        $this->severity = $result;
        return $this->severity;
    }

    protected function parse(&$alert, $locations, $stnTimeZone='UTC'){
        foreach($alert->children('http://www.georss.org/georss') as $geo){
            array_push($this->polygon, end($geo));
        }
        $idStr = $alert->id;
        $id = explode("/",$idStr);
        if(is_array($id) && sizeof($id)){
            $id = end($id);
        }
        else{

        }
        $this->id = $id;
        preg_match("/(?<=Expires:\s)([\d:\-T]+)/", #"/(?<=Expires:\s)(.+)(?=\n)/",
            $alert->summary, $matches);
        if(sizeof($matches)>0){
            try {
                if(sizeof($matches[0])>0){
                    $expires = new \DateTime($matches[0]);
                    $now = new \DateTime($stnTimeZone);
                    if($now > $expires){
                        # expired, no need to process
                        return false;
                        //return true;
                    }
                    $this->expires($expires);
                }
            } catch (Exception $exc) {
            }
        }
        $updated = new \DateTime($alert->updated);
        $this->updated($updated);
        $this->title((string)$alert->title);
        $this->alertAuthority((string)$alert->author->name);
        $this->setAlertAlertSeverity();
        $this->href = (string)$alert->link->attributes()->href;

        foreach ($alert->category as $category){
            // special Pelmorex feed info
            foreach($category->attributes() as $valueRaw){
                $raw = (string)$valueRaw[0];
                if($raw == ""){
                    continue;
                }
                $value = explode("=", $raw);
                $key = array_shift($value);
                if($key == "language"){
                    $this->language(end($value));
                }
                elseif ($key == "category") {
                    $this->category = end($value);
                }
                elseif ($key == "msgType") {
                    $this->type = end($value);
                }
                elseif ($key == "status") {
                    $this->status = end($value);
                }
                elseif ($key == "severity") {
                    $this->severityLevel = end($value);
                }
                elseif ($key == "urgency") {
                    $this->urgency = end($value);
                }
            }
        }

        preg_match("/(?<=Area:\s)([^\n\<]+)/", #"/(?<=Expires:\s)(.+)(?=\n)/",
            $alert->summary, $areas);
        $stationLocations = explode(",", $locations);
        if(!is_array($locations)){
            $locations = array($locations);
        }
        $alertValidAreas = array_merge($stationLocations, $locations);
        //$match = array_shift($alertValidAreas);
        if(sizeof($areas)<1){
            $areas = $alert->summary;
            if(!in_array($alertValidAreas, $areas)){
                // does not include given locations
                return FALSE;
            }
        }
        else{
            $areas = explode(',', $areas[0]);
            $validArea = False;
            foreach ($alertValidAreas as $location){
                $location = trim($location);
                if($location == NULL){
                    // if no station wide locations are defined a null
                    // entry will exist, skip it when we get here
                    continue;
                }
                foreach ($areas as $area) {
                    $area = trim($area);
                    if(strpos($area, $location)!==false || $location == "*"){

                        if($this->exactMatchLocation && $area != $location){
                            continue;
                        }
                        // does not include given locations
                        $validArea = TRUE;
                        break;
                    }
                }
            }
            if(!$validArea){
                return False;
            }
        }
        $this->areas = $areas;

        preg_match("/(?<=Description:\s)([^\n\<]+)/", #"/(?<=Expires:\s)(.+)(?=\n)/",
            $alert->summary, $description);
        if(sizeof($description)<1){
            $description = "";
        }
        if(!is_string($description)){
            $this->text(end($description));
        }
        else{
            $this->text($description);
        }
        $this->setActive();
        return True;
    }

    public static function parseAtomAlerts($source, $locations, $provider, $logo, $exactLocation=False,
                                           $timeZone='UTC'){
        //$xml = file_get_contents($source);
        //$entries = new \SimpleXmlElement($xml);
        $entries = simplexml_load_file($source);
        $entries->registerXPathNamespace('prefix', 'http://www.w3.org/2005/Atom');
        $results = $entries->xpath("//prefix:entry");
        $alerts = array();
        foreach ($results as $entry) {
            $alertObj = new \TPS\atomAlert();
            $alertObj->exactMatchLocation($exactLocation);
            $alertObj->image($logo);
            $alertObj->provider = $provider;
            if($alertObj->parse($entry, $locations, $timeZone)) {
                array_push($alerts, $alertObj);
            }
        }
        return $alerts;
    }
}


/**
 * Description of emergencyAlert
 *
 * @author James Oliver <support@ckxu.com>
 */
class emergencyAlert extends station{
    private $alerts = array();
    private $formatted = array();
    private $expiredAlerts = array();
    private $providers = array(
        "AEMA" => array(
            "feed" => "http://www.emergencyalert.alberta.ca/aeapublic/feed.atom",
            "logo" => "/images/AEMA.png",
            ),
        "NAAD/NPAS" => array(
            "feed" => "http://rss.naad-adna.pelmorex.com/",
            "logo" => "/images/NPAS.png",
            ),
    );
    public $location = "*";
    public $format = "json";
    private $exactMatchLocation = False;

    public function __construct($station=Null, $locations=Null,
            $format=Null, $sources=Null) {
        parent::__construct($station);
        if($sources != Null && is_array($sources) ){
            $this->providers = $sources;
        }
        $this->format = $format;
        $this->location = explode(",", $locations);
        $this->providers = $this->getAlertProviders();
        //foreach($alertProviders as $name => $values){
        //    $this->location .= explode(',',$values['locations']);
        //}
    }

    private function setParams(&$alert,&$previous,$alertDate){
        if(is_string($previous)){
            $previous = $this->alerts[$previous];
            assert(sizeof($previous)>0,
                    "Could not retrieve previous alert");
        }
        $previous->title($alert->title);
        $previous->text($alert->text);
        $previous->alertAuthority($alert->alertAuthority());
        $previous->href = $alert->href;
        $previous->language($alert->language());
        $previous->areas = array_merge($previous->areas,$alert->areas);
        if($alertDate < $previous->updated){
            return True;
        }
        return True;
    }

    public function locations($value){
        $this->location = $value;
    }

    public function exactMatchLocation($bool){
        assert(is_bool($bool), "non boolean value provided");
        $this->exactMatchLocation = $bool;
    }

    protected function checkAlert($provider, $data, $timeZone='UTC'){
        if(!key_exists("feed",$data) || !key_exists("logo", $data)){
            throw new Exception("Missing key value from alert data");
        }
        $source = $data["feed"];
        $logo = $data["logo"];
        $locations = $data["locations"];
        $type = "atom";
        if(key_exists("type", $data)){
            $type = strtolower($data['type']);
        }
        if($type == "atom"){
            try {
                $x = atomAlert::parseAtomAlerts($source, $locations, $provider, $logo, False, $this->timezone);
                $alerts = array();
                array_map(function($t) use (&$alerts){
                    if(!array_key_exists($t->id, $alerts)){
                        $alerts[$t->id] = array();
                    }
                    array_push($alerts[$t->id], $t);
                },$x);
                usort($x, function($a,$b){return $a->expires > $b->expires;});
                $this->alerts = $x;
            } catch (Exception $exc) {
                $exc->getTraceAsString();
            }
        }
    }

    protected function checkAlerts($provider=Null, $timeZone='UTC'){
        foreach ($this->providers as $key => $value) {
            if ($provider != Null && !in_array($key,$provider)){
                continue;
            }
            try {
                $this->checkAlert($key, $value, $timeZone);
            } catch (Exception $exc) {
                $trace = $exc->getTraceAsString();
                $this->logger->error("Check alerting partner failed", $trace);
            }
        }
    }

    protected function formatAlert($provider){

    }

    protected function formatAlerts(){
    }

    protected function printAlert($alert){
        $html = "";
        switch (strtolower($alert->severity)) {
            case "test":
                $html .= "<div class=\"ui-state-highlight\" style=\"background-color:green\">";
                break;
            case "warning":
                $html .= "<div class=\"ui-state-error\">";
                break;
            case "watch":
                $html .= "<div class=\"ui-state-highlight\">";
                break;

            default:
                $html .= "<div class=\"ui-state-highlight\" style=\"background-color:grey\">";
                break;
        }
        $html .= "<span class='emergency_logo'><img src='"
                .addcslashes($alert->image,"\"")."'/></span>";
        $html .= "<span class='alert_info'><strong><a href=\""
                .addcslashes($alert->href?:"#","\"").
                "\" target=\"_blank\">".addcslashes($alert->title[0],"\"")
                ." / ".addcslashes($alert->title[1],"\"").
                "</a></strong><p>";
        foreach($alert->text as $key=>$text){
            $text = str_split($text,200);
            $html .= addcslashes(strtoupper($alert->language[$key]),"\"") . ": "
                    .addcslashes(array_shift($text),"\"");
            if(sizeof($text)>0){
                $html .= "&hellip;";
            }
            $html.="</br>";
        }
        $html .= "<span>Areas: " . implode(", ",$alert->areas). "</span>";
        $html .= "</p>";
        $html .= "</span>";
        $html .= "</div>";
        return $html;
    }

    protected function printAlerts(){
        $html = "<style>.emergency_logo{
            background-color:white;
            display: inline-block;
            float: left;
            width: 120px;
            /*height: 100px;*/
            max-height: 100px;
            max-width: 100px;
        }
        .emergency_logo img{
            max-height: 100px;
            max-width: 100px;
        }
        .alert_info{
            display: inline-block;
            /*float: left;*/
            padding: 0 0 0 0;
            margin: 0 0 0 0;
            width: 85%;
            height:100%;
            min-height: 100px;
            /*rgin-left:110px;*/
        }
        .alert_info a{
            font-size: large;
            color: lightgrey;
        }
        </style>";
        $i = 0;
        foreach($this->alerts as $alert){
            if($i>0){
                $html .= "<hr/>";
            }
            $html .= $this->printAlert($alert);
            $i++;
        }
        print $html;
    }

    private function removeOldUpdates(){
        $duplicates = array();
        foreach($this->alerts as $k1=>$a1){
            if(in_array($k1, $duplicates)){
                continue;
            }
            foreach($this->alerts as $k2=>$a2){
                if($a1->areas == $a2->areas && $k1!=$k2){
                    // the alerts SHOULD be in order
                    if($a1->alertAuthority == $a2->alertAuthority
                            && $a1->provider == $a1->provider){
                        if($a1->active == false && $a2->active == true){
                            // alert ended;
                            $duplicates[$a1->id] = false;
                        }
                        $duplicates[$a2->id] = true;
                    }
                }
            }
        }
        foreach($duplicates as $key=>$value){
            if($value){
                unset($this->alerts[$key]);
            }
        }
        foreach ($this->alerts as $k1=>$a1) {
            if (strtolower($a1->urgency) == "past") {
                unset($this->alerts[$k1]);
            }
        }
    }

    public function run(){
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $this->checkAlerts();
        $this->removeOldUpdates();
        if(sizeof($this->alerts)){
            $this->log->info(sizeof($this->alerts)." found for "
                    .$this->callsign.", sent to ".$ip);
        }
        foreach ($this->alerts as $key => $value) {
            $this->log->info($value->provider." alert '$key' provided to ".$ip);
        }
        if($this->format == "json"){
            return json_encode($this->alerts);
        }
        else{
            return $this->printAlerts();
        }
    }
}
