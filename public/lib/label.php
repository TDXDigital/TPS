<?php

/*
 * The MIT License
 *
 * Copyright 2015 J.oliver.
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

require_once "tps.php";
require_once "logger.php";

/**
 * Description of label
 *
 * @author J.oliver
 */
class label extends TPS{
    private $id;
    private $name;
    private $nameAlias;
    private $location;
    private $Size;
    private $updated;
    private $verified;
    private $parentComapny;
    public $log = Null;

    public function __construct($id, $enableDbReporting = FALSE) {
        parent::__construct($enableDbReporting);
        $this->id = $id;
        $this->fetch();
        $this->rootParentCompany();
        $this->log = new \TPS\logger();
    }
    
    private function formatTreeReturn(&$root, &$subsidiaries,
                                      $nodeStructure=False, &$result = array()){
        
        if(!$nodeStructure){
            $result[$root->id] = array("name" => $root->name,
                "alias" => $root->nameAlias,
                "subsidiaries" => $subsidiaries);
        }
        else{
            $text =array( 
                "name"=> $root->name,
                "alias" => $root->nameAlias?:$root->name
                    );
            $storage = array(
                "text"=> $text,
                "children" => $subsidiaries
            );
            if(sizeof($subsidiaries)<1 || sizeof($subsidiaries[0])<1){
                unset($storage["children"]);
            }
            array_push($result, $storage);
        }
        return $result;
    }
    
    public function companyTree($nodeStructure=False){
        $root = new \TPS\label($this->rootParentCompany());
        $subsidiaries = $root->subsidiariesTree($nodeStructure);
        $result = $this->formatTreeReturn($root, $subsidiaries, $nodeStructure);
        if($nodeStructure && sizeof($result>0)){
            return $result[0];
        }
        else{
            return $result;
        }
    }
    
    public function rootParentCompany(){
        if($this->parentComapny){
            $label = new \TPS\label($this->parentComapny);
            $parentParent = $label->rootParentCompany();
            return $parentParent;
        }
        else{
            return $this->id;
        }
    }
    
    public function subsidiariesTree($nodeStructure=False){
        $subsidiaries = $this->subsidiaries();
        $result = array();
        foreach ($subsidiaries as $id => $name) {
            $label = new \TPS\label($id);
            $subsidiaries = $label->subsidiariesTree($nodeStructure);
            $this->formatTreeReturn($label, $subsidiaries, 
                    $nodeStructure, $result);
        }
        return $result;
    }

    public function subsidiaries(){
        $stmt = $this->mysqli->prepare(
                "SELECT LabelNumber, Name from recordlabel "
                . "where parentCompany=? order by LabelNumber asc"
                );
        $stmt->bind_param("i",$this->id);
        $stmt->execute();
        $stmt->bind_result($idArray, $nameArray);
        $result = array();
        while($stmt->fetch()){
            $result[$idArray] = $nameArray;
        }
        $stmt->close();
        return $result;
    }
    
    private function setParameter($param, $value){
        $stmt = $this->mysqli->prepare(
                "UPDATE recordlabel SET $value=? WHERE LabelNumber=?");
        if ($stmt === false) {
            trigger_error($this->mysqli->error, E_USER_ERROR);
        }
        $stmt->bind_param("si",$param,$this->id);
        $status = $stmt->execute();
        if($status === false){
            trigger_error($this->mysqli->error, E_USER_ERROR);
            return false;
        }
        else{
            return true;
        }
    }
    
    public function setName($param) {
        $result = true;
        if($param!=$this->name){
            $result = $this->setParameter($param,"Name");
            if($result){
                $this->name = $param;
            }
        }
        return $result;
    }
    
    public function setLocation($param) {
        $result = true;
        if($param!=$this->location){
            $result = $this->setParameter($param,"Location");
            if($result){
                $this->location = $param;
            }
        }
        return $result;
    }
    
    public function setSize($param) {
        $result = true;
        if($param!=$this->Size){
            $result = $this->setParameter($param,"Size");
            if($result){
                $this->Size = $param;
            }
        }
        return $result;
    }
    
    public function setAlias($param) {
        if(!is_null($param) && ($param==$this->id || $param==$this->parentComapny)){
            trigger_error("Cannot set label alias to itself",E_USER_ERROR);
            return False;
        }
        $result = true;
        if($param!=$this->nameAlias){
            $result = $this->setParameter($param,"name_alias_duplicate");
            if($result){
                $this->nameAlias = $param;
            }
        }
        return $result;
    }
    
    public function setVerified($param) {
        $result = true;
        $param = $param?TRUE:FALSE;
        if($param!=$this->verified){
            $result = $this->setParameter($param?"TRUE":"FALSE","verified");
            if($result){
                $this->name = $param;
            }
        }
        return $result;
    }
    
    public function setParentCompany($param) {
        $result = true;
        if(!is_null($param) && ($param==$this->id || $param==$this->nameAlias)){
            trigger_error("Cannot set label parent company to itself"
                    ,E_USER_ERROR);
            return False;
        }
        if($param!=$this->parentComapny){
            $result = $this->setParameter($param,"parentCompany");
            if($result){
                $this->parentComapny = $param;
            }
        }
        return $result;
    }
        
    public function fetch() {
        $stmt = $this->mysqli->prepare(
                "SELECT LabelNumber, Name, Location, Size, name_alias_duplicate,"
                . "updated, verified, parentCompany from recordlabel "
                . "where LabelNumber=?"
                );
        $stmt->bind_param("i",$this->id);
        $stmt->execute();
        $stmt->bind_result($id, $name, $location, $size, $alias, 
                $updated ,$verified, $parent);
        while($stmt->fetch()){
            if(!is_null($id)){
                $this->id = $id;
                $this->name = $name;
                $this->location = $location;
                $this->Size = $size;
                $this->nameAlias = $alias;
                if($verified=="TRUE"){
                    $this->verified = TRUE;
                }
                else{
                    $this->verified = FALSE;
                }
                $this->updated = $updated;
                $this->parentComapny = $parent;
            }
        }
        $stmt->close();
        $result = array(
            "id" => $this->id,
            "name" => $this->name,
            "location" => $this->location,
            "size" => $this->Size,
            "alias" => $this->nameAlias,
            "verified" => $this->verified,
            "updated" => $this->updated,
            "parentCompany" => $this->parentComapny,
        );
        return $result;
    }
    
    static function nameSearch($name, $includeAlias=True) {
        if(isset($this)){
            if(!$this->mysqli){
                parent::__construct();
            }
        }
        else{
            $self = new \TPS\TPS();
        }
        $stmt = $self->mysqli->prepare(
                "SELECT LabelNumber, Name, name_alias_duplicate "
                . "from recordlabel where Name like ?"
                );
        $stmt->bind_param("s",$name);
        $stmt->execute();
        $stmt->bind_result($idArray, $nameArray, $alias);
        $result = array();
        while($stmt->fetch()){
            if($includeAlias || (!$includeAlias && is_null($alias))){
                $result[$idArray] = $nameArray;
            }
        }
        $stmt->close();
        return $result;
    }
}
