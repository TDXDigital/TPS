<?php

/*
 * The MIT License
 *
 * Copyright 2015 James Oliver.
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
/**
 * Genre handles communications with the database
 * for retrieving 
 *
 * @author support@ckxu.com
 */
class genre extends TPS{
    private $callsign = null;
      
    public function __construct($callsign) {
        parent::__construct();
        $this->callsign = $callsign;
    }
    
    public function create($name, $govReq=0, $govReqPerc=0, $govReqType=1,
            $playslist=0, $playlistperc=0, $plType=1){
        try {
            $stmt = $this->db->prepare("INSERT INTO genre (genreid, cancon, "
                    . "playlist, canconperc, playlistperc, PlType, CCType, "
                    . "Station) VALUES (:name, :canCon, :playlist, :canConPerc,"
                    . " :plType, :playlistPerc :ccType, :station)");
            $this->db->beginTransaction(); 
            $stmt->bindParam(":name", $name, \PDO::PARAM_STR);
            $stmt->bindParam(":station", $this->callsign, \PDO::PARAM_STR);

            $stmt->bindParam(":canCon", $govReq);
            $stmt->bindParam(":canConPerc", $govReqPerc);
            $stmt->bindParam(":ccType", $govReqType);

            $stmt->bindParam(":playlist", $playslist);
            $stmt->bindParam(":playlistPerc", $playlistperc);
            $stmt->bindParam(":plType", $plType);
            
            $stmt->execute();
            $result = $this->db->lastInsertId()?:
                    $stmt->fetch(\PDO::FETCH_ASSOC)['genreid']; 
            $this->db->commit(); 
            $stmt = null;
            return $result;

        } catch (PDOException $exc) {
            $this->db->rollback(); 
            error_log(sprintf("PDO Exception, %s: %s"
                    ,$exc->getMessage(), $exc->getTraceAsString()));
            return FALSE;
        }

    }
    
    public function all(){
        $stmt = $this->db->prepare(
                "SELECT genreid, cancon, playlist, canconperc, playlistperc,"
                . " UID, CCType, PlType, Station, (SELECT count(programname)"
                . " FROM program WHERE program.genre=genre.genreid AND"
                . " program.active='1' AND LOWER(program.callsign) = "
                . "LOWER(genre.Station)) AS PGM_Count, (SELECT count(*)"
                . " FROM program where program.active='1' AND "
                . "LOWER(program.callsign) = LOWER(genre.Station)) AS Total"
                . ", (SELECT PGM_Count / Total) AS Percent FROM genre"
                . " WHERE LOWER(station) = LOWER(:callsign) order by "
                . "genreid asc");
        $stmt->bindParam(":callsign", $this->callsign, \PDO::PARAM_STR);
        $stmt->execute();
        $result = array();
        while(list($genreId, $govRec, $playlist, $govRecPerc, 
                $playlistPerc, $UID, $CcType, $PlType, $station,
                $activePrograms, $totalPrograms, $percentPrograms) = 
                $stmt->fetch( \PDO::FETCH_NUM )){
            $result[$genreId] = array(
                "governmentRequirements" => array(
                    "type" => $CcType,
                    "numeric" => $govRec,
                    "percentage" => $govRecPerc,
                ),
                "playlistRequirements" => array(
                    "type" => $PlType,
                    "numeric" => $playlist,
                    "percentage" => $playlistPerc,
                ),
                "statistics" => array(
                    "activePrograms" => $activePrograms,
                    "totalPrograms" => $totalPrograms,
                    "percentPrograms" => $percentPrograms
                ),
                "UID" => $UID,
                "station" => $station,
            );
        }
        $stmt = null; #close statement
        return $result;
    }
    
    public function get($id){
        $stmt = $this->db->prepare(
                "SELECT genreid, cancon, playlist, canconperc, playlistperc,"
                . " UID, CCType, PlType, Station, (SELECT count(programname)"
                . " FROM program WHERE program.genre=genre.genreid AND"
                . " program.active='1' AND LOWER(program.callsign) = "
                . "LOWER(genre.Station)) AS PGM_Count, (SELECT count(*)"
                . " FROM program where program.active='1' AND "
                . "LOWER(program.callsign) = LOWER(genre.Station)) AS Total"
                . ", (SELECT PGM_Count / Total) AS Percent FROM genre"
                . " WHERE LOWER(station) = LOWER(:callsign) "
                . "and genreId = :id order by "
                . "genreid asc");
        $stmt->bindParam(":callsign", $this->callsign, \PDO::PARAM_STR);
        $stmt->bindParam(":id", $id , \PDO::PARAM_INT);
        $stmt->execute();
        $result = array();
        while(list($genreId, $govRec, $playlist, $govRecPerc, 
                $playlistPerc, $UID, $CcType, $PlType, $station,
                $activePrograms, $totalPrograms, $percentPrograms) = 
                $stmt->fetch( \PDO::FETCH_NUM )){
            $result[$genreId] = array(
                "governmentRequirements" => array(
                    "type" => $CcType,
                    "numeric" => $govRec,
                    "percentage" => $govRecPerc,
                ),
                "playlistRequirements" => array(
                    "type" => $PlType,
                    "numeric" => $playlist,
                    "percentage" => $playlistPerc,
                ),
                "statistics" => array(
                    "activePrograms" => $activePrograms,
                    "totalPrograms" => $totalPrograms,
                    "percentPrograms" => $percentPrograms
                ),
                "UID" => $UID,
                "station" => $station,
            );
        }
        $stmt = null; #close statement
        return $result;
    }
}
