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

/*$app->get('/login', function() use ($app){
    $app->render('login.html.twig');
    //$app->redirect('/Security/login.html');
);*/

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
        // Get book with ID
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
    
    $app->group('/review', $authenticate, function () use ($app,$authenticate){
            $app->get('/', $authenticate, function () use ($app){
                global $mysqli;
                $maxResult = 100;
                $select = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite, library.format, library.year, library.album,library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre from library left join review on library.RefCode = review.RefCode left join band_websites on library.RefCode=band_websites.ID where review.id is NULL order by library.datein asc limit ?;";
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
                    $select = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite,if(recordlabel.name_alias_duplicate is NULL, recordlabel.Name, "
                            . "(SELECT Name from recordlabel where LabelNumber = recordlabel.name_alias_duplicate) ) as recordLabel, "
                            . "if(review.id is NULL,0,1) as reviewed, library.labelid, library.Locale, library.variousartists, library.format, library.year, library.album, "
                            . "library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre, "
                            . "review.reviewer, review.ts, review.approved, review.femcon, review.hometown, review.subgenre, review.description, review.recommendations, review.id "
                            . "from library left join review on library.RefCode = review.RefCode left join recordlabel on library.labelid = recordlabel.LabelNumber left join band_websites on library.RefCode=band_websites.ID where "
                            . "library.refcode = ? order by library.datein asc limit ?;";
                    $params = array();
                    if($stmt = $mysqli->prepare($select)){
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
                    $app->render('review.twig',$params);
                });
                $app->get('/:refcode', $authenticate, function ($term) use ($app){
                    // Get Album Reviews
                    global $mysqli;
                    $maxResult = 100;
                    $select = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite, if(recordlabel.name_alias_duplicate is NULL, recordlabel.Name, "
                            . "(SELECT Name from recordlabel where LabelNumber = recordlabel.name_alias_duplicate) ) as recordLabel, "
                            . "if(review.id is NULL,0,1) as reviewed, library.labelid, library.Locale, library.variousartists, library.format, library.year, library.album, "
                            . "library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre, "
                            . "review.reviewer, review.ts, review.approved, review.femcon, review.hometown, review.subgenre, review.description, review.recommendations, review.id "
                            . "from library left join review on library.RefCode = review.RefCode left join recordlabel on library.labelid = recordlabel.LabelNumber left join band_websites on library.RefCode=band_websites.ID where "
                            . "library.refcode = ? order by library.datein asc limit ?;";
                    $params = array();
                    if($stmt = $mysqli->prepare($select)){
                        $stmt->bind_param('si',$term,$maxResult);
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
                        $stmt->close();
                    }
                    else{
                        $params['error']=$mysqli->error;
                    }
                    $app->render('reviewList.twig',$params);
                });
            }); // end album group
            $app->get('/:id', $authenticate, function ($id) use ($app){
                // Get review based on id
                
                global $mysqli;
                $maxResult = 100;
                $select = "Select library.RefCode, if(recordlabel.name_alias_duplicate is NULL, recordlabel.Name, "
                        . "(SELECT Name from recordlabel where LabelNumber = recordlabel.name_alias_duplicate) ) as recordLabel, "
                        . "if(review.id is NULL,0,1) as reviewed, library.labelid, library.Locale, library.variousartists, library.format, library.year, library.album, "
                        . "library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre, "
                        . "review.reviewer, review.ts, review.approved, review.femcon, review.hometown, review.subgenre, review.description, review.recommendations, review.id "
                        . "from library left join review on library.RefCode = review.RefCode left join recordlabel on library.labelid = recordlabel.LabelNumber where "
                        . "review.id = ? order by library.datein asc limit ?;";
                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
                $params = array(
                    "title"=>"Album Reviews",
                    "ip"=>$ip
                );
                if($stmt = $mysqli->prepare($select)){
                    $stmt->bind_param('si',$id,$maxResult);
                    $stmt->execute();
                    $stmt->bind_result($RefCode,$recordLabel,$reviewed,$labelid,$locale,$variousArtists,$format,$year,$album,$artist,$canCon,$datein,$playlist_flag,$genre,
                            $reviewer,$timestamp,$approved,$femcon,$hometown,$subgenre,$description,$recommends,$reviewID);
                    while($stmt->fetch()){
                        $album = array(
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
                #$app->render('reviewList.twig',$params);
                $app->render('notSupported.twig',$params);
            });
            $app->post('/search/album', $authenticate, function () use ($app){
                global $mysqli;
                $term = $app->request()->post('q');
                $orig_term = $term;
                $term = "%".$term."%";
                $maxResult = 100;
                $select = "Select library.RefCode, if(band_websites.ID is NULL,'No','Yes') as hasWebsite, if(review.id is NULL,'No', 'Yes') as reviewed, library.format, library.year, library.album,library.artist, library.CanCon, library.datein, library.playlist_flag, library.genre from library left join review on library.RefCode = review.RefCode left join band_websites on library.RefCode=band_websites.ID where (library.refcode like ? or library.year like ? or library.album like ? or library.artist like ? or library.datein like ?) order by library.datein asc limit ?;";
                $params = array();
                $albums = array();
                if($stmt = $mysqli->prepare($select)){
                    $stmt->bind_param('sssssi',$term,$term,$term,$term,$term,$maxResult);
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
            $app->get('/search/:term', $authenticate, function ($term) use ($app){
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
