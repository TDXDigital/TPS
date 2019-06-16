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
class playlist extends TPS{
    //put your code here
    
    public function __construct(
            $enableDbReporting = FALSE, $requirePDO = FALSE, 
            $settingsTarget = NULL, $settingsPath = NULL) {
        parent::__construct($enableDbReporting, $requirePDO, $settingsTarget, 
                $settingsPath);
    }
    
    private function getRangeGap($start, $list){
        sort($list);
        $terminus = max($list);
        foreach ($list as $val){
            if($val < $start){
                continue;
            }
            if(!in_array($start, $list)){
                $start+=1;
                continue;
            }
            break;
        }
        $max = $start-1;
        foreach ($list as $iter=>$value) {
            if($value < $start){
                continue;
            }
            if($max+1==$value){
                $max = $value;
                continue;
            }
            break;
        }
        return $max;
    }
    
    public function getRangeGaps($list, $start=FALSE){
        if($start===FALSE){
            $start = min($list);
        }
        $ranges = array();
        while ($start < max($list)) {
            $max = $this->getRangeGap($start, $list);
            array_push($ranges, [$start, $max]);
            $start = $max+2; #plus one will not work as we know 
            # the +1 value is missing
        }
        return $ranges;
    }
    
    public function getRangeGapsForGenres($station, $today=FALSE,
            $genres=FALSE){
        if(!is_array($genres) && !($genres===FALSE)){
            $genres = [$genres];
        }
        $today = $today?:date("Y-m-d");
        $genresList = $this->getGenreDividedValidShortCodes($station, 
                $today, "0 days");
        $gaps = array();
        foreach ($genresList as $key => $value) {
            if(!($genres===FALSE) && !in_array($key, $genres)){
                continue;
            }
            $var = array();
            foreach ($value as $data) {
                if(!$data['shortCodes']){
                    continue;
                }
                $res = $this->getRangeGaps($data['shortCodes']);
                $min = min($data['shortCodes']);
                array_push($var, array(
                        "formats"=>$data['formats'],
                        "gaps"=>$res
                    )
                );
            }
            $gaps[$key] = $var;
        }
        return $gaps;
    }
    
    public function getRangesFormats($id){
        #@todo: expand to accept array
        $stmt = $this->db->prepare("SELECT format, fid FROM playlistRangesFormat "
                . "WHERE id=:id");
        $stmt->bindParam(":id", $id);
        $result = array();
        if(!$stmt->execute()){
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                array_push($result[$id], $row['format']);
            }
        }
        return $result;
    }
    
    public function getGenreShortCodeRanges($station, $useDb=False){
        if($useDb){
            $stmt = $this->db->prepare("SELECT * FROM playlistRanges WHERE callsign"
                . "=:callsign");
            $stmt->bindParam(":callsign", $station);
            $result = array();
            if(!$stmt->execute()){
                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                    $data = $this->getRangesFormats($row['id']);
                    $row['formats'] = $data[$row['id']];
                    $result[$row['id']] = $row;
                }
            }
            return $result;
        }
        $ranges = array(
            "RP" => array(
                array(
                    "format"=>['CD', 'Cart', 'MD', 'Other'],
                    "range"=>[1,599]
                ),
                array(
                    "format"=>['7in', '10in', '12in'],
                    "range"=>[600,699]
                ),
                array(
                    "format"=>['Cass'],
                    "range"=>[700,799]
                ),
                array(
                    "format"=>['Digital'],
                    "range"=>[800,999]
                )
            ),
            "FR" => array(
                array(
                    "format"=>['CD', 'Cart', 'MD', 'Other', 'Digital',
                        '7in', '10in', '12in', 'Cass'],
                    "range"=>[1000,1999]
                )
            ),
            "HM" => array(
                array(
                    "format"=>['CD', 'Cart', 'MD', 'Other', 'Digital',
                        '7in', '10in', '12in', 'Cass'],
                    "range"=>[2000,2999]
                )
            ),
            "EL" => array(
                array(
                    "format"=>['CD', 'Cart', 'MD', 'Other', 'Digital',
                        '7in', '10in', '12in', 'Cass'],
                    "range"=>[3000,3999]                
                )
            ),
            "HH" => array(
                array(
                    "format"=>['CD', 'Cart', 'MD', 'Other', 'Digital',
                        '7in', '10in', '12in', 'Cass'],
                    "range"=>[4000,4999]
                )
            ),
            "WD" => array(
                array(
                    "format"=>['CD', 'Cart', 'MD', 'Other', 'Digital',
                        '7in', '10in', '12in', 'Cass'],
                    "range"=>[5000,5999]
                )
            ),
            "JC" => array(
                array(
                    "format"=>['CD', 'Cart', 'MD', 'Other', 'Digital',
                        '7in', '10in', '12in', 'Cass'],
                    "range"=>[6000,6999]
                )
            ),
            "EX" => array(
                array(
                    "format"=>['CD', 'Cart', 'MD', 'Other', 'Digital',
                        '7in', '10in', '12in', 'Cass'],
                    "range"=>[7000,7999]
                )
            ),
            "OT" => array(
                array(
                    "format"=>['CD', 'Cart', 'MD', 'Other', 'Digital',
                        '7in', '10in', '12in', 'Cass'],
                    "range"=>[8000,8999]
                )
            )
        );
        return $ranges;
    }
    
    public function getGenreShortCodeRange($code, $station){
        $ranges = $this->getGenreShortCodeRanges($station);
        if($code){
            return $ranges[$code];
        }
        else{
            return $ranges;
        }
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
    
    public function setPlaylistId($refcodes, $plid){
        if(!is_array($refcodes)){
            $refcodes = array($refcodes);
        }
        $refcode = NULL;
        $stmt = $this->db->prepare("UPDATE playlist SET PlaylistId=:plid"
            . " WHERE RefCode=:refcode");
        $stmt->bindParam(":refcode", $refcode, \PDO::PARAM_STR);
        $stmt->bindParam(":plid", $plid);
        foreach($refcodes as $refcode){
            $stmt->execute();
        }
        return true;
    }
    
    public function get($plIds){
        if(!is_array($plIds)){
            $params = array($plIds);
        }
        $param = NULL;
        $stmt = $this->db->prepare(
                "SELECT * FROM playlist"
            . " WHERE PlaylistId=:param");
        $stmt->bindParam(":param", $param, \PDO::PARAM_STR);
        $result = array();
        foreach($params as $param){
            if ($stmt->execute()) {
                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                    $result[$row['PlaylistId']] = $row;
                }
            }
        }
        return $result;
    }
    
    public function getCurrentByRefCode($refCodes, $startDate, $endDate){
        if(!is_array($refCodes)){
            $params = array($refCodes);
        }
        else{
            $params = $refCodes;
        }
        $param = NULL;
        $stmt = $this->db->prepare(
                "SELECT * FROM playlist"
            . " WHERE RefCode=:param and (Activate <= :startDate and "
            . " Expire >= :endDate) or "
            . " :startDate between Activate and Expire or :endDate between"
            . " Activate and Expire");
        $stmt->bindParam(":param", $param, \PDO::PARAM_STR);
        $stmt->bindParam(":startDate", $startDate);
        $stmt->bindParam(":endDate", $endDate);
        $result = array();
        foreach($params as $param){
            if ($stmt->execute()) {
                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                    $result[$row['PlaylistId']] = $row;
                }
            }
        }
        return $result;
    }
    
    public function getCurrentByShortCode($shortCodes, $startDate, $endDate){
        if(!is_array($shortCodes)){
            $params = array($shortCodes);
        }
        else{
            $params = $shortCodes;
        }
        $param = NULL;
        $stmt = $this->db->prepare(
                "SELECT * FROM playlist"
            . " WHERE SmallCode=:param and (Activate between :startDate and "
            . " :endDate or Expire between :startDate and :endDate) or "
            . " :startDate between Activate and Expire or :endDate between"
            . " Activate and Expire");
        $stmt->bindParam(":param", $param, \PDO::PARAM_STR);
        $stmt->bindParam(":startDate", $startDate);
        $stmt->bindParam(":endDate", $endDate);
        $result = array();
        foreach($params as $param){
            if ($stmt->execute()) {
                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                    $result[$row['PlaylistId']] = $row;
                }
            }
        }
        return $result;
    }
    
    public function getPlaylist($startDate, $endDate){
        $stmt = $this->db->prepare(
                "SELECT * FROM playlist"
                . " LEFT JOIN library ON playlist.RefCode=library.RefCode WHERE"
                . " (Activate between :startDate and "
                . " :endDate or Expire between :startDate and :endDate) "
                . "or :startDate between Activate and Expire or :endDate "
                . "between Activate and Expire "
                . "order by playlist.SmallCode ASC");
        $stmt->bindParam(":startDate", $startDate);
        $stmt->bindParam(":endDate", $endDate);
        $result = array();
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
		$id = $row['PlaylistId'];
                $result[$id] = $row;

		// Attach labels
		$labels = [];
		$sql = $this->db->query("SELECT * FROM recordlabel WHERE LabelNumber IN " .
					"(SELECT recordLabel_LabelNumber from library_recordlabel WHERE library_RefCode={$row['RefCode']})");
		while ($label = $sql->fetch(\PDO::FETCH_ASSOC))
		    array_push($labels, $label);
		$result[$id]['labels'] = $labels;
            }
        }
        return $result;
    }

    public function getUsedShortCodes($startDate, $endDate){
        $stmt = $this->db->prepare(
                "SELECT SmallCode FROM playlist"
                . " WHERE (:startDate >= Activate AND :startDate <= Expire) OR "
                . "(:endDate <= Expire AND :endDate >= Activate) OR "
                . "(:startDate <= Activate AND :endDate >= Expire)");
        $stmt->bindParam(":startDate", $startDate);
        $stmt->bindParam(":endDate", $endDate);
        $result = array();
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $val = (int)$row['SmallCode'];
                $result[$val] = $val;
            }
        }
        return $result;
    }
    
    public function validShortCodes($startDate, $endDate, $startNum, $endNum){
        $used = $this->getUsedShortCodes($startDate, $endDate);
        $range = range($startNum, $endNum); 
        $result = array_diff($range, $used);
        return array_values($result);
    }
    
    public function validateShortCode($startDate, $endDate, $code){
        $used = $this->getUsedShortCodes($startDate, $endDate);
        return !(key_exists((int)$code, $used));
    }
    
    public function getGenreDividedValidShortCodes($station, $defaultOffsetDate, 
            $defaultOffset=True, $genres=False, $format=False){
        /**
         * @param mixed $defaultOffet True to use offet proveded by genre, 
         * string otherwise
         */
        if(!is_array($genres) && $genres != FALSE){
            $genres = [$genres];
        }
        $playlist = new \TPS\playlist();
        $library = new \TPS\library($station);
        $ranges = $playlist->getGenreShortCodeRanges($station);
        $validRanges = array();
        foreach ($ranges as $genre => $ranges) {
            if($genres){
                if(!in_array($genre, $genres)){
                    continue;
                }
            }
            $validRanges[$genre] = [];
            foreach ($ranges as $range) {
                if($defaultOffset===TRUE){
                    $code = $library->getLibraryCodeValueByGenre($genre);
                    $defaultOffsetStr = "$defaultOffsetDate +".
                            $code['PlaylistDuration']['value'] .
                            " " . $code['PlaylistDuration']['unit'];
                }
                else{
                    $defaultOffsetStr = "$defaultOffsetDate +$defaultOffset";
                }
                $codes = $this->validShortCodes(
                        $defaultOffsetDate, date("Y-m-d", strtotime($defaultOffsetStr)),
                        $range['range'][0], $range['range'][1]);
                if(!$codes){
                    continue;
                }
                if($format===FALSE || in_array($format, $range['format'])){
                    array_push($validRanges[$genre], array(
                            "formats"=>$range['format'],
                            "shortCodes"=>$codes
                        )
                    );
                }
            }
        }
        return $validRanges;
    }


    public function getAllByRefCode($refCodes){
        if(!is_array($refCodes)){
            $params = array($refCodes);
        }
        $param = NULL;
        $stmt = $this->db->prepare(
                "SELECT * FROM playlist"
            . " WHERE RefCode=:param");
        $stmt->bindParam(":param", $param, \PDO::PARAM_STR);
        $result = array();
        foreach($params as $param){
            if ($stmt->execute()) {
                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                    $result[$row['PlaylistId']] = $row;
                }
            }
        }
        return $result;
    }
    
    public function getAllByShortCode($shortCodes){
        if(!is_array($shortCodes)){
            $params = array($shortCodes);
        }
        $param = NULL;
        $stmt = $this->db->prepare(
                "SELECT * FROM playlist"
            . " WHERE SmallCode=:param");
        $stmt->bindParam(":param", $param, \PDO::PARAM_STR);
        $result = array();
        foreach($params as $param){
            if ($stmt->execute()) {
                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                    $result[$row['PlaylistId']] = $row;
                }
            }
        }
        return $result;
    }
    
    public function getAll($startDate, $endDate, $pagination=Null, $maxResult=Null){
        $this->sanitizePagination($pagination, $maxResult);
        $stmt = $this->db->prepare(
                "SELECT * FROM playlist WHERE `Activate` >= :startDate and "
            . " (`Expire` <= :endDate or `Expire` IS NULL) LIMIT :start, :end");
        $stmt->bindParam(":start", $pagination, \PDO::PARAM_INT);
        $stmt->bindParam(":end", $maxResult, \PDO::PARAM_INT);
        $stmt->bindParam(":startDate", $startDate);
        $stmt->bindParam(":endDate", $endDate);
        $result = array();
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $result[$row['PlaylistId']] = $row;
            }
        }
        return $result;
    }
    
    public function create($refcodes, $startDate, $endDate,
            $zoneCode=Null, $zoneNumber=Null, $smallCode=NULL){
        if(!is_array($refcodes)){
            $refcodes = array($refcodes);
        }
        $refcode = NULL;
        $smallCode = $refcodes[0];
        $stmt = $this->db->prepare("INSERT INTO playlist (RefCode, Activate, "
                . "Expire, ZoneCode, ZoneNumber, SmallCode) VALUES "
                . "(:refcode, :activate, :expire, :zoneCode, :zoneNumber, "
                . ":smallCode)");
        $stmt->bindParam(":refcode", $refcode);
        $stmt->bindParam(":activate", $startDate);
        $stmt->bindParam(":expire", $endDate);
        $stmt->bindParam(":smallCode", $smallCode, \PDO::PARAM_INT);
        $stmt->bindParam(":zoneCode", $zoneCode);
        $stmt->bindParam(":zoneNumber", $zoneNumber);
        $ids = [];
        foreach($refcodes as $refcode){
            $result = $stmt->execute();
            if(!$result){
                $ids[$refcode] = $stmt->errorInfo();
            }
            else{
                $ids[$refcode] = $this->db->lastInsertId();
            }
        }
        return $ids;
    }
    
    public function change($playlistIds, $startDate, $endDate,
            $zoneCode=Null, $zoneNumber=Null, $smallCode=NULL){
        $this->setStart($playlistIds, $startDate);
        $this->setExpiry($playlistIds, $endDate);
    }
    
    public function countAll(){
        $stmt = $this->db->prepare(
                "SELECT count(*) as count FROM playlist");
        $result = 0;
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $result += (int)$row['count'];
            }
        }
        return $result;
    }
    
}
