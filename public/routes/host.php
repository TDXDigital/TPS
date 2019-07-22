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




$app->get('/host', function () use ($app) {
    $app->redirect('./host/');
});

$app->group('/host', $authenticate, function () use ($app,$authenticate){

  $app->get('/new', function() use ($app, $authenticate){
	$callsign = $_SESSION['CALLSIGN'];
	$station = new \TPS\station();
	$station = $station->getStation($callsign);
	$probationDays = $station[$callsign]['hostProbationDays'];
	$probationEnds = date('Y-m-d', strtotime("+{$probationDays} days"));
	$probationMultiplier = $station[$callsign]['hostProbationWeight'];

        $params = array(
	    "area"=>"Host",
            "title"=>"New",
	    "host"=>['probationEnds'=>$probationEnds],
	    "station"=>['probationMultiplier'=>$probationMultiplier]
         );
         $app->render("hostNew.twig", $params);
    });


  $app->post('/create', function() use ($app, $authenticate){
    $host = new \TPS\host($_SESSION['CALLSIGN']);

    $djname = $app->request->post('hostName');
    $alias = $app->request->post('alias');
    $years = $app->request->post('JoinedYear');
    $weight = $app->request->post('weight');
    $active = $app->request->post('active')!==null? 1:0;
    $probEndDate = $app->request->post('probEndDate') ?: date('Y-m-d');
   
    $host -> createHost($alias, $djname, $active, $years, $weight, NULL, NULL, NULL, $probEndDate);
    $app->redirect('./search');
    
    });

    $app->get('/search', function() use ($app, $authenticate){
         $params = array(
            "area"=>"Host",
            "title"=>"Search",
        );
        $app->render("hostSearch.twig", $params);
    });

     // Host search table
    $app->get('/display', $authenticate, function () use ($app){
        $host = new \TPS\host($_SESSION['CALLSIGN']);
        $filter = $app->request->get("filter");
        echo $host -> displayTable($filter);
    });

     $app->get('/edit/:alias', $authenticate, function ($alias) use ($app){
        $host = new \TPS\host($_SESSION['CALLSIGN']);
        $hostToEdit = $host->get($alias);

	$callsign = $_SESSION['CALLSIGN'];
	$station = new \TPS\station();
	$station = $station->getStation($callsign);
	$probationMultiplier = $station[$callsign]['hostProbationWeight'];

        $params = array(
	    "area"=>"Host",
            "title"=>"Edit",
            "host"=>$hostToEdit,
	    "station"=>['probationMultiplier'=>$probationMultiplier]
        );
        $app->render('hostNew.twig', $params);
    });



}); 
