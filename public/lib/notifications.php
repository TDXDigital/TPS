<?php

/*
 * The MIT License
 *
 * Copyright 2016 J.oliver.
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
 * Description of playlist
 *
 * @author J.oliver
 */
class notification extends station{
    //put your code here
    
    public function __construct(
            $enableDbReporting = FALSE, $requirePDO = FALSE, 
            $settingsTarget = NULL, $settingsPath = NULL) {
        parent::__construct($enableDbReporting, $requirePDO, $settingsTarget, 
                $settingsPath);
    }
    
    public function notifications($userName="*"){
        #@todo: expand to accept array
        $stmt = $this->db->prepare("SELECT * FROM notification "
                . "WHERE `userName`=:uname and `station`=:station");
        $stmt->bindParam(":userName", $userName);
        $stmt->bindParam(":station", $this->callsign);
        $result = array();
        if(!$stmt->execute()){
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                array_push($result[$row['notificationid']], $row);
            }
        }
        return $result;
    }
    
    public function setExpiry($playlistIds, $date){
       if(!is_array($playlistIds)){
            $playlistIds = array($playlistIds);
        }
        $param = NULL;
        $stmt = $this->db->prepare("UPDATE playlist SET `Expire`=:date"
            . " WHERE PlaylistId=:param");
        $stmt->bindParam(":param", $param);
        $stmt->bindParam(":date", $date);
        foreach($playlistIds as $param){
            if(!$stmt->execute()){
                throw new Exception($stmt->errorInfo());
            }
        }
        return true;
    }
    
    public function setStart($playlistIds, $date){
        if(!is_array($playlistIds)){
            $playlistIds = array($playlistIds);
        }
        $param = NULL;
        $stmt = $this->db->prepare("UPDATE playlist SET Activate=:date"
            . " WHERE PlaylistId=:param");
        $stmt->bindParam(":param", $param);
        $stmt->bindParam(":date", $date);
        foreach($playlistIds as $param){
            if(!$stmt->execute()){
                throw new Exception($stmt->errorInfo());
            }
        }
        return true;
    }
}
