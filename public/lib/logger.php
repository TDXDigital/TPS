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
    private $usernameLog = "Anonamous";
    private $validLogLevel = ['debug','info','warn','error','exception'];
    private $currentLogLevels = ['debug','info','warn','error','exception'];
    private $logLevel = "info";
    private $startTime = NULL;
    private $endTime = NULL;
    
    private $data = array();
    
    public function __construct($username=NULL, $email=NULL, $logLevel=NULL, $ipv6=NULL,  
            $ipv4=NULL, $timezone=NULL) {
        if(!is_null($username)){
            $this->usernameLog = $username;
        }
        else if(isset($_SESSION['account'])){
            $this->usernameLog = $_SESSION['account'];
        }
        if(!is_null($logLevel)){
            $this->logLevel($logLevel);
        }
        elseif (isset($GLOBALS['logLevel'])) {
            $this->logLevel($GLOBALS['logLevel']);
        }
        else{
            $this->logLevel('info');
        }
        register_shutdown_function(array("\\TPS\\logger","fatalError"));
        parent::__construct();
    }
    /*
    public function __destruct() {
        return true;
    }
     * 
     */
    
    public function logLevel($level=NULL){
        if(!is_null($level)){
            if(in_array($level, $this->validLogLevel) || $level==FALSE){
                switch ($level) {
                    case 'debug':
                        $this->currentLogLevels = $this->validLogLevel;
                        break;
                    case 'info':
                        $this->currentLogLevels = array_diff(
                                $this->validLogLevel,
                                array('debug'));
                        break;
                    case 'warn':
                        $this->currentLogLevels = array_diff(
                                $this->validLogLevel,
                                array('debug','info'));
                        break;
                    case 'error':
                        $this->currentLogLevels = array_diff(
                                $this->validLogLevel,
                                array('debug','info','warn'));
                        break;
                    case 'exception':
                        $this->currentLogLevels = array_diff(
                                $this->validLogLevel,
                                array('debug','info','warn','error'));
                        break;
                    default:
                        $this->currentLogLevels = NULL; #off
                        break;
                }
                $this->logLevel = $level;
                $GLOBALS['logLevel'] = $level;
            }
            else{
                error_log("invalid logging level $level provided,"
                        . " please use one of the following: "
                        . json_encode($this->validLogLevel));
            }
        }
        return $this->logLevel;
    }
    
    static protected function traceCallingFile($limit=0){
        return debug_backtrace($options=DEBUG_BACKTRACE_IGNORE_ARGS, $limit);
    }
    
    static public function fatalError(){
        $error = error_get_last();
        if($error['type'] === E_ERROR){
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 0);
            foreach ($trace as $value) {
                error_log(json_encode($value));
            }
        }
    }
    
    /**
     * 
     * @param type $level
     * @param type $string
     * @param type $trace
     * @return string
     */
    static protected function formatPHPlogLine($level, $string, $trace=True){
        try{
            if($trace){
                $btrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 0);
                $traceStrFmt = " [%s, %s L%i] in %s]";
                $traceStr = sprintf($traceStrFmt, date("Y-m-d H:i:s"), 
                        $btrace[3]['file'], $btrace[3]['line'],
                        $btrace[3]['function']);
            }
            else{
                $traceStr = " ";
            }
            $format = '%1$s : %3$s %2$s';
            $format = sprintf($format, $level, $string, $traceStr);
            return $format;
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
            if(!$this->mysqli->errno){
                return true;
            }
            else{
                error_log($this->formatPHPlogLine(
                                "ERROR", $this->mysqli->error, TRUE));
            }
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
    
    public function debug($event, $result=NULL, $source=NULL){
        try{
            if(in_array('debug',$this->currentLogLevels)){
                $this->saveInDatabase("debug",$event,$source,$result);
            }
        } catch (Exception $ex) {
            error_log("Exception occured in logging, $ex");
        }
    }
    
    public function info($event, $result=NULL, $source=NULL){
        try{
            if(in_array('info',$this->currentLogLevels)){
                $this->saveInDatabase("info",$event,$source,$result);
            }
        } catch (Exception $ex) {
            error_log("Exception occured in logging, $ex");
        }
    }
    
    public function warn($event, $result=NULL, $source=NULL){
        try{
            if(in_array('warn',$this->currentLogLevels)){
                $this->saveInDatabase("warn",$event,$source,$result);
            }
        } catch (Exception $ex) {
            error_log("Exception occured in logging, $ex");
        }
    }
    
    public function error($event, $result=NULL, $source=NULL){
        try{
            if(in_array('error',$this->currentLogLevels)){
                $this->saveInDatabase("error",$event,$source,$result);
            }
        } catch (Exception $ex) {
            error_log("Exception occured in logging, $ex");
        }
    }
    
    public function exception($event, $result=NULL, $source=NULL){
        try{
            if(in_array('exception',$this->currentLogLevels)){
                if(get_class($event) == "Exception"){
                    $exception = $event;
                    $event = $exception->getMessage();
                }
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
            //$this->startTime = Null;
            return true;
        }
        else{
            $this->endTime = NULL;
            return false;
        }
    }
    
    public function timerDuration(){
        if(is_null($this->endTime)){
            $this->stopTimer();
        }
        return ($this->endTime) - ($this->startTime);
    }
}