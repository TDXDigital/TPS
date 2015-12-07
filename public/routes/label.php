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
        $search = $app->request->get('search')?:null;
        $labels = \TPS\label::nameSearch("%$search%",$search?True:False);
        $params = array(
            "area" => "Record Labels",
            "title" => "Management",
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
    $app->group('/new', $authenticate, function() use ($app,$authenticate){
        $app->get('/', $authenticate, function() use ($app){
            $labels = \TPS\label::nameSearch("%",False);
            $params = array(
                "recordLabels" => $labels
            );
            $app->render("labelManager.twig",$params);
        });
        $app->post('/', $authenticate, function() use ($app){
            $format = $app->request->get('format');
            $name = $app->request->put('name')?:null;
            $location = $app->request->put('location')?:null;
            $size = $app->request->put('size')?:null;
            $alias = $app->request->put('alias')?:null;
            $parent = $app->request->put('parent')?:null;
            $verified = $app->request->put('verified')?:null;
            $labels = \TPS\label::nameSearch($name);
            if(sizeof($labels)>1){
                if($labels[0]["alias"]){
                    $app->request->setStatus(400);
                    print "400, Bad Request<br>Label Already Exists";
                }
                else{
                    $id = key($labels[0]);
                }
            }
            else{
                $id = \TPS\label::createLabel($name, 1);
                $labelRewrite = array(
                    "/(.+)(?=(?i)\srecord.{0,5})/",
                );
                foreach ($labelRewrite as $regex) {
                    $value = false;
                    $value = preg_match($regex, $name, $matches);
                    if($value==1){
                        $id2 = \TPS\label::createLabel($matches[0],1);
                        $subLabel = new \TPS\label($id2);
                        $subLabel->setAlias($id);
                    }
                }

            }
            $label = new \TPS\label($id);
            $label->setName($name);
            $label->setLocation($location);
            $label->setSize($size);
            $label->setAlias($alias);
            $label->setParentCompany($parent);
            $label->setVerified($verified);
            $app->redirect("../$id");
        });
    });
    $app->get('/:id',$authenticate($app,2), 
            function ($id) use ($app,$authenticate){
        $format = $app->request->get('format');
        $label = new \TPS\label($id);
        $labels = \TPS\label::nameSearch("%",False);
        $params=array(
            "area" => "Record Labels",
            "title" => "Label Management",
            'label'=>$label->fetch(),
            "recordLabels"=>$labels,
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
    $app->put('/:id', $authenticate($app,2), function($id) use ($app){
        $format = $app->request->get('format');
        $name = $app->request->put('name')?:null;
        $location = $app->request->put('location')?:null;
        $size = $app->request->put('size')?:null;
        $alias = $app->request->put('alias')?:null;
        $parent = $app->request->put('parent')?:null;
        $verified = $app->request->put('verified')?:null;
        $label = new \TPS\label($id);
        $label->setName($name);
        $label->setLocation($location);
        $label->setSize($size);
        $label->setAlias($alias);
        $label->setParentCompany($parent);
        $label->setVerified($verified);
        $isXHR = $app->request->isAjax();
        if($isXHR || $format=="json"){
            $app->response->setStatus(200);
            print "200";
        }
        else{
            $app->flash("success","$name ($id) updated");
            $label->log->info("$name ($id) updated");
            $app->redirect($id);
        }
    });
    $app->delete('/:id', $authenticate($app,2), function($id) use ($app){
        $app->response->setStatus(200);
        print "203";
    });
    $app->post('/:id', $authenticate($app,2), function($id) use ($app){
        $app->response->setStatus(405);
        print "405 Method Not Allowed";
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

