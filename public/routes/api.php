<?php

/*//////////////////////////////////////////////////////////////////////////////
    
                                    API
    
//////////////////////////////////////////////////////////////////////////////*/

$app->group('/api', $authenticate, function () use ($app,$authenticate) {
    $app->group('/review', $authenticate, function () use ($app,$authenticate){
        $app->get('/', $authenticate, function () use ($app){
            $l = $app->request()->get('l')?:25;
            $p = $app->request()->get('p')?:1;
            $reviews = new reviews();
            $params = $reviews->getAlbumList($p,$l);
            print json_encode($params);
        });
        $app->get('/album/:refcode', $authenticate, function ($term) use ($app){
            $reviews = new reviews();
            $l = $app->request()->get('l')?:25;
            $p = $app->request()->get('p')?:1;
            $params = $reviews->getReviewsByAlbum($term, $p, $l);
            print json_encode($params);
        });
        $app->get('/:refcode', $authenticate, function ($term) use ($app){
            global $mysqli;
            $maxResult = 100;
            $select = "Select library.RefCode, if(recordlabel.name_alias_duplicate is NULL, recordlabel.Name, "
                    . "(SELECT Name from recordlabel where LabelNumber = recordlabel.name_alias_duplicate) ) as recordLabel, "
                    . "if(review.id is NULL,0,1) as reviewed, library.labelid, library.Locale, library.variousartists, library.format, library.year, library.album, "
                    . "library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre, "
                    . "review.reviewer, review.ts, review.approved, review.femcon, review.hometown, review.subgenre, review.description, review.recommendations, review.id "
                    . "from library left join review on library.RefCode = review.RefCode left join recordlabel on library.labelid = recordlabel.LabelNumber where "
                    . "review.id = ? order by library.datein asc limit ?;";
            $params = array();
            if($stmt = $mysqli->prepare($select)){
                $stmt->bind_param('si',$term,$maxResult);
                $stmt->execute();
                $stmt->bind_result($RefCode,$recordLabel,$reviewed,$labelid,$locale,$variousArtists,$format,$year,$album,$artist,$canCon,$datein,$playlist_flag,$genre,
                        $reviewer,$timestamp,$approved,$femcon,$hometown,$subgenre,$description,$recommends,$reviewID);
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
                            "label"=>array(
                                "Name"=>$recordLabel,
                                "Id"=>$labelid,
                            ),
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
                            )
                        );
                }
                $stmt->close();
            }
            else{
                print $mysqli->error;
            }
            print json_encode($params);
        });
        $app->get('/search/:term', $authenticate, function ($term) use ($app){
            global $mysqli;
            $term = "%".$term."%";
            $maxResult = 100;
            $select = "Select library.RefCode, library.format, library.year, library.album,library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre from library left join review on library.RefCode = review.RefCode where review.id is NULL and (library.refcode like ? or library.year like ? or library.album like ? or library.artist like ? or library.datein like ?) order by library.datein asc limit ?;";
            $params = array();
            if($stmt = $mysqli->prepare($select)){
                $stmt->bind_param('sssssi',$term,$term,$term,$term,$term,$maxResult);
                $stmt->execute();
                $stmt->bind_result($RefCode,$format,$year,$album,$artist,$canCon,$datein,$playlist_flag,$genre);
                while($stmt->fetch()){
                    $params[$RefCode] = array(
                            "format"=>$format,
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
            print json_encode($params);
        });
    });
    $app->group('/library', $authenticate, function () use ($app,$authenticate){
        $app->get('/:refcode', function ($refcode){
            $library = new \TPS\library();
            print json_encode($library->getAlbumByRefcode($refcode));
        });
        $app->get('/artist/:artist', function ($artist) use ($app) {
            $library = new \TPS\library();
            print json_encode($library->searchLibraryWithAlbum($artist));
        });
        $app->get('/:artist/:album', function ($artist,$album) use ($app) {
            $library = new \TPS\library();
            print json_encode($library->searchLibraryWithAlbum($artist,$album));
        });
        $app->group('/print', $authenticate, function() use ($app,$authenticate){
            /**
             * @abstract resets print queue
             */
            $app->delete('/',$authenticate, function () use ($app){
                unset($_SESSION['PRINTID']);
                print '[PRINTID cleared]';
            });
            $app->get('/',$authenticate, function () use ($app){
                //unset($_SESSION['PRINTID']);
                if(isset($_SESSION['PRINTID'])){
                    print json_encode($_SESSION['PRINTID']);
                }
                else{
                    json_encode(array());
                }
            });
        });
        $app->get('/', $authenticate, function () {
            $library = new \TPS\library();
            print json_encode($library->ListAll());
        });
    });
    $app->group('/episode', $authenticate, function() use ($app,$authenticate){
        $app->get('/recent', $authenticate, function () use ($app){
            global $mysqli;
            $response = array(
                "cols"=>array(
                    array(
                        "id"=>"Room",
                        "label"=>"Room",
                        "type"=>"string",
                        "pattern"=>"",
                    ),
                    array(
                        "id"=>"Name",
                        "label"=>"Name",
                        "type"=>"string",
                        "pattern"=>"",
                    ),
                    array(
                        "id"=>"Start",
                        "label"=>"Start",
                        "type"=>"date",
                    ),
                    array(
                        "id"=>"End",
                        "label"=>"End",
                        "type"=>"date",

                    ),
                ),
                "rows"=>array()
            );
            $sql_episode="SELECT concat(DATE_FORMAT(date, '%Y:%m:%d'),\" \",DATE_FORMAT(starttime, '%H:%i:%s')) AS start, concat(DATE_FORMAT(date, '%Y:%m:%d'),\" \",DATE_FORMAT(endtime, '%H:%i:%s')) AS end, programname, date, Type FROM episode WHERE DATE(date)>DATE_ADD(CURDATE(), INTERVAL -2 DAY) and DATE(date)<=CURDATE() and endtime > starttime and Type = 0 order by start";
            $results = $mysqli->query($sql_episode) or trigger_error($mysqli->error."[$sql_episode]");
            while($row = $results->fetch_array()){
                $response['rows'][]=array(
                    "c"=>[
                        array('v'=>"Logged"),
                        array('v'=>$row['programname']),
                        array('v'=>"Date(".date("Y,m,d,H,i,s",strtotime($row['start'])).")"),
                        array('v'=>"Date(".date("Y,m,d,H,i,s",strtotime($row['end'])).")"),
                        ]
                    );
            }
            print json_encode($response);
        });
        $app->get('/prerecords/pending', $authenticate, function () use ($app){
            global $mysqli;
            $response = array(
                "cols"=>array(
                    array(
                        "id"=>"Room",
                        "label"=>"Room",
                        "type"=>"string",
                        "pattern"=>"",
                    ),
                    array(
                        "id"=>"Name",
                        "label"=>"Name",
                        "type"=>"string",
                        "pattern"=>"",
                    ),
                    array(
                        "id"=>"Start",
                        "label"=>"Start",
                        "type"=>"date",
                    ),
                    array(
                        "id"=>"End",
                        "label"=>"End",
                        "type"=>"date",

                    ),
                ),
                "rows"=>array()
            );
            $sql_prerecord="SELECT concat(DATE_FORMAT(date, '%Y:%m:%d'),\" \",DATE_FORMAT(starttime, '%H:%i:%s')) AS start, concat(DATE_FORMAT(date, '%Y:%m:%d'),\" \",DATE_FORMAT(endtime, '%H:%i:%s')) AS end, programname, date, Type FROM episode WHERE DATE(date)<DATE_ADD(CURDATE(), INTERVAL +30 DAY) and DATE(date)>=CURDATE() and (Type = 1 or prerecorddate is not null) and endtime IS NOT NULL order by start";$sql_episode="SELECT concat(DATE_FORMAT(date, '%Y:%m:%d'),\" \",DATE_FORMAT(starttime, '%H:%i:%s')) AS start, concat(DATE_FORMAT(date, '%Y:%m:%d'),\" \",DATE_FORMAT(endtime, '%H:%i:%s')) AS end, programname, date, Type FROM episode WHERE DATE(date)>DATE_ADD(CURDATE(), INTERVAL -2 DAY) and DATE(date)<=CURDATE() and endtime > starttime and Type = 0 order by start";
            $results = $mysqli->query($sql_prerecord) or trigger_error($mysqli->error."[$sql_episode]");
            while($row = $results->fetch_array()){
                $response['rows'][]=array(
                    "c"=>[
                        array('v'=>"Logged"),
                        array('v'=>$row['programname']." (".$row['date'].")"),
                        array('v'=>"Date(".date("Y,m,d,H,i,s",strtotime($row['start'])).")"),
                        array('v'=>"Date(".date("Y,m,d,H,i,s",strtotime($row['end'])).")"),
                        ]
                    );
            }
            print json_encode($response);
        });
    });
    $app->get('/', function() use ($app){
        $app->render('error.html.twig',
                array(
                    'title'=>'API Access',
                    'message'=>'please query against a supported API section',
                    'details'=>array(
                        'for more information please see our wiki on '
                    . '<a href="https://github.com/TDXDigital/TPS/wiki/API-Documentation">'
                    . 'GitHub</a>'),
                    )
                ); 
    });

});
