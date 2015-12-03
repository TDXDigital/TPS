<?php

/* 
 * The MIT License
 *
 * Copyright 2015 J.oliver.
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

// labels
$app->group('/label', $authenticate, function () use ($app,$authenticate){
    $app->get('/', $authenticate($app,2), function() use ($app, $authenticate){
        $format = $app->request->get('format');
        $search = $app->request->get('search');
        $labels = \TPS\label::nameSearch("%$search%");
        $params = array(
            "area" => "Record Labels",
            "title" => "Label Management",
            "search" => $search,
            "labels" => $labels,
        );
        $isXHR = $app->request->isAjax();
        if($isXHR || $format=="json"){
            print json_encode($params);
        }
        else{
            $app->render("labels.twig",$params);
        }
    });
    $app->get('/:id',$authenticate($app,2), 
            function ($id) use ($app,$authenticate){
        $format = $app->request->get('format');
        $label = new \TPS\label($id);
        $params=array(
            "area" => "Record Labels",
            "title" => "Label Management",
            'label'=>$label->fetch(),
            );
        if(!is_null($params['label']['alias'])){
            $alias = new \TPS\label($params['label']['alias']);
            $params['alias'] = $alias->fetch();
        }
        $isXHR = $app->request->isAjax();
        if($isXHR){
            print json_encode($params);
        }
        elseif(!is_null($format) && $format=="json"){
            print json_encode($params);
        }
        else{
            $app->render("labelManager.twig",$params);
        }
    });
    $app->get('/:id/tree',$authenticate($app,2), 
            function ($id) use ($app,$authenticate){
        $format = $app->request->get('format');
        $label = new \TPS\label($id);
        $params = $label->companyTree(True);
        $isXHR = $app->request->isAjax();
        if($isXHR || $format=="json"){
            print json_encode($params);
        }
        else{
            $app->render("notSupported.twig",$params);
        }
    });
});

