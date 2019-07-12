<?php
namespace TPS;
/** 
 * @abstract contains all functions and methods related to reviews
 * @version 1.0
 * @author James Oliver <support@ckxu.com>
 * @license https://raw.githubusercontent.com/TDXDigital/TPS/master/LICENSE MIT
 */

require_once 'station.php';
class reviews extends station{
    /**
     * 
     * @global type $mysqli
     * @version 1.0
     */
    public function __construct() {
        parent::__construct();
	$this->library = new \TPS\library();
    }
    
    protected $error;


    /*
    * @abstract Returns an array of review ids for a given album
    * @author Derek Melchin
    * @param int $refCode RefCode of the album
    * @return array List of review ids
    */
    public function getReviewIdsByRefCode($refCode) {
	$ids = [];
	$stmt = $this->mysqli->query("SELECT id FROM review WHERE RefCode=$refCode AND approved=1;");
	while ($row = $stmt->fetch_array(MYSQLI_ASSOC))
	    array_push($ids, $row['id']);
	$stmt->close();
	return $ids;
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
        $this->sanitizePagination($pagination,$maxResult);
        //print ($floor.":".$ceil);
        $select = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite, library.format, library.year, library.album,library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre from library left join (SELECT * from review where (approved = 1 or approved is null) ) b on library.RefCode = b.RefCode left join band_websites on library.RefCode=band_websites.ID where b.id is NULL and library.status=1 order by library.datein asc limit ?,?;";
        $albums = array();
        if($stmt = $this->mysqli->prepare($select)){
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
            error_log($this->mysqli->error);
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
        $this->sanitizePagination($pagination,$maxResult);
        $select = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite, "
                . "if(review.id is NULL,0,1) as reviewed, library.Locale, library.variousartists, library.format, library.year, library.album, "
                . "library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre, "
                . "review.reviewer, review.ts, review.approved, review.femcon, review.hometown, review.subgenre, review.description, review.recommendations, review.id "
                . "from library left join review on library.RefCode = review.RefCode "
                . "left join band_websites on library.RefCode=band_websites.ID where "
                . "library.refcode = ? order by library.datein asc limit ?,?;";
        $params = array();
        if($stmt = $this->mysqli->prepare($select)){
            $stmt->bind_param('sii',$RefCode,$pagaination,$maxResult);
            $stmt->execute();
            $stmt->bind_result($RefCode,$hasWebsite,$reviewed,$locale,$variousArtists,$format,$year,$album,$artist,$canCon,$datein,$playlist_flag,$genre,
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
            if($this->mysqli->error){
                error_log($this->mysqli->error);
            }
            $stmt->close();
        }
        else{
            error_log($this->mysqli->error);
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
        $orig_term = $term;
        $term = urldecode($term);
        if(!$exactSearch){
            $term = '%'.$term.'%';
        }
        $this->sanitizePagination($pagination,$maxResult);
        $select = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite, if(b.id is NULL,'No', 'Yes') as reviewed, library.format, library.year, library.album,library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre from library left join (SELECT * from review where (approved = 1 or approved is null) ) b on library.RefCode = b.RefCode left join band_websites on library.RefCode=band_websites.ID where (library.refcode like ? or library.album like ? or library.artist like ? or library.year = ? or library.datein = ?) order by library.datein asc limit ?,?;";
        $albums = array();
        if($stmt = $this->mysqli->prepare($select)){
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
            error_log($this->mysqli->error);
            return FALSE;
        }
        return $albums;
    }
    
    public function getFullReview($term){
        $params = array();
        $maxResult = 100;
        $select = "Select library.RefCode, "
                . "if(review.id is NULL,0,1) as reviewed, library.Locale, library.variousartists, library.format, library.year, library.album, "
                . "library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre, "
                . "review.reviewer, review.ts, review.approved, review.femcon, review.hometown, review.subgenre, review.description, review.recommendations, review.id, "
                . "review.notes from library left join review on library.RefCode = review.RefCode where "
                . "review.id = ? order by library.datein asc limit ?;";
        if($stmt = $this->mysqli->prepare($select)){
            $stmt->bind_param('si',$term,$maxResult);
            $stmt->execute();
            $stmt->bind_result($RefCode,$reviewed,$locale,$variousArtists,$format,$year,$album,$artist,$canCon,$datein,$playlist_flag,$genre,
                    $reviewer,$timestamp,$approved,$femcon,$hometown,$subgenre,$description,$recommends,$reviewID,$notes);
            while($stmt->fetch()){
                $params = array(
                        "RefCode"=>$RefCode,
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
                        "review"=>array(
                            "id"=>$reviewID,
                            "reviewer"=>$reviewer,
                            "timestamp"=>$timestamp,
                            "approved"=>$approved,
                            "femcon"=>$femcon,
                            "hometown"=>$hometown,
                            "subgenre"=>$subgenre,
                            "description"=>$description,
                            "recommendations"=>$recommends,
                            "notes"=>$notes,
                        )
                    );
            }
            $stmt->close();
	    $params["labels"] = $this->library->getLabelsByRefCode($RefCode);
        $params["hometown"] = $this->library->getHometownsByRefCode($RefCode);
        $params['subgneres'] = $this->library->getSubgenresByRefCode($RefCode);
        }
        else{
            error_log($this->mysqli->error);
            return FALSE;
        }
        return $params;
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
        if(!$exact):
            $term = "%".$term."%";            
        endif;
        $maxResult = 100;
        $select = "Select library.RefCode, if(review.id is NULL,'No', 'Yes') as reviewed, library.format, library.year, library.album,library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre from library left join review on library.RefCode = review.RefCode where review.id is NULL and (library.refcode like ? or library.year like ? or library.album like ? or library.artist like ? or library.datein like ?) order by library.datein asc limit ?;";
        $params = array();
        if($stmt = $this->mysqli->prepare($select)){
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
            print $this->mysqli->error;
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
        $this->sanitizePagination($pagination, $maxResult);
        $select = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite, library.format, library.year, library.album,library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre from library left join (SELECT * from review where (approved = 1 or approved is null) ) b on library.RefCode = b.RefCode left join band_websites on library.RefCode=band_websites.ID where b.id is NULL and library.status=1 order by library.datein asc limit ?,?;";
        $albums = array();
        if($stmt = $this->mysqli->prepare($select)){
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
            error_log($this->mysqli->error);
            return FALSE;
        }
        return $albums;
    }

    /*
    * @author Derek Melchin
    * @abstract Add album review attributes to a review in the database. Things like tags, hometowns, subgenres, labels)
    * @param string $attNAme      The name of the album attribute you are adding
    * @param array  $attValueList List of values to add to the attribute
    * @param int    $reviewID     ID number of the review in the database
    * @param string $idCol        The name of the id column for the attribute in the database on table {$attName}
    * @param string $nameCol      The name of the name column for the attribute in the database on table {$attName}
    */
    public function applyReviewAttributes($attName, $attValueList, $reviewID, $idCol='id', $nameCol='name') {
	// Sanitize input strings
	$strings = [$attName, $idCol, $nameCol];
	$this->sanitizeStrings($strings);
	$this->sanitizeStrings($attValueList);

	// If the review was submitted with {$attName}
	if (count($attValueList) > 0) {
	    // Make an associative array with the {$attNAme} name as the key and the id as the value
	    // If a {$attNAme} doesn't have an id, leave it's value in the array as null
	    $valueListStr = "('" . implode("', '", $attValueList) . "')"; 
	    $stmt = $this->mysqli->query("SELECT * FROM $attName WHERE name IN $valueListStr;");
	    $attDict = array_fill_keys($attValueList, NULL);
	    while ($row = $stmt->fetch_array(MYSQLI_ASSOC))
		if (in_array($row[$nameCol], $attValueList))
		    $attDict[$row[$nameCol]] = $row[$idCol];

	    // Foreach {$attName} that doesn't have an id, insert it into the {$attName} table and
	    // add the assigned database id to the associative array
	    $valuesToAdd = array_keys($attDict, NULL);
	    if (count($valuesToAdd) > 0) {
	        $valuesToAddStr = "('" . implode("'), ('", $valuesToAdd) . "')";
	        $this->mysqli->query("INSERT INTO $attName (name) VALUES $valuesToAddStr;");
	        $insertID = $this->mysqli->query("SELECT LAST_INSERT_ID() as id")->fetch_array(MYSQLI_ASSOC)['id'];
	        foreach ($attDict as $name => &$id)
		    if (is_null($id)) {
		        $id = $insertID;
			$insertID++;
		    }
	    }

	    // Insert all of the {$attName} ids and $reviewID combos submitted in the review to the 
	    // review_${attName} table
	    $values = '';
	    $elementNum = 0;
	    foreach ($attDict as $attID) {
		if ($elementNum != 0)
		   $values .= ", ";
		$values .= "($reviewID, $attID)";
		$elementNum++;
	    }
	    $this->mysqli->query("INSERT IGNORE INTO review_$attName VALUES $values;");
	}
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

        if(isset($_POST["csvImport"]))
        {
            $description = $_POST['description'];
            $notes =  $_POST['notes'];
            $reviewer = $_POST['reviewer'];
            $recommend = $_POST['recommend'];
	    $approved = 1;
        }
        else
        {
            $description = $app->request()->post('description');
            $notes = $app->request()->post('notes');
            $reviewer = $app->request()->post('reviewer');
            $subgenres = $app->request()->post('subgenres');
            $hometowns = $app->request()->post('hometown');
            $labels = $app->request()->post('label');
            $tags = $app->request()->post('tag');
            $recommend = $app->request()->post('recommend');
            $approved = NULL;
        }
        $newReviewSql = "INSERT IGNORE INTO review (RefCode,reviewer, approved,ip,description,recommendations,notes) "
                      . "VALUES (?,?,?,?,?,?,?)";
        if($stmt = $this->mysqli->prepare($newReviewSql)){
            $stmt->bind_param('isissss',$RefCode,$reviewer,$approved,$ip,$description,$recommend,$notes);
            if($stmt->execute()){
		if(!isset($_POST["csvImport"])) {
		    // Get the review ID
		    $reviewID = $this->mysqli->query("SELECT LAST_INSERT_ID() as id;")->fetch_array(MYSQLI_ASSOC)['id'];

		    // Apply all of the review attribute values that are given in the review GUI
		    $this->applyReviewAttributes('subgenres', $subgenres, $reviewID);
		    $this->applyReviewAttributes('hometowns', $hometowns, $reviewID);
		    $this->applyReviewAttributes('recordlabel', $labels, $reviewID, 'LabelNumber', 'Name');
		    $this->applyReviewAttributes('tags', $tags, $reviewID);
		}

                $app->flash('success',"Review submitted for album #$RefCode");
                return TRUE;
            }
            else{
                $app->flash('error','Review could not be stored, '.$this->mysqli->error);
                return FALSE;
            }
        }
        else{
            $app->flash('error',$this->mysqli->error);
            return FALSE;
        }
        return FALSE; #should not be needed
    }
    
    public function setPrintLabel($RefCode){
        // echo $RefCode;
        if(array_key_exists('ReviewLabels',$_SESSION)){
            if(($key = array_search($RefCode, $_SESSION['ReviewLabels'])) !== false){
                return array(TRUE,304);
            }
        }
        else{
            $_SESSION['ReviewLabels'] = [];
        }
        if($_SESSION['ReviewLabels'][] = $RefCode){
            print_r($_SESSION['ReviewLabels']);
            return array(TRUE,201);
        }
        else{
            return array(FALSE,400);
        }
    }
    
    
    public function clearPrintLabel($RefCode){
        if(array_key_exists('ReviewLabels',$_SESSION)){

            //$_SESSION['ReviewLabels'][$RefCode] = NULL;
            if(($key = array_search($RefCode, $_SESSION['ReviewLabels'])) !== false) {
                unset($_SESSION['ReviewLabels'][$key]);
            }
            else{
                return array(FALSE,203);
            }
            return array(TRUE,202);
        }
        else{
            return array(FALSE,400);
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
        if(array_key_exists('ReviewLabels',$_SESSION)){
            return $_SESSION['ReviewLabels'];
        }
        else{
            return FALSE;
        }
    }
    
    /**
     * 
     * @todo verify use and functionality
     * @global \TPS\type $mysqli
     * @param type $pagination
     * @param type $maxResult
     * @return boolean|array
     */
    public function getApprovedReviews($pagination=1,$maxResult=100){
        $this->sanitizePagination($pagination, $maxResult);
        $reviews = array();
        $selectReviews = "SELECT review.id, review.refcode, library.artist, library.album, review.reviewer, review.ts, review.notes "
                . "FROM review LEFT JOIN library on review.refcode=library.RefCode where review.approved = 1 order by ts limit ?,?";
        if($stmt = $this->mysqli->prepare($selectReviews)){
            $stmt->bind_param('ii',$pagination,$maxResult);
            $stmt->bind_result($id,$refcode,$artist,$album,$reviewer,$timestamp,$notes);
            $stmt->execute();
            while($stmt->fetch()){
                $reviews[$id]= array(
                    "refCode"=>$refcode,
                    "artist"=>$artist,
                    "album"=>$album,
                    "review"=>array(
                        "reviewer"=>$reviewer,
                        ),
                    "timestamp"=>$timestamp,
                    "notes"=>$notes,
                );
            }
        }
        else{
            error_log($this->mysqli->error);
            return FALSE;
        }
        return $reviews;
    }
    
    /**
     * 
     * @todo verify functionality and use
     * @global \TPS\type $mysqli
     * @param type $pagination
     * @param type $maxResult
     * @return boolean
     */
    public function getPendingReviews($pagination=1,$maxResult=100){
        $this->sanitizePagination($pagination, $maxResult);
        $reviews = array();
        $selectReviews = "SELECT review.id, review.refcode, library.artist, library.album, review.reviewer, review.ts, review.notes "
                . "FROM review LEFT JOIN library on review.refcode=library.RefCode where review.approved is null order by ts limit ?,?";
        if($stmt = $this->mysqli->prepare($selectReviews)){
            $stmt->bind_param('ii',$pagination,$maxResult);
            $stmt->bind_result($id,$refcode,$artist,$album,$reviewer,$timestamp,$notes);
            $stmt->execute();
            while($stmt->fetch()){
                $reviews[$id]= array(
                    "refCode"=>$refcode,
                    "artist"=>$artist,
                    "album"=>$album,
                    "review"=>array(
                        "reviewer"=>$reviewer,
                        ),
                    "timestamp"=>$timestamp,
                    "notes"=>$notes,
                );
            }
        }
        else{
            error_log($this->mysqli->error);
            return FALSE;
        }
        return $reviews;
    }
    
    public function countApprovedReviews(){
        $reviews = array();
        $count = 0;
        $selectReviews = "SELECT count(review.id) FROM review LEFT JOIN library on review.refcode=library.RefCode where review.approved = 1";
        if($stmt = $this->mysqli->prepare($selectReviews)){
            $stmt->bind_result($count);
            $stmt->execute();
            $i = 0;
            while($stmt->fetch()){
                $i++;
            }
        }
        else{
            error_log($this->mysqli->error);
            return FALSE;
        }
        if($i>1){
            return -1;
        }
        else{
            return $count;
        }
    }
    
    /**
     * 
     * @global \TPS\type $mysqli
     * @param type $id
     */
    public function getAlbumAndReview($id){
        $maxResult = 100;
        $params = array();
        $selectAlbum = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite, "
                . "if(review.id is NULL,0,1) as reviewed, library.Locale, library.variousartists, library.format, library.year, library.album, "
                . "library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre, "
                . "review.reviewer, review.ts, review.approved, review.femcon, review.hometown, review.subgenre, review.description, review.recommendations, review.id, review.notes "
                . "from review left join library on library.RefCode = review.RefCode left join band_websites on library.RefCode=band_websites.ID where "
                . "review.id = ?;";
        $selectWebsites = "Select band_websites.URL, band_websites.Service, band_websites.date_available, band_websites.date_discontinue"
                . " from band_websites where band_websites.ID=?;";
        
        if($stmt = $this->mysqli->prepare($selectAlbum)){
            $stmt->bind_param('i',$id);
            $stmt->execute();
            $stmt->bind_result($RefCode,$hasWebsite,$reviewed,$locale,$variousArtists,$format,$year,$album,$artist,$canCon,$datein,$playlist_flag,$genre,
                    $reviewer,$timestamp,$approved,$femcon,$hometown,$subgenre,$description,$recommends,$reviewID,$notes);
            while($stmt->fetch()){
                $params['review'] = array(
                    "reviewer" => $reviewer,
                    "approved" => $approved,
                    "femcon" => $femcon,
                    "timestamp" => $timestamp,
                    "subGenre" => $subgenre,
                    "description" => $description,
                    "hometown" => $hometown,
                    "recommends" => $recommends,
                    "ReviewID" => $reviewID,
                    "notes"=>$notes,
                );
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
                        "variousArtists"=>$variousArtists
                    );
            }
            $stmt->close();
	    $params["album"]["labels"] = $this->library->getLabelsByRefCode($RefCode);
        }
        else{
            error_log($this->mysqli->error);
            #$params['error']=$this->mysqli->error;
            return False;
        }
        if(sizeof($params)<1){
            return $params;
        }
        $RefCode = $params['album']['RefCode'];
        if($bands = $this->mysqli->prepare($selectWebsites)){
            $websites = array();
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
            $params['error']=$this->mysqli->error;
        }
        $params['websites']=$websites?:NULL;
        return $params;
    }



    public function importCSV($app, $filename)
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
        // Retrive refcode
        if(!$stmt3 = $this->mysqli->prepare("SELECT refCode FROM library where artist = ? AND album = ?"))
        {
            $stmt3->close();
            header("location: ./?q=new&e=".$stmt3->errno."&s=3&m=".$stmt3->error);
        }

        while (($getData = fgetcsv($file, 10000, ",")) !== FALSE)
        {
            //skip the first row
            if($getData[0]=='timestamp')
                continue;

            if(!$stmt3->bind_param(
                "ss",
                $getData[1],    
                $getData[2]    
            )){
                $stmt3->close();
                return $this->mysqli->error;
            }
            $stmt3->execute();
            $stmt3->store_result();
            $stmt3->bind_result($refCode);
            $stmt3->fetch();
            if($refCode == '')
            {
                echo $getData[1].' - '.$getData[2].' does not exist in DB <br>';
            }
            else
            {
                $_POST['description'] = $getData[4];
                $_POST['notes'] = $getData[3];
                $_POST['reviewer'] = 'Excel Import';
                $_POST['recommend'] = $getData[5];
                $_POST['csvImport'] = 'true';
                echo 'Inserting---- refCode: '. $refCode .' <br>';
                self::createReview($app, $refCode);
            }
            $stmt3->free_result();
            flush();    
        }
        $stmt3->close();
        fclose($file);  
        return true;
}

}
