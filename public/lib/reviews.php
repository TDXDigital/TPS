<?php
namespace TPS;
/** 
 * @abstract contains all functions and methods related to reviews
 * @version 1.0
 * @author James Oliver <support@ckxu.com>
 * @license https://raw.githubusercontent.com/TDXDigital/TPS/master/LICENSE MIT
 */
class reviews{
    /**
     * 
     * @global type $mysqli
     * @version 1.0
     */
    public function __construct() {
        global $mysqli;
    }
    
    protected $error;

    /**
     * @access private
     * @param int $pagination current page index
     * @param int $maxResult number of items to in response
     */
    private function sanitizePagination(&$pagination,&$maxResult){
        if( !is_int($maxResult) || $maxResult > 1000):
            $maxResult = 1000;
        endif;
        if( !is_int($pagination)):
            $pagination = 1;
        endif;
        $floor = abs(($pagination*$maxResult))-($maxResult+1);
        $ceil = abs(($pagination*$maxResult));
        // Simply for security. should never happen
        if ($floor < 0):
            $floor=0;
        endif;
        if($ceil < 0):
            $ceil = abs($ceil);
        endif;
        $pagination = $floor;
        $maxResult = $ceil;
    }
    
    /**
     * 
     * @abstract returns an array of Albums
     * @global type $mysqli
     * @access public
     * @version 1.0
     * @author James Oliver <support@ckxu.com>
     * @param int $pagination current page index
     * @param int $maxResult number of items to in response
     * @return array list of albums, False on error
     */
    public function getAlbumList($pagination=1,$maxResult=25){
        global $mysqli;
        $this->sanitizePagination($pagination,$maxResult);
        //print ($floor.":".$ceil);
        $select = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite, library.format, library.year, library.album,library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre from library left join (SELECT * from review where (approved = 1 or approved is null) ) b on library.RefCode = b.RefCode left join band_websites on library.RefCode=band_websites.ID where b.id is NULL and library.status=1 order by library.datein asc limit ?,?;";
        $albums = array();
        if($stmt = $mysqli->prepare($select)){
            $stmt->bind_param('ii',$pagination,$maxResult);
            $stmt->execute();
            $stmt->bind_result($RefCode,$hasWebsite,$format,$year,$album,$artist,$canCon,$datein,$playlist_flag,$genre);
            while($stmt->fetch()){
                $albums[$RefCode] = array(
                        "format"=>$format,
                        "hasWebsite"=>$hasWebsite,
                        "year"=>$year,
                        "album"=>$album,
                        "artist"=>$artist,
                        "CanCon"=>$canCon,
                        "datein"=>$datein,
                        "playlist"=>$playlist_flag,
                        "genre"=>$genre,
                    );
            }
            $stmt->close();
        }
        else{
            error_log($mysqli->error);
            return FALSE;
        }
        return $albums;
    }
    
    /**
     * @access public
     * @version 1.0
     * @author James Oliver <support@ckxu.com>
     * @global type $mysqli
     * @param type $RefCode Album RefCode
     * @param type $pagination page index
     * @param type $maxResult max number of items to return
     * @return array false if error, array of reviews otherwise
     */
    public function getReviewsByAlbum($RefCode, $pagination=1,$maxResult=25) {
        global $mysqli;
        $this->sanitizePagination($pagination,$maxResult);
        $select = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite, if(recordlabel.name_alias_duplicate is NULL, recordlabel.Name, "
                . "(SELECT Name from recordlabel where LabelNumber = recordlabel.name_alias_duplicate) ) as recordLabel, "
                . "if(review.id is NULL,0,1) as reviewed, library.labelid, library.Locale, library.variousartists, library.format, library.year, library.album, "
                . "library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre, "
                . "review.reviewer, review.ts, review.approved, review.femcon, review.hometown, review.subgenre, review.description, review.recommendations, review.id "
                . "from library left join review on library.RefCode = review.RefCode left join recordlabel on library.labelid = recordlabel.LabelNumber "
                . "left join band_websites on library.RefCode=band_websites.ID where "
                . "library.refcode = ? order by library.datein asc limit ?,?;";
        $params = array();
        if($stmt = $mysqli->prepare($select)){
            $stmt->bind_param('sii',$RefCode,$pagaination,$maxResult);
            $stmt->execute();
            $stmt->bind_result($RefCode,$hasWebsite,$recordLabel,$reviewed,$labelid,$locale,$variousArtists,$format,$year,$album,$artist,$canCon,$datein,$playlist_flag,$genre,
                    $reviewer,$timestamp,$approved,$femcon,$hometown,$subgenre,$description,$recommends,$reviewID);
            while($stmt->fetch()){
                $params[$reviewID] = array( // this is ok as if the review ID is null there will also be no other entries as ID is a PK
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
                        "review"=>array(
                            "reviewer"=>$reviewer,
                            "timestamp"=>$timestamp,
                            "approved"=>$approved,
                            "femcon"=>$femcon,
                            "hometown"=>$hometown,
                            "subgenre"=>$subgenre,
                            "description"=>$description,
                            "recommendations"=>$recommends,
                        )
                    );
            }
            if($mysqli->error){
                error_log($mysqli->error);
            }
            $stmt->close();
        }
        else{
            error_log($mysqli->error);
            return FALSE;
        }
        return $params;
    }
    
    /**
     * 
     * @global type $mysqli
     * @param type $term
     * @param type $pagination
     * @param type $maxResult
     * @param type $exactSearch provide true to prevent wildcard search
     * @return array reviews based on search term, false on error
     */
    public function getAlbumReviews($term,$pagination=1,$maxResult=25,$exactSearch=FALSE){
        global $mysqli;
        $orig_term = $term;
        $term = urldecode($term);
        if(!$exactSearch){
            $term = '%'.$term.'%';
        }
        $this->sanitizePagination($pagination,$maxResult);
        $select = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite, if(b.id is NULL,'No', 'Yes') as reviewed, library.format, library.year, library.album,library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre from library left join (SELECT * from review where (approved = 1 or approved is null) ) b on library.RefCode = b.RefCode left join band_websites on library.RefCode=band_websites.ID where (library.refcode like ? or library.album like ? or library.artist like ? or library.year = ? or library.datein = ?) order by library.datein asc limit ?,?;";
        $albums = array();
        if($stmt = $mysqli->prepare($select)){
            $stmt->bind_param('sssssii',$term,$term,$term,$orig_term,$orig_term,$pagination,$maxResult);
            $stmt->execute();
            $stmt->bind_result($RefCode,$hasWebsite, $reviewed, $format,$year,$album,$artist,$canCon,$datein,$playlist_flag,$genre);
            while($stmt->fetch()){
                $albums[$RefCode] = array(
                        "format"=>$format,
                        "hasWebsite"=>$hasWebsite,
                        "reviewed"=>$reviewed,
                        "year"=>$year,
                        "album"=>$album,
                        "artist"=>$artist,
                        "CanCon"=>$canCon,
                        "datein"=>$datein,
                        "playlist"=>$playlist_flag,
                        "genre"=>$genre,
                    );
            }
            $stmt->close();
        }
        else{
            error_log($mysqli->error);
            return FALSE;
        }
        return $albums;
    }
    
    /**
     * 
     * @todo Needs rebuilt
     * @global type $mysqli
     * @param string $term search term
     * @param type $exact removes wildcard search
     * @return array
     */
    public function getReview($term,$exact=TRUE){
        global $mysqli;
        if(!$exact):
            $term = "%".$term."%";            
        endif;
        $maxResult = 100;
        $select = "Select library.RefCode, if(review.id is NULL,'No', 'Yes') as reviewed, library.format, library.year, library.album,library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre from library left join review on library.RefCode = review.RefCode where review.id is NULL and (library.refcode like ? or library.year like ? or library.album like ? or library.artist like ? or library.datein like ?) order by library.datein asc limit ?;";
        $params = array();
        if($stmt = $mysqli->prepare($select)){
            $stmt->bind_param('sssssi',$term,$term,$term,$term,$term,$maxResult);
            $stmt->execute();
            $stmt->bind_result($RefCode,$reviewed, $format,$year,$album,$artist,$canCon,$datein,$playlist_flag,$genre);
            while($stmt->fetch()){
                $params[$RefCode] = array(
                        "format"=>$format,
                        "reviewed"=>$reviewed,
                        "year"=>$year,
                        "album"=>$album,
                        "artist"=>$artist,
                        "CanCon"=>$canCon,
                        "datein"=>$datein,
                        "playlist"=>$playlist_flag,
                        "genre"=>$genre,
                    );
            }
            $stmt->close();
        }
        else{
            print $mysqli->error;
        }
        return $params;
    }
    
    /**
     * 
     * @global type $mysqli
     * @param type $pagination
     * @param type $maxResult
     * @return array albums without reviews
     */
    public function getAvailableReviews($pagination=1,$maxResult=25){
        global $mysqli;
        $this->sanitizePagination($pagination, $maxResult);
        $select = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite, library.format, library.year, library.album,library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre from library left join (SELECT * from review where (approved = 1 or approved is null) ) b on library.RefCode = b.RefCode left join band_websites on library.RefCode=band_websites.ID where b.id is NULL and library.status=1 order by library.datein asc limit ?,?;";
        $albums = array();
        if($stmt = $mysqli->prepare($select)){
            $stmt->bind_param('ii',$pagination,$maxResult);
            $stmt->execute();
            $stmt->bind_result($RefCode,$hasWebsite,$format,$year,$album,$artist,$canCon,$datein,$playlist_flag,$genre);
            while($stmt->fetch()){
                $albums[$RefCode] = array(
                        "format"=>$format,
                        "hasWebsite"=>$hasWebsite,
                        "year"=>$year,
                        "album"=>$album,
                        "artist"=>$artist,
                        "CanCon"=>$canCon,
                        "datein"=>$datein,
                        "playlist"=>$playlist_flag,
                        "genre"=>$genre,
                    );
            }
            $stmt->close();
        }
        else{
            error_log($mysqli->error);
            return FALSE;
        }
        return $albums;
    }

    /**
     * @author James Oliver <support@ckxu.com>
     * @abstract expects post to contain 'description', 'femcon'\
     *  'notes', 'reviewer', 'hometown', 'subgenres', 'recommend'
     * @param slim $app Slim application with request()
     * @param int $RefCode required integer of album
     * @return boolean False on error
     */
    public function createReview(&$app,$RefCode){
        global $mysqli;
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_raw = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_raw = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip_raw = $_SERVER['REMOTE_ADDR']?:NULL;
        }
        if(isset($ip_raw) && filter_var($ip_raw, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
            $ip = ip2long($ip_raw);
        }
        else{
            $ip=NULL;
        }
        $description = $app->request()->post('description');
        $notes = $app->request()->post('notes');
        $reviewer = $app->request()->post('reviewer');
        $hometown = $app->request()->post('hometown');
        $subgenres = $app->request()->post('subgenres');
        $recommend = $app->request()->post('recommend');
        $femcon = $app->request()->post('femcon');
        $newReviewSql = "INSERT INTO review (RefCode,reviewer,femcon,hometown,subgenre,ip,description,recommendations,notes) "
                . "VALUES (?,?,?,?,?,?,?,?,?)";
        if($stmt = $mysqli->prepare($newReviewSql)){
            $stmt->bind_param('isissssss',$RefCode,$reviewer,$femcon,$hometown,
                    $subgenres,$ip,$description,$recommend,$notes);
            if($stmt->execute()){
                $app->flash('success',"Review submitted for album #$RefCode");
                return TRUE;
            }
            else{
                $app->flash('error','Review could not be stored, '.$mysqli->error);
                return FALSE;
            }
        }
        else{
            $app->flash('error',$mysqli->error);
            return FALSE;
        }
        return FALSE; #should not be needed
    }
    
    public function setPrintLabel($RefCode){
        if($_SESSION['ReviewLables'][] = $RefCode){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
    
    
    public function clearPrintLabel($RefCode){
        if(array_key_exists($RefCode, $_SESSION['ReviewLabels'])){
            $_SESSION['ReviewLabels'] = NULL;
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
    
    public function clearPrintLabels(){
        try {
            unset($_SESSION['ReviewLabels']);
        } catch (Exception $ex) {
            return FALSE;
        }
    }
    
    public function getPrintLables(){
        if(isset($_SESSION['ReviewLabel'])){
            return $_SESSION['ReviewLabesl'];
        }
        else{
            return FALSE;
        }
    }
}