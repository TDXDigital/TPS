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
    $app->get('/:station',$authenticate($app,2), function ($callsign) use ($app,$authenticate){
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
    $app->put('/:station',$authenticate($app,2), function ($callsign) use ($app,$authenticate){
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
            $groupPlaylistProgramming = $app->request->put('groupPlaylistProgramming');
            $groupPlaylistReporting = $app->request->put('groupPlaylistReporting');
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
                #$station->setGroupPlaylistProgramming($groupPlaylistProgramming);
            }
            if($groupPlaylistReporting!=$stn['groupPlaylistReporting']){
                #$station->setGroupPlaylistProgramming($groupPlaylistReporting);
            }
            if($displayCounters!=$stn['displayCounters']){
                if($displayCounters){
                    $station->programCountersOn();
                }
                else{
                    $station->programCountersOff();
                }
            }
            $app->flash('success',"$callsign updated succesfully");
            $app->redirect("./$callsign");
        }
        else{
            $app->flash("error","Callsign is invalid for update task");
            $app->redirect("./$callsign");
        }
    });
    $app->delete('/:station',$authenticate($app,2), function ($callsign) use ($app,$authenticate){
        $app->render('notSupported.twig');
    });
    $app->get('/new/',$authenticate($app,2), function () use ($app,$authenticate){
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
});
