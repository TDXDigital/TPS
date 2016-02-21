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

$playlist = new \TPS\playlist();

$app->group('/playlist', function() use ($app, $authenticate, $playlist){
    $app->get('/', function() use ($app, $playlist){
        $isXHR = $app->request->isAjax();
        $page = (int)$app->request->get("p")?:1;
        $limit = (int)$app->request->get("l")?:25;
        $refcodes = $app->request->get("refcodes");
        $startDate = $app->request->get("startDate")?:"1000-01-01";
        $endDate = $app->request->get("endDate")?:"9999-12-31";
        if($refcodes){
            $result = $playlist->getByRefCode($refcodes, $startDate, $endDate);
        }
        else{
            $result = $playlist->getAll( $startDate, $endDate, $page, $limit);
        }
        if($isXHR){
            standardResult::ok($app, $result, NULL);
        }
        else{
            standardResult::ok($app, $result, NULL);
        }
    });
    $app->post('/', function() use ($app, $playlist){
        $isXHR = $app->request->isAjax();
        $refCodes = $app->request->post("refCode");
        $startDate = $app->request->post("startDate");
        $endDate = $app->request->post("endDate");
        if(is_null($refCodes) || is_null($startDate) || is_null($endDate)){
            standardResult::badRequest($app, 
                    array($refCodes, $startDate, $endDate),
                    "Required Parameter missing");
        }
        $result = $playlist->create($refCodes, $startDate, $endDate);
        if($isXHR){
            standardResult::created($app, $result, NULL);
        }
        else{
            standardResult::created($app, $result, NULL);
        }
    });
    $app->get('/:id', function($refCodes) use ($app, $playlist){
        $isXHR = $app->request->isAjax();
        $result = $playlist->get($refCodes);
        if($isXHR){
            standardResult::created($app, $result, NULL);
        }
        else{
            standardResult::created($app, $result, NULL);
        }
    });
});