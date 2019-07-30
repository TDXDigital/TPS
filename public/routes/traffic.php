<?php
/* 
 * The MIT License
 *
 * Copyright 2016 J.oliver.
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

$app->get('/traffic', function () use ($app) {
    $app->redirect('./traffic/');
});

$app->group('/traffic', function() use ($app, $authenticate){
    $app->get('/new', function() use ($app){
	$traffic = new \TPS\traffic();
        $station = new \TPS\station($_SESSION['CALLSIGN']);
        $programs = $station->getAllPrograms();
        $params = array(
            "area"=>"Traffic",
            "title"=>"New",
    	    "clients"=> $traffic->getClientsNames(),
            "programs"=> $programs
        );
        $app->render("trafficNew.twig", $params);
    });

    $app->post('/create', function() use ($app){

    $traffic = new \TPS\traffic();

    $cat = $app->request->post('cat');
    $psa = $app->request->post('psa') ?: 0;
    $clientID = $app->request->post('clientID') ?: NULL;
    $clientName = $app->request->post('client') ?: NULL;
    $company = $app->request->post('company') ?: NULL;
    $contactEmail = $app->request->post('email') ?: NULL;
    $clientPhone = $app->request->post('phone') ?: NULL;
    $adName = $app->request->post('adName');
    $maxPlayCount = $app->request->post('maxPlayCount') ?: NULL;
    $maxDailyPlayCount = $app->request->post('maxDailyPlayCount') ?: NULL;
    $assignedShow = $cat==51? $app->request->post('assignedShow') : $app->request->post('assignedShowSponsor');
    $assignedHour = $cat==51? $app->request->post('assignedHour') :$app->request->post('assignedHourSponsor');
    $backingTrack = $app->request->post('song') ?: NULL;
    $backingArtist = $app->request->post('artist') ?: NULL;
    $backingAlbum = $app->request->post('album') ?: NULL;
    $showName =  $app->request->post('showName') ?: NULL;
    // $showDayTimes = ['Sun' => [['12:00', '14:00'], ['16:30', '18:00']], 'Tue' => [['12:00', '14:00']]];

    $showDayTimes = array();
    $showDays = $app->request->post('showDayVal');
    if(is_array($showDays))
    {
        $showTimeStartVal = $app->request->post('showTimeStartVal');
        $showTimeEndVal = $app->request->post('showTimeEndVal');
        foreach($showDays as $key => $day)
        {
	    $duration = [$showTimeStartVal[$key], $showTimeEndVal[$key]];
	    if (in_array($day, array_keys($showDayTimes)))
		array_push($showDayTimes[$day], $duration);
	    else
		$showDayTimes[$day] = [$duration];
        }
    }
    
    // $title =  $app->request->post('title') ?: NULL;
    $length = $app->request->post('length');
    $lang = $app->request->post('lang') ?: 'English';
    $startDate = $app->request->post('startDate');
    $endDate = $app->request->post('endDate');
    $active = $app->request->post('active') ?? 0;
    $friend = $app->request->post('friend') ?? 0;

    if ($clientID == NULL) {
	if ($clientName != NULL)
	    $clientID = $traffic->createClient($clientName, $company, $contactEmail, $clientPhone);
    } else {
	$traffic->updateClient($clientID, $clientName, $company, $contactEmail, $clientPhone);
    }

    $id = $traffic->createNewAd($adName, $cat, $length, $lang, $startDate, $endDate, $active, $friend, $clientID, 
				$maxPlayCount, $maxDailyPlayCount, $assignedShow, $assignedHour, $backingTrack,
				$backingArtist, $backingAlbum, $showName, $showDayTimes, $psa);
    $ad = $traffic->get($id);

    if($id == -1 )
        $flash['error'] = 'Failed to Create new Ad';
    else
        $flash['success'] = 'Created new Ad';

    $traffic = new \TPS\traffic();
    $station = new \TPS\station($_SESSION['CALLSIGN']);
    $params = array(
        "area"=>"Traffic",
        "title"=>"New",
        "ad"=>$ad,
        "flash" => $flash,
    	"clients"=> $traffic->getClientsNames(),
        "programs"=> $station->getAllPrograms()
    );
    $app->render("trafficSearch.twig", $params);
});

    $app->get('/search', function() use ($app, $authenticate){
     $params = array(
        "area"=>"Traffic",
        "title"=>"Search",
    );
     $app->render("trafficSearch.twig", $params);
 });

    $app->get('/display', $authenticate, function () use ($app){
        $traffic = new \TPS\traffic();
        $filter = $app->request->get("filter");
        echo $traffic -> displayTable($filter);
    });

    $app->get('/edit/:id', $authenticate, function ($id) use ($app){
        $traffic = new \TPS\traffic();
        $station = new \TPS\station($_SESSION['CALLSIGN']);
        $programs = $station->getAllPrograms();
        $ad = $traffic->get($id);
    	$clients = $traffic->getClientsNames();
    	$client = $traffic->getClientByID($ad['ClientID']);
    	$promo = $traffic->getPromoInfo($id);
        // print_r($promo);
        // exit;
        $params = array(
	    "area" => "Traffic",
	    "title" => "Edit",
        "ad"=>$ad,
	    "clients" => $clients,
	    "client" => $client,
	    "promo" => $promo,
        "programs"=> $programs
        );
        $app->render("trafficNew.twig", $params);
    });

    $app->post('/update', function() use ($app){

        $traffic = new \TPS\traffic();
        $adId = $app->request->post('adNumber');
        $cat = $app->request->post('catUpdate');
        $psa = $app->request->post('psa') ?: 0;
        $clientID = $app->request->post('clientID') ?: NULL;
        $clientName = $app->request->post('client') ?: NULL;
        $company = $app->request->post('company') ?: NULL;
        $contactEmail = $app->request->post('email') ?: NULL;
        $clientPhone = $app->request->post('phone') ?: NULL;
        $adName = $cat == 12 ? $app->request->post('title') : $app->request->post('adName');
        $maxPlayCount = $app->request->post('maxPlayCount') ?: NULL;
        $maxDailyPlayCount = $app->request->post('maxDailyPlayCount') ?: NULL;
        $assignedShow = $cat==51? $app->request->post('assignedShow') : $app->request->post('assignedShowSponsor');
        $assignedHour = $cat==51? $app->request->post('assignedHour') :$app->request->post('assignedHourSponsor');
        $backingTrack = $app->request->post('song') ?: NULL;
        $backingArtist = $app->request->post('artist') ?: NULL;
        $backingAlbum = $app->request->post('album') ?: NULL;
        $showName =  $app->request->post('showName') ?: NULL;
        // $showDayTimes = ['Sun' => [['12:00', '14:00'], ['16:30', '18:00']], 'Tue' => [['12:00', '14:00']]];

        $showDayTimes = array();
        $showDays = $app->request->post('showDayVal');
        if(is_array($showDays))
        {
            $showTimeStartVal = $app->request->post('showTimeStartVal');
            $showTimeEndVal = $app->request->post('showTimeEndVal');
            foreach($showDays as $key => $day)
            {
            $duration = [$showTimeStartVal[$key], $showTimeEndVal[$key]];
            if (in_array($day, array_keys($showDayTimes)))
            array_push($showDayTimes[$day], $duration);
            else
            $showDayTimes[$day] = [$duration];
            }
        }
        
        // $title =  $app->request->post('title') ?: NULL;
        $length = $app->request->post('length');
        $lang = $app->request->post('lang') ?: 'English';
        $startDate = $app->request->post('startDate');
        $endDate = $app->request->post('endDate');
        $active = $app->request->post('active') ?? 0;
        $friend = $app->request->post('friend') ?? 0;

	if ($clientID == NULL) {
    if ($clientName != NULL)
        $clientID = $traffic->createClient($clientName, $company, $contactEmail, $clientPhone);
    } else {
    $traffic->updateClient($clientID, $clientName, $company, $contactEmail, $clientPhone);
    }
	$client = $traffic->getClientByID($clientID);

        $id = $traffic ->updateAd($adId, $adName, $cat, $length, $lang, $startDate, $endDate, $active, $friend, $clientID,
				  $maxPlayCount, $maxDailyPlayCount, $assignedShow, $assignedHour, $backingTrack,
				  $backingArtist, $backingAlbum, $showName, $showDayTimes, $psa);
        $ad = $traffic->get($id);
	$promoInfo = $traffic->getPromoInfo($id);

        $flash = array();
        if($id == -1 )
            $flash['error'] = 'Failed to Update the Ad';
        else
            $flash['success'] = 'Updated the Ad';
        $params = array(
            "area"=>"Traffic",
            "title"=>"Update",
            "ad"=>$ad,
            "flash" => $flash
        );
        $app->render("trafficSearch.twig", $params);
    });

    $app->post('/searchClient/:clientId', function($clientId) use ($app, $authenticate){
        $traffic = new \TPS\traffic();
        $clientInfo = $traffic->getClientByID($clientId);

        echo json_encode($clientInfo);
    });
});

