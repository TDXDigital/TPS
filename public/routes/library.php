<?php
// Library
$app->group('/library', $authenticate, function () use ($app,$authenticate){
    $app->get('/new', $authenticate($app,array(1,2)), function () use ($app){
        $library = new \TPS\library();
        $params = array(
            "govCats"=>$library->getGovernmentCodes(),
            "genres"=>$library->getLibraryGenres(),
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
        global $mysqli;
        /* @var $artist Contains the artist name */
        $artist = filter_input(INPUT_POST, "artist");
        $album = filter_input(INPUT_POST,"album");
        $genre = filter_input(INPUT_POST,"genre")?:NULL;
        $datein = filter_input(INPUT_POST, "indate")?:NULL;
        $label = filter_input(INPUT_POST, "label")?:NULL;
        $format = filter_input(INPUT_POST, "format")?:NULL;
        $governmentCategory = filter_input(INPUT_POST, "category")?:NULL;
        $schedule = filter_input(INPUT_POST, "schedule")?:2;
        $playlist = filter_input(INPUT_POST, "playlist")?:1;
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
        else{
            $print=0;
            $playlist=2;
        }
        $labelNum = NULL;

        // Get label number if exists
        $stmt1 = $mysqli->prepare("SELECT labelNumber FROM recordlabel where Name=? limit 1");
        $stmt1->bind_param("s",$label);
        if(!$stmt1->execute()){
            $stmt1->close();
            $app->flash('error',$mysqli->error);
            $app->redirect('./new');
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
                $app->redirect('./new');
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
            $app->redirect('./new');
        }

        /*if($playlist==FALSE){
            $playlist=NULL;
            // check if entry exists in playlist table

            // if so, report error

            // else lleave set to FALSE
            //$playlist=1;
        }
        else{*/
        // check if entriy exists in 'playlist' table

        // if rejected, it cannot go to playlist by definition.
        // set to FALSE in that case (1)
        if(!$accepted){
            $playlist=2;
        }


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
        #header("location: /library/?q=new&m=$artist'$s%20new%20album%20entered ($id_last)");
        $app->flash('Success',"Album Recieved");
        #var_dump($_SESSION);
        $app->redirect('./new');
    });
    $app->get('/search/', $authenticate, function () use ($app){
        $library = new \TPS\library();
        $result = $library->searchLibrary("");
        $params = array(
            "area"=>"Library",
            "albums"=>$result,
            "title"=>"Search",
        );
        $app->render('searchLibrary.twig',$params);
    });
    $app->post('/search/', $authenticate, function () use ($app){
        $term = $app->request()->post('q');
        $term = urlencode($term);
        $app->redirect("/library/search/$term");
    });
    $app->get('/search/:value', $authenticate, function ($term) use ($app){
        $library = new \TPS\library();
        $result = $library->SearchLibrary($term);
        $params = array(
            "title"=>"Search $term",
            "albums"=>$result,
            "search"=>$term,
        );
        $app->render('searchLibrary.twig',$params);
    });
    $app->get('/:RefCode', $authenticate($app,array(1,2)), function ($RefCode) use ($app){
        $library = new \TPS\library();
        //global $mysqli;
        $album=$library->getAlbumByRefcode($RefCode)[0];
        $album['label']=$library->getLabelbyId($album['labelid'])[0];
        $album['websites']=$library->getWebsitesByRefCode($RefCode);
        
        $params = array(
            "album"=>$album,
            "govCats"=>$library->getGovernmentCodes(),
            "genres"=>$library->getLibraryGenres(),
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
});
