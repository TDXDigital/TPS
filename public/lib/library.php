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

/**
 * @author James Oliver <support@ckxu.com>
 * @abstract library interface
 * @version 1.0
 */
use PhpParser\Builder\Trait_;
//use SSP;
require_once 'ssp.class.php';
require_once 'station.php';

class library extends station{

    public $playlist = null;

    public function __construct($callsign=null){
        parent::__construct($callsign);
        $this->playlist = new \TPS\playlist();
    }
    #protected $RefCode;

    /**
     *
     * @global mysqli $mysqli
     * @param type $RefCode Reference Code for Album
     * @return array Array of websites, False on Error
     * @version 0.1
     */
    public function getWebsitesByRefCode($RefCode){
        $this->mysqli;
        $websites = array();
        $selectWebsites = "Select band_websites.URL, band_websites.Service, band_websites.date_available, band_websites.date_discontinue"
                    . " from band_websites where band_websites.ID=?;";
        if($bands = $this->mysqli->prepare($selectWebsites)){
            $bands->bind_param('i',$RefCode);
            $bands->execute();
            $bands->bind_result($url,$service,$available,$discontinue);
            while($bands->fetch()){
                $websites[$service]=array(
                    "url" => $url,
                    "available" => $available,
                    "discontinue" => $discontinue);
            }
            $bands->close();
        }
        else{
            error_log($this->mysqli->errno.": ".$this->mysqli->error);
            return FALSE;
        }
        return $websites;
    }

    /**
     * @author Derek Melchin 
     * @abstract Returns library note for a given album
     * @global mysqli $mysqli
     * @param type $RefCode Reference Code for Album
     * @return string of notes
     */
    public function getNoteByRefCode($RefCode) {
	$sql = $this->mysqli->query("SELECT note FROM library WHERE RefCode=" . $RefCode . ";");
	while ($row = $sql->fetch_array(MYSQLI_ASSOC))
	    return $row['note'];
	throw new Exception("Error while fetching note for RefCode " . $RefCode);
    }
    /**
     * @abstract returns genre for a given album
     * @global mysqli $mysqli
     * @param type $RefCode Reference Code for Album
     * @return string of genre
     */

    public function getGenreByRefCode($RefCode) {
	$sql = $this->mysqli->query("SELECT genre FROM library WHERE RefCode=" . $RefCode . ";");
	while ($row = $sql->fetch_array(MYSQLI_ASSOC))
	    return $row['genre'];
	throw new Exception("Error while fetching genre for RefCode " . $RefCode);
    }

    /**
     * @abstract returns format for a given album
     * @global mysqli $mysqli
     * @param type $RefCode Reference Code for Album
     * @return string of format
     */
    public function getFormatByRefCode($RefCode) {
	$sql = $this->mysqli->query("SELECT format FROM library WHERE RefCode=" . $RefCode . ";");
	while ($row = $sql->fetch_array(MYSQLI_ASSOC))
	    return $row['format'];
	throw new Exception("Error while fetching genre for RefCode " . $RefCode);
    }

    /*
    * @author Derek Melchin
    * @abstract Return all recordlabels
    * @param bool $includeUnconfirmed Whether or not to include unconfirmed albums in the return
    * @return array Array of all recordlabels used in the system
    */
    public function getLabels($includeUnconfirmed=False) {
	if ($includeUnconfirmed)
	    $where = "";
	else
	    $where = " WHERE confirmed=1 ";
	$sql = $this->mysqli->query("SELECT * FROM recordlabel $where ORDER BY Name");
	$labels = [];
	while ($row = $sql->fetch_array(MYSQLI_ASSOC))
	    array_push($labels, $row);
	return $labels;
    }

    /**
     * @abstract returns record labels for a given album
     * @global mysqli $mysqli
     * @param type $RefCode Reference Code for Album
     * @return array of record label names
     */
    public function getLabelsByRefCode($RefCode) {
	$sql = $this->mysqli->query("SELECT * FROM recordlabel WHERE LabelNumber IN " .
				    "(SELECT recordlabel_LabelNumber FROM library_recordlabel WHERE library_RefCode=" . $RefCode . ") " .
				    "ORDER BY Name");
	$labels = [];
	while ($row = $sql->fetch_array(MYSQLI_ASSOC))
	    array_push($labels, $row);
	return $labels;
    }

    /**
     * @abstract returns static associative array of genres
     * @todo implement database storage
     * @return array
     * @version 0.1
     */
    public function getLibraryGenres(){
        $genres = array(
                "RP" => "0-- Rock/Pop",
                "FR" => "1-- Folk/Roots",
                "HM" => "2-- Heavy/Punk/Metal",
                "EL" => "3-- Electronic",
                "HH" => "4-- Hip-Hop",
                "WD" => "5-- World",
                "JC" => "6-- Jazz/Classical",
                "EX" => "7-- Experimental",
                "OT" => "8-- Other",
            );
        return $genres;
    }


    /**
     * @abstract Library codes are prepended to a barcode and used
     * for indexing and management within a physical library.
     * @todo store in DB
     * @return array
     * @version 1.0
     */
    public function listLibraryCodes(){
        $codes = array(
            "0" => array(
                "Title"=>"Rock/Pop",
                "Genre"=>"RP",
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>6,
                ),
                "SubGenres" => array(
                    "21"=>"Rock",
                    "21"=>"Pop",
                    "21"=>"Alternative",
                    "21"=>"Electronic Pop",
                    "21"=>"Funk",
                    "23"=>"Acoustic Versions",
                    "24"=>"Easy Listening",
                    )
                ),
            "1" => array(
                "Title"=>"Folk/Roots",
                "Genre"=>"FR",
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>6,
                ),
                "SubGenres" => array(
                    "21"=>"Folk Rock",
                    "21"=>"Singer-Songwriter",
                    "21"=>"Blues Rock",
                    "22"=>"Country",
                    "32"=>"Traditional Folk",
                    "34"=>"Traditional Blues",
                    )
                ),
            "2" => array(
                "Title"=>"Heavy",
                "Genre"=>"HM",
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>6,
                ),
                "SubGenres" => array(
                    "21"=>"Metal",
                    "21"=>"Punk",
                    "21"=>"Hardcore",
                    "21"=>"Hard Rock",
                    )
                ),
            "3" => array(
                "Title"=>"Electronic",
                "Genre"=>"EL",
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>6,
                ),
                "SubGenres" => array()
                ),
            "4" => array(
                "Title"=>"Hip-hop/Rap",
                "Genre"=>"HH",
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>6,
                ),
                "SubGenres" => array(
                    "21"=>"Hip-hop/Rap",
                    )
                ),
            "5" => array(
                "Title"=>"World",
                "Genre"=>"WD",
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>6,
                ),
                "SubGenres" => array(
                    "35"=>"World Pop",
                    "35"=>"Traditional World",
                    "35"=>"3rd Language",
                    )
                ),
            "6" => array(
                "Title"=>"Jazz/Classical",
                "Genre"=>"JC",
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>6,
                ),
                "SubGenres" => array(
                    "31"=>"Classical",
                    "34"=>"Jazz",
                    )
                ),
            "7" => array(
                "Title"=>"Experimental",
                "Genre"=>"EX",
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>9,
                ),
                "SubGenres" => array(
                    "36"=>"Avant composition",
                    "36"=>"Noise",
                    "36"=>"Field Recordings",
                    "36"=>"Acousmatic",
                    )
                ),
            "8" => array(
                "Title"=>"Other",
                "Genre"=>"OT",
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>9,
                ),
                "SubGenres" => array(
                    "12"=>"Spoken Word",
                    "12"=>"Comedy",
                    "12"=>"Radio Drama",
                    "11"=>"News",
                    "35"=>"Religious",
                    )
                ),
            "9" => array(
                "Title"=>"System",
                "Genre"=>null,
                "PlaylistDuration"=>array(
                    "unit"=>"month",
                    "value"=>3,
                ),
                "SubGenres" => array(
                    "45"=>"Tech Test"
                )
            )
        );
        return $codes;
    }

    public function getLibraryCodeByGenre($Genre) {
        $list = $this->listLibraryCodes();
        $comVal='Genre';
        foreach ($list as $key => $value) {
            if ($value[$comVal] == $Genre){
                return $key;
            }
        }
        return False;
    }

    public function getLibraryCodeValueByGenre($Genre) {
        $list = $this->listLibraryCodes();
        $comVal='Genre';
        foreach ($list as $key => $value) {
            if ($value[$comVal] == $Genre){
                return $value;
            }
        }
        return False;
    }

    /**
    * @author Derek Melchin
    * @abstract Determine the genre-leading library number
    * @param int $RefCode The album's unique reference number
    * @return str The library code (Genre number <dash> RefCode)
    */
    public function getLibraryCodeByRefCode($RefCode) {
        $genre = $this->getGenreByRefCode($RefCode);
        $genre_num = NULL;
        if($genre != "null"){
            $genres = $this->getLibraryGenres();
            if (in_array($genre, array_keys($genres))) {
                $genre_text = $genres[$genre];
                preg_match("/[0-9]+/", $genre_text, $matches);
                $genre_num = $matches[0];
            }
        }
        return "{$genre_num}-{$RefCode}";
    }

    public function createBarcode($refcode){
        if ($stmt = $this->mysqli->prepare(
                    "UPDATE library SET Barcode=? WHERE RefCode=?")){
            $stmt->bind_param("ii",$Barcode,$RefCode);
            $stmt->execute();
            $stmt->close();
        }
        else{
            throw new Exception(
                      "Could not create barcode ".$this->mysqli->error);
        }
    }

    public function getBarcodeByRefCode($RefCode){
        $attr = $this->getAlbumByRefcode($RefCode, True);
        if(is_null($attr['barcode'])){
            $barcode = $this->createBarcode($RefCode);
        }
        else{
            $barcode = $attr['barcode'];
        }
        return $barcode;
    }

    /**
     * @abstract gets static associative array of covernment codes
     * @todo store in DB
     * @return string
     * @version 0.1
     */
    public function getGovernmentCodes(){
        $govCat = array(
                // CRTC Categories http://www.crtc.gc.ca/eng/archive/2010/2010-819.HTM
                "21" => "Pop, rock and dance",
                "11" => "News",
                "12" => "Spoken word-other",
                "22" => "Country and country-oriented",
                "23" => "Acoustic",
                "24" => "Easy listening",
                "31" => "Concert",
                "32" => "Folk and folk-oriented",
                "33" => "World beat and international",
                "34" => "Jazz and blues",
                "35" => "Non-classic religious",
                "36" => "Experimental Music",
                "41" => "Musical themes, bridges and stingers",
                "42" => "Technical tests",
                "43" => "Musical station identification",
                "44" => "Musical identification of announcers, programs",
                "45" => "Musical promotion of announcers, programs",
                "51" => "Commercial announcement",
                "52" => "Sponsor Identification",
                "53" => "Promotion with sponsor mention",
            );
        return $govCat;
    }

    /**
     *
     * @abstract return Media Fromats for library
     * @todo store in db
     * @return array
     * @version 0.1
     */
    public function getMediaFormats(){
        $formats = array(
                "CD" => "Compact Disc",
		"2CD" => "2CD",
		"Oversized CD" => "Oversized CD",
                "Digital"=>"Digital",
                "12in" => "12\"",
                "10in" => "10\"",
                "7in" => "7\"",
		"Other Vinyl" => "Other Vinyl",
                "Cass" => "Cassette",
                "Cart"=>"Fidelipac (cart)",
                "MD" => "Mini Disc",
                "Other"=>"Other"
            );
        return $formats;
    }

    /**
     *
     * @abstract return artist locale for library
     * @todo store in db
     * @return array
     * @version 0.1
     */
    public function getLocales(){
        $formats = array(
            "International" => "International",
            "Country" => "Country",
            "Province" => "Province",
            "Local" => "Local"
        );
        return $formats;
    }

    /**
     * @abstract provide schedule information is associative array
     * @todo implement database storage
     * @return string
     */
    public function getScheduleBlocks(){
        $blocks = array(
            NULL => "Select",
            "D" => "Daytime [06:00-21:00]",
            "N" => "Nighttime [21:00-06:00]",
        );
        return $blocks;
    }

    /**
     *
     * @abstract return label information based on id
     * @global mysqli $mysqli
     * @param int $labelid label identification number
     * @return boolean|array
     */
    public function getLabelbyId($labelid){
        $this->mysqli;
        $result = array();
        /*elseif(!$exact){
            $refcode="%{$refcode}%";
        }*/
        if($stmt = $this->mysqli->prepare("SELECT LabelNumber, Name, Location, Size,"
                . "name_alias_duplicate as alias, updated, verified FROM recordlabel"
                . " WHERE LabelNumber=?")){
            $stmt->bind_param('i',$labelid);
            $stmt->execute();
            $stmt->bind_result($LabelNumber,$name,$location,$size,$alias,$updated,
                    $verified);
            while($stmt->fetch()){
                array_push($result, array(
                    'labelNumber'=>$LabelNumber,'name'=>$name,'location'=>$location,
                    'size'=>$size,'alias'=>$alias,'updated'=>$updated,'verified'=>$verified,
                ));
            }
            $stmt->close();
        }
        else{
            error_log($this->mysqli->error);
            return FALSE;
        }
        return $result;
    }

    /**
     * @abstract return the music director rating of a specific album
     * @global mysqli $mysqli
     * @return integer
     */
    public function getRatingByRefCode($refcode) {
	$sql = $this->mysqli->query("SELECT rating FROM library WHERE RefCode={$refcode}");
	$rating = $sql->fetch_array(MYSQLI_ASSOC)['rating'];
	return $rating;
    }

    /**
     * @abstract return the cancon status of a specific album
     * @global mysqli $mysqli
     * @return bool (1/0)
     */
    public function getCanconByRefCode($refcode) {
	$sql = $this->mysqli->query("SELECT CanCon FROM library WHERE RefCode={$refcode}");
	$cancon = $sql->fetch_array(MYSQLI_ASSOC)['CanCon'];
	return $cancon;
    }

    /**
     * @abstract return the various artists status of a specific album
     * @global mysqli $mysqli
     * @return bool (1/0)
     */
    public function getVariousArtistsByRefCode($refcode) {
	$sql = $this->mysqli->query("SELECT variousartists FROM library WHERE RefCode={$refcode}");
	$va = $sql->fetch_array(MYSQLI_ASSOC)['variousartists'];
	return $va;
    }

    /**
     * @abstract return all the subgenres ever used on albums
     * @global mysqli $mysqli
     * @param  bool   $includeUnconfirmed Whether or not to include rows that aren't confirmed yet
     * @return array
     */
    public function getSubgenres($includeUnconfirmed=False) {
	if ($includeUnconfirmed)
	    $where = "";
	else
	    $where = "WHERE confirmed=1";
	$sql = $this->mysqli->query("SELECT name FROM subgenres " . $where . " ORDER BY name");
	$subgenres = [];
	while ($row = $sql->fetch_array(MYSQLI_ASSOC))
	    array_push($subgenres, $row['name']);
	return $subgenres;
    }

    /**
     * @abstract return all subgenres for a specific album
     * @global mysqli $mysqli
     * @return array
     */
    public function getSubgenresByRefCode($refcode) {
	$sql = $this->mysqli->query("SELECT name FROM subgenres WHERE id IN (SELECT subgenre_id FROM library_subgenres WHERE library_RefCode=" . $refcode . ") ORDER BY name");
	$subgenres = [];
	while ($row = $sql->fetch_array(MYSQLI_ASSOC))
	    array_push($subgenres, $row['name']);
	return $subgenres;
    }

    /**
     * @abstract return all the hometowns ever used on albums
     * @global mysqli $mysqli
     * @param  bool   $includeUnconfirmed Whether or not to include rows that aren't confirmed yet
     * @return array
     */
    public function getHometowns($includeUnconfirmed=False) {
	if ($includeUnconfirmed)
	    $where = "";
	else
	    $where = "WHERE confirmed=1";
	$sql = $this->mysqli->query("SELECT name FROM hometowns " . $where . " ORDER BY name");
	$hometowns = [];
	while ($row = $sql->fetch_array(MYSQLI_ASSOC))
	    array_push($hometowns, $row['name']);
	return $hometowns;
    }

    /**
     * @abstract return all hometownss for a specific album
     * @global mysqli $mysqli
     * @return array
     */
    public function getHometownsByRefCode($refcode) {
	$sql = $this->mysqli->query("SELECT name FROM hometowns WHERE id IN (SELECT hometown_id FROM library_hometowns WHERE library_RefCode=" . $refcode . ") ORDER BY name");
	$hometowns = [];
	while ($row = $sql->fetch_array(MYSQLI_ASSOC))
	    array_push($hometowns, $row['name']);
	return $hometowns;
    }

    /**
     * @abstract return all the tags ever used on albums
     * @global mysqli $mysqli
     * @param  bool   $includeUnconfirmed Whether or not to include rows that aren't confirmed yet
     * @return array
     */
    public function getTags($includeUnconfirmed=False) {
	if ($includeUnconfirmed)
	    $where = "";
	else
	    $where = "WHERE confirmed=1";
	$sql = $this->mysqli->query("SELECT name FROM tags " . $where . " ORDER BY name");
	$tags = [];
	while ($row = $sql->fetch_array(MYSQLI_ASSOC))
        {
	       array_push($tags, $row['name']);
        }
	return $tags;
    }

    /**
     * @abstract return all tags for a specific album
     * @global mysqli $mysqli
     * @return array
     */
    public function getTagsByRefCode($refcode) {
	$sql = $this->mysqli->query("SELECT name FROM tags WHERE id IN (SELECT tag_id FROM library_tags WHERE library_RefCode=" . $refcode . ") ORDER BY name");
	$tags = [];
	while ($row = $sql->fetch_array(MYSQLI_ASSOC))
	    array_push($tags, $row['name']);
	return $tags;
    }

    /**
     * set status of refcode(s)
     * @param array $refcodes
     * @param int $status
     * @return boolean
     */
    protected function setStatus($refcodes, $status){
        if(!is_array($refcodes)){
            $refcodes = array($refcodes);
        }
        $refcode = NULL;
        $stmt = $this->db->prepare("UPDATE library SET status=:status"
            . " WHERE RefCode=:refcode");
        $stmt->bindParam(":refcode", $refcode, \PDO::PARAM_STR);
        $stmt->bindParam(":status", $status, \PDO::PARAM_INT);
        foreach($refcodes as $refcode){
            $stmt->execute();
        }
        return true;
    }

    /**
     * enable library entries by refcode
     * @param type $refcodes
     * @return type
     */
    public function enable($refcodes){
        return $this->setStatus($refcodes, 1);
    }

    /**
     * disable library entries by refcodes
     * @param type $refcodes
     * @return type
     */
    public function disable($refcodes){
        return $this->setStatus($refcodes, 0);
    }

    /**
     * Change an Attribute
     * @param type $refcodes
     * @param type $attribute
     * @param type $value
     * @return boolean
     */
    public function attribute($refcodes, $value, $attribute){
        if(!is_array($refcodes)){
            $refcodes = array($refcodes);
        }
        //$quote = $this->db->quote($attribute);
        #$quote = "`"+$attribute+"`";
        $quote = $attribute;
        if(strpos($quote, ";")){
            throw new \Exception("Invalid Request");
        }
        $refcode = NULL;
        $stmt = $this->db->prepare("UPDATE library SET $quote=:value"
            . " WHERE RefCode=:refcode");
        $stmt->bindParam(":refcode", $refcode, \PDO::PARAM_STR);
        $stmt->bindParam(":value", $value);
        foreach($refcodes as $refcode){
            $stmt->execute();
        }
        return true;
    }

    public function pendingPlaylist(){
        return $this->playlistFetch('PENDING');
    }

    public function completePlaylist(){
        return $this->playlistFetch('COMPLETE');
    }

    public function rejectedPlaylist(){
        return $this->playlistFetch('FALSE');
    }

    private function playlistFetch($flag){
        $stmt = $this->db->prepare(
                "SELECT * FROM library"
            . " WHERE playlist_flag=:flag and "
            . " status=1");
        $stmt->bindParam(":flag", $flag);
        $result = array();
        if ($stmt->execute()) {
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                array_push($result,$row);
            }
        }
        return $result;
    }

    public function playlistStatus($refcodes, $value){
        if(!is_array($refcodes)){
            $refcodes = array($refcodes);
        }
        //$quote = $this->db->quote($attribute);
        #$quote = "`"+$attribute+"`";
        $quote = $value;
        if(strpos($quote, ";")){
            throw new \Exception("Invalid Request");
        }
        $refcode = NULL;
        $stmt = $this->db->prepare("UPDATE library SET playlist_flag=:value"
            . " WHERE RefCode=:refcode");
        $stmt->bindParam(":refcode", $refcode, \PDO::PARAM_STR);
        $stmt->bindParam(":value", $value);
        $status = [];
        foreach($refcodes as $refcode){
            $status[$refcode] = $stmt->execute();
        }
        return $status;
    }

    /**
     *
     * @abstract get album information from library by RefCode
     * @global \TPS\mysqli $mysqli
     * @param string $refcode
     * @param boolena #exact
     * @return boolean|array
     */
    public function getAlbumByRefcode($refcode,$exact=FALSE){
        $this->mysqli;
        $result = array();
        if($refcode===Null){
            $refcode='%';
        }
        /*elseif(!$exact){
            $refcode="%{$refcode}%";
        }*/
        if($stmt = $this->mysqli->prepare("SELECT Barcode,year,datein,dateout,RefCode,artist,album,"
                . "`format`,variousartists,`condition`,genre,`status`,"
                . "Locale,CanCon,updated,release_date,note,playlist_flag,governmentCategory,"
                . "scheduleCode, rating, library_code "
                . "FROM library where "
                . "Refcode = ?")){
            $stmt->bind_param('s',$refcode);
            $stmt->execute();
            $stmt->bind_result($barcode,$year,$datein,$dateout,$RefCode_q,
                    $artist_q,$album_q,$format,$variousartists,
                    $condition,$genre,$status,
                    $Locale,$CanCon,$updated,$release_date,
                    $note,$playlist_flag,$govCat,$scCode, $rating, $libraryCode);
            while($stmt->fetch()){
                array_push($result, array(
                    'barcode'=>$barcode,'year'=>$year,
                    'datein'=>$datein,'dateout'=>$dateout,'RefCode'=>$RefCode_q,
                    'artist'=>$artist_q,'album'=>$album_q,'format'=>$format,
                    'variousartists'=>$variousartists,
                    'condition'=>$condition,'genre'=>$genre,'status'=>$status,
                    'Locale'=>$Locale,'CanCon'=>$CanCon,'updated'=>$updated,
                    'release_date'=>$release_date,
                    'note'=>$note,'playlist_flag'=>$playlist_flag,
                    'governmentCategory'=>$govCat,'scheduleCode'=>$scCode,
		    'rating'=>$rating,
		    'libraryCode'=>$libraryCode
                ));
            }
            $stmt->close();
        }
        else{
            //$result=["error"=>$mysqli->error];
            error_log($this->mysqli->error);
            return FALSE;
        }
        return $result;
    }

    /**
     * More general search of the library that matches on any
     * of the following columns: artist, album, note, Locale, or Genre
     * @abstract Get all key library information based on \
     * given input and return in json format \
     * all values that match the paramaters. \
     * Similar to SeathcLibraryWithAlbum but broader search
     * @todo convert to wrapper for searchLibraryWithAlbum
     * @global \TPS\mysqli $mysqli
     * @param string $term
     * @param boolean $exact
     * @param string $sort columns name to sort result by
     * @return boolean|array
     */
    public function searchLibrary($term,$exact=False,$page=1,$limit=25, $sort='RefCode', $reverse=false){

        //$this->mysqli;
        $tps = new \TPS\TPS();
        $tps->sanitizePagination($page, $limit);
        $result = array();
        if(!$exact){
            $term="%{$term}%";
        }
        $cols = $tps->listDatabaseColumns('library');
        if(!in_array($sort, $cols, true)){
            $sortIndex = array_find($sort, $cols);
            if(!$sortIndex){
                throw new \Exception("requested column does not exist in specified table");
            }
            else{
                $oldSort = (string)$sort;
                $sort = $cols[$sortIndex];
                $this->log->warn("table column `$oldSort` requested but wrong case found, will use `$sort`",
                    200, 'library search');
            }
        }
        $direction = $reverse?"DESC":"";
        $saniCol = "";
        try{
            $saniCol = $this->mysqli->real_escape_string($sort);
        }
        catch (\Exception $e){
            # PDO
            $saniCol = $this->db->quote($sort);
        }
        if($stmt = $this->mysqli->prepare("SELECT datein,dateout,RefCode,artist,album,"
                . "`format`,variousartists,`condition`,genre,`status`,"
                . "Locale,CanCon,updated,release_date,note,playlist_flag,year "
                . "FROM library where "
                . "artist like ? or album like ? or note like ? or"
                . " Locale like ? or genre like ? ORDER BY `library`.`$saniCol`, `library`.`RefCode` "
                . "$direction limit ?,?")){
            $stmt->bind_param('sssssii', $term, $term, $term, $term, $term, $page, $limit);
            $stmt->execute();
            $stmt->bind_result($datein,$dateout,$RefCode_q,
                    $artist_q,$album_q,$format,$variousartists,
                    $condition,$genre,$status,
                    $Locale,$CanCon,$updated,$release_date,
                    $note,$playlist_flag,$year);
            while($stmt->fetch()){
                array_push($result, array(
                    'datein'=>$datein,'dateout'=>$dateout,'RefCode'=>$RefCode_q,
                    'artist'=>$artist_q,'album'=>$album_q,'format'=>$format,
                    'variousartists'=>$variousartists,
                    'condition'=>$condition,'genre'=>$genre,'status'=>$status,
                    'Locale'=>$Locale,'CanCon'=>$CanCon,'updated'=>$updated,
                    'release_date'=>$release_date,
                    'note'=>$note,'playlist_flag'=>$playlist_flag,'year'=>$year,
                ));
            }
            $stmt->close();
        }
        else{
            //$result=["error"=>$mysqli->error];
            error_log($this->mysqli->error);
            return FALSE;
        }
        return $result;
        
    }
    public function getTableFilter($filter)
    {
         switch($filter["status"])
        {   
            case 'all': $where = " true"; break;
            case 'accept': $where = " status = 1"; break;
            case 'reject': $where = " status = 0"; break;
            case 'na': $where = " status = -1"; break;
        }
        switch($filter["date"])
        {   
            case 'all': $where .= " AND true"; break;
            case 'new_recive': $where .= " AND TIMESTAMPDIFF(MONTH, dateIn, now()) < 6"; break;
            case 'new_release': $where .= " AND TIMESTAMPDIFF(MONTH, release_date, now()) < 6"; break;
            case 'old_recive': $where .= " AND TIMESTAMPDIFF(MONTH, dateIn, now()) >= 6"; break;
            case 'old_release': $where .= " AND TIMESTAMPDIFF(MONTH, release_date, now()) >= 6"; break;
        }
        switch($filter["genre"])
        {
            case 'all': $where .= " AND true"; break;
            default: $where .= " AND genre = '". $filter["genre"]."'";
        }
        switch($filter["locale"])
        {
            case 'all': $where .= " AND true"; break;
            default: $where .= " AND Locale = '". $filter["locale"]."'";
        }
        switch($filter["format"])
        {
            case 'all': $where .= " AND true"; break;
            default: $where .= " AND format = '". $filter["format"]."'";
        }
        switch($filter["missing_info"])
        {
            case 'all': $where .= " AND true"; break;
	    case 'album' : $where .= " AND missing=1"; break;
            case 'label': $where .= " AND refCode NOT IN (SELECT library_refcode from library_recordlabel)"; break;
            case 'hometown': $where .= " AND (SELECT COUNT(*) from library_hometowns WHERE library_refCode=refcode) = 0"; break;
            case 'genre': $where .= " AND Genre is null"; break;
            case 'rating': $where .= " AND (rating is null OR rating = 0)"; break;
            case 'rel_date': $where .= " AND (release_date is null OR release_date = '1970-01-01')"; break;
            case 'status': $where .= " AND status = -1"; break;
            case 'crtc': $where .= " AND (governmentCategory is null or governmentCategory='None')"; break;
        }
        switch($filter['tag'])
        {
            case 'all': $where .= " AND true"; break;
            default: $where .= " AND RefCode IN (select library_refCode from library_tags where tag_id = (select id from tags where name = ". $this->db->quote($filter['tag']) ."))";
        }
        switch($filter['hometown'])
        {
            case 'all': $where .= " AND true"; break;
            default: $where .= " AND RefCode IN (select library_refCode from library_hometowns where hometown_id = (select id from hometowns where name = ". $this->db->quote($filter['hometown']) ."))";
        }
        switch($filter['subgenre'])
        {
            case 'all': $where .= " AND true"; break;
            default: $where .= " AND RefCode IN (select library_refCode from library_subgenres where subgenre_id = (select id from subgenres where name = ". $this->db->quote($filter['subgenre']) ."))";
        }
        return $where;
    }

    public function displayTable($filter, &$app)
    {
        $where = self::getTableFilter($filter);

        $table = 'library';
         
        // Table's primary key
        $primaryKey = 'RefCode';         
        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        
        $columns = array(
            array( 'db' => 'refCode', 'dt' => 'DT_RowId' ),
            array( 'db' => 'refCode', 'dt' => 'refCode' ),
            array( 'db' => 'status', 'dt' => 'status' ),
            array( 'db' => 'datein', 'dt' => 'datein' ),
            array( 'db' => 'artist',  'dt' => 'artist' ),
            array( 'db' => 'album',   'dt' => 'album' ),
            array( 'db' => 'genre',   'dt' => 'genre' ),
            array( 'db' => 'genre',   'dt' => 'genre_detail' ),
            array( 'db' => 'year', 'dt' => 'year'),
            array( 'db' => 'format',   'dt' => 'format' ),
            array( 'db' => 'condition',   'dt' => 'condition' ),
            array( 'db' => 'status',   'dt' => 'status' ),
            array( 'db' => 'locale',   'dt' => 'locale' ),
            array( 'db' => 'CanCon',   'dt' => 'CanCon' ),
            array( 'db' => 'note',   'dt' => 'note' ),
            array( 'db' => 'rating',   'dt' => 'rating' ),
            array( 'db' => 'playlist_flag',   'dt' => 'playlist_flag' ),
            array( 'db' => 'release_date',   'dt' => 'release_date' ),
            array( 'db' => 'rating',   'dt' => 'rating' ),
        );

        $lib_data = \SSP::complex($_GET, $this->db, $table, $primaryKey, $columns, null, $where);

        //
        $genreList = self::getLibraryGenres();
        foreach($lib_data['data'] as $i => $item)
        {
            if($item['genre']!='' && isset($genreList[$lib_data['data'][$i]['genre']]))
                $lib_data['data'][$i]['genre_detail'] = $genreList[$lib_data['data'][$i]['genre']];
        }
        return json_encode($lib_data);
    }

    public function countSearchLibrary($term="",$exact=False){
        //$this->mysqli;
        $tps = new \TPS\TPS();
        $tps->sanitizePagination($page, $limit);
        $result = 0;
        if(!$exact){
            $term="%{$term}%";
        }
        if($stmt = $this->mysqli->prepare("SELECT count(*)"
                . "FROM library where "
                . "artist like ? or album like ? or note like ? or"
                . " Locale like ? or genre like ?")){
            $stmt->bind_param('sssss',$term,$term,$term,$term,$term);
            $stmt->execute();
            $stmt->bind_result($count);
            while($stmt->fetch()){
                $result+=$count;
            }
            $stmt->close();
        }
        else{
            //$result=["error"=>$mysqli->error];
            error_log($this->mysqli->error);
            return FALSE;
        }
        return $result;
    }

    public function importCSV($filename)
    {
        header('X-Accel-Buffering: no');
        
        // Turn off output buffering
        ini_set('output_buffering', 'off');
        // Turn off PHP output compression
        ini_set('zlib.output_compression', false);
                
        //Flush (send) the output buffer and turn off output buffering
        //ob_end_flush();
        while (@ob_end_flush());
                
        // Implicitly flush the buffer(s)
        ini_set('implicit_flush', true);
        ob_implicit_flush(true);

        $file = fopen($filename, "r");
        //for label
        if(!$stmt3 = $this->mysqli->prepare("INSERT IGNORE INTO recordlabel(Name,size)
            VALUES (?,?)")){
            $stmt3->close();
            header("location: ./?q=new&e=".$stmt3->errno."&s=3&m=".$stmt3->error);
        }

        while (($getData = fgetcsv($file, 10000, ",")) !== FALSE)
        {
            $getData = array_map('trim', $getData); // Trim leading/trailing whitespaces
            if($getData[1]=='' && $getData[2]=='')
                break;

            if($getData[0] == '' || $getData[1] == '' || $getData[2] == '')
                continue;


	    $labelName = $getData[3];
            $tempLabelName = strtoupper($labelName);
            if($tempLabelName == 'SR' ||$tempLabelName == 'SELF-RELEASED' ||$tempLabelName == 'S/R' ||$tempLabelName == 'INDEPENDENT')
                $labelName = 'Self-Released';

            $size = 1;
            if(!$stmt3->bind_param(
                "si",
                $labelName,    
                $size    
            )){
                $stmt3->close();
                return $this->mysqli->error;
            }
            if(!$stmt3->execute()){ 
                error_log("SQL-STMT Error (SEG-3):[".$this->mysqli->errno."] ".$this->mysqli->error);
                $error = [$this->mysqli->errno,$this->mysqli->error];
                return $this->mysqli->error;
            }
            $labels = \TPS\label::nameSearch($labelName);
            $labels = array_keys($labels);
            // $labels = $stmt3->insert_id;
            $genreKeys = array_keys(self::getLibraryGenres());
            $null = null;
            $dateIn = $getData[5] == '?'? $null:strtotime($getData[5]);
            $dateIn = date("Y-m-d", $dateIn);
            $dateRel = $getData[4] == '?'||''? $null:strtotime($getData[4]);
            $dateRel = date("Y-m-d", $dateRel);
            
            $canCon = $getData[21];
            $rating = substr_count($getData[10], "*");
            $note = substr($getData[13], 0,119);

	    $missing = False;
	    if (strpos(strtolower($note), 'missing from playlist') !== False)
		$missing = True;

            $locale = 'International';
            switch($getData[22])
            {   
                case 1: $locale = "Local"; break;
                case 2: $locale = "Province"; break;
                case 3: $locale = "Country"; break;
            }
             //Accept status and Playlist flag
            switch(strtoupper($getData[9]))
            {   
                case 'O': $accept = 1; $playlist_flag = 'Complete'; break;
                case 'DUPLICATE': $accept = 1; $playlist_flag = 'Complete'; break;
                case 'X': $accept = 0; $playlist_flag = 'False'; break;
                case 'L': $accept = 1; $playlist_flag = 'False'; break;
                default:  $accept = -1; $playlist_flag = 'False';
            }
            $year = $getData[18] == ''||!is_numeric($getData[18])? $null : $getData[18];
            
            if($getData[6] == '' && $getData[7]=='')
            {
                $genreKey = $null;
                $genre_num = 9;
            }
            //0~1200 rows, genre# is missing, so get it from genre name
            else if($getData[6] == '' || !is_numeric($getData[6]))
            {
                $genreName = strtolower($getData[7]);
                if(strpos($genreName, 'rock')!== false||strpos($genreName, 'pop')!== false)
                {
                    $genreKey = 'RP';
                    $genre_num = 0;
                }
                elseif(strpos($genreName, 'folk') !== false||strpos($genreName, 'roots') !== false)
                {
                    $genreKey = 'FR';
                    $genre_num = 1;
                }
                elseif(strpos($genreName, 'heavy') !== false||strpos($genreName, 'punk') !== false||
                    strpos($genreName, 'metal') !== false)
                {
                    $genreKey = 'HM';
                    $genre_num = 2;
                }
                elseif(strpos($genreName, 'electronic') !== false)
                {
                    $genreKey = 'EL';
                    $genre_num = 3;
                }
                elseif(strpos($genreName, 'hiphop') !== false)
                {
                    $genreKey = 'HH';
                    $genre_num = 4;
                }
                elseif(strpos($genreName, 'world') !== false)
                {
                    $genreKey = 'WD';
                    $genre_num = 5;
                }
                elseif(strpos($genreName, 'jazz') !== false||strpos($genreName, 'classical') !== false)
                {
                    $genreKey = 'JC';
                    $genre_num = 6;
                }
                elseif(strpos($genreName, 'experimental') !== false)
                {
                    $genreKey = 'EX';
                    $genre_num = 7;
                }
                else
                {
                    $genreKey = 'OT';
                    $genre_num = 8;
                }
            }
            else
            {
                $genreKey = $genreKeys[$getData[6]];
                $genre_num = $getData[6];
            }
            if(is_numeric($getData[16]))
                $playlistid = $getData[16]; 
            else
            {
                $playlistid = $null;
                $playlist_flag = 'False';
            }

	    $variousArtists = False;
	    $artist = $getData[1];
	    if ($artist == 'V/A')
		$variousArtists = True;

            $tags = [];
            if($getData[11] == 'o')
                array_push($tags, 'FemCon');

	    $hometowns = $getData[8];
	    if ($hometowns == '?')
		$hometowns = '';
	    $hometowns = explode("/", $hometowns);

            $format = $getData[12] == ''? 'CD':$getData[12];

            $subgenres = $getData[7] == ''? [] : explode('/', $getData[7]);
	    $subgenres = array_map('trim', $subgenres);

            $result = self::createAlbum($artist, $getData[2], $format, $genreKey, $genre_num, $labels, 
                $locale, $canCon, $playlist_flag, $null, $null, $note, $accept, $variousArtists,
                $dateIn, $dateRel, 1, $rating, $tags, $hometowns, $subgenres, [], $missing);

            if($playlist_flag == 'Complete' && $playlistid != $null)
            {
                $expire = $null;
                if((bool)strtotime($dateIn))
                    $expire = date('Y-m-d', strtotime($dateIn. " +180 days"));
                $playlist = new \TPS\playlist();
                $playlist->create($result, $dateIn, $expire, $null,$null ,$playlistid);
            }

            echo 'Inserting---- row: '.$getData[0].' RefCode: '.$result.' <br>';
            flush();    
        }
        $stmt3->close();
        fclose($file);  
        return true;
}

    /**
     * @abstract Get all key library information based on
     * given input and return in json format
     * all values that match the paramaters
     * Was GetLibraryFull()
     * @global \TPS\mysqli $mysqli
     * @param type $artist
     * @param type $album
     * @return boolean|array
     */
    function searchLibraryWithAlbum($artist, $album=NULL,$exact=FALSE){
        $this->mysqli;
        $result = array();
        $artist = urldecode($artist);
        if($album){
            $album = urldecode($album);
        }
        if($artist===Null){
            $artist='%';
        }
        elseif(!$exact){
            $artist="%{$artist}%";
        }
        if($album===Null){
            $album='%';
        }
        elseif(!$exact){
            $album="%{$album}%";
        }
        if($stmt = $this->mysqli->prepare("SELECT datein,dateout,RefCode,artist,album,"
                . "`format`,variousartists,`condition`,genre,`status`,"
                . "Locale,CanCon,updated,release_date,note,playlist_flag,year "
                . "FROM library where "
                . "artist like ? and album like ?")){
            $stmt->bind_param('ss',$artist,$album);
            $stmt->execute();
            $stmt->bind_result($datein,$dateout,$RefCode_q,
                    $artist_q,$album_q,$format,$variousartists,
                    $condition,$genre,$status,
                    $Locale,$CanCon,$updated,$release_date,
                    $note,$playlist_flag,$year);
            while($stmt->fetch()){
                array_push($result, array(
                    'datein'=>$datein,'dateout'=>$dateout,'RefCode'=>$RefCode_q,
                    'artist'=>$artist_q,'album'=>$album_q,'format'=>$format,
                    'variousartists'=>$variousartists,
                    'condition'=>$condition,'genre'=>$genre,'status'=>$status,
                    'Locale'=>$Locale,'CanCon'=>$CanCon,'updated'=>$updated,
                    'release_date'=>$release_date,
                    'note'=>$note,'playlist_flag'=>$playlist_flag,'year'=>$year
                ));
            }
            $stmt->close();
        }
        else{
            //$result=["error"=>$mysqli->error];
            error_log($this->mysqli->error);
            return FALSE;
        }
        return $result;
    }

    /**
     * @abstract provide basic list of all albums
     * @global \TPS\mysqli $mysqli
     * @return string|array
     * @todo add pagination
     */
    public function ListAll(){
        $this->mysqli;
        if(is_null($this->mysqli)){
            return '';#$mysqli = $GLOBALS['db'];
        }
        $result = [];
        $library = $this->mysqli->query(
                "SELECT RefCode,artist,album,status FROM library");
        while($result_temp = $library->fetch_array(MYSQLI_ASSOC)){
            array_push($result, $result_temp);
        }
        return $result;
    }


    /**
     * Takes a RefCode and returns an array with all library information included
     * @global \TPS\mysqli $mysqli
     * @param type $term
     * @return array|boolean
     * @todo Optomize
     */
    public function GetFullAlbum($term){
        $this->mysqli;
        $maxResult = 100;
	$selectAlbum = "SELECT " .
			"library.RefCode, " .
			"if(band_websites.ID is NULL,'No','Yes') as hasWebsite, " .
			"if(review.id is NULL,0,1) as reviewed, " .
			"library.Locale, " .
			"library.variousartists, " .
			"library.format, " .
			"library.year, " .
			"library.album, " .
			"library.artist, " .
			"library.CanCon, " .
			"library.datein, " .
			"library.playlist_flag, " .
			"library.genre " .
		"FROM " .
			"library " .
			"left join review on library.RefCode = review.RefCode " .
			"left join band_websites on library.RefCode=band_websites.ID " .
		"WHERE " .
			"library.refcode = ? order by library.datein asc limit ?;";
        $selectWebsites = "Select band_websites.URL, band_websites.Service, band_websites.date_available, band_websites.date_discontinue"
                . " from band_websites where band_websites.ID=?;";
        $params = array();
        if($stmt = $this->mysqli->prepare($selectAlbum)){
            $stmt->bind_param('si',$term,$maxResult);
            $stmt->execute();
            $stmt->bind_result($RefCode,$hasWebsite,$reviewed,$locale,$variousArtists,$format,$year,$album,$artist,$canCon,$datein,$playlist_flag,$genre);
            while($stmt->fetch()){
                $params['album'] = array( // this is ok as if the review ID is null there will also be no other entries as ID is a PK
                        "RefCode"=>$RefCode,
                        "hasWebsite"=>$hasWebsite,
                        "hasReview"=>$reviewed,
                        "format"=>$format,
                        "year"=>$year,
                        "album"=>$album,
                        "artist"=>$artist,
                        "CanCon"=>$canCon,
                        "datein"=>$datein,
                        "playlist"=>$playlist_flag,
                        "genre"=>$genre,
                        "locale"=>$locale,
                        "variousArtists"=>$variousArtists,
			"labels"=>[]
                    );
            }
            $stmt->close();
	    $labels = $this->getLabelsByRefCode($RefCode);
	    foreach($labels as $label) {
		$label_info = array(
		    "Name"=>$label['Name'],
		    "Id"=>$label['LabelNumber']
		);
		array_push($params['album']['labels'], $label_info);
	    }
        }
        else{
            $params['error']=$mysqli->error;
        }
        if($bands = $this->mysqli->prepare($selectWebsites)){
            $websites = array();
            $bands->bind_param('i',$term);
            $bands->execute();
            $bands->bind_result($url,$service,$available,$discontinue);
            while($bands->fetch()){
                $websites[$service]=array(
                    "url" => $url,
                    "available" => $available,
                    "discontinue" => $discontinue);
            }
            $bands->close();
        }
        else{
            error_log($this->mysqli->errno.": ".$this->mysqli->error);
            $params['error']=$this->mysqli->error;
        }
        $params['websites']=$websites?:NULL;
        return $params;
    }


    public function updateAlbumAttribute($attName, $attValueList, $RefCode, $idCol='id', $nameCol='name') {
	$attNameSingular = substr($attName, -1) == 's' ? substr($attName, 0, -1) : $attName;
	if(!is_null($attValueList) && count($attValueList) > 0) {
	    // Check which {$attName}s are already in the database & get their ids
	    $sql=$this->mysqli->query("SELECT * FROM {$attName} WHERE name IN ('" . implode("', '", $attValueList) . "')");
	    $results = [];
	    while($row = $sql->fetch_array(MYSQLI_ASSOC))
	        array_push($results, $row);
	    $ids = array_fill(0, sizeof($attValueList), NULL); // Parallel array of db id for each {$attName}
	    $in_db = [];
	    foreach($results as $result) {
	        $ids[array_search($result[$nameCol], $attValueList)] = $result[$idCol];
		array_push($in_db, $result[$nameCol]);
	    }

	    // Determine which {$attName}s need to be added to the `{$attName}s` table
	    $to_add_to_db = array_diff($attValueList, $in_db);

	    // Insert all {$attName}s into the ${attName}s table that aren't already in there
	    if(sizeof($to_add_to_db) > 0) {
		// Insert new {$attName}s into `{$attName}s` table
	    	$this->mysqli->query("INSERT INTO {$attName} (name) VALUES ('" . implode("'), ('", $to_add_to_db) . "')");

	        // Complete the list of {$attName} id's for this album
	        $sql = $this->mysqli->query("SELECT LAST_INSERT_ID()");
	        $last_insert_id = $sql->fetch_array(MYSQLI_ASSOC)['LAST_INSERT_ID()'];
		foreach($ids as $i=>$id)
		    if(is_null($id))
			$ids[$i] = $last_insert_id++;
	    }

	    // Determine which ${attName}s have been added to the album in the UI
	    $add_to_album = [];
	    $sql = $this->mysqli->query("SELECT {$nameCol} FROM {$attName} WHERE {$idCol} IN (SELECT {$attNameSingular}_{$idCol} FROM library_{$attName} WHERE library_RefCode={$RefCode})");
	    $in_db_for_this_album = [];
	    while($row = $sql->fetch_array(MYSQLI_ASSOC))
		array_push($in_db_for_this_album, $row[$nameCol]);
	    $added_in_ui = array_diff($attValueList, $in_db_for_this_album);

	    if(sizeof($added_in_ui)>0) {
	        // Determine database ids for the added {$attName}s in the UI
	        $sql = $this->mysqli->query("SELECT $idCol FROM {$attName} WHERE $nameCol IN ('" . implode("', '", $added_in_ui) . "')");
	        $ids_of_added_in_ui = [];
		while($row = $sql->fetch_array(MYSQLI_ASSOC))
		    array_push($ids_of_added_in_ui, $row[$idCol]);

		// Insert new library/{$attName} combos into intermediary table
	        $values = "";
	        foreach($ids_of_added_in_ui as $id)
	            $values = $values . "(" . $RefCode  .  ", " . $id  . "), ";
	        $values = substr($values, 0, strlen($values)-2); // Remove trailing comma
	        $this->mysqli->query("INSERT INTO library_{$attName} (library_RefCode, {$attNameSingular}_{$idCol}) VALUES " . $values);
	    }
	}

	// Determine which {$attName}s have been removed from the album in the UI
	$sql=$this->mysqli->query("SELECT * FROM {$attName} WHERE {$idCol} IN (SELECT {$attNameSingular}_{$idCol} FROM library_{$attName} WHERE library_RefCode={$RefCode})");
	$assigned_in_db = [];
	while($row = $sql->fetch_array(MYSQLI_ASSOC))
	    $assigned_in_db[$row[$idCol]] = $row[$nameCol];
	$to_remove_from_int_table = is_null($attValueList) ? $assigned_in_db : array_diff($assigned_in_db, $attValueList);

	// Remove {$attName}s from album if needed
	if(sizeof($to_remove_from_int_table) > 0) {
	    // Delete {$attName} from intermediary table if user removed them in the UI
	    $this->mysqli->query("DELETE FROM library_{$attName} WHERE library_RefCode={$RefCode} " .
	  	                 "AND {$attNameSingular}_{$idCol} IN (" . implode(", ", array_keys($to_remove_from_int_table)) . ")");
	}

	// Get all of the {$attNames}s being used by albums
	$sql = $this->mysqli->query("SELECT {$attName}.{$idCol} FROM {$attName} RIGHT JOIN library_{$attName} " .
				        "ON library_{$attName}.{$attNameSingular}_{$idCol}={$attName}.{$idCol} GROUP BY {$attName}.{$idCol}");
	$ids_being_used = [];
	while($row = $sql->fetch_array(MYSQLI_ASSOC))
	    array_push($ids_being_used, $row[$idCol]);

	// Get all the {$attName}s in the database
	$sql = $this->mysqli->query("SELECT {$idCol} FROM {$attName}");
	$all = [];
	while($row = $sql->fetch_array(MYSQLI_ASSOC))
	    array_push($all, $row[$idCol]);

	// Delete any dangling {$attName}s from `{$attName}s` table if no albums use it anymore
	$to_delete = array_diff($all, $ids_being_used);
	if(sizeof($to_delete)>0)
	    $this->mysqli->query("DELETE FROM {$attName} WHERE {$idCol} IN (" . implode(", ", $to_delete) . ")");
    }

    public function mysql_escape($val) {
	return mysqli_real_escape_string($this->mysqli, stripslashes($val));
    }

    /*
    * @abstract Add values to the attributes of an album. For example, add 'Good' and 'Mind-blowing' as tags to a given album.
    * @param string $attName       Name of the attribute you want to add values to
    * @param array  $attValueList  Values to add to the album for the given attribute
    * @parar int    $refcode       Refcode of the Album
    */
    public function addAttributeToAlbum($attName, $attValueList, $refcode) {
	$attValueList = array_diff($attValueList, ['']); // Remove any blanks
	if(count($attValueList)>0) {
	    // Check which {$attName}s are already in the database
	    $temp = $attValueList;
	    $attValueList = array_map(array($this, 'mysql_escape'), $temp);
	    $sql=$this->mysqli->query("SELECT * FROM {$attName}s WHERE name IN ('" . implode("', '", $attValueList) . "')");
	    $results = [];
	    while($result_temp = $sql->fetch_array(MYSQLI_ASSOC))
		array_push($results, $result_temp);
	    $ids = array_fill(0, sizeof($attValueList), NULL); // Parallel array of db id for each {$attName}
	    foreach($results as $result)
		$ids[array_search($result['name'], $attValueList)] = $result['id'];

	    // Insert all {$attName}s into the {$attName}s table that aren't already in there
	    $to_add = [];
	    foreach($ids as $index => $id)
		if(is_null($id))
		    array_push($to_add, $attValueList[$index]);

	    if(sizeof($to_add)>0) {
		$temp = $to_add;
		$to_add = array_map(array($this, 'mysql_escape'), $temp);
		$this->mysqli->query("INSERT IGNORE INTO {$attName}s (name) VALUES ('" . implode("'), ('", $to_add) . "')");

	        // Gather all {$attName} id's for this album
		$sql = $this->mysqli->query("SELECT LAST_INSERT_ID()");
		$last_insert_id = $sql->fetch_array(MYSQLI_ASSOC)['LAST_INSERT_ID()'];
		foreach($ids as $i=>$id)
		    if(is_null($id))
			$ids[$i] = $last_insert_id++;
	    }

	    // Insert library/{$attName} combos into intermediary table
	    $values = "";
	    foreach($ids as $id)
		$values = $values . "(" . $refcode  .  ", " . $id  . "), ";
	    $values = substr($values, 0, strlen($values)-2); // Remove trailing comma
	    $this->mysqli->query("INSERT IGNORE INTO library_${attName}s (library_RefCode, ${attName}_id) VALUES " . $values);
	}
    }


    public function createAlbum($artist, $album, $format, $genre, $genre_num, $labelNums, $locale, $CanCon, $playlist,
                                $governmentCategory, $schedule, $note="", $accepted=1, $variousartists=False,
                                $datein=null, $release_date=null, $print=1, $rating=null, $tags=[], $hometowns=[],
				$subgenres=[], $tracklist=[], $missing=false){
	$this->formatArtistName($artist);
        if(is_null($datein)){
            $datein = date("Y-m-d");
        }
        if(!$stmt3 = $this->mysqli->prepare("REPLACE INTO library(datein,artist,album,variousartists,
            format,genre,status,Locale,CanCon,release_date,year,note,playlist_flag,
            governmentCategory,scheduleCode, rating, library_code, missing)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")){
            $stmt3->close();
            header("location: ./?q=new&e=".$stmt3->errno."&s=3&m=".$stmt3->error);
        }
        if(!is_null($release_date)){
            $year = date('Y',strtotime($release_date));
        }
        else{
            $year = NULL;
        }
        $library_code = 0;
        if(!$stmt3->bind_param(
            "sssissisissssssiii",
            $datein,
            $artist,
            $album,
            $variousartists,
            $format,
            $genre,
            $accepted,
            $locale,
            $CanCon,
            $release_date,
            $year,
            $note,
            $playlist,
            $governmentCategory,
            $schedule,
	    $rating,
            $library_code,
	    $missing
        )){
            $stmt3->close();
            return $this->mysqli->error;
        }

        if(!$stmt3->execute()){
            error_log("SQL-STMT Error (SEG-3):[".$this->mysqli->errno."] ".$this->mysqli->error);
            $error = [$this->mysqli->errno,$this->mysqli->error];
            $stmt3->close();
            return $this->mysqli->error;
        }
        else{
            $url = "";
            $service = "";
            $id_last = $stmt3->insert_id;
            $stmt3->close();
            if($stmt4=$this->mysqli->prepare("INSERT INTO band_websites (ID,URL,Service) VALUES (?,?,?)")){
                $stmt4->bind_param("iss",$id_last,$url,$service);
                $services=[
                    "twitter"=>filter_input(INPUT_POST, 'twitter',FILTER_SANITIZE_URL),
                    "facebook"=>filter_input(INPUT_POST, 'facebook',FILTER_SANITIZE_URL),
                    "bandcamp"=>filter_input(INPUT_POST, 'bandcamp',FILTER_SANITIZE_URL),
                    "soundcloud"=>filter_input(INPUT_POST, 'soundcloud',FILTER_SANITIZE_URL),
                    "website"=>filter_input(INPUT_POST, 'website',FILTER_SANITIZE_URL)
                ];
                if(strpos($services["bandcamp"], "soundcloud.com")&&(is_null($services['soundcloud'])||$service['soundcloud']==''))
                {
                    // if soundcloud is in the bandcamp URL, reassign it to soundcloud
                    $services["soundcloud"] = $services["bandcamp"];
                    $services["bandcamp"] = NULL;
                }
                foreach($services as $key=>$value){
                    $url=$value;
                    $service=$key;
                    if($value!=""&&!is_null($value)){
                        if(!$stmt4->execute()){
                            error_log($this->mysqli->error);
                        }
                    }
                }
            }
            else{
                error_log($this->mysqli->error);
            }

	    // Insert library code with leading genre number
	    $library_code = "{$genre_num}-{$id_last}";
	    $this->mysqli->query("UPDATE library SET library_code='{$library_code}' WHERE RefCode={$id_last}");

	    if(sizeof($labelNums)>0) {
		// Insert album and record label combos into library_recordlabel intermediary table
		$values = "";
		foreach($labelNums as $labelNum)
		    $values = $values . "(" . $id_last  .  ", " . $labelNum  . "), ";
		$values = substr($values, 0, strlen($values)-2); // Remove trailing comma
		$this->mysqli->query("INSERT INTO library_recordlabel (library_RefCode, recordlabel_LabelNumber) VALUES " . $values);
	    }

	    $this->addAttributeToAlbum("hometown", $hometowns, $id_last);
	    $this->addAttributeToAlbum("tag", $tags, $id_last);
	    $this->addAttributeToAlbum("subgenre", $subgenres, $id_last);

	    $this->addTracklist($tracklist, $id_last);

            if(strtolower(substr($artist,-1))!='s'){
                $s = "s";
            }
            else{
                $s="";
            }
            if($print==1){
                $_SESSION['PRINTID'][]=["RefCode"=>$id_last];
            }
        }
        return $id_last;
    }
    /*
    * @author Derek Melchin
    * @abstract Add a tracklist to an album
    * @param array $tracklist List of track names for the album
    * @param int   $RefCode   Reference code of the album
    */
    public function getTracklistByRefCode($RefCode) {
	$stmt = $this->mysqli->query("SELECT trackName FROM tracklists WHERE library_RefCode=$RefCode ORDER BY trackNum ASC;");
	$tracks = [];
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC))
	    array_push($tracks, $row['trackName']);
	return $tracks;
    }

    /*
    * @author Derek Melchin
    * @abstract Add a tracklist to an album
    * @param array $tracklist List of track names for the album
    * @param int   $RefCode   Reference code of the album
    */
    public function addTracklist($tracklist, $RefCode) {
	if (count($tracklist) == 0)
	    return;

	// Sanitize strings to prevent SQL injection
	$this->sanitizeStrings($tracklist);

	// Create insertion rows string
	$values = "";
	foreach ($tracklist as $i => $trackName) {
	    if ($i > 0)
		$values .= ",";
	    $values .= "($RefCode, " . ($i + 1) . ", '$trackName')";
	}

	// Insert tracklist into database
	$this->mysqli->query(
		  "INSERT INTO tracklists "
			. "(library_RefCode, trackNum, trackName) "
		. "VALUES "
			. "$values;"
	);
    }

    /*
    * @author Derek Melchin
    * @abstract Updates the tracklist of an album
    * @param array $tracklist List of track names for the album
    * @param int   $RefCode   Reference code of the album
    */
    public function updateTracklist($tracklist, $RefCode) {
	$this->mysqli->query("DELETE FROM tracklists WHERE library_RefCode=$RefCode");
	$this->addTracklist($tracklist, $RefCode);
    }

    /*
    * @abstract When given an artist name, format it by placing 'The', 'A', and 'An' at the end of the name, preceeded by a comma
    * @param string $name Name of the artist
    * @return N/A $name is passed by reference
    */
    public function formatArtistName(&$name) {
	$words = explode(" ", $name);
	if (count($words) > 1) {
	    $firstWord = strtoupper($words[0]);
	    $secondLastWord = $words[count($words) - 2];
	    $lastWord = strtoupper($words[count($words) - 1]);

	    $magicWords = ['THE', 'AN', 'A'];
	    $alreadyFormated = substr($secondLastWord, -1) == ',' && in_array($lastWord, $magicWords);
	    if (!$alreadyFormated && in_array($firstWord, $magicWords)) {
		$newFormat = array_slice($words, 1);
		array_push($newFormat, $words[0]); // Move first word to the end
		$newFormat[count($words) - 2] .= ','; // Add comma to second last word
		$name = join(" ", $newFormat); // Update the artist name reference
	    }
	}
    }
}
