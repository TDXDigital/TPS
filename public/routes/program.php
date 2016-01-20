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

// Review(s)
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
            foreach ($progams as $value) {
                $program = new \TPS\program($station, $value);
                $programs = $program->getValues()?:array();
            }
            
            $params=array(
                'station'=>$stn,
                'programs'=>$programs,
                );
            var_dump($params);
            #$app->render('notSupported.twig',$params);
        });
        $app->get('/:station/all',$authenticate($app,2), 
                function ($callsign) use ($app,$authenticate){
            $station = new \TPS\station($callsign);
            $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
            if($stn = $station->getStation($callsign)){
                $stn = $stn[$callsign];
                $stn['callsign']=$callsign;
            }
            $params=array(
                'station'=>$stn,
                'timezones'=>$tzlist,
                'area'=>'Administration',
                'title'=>'Manage Station',
                );
            $app->render('notSupported.twig',$params);
        });
    });
});
