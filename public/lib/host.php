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

require_once 'station.php';
/**
 * Genre handles communications with the database
 * for retrieving 
 *
 * @author support@ckxu.com
 */
class host extends station{
    public function __construct($callsign) {
        parent::__construct($callsign);
    }
    
    public function createHost($alias, $djname, $active, $years, $weight=1, $emailBlock=NULL,
            $memberRef=NULL, $GUID=NULL){
        try {
            $stmt = $this->db->prepare("REPLACE INTO dj (Alias, djname, active,"
                    . " years, weight, email_block, member_ref, GUID"
                    . ") VALUES (:alias, :djname, :active, :weight, :years,"
                    . " :emailBlock, :memberRef, :guid)");
            $stmt->bindParam(":alias", $alias, \PDO::PARAM_STR);
            //$stmt->bindParam(":station", $this->callsign, \PDO::PARAM_STR);
            $stmt->bindParam(":djname", $djname);
            $stmt->bindParam(":active", $active);
            $stmt->bindParam(":weight", $weight);
            $stmt->bindParam(":years", $years);
            $stmt->bindParam(":emailBlock", $emailBlock);
            $stmt->bindParam(":memberRef", $member_ref);
            $stmt->bindParam(":guid", $GUID);
            
            $stmt->execute(); 
            //$id = $this->db->lastInsertId();
            /*$genre = FALSE;
            $query = $this->db->prepare("SELECT genreid FROM genre WHERE UID=? or genreid=?");
            $query->execute(array($id,$name));
            $genre = $query->fetchColumn();
            if(!$genre){
                throw new \Exception("Genre Not Created");
            }
            $stmt = null;
            return $genre;*/
            return TRUE;

        } catch (PDOException $exc) {
            $this->db->rollback(); 
            error_log(sprintf("PDO Exception, %s: %s"
                    ,$exc->getMessage(), $exc->getTraceAsString()));
            return FALSE;
        }

    }
    
    /***
     * @todo WIP
     */
    public function change($name, $UID, $govReq=0, $govReqPerc=0, $govReqType=1,
            $playslist=0, $playlistperc=0, $plType=1, 
            $femcon=0, $femconType=1, $femconPerc=0, $color=Null){
        try {
            $stmt = $this->db->prepare("UPDATE genre SET genreid=:name,"
                    . " cancon=:canCon, playlist=:playlist,"
                    . " canconperc=:canConPerc, playlistperc=:playlistPerc,"
                    . " PlType=:plType, CCType=:ccType, Station=:station,"
                    . " femcon=:femcon, femconperc=:femconPerc,"
                    . " FcType=:femconType, colorPrimary=:colorPrimary"
                    . " WHERE UID=:UID");
            $stmt->bindParam(":name", $name, \PDO::PARAM_STR);
            $stmt->bindParam(":station", $this->callsign, \PDO::PARAM_STR);
            $stmt->bindParam(":colorPrimary", $color);

            $stmt->bindParam(":canCon", $govReq);
            $stmt->bindParam(":canConPerc", $govReqPerc);
            $stmt->bindParam(":ccType", $govReqType);

            $stmt->bindParam(":playlist", $playslist);
            $stmt->bindParam(":playlistPerc", $playlistperc);
            $stmt->bindParam(":plType", $plType);
            
            $stmt->bindParam(":femcon", $femcon);
            $stmt->bindParam(":femconType", $femconType);
            $stmt->bindParam(":femconPerc", $femconPerc);
            $stmt->bindParam(":UID", $UID);
            
            $stmt->execute(); 
            $id = $this->db->lastInsertId();
            $genre = FALSE;
            $query = $this->db->prepare("SELECT genreid FROM genre WHERE UID=? or genreid=?");
            $query->execute(array($id,$name));
            $genre = $query->fetchColumn();
            if(!$genre){
                throw new \Exception("Genre Not Created");
            }
            $stmt = null;
            return $genre;

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
                . " UID, CCType, PlType, femcon, femconPerc, FcType,"
                . " colorPrimary, Station, (SELECT count(programname)"
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
                $playlistPerc, $UID, $CcType, $PlType, $femcon, $femconPerc,
                $femconType, $colorPrimary, $station,
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
                "femconRequirements" => array(
                    "type" => $femconType,
                    "numeric" => $femcon,
                    "percentage" => $femconPerc,
                ),
                "statistics" => array(
                    "activePrograms" => $activePrograms,
                    "totalPrograms" => $totalPrograms,
                    "percentPrograms" => $percentPrograms
                ),
                "UID" => $UID,
                "colorPrimary" => $colorPrimary,
                "station" => $station,
            );
        }
        $stmt = null; #close statement
        return $result;
    }
    

    //retrn the dj info
    public function get($alias){
        $result = array();
        $con = $this->mysqli->prepare(
                "SELECT Alias, djname, active, years, weight FROM dj"
                . " WHERE alias = ?");
        if(!$con){
            $error = $this->myslqi->errorInfo();
            throw new \Exception($error[2]);
        }

        $con->bind_param("s", $alias);
        $con->bind_result($alias,$djname,$active,$years,$weight);
        if(!$con->execute())
            return false;
        while($con->fetch()){
            $result = array(
                'alias'=>$alias,'djname'=>$djname,'active'=>$active,
                'years'=>$years,'weight'=>$weight);
        }
        $con->close();
        return $result;
    }
    
    public function delete($ids){
        if(!is_array($ids)){
            $ids = array($ids);
        }
        $ident = NULL;
        $stmt = $this->db->prepare("DELETE FROM genre WHERE genre.genreid=:id"
                . " and genre.Station=:station and"
                . " NOT EXISTS (SELECT program.genre FROM program"
                . " WHERE program.callsign=:station and program.genre=:id)");
        $stmt->bindParam(":id", $ident);
        $stmt->bindParam(":station", $this->callsign);
        $result = [];
        foreach($ids as $ident){
            if($stmt->execute()){
                $result[$ident] = $stmt->rowCount();
            }
        }
        return $result;
    }

     public function displayTable($filter)
    {
        
        $where = '';
        $table = 'dj';
         
        // Table's primary key
        $primaryKey = 'Alias';         
        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        
        $columns = array(
            array( 'db' => 'djname',   'dt' => 'djname' ),
            array( 'db' => 'alias', 'dt' => 'alias' ),
            array( 'db' => 'active', 'dt' => 'active' ),
            array( 'db' => 'years', 'dt' => 'years' ),
            array( 'db' => 'weight', 'dt' => 'weight' ),
        );

        $prog_data = \SSP::complex($_GET, $this->db, $table, $primaryKey, $columns, null, $where);

        // foreach($prog_data['data'] as &$program) {
        //     $program['host'] = $this->getDjByProgramName($program['programname']);
        // }
        return json_encode($prog_data);
    }

    public function getDjByProgramName($programName)
    {
        $con = $this->mysqli->prepare("SELECT djname FROM dj JOIN performs WHERE dj.alias = performs.alias AND programname = ?;");
        $con->bind_param("s", $programName);
        $con->bind_result($djName);

        if($con->execute())
        {
            $con->fetch();
            $con->close();
            return $djName;
        }
        else
            return false;
    }
}
