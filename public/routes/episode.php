<?php

/*
 * The MIT License
 *
 * Copyright 2015 J.oliver.
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

$app->get('/episode', function() use ($app){
    $app->redirect("/episode/");
});
$app->group('/episode', $authenticate($app,[1,2]),
        function () use ($app,$authenticate){
    $app->get('/', $authenticate($app, [1,2]), function() use ($app, &$station){
        $callsign = $app->request->get('callsign')?:$_SESSION['CALLSIGN'];
        $programId = $app->request->get('program')?:NULL;
        $episodeId = $app->request->get('episode')?:NULL;
        $type = $app->request->get('type')?:0;
        $station= new \TPS\station($callsign);
        if(!is_null($programId)){
            $program = new \TPS\program($station, $programId);
        }
        if(!is_null($episodeId)){
            $program = new \TPS\episode($station, $episodeId);
        }
        $episodes = new \TPS\episodes($station->getCallsign());
        $allEpisodes = $episodes->getAllEpisodes();
        $result = [];
        foreach ($allEpisodes as $key=>&$episode){
            array_push($result, $episode->getEpisode());
        }
        print standardResult::ok($app, $result, NULL, 200, true);
    });
    $app->get('/new',$authenticate($app,[1,2]),
            function () use ($app){
        $params=array(
            'area'=>'Episode',
            'title'=>'New'
            );
        $callsign = $app->request->get('callsign')?:$_SESSION['CALLSIGN'];
        $format = $app->request->get('format');
        $station = new \TPS\station($callsign);
        $params['stations'] = $station->getStations();
        $genres = array();
        if(is_null($callsign) || !in_array($callsign,$params['stations'])){
            // invalid or missing callsign
            if(!is_null($callsign)){
                $warn = "Error, invalid callsign `$callsign`"
                        . " provided, using default";
                $app->flashNow('error',$warn);
                $station->log->warn($warn);
            }
            $callsign = key($params['stations']);
        }
        $params['callsign'] = $station->setStation($callsign);
        $params['station'] = $station->getStation($callsign);
        $programIds = $station->getAllProgramIds(True);
        $temp = array();
        foreach ($programIds as $id) {
            $program = new \TPS\program($station, $id);
            $pgm = $program->getValues();
            array_push($temp, $pgm);
            array_push($genres, $pgm['genre']);
        }
        $genres = array_unique($genres,SORT_STRING);
        sort($genres);
        $params['genres'] = $genres;
        $params['program'] = $temp;
        $params['legacy'] = $app->request->get('legacy')??'false';
        $isXHR = $app->request->isAjax();
        if($isXHR){
            print json_encode($params);
        }
        elseif(!is_null($format) && $format=="json"){
            print json_encode($params);
        }
        else{
            $app->render("episodeNew.twig",$params);
        }
    });
    $app->post('/new', $authenticate($app,[1,2]), function() use ($app){
        $allParams = $app->request->params();

        $callsign = $app->request->post('callsign')?:NULL;
        $programID = $app->request->post('program')?:NULL;
        $airDate = $app->request->post('airDate')?:NULL;
        $recordDate = $app->request->post('recordDate')?:NULL;
        $airTime = $app->request->post('airTime')?:NULL;
        $type = $app->request->post('type')?:0;
        $description = $app->request->post('description')?:NULL;

        if($type==2 && is_null($airDate)){
            $airDate="1973-01-01";
        }

        $station = new \TPS\station($callsign);
        $program = new \TPS\program($station, $programID);
	    $traffic = new \TPS\traffic();

        $req = $program->getRequirement();
       
        $episode = new \TPS\episode($program, NULL, $airDate, $airTime,
                $description, $type, $recordDate);
        $episodeCheck = new \TPS\episode($program, NULL, $airDate, $airTime,
                $description, $type, $recordDate);

        $ads = $episode->getAdOptions($req['spons']);
        $commercials =  $episode->getAllCommercials($ads);
         
        if( $type == 2){
            while($episodeCheck->getEpisode()["id"] != Null){
                $airTime = date("H:i",
                        strtotime("$airDate $airTime + 1 minutes"));
                $episodeCheck = new \TPS\episode($program, NULL, $airDate, $airTime,
                    $description, $type, $recordDate);
            }
            $episode = new \TPS\episode($program, NULL, $airDate, $airTime,
                    $description, $type, $recordDate);
        }

        $params = array(
            'area'=>'Episode',
            'title'=>'Log Addition',
            'req' => $req,
            'ads' => $ads,
            'commercial' => $commercials,
            'sponsorIds' => $traffic->getSponsorID(),
            'sponsorPromos'=>$traffic-> getSponsorPromo(),
            'action' => '/episode/finalize',
	    'radioShowPromos' => $traffic->getPromos(),
	    'PSAs' => $traffic->getPSAs()
        );
        if($episodeCheck->getEpisode()['id']){
            $params['episode'] = $episode->getEpisode();
        }
        else{
            $params['episode'] = $episode->createEpisode();
        }
        // Redirect to query URL in future
        $app->response->setStatus(201);
        $isXHR = $app->request->isAjax();
        if(!$isXHR){
            if($app->request->post('legacy')=='true')
                $app->render("episodeRedirect.twig",$params);
            else
            {
                $app->render("episodeInsertSong.twig",$params);
            }

        }
        else{
            print json_encode($params);
        }
        //var_dump($params);
    });


  // Create new playsheet
    $app->post('/insertSong', function() use ($app, $authenticate){
 
        $params=array(
            'area'=>'Episode',
            'title'=>'Log Addition'
            );

        $app->render("episodeInsertSong.twig",$params);
    });


    $app->post('/searchSong/:playlistId', function($shortCode) use ($app, $authenticate){
        $playlist = new \TPS\playlist();
        $albumInfo = $playlist->getAllByShortCode($shortCode);
        // echo $playlistId;
        // print_r(reset($albumInfo));
        echo json_encode(reset($albumInfo));
    });

     $app->post('/finalize', function() use ($app, $authenticate){
        $epNum = $app->request->post('epNum');
        $row = $app->request->post('row');
        $playlisdId = $app->request->post('playlistNum');
        $category = $app->request->post('cat');
        $time = $app->request->post('time');
        $title = $app->request->post('title');
        $artist = $app->request->post('artist');
        $album = $app->request->post('album');
        $composer = $app->request->post('composer');
        $cancon = $app->request->post('cancon')?? array();
        $hit = $app->request->post('hit')?? array();
        $inst = $app->request->post('instrumental')?? array();
        $type = $app->request->post('type');
        $lang = $app->request->post('lang');
        $note = $app->request->post('note');
        $endTime = $app->request->post('endTime');
        $spokenTime = $app->request->post('spokenTime');
        \TPS\episode::insertSongs($row, $epNum, $title, $album, $composer, $time, $artist, $cancon, $playlisdId, $type, $category, $hit, $inst, $lang, $note);
        \TPS\episode::updateEndTime($epNum, $endTime);
        \TPS\episode::updateSpokenTime($epNum, $spokenTime);
        
        $params = array();
        $params["songs"] = \TPS\episode::getSongByEpNum($epNum);
        $params["title"] = 'View Program Log';
        $params["episode"] = \TPS\episode::getEpisodeByEpNum($epNum);

        $app->render("episodeLog.twig",$params);
    });

     // Create new program
    $app->get('/search', function() use ($app, $authenticate){
 
        $params=array(
            'area'=>'Episode',
            'title'=>'Search'
            );
        $app->render("episodeSearch.twig",$params);
    });

    $app->get('/display', $authenticate, function () use ($app){
        $episodes = new \TPS\episodes($_SESSION['CALLSIGN']);
        $filter = $app->request->get("filter");
        echo $episodes -> displayTable($filter);
    });


      $app->get('/edit/:epNum', $authenticate, function ($epNum) use ($app){

        $station = new \TPS\station($_SESSION['CALLSIGN']);
        $episodeVal = \TPS\episode::getEpisodeByEpNum($epNum);  //array, not object
        $programID = \TPS\program::getId($_SESSION['CALLSIGN'], $episodeVal['name']);
	    $traffic = new \TPS\traffic();

        $program = new \TPS\program($station, $programID);
        $episode = new \TPS\episode($program);  //episode object
        $episode->setAttributes($epNum);

        $req = $program->getRequirement();
        $ads = $episode->getAdOptions($req['spons']);
        $commercials =  $episode->getAllCommercials($ads);
        $params = array(
            'area'=>'Episode',
            'title'=>'Log Addition',
            'action' => '/episode/update',
            'req' => $req,
            'ads' => $ads,
            'commercial' => $commercials,
            'sponsorIds' => $traffic->getSponsorID(),
            'sponsorPromos'=>$traffic-> getSponsorPromo(),
	    'radioShowPromos' => $traffic->getPromos(),
	    'PSAs' => $traffic->getPSAs()
        );
        $params["episode"] = $episodeVal;
        $params["songs"] = \TPS\episode::getSongByEpNum($epNum);

        $app->render('episodeInsertSong.twig', $params);
    });


  $app->post('/update', function() use ($app, $authenticate){
        $epNum = $app->request->post('epNum');
        $row = $app->request->post('row');
        $playlisdId = $app->request->post('playlistNum');
        $category = $app->request->post('cat');
        $time = $app->request->post('time');
        $title = $app->request->post('title');
        $artist = $app->request->post('artist');
        $album = $app->request->post('album');
        $composer = $app->request->post('composer');
        $cancon = $app->request->post('cancon')?? array();
        $hit = $app->request->post('hit')?? array();
        $inst = $app->request->post('instrumental')?? array();
        $type = $app->request->post('type');
        $lang = $app->request->post('lang');
        $note = $app->request->post('note');
        $endTime = $app->request->post('endTime');
        $spokenTime = $app->request->post('spokenTime');

        \TPS\episode::updateEndTime($epNum, $endTime);
        \TPS\episode::updateSpokenTime($epNum, $spokenTime);
        \TPS\episode::updateSongs($row, $epNum, $title, $album, $composer, $time, $artist, $cancon, $playlisdId, $type, $category, $hit, $inst, $lang, $note);

        $params = array();
        $params["songs"] = \TPS\episode::getSongByEpNum($epNum);
        $params["title"] = 'View Program Log';
        $params["episode"] = \TPS\episode::getEpisodeByEpNum($epNum);

        $app->render("episodeLog.twig",$params);
    });

    $app->get('/log/:epNum', $authenticate, function ($epNum) use ($app){

        $params = array();
        $playlist = new \TPS\playlist();

        $songs = \TPS\episode::getSongByEpNum($epNum);
        foreach ($songs as $key => $song) {
            $record = $playlist->getAllByShortCode($song["playlistnumber"]);
            $refcode = reset($record)['RefCode'];
            $songs[$key]["RefCode"] = $refcode; 
        }
        $params["songs"] = $songs;
        $params["title"] = 'View Program Log';
        $params["episode"] = \TPS\episode::getEpisodeByEpNum($epNum);

        $app->render("episodeLog.twig",$params);
    });
});
