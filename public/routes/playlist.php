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
        $count = ceil($playlist->countAll()/$limit);
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
            #standardResult::ok($app, $result, NULL);
            $library = new \TPS\library();
            foreach ($result as $key => $value) {
                $lib = $library->getAlbumByRefcode($value['RefCode']);
                $result[$key]["library"] = array_pop($lib);
            }
            $params = array(
                "title"=>"Playlist",
                "playlists"=>$result,
                "page"=>$page,
                "pages"=>$count,
                "limit"=>$limit,
            );
            $app->render("playlists.twig", $params);
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
            $app->redirect("./".array_pop($result));
        }
    });
    $app->get('/:id', function($refCodes) use ($app, $playlist){
        $isXHR = $app->request->isAjax();
        $id = $app->request->get("refCode");
        $result = $playlist->get($refCodes);
        if(sizeof($result)<1 && $id){
            $result[null] = array("RefCode"=>$id);
        }
        if(sizeof($result)<1 && strtolower($refCodes)!='new'){
            $app->notFound();
        }
        if($isXHR){
            standardResult::ok($app, array("refCode", "startDate", "endDate"), NULL);
        }
        else{
            $library = new \TPS\library();
            foreach ($result as $key => $value) {
                $lib = $library->getAlbumByRefcode($value['RefCode']);
                $result[$key]["library"] = $lib;
            }
            $params = array(
                "title"=>"Playlist",
                "playlists"=>$result,
            );
            $app->render("playlist.twig", $params);
        }
    });
    $app->put('/:id', function($refCodes) use ($app, $playlist){
        $isXHR = $app->request->isAjax();
        $startDate = $app->request->put("startDate");
        $endDate = $app->request->put("endDate");
        $zoneNumber = $app->request->put("zoneNumber");
        $zoneCode = $app->request->put("zoneCode");
        $smallCode = $app->request->put("smallCode");
        $result = $playlist->change($refCodes, $startDate, $endDate, 
                $zoneCode, $zoneNumber, $smallCode);
        if($isXHR){
            standardResult::accepted($app, array("refCode", "startDate", "endDate"), NULL);
        }
        else{
            $app->redirect("./$refCodes");
        }
    });
});