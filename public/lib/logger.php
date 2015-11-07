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

require_once 'tps.php';
class logger extends TPS{
    private $ipv4 = NULL;
    private $ipv6 = NULL;
    private $timezone = NULL;
    private $email = NULL;
    private $usernameLog = NULL;
    private $validLogLevel = ['debug','info','warn','error','exception'];
    private $logLevel = "info";
    private $startTime = NULL;
    private $endTime = NULL;
    
    private $data = array();
    
    public function __construct($username=NULL, $email=NULL, $logLevel=NULL, $ipv6=NULL,  
            $ipv4=NULL, $timezone=NULL) {
        $this->usernameLog = $username;
        register_shutdown_function(array("\\TPS\\logger","fatalError"));
        parent::__construct();
    }
    /*
    public function __destruct() {
        return true;
    }
     * 
     */
    
    static private function traceCallingFile($limit=0){
        return debug_backtrace($options=DEBUG_BACKTRACE_IGNORE_ARGS, $limit);
    }
    
    static public function fatalError(){
        error_log(self::formatPHPlogLine("Exception","Fatal error encountered"));
    }
    
    /**
     * 
     * @param type $level
     * @param type $string
     * @param type $trace
     * @return string
     */
    static private function formatPHPlogLine($level, $string, $trace=True){
        try{
            if($trace){
                $btrace = self::traceCallingFile();
                $traceStrFmt = " [%s, %s L%i] in %s]";
                $traceStr = sprintf($traceStrFmt, date("Y-m-d H:i:s"), 
                        $btrace[0]['file'], $btrace[0]['line'], $btrace[0]['function']);
            }
            else{
                $traceStr = " ";
            }
            $format = '%1$s:%3$s %2s';
            return sprintf($format, $level, $string, $traceStr);
        }
        catch(Exception $ex){
            return "Exception $ex";
        }
    }
    
    /**
     * 
     * @param type $event
     * @param type $source
     * @param type $result
     * @return boolean
     */
    public function saveInDatabase($severity,$event, $source, $result){
        if(!$this->mysqli){
            error_log($this->formatPHPlogLine("error","mysqli not yet defined:"
                    . "$event"));
            return false;
        }
        $con = $this->mysqli->prepare("INSERT INTO eventlog "
                . "(`user`,`event`,`source`,`result`,`severity`) VALUES "
                . "(?,?,?,?,?)");
        if($con){
            try{
                $con->bind_param("sssss",
                    $this->usernameLog,$event, $source, $result,$severity);
                $con->execute();
            } catch (Exception $ex) {
                error_log($this->formatPHPlogLine(
                        "ERROR",$this->mysqli->error));
            }
            return true;
        }
        else{
            error_log($this->formatPHPlogLine("ERROR",$this->mysqli->error));
            trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
            return false;
        }
    }
    
    public function info($event, $result=NULL, $source=NULL){
        try{
            if(in_array(strtolower($this->logLevel), 
                    ['info','warn','error','exception'])){
                $this->saveInDatabase("info",$event,$source,$result);
            }
        } catch (Exception $ex) {
            error_log("Exception occured in logging, $ex");
        }
    }
    
    public function warn($event, $result=NULL, $source=NULL){
        try{
            if(in_array(strtolower($this->logLevel), 
                    ['info','warn','error','exception'])){
                $this->saveInDatabase("warn",$event,$source,$result);
            }
        } catch (Exception $ex) {
            error_log("Exception occured in logging, $ex");
        }
    }
    
    public function error($event, $result=NULL, $source=NULL){
        try{
            if(in_array(strtolower($this->logLevel), 
                    ['info','warn','error','exception'])){
                $this->saveInDatabase("error",$event,$source,$result);
            }
        } catch (Exception $ex) {
            error_log("Exception occured in logging, $ex");
        }
    }
    
    public function exception($event, $result=NULL, $source=NULL){
        try{
            if(in_array(strtolower($this->logLevel), 
                    ['exception'])){
                $this->saveInDatabase("exception",$event,$source,$result);
            }
        } catch (Exception $ex) {
            error_log("Exception occured in logging, $ex");
        }
    }
    /*
    public function __sleep() {
        return array('ipv4', 'ipv4', 'timezone', 'email', 'username', 'logLevel');
    }
    
    public function __wakeup() {
        ;
    }*/
    
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
    
    public function startTimer(){
        if($this->startTime === NULL){
            $this->startTime = microtime(true); 
            $this->endTime = NULL;
            return true;
        }
        else{
            $this->startTime = microtime(true);
            $this->endTime = NULL;
        }
    }
    
    public function stopTimer(){
        if($this->startTime != null){
            $this->endTime = microtime(true); 
            $this->startTime = Null;
            return true;
        }
        else{
            $this->endTime = NULL;
            return false;
        }
    }
    
    public function timerDuration(){
        return $this->endTime - $this->startTime;
    }
}