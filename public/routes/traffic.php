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
        $clientInfo = $traffic->getClientByID(1);
        $app->render("trafficNew.twig", $params);
    });

    $app->post('/create', function() use ($app){

    $traffic = new \TPS\traffic();

    $cat = $app->request->post('cat');
    $clientName = $app->request->post('client');
    $company = $app->request->post('company');
    $contactEmail = $app->request->post('email');
    $clientPhone = $app->request->post('phone');
    $adName = $app->request->post('adName');
    $maxPlayCount = $app->request->post('maxPlayCount');
    $maxDailyPlayCount = $app->request->post('maxDailyPlayCount');
    $assignedShow = $cat==52? $app->request->post('assignedShowSponsor') : $app->request->post('assignedShow');
    $assignedHour = $cat==52? $app->request->post('assignedHourSponsor') :$app->request->post('assignedHour');
    $backingTrack = $app->request->post('song');
    $backingArtist = $app->request->post('artist');
    $backingAlbum = $app->request->post('album');
    $showName =  $app->request->post('showName');
    // $showDayTimes = [0 => [['start' => '12:00', 'end' => '14:00'], ['start' => '16:30', 'end' => '18:00']], 3 => [['start' => '12:00', 'end' => '14:00']]];

    $showDayTimes = array();
    $showDays = $app->request->post('showDayVal');
    if(is_array($showDays))
    {
        $showTimeStartVal = $app->request->post('showTimeStartVal');
        $showTimeEndVal = $app->request->post('showTimeEndVal');
        foreach($showDays as $key => $day)
        {
            $showDayTimes[$key] = array(
                'day' => $showDays[$key],
                'start' => $showTimeStartVal[$key],
                'end' => $showTimeEndVal[$key]
            );
        }
    }
    
    $title =  $app->request->post('title');
    $language =  $app->request->post('lang');
    $length = $app->request->post('length');
    $lang = $app->request->post('lang');
    $startDate = $app->request->post('startDate');
    $endDate = $app->request->post('endDate');
    $active = $app->request->post('active') ?? 0;
    $friend = $app->request->post('friend') ?? 0;

    $clientID = $app->request->post('clientID');
    if ($clientID == NULL) 
	$clientID = $traffic->createClient($clientName, $company, $contactEmail);
    else
	$traffic-updateClient($clientID, $clientName, $company, $contactEmail);

    $id = $traffic->createNewAd($adName, $cat, $length, $lang, $startDate, $endDate, $active, $friend, $clientID, 
				$maxPlayCount, $maxDailyPlayCount, $assignedShow, $assignedHour, $backingTrack,
				$backingArtist, $backingAlbum, $showName, $showDayTimes);
    $ad = $traffic->get($id);

    if($id == -1 )
        $flash['error'] = 'Failed to Create new Ad';
    else
        $flash['success'] = 'Created new Ad';

    $params = array(
        "area"=>"Traffic",
        "title"=>"New",
        "ad"=>$ad,
        "flash" => $flash
    );
    $app->render("trafficNew.twig", $params);
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
        $ad = $traffic->get($id);
	$clients = $traffic->getClientsNames();
	$client = $traffic->getClientByID($ad['ClientID']);
	$promo = $traffic->getPromoInfo($id);

        $params = array(
	    "area" => "Traffic",
	    "title" => "Edit",
            "ad"=>$ad,
	    "clients" => $clients,
	    "client" => $client,
	    "promo" => $promo
        );
        $app->render("trafficNew.twig", $params);
    });

    $app->post('/update', function() use ($app){

        $traffic = new \TPS\traffic();
        $adId = $app->request->post('adNumber');
        $adName = $app->request->post('adName');
        $cat = $app->request->post('cat');
        $length = $app->request->post('length');
        $lang = $app->request->post('lang');
        $startDate = $app->request->post('startDate');
        $endDate = $app->request->post('endDate');
        $active = $app->request->post('active') ?? 0;
        $friend = $app->request->post('friend') ?? 0;

        // Need to receive the following info from post as UI is built...
        $clientName = 'Toys Inc.';
    	$company = 'Nike';
    	$contactEmail = 'email@email.com';
        $clientID = $app->request->post('clientID') ?? 34;
        $maxPlayCount = Null;
        $maxDailyPlayCount = Null;
        $assignedShow = Null;
        $assignedHour = Null;
        $backingTrack = Null;
        $backingArtist = Null;
        $backingAlbum = Null;
//      $showName = Null;
        $showName = 'Derek\'s Show!';
//      $showDayTimes = [];
        $showDayTimes = [0 => [['12:00', '14:00'], ['16:30', '18:00']], 3 => [['12:00', '14:00']]];
        $title = Null;

	if ($clientID == NULL)
	    $clientID = $traffic->createClient($clientName, $ompany, $contactEmail);
	else
	    $traffic->updateClient($clientID, $clientName, $company, $contactEmail);
	$client = $traffic->getClientByID($clientID);

        $id = $traffic ->updateAd($adId, $adName, $cat, $length, $lang, $startDate, $endDate, $active, $friend, $clientID,
				  $maxPlayCount, $maxDailyPlayCount, $assignedShow, $assignedHour, $backingTrack,
				  $backingArtist, $backingAlbum, $showName, $showDayTimes);
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
        $app->render("trafficNew.twig", $params);
    });

    $app->post('/searchClient/:clientId', function($clientId) use ($app, $authenticate){
        $traffic = new \TPS\traffic();
        $clientInfo = $traffic->getClientByID($clientId);
        // echo $playlistId;
        // print_r(reset($albumInfo));
        echo json_encode($clientInfo);
    });
});

