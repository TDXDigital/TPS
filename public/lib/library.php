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
require_once 'station.php';

class library extends station{
    
    public function __construct(){
        parent::__construct();
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
     * @abstract returns static associative array of genres
     * @todo implement database storage
     * @return array
     * @version 0.1
     */
    public function getLibraryGenres(){
        $genres = array(
                "RP" => "Rock/Pop",
                "FR" => "Folk/Roots",
                "EL" => "Electronic",
                "EX" => "Experimental",
                "JC" => "Jazz/Classical",
                "HH" => "Hip-Hop",
                "HM" => "Heavy/Punk/Metal",
                "WD" => "World",
                "OT" => "Other",
            );
        return $genres;
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
                "Digital"=>"Digital",
                "12in" => "12\"",
                "10in" => "10\"",
                "7in" => "7\"",
                "Cass" => "Cassette",
                "Cart"=>"Fidelipac (cart)",
                "MD" => "Mini Disc",
                "Other"=>"Other"
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
            "M" => "Daytime1  [06:00-12:00]",
            "D" => "Daytime2  [12:00-18:00]",
            "E" => "Evening   [18:00-00:00]",
            "N" => "Nighttime [00:00-06:00]",
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
                . "`format`,variousartists,`condition`,genre,`status`,labelid,"
                . "Locale,CanCon,updated,release_date,note,playlist_flag,governmentCategory,"
                . "scheduleCode "
                . "FROM library where "
                . "Refcode = ?")){
            $stmt->bind_param('s',$refcode);
            $stmt->execute();
            $stmt->bind_result($barcode,$year,$datein,$dateout,$RefCode_q,
                    $artist_q,$album_q,$format,$variousartists,
                    $condition,$genre,$status,$labelid,
                    $Locale,$CanCon,$updated,$release_date,
                    $note,$playlist_flag,$govCat,$scCode);
            while($stmt->fetch()){
                array_push($result, array(
                    'barcode'=>$barcode,'year'=>$year,
                    'datein'=>$datein,'dateout'=>$dateout,'RefCode'=>$RefCode_q,
                    'artist'=>$artist_q,'album'=>$album_q,'format'=>$format,
                    'variousartists'=>$variousartists,
                    'condition'=>$condition,'genre'=>$genre,'status'=>$status,
                    'labelid'=>$labelid,
                    'Locale'=>$Locale,'CanCon'=>$CanCon,'updated'=>$updated,
                    'release_date'=>$release_date,
                    'note'=>$note,'playlist_flag'=>$playlist_flag,
                    'governmentCategory'=>$govCat,'scheduleCode'=>$scCode,
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
     * @return boolean|array
     */
    public function searchLibrary($term,$exact=False){
        $this->mysqli;
        $result = array();
        if(!$exact){
            $term="%{$term}%";
        }
        if($stmt = $this->mysqli->prepare("SELECT datein,dateout,RefCode,artist,album,"
                . "`format`,variousartists,`condition`,genre,`status`,labelid,"
                . "Locale,CanCon,updated,release_date,note,playlist_flag,year "
                . "FROM library where "
                . "artist like ? or album like ? or note like ? or"
                . " Locale like ? or genre like ?")){
            $stmt->bind_param('sssss',$term,$term,$term,$term,$term);
            $stmt->execute();
            $stmt->bind_result($datein,$dateout,$RefCode_q,
                    $artist_q,$album_q,$format,$variousartists,
                    $condition,$genre,$status,$labelid,
                    $Locale,$CanCon,$updated,$release_date,
                    $note,$playlist_flag,$year);
            while($stmt->fetch()){
                array_push($result, array(
                    'datein'=>$datein,'dateout'=>$dateout,'RefCode'=>$RefCode_q,
                    'artist'=>$artist_q,'album'=>$album_q,'format'=>$format,
                    'variousartists'=>$variousartists,
                    'condition'=>$condition,'genre'=>$genre,'status'=>$status,
                    'labelid'=>$labelid,
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
                . "`format`,variousartists,`condition`,genre,`status`,labelid,"
                . "Locale,CanCon,updated,release_date,note,playlist_flag,year "
                . "FROM library where "
                . "artist like ? and album like ?")){
            $stmt->bind_param('ss',$artist,$album);
            $stmt->execute();
            $stmt->bind_result($datein,$dateout,$RefCode_q,
                    $artist_q,$album_q,$format,$variousartists,
                    $condition,$genre,$status,$labelid,
                    $Locale,$CanCon,$updated,$release_date,
                    $note,$playlist_flag,$year);
            while($stmt->fetch()){
                array_push($result, array(
                    'datein'=>$datein,'dateout'=>$dateout,'RefCode'=>$RefCode_q,
                    'artist'=>$artist_q,'album'=>$album_q,'format'=>$format,
                    'variousartists'=>$variousartists,
                    'condition'=>$condition,'genre'=>$genre,'status'=>$status,
                    'labelid'=>$labelid,
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
        $selectAlbum = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite,if(recordlabel.name_alias_duplicate is NULL, recordlabel.Name, "
                . "(SELECT Name from recordlabel where LabelNumber = recordlabel.name_alias_duplicate) ) as recordLabel, "
                . "if(review.id is NULL,0,1) as reviewed, library.labelid, library.Locale, library.variousartists, library.format, library.year, library.album, "
                . "library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre, "
                . "review.reviewer, review.ts, review.approved, review.femcon, review.hometown, review.subgenre, review.description, review.recommendations, review.id "
                . "from library left join review on library.RefCode = review.RefCode left join recordlabel on library.labelid = recordlabel.LabelNumber left join band_websites on library.RefCode=band_websites.ID where "
                . "library.refcode = ? order by library.datein asc limit ?;";
        $selectWebsites = "Select band_websites.URL, band_websites.Service, band_websites.date_available, band_websites.date_discontinue"
                . " from band_websites where band_websites.ID=?;";
        $params = array();
        if($stmt = $this->mysqli->prepare($selectAlbum)){
            $stmt->bind_param('si',$term,$maxResult);
            $stmt->execute();
            $stmt->bind_result($RefCode,$hasWebsite,$recordLabel,$reviewed,$labelid,$locale,$variousArtists,$format,$year,$album,$artist,$canCon,$datein,$playlist_flag,$genre,
                    $reviewer,$timestamp,$approved,$femcon,$hometown,$subgenre,$description,$recommends,$reviewID);
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
                        "label"=>array(
                            "Name"=>$recordLabel,
                            "Id"=>$labelid,
                        ),
                    );
            }
            $stmt->close();
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
}