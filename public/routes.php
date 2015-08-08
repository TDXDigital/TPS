<?php
$app->notFound(function() use ($app) {
    global $base_url, $twig;
    $params = array(
        'base_url' => $base_url,
        'title' => 'Error 404',
        'message' => "We couldn't find the page you asked for, sorry about that",
    );
    $app->render('error.html.twig',$params);
});

$app->get('/', $authenticate($app), function() use ($app){
    
    $app->render('dashboard.twig');
});
$app->post('/', $authenticate($app), function() use ($app){
    $app->render('dashboard.twig');
});

$app->get("/login", function () use ($app) {
   $flash = $app->view()->getData('flash');
   $error = '';
   if (isset($flash['error'])) {
      $error = $flash['error'];
   }
   $urlRedirect = '/';
   if ($app->request()->get('r') && $app->request()->get('r') != '/logout' && $app->request()->get('r') != '/login') {
      $_SESSION['urlRedirect'] = $app->request()->get('r');
   }
   if (isset($_SESSION['urlRedirect'])) {
      $urlRedirect = $_SESSION['urlRedirect'];
   }
   $email_value = $email_error = $password_error = '';
   if (isset($flash['Username'])) {
      $email_value = $flash['Username'];
   }
   if (isset($flash['errors']['Username'])) {
      $email_error = $flash['errors']['Username'];
   }
   if (isset($flash['errors']['password'])) {
      $password_error = $flash['errors']['password'];
   }
   $app->render('login.html.twig', array('error' => $error, 'Username' => $email_value, 'Username_error' => $email_error, 'password_error' => $password_error, 'urlRedirect' => $urlRedirect));
});

$app->post("/login", function () use ($app) {
    $username = $app->request()->post('name');
    $password = $app->request()->post('pass');
    $databaseID = $app->request()->post('SRVID');
    $access = 0;
    $errors = array();
    
    require_once ("TPSBIN".DIRECTORY_SEPARATOR."functions.php");
    $dbxml = simplexml_load_file("TPSBIN".DIRECTORY_SEPARATOR."XML"
            .DIRECTORY_SEPARATOR."DBSETTINGS.xml");
    // check auth type
    foreach($dbxml->SERVER as $server):
        if((string)$server->ID==$databaseID):
            if((string)$server->AUTH == strtoupper("LDAP")){
                if(!extension_loaded('ldap')):
                    error_log("ldap module not installed but requested by login");
                    $e_params = array(
                        "statusCode" => 500,
                        "title" => "Internal Server Error",
                        "message" => "A login method was requested that is not supported by this server",
                    );
                    $app->render('error.html.twig',$e_params);
                endif;
                if((string)$server->ACTIVE == '0'):
                    error_log("server [".$server->ID."] was requested but is disabled in DBSETTINGS.XML");
                    $e_params = array(
                        "statusCode" => 403,
                        "title" => "Permission Denied",
                        "message" => "A login was requested that is disabled",
                    );
                    $app->render('error.html.twig',$e_params);
                endif;
                $ldap_host = (string)$server->LDP_SERVER;   // LDAP Server
                $ldap_port = (string)$server->LDP_PORT;     // LDAP Port
                $ldap_dn = (string)$server->LDP_BASE_DN;    // Active Directory Base DN
                $logo = (string)$server->LOGO_PATH;         // Logo
                $m_logo = (string)$server->MENU_LOGO_PATH;  // Menu Logo (Small)
                $ldap_usr_dom = (string)$server->LDP_DOMAIN;// Domain, for purposes of constructing $user
                $accountFilter = (string)$server->LDP_AccParam ? : 'sAMAccountName';
                $authorization = array(
                    "manager"=>["WebAdmins"],
                    "user"=>["WebUsers","Authenticated Users"]
                );
                
                //connect and bind
                try{
                    $ldap = ldap_connect($ldap_host,$ldap_port);
                    if($ldap_usr_dom!=''){
                        $bind = ldap_bind($ldap, $ldap_usr_dom . '\\' . $username, $password);
                    }
                    else{
                        $bind = ldap_bind($ldap, $username, $password);
                    }
                    if($bind){
                        $filter = "($accountFilter=" . $username . ")";
                        $attr = array("memberof");
                        $result = ldap_search($ldap, $ldap_dn, $filter, $attr);
                        $entries = ldap_get_entries($ldap, $result);
                        ldap_unbind($ldap);
                        $nameLDAP = substr(ldap_explode_dn($entries[0]["dn"],0)[0],3); //get username
                        foreach($entries[0]['memberof'] as $grps) {
                            foreach($authorization['manager'] as $manager_group):
                                if (strpos($grps, $manager_group)) { $access = max(array(2,$access)); break; }
                            endforeach;
                            foreach($authorization['user'] as $user_group):
                                if (strpos($grps, $user_group)) { $access = max(array(1,$access)); break;}
                            endforeach;
                        }
                        if($access>0){
                            $_SESSION['usr'] = easy_decrypt(ENCRYPTION_KEY,(string)$server->USER);
                            //define("USER",easy_decrypt(ENCRYPTION_KEY,(string)$server->USER));
                            $_SESSION['rpw'] = easy_decrypt(ENCRYPTION_KEY,(string)$server->PASSWORD);
                            //define("PASSWORD",easy_decrypt(ENCRYPTION_KEY,(string)$server->PASSWORD));
                            $_SESSION['access'] = $access;
                            $_SESSION['fname'] = $nameLDAP;//"LDAP Authenticated User";
                            $_SESSION['DBNAME'] = (string)$server->DATABASE;//"CKXU";
                            if((string)$server->RESOLVE == 'URL'){
                                $_SESSION['DBHOST'] = (string)$server->URL;
                            }
                            else{
                                $_SESSION['DBHOST'] = (string)$server->IPV4;
                            }
                            //define("HOST",(string)$_SESSION['DBHOST']);
                            //define('DBNAME',(string)$_SESSION['DBNAME']);
                            $_SESSION['SRVPOST'] = (string)$server->ID;//addslashes($_POST['SID']);
                            $_SESSION['logo']=$logo;
                            $_SESSION['m_logo']=$m_logo;
                            $_SESSION['account'] = $username;
                            $_SESSION['AutoComLimit'] = 8;
                            $_SESSION['AutoComEnable'] = TRUE;
                            $_SESSION['TimeZone']='UTC'; // this is just the default to be updated after login
                        }
                        else{
                            $errors['Username'] = "Invalid username or password";
                        }
                        
                            
                    }
                }
                catch (Exception $ex){
                    #error_log("Could not Bind LDAP server");
                    $errors['Username'] = "Invalid login";
                }
            }
            elseif((string)$server->AUTH == strtoupper("SECL")){
                if ($username != "brian@nesbot.com") {
                    $errors['Username'] = "Username not found.";
                } else if ($password != "aaaa") {
                    $app->flash('Username', $username);
                    $errors['password'] = "Password does not match.";
                } 
            }
            elseif((string)$server->AUTH == strtoupper("LIST")){
                if ($username != "brian@nesbot.com") {
                    $errors['Username'] = "Username not found.";
                } else if ($password != "aaaa") {
                    $app->flash('Username', $username);
                    $errors['password'] = "Password does not match.";
                } 
            }
        endif;
    endforeach;
    if (count($errors) > 0) {
        $app->flash('errors', $errors);
        $app->redirect('/login');
    }
    if (isset($_SESSION['urlRedirect'])) {
       $tmp = $_SESSION['urlRedirect'];
       unset($_SESSION['urlRedirect']);
       $app->redirect($tmp);
    }
    $app->redirect('/');
});

$app->get("/logout", function () use ($app) {
   session_unset();
   $app->view()->setData('access', null);
   $app->render('error.html.twig',array('statusCode'=>'Logout','title'=>'Logout', 'message'=>'You have been logged out'));
});

if(isset($_SESSION["DBHOST"])){
    require_once 'TPSBIN'.DIRECTORY_SEPARATOR.'functions.php';
    require_once 'TPSBIN'.DIRECTORY_SEPARATOR.'db_connect.php';
    require_once 'lib_api'.DIRECTORY_SEPARATOR.'LibraryAPI.php';
    $app->get('/updates', $authenticate, function () use ($app) {
        $updates = scandir("./Update/proc/");
        $updateList=array();
        $update_JSON = array();
        foreach ($updates as $update){
            if(strtolower(substr($update,-5))==='.json'){
                $update_JSON[$update]=json_decode(file_get_contents('./Update/proc/'.$update),true);
                $updateList[$update]=$update_JSON[$update]['TPS_Errno'];
            }
        }
        $params = array(
            'updateList'=>json_encode($updateList),
            'updates'=>$update_JSON,
            'title'=>'System Updates',
            
            );
        $app->render('update.twig',$params);
    });
    // user group
    $app->group('/user', $authenticate, function () use ($app) {
        // User page
        $app->get('/:id', function ($id) use ($app) {
            $app->render('notSupported.twig',array('title'=>'User Profile'));
        });
        $app->get('/:id/inbox', function ($id) use ($app) {
            $app->render('notSupported.twig', array('title'=>'User Inbox'));
        });
        $app->get('/:id/settings', function ($id) use ($app) {
            $app->render('notSupported.twig', array('title'=>'User Settings'));
        });
    });
    // Library
    $app->group('/library', $authenticate, function () use ($app,$authenticate){
        $app->get('/', $authenticate, function () use ($app){
            $params = array(
                "govCats"=>array(
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
                ),
                "genres"=>array(
                    "RP" => "Rock/Pop",
                    "FR" => "Folk/Roots",
                    "EL" => "Electronic",
                    "EX" => "Experimental",
                    "JC" => "Jazz/Classical",
                    "HH" => "Hip-Hop",
                    "HM" => "Heavy/Punk/Metal",
                    "WD" => "World",
                    "OT" => "Other",
                ),
                "format"=>array(
                    "CD" => "Compact Disc",
                    "Digital"=>"Digital",
                    "12in" => "12\"",
                    "10in" => "10\"",
                    "7in" => "7\"",
                    "Cass" => "Cassette",
                    "Cart"=>"Fidelipac (cart)",
                    "MD" => "Mini Disc",
                    "Other"=>"Other"
                ),
                "scheduleBlock"=>array(
                    NULL => "Select",
                    "M" => "Daytime1  [06:00-12:00]",
                    "D" => "Daytime2  [12:00-18:00]",
                    "E" => "Evening   [18:00-00:00]",
                    "N" => "Nighttime [00:00-06:00]",
                ),
                "title"=>"Receiving",
            );
            if(isset($_SESSION['PRINTID'])){
                $params["PRINTID"]=json_encode($_SESSION['PRINTID']);
            }
            $app->render('libraryInduct.twig',$params);
        });
        $app->post('/', $authenticate , function () use ($app){
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
            $accepted = filter_input(INPUT_POST, "accepted")? :0;
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
                $playlist=1;
            }
            else{
                // check if entriy exists in 'playlist' table

                // if rejected, it cannot go to playlist by definition.
                // set to FALSE in that case (1)
                if(!$accepted){
                    $playlist = 1;
                }
                else{
                    // if not set to 'PENDING'
                    $playlist = 0;

                    // if so set to 'COMPLETE'
                    // this should no happen unless changing back to already set value??
                }
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
                            /*if(!$stmt4->execute())
                            {
                                $webresult .= $mysqli->error;
                            }*/
                            $stmt4->execute();
                        }
                    }
                }
                /*else{
                    $webresult .= $mysqli->error;
                }*/

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
            $app->redirect('./');
        });
        $app->put('/', $authenticate, function () use ($app){
            if($_SESSION['access']<2){
                $app->render('error.html.twig');
            }
            else{
                $app->render('notSupported.twig');
            }
        });
    });
    // Review(s)
    $app->group('/review', $authenticate, function () use ($app,$authenticate){
            $app->get('/', $authenticate, function () use ($app){
                global $mysqli;
                $maxResult = 100;
                $select = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite, library.format, library.year, library.album,library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre from library left join (SELECT * from review where (approved = 1 or approved is null) ) b on library.RefCode = b.RefCode left join band_websites on library.RefCode=band_websites.ID where b.id is NULL order by library.datein asc limit ?;";
                $albums = array();
                $params = array();
                if($stmt = $mysqli->prepare($select)){
                    $stmt->bind_param('i',$maxResult);
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
                    print $mysqli->error;
                }
                
                $params=array(
                    'albums'=>$albums,
                    'title'=>'Available Reviews'
                    );
                $app->render('reviewList.twig',$params);
            });
            $app->group('/album', $authenticate, function () use ($app,$authenticate){
                $app->post('/:refcode', $authenticate, function ($RefCode) use ($app){
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
                        }
                        else{
                            $app->flash('error','Review could not be stored, '.$mysqli->error);
                        }
                    }
                    else{
                        $app->flash('error',$mysqli->error);
                    }
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
                    // Get Album Reviews
                    global $mysqli;
                    $maxResult = 100;
                    $raw_term = $term;
                    $select = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite, if(recordlabel.name_alias_duplicate is NULL, recordlabel.Name, "
                            . "(SELECT Name from recordlabel where LabelNumber = recordlabel.name_alias_duplicate) ) as recordLabel, "
                            . "if(review.id is NULL,0,1) as reviewed, library.labelid, library.Locale, library.variousartists, library.format, library.year, library.album, "
                            . "library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre, "
                            . "review.reviewer, review.ts, review.approved, review.femcon, review.hometown, review.subgenre, review.description, review.recommendations, review.id "
                            . "from review left join library on library.RefCode = review.RefCode left join recordlabel on library.labelid = recordlabel.LabelNumber left join band_websites on library.RefCode=band_websites.ID where "
                            . "review.RefCode = ? order by library.datein asc limit ?;";
                    $params = array(
                        "title" => "Reviews for #$raw_term",
                        "search" => $raw_term,
                    );
                    $reviews = array();
                    if($stmt = $mysqli->prepare($select)){
                        $stmt->bind_param('si',$term,$maxResult);
                        $stmt->execute();
                        $stmt->bind_result($RefCode,$hasWebsite,$recordLabel,$reviewed,$labelid,$locale,$variousArtists,$format,$year,$album,$artist,$canCon,$datein,$playlist_flag,$genre,
                                $reviewer,$timestamp,$approved,$femcon,$hometown,$subgenre,$description,$recommends,$reviewID);
                        while($stmt->fetch()){
                            $reviews[$reviewID] = array( // this is ok as if the review ID is null there will also be no other entries as ID is a PK
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
                        $stmt->close();
                    }
                    else{
                        $params['error']=$mysqli->error;
                    }
                    $params['albums']=$reviews;
                    $app->render('reviewList.twig',$params);
                });
            }); // end review/album group
            
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
            
            // SEARCH REVIEWS
            $app->group('/search', $authenticate, function () use ($app,$authenticate){
                $app->post('/album', $authenticate, function () use ($app){
                    $term = urlencode($app->request()->post('q'));
                    $app->redirect("/review/search/album/$term");
                });
                $app->get('/album/', $authenticate, function () use ($app){
                    global $mysqli;
                    $term = NULL;
                    $orig_term = $term;
                    $term = "%".$term."%";
                    $maxResult = 100;
                    $select = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite, if(b.id is NULL,'No', 'Yes') as reviewed, library.format, library.year, library.album,library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre from library left join (SELECT * from review where (approved = 1 or approved is null) ) b on library.RefCode = b.RefCode left join band_websites on library.RefCode=band_websites.ID where (library.refcode like ? or library.album like ? or library.artist like ? or library.year = ? or library.datein = ?) order by library.datein asc limit ?;";
                    $params = array();
                    $albums = array();
                    if($stmt = $mysqli->prepare($select)){
                        $stmt->bind_param('sssssi',$term,$term,$term,$orig_term,$orig_term,$maxResult);
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
                        print $mysqli->error;
                    }
                    $params=array(
                        'albums'=>$albums,
                        'search'=>$orig_term,
                        'area'=>'Search',
                        'title'=>'Available Reviews'
                        );
                    $app->render('reviewList.twig',$params);
                });
                $app->get('/album/:term', $authenticate, function ($term) use ($app){
                    global $mysqli;
                    $term = urldecode($term);
                    $orig_term = $term;
                    $term = "%".$term."%";
                    $maxResult = 100;
                    $select = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite, if(b.id is NULL,'No', 'Yes') as reviewed, library.format, library.year, library.album,library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre from library left join (SELECT * from review where (approved = 1 or approved is null) ) b on library.RefCode = b.RefCode left join band_websites on library.RefCode=band_websites.ID where (library.refcode like ? or library.album like ? or library.artist like ? or library.year = ? or library.datein = ?) order by library.datein asc limit ?;";
                    $params = array();
                    $albums = array();
                    if($stmt = $mysqli->prepare($select)){
                        $stmt->bind_param('sssssi',$term,$term,$term,$orig_term,$orig_term,$maxResult);
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
                        print $mysqli->error;
                    }
                    $params=array(
                        'albums'=>$albums,
                        'search'=>$orig_term,
                        'area'=>'Search',
                        'title'=>'Available Reviews'
                        );
                    $app->render('reviewList.twig',$params);
                });
                $app->get('/:term', $authenticate, function ($term) use ($app){
                    global $mysqli;
                    $term = "%".$term."%";
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
                    print json_encode($params);
                });
                $app->get('/', $authenticate, function () use ($app){
                    global $mysqli;
                    $term = NULL;
                    $term = "%".$term."%";
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
                    print json_encode($params);
                });
            });
        });
    
/*//////////////////////////////////////////////////////////////////////////////
    
                                    API
    
//////////////////////////////////////////////////////////////////////////////*/

    $app->group('/api', $authenticate, function () use ($app,$authenticate) {
        $app->group('/review', $authenticate, function () use ($app,$authenticate){
            $app->get('/', $authenticate, function () use ($app){
                global $mysqli;
                $maxResult = 100;
                $select = "Select library.RefCode, library.format, library.year, library.album,library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre from library left join review on library.RefCode = review.RefCode where review.id is NULL order by library.datein asc limit ?;";
                $params = array();
                if($stmt = $mysqli->prepare($select)){
                    $stmt->bind_param('i',$maxResult);
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
            $app->get('/album/:refcode', $authenticate, function ($term) use ($app){
                global $mysqli;
                $maxResult = 100;
                $select = "Select library.RefCode, if(recordlabel.name_alias_duplicate is NULL, recordlabel.Name, "
                        . "(SELECT Name from recordlabel where LabelNumber = recordlabel.name_alias_duplicate) ) as recordLabel, "
                        . "if(review.id is NULL,0,1) as reviewed, library.labelid, library.Locale, library.variousartists, library.format, library.year, library.album, "
                        . "library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre, "
                        . "review.reviewer, review.ts, review.approved, review.femcon, review.hometown, review.subgenre, review.description, review.recommendations, review.id "
                        . "from library left join review on library.RefCode = review.RefCode left join recordlabel on library.labelid = recordlabel.LabelNumber where "
                        . "library.refcode = ? order by library.datein asc limit ?;";
                $params = array();
                if($stmt = $mysqli->prepare($select)){
                    $stmt->bind_param('si',$term,$maxResult);
                    $stmt->execute();
                    $stmt->bind_result($RefCode,$recordLabel,$reviewed,$labelid,$locale,$variousArtists,$format,$year,$album,$artist,$canCon,$datein,$playlist_flag,$genre,
                            $reviewer,$timestamp,$approved,$femcon,$hometown,$subgenre,$description,$recommends,$reviewID);
                    while($stmt->fetch()){
                        $params[$reviewID] = array( // this is ok as if the review ID is null there will also be no other entries as ID is a PK
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
                print json_encode(GetLibraryRefcode($refcode));
            });
            $app->get('/artist/:artist', function ($artist) use ($app) {
                print json_encode(GetLibraryfull($artist));
            });
            $app->get('/:artist/:album', function ($artist,$album) use ($app) {
                print json_encode(GetLibraryfull($artist,$album));
            });
            $app->get('/', $authenticate, function () {
                print json_encode(ListLibrary());
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
                        ));
        });

    });
}
$app->run();
