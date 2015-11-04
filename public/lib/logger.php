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
    
    private $data = array();
    
    public function __construct($username=NULL, $email=NULL, $logLevel=NULL, $ipv6=NULL,  
            $ipv4=NULL, $timezone=NULL) {
        $this->usernameLog = $username;
        parent::__construct();
    }
    /*
    public function __destruct() {
        return true;
    }
     * 
     */
    
    public function warn($event, $result=NULL, $source=NULL){}
    public function error($event, $result=NULL, $source=NULL){}
    public function exception($event, $result=NULL, $source=NULL){}
    
    private function traceCallingFile($limit=2){
        return debug_backtrace($options=DEBUG_BACKTRACE_IGNORE_ARGS, $limit);
    }
    
    /**
     * 
     * @param type $level
     * @param type $string
     * @param type $trace
     * @return string
     */
    private function formatPHPlogLine($level, $string, $trace=True){
        if($trace){
            $btrace = $this->traceCallingFile()[1];
            $traceStrFmt = " [%s, %s L%i] in %s]";
            $traceStr = sprintf($traceStrFmt, date("Y-m-d H:i:s"), 
                    $btrace['file'], $btrace['line'], $btrace['function']);
        }
        else{
            $traceStr = " ";
        }
        $format = '%1$s:%3$s %2s';
        return sprintf($format, $level, $string, $traceStr);
    }
    
    /**
     * 
     * @param type $event
     * @param type $source
     * @param type $result
     * @return boolean
     */
    public function saveInDatabase($severity,$event, $source, $result){
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
}