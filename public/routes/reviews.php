<?php
// Review(s)
$app->group('/review', $authenticate, function () use ($app,$authenticate){
    $app->get('/', $authenticate, function () use ($app){
        $p = $app->request()->get('p');
        $l = $app->request()->get('l');
        $reviews = new \TPS\reviews();
        $albums = $reviews->getAvailableReviews($p,$l);
        $params=array(
            'albums'=>$albums,
            'title'=>'Available Reviews'
            );
        $app->render('reviewList.twig',$params);
    });
    $app->get('/complete' ,$authenticate , function () use ($app){
        global $mysqli;
        $reviews = array();
        $selectReviews = "SELECT review.id, review.refcode, library.artist, library.album, review.reviewer, review.ts, review.notes "
                . "FROM review LEFT JOIN library on review.refcode=library.RefCode where review.approved is null order by ts";
        if($stmt = $mysqli->prepare($selectReviews)){
            $stmt->bind_result($id,$refcode,$artist,$album,$reviewer,$timestamp,$notes);
            $stmt->execute();
            while($stmt->fetch()){
                $reviews[$id]= array(
                    "refCode"=>$refcode,
                    "artist"=>$artist,
                    "album"=>$album,
                    "reviewer"=>$reviewer,
                    "timestamp"=>$timestamp,
                    "notes"=>$notes,
                );
            }
        }
        $params = array(
            "title" => "Completed Reviews",
            "reviews" => $reviews,
        );
        $app->render('reviewListCompleted.twig',$params);
    });
    $app->put('/:id', $authenticate, function ($id) use ($app){ // Update
        if($_SESSION['access']<2){
            $app->render('error.html.twig',array("status"=>403,"title"=>"Error 403","details"=>array("permission denied")));
        }
        else{
            #$app->render('notSupported.twig');
            $description = $app->request()->post('description');
            $notes = $app->request()->post('notes');
            $reviewer = $app->request()->post('reviewer');
            $hometown = $app->request()->post('hometown');
            $subgenres = $app->request()->post('subgenres');
            $recommend = $app->request()->post('recommend');
            $femcon = $app->request()->post('femcon');
            $approved = $app->request()->post('accepted')?:NULL;
            $id_post = $app->request()->post('id')?:NULL;
            if($id_post != $id){
                var_dump($_POST);
                die("ID mismatch");
            }
            global $mysqli;
            $update = "UPDATE review SET approved=?, femcon=?, reviewer=?,"
                    . "hometown=?, subgenre=?, description=?, recommendations=?,"
                    . "notes=? where id=?";
            $albums = array();
            $params = array();
            if($stmt = $mysqli->prepare($update)){
                $stmt->bind_param('iissssssi',
                        $approved,$femcon,$reviewer,$hometown,$subgenres,
                        $description,$recommend,$notes,$id);
                if($stmt->execute()){
                    $stmt->close();
                    $app->flash('success',"$id updated succesfully");
                    $app->redirect('./complete');
                }
                $stmt->close();
            }
            else{
                print $mysqli->error;
            }
        }

    });
    $app->post('/:id',$authenticate, function ($id) use ($app){ // Create (not allowed)
        $app->render('notSupported.twig');
    });
    $app->get('/:id', $authenticate, function ($id) use ($app){ // Query
        // Create new Album Review
        global $mysqli;
        $maxResult = 100;
        $selectAlbum = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite,if(recordlabel.name_alias_duplicate is NULL, recordlabel.Name, "
                . "(SELECT Name from recordlabel where LabelNumber = recordlabel.name_alias_duplicate) ) as recordLabel, "
                . "if(review.id is NULL,0,1) as reviewed, library.labelid, library.Locale, library.variousartists, library.format, library.year, library.album, "
                . "library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre, "
                . "review.reviewer, review.ts, review.approved, review.femcon, review.hometown, review.subgenre, review.description, review.recommendations, review.id, review.notes "
                . "from review left join library on library.RefCode = review.RefCode left join recordlabel on library.labelid = recordlabel.LabelNumber left join band_websites on library.RefCode=band_websites.ID where "
                . "review.id = ?;";
        $selectWebsites = "Select band_websites.URL, band_websites.Service, band_websites.date_available, band_websites.date_discontinue"
                . " from band_websites where band_websites.ID=?;";
        $params = array(
            "title" => "View Review",
            //"access" => $_SESSION['access'],

        );
        if($stmt = $mysqli->prepare($selectAlbum)){
            $stmt->bind_param('i',$id);
            $stmt->execute();
            $stmt->bind_result($RefCode,$hasWebsite,$recordLabel,$reviewed,$labelid,$locale,$variousArtists,$format,$year,$album,$artist,$canCon,$datein,$playlist_flag,$genre,
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
        $RefCode = $params['album']['RefCode'];
        if($bands = $mysqli->prepare($selectWebsites)){
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
            error_log($mysqli->errno.": ".$mysqli->error);
            $params['error']=$mysqli->error;
        }
        $params['websites']=$websites?:NULL;
        $app->render('review.twig',$params);
    });
    
    $app->group('/print' ,$authenticate , function () use ($app,$authenticate){
        $app->get('/',$authenticate, function () use ($app){
            $p = $app->request()->get('p') ?: 1;
            $max = $app->request()->get('l') ?: 100;
            $review = new \TPS\reviews();
            $numReviews = $review->countApprovedReviews();
            $reviews = $review->getApprovedReviews($p,$max);
            $labels = $review->getPrintLables();
            $pagination = array(
                'currentPage'=>$p,
                'limit'=>$max,
                'max'=>$numReviews,
                    );
            $params = array(
                "area" => "Reviews",
                "title" => "Approved",
                "reviews" => $reviews,
                "pagination" => $pagination,
                "labels" => $labels,
            );
            $app->render('reviewPrint.twig',$params);
        });
        $app->put('/:RefCode', $authenticate, function($RefCode) use ($app){
            $reviews = new \TPS\reviews();
            $result = $reviews->setPrintLabel($RefCode);
            $app->response->setStatus($result[1]);
            print $result[1]." ($RefCode)";
        });
        $app->delete('/:RefCode', $authenticate, function($RefCode) use ($app){
            $reviews = new \TPS\reviews();
            $result = $reviews->clearPrintLabel($RefCode);
            $app->response->setStatus($result[1]);
            print $result[1]." ($RefCode)";
        });
    });

    // review/album group
    $app->group('/album', $authenticate, function () use ($app,$authenticate){
    $app->post('/:refcode', $authenticate, function ($RefCode) use ($app){
        $reviews = new \TPS\reviews();
        $reviews->createReview($app, $RefCode);
        $app->redirect('/review');
    });
    $app->get('/:refcode/new', $authenticate, function ($term) use ($app){
        // Create new Album Review
        global $mysqli;
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
        if($stmt = $mysqli->prepare($selectAlbum)){
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
        if($bands = $mysqli->prepare($selectWebsites)){
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
            error_log($mysqli->errno.": ".$mysqli->error);
            $params['error']=$mysqli->error;
        }
        $params['websites']=$websites?:NULL;
        //var_dump($params);
        $app->render('review.twig',$params);
    });
    $app->get('/:refcode', $authenticate, function ($term) use ($app){
        $params = array(
            "title" => "Completed Reviews for $term",
        );
        $reviews = new \TPS\reviews();
        $reviews = $reviews->getReviewsByAlbum($term);
        $params['reviews']=$reviews;
        $app->render('reviewListCompleted.twig',$params);
    });
}); // end review/album group
    
    // SEARCH REVIEWS
    $app->group('/search', $authenticate, function () use ($app,$authenticate){
        $app->post('/album', $authenticate, function () use ($app){
            $term = urlencode($app->request()->post('q'));
            $app->redirect("/review/search/album/$term");
        });
        $app->get('/album/', $authenticate, function () use ($app){
            $term = NULL;
            $orig_term = $term;
            $review = new \TPS\reviews();
            $albums = $review->getAlbumReviews($term);
            $params=array(
                'albums'=>$albums,
                'search'=>$orig_term,
                'area'=>'Search',
                'title'=>'Available Reviews'
                );
            $app->render('reviewList.twig',$params);
        });
        $app->get('/album/:term', $authenticate, function ($term) use ($app){
            $review = new \TPS\reviews();
            $albums = $review->getAlbumReviews($term);
            $params=array(
                'albums'=>$albums,
                'search'=>$term,
                'area'=>'Search',
                'title'=>'Available Reviews'
                );
            $app->render('reviewList.twig',$params);
        });
        $app->get('/:term', $authenticate, function ($term) use ($app){
            $reviews = new \TPS\reviews();
            $reviewList = $reviews->getReview($term);
            $params=array(
                'search'=>$term,
                'area'=>'Search',
                'title'=>'Reviews',
                );
            #$app->render('reviewList.twig',$params);
            print json_encode($reviewList);
        });
        $app->get('/', $authenticate, function () use ($app){
            $params=array(
                'area'=>'Reviews',
                'title'=>'Search'
                );
            $app->render('reviewList.twig',$params);
        });
    });
});
