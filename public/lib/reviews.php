<?php

class reviews{
    public function __construct() {
        global $mysqli;
    }
    
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
    
    public function getAlbumList($pagination=1,$maxResult=25){
        /**
         * inline tags demonstration
         * 
         * @return list returns list if found, False otherwise
         * @author James Oliver
         * @version 1.0
         * @access public
         * @param int $pagination PageIncrement
         * @package int $maxResult number of results
         */
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
    public function getReview($term,$exact=TRUE){
        /**
         * @todo Needs rebuilt
         */
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
}