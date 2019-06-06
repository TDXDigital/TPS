<?php
// Library
$app->group('/library', $authenticate, function () use ($app,$authenticate){
    $app->get('/inventory', function () use ($app) {
        $app->redirect('./inventory/');
    });
    $app->group('/inventory', $authenticate, function () use ($app) {
        // inventory management
        $app->get('/', function () use ($app) {
            $app->render('notSupported.twig',array(
                'title'=>'Library Inventory Mangement'));
        });
    });
    $app->get('/new', $authenticate($app,array(1,2)), function () use ($app){
        $library = new \TPS\library();
        $params = array(
            "govCats"=>$library->getGovernmentCodes(),
            "genres"=>$library->getLibraryGenres(),
	    "tags"=>$library->getTags(),
            "labels"=>\TPS\label::nameSearch("%",False),
            "format"=>$library->getMediaFormats(),
            "scheduleBlock"=>$library->getScheduleBlocks(),
            "title"=>"Receiving",
        );
        if(isset($_SESSION['PRINTID'])){
            $params["PRINTID"]=json_encode($_SESSION['PRINTID']);
        }
        $app->render('libraryInduct.twig',$params);
    });
    $app->post('/new', $authenticate($app,array(1,2)) , function () use ($app){
        $station = new \TPS\station();
        global $mysqli;
        /* @var $artist Contains the artist name */
        $artist = filter_input(INPUT_POST, "artist");
        $album = filter_input(INPUT_POST,"album");
        $genre = filter_input(INPUT_POST,"genre")?:NULL;
        $datein = filter_input(INPUT_POST, "indate")?:NULL;
        $rec_labels = filter_input(INPUT_POST, "label", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY)?:NULL;
        $format = filter_input(INPUT_POST, "format")?:NULL;
	$rating = filter_input(INPUT_POST, "rating")?:NULL;
        $governmentCategory = filter_input(INPUT_POST, "category")?:NULL;
        $schedule = filter_input(INPUT_POST, "schedule")?:2;
        $playlist = filter_input(INPUT_POST, "playlist")?:1;
        $print = filter_input(INPUT_POST, "print")? : 0;
        $accepted = filter_input(INPUT_POST, "accept")? :0;
        $variousartists = filter_input(INPUT_POST, "va")? :0;
        $label_size = filter_input(INPUT_POST, "Label_Size")? : 1;
        $locale = filter_input(INPUT_POST, "locale")? :"international";
        $release_date = filter_input(INPUT_POST,'rel_date')?:NULL;
        $tags = filter_input(INPUT_POST, "tag", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY)?:NULL;
        $note = filter_input(INPUT_POST, "notes")?:NULL;

        $replacePatterns = array(
            ["$2, $1","/^\\s*?((?i)the)(?<=(?i)the)\\s+([[:print:]]{3,})/"],
            ["$2, $1","/^\\s*?((?i)a)(?<=(?i)a)\\s+([[:print:]]{3,})/"]
        );
        foreach ($replacePatterns as $regex) {
            $controller = 0;
            $artRep = preg_replace($regex[1], $regex[0], $artist, 1, $controller);
            if($controller){
                $artist = $artRep;
                break;
            }
        }

        $library = new \TPS\library();
        $result = $library->searchLibraryWithAlbum($artist, $album, True);
        if(sizeof($result)>0 && $result[0]['datein']==$datein){
            $app->flash("error", "album already entered in database with same receiving date");
            $app->redirect("./".$result[0]['RefCode']);
            $app->halt();
        }

        if($locale=="International"){
            $CanCon=0;
        }
        else{
            $CanCon=1;
        }

        if($accepted<>0){
            $accepted = 1;

        }
        else{
            $print=0;
            $playlist=2;
        }

        $labelNums = array_fill(0, sizeof($rec_labels), NULL);
	foreach($rec_labels as $i=>&$label) {
            //find id
            $labels = \TPS\label::nameSearch($label);
            if(is_numeric($label)){
                $label = new \TPS\label($label);
                $labels = array($label->fetch());
                if(sizeof($labels)){
                    $labelNums[$i] = $label;
                }
            }
            if(sizeof($labels)>0){
                foreach ($labels as $key => $value) {
                    if(is_array($value) && key_exists("alias", $value)){
                        if(!is_null($value["alias"])){
                            $labelNums[$i] = $value["alias"];
                        }
                    }
                    else{
                        $labelNums[$i] = $key;
                    }
                }
            }
            if(is_null($labelNums[$i])){
                $labelNums[$i] = \TPS\label::createLabel($label, 1);
                $labelRewrite = array(
                    "/(.+)(?=(?i)\srecord.{0,5})/",
                );
                foreach ($labelRewrite as $regex) {
                    $value = false;
                    $value = preg_match($regex, $label, $matches);
                    if($value==1){
                        $id = \TPS\label::createLabel($matches[0],1);
                        $subLabel = new \TPS\label($id);
                        $subLabel->setAlias($labelNums[$i]);
                    }
                }

            }
            if(is_null($labelNums[$i])||$labelNums[$i]=="null"){
                $app->flash('error','label is required but was not proveded or was invalid. could not recieve album');
                $app->redirect('./new');
            }
	}

        if($genre=="null"){
            $genre=NULL;
        }
        if(!$accepted){
            $playlist=2;
        }
	if($rating == "None") {
	    $rating = NULL;
	} else {
	    $rating = (int)$rating;
	}

        $result = $library->createAlbum($artist, $album, $format, $genre, $labelNums, $locale, $CanCon, $playlist,
            $governmentCategory, $schedule,$note, $accepted, $variousartists, $datein, $release_date, $print,
	    $rating, $tags);

        if(is_string($result)){
            $app->flash('error',$mysqli->error);
            $app->redirect('./new');
        }
        /*
        if(!$stmt3 = $mysqli->prepare("INSERT INTO library(datein,artist,album,variousartists,
            format,genre,status,labelid,Locale,CanCon,release_date,year,note,playlist_flag,
            governmentCategory,scheduleCode)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")){
            $stmt3->close();
            header("location: ./?q=new&e=".$stmt3->errno."&s=3&m=".$stmt3->error);
        }
        if(!is_null($release_date)){
            $year = date('Y',strtotime($release_date));
        }
        else{
            $year = NULL;
        }
        if(!$stmt3->bind_param(
                "sssissiisisssiss",
                $datein,
                $artist,
                $album,
                $variousartists,
                $format,
                $genre,
                $accepted,
                $labelNum,
                $locale,
                $CanCon,
                $release_date,
                $year,
                $note,
                $playlist,
                $governmentCategory,
                $schedule
                )){
            $stmt3->close();
            $app->flash('error',$mysqli->error);
            $app->redirect('./new');
            #header("location: ../library/?q=new&e=".$mysqli->errno."&s=3_b&m=".$mysqli->error);
        }

        if(!$stmt3->execute()){
            error_log("SQL-STMT Error (SEG-3):[".$mysqli->errno."] ".$mysqli->error);
            $error = [$mysqli->errno,$mysqli->error];
            $stmt3->close();
            #header("location: /library/?q=new&e=".$error[0]."&s=3&m=".$error[1]);
            $app->flash('error',$mysqli->error);
            $app->redirect('./new');
            //echo "ERROR #".$mysqli->errno . "  " .    $mysqli->error;
        }
        else{
            $id_last = $stmt3->insert_id;
            $stmt3->close();
            if($stmt4=$mysqli->prepare("INSERT INTO band_websites (ID,URL,Service) VALUES (?,?,?)")){
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
                            error_log($mysqli->error);
                        }
                    }
                }
            }
            else{
                error_log($mysqli->error);
            }

            if(strtolower(substr($artist,-1))!='s'){
                $s = "s";
            }
            else{
                $s="";
            }
            if($print==1){
                $_SESSION['PRINTID'][]=$id_last;
            }
        }
        */
        #header("location: /library/?q=new&m=$artist'$s%20new%20album%20entered ($id_last)");
        $app->flash('Success',"Album Recieved");
        $station->log->info("Album $album by $artist created");
        #var_dump($_SESSION);
        if($app->request->isAjax() || $format == "json"){
            standardResult::created($app, $result);
        }
        else{
            $app->redirect('./new');
        }
    });
    $app->get('/display', $authenticate, function () use ($app){
         $library = new \TPS\library();
         echo $app->request->get("foo");
         echo $library -> displayTable();
    });
    $app->get('/search', $authenticate, function () use ($app){
        $app->redirect('./search/');
    });
    $app->group('/search', $authenticate($app, array(1,2)), function () use ($app, $authenticate){
        $app->get('/', $authenticate, function () use ($app){
            $format = $app->request->get("format");
            $page = (int)$app->request->get("p")?:1;
            $limit = (int)$app->request->get("l")?:25;
            $reverse = (bool)$app->request->get('reverseSort')?True:False;
            $sortCol = $app->request->get('column')?:"RefCode";
            $library = new \TPS\library();
            $library->log->startTimer();
            $result = $library->searchLibrary("", False, $page, $limit, $sortCol, $reverse);
            $pages = ceil($library->countSearchLibrary()/$limit);
            $library->log->info("Search basic retrieval took ".
                    $library->log->timerDuration(). "s");
            $params = array(
                "area"=>"Library",
                "albums"=>$result,
                "title"=>"Search",
                "page"=>$page,
                "pages"=>$pages,
                "limit"=>$limit,
                "sortReversed"=>$reverse?1:0,
                "sortColumn"=>$sortCol
            );
            $isXHR = $app->request->isAjax();
            if($isXHR || $format=="json"){
                print json_encode($params);
            }
            else{
                
                $app->render('searchLibrary.twig', $params);
            }
        });
       

        $app->post('/', $authenticate, function () use ($app){
            $term = $app->request()->post('q');
            $term = urlencode($term);
            $app->redirect("/library/search/$term");
        });
        $app->get('/:value', $authenticate, function ($term) use ($app){
            $format = $app->request->get("format");
            $page = (int)$app->request->get("p")?:1;
            $limit = (int)$app->request->get("l")?:25;
            $reverse = (bool)$app->request->get('reverseSort')?True:False;
            $sortCol = $app->request->get('column')?:"RefCode";
            $library = new \TPS\library();
            $time_start = microtime(true);
            $result = $library->SearchLibrary($term,False,$page,$limit, $sortCol, $reverse);
            $pages = ceil($library->countSearchLibrary($term)/$limit);
            $time_end = microtime(true);
            $library->log->info("Search basic retrieval took ".
                    ($time_end - $time_start). "s");
            $params = array(
                "title"=>"Search $term",
                "albums"=>$result,
                "search"=>$term,
                "page"=>$page,
                "pages"=>$pages,
                "limit"=>$limit,
                "sortReversed"=>$reverse?1:0,
                "sortColumn"=>$sortCol
            );
            $app->render('searchLibrary.twig', $params);
        });
    });
    $app->put('/batch', $authenticate($app, array(2)), function () use ($app){
        $library = new \TPS\library();
        $XHR = $app->request->isAjax();
        $bulkIds = $app->request->put('bulkEditId');
        $action = $app->request->put('action');
        $value = $app->request->put('value');
        $attribute = $app->request->put('attribute');
        $bulkActions = array("status", "attribute", "playlistStatus", "proper");
        $result = [];
        if(in_array($action, $bulkActions)){
            switch ($action){
                case "status":
                    if($value==TRUE){
                        $result[] = $library->enable($bulkIds);
                        break;
                    }
                    $result[] = $library->disable($bulkIds);
                    break;
                case "attribute":
                    $result[] = $library->attribute($bulkIds, $attribute, $value);
                    break;
                case "proper":
                    if(in_array(strtolower($attribute),
                            array("proper", "upper", "lower", "cap_first"))){
                        $albums = array();
                        foreach ($bulkIds as $key => $val) {
                            $albums[$val] = $library->getAlbumByRefcode(
                                    $val)[0];
                        }
                        if(strtolower($value) == "properartist"){
                            foreach ($albums as $key => $val2) {
                                $library->attribute(
                                        $key, ucwords($val2['artist']),
                                        "artist");
                            }
                        }
                        elseif(strtolower($value) == "properalbum"){
                            foreach ($albums as $key => $val2) {
                                $library->attribute(
                                        $key, ucwords($val2['album']),
                                        "album");
                            }
                        }
                        elseif(strtolower($value) == "propernotes"){
                            foreach ($albums as $key => $val2) {
                                $library->attribute(
                                        $key, ucwords($val2['note']),
                                        "note");
                            }
                        }
                        else{
                            throw new Exception("Invalid value provided");
                        }
                        break;
                    }
                    throw new Exception(
                            "Invalid attribute provided for proper");
                case "playlistStatus":
                    if(in_array(strtolower($value),
                                array("complete", "pending", "false")
                            )){
                        $result[] = $library->playlistStatus($bulkIds,
                                                           strtoupper($value));
                        break;
                    }
                    throw new Exception("Invalid value for playlist flag");
            }
        }
        else{
            foreach( $bulkIds as $bulk ){
                switch ($action) {
                    case "print":
                        $libCode = $library->getLibraryCodeByRefCode($bulk)?:9;
                        $_SESSION['PRINTID'][] = array("RefCode"=>$bulk, "LibCode"=>$libCode);
                        break;
                    default:
                        throw new \Exception("Unknown Attribute");
                        break;
                }
            }
        }

        if(in_array(TRUE, $result)){
            $app->flash('success',"Batch Update Performed");
        }
        if(in_array(FALSE, $result)){
            $fails = array_filter($result, function($n) {
                if($n === FALSE){
                    return $n;
                }
            });
            $app->flash("error",
                    "Could not set the Playlist Status for: "
                    . implode(", ",$fails));
        }
        if($XHR){
            print json_encode($result);
            return TRUE;
        }
        $app->redirect($app->request->getReferrer());
    });
    $app->get('/batch', $authenticate, function () use ($app){
        $app->redirect('./batch/');
    });
    $app->group('/batch', function () use ($authenticate, $app){
        $library = new \TPS\library();
        $app->get('/', $authenticate($app, array(2)), function () use ($app, $library){
            $max_input_vars = ini_get('max_input_vars');
            $json = $app->request()->get('format')?: "html";
            $ajax = $app->request()->isAjax();
            $genres = $library->getLibraryGenres();
            $params=array(
                'area'=>'Library',
                'title'=>'Bulk Import',
                'max_input_vars'=> $max_input_vars
            );
            if(!strtolower($json) == "json" || $ajax){
                standardResult::ok($app, "", NULL);
            }
            else{
                $app->render('libraryBulk.twig', $params);
            }
        });
        $app->post('/', $authenticate($app, array(2)), function () use ($app, $library){

        });
        $app->put('/', $authenticate($app, array(2)), function () use ($app, $library){

        });

        $app->get('/options', $authenticate($app, array(1,2)), function () use ($app){
            $options = array(
                // Option => Completes Transaction (No More Options)
                "print" => "Print",
                "status" => "Status",
                "proper" => "Normalize / Clean",
                "attribute" => "Attribute",
                "playlistStatus" => "Playlist Status"
            );
            $app->response->headers->set('Content-Type', 'application/json');
            print json_encode($options);
        });
        $app->group('/options', function () use ($authenticate, $app){
            $app->get('/print', $authenticate($app, [1,2]), function () use ($app){
                print json_encode(True);
            });
            $app->get('/status', $authenticate($app, [1,2]), function () use ($app){
                $options = array(
                    "inputType"=>"select",
                    "values"=>array(
                        1=>"Approve",
                        0=>"Reject"
                    )
                );
                print json_encode($options);
            });
            $app->get('/proper', $authenticate($app, [1,2]), function () use ($app){
                $options = array(
                    "inputType"=>"attribute",
                    "values"=>array(
                        "properAlbum" => array(
                                "value"=>"Album",
                                "input"=>"select"
                            ),
                        "properArtist"=>array(
                                "value"=>"Artist",
                                "input"=>"select"
                            ),
                        "properNotes"=>array(
                            "value"=>"Notes",
                            "input"=>"select"
                        )
                    )
                );
                standardResult::ok($app, $options, NULL);
            });
            $app->get('/properArtist', $authenticate($app, [1,2]), function () use ($app){
                $optons=array(
                    "PROPER"=>"Capitalize Words",
                    #"LOWER"=>"lower case",
                    #"UPPER"=>"UPPER CASE"#,
                    #"CAP_FIRST"=>"Capitalize first"
                );
                standardResult::ok($app, $optons, NULL);
            });
            $app->get('/properAlbum', $authenticate($app, [1,2]), function () use ($app){
                $optons=array(
                    "PROPER"=>"Capitalize Words"#,
                    #"LOWER"=>"lower case",
                    #"UPPER"=>"UPPER CASE",
                    #"CAP_FIRST"=>"Capitalize first"
                );
                standardResult::ok($app, $optons, NULL);
            });
            $app->get('/properNotes', $authenticate($app, [1,2]), function () use ($app){
                $optons=array(
                    "PROPER"=>"Capitalize Words"#,
                    #"LOWER"=>"lower case",
                    #"UPPER"=>"UPPER CASE",
                    #"CAP_FIRST"=>"Capitalize first"
                );
                standardResult::ok($app, $optons, NULL);
            });
            $app->get('/playlistStatus', $authenticate($app, [1,2]), function () use ($app){
                $options = array(
                    "inputType"=>"select",
                    "values"=>array(
                        "PENDING"=>"Pending",
                        "COMPLETE"=>"Completed",
                        "FALSE"=>"Rejected"
                    )
                );
                //print json_encode($options);
                standardResult::ok($app, $options, NULL);
            });
            $app->get('/attribute', $authenticate($app, [1,2]), function () use ($app){
                $options = array(
                    "inputType"=>"attribute",
                    "values"=>array(
                        "album" => array(
                                "value"=>"Album",
                                "input"=>"text"
                            ),
                        "artist"=>array(
                                "value"=>"Artist",
                                "input"=>"text"
                            ),
                        "date"=>array(
                                "value"=>"Date",
                                "input"=>"text"
                            ),
                        "year"=>array(
                                "value"=>"Year",
                                "input"=>"text"
                            ),
                        "genre"=>array(
                            "value"=>"Genre",
                            "input"=>"select"
                        )
                    )
                );
                print json_encode($options);
            });
            $app->get('/genre', $authenticate($app,[1,2]), function () use ($app){
                $library = new \TPS\library();
                $genres = $library->getLibraryGenres();
                print json_encode($genres);
            });
        });
    });
    $app->get('/:RefCode', $authenticate($app,array(1,2)), function ($RefCode) use ($app){
        $library = new \TPS\library();
        //global $mysqli;
        $album=$library->getAlbumByRefcode($RefCode);
        if(sizeof($album)>0){
            $album = $album[0];
        }
        else{
            $params = array(
                "title" => "400 Bad Request",
                "message" => "The resource `$RefCode` does not exist or is invalid",
            );
            $app->response->setStatus(400);
            $app->render("error.html.twig",$params);
            $app->halt(500, "not valid");
        }
        $album['label']=$library->getLabelbyId($album['labelid'])[0];
	$album['labels'] = $library->getLabelsByRefCode($RefCode);
        $album['websites']=$library->getWebsitesByRefCode($RefCode);
        $album['playlist'] = $library->playlist->getAllByRefCode($RefCode);
	$album['tags'] = $library->getTagsByRefCode($RefCode);

        $params = array(
            "album"=>$album,
            "govCats"=>$library->getGovernmentCodes(),
            "genres"=>$library->getLibraryGenres(),
	    "tags"=>$library->getTags(),
            "labels"=>\TPS\label::nameSearch("%",False),
            "format"=>$library->getMediaFormats(),
            "scheduleBlock"=>$library->getScheduleBlocks(),
            "title"=>"Receiving",
        );
        if(isset($_SESSION['PRINTID'])){
            $params["PRINTID"]=json_encode($_SESSION['PRINTID']);
        }
        $app->render('libraryInduct.twig',$params);
    });
    $app->put('/:RefCode', $authenticate($app,2), function ($RefCode) use ($app){
        if($_SESSION['access']<2){
            $app->render('error.html.twig');
        }
        else{
            $library = new \TPS\library();
            global $mysqli;
            /* @var $artist Contains the artist name */
            $artist = filter_input(INPUT_POST, "artist");
            $album = filter_input(INPUT_POST,"album");
            $genre = filter_input(INPUT_POST,"genre")?:NULL;
            $datein = filter_input(INPUT_POST, "indate")?:NULL;
            $label = filter_input(INPUT_POST, "label")?:NULL;
            $format = filter_input(INPUT_POST, "format")?:NULL;
            $governmentCategory = filter_input(INPUT_POST, "category")?:NULL;
            $schedule = filter_input(INPUT_POST, "schedule")?:NULL;
            $playlist = filter_input(INPUT_POST, "playlist")?:FALSE;
            $print = filter_input(INPUT_POST, "print")? : 0;
            $accepted = filter_input(INPUT_POST, "accept")? :0;
            $variousartists = filter_input(INPUT_POST, "va")? :0;
            $label_size = filter_input(INPUT_POST, "Label_Size")? : 1;
            $locale = filter_input(INPUT_POST, "locale")? :"international";
            $release_date = filter_input(INPUT_POST,'rel_date')?:NULL;
            $note = filter_input(INPUT_POST, "notes")?:NULL;

            if($locale=="International"){
                $CanCon=0;
            }
            else{
                $CanCon=1;
            }

            if($accepted<>0){
                $accepted = 1;
            }
            $labelNum = NULL;

            // Get label number if exists
            $stmt1 = $mysqli->prepare("SELECT labelNumber FROM recordlabel where Name=? limit 1");
            $stmt1->bind_param("s",$label);
            if(!$stmt1->execute()){
                $stmt1->close();
                $app->flash('error',$mysqli->error);
                $app->redirect('./');
            }
            $stmt1->bind_result($labelNum);
            $stmt1->fetch();
            $stmt1->close();

            //if does not exist create label
            if(is_null($labelNum)){
                $stmt2 = $mysqli->prepare("INSERT INTO recordlabel(Name,size) VALUES (?,?)");
                $stmt2->bind_param("si",$label,$label_size);
                if(!$stmt2->execute()){
                    $stmt2->close();
                    #header("location: ../library/?q=new&e=".$mysqli->errno."&s=2");
                    $app->flash('error',$mysqli->error);
                    $app->redirect('./');
                    //echo "ERROR: " .    $mysqli->error;
                }
                else{
                    $labelNum=$stmt2->insert_id;
                    //echo "created recordlabel #".$labelNum;
                }
                $stmt2->close();
            }
            else{
                //echo $labelNum ? : " NULL ";
            }
            //echo "creating album...";
            if($genre=="null"){
                $genre=NULL;
            }
            if(is_null($labelNum)||$labelNum=="null"){
                #header("location: ../library/?q=new&e=9999&s=3");
                $app->flash('error','label is required but was not proveded or was invalid. could not recieve album');
                $app->redirect('./');
            }

            if($playlist===FALSE){
                // check if entry exists in playlist table

                // if so, report error

                // else lleave set to FALSE
                $playlist=2;
            }
            else{
                if($playlist=="FALSE"){
                    $playlist=2;
                }
                elseif($playlist=="PENDING"){
                    $playlist=1;
                }
                else{
                    $playlist=3;
                }
                /*if(!$accepted){
                    $playlist = 2;
                }
                else{
                    // if not set to 'PENDING'
                    $playlist = 1;

                    // if so set to 'COMPLETE'
                    // this should no happen unless changing back to already set value??
                }*/
            }

            if(!$stmt3 = $mysqli->prepare("UPDATE library SET datein=?, artist=?, album=?, variousartists=?,
                format=?,genre=?,status=?,labelid=?,Locale=?,CanCon=?,release_date=?,year=?,note=?,playlist_flag=?,
                governmentCategory=?,scheduleCode=? WHERE RefCode=?")){
                header("location: ./?q=new&e=".$mysqli->errno."&s=3&m=".$mysqli->error);
            }
            if(!is_null($release_date)){
                $year = date('Y',strtotime($release_date));
            }
            else{
                $year = NULL;
            }
            if(!$stmt3->bind_param(
                    "sssissiisisssissi",
                    $datein,
                    $artist,
                    $album,
                    $variousartists,
                    $format,
                    $genre,
                    $accepted,
                    $labelNum,
                    $locale,
                    $CanCon,
                    $release_date,
                    $year,
                    $note,
                    $playlist,
                    $governmentCategory,
                    $schedule,
                    $RefCode
                    )){
                $stmt3->close();
                $app->flash('error',$mysqli->error);
                $app->redirect('./');
                #header("location: ../library/?q=new&e=".$mysqli->errno."&s=3_b&m=".$mysqli->error);
            }

            if(!$stmt3->execute()){
                error_log("SQL-STMT Error (SEG-3):[".$mysqli->errno."] ".$mysqli->error);
                $error = [$mysqli->errno,$mysqli->error];
                $stmt3->close();
                #header("location: /library/?q=new&e=".$error[0]."&s=3&m=".$error[1]);
                $app->flash('error',$mysqli->error);
                $app->redirect('./');
                //echo "ERROR #".$mysqli->errno . "  " .    $mysqli->error;
            }
            else{
                $id_last = $stmt3->insert_id;
                $stmt3->close();
                $services_add=[
                    "twitter"=>filter_input(INPUT_POST, 'twitter',FILTER_SANITIZE_URL),
                    "facebook"=>filter_input(INPUT_POST, 'facebook',FILTER_SANITIZE_URL),
                    "bandcamp"=>filter_input(INPUT_POST, 'bandcamp',FILTER_SANITIZE_URL),
                    "soundcloud"=>filter_input(INPUT_POST, 'soundcloud',FILTER_SANITIZE_URL),
                    "website"=>filter_input(INPUT_POST, 'website',FILTER_SANITIZE_URL)
                ];
                if(strpos($services_add["bandcamp"], "soundcloud.com")&&(is_null($services_add['soundcloud'])))
                {
                    // if soundcloud is in the bandcamp URL, reassign it to soundcloud
                    $services_add["soundcloud"] = $services_add["bandcamp"];
                    $services_add["bandcamp"] = NULL;
                }
                $services_update=array();
                $services_delete=array();
                $band_websites = $library->getWebsitesByRefCode($RefCode);
                error_log("PRE:".json_encode(array("add"=>$services_add,"delete"=>$services_delete,"update"=>$services_update)));
                foreach ($band_websites as $serviceKey => $data){
                    if(array_key_exists($serviceKey, $services_add)&&
                            ($services_add[$serviceKey]!=''||!is_null($services_add[$serviceKey]))){
                        if($services_add[$serviceKey]!=''){
                            $services_update[$serviceKey]=$services_add[$serviceKey];
                            $services_add[$serviceKey]=NULL;
                        }
                        else{
                            $services_delete[]=$serviceKey;
                        }
                    }
                    else{
                        //$services_add[$serviceKey]=$serviceKey;
                    }
                }
                error_log(json_encode(array("add"=>$services_add,"delete"=>$services_delete,"update"=>$services_update)));
                if($stmt4=$mysqli->prepare("UPDATE band_websites SET URL=? WHERE Service=? and ID=?")){
                    $service='';
                    $stmt4->bind_param("ssi",$url,$service,$RefCode);
                    /*if(strpos($services_update["bandcamp"], "soundcloud.com")&&(is_null($services_update['soundcloud'])||$service['soundcloud']==''))
                    {
                        // if soundcloud is in the bandcamp URL, reassign it to soundcloud
                        $services_update["soundcloud"] = $services_update["bandcamp"];
                        $services_update["bandcamp"] = NULL;
                    }*/
                    foreach($services_update as $key=>$value){
                        $url=$value;
                        $service=$key;
                        $stmt4->execute();
                    }
                    $stmt4->close();
                }
                if($stmt_del=$mysqli->prepare("DELETE FROM band_websites WHERE Service=? and ID=?")){
                    $stmt_del->bind_param("si",$service,$RefCode);
                    foreach($services_delete as $key){
                        $service=$key;
                        $stmt_del->execute();
                    }
                    $stmt_del->close();
                }
                                    if($stmt_add=$mysqli->prepare("INSERT INTO band_websites (URL,Service,ID) VALUES (?,?,?) ")){
                    $stmt_add->bind_param("ssi",$url,$service,$RefCode);
                    /*if(strpos($services_add["bandcamp"], "soundcloud.com")&&(is_null($services_add['soundcloud'])||$service_add['soundcloud']==''))
                    {
                        // if soundcloud is in the bandcamp URL, reassign it to soundcloud
                        $services["soundcloud"] = $services["bandcamp"];
                        $services["bandcamp"] = NULL;
                    }*/
                    foreach($services_add as $key=>$value){
                        $url=$value;
                        $service=$key;
                        if($value!=""&&!is_null($value)){
                            $stmt_add->execute();
                        }
                    }
                    $stmt_add->close();
                }
                else{
                    error_log($mysqli->error);
                }

                if(strtolower(substr($artist,-1))!='s'){
                    $s = "s";
                }
                else{
                    $s="";
                }
                if($print==1){
                    $libCode=$library->getLibraryCodeByRefCode($RefCode);
                    $_SESSION['PRINTID'][] = array("RefCode"=>$RefCode,
                        "LibCode"=>$libCode);
                }
            }
            #header("location: /library/?q=new&m=$artist'$s%20new%20album%20entered ($id_last)");
            $app->flash('success',"Album Updated");
            #var_dump($_SESSION);
            $app->redirect("./$RefCode");
        }
    });
    $app->group('/parameters', function () use ($app, $authenticate){
        /*
         * "govCats"=>$library->getGovernmentCodes(),
         * "genres"=>$library->getLibraryGenres(),
         * "labels"=>\TPS\label::nameSearch("%",False),
         * "format"=>$library->getMediaFormats(),
         * "scheduleBlock"=>$library->getScheduleBlocks(),
         */
        $library = new \TPS\library();
        $app->get("/governmentcodes", $authenticate($app, array(1,2)), function () use ($app, $library){
            standardResult::ok($app, $library->getGovernmentCodes(), null);
        });
        $app->get("/categories", $authenticate($app, array(1,2)), function () use ($app, $library){
            standardResult::ok($app, $library->getLibraryGenres(), null);
        });
        $app->get("/formats", $authenticate($app, array(1,2)), function () use ($app, $library){
            standardResult::ok($app, $library->getMediaFormats(), null);
        });
        $app->get("/scheduleblocks", $authenticate($app, array(1,2)), function () use ($app, $library){
            standardResult::ok($app, $library->getScheduleBlocks(), null);
        });

        $app->get("/regions", $authenticate($app, array(1,2)), function () use ($app, $library){
            standardResult::ok($app, $library->getLocales(), null);
        });
    });
});
