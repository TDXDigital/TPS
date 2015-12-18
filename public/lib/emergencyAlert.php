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
class alert{
    public $polygon="";
    public $name="";
    public $title=array();
    public $provider="";
    public $updated="";
    public $image="";
    public $active = True;
    public $text=array();
    public $alertAuthority=array();
    public $expires="";
    public $id="";
    public $status="";
    public $areas=array();
    public $hred="";
    
    public function __construct() {
        $this->expires = new \DateTime();
        $this->updated = new \DateTime();
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
}


/**
 * Description of emergencyAlert
 *
 * @author James Oliver <support@ckxu.com>
 */
class emergencyAlert {
    private $alerts = array();
    private $formatted = array();
    private $logger = Null;
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

    public function __construct($station, $sources=Null, $severity="all") {
        if($sources != Null && is_array($sources) ){
            $this->$providers = $sources;
        }
        $this->logger = new \TPS\logger();
    }
    
    public function locations($value){
        $this->location = $value;
    }
    
    private function parseAtomAlert(&$alert, &$alertObj){
        $idStr = $alert->id;
        $id = explode("/",$idStr);
        if(is_array($id) && sizeof($id)){
            $id = end($id);
        }
        else{
            
        }
        preg_match("/(?<=Expires:\s)([\d:\-T]+)/", #"/(?<=Expires:\s)(.+)(?=\n)/", 
                $alert->summary, $matches);
        if(sizeof($matches)>0){
            try {
                if(sizeof($matches[0])>0){
                    $expires = new \DateTime($matches[0]);
                    $now = new\DateTime();
                    if($now > $expires){
                        # expired, no need to process
                        return true;
                    }
                    $alertObj->expires($expires);
                }
            } catch (Exception $exc) {
            }
        }
        $updated = new \DateTime($alert->updated);
        $alertObj->updated($updated);
        $title = $alert->title;
        $alertObj->title($title);
        $alertObj->alertAuthority($alert->author->name);
        
        preg_match("/(?<=Area:\s)([^\n\<]+)/", #"/(?<=Expires:\s)(.+)(?=\n)/", 
                $alert->summary, $areas);
        if(sizeof($areas)<1){
            $areas = $alert->summary;
            if(!in_array($this->location,$area)){
                // does not include given locations
                return FALSE;
            }
        }
        else{
            $areas = explode(',', $areas[0]);
            $validArea = False;
            foreach ($areas as $area){
                if($this->location == "*" || in_array($this->location,$area)){
                    // does not include given locations
                    $validArea = TRUE;
                    break;
                }
            }
            if(!$validArea){
                return True;
            }
        }
        
        preg_match("/(?<=Description:\s)([^\n\<]+)/", #"/(?<=Expires:\s)(.+)(?=\n)/", 
                $alert->summary, $description);
        if(sizeof($description)<1){
            $description = $alert->summary;
        }
        if(!is_string($description)){
            $alertObj->text($description[0]);
        }
        else{
            $alertObj->text($description);
        }
        
        if(key_exists($id,$this->alerts)){
            $previousAlert = $this->alerts[$id];
            $previousAlert->text($alert->text);
            $alertDate = new \DateTime($alert->updated);
            if($alertDate < $previousAlert->updated){
                return True;
            }
        }
        
        if(!sizeof($id)){
            array_push($this->alerts, $alertObj);
        }
        else{
            $this->alerts[(string)$id] = $alertObj;
        }
    }
    
    private function parseAtomAlerts($source, $logo){
        $xml = file_get_contents($source);
        $entries = new \SimpleXmlElement($xml);
        $entries->registerXPathNamespace('prefix', 'http://www.w3.org/2005/Atom');
        $results = $entries->xpath("//prefix:entry");
        foreach ($results as $entry) {
            $alertObj = new \TPS\alert();
            $alertObj->image($logo);
            $this->parseAtomAlert($entry, $alertObj);
        }
        
    }
    
    protected function checkAlert($provider, $data){
        if(!key_exists("feed",$data) || !key_exists("logo", $data)){
            throw new Exception("Missing key value from alert data");
        }
        $source = $data["feed"];
        $logo = $data["logo"];
        $type = "atom";
        if(key_exists("type", $data)){
            $type = strtolower($data['type']);
        }
        if($type == "atom"){
            try {
                $this->parseAtomAlerts($source, $logo);
            } catch (Exception $exc) {
                $exc->getTraceAsString();
            }
        }        
    }

    protected function checkAlerts($provider=Null){
        foreach ($this->providers as $key => $value) {
            if ($provider != Null && !in_array($key,$provider)){
                continue;
            }
            try {
                $this->checkAlert($key, $value);
            } catch (Exception $exc) {
                $trace = $exc->getTraceAsString();
                $this->logger->error("Check alerting partner failed", $trace);
            }
        }
    }
    
    protected function formatAlert($provider){
        
    }

    protected function formatAlerts(){
        $this->formatted = $this->alerts;
    }
    
    protected function printAlert($index){
        
    }
    
    protected function printAlerts(){
        
    }

    public function run(){
        $this->checkAlerts();
        $this->formatAlerts();
        if($this->format == "json"){
            return json_encode($this->formatted);
        }
        else{
            return $this->printAlerts();
        }
    }
}