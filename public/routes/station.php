<?php

/* 
 * The MIT License
 *
 * Copyright 2015 James Oliver <support@ckxu.com>.
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

// station
$app->group('/station', $authenticate, function () use ($app,$authenticate){
    $app->get('/', $authenticate($app,2), function () use ($app){
        $p = $app->request()->get('p');
        $l = $app->request()->get('l');
        $data = array();
        $station = new \TPS\station();
        $callsigns = $station->getStations();
        foreach($callsigns as $callsign=>$name){
            $json = $station->getStation($callsign);
            if(sizeof($json)>0){
                $data[$callsign] = $json[$callsign];
            }
        }
        $params=array(
            'stations'=>$data,
            'area'=>'Administration',
            'title'=>'Manage Stations'
            );
        $app->render('stations.twig',$params);
    });
    $app->get('/:station',$authenticate($app,2), 
            function ($callsign) use ($app,$authenticate){
        $data = array();
        $station = new \TPS\station();
        $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        if($stn = $station->getStation($callsign)){
            $stn = $stn[$callsign];
            $stn['callsign']=$callsign;
        }
        $params=array(
            'station'=>$stn,
            'timezones'=>$tzlist,
            'area'=>'Administration',
            'title'=>'Manage Station',
            );
        $app->render('station.twig',$params);
    });
    $app->put('/:station',$authenticate($app,2), 
            function ($callsign) use ($app,$authenticate){
        $data = array();
        $station = new \TPS\station($callsign);
        $stn = $station->getStation($callsign);
        if(sizeof($stn>0)){
            $stn = $stn[$callsign];
            
            $brand = $app->request->put('brand');
            $address = $app->request->put('address');
            $designation = $app->request->put('designation');
            $frequency = $app->request->put('frequency');
            $website = $app->request->put('website');
            $phoneManager = $app->request->put('phoneManager');
            $phoneDirector = $app->request->put('phoneDirector');
            $phoneMain = $app->request->put('phoneMain');
            $defaultSort = $app->request->put('defaultSort');
            $groupPlaylistProgramming = 
                    $app->request->put('groupPlaylistProgramming');
            $groupPlaylistReporting = 
                    $app->request->put('groupPlaylistReporting');
            $forceComposer = $app->request->put('forceComposer');
            $forceArtist = $app->request->put('forceArtist');
            $forceAlbum = $app->request->put('forceAlbum');
            $displayCounters = $app->request->put('displayCounters');
            $colorPass = $app->request->put('passColor');
            $colorFail = $app->request->put('failColor');
            $colorNote = $app->request->put('noteColor');
            $colorWarning = $app->request->put('warningColor');
            $perHourTraffic = $app->request->put('perHourTraffic');
            $perHourPSAs = $app->request->put('perHourPSAs');
            $timezone = $app->request->put('timezone');
	    $hostProbationDays = $app->request->put('host_probation_days');
	    $hostProbationWeight = $app->request->put('host_probation_weight');
            
            if($brand!=$stn['name']){
                $station->setStationName($brand);
            }
            if($address!=$stn['address']){
                $station->setStationAddress($address);
            }
            if($designation!=$stn['designation']){
                $station->setStationDesignation($designation);
            }
            if($frequency!=$stn['frequency']){
                $station->setStationFrequency($frequency);
            }
            if($website!=$stn['website']){
                $station->setStationWebsite($website);
            }
            if($defaultSort!=$stn['defaultSort']){
                $station->setDefaultSortOrder($defaultSort);
            }
            if($phoneManager!=$stn['phone']['manager']){
                $station->setStationPhoneManager($phoneManager);
            }
            if($phoneDirector!=$stn['phone']['director']){
                $station->setStationPhoneDirector($phoneDirector);
            }
            if($phoneMain!=$stn['phone']['main']){
                $station->setStationPhoneRequest($phoneMain);
            }
            if($groupPlaylistProgramming!=$stn['groupPlaylistProgramming']){
                $station->togglePlaylistLiveGrouping();
            }
            if($groupPlaylistReporting!=$stn['groupPlaylistReporting']){
                $station->togglePlaylistReportingGrouping();
            }
            if($displayCounters!=$station->programCounters()){
                $station->toggleProgramCounters();
            }
            if($perHourPSAs!=$station->hourlyPSA()){
                $station->setHourlyPSA($perHourPSAs);
            }
            if($perHourTraffic!=$station->hourlyTraffic()){
                $station->setHourlyTraffic($perHourTraffic);
            }
            if($forceAlbum!=$station->forceAlbum()){
                $station->toggleForceAlbum();
            }
            if($forceArtist!=$station->forceArtist()){
                $station->toggleForceArtist();
            }
            if($forceComposer!=$station->forceComposer()){
                $station->toggleForceComposer();
            }
	    if($timezone!=$stn['timezone'])
		$station->setStationTimeZone($timezone);
	    if($hostProbationDays!=$stn['hostProbationDays'])
		$station->setHostProbationDays($hostProbationDays);
	    if($hostProbationWeight!=$stn['hostProbationWeight'])
		$station->setHostProbationWeight($hostProbationWeight);
            $app->flash('success',"$callsign updated succesfully");
            $app->redirect("./$callsign");
        }
        else{
            $app->flash("error","Callsign is invalid for update task");
            $app->redirect("./$callsign");
        }
    });
    $app->delete('/:station',$authenticate($app,2), 
            function ($callsign) use ($app,$authenticate){
        $log = new \TPS\logger();
        $log->info("user denied access to delete a station ($station)");
        $app->render('notSupported.twig');
    });
    $app->get('/new/',$authenticate($app,2), 
            function () use ($app,$authenticate){
        $data = array();
        $station = new \TPS\station();
        $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $params=array(
            'timezones'=>$tzlist,
            'area'=>'Administration',
            'title'=>'New Station',
            );
        $app->render('station.twig',$params);
    });
    
    $app->post('/new/', $authenticate($app,2), function() use ($app){
        $brand = $app->request->post('brand');
        $callsign = $app->request->post('callsign');
        $address = $app->request->post('address');
        $designation = $app->request->post('designation');
        $frequency = $app->request->post('frequency');
        $website = $app->request->post('website');
        $phoneManager = $app->request->post('phoneManager');
        $phoneDirector = $app->request->post('phoneDirector');
        $phoneMain = $app->request->post('phoneMain');
        $defaultSort = $app->request->post('defaultSort');
        $groupPlaylistProgramming = 
                $app->request->post('groupPlaylistProgramming');
        $groupPlaylistReporting = 
                $app->request->post('groupPlaylistReporting');
        $forceComposer = $app->request->post('forceComposer');
        $forceArtist = $app->request->post('forceArtist');
        $forceAlbum = $app->request->post('forceAlbum');
        $displayCounters = $app->request->post('displayCounters');
        $colorPass = $app->request->post('passColor');
        $colorFail = $app->request->post('failColor');
        $colorNote = $app->request->post('noteColor');
        $colorWarning = $app->request->post('warningColor');
        $perHourTraffic = $app->request->post('perHourTraffic');
        $perHourPSAs = $app->request->post('perHourPSAs');
        $timezone = $app->request->post('timezone');
        $stationId = \TPS\station::create(
                $callsign, $brand, $designation, $frequency, $website, 
                $address, $phoneMain, $phoneManager);
        $station = new \TPS\station($callsign);
        $station->setStationPhoneDirector($phoneDirector);
        $station->setDefaultSortOrder($defaultSort);
        $station->forceComposer($forceComposer);
        $station->forceArtist($forceArtist);
        $station->forceAlbum($forceAlbum);
        $groupPlaylistReporting?$station->PlaylistReportingGroupingOn():
            $station->PlaylistReportingGroupingOff();
        $groupPlaylistProgramming?$station->playlistLiveGroupingOn():
            $station->playlistLiveGroupingOff();
        $displayCounters?$station->programCountersOn():
            $station->programCountersOff();
        //$app->render("notSupported.twig");
        $app->redirect("../$callsign");
    });
    
    $app->post('/',$authenticate($app,2), function () use ($app,$authenticate){
        $data = array();
        $station = new \TPS\station();
        $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $params=array(
            'timezones'=>$tzlist,
            'area'=>'Administration',
            'title'=>'New Station',
            );
        $app->render('station.twig',$params);
    });
    
    $app->group('/category', $authenticate, 
            function () use ($app,$authenticate){
        $app->get('/:station', $authenticate($app,[1,2]), 
                function ($callsign) use ($app){
            $station = new \TPS\station($callsign);
            $genres = $station->genres->all();
            $isXHR = $app->request->isAjax();
            if($isXHR){
                standardResult::ok($app, $genres);
            }
            else{
                $params = array(
                    'area'=>'Station Management',
                    'title'=>'Category',
                    'genres' => $genres,
                    'callsign' => $callsign
                );
                $app->render("genreList.twig", $params);
            }
        });
        $app->post('/:station/', $authenticate($app,[2]),
                function ($callsign) use ($app){
            $station = new \TPS\station($callsign);
            $name = $app->request->post('name');
            $govReq = $app->request->post('govReq')?:0;
            $govReqPerc = $app->request->post('govReqPerc')/100?:0;
            $govReqType = $app->request->post('govReqType')?:1;
            $playlist = $app->request->post('playlist')?:0;
            $playlistPerc = $app->request->post('playlistPerc')/100?:0;
            $playlistType = $app->request->post('playlistType')?:1;
            $femcon = $app->request->post('femcon')?:0;
            $femconPerc = $app->request->post('femconPerc')/100?:0;
            $femconType = $app->request->post('femconType')?:1;
            $colorPrimary = $app->request->post('colorPrimary');
            $id = $station->genres->create($name,$govReq, $govReqPerc, 
                    $govReqType, $playlist, $playlistPerc, $playlistType, 
                    $femcon, $femconType, $femconPerc, $colorPrimary);
            $isXHR = $app->request->isAjax();
            if($isXHR){
                header("Content-Type: application/json");
                json_encode($id);
            }
            else{
                $app->redirect("./$id");
            }
        });
        // station/genre/####/$$$$
        $app->get('/:station/', $authenticate($app,[2]),
                function ($callsign) use ($app){
            $station = new \TPS\station($callsign);
            $isXHR = $app->request->isAjax();
            if($isXHR){
                header("Content-Type: application/json");
                print json_encode([]);
            }
            else{
                $params = array(
                    'area'=>'Station Management',
                    'title'=>'Category',
                    'callsign' => $callsign
                );
                $app->render("genre.twig", $params);
            }
        });
        $app->get('/:station/:id', $authenticate($app,[1,2]),
                function ($callsign,$id) use ($app){
            $station = new \TPS\station($callsign);
            $genre = $station->genres->get($id);
            $isXHR = $app->request->isAjax();
            if($isXHR){
                header("Content-Type: application/json");
                json_decode($genre);
            }
            else{
                $params = array(
                    'area'=>'Station Management',
                    'title'=>'Genre',
                    'genres' => $genre,
                    'callsign' => $callsign
                );
                $app->render("genre.twig", $params);
            }
        });
        $app->put('/:station/:id', $authenticate($app,[2]),
                function ($callsign,$id) use ($app){
            $station = new \TPS\station($callsign);
            $name = $app->request->put('name');
            $UID = $app->request->put('UID');
            $govReq = $app->request->put('govReq')?:0;
            $govReqPerc = $app->request->put('govReqPerc')/100?:0;
            $govReqType = $app->request->put('govReqType')?:0;
            $playlist = $app->request->put('playlist')?:0;
            $playlistPerc = $app->request->put('playlistPerc')/100?:0;
            $playlistType = $app->request->put('playlistType')?:0;
            $femcon = $app->request->put('femcon')?:0;
            $femconPerc = $app->request->put('femconPerc')/100?:0;
            $femconType = $app->request->put('femconType')?:0;
            $colorPrimary = $app->request->put('genreColor');
            $id = $station->genres->change($name, $UID, $govReq, $govReqPerc, 
                    $govReqType, $playlist, $playlistPerc, $playlistType, 
                    $femcon, $femconType, $femconPerc, $colorPrimary);
            $isXHR = $app->request->isAjax();
            if($isXHR){
                header("Content-Type: application/json");
                json_encode($id);
            }
            else{
                $app->redirect("./$id");
            }
        });
        $app->delete('/:station/:id', $authenticate($app,[2]),
                function ($callsign,$id) use ($app){
            $isXHR = $app->request->isAjax()?:
                    $app->request->get("format")?:False;
            $station = new \TPS\station($callsign);
            $result = $station->genres->delete($id);
            if(!$result[$id]){
                $app->flash('error', "could not delete genre '$id'");
            }
            if($isXHR){
                print json_encode($result);
                return false;
            }
            $app->redirect("../$callsign");            
        });
        $app->options('/:station/:id', $authenticate($app,[1,2]),
                function ($callsign,$id) use ($app){
            $station = new \TPS\station($callsign);
        });
    });
    
});
