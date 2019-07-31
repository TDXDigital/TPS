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
    $showDays = $app->request->post('showDayVal');
    $showTimeStartVals = $app->request->post('showTimeStartVal');
    $showTimeEndVals = $app->request->post('showTimeEndVal');
    $showSchedule = $traffic->createSchedule($showDays, $showTimeStartVals, $showTimeEndVals);

//    $hourlyLimit =  $app->request->post('hourlyLimit') ?: NULL;
    $hourlyLimits = [1, 1, 1, 2];
//    $blockLimit =  $app->request->post('blockLimit') ?: NULL;
    $blockLimits = [1, 2, 3, 1];
//    $adDays = $app->request->post('adDayVal');
    $adDays = ['Mon', 'Fri', 'Fri', 'Sun'];
//    $adTimeStarts = $app->request->post('adTimeStartVal');
    $adTimeStarts = ['12:00', '11:30', '16:00', '6:00'];
//    $adTimeEnds = $app->request->post('adTimeEndVal');
    $adTimeEnds = ['2:00', '1:30', '18:00', '8:00'];

    $adSchedules = [];
    $i = 0;
    while ($i < count($adDays)) {
	$adSchedule = $traffic->createSchedule([$adDays[$i]], [$adTimeStarts[$i]], [$adTimeEnds[$i]]);
	array_push($adSchedules, $adSchedule);
	$i++;
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
				$backingArtist, $backingAlbum, $showName, $showSchedule, $psa);
    $ad = $traffic->get($id);

    $traffic->setAdRequirements($id, $adSchedules, $hourlyLimits, $blockLimits);

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

    $app->get('/schedule', function() use ($app, $authenticate){
        $params = array(
            "area"=>"Traffic",
            "title"=>"Requirement",
        );
         $app->render("trafficSchedule.twig", $params);
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
        $adName = $cat == 12 ? $app->request->post('adName') : $app->request->post('title');
        $maxPlayCount = $app->request->post('maxPlayCount') ?: NULL;
        $maxDailyPlayCount = $app->request->post('maxDailyPlayCount') ?: NULL;
        $assignedShow = $cat==51? $app->request->post('assignedShow') : $app->request->post('assignedShowSponsor');
        $assignedHour = $cat==51? $app->request->post('assignedHour') :$app->request->post('assignedHourSponsor');
        $backingTrack = $app->request->post('song') ?: NULL;
        $backingArtist = $app->request->post('artist') ?: NULL;
        $backingAlbum = $app->request->post('album') ?: NULL;
        $showName =  $app->request->post('showName') ?: NULL;
        $showDays = $app->request->post('showDayVal');
        $showTimeStartVals = $app->request->post('showTimeStartVal');
        $showTimeEndVals = $app->request->post('showTimeEndVal');
        $showSchedule = $traffic->createSchedule($showDays, $showTimeStartVals, $showTimeEndVals);

        $hourlyLimit =  $app->request->post('hourlyLimit') ?: NULL;
        $blockLimit =  $app->request->post('blockLimit') ?: NULL;
        $adDays = $app->request->post('adDayVal');
        $adStarts = $app->request->post('adTimeStartVal');
        $adEnds = $app->request->post('adTimeEndVal');
//        $hourlyLimit =  $app->request->post('hourlyLimit') ?: NULL;
        $hourlyLimits = [1, 1, 1, 2];
//        $blockLimit =  $app->request->post('blockLimit') ?: NULL;
        $blockLimits = [1, 2, 3, 1];
//        $adDays = $app->request->post('adDayVal');
        $adDays = ['Mon', 'Fri', 'Fri', 'Wed'];
//        $adTimeStarts = $app->request->post('adTimeStartVal');
        $adTimeStarts = ['12:00', '11:30', '16:00', '7:00'];
//        $adTimeEnds = $app->request->post('adTimeEndVal');
        $adTimeEnds = ['2:00', '1:30', '18:00', '8:00'];

        $adSchedules = [];
        $i = 0;
        while ($i < count($adDays)) {
    	    $adSchedule = $traffic->createSchedule([$adDays[$i]], [$adTimeStarts[$i]], [$adTimeEnds[$i]]);
	    array_push($adSchedules, $adSchedule);
	    $i++;
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
				  $backingArtist, $backingAlbum, $showName, $showSchedule, $psa);
        $ad = $traffic->get($id);
	$promoInfo = $traffic->getPromoInfo($id);

        $traffic->setAdRequirements($id, $adSchedules, $hourlyLimits, $blockLimits);

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

