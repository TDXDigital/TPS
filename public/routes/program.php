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




$app->get('/programs', function () use ($app) {
    $app->redirect('./programs/');
});

$app->group('/programs', $authenticate, function () use ($app,$authenticate){
    $app->group('/list',$authenticate($app,2),
            function () use ($app,$authenticate){
        $app->get('/:station/active',$authenticate($app,2),
                function ($callsign) use ($app,$authenticate){
            $station = new \TPS\station($callsign);
            if($stn = $station->getStation($callsign)){
                $stn = $stn[$callsign];
                $stn['callsign']=$callsign;
            }
            $progams=array();
            $programs = $station->getAllProgramIds(True);
            sort($programs);
            foreach ($programs as $value) {
                $program = new \TPS\program($station, $value);
                $progams[$value] = $program->getValues()?:array();
            }

            $params=array(
                'station'=>$stn,
                'programs'=>$progams,
                );
            var_dump($params);
            #$app->render('notSupported.twig',$params);
        });

        $app->get('/:station/all',$authenticate($app,2),
                function ($callsign) use ($app,$authenticate){
            $station = new \TPS\station($callsign);
            //$tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
            if($stn = $station->getStation($callsign)){
                $stn = $stn[$callsign];
                $stn['callsign']=$callsign;
            }
            $progams=array();
            $programs = $station->getAllProgramIds(True);
            sort($programs);
            $programsInact = $station->getAllProgramIds(True);
            sort($programsInact);
            $allPrograms = array_merge($programs, $programsInact);
            foreach ($allPrograms as $value) {
                $program = new \TPS\program($station, $value);
                $progams[$value] = $program->getValues()?:array();
            }
            $params=array(
                'station'=>$stn,
                //'timezones'=>$tzlist,
                'area'=>'Administration',
                'title'=>'Manage Station',
                'programs'=>$progams,
                );
            var_dump($params);
            //$app->render('notSupported.twig',$params);
        });
    });

  $app->get('/new', function() use ($app, $authenticate){

    $station = new \TPS\station($_SESSION['CALLSIGN']);
    $program = new \TPS\program($station,180);


    $program->getRequirement();


    $stations = $station->getStations();
    $hosts = $station->getHosts();
    $programGenre = $program->getProgramGenre();
    $params = array(
	    "area"=>"Programs",
            "title"=>"New Program",
            "sessionStation" => $_SESSION['CALLSIGN'],
            "station"=>$stations,
            "hosts"=>$hosts,
            "genre"=>$programGenre
        );
         $app->render("programNew.twig", $params);
    });


  // Create new program
    $app->post('/create', function() use ($app, $authenticate){

        $station = new \TPS\station($_SESSION['CALLSIGN']);
        $stations = $station->getStations();
        $program = new \TPS\program($station);

        $callsign = $station->getCallsign();
        $progname = $app->request->post('progName');
        $stationName = $app->request->post('station');
        $length = $app->request->post('length');
        $syndicate = $app->request->post('syndicate');
        $host = $app->request->post('host');
        $genre = $app->request->post('genre');
        $weight = $app->request->post('weight');
        $active = $app->request->post('active')!==null? 1:0;
       
        $program -> createNewProgram($stationName, $progname, $length, $syndicate, $host, $genre, $weight, $active);

        $app->redirect('./search');
    });

    //  program search
    $app->get('/search', function() use ($app, $authenticate){
        $params = array(
            "area" => "Programs",
            "title" => "Search",
        );
        $app->render('programSearch.twig',$params);
    });

    // program search table
    $app->get('/display', $authenticate, function () use ($app){
        $station = new \TPS\station($_SESSION['CALLSIGN']);
        $stations = $station->getStations();
        $program = new \TPS\program($station);
        $filter = $app->request->get("filter");
        echo $program -> displayTable($filter);
    });

     $app->get('/edit/:id', $authenticate, function ($id) use ($app){

        $station = new \TPS\station($_SESSION['CALLSIGN']);
        $stations = $station->getStations();
        $hosts = $station->getHosts();
        $program = new \TPS\program($station, $id);
        $programToEdit = $program->getValues();
        $programGenre = $program->getProgramGenre();

        
        $params = array(
            "title"=>"Edit Program",
            "sessionStation" => $_SESSION['CALLSIGN'],
            "station"=>$stations,
            "hosts"=>$hosts,
            "program"=>$programToEdit,
            "genre"=>$programGenre
        );

            $app->render('programNew.twig', $params);
    });

    $app->get('/history/:progname', $authenticate, function ($progname) use ($app){
        $app->redirect('../../legacy/oep/EPV3/Audit.php?programName='.$progname);
    });




}); 
