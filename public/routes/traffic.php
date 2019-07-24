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
    $app->get('/new-contract', function() use ($app){
        $params = array(
	    "area"=>"Traffic",
            "title"=>"New Contract",
        );
        $app->render("contract.twig", $params);
    });
    $app->post('/new-contract', function() use ($app){
//        $refCodes = $app->request->post("refCode");
    });

    $app->get('/new', function() use ($app){
        $params = array(
        "area"=>"Traffic",
        "title"=>"New",
         );
         $app->render("trafficNew.twig", $params);
    });

    $app->post('/create', function() use ($app){
       
       print_r($_POST);

       $advertiser = $app->request->post('advertiser');
       $cat = $app->request->post('cat');
       $lang = $app->request->post('lang');
       $startDate = $app->request->post('startDate');
       $endDate = $app->request->post('endDate');
       $active = $app->request->post('active') ?? 0;
       $friend = $app->request->post('friend') ?? 0;
       
       
        $params = array(
        "area"=>"Traffic",
        "title"=>"New",
        // "host"=>['probationEnds'=>$probationEnds],
        // "station"=>['probationMultiplier'=>$probationMultiplier]
         );
         $app->render("trafficNew.twig", $params);
    });
});

