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

$app->get('/playlist', function () use ($app) {
    $app->redirect('./playlist/');
});
$app->group('/playlist', function() use ($app, $authenticate, $playlist){
    $app->get('/', function() use ($app, $playlist){
        $isXHR = $app->request->isAjax();
        $format = (string)$app->request->get("format")?:"html";
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
        if($isXHR || $format=="json"){
            standardResult::ok($app, $result, NULL);
        }
        else{
            #standardResult::ok($app, $result, NULL);
            $library = new \TPS\library();
            foreach ($result as $key => $value) {
                $lib = $library->getAlbumByRefcode($value['RefCode']);
                $result[$key]["library"] = array_pop($lib);
        		$result[$key]['library']["subgenres"] = $library->getSubgenresByRefCode($value['RefCode']);
        		$result[$key]['library']['hometowns'] = $library->getHometownsByRefCode($value['RefCode']);
            }
            $params = array(
		"area"=>"Playlist",
                "title"=>"Search",
                "playlists"=>$result,
                "page"=>$page,
                "pages"=>$count,
                "limit"=>$limit,
		"subgenres"=>$library->getSubgenres(),
		"hometowns"=>$library->getHometowns(),
		"tags"=>$library->getTags(),
		"showExpired"=>$app->request->get("expired")=='true'
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
            $app->redirect("./".$result);
        }
    });
    $app->get('/display-playlist', $authenticate, function () use ($app) {
	$filter = $app->request->get("filter");
	$playlist = new \TPS\playlist;
	echo $playlist->displayTable($filter);
    });
   $app->get('/lastProg/:id', $authenticate, function($id) use ($app, $playlist){
            $prog = $playlist->getLastProgram($id);

            if (array_key_exists(0,$prog))
                echo 'Last Program: ' . $prog[0]["programname"] . PHP_EOL .'Date: ' . $prog[0]["date"];
            else
                echo 'Cannot find last program';

        });
    $app->get('/chart', function () use ($app, $authenticate, $playlist) {
	$startDate = date("Y-m-d", strtotime("-1 week"));
	$endDate = date("Y-m-d");

        $sDate = new \DateTime($startDate);
        $eDate = new \DateTime($endDate);
        $fourWeekStart = clone $eDate;
        $fourWeekStart->modify('-28 days');
        $sDate = $sDate < $fourWeekStart ? $sDate : $fourWeekStart;
	$sDate = $sDate->format('Y-m-d');

	$charts =  $playlist->getTop40($startDate, $endDate);

        $param = array(
		"area"=>"Playlist",
                "title"=>"Chart",
                "startDate"=>$startDate,
		"sDate"=>$sDate,
                "endDate"=>$endDate,
                "charts"=>$charts,
        );
        $app->render('chart.twig', $param);
    });

    $app->post('/chart', function () use ($app, $authenticate, $playlist) {
        $startDate = $app->request->post("startDate");
        $endDate = $app->request->post("endDate");

        $sDate = new \DateTime($startDate);
        $eDate = new \DateTime($endDate);
        $fourWeekStart = clone $eDate;
        $fourWeekStart->modify('-28 days');
        $sDate = $sDate < $fourWeekStart ? $sDate : $fourWeekStart;
	$sDate = $sDate->format('Y-m-d');

        $charts =  $playlist->getTop40($startDate, $endDate);

        $param = array(
	    "area"=>"Playlist",
            "title"=>"Chart",
            "charts"=>$charts,
            "startDate"=>$startDate,
	    "sDate"=>$sDate,
            "endDate"=>$endDate
        );
        $app->render('chart.twig', $param);
    });


    $app->get('/generate', function () use ($app) {
        $app->redirect('./generate/');
    });
    $app->group('/generate', function() use ($app, $authenticate, $playlist){
        $app->get('/', $authenticate($app, [1,2]), 
                function() use ($app, $playlist){
            $isXHR = $app->request->isAjax();
            $format = $app->request->get("format");
            $today = $app->request->get("today")?:date("Y-m-d");
            $getCode = function ($genre, $codes){
                foreach ($codes as $key => $value) {
                    if($value['Genre'] == $genre){
                        return array($key, $value);
                    }
                }
                return FALSE;
            };
            $library = new \TPS\library();
            $pending = $library->pendingPlaylist();
            $libCodes = $library->listLibraryCodes();
            foreach ($pending as &$entry) {
                list($genreNum, $entry['genre']) = $getCode($entry['genre'], $libCodes);
		$entry['genre']['number'] = $genreNum;
		$labels = $library->getLabelsByRefCode($entry['RefCode']);
		$entry['labelNames'] = array_map(function($label) {return $label['Name'];}, $labels);
		$entry['labelIDs'] = array_map(function($label) {return $label['LabelNumber'];}, $labels);
            }

	    // Start sorting $pending by (1) genre number, then (2) Region...
	    function sort_by_genre_num($a, $b) {
		return $a['genre']['number'] > $b['genre']['number'];
	    }

	    function sort_by_region($a, $b) {
		$region_ranking = ["Local" => 1, "Province" => 2, "Country" => 3, "International" => 4];
		return $region_ranking[$a['Locale']] > $region_ranking[$b['Locale']];
	    }

	    usort($pending, 'sort_by_genre_num');
	    $albums_per_genre = [];
	    foreach ($pending as $i=>$album) {
		$index = $album['genre']['number'];
		if (isset($albums_per_genre[$index]))
		    $albums_per_genre[$index]++;
		else
		    $albums_per_genre[$index] = 1;
	    }
	    $albums_per_genre = array_values($albums_per_genre); // Reset array keys
	    foreach ($albums_per_genre as $i=>$count) {
		$j = $i - 1;
		$albums_before = 0;
		while ($j >= 0) {
		    $albums_before += $albums_per_genre[$j];
		    $j--;
		}
		$genre_group = array_slice($pending, $albums_before, $count);
		usort($genre_group, 'sort_by_region');
		array_splice($pending, $albums_before, $count, $genre_group);
	    }
	    // $pending is now sorted!

	    // Assign ShortCodes to albums
            $validRanges = $playlist->getGenreDividedValidShortCodes(
                    $_SESSION['CALLSIGN'], $today);
            foreach ($pending as &$entry) {
		try{
                    foreach ($validRanges[$entry['genre']['Genre']] 
                            as $key => &$value) {
                        if(sizeof($value['shortCodes'])<1){
                            continue;
                        }
                        if(in_array($entry['format'], $value['formats'])){
                            $entry['ShortCode'] = array_shift(
                                $value['shortCodes']);
                            break;
                        }
                    }
                } catch (Exception $ex) {
                    $library->log->error("No Available ShortCodes for "
                            . "library genre ".$entry['genre']['Genre']);
                }
	    }

            if($isXHR || strtolower($format) == "json"){
                standardResult::ok($app, $pending, NULL);
            }
            else{
                $params = array(
                    "title"=>"Generation",
                    "area"=>"Playlist",
                    "today"=>$today,
                    "playlists"=>$pending,
                );
                $app->render("playlistGeneration.twig", $params);
            }
        });
        $app->post('/', $authenticate($app, [1,2]), 
                function() use ($app, $playlist){
            $library = new \TPS\library();
            $isXHR = $app->request->isAjax();
            $format = $app->request->post("format");
            $enabled = $app->request->post("enabled");
            $refCode = $app->request->post("refCode");
            $endDate = $app->request->post("endDate");
            $startDate = $app->request->post("startDate");
            $smallCode = $app->request->post("smallCode");
            foreach ($refCode as $key => $entry) {
                if(!in_array($entry, $enabled)){
                    continue;
                }
                $playlist->create($entry, $startDate[$key], $endDate[$key],
                        NULL, NULL, $smallCode[$key]);
            }
            $library->playlistStatus($enabled, "COMPLETE");
            $app->redirect("../");
        });
    });
    $app->get('/shortcode', function () use ($app) {
        $app->redirect('./shortcode/');
    });
    $app->group('/shortcode', function() use ($app, $authenticate, $playlist){
        $app->get('/', $authenticate($app, [1,2]), 
                function() use ($app, $playlist){
            $isXHR = $app->request->isAjax();
            $format = (string)$app->request->get("format")?:"html";
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
            standardResult::ok($app, $result, NULL);
        });
        $app->get('/gaps', $authenticate($app, [1,2]), 
                function() use ($app, $playlist){
            $isXHR = $app->request->isAjax();
            $format = (string)$app->request->get("format")?:"html";
            $page = (int)$app->request->get("p")?:1;
            $limit = (int)$app->request->get("l")?:25;
            $count = ceil($playlist->countAll()/$limit);
            $refcodes = $app->request->get("refcodes");
            $startDate = $app->request->get("startDate")?:"1000-01-01";
            $endDate = $app->request->get("endDate")?:"9999-12-31";
            $result = $playlist->getRangeGapsForGenres($_SESSION['CALLSIGN']);
            standardResult::ok($app, $result, NULL);
        });
        $app->get('/available', function () use ($app) {
            $app->redirect('./available/');
        });
        $app->group('/available', function() use ($app, $authenticate, $playlist){
            $app->get('/all', $authenticate($app, [1,2]), 
                    function() use ($app, $playlist){
                $startDate = $app->request->get("startDate")?:"1000-01-01";
                $endDate = $app->request->get("endDate")?:"9999-12-31";
                $startNum = $app->request->get('start')?:0;
                $endNum = $app->request->get('end')?:99999;
                $result = $playlist->validShortCodes(
                        $startDate, $endDate, $startNum, $endNum);
                standardResult::ok($app, $result, NULL);
            });
            $app->get('/', $authenticate($app, [1,2]), 
                    function() use ($app, $playlist){
                $today = $app->request->get("date")?: date("Y-m-d");
                $defaultOffset = $app->request->get("offset")?:True;
                $genre = $app->request->get('genre')?:False;
                $format = $app->request->get('format')?:False;
                $result = $playlist->getGenreDividedValidShortCodes(
                        $_SESSION['CALLSIGN'], $today, 
                        $defaultOffset, $genre, $format);
                standardResult::ok($app, $result, NULL);
            });
            $app->get('/:genre', $authenticate($app, [1,2]), 
                    function($genre) use ($app, $playlist){
                $today = $app->request->get("date")?: date("Y-m-d");
                $defaultOffset = $app->request->get("offset")?:True;
                $format = $app->request->get('format')?:False;
                $term = $app->request->get('term')?:NULL;
                $ltrim = $app->request->get('trim')?:FALSE;
                $lpad = $app->request->get('tpad')?:TRUE;
                $limit = $app->request->get('limit')?:FALSE;
                $result = $playlist->getGenreDividedValidShortCodes(
                        $_SESSION['CALLSIGN'], $today, 
                        $defaultOffset, $genre, $format);
                $codes = array();
                foreach ($result as $key => $value) {
                    foreach ($value as $entry){
                        $codes = array_merge($codes, $entry['shortCodes']);
                    }
                }
                if($term && $term != 0){
                    $temp = array();
                    foreach($codes as $code){
                        if($lpad){
                            $codeCompare = sprintf('%04d',$code);
                        }
                        else{
                            $codeCompare = $code;
                        }
                        if($ltrim){
                            $pos = strpos($codeCompare, (string)((int)$term));
                        }
                        else{
                            $pos = strpos($codeCompare, (string)$term);
                        }
                        if($pos === false){
                            continue;
                        }
                        array_push($temp, $codeCompare);
                    }
                    $codes = $temp;
                }
                if($limit){
                    $codes = array_slice($codes, 0, $limit);
                }
                standardResult::ok($app, $codes, NULL);
            });
        });
        $app->get('/valid', $authenticate($app, [1,2]), 
                function() use ($app, $playlist){
            $isXHR = $app->request->isAjax();
            $format = (string)$app->request->get("format")?:"html";
            $page = (int)$app->request->get("p")?:1;
            $limit = (int)$app->request->get("l")?:25;
            $count = ceil($playlist->countAll()/$limit);
            $startDate = $app->request->get("startDate")?:"1000-01-01";
            $endDate = $app->request->get("endDate")?:"9999-12-31";
            if(!($playlist->validateIsoDate($startDate) &&
                    $playlist->validateIsoDate($endDate))){
                        throw new \Exception("Invalid date format provided,"
                                . " dates must be ISO8601 format "
                                . " i.e. (2013-12-30)");
            }
            $code = $app->request->get('code')?:99999;
            $result = $playlist->validateShortCode(
                    $startDate, $endDate, $code);
            if($result){
                standardResult::ok($app, $result, NULL);
            }
            else{
                $conflict = $playlist->getCurrentByShortCode($code, 
                        $startDate, $endDate);
                $conflict = array_pop($conflict);
                $plid = $conflict['PlaylistId'];
                $onDate = new DateTime($conflict['Activate']);
                $offDate = new DateTime($conflict['Expire']);
                print "$code conflicts with "
                        ."<a href='../$plid' target='_blank'>PL#$plid</a> ".
                        sprintf("(`%'.04d`)", 
                            $conflict['RefCode'])." (".$onDate->format("Y-m-d")
                        ." -> ".$offDate->format("Y-m-d").")";
                $app->response->setStatus(406);
                $app->response->headers->set('Content-Type', 'application/json');
                $app->stop();
            }
        });
        $app->post('/', $authenticate($app, [1,2]), 
                function() use ($app, $playlist){
            $library = new \TPS\library();
            $isXHR = $app->request->isAjax();
            $format = $app->request->post("format");
            $enabled = $app->request->post("enabled");
            $refCode = $app->request->post("refCode");
            $endDate = $app->request->post("endDate");
            $startDate = $app->request->post("startDate");
            $smallCode = $app->request->post("smallCode");
            foreach ($refCode as $key => $entry) {
                if(!in_array($entry, $enabled)){
                    continue;
                }
                $playlist->create($entry, $startDate[$key], $endDate[$key],
                        NULL, NULL, $smallCode[$key]);
            }
            $library->playlistStatus($enabled, "COMPLETE");
            $app->redirect("../");
        });
        $app->delete('/', $authenticate($app, [1,2]), 
                function() use ($app, $playlist){
            $library = new \TPS\library();
            $isXHR = $app->request->isAjax();
            $format = $app->request->post("format");
            $enabled = $app->request->post("enabled");
            $refCode = $app->request->post("refCode");
            $endDate = $app->request->post("endDate");
            $startDate = $app->request->post("startDate");
            $smallCode = $app->request->post("smallCode");
            foreach ($refCode as $key => $entry) {
                if(!in_array($entry, $enabled)){
                    continue;
                }
                $playlist->create($entry, $startDate[$key], $endDate[$key],
                        NULL, NULL, $smallCode[$key]);
            }
            $library->playlistStatus($enabled, "COMPLETE");
            $app->redirect("../");
        });
        $app->get('/:id', $authenticate($app, [1,2]), 
                function($id) use ($app, $playlist){
            $library = new \TPS\library();
            $isXHR = $app->request->isAjax();
            $format = $app->request->post("format");
            $enabled = $app->request->post("enabled");
            $refCode = $app->request->post("refCode");
            $endDate = $app->request->post("endDate");
            $startDate = $app->request->post("startDate");
            $smallCode = $app->request->post("smallCode");
            foreach ($refCode as $key => $entry) {
                if(!in_array($entry, $enabled)){
                    continue;
                }
                $playlist->create($entry, $startDate[$key], $endDate[$key],
                        NULL, NULL, $smallCode[$key]);
            }
            $library->playlistStatus($enabled, "COMPLETE");
            $app->redirect("../");
        });
    });
    
    $app->get('/genre', function () use ($app) {
        $app->redirect('./genre/');
    });
    $app->group('/genre', function() use ($app, $authenticate, $playlist){
        $app->get('/range', $authenticate($app, [1,2]), 
                function() use ($app, $playlist){
            $isXHR = $app->request->isAjax();
            $format = $app->request->post("format");
            $id = $app->request->post("id");
            $range = $playlist->getGenreShortCodeRange($id, 
                    $_SESSION['CALLSIGN']);
            standardResult::ok($app, $range, NULL, 200, True);
        });
        $app->get('/', $authenticate($app, [1,2]), 
                function() use ($app, $playlist){
            $isXHR = $app->request->isAjax();
            $format = $app->request->post("format");
            $id = $app->request->post("id");
            $range = $playlist->getGenreShortCodeRanges($_SESSION['CALLSIGN']);
            standardResult::ok($app, $range, NULL, 200, True);
        });
    });
    $app->get('/report', function () use ($app) {
        $app->redirect('./report/');
    });
    $app->group('/report', function() use ($app, $authenticate, $playlist){
        $app->get('/', $authenticate($app, [1,2]), 
                function() use ($app, $playlist){
            $isXHR = $app->request->isAjax();
            $format = $app->request->get("format");
            $startDate = $app->request->get("startDate")?:date("Y-m-d");
            $endDate = $app->request->get("endDate")?:date("Y-m-d");
            $id = $app->request->get("id");
            $library = new \TPS\library($_SESSION['CALLSIGN']);
            $playlistVals = $playlist->getPlaylist($startDate, $endDate);
            $gaps = $playlist->getRangeGapsForGenres(
                    $_SESSION['CALLSIGN'], $startDate);
            foreach ($gaps as $key => &$value) {
                $genres = $library->getLibraryGenres();
                try{
                    $value['genre'] = $genres[$key];
                } catch (Exception $ex) {
                    $value['genre'] = "`$key` is a Invalid Genre";
                }
            }
            if($isXHR || strtolower($format)=="json"){
                standardResult::ok($app, $shortCodes, NULL);
            }
            else{
                $params = array(
                    "title"=>"Report",
                    "area"=>"Playlist",
                    "gaps"=>$gaps,
                    "today"=>$startDate,
                    "playlists"=>$playlistVals,
                );
                $app->render("playlistReport.twig", $params);
            }
        });
        $app->get('/xlsx', $authenticate($app, [1,2]), 
                function() use ($app, $playlist){
            $filename = "playlist_".date("Y-m-d").rand(1, 9999).".xlsx";
            header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            $isXHR = $app->request->isAjax();
            $format = $app->request->post("format");
            $startDate = $app->request->post("startDate")?:date("Y-m-d");
            $endDate = $app->request->post("endDate")?:date("Y-m-d");
            $id = $app->request->post("id");
            $shortCodes = $playlist->getPlaylist($startDate, $endDate);
            $writer = new \XLSXWriter();
            reset($shortCodes);
            $firstKey = key($shortCodes);
            $keys = array_keys($shortCodes[$firstKey]);
            array_unshift($shortCodes, $keys);
	    foreach ($shortCodes as $j=>&$shortCode) {
		$labelsCell = "";
		if (array_keys($shortCode)[0])
		    foreach ($shortCode['labels'] as $i=>$label)
		        $labelsCell .= "-".$label['Name']."\n";
	        $shortCode['labels'] = $labelsCell;
	    }
            $writer->writeSheet($shortCodes);//$data);
            echo $writer->writeToString();
        });
	$app->get('/pdf', $authenticate($app, [1,2]), function() use ($app, $playlist) {
	    $library = new \TPS\library();
	    $startDate = $app->request->get("startDate")?:"1000-01-01";
            $endDate = $app->request->get("endDate")?:"9999-12-31";
	    $playlistAlbums = array_values($playlist->getAll($startDate, $endDate));
	    foreach ($playlistAlbums as &$album) {
                $lib = $library->getAlbumByRefcode($album['RefCode']);
                $album["library"] = array_pop($lib);
        	$album['library']["subgenres"] = $library->getSubgenresByRefCode($album['RefCode']);
        	$album['library']['hometowns'] = $library->getHometownsByRefCode($album['RefCode']);
            }
            $station = new \TPS\station();
            $serverTime = (new \DateTime)->format('Y-m-d H:i:s');
            $localTime = $station->getTimeFromServerTime($serverTime);
            $today = (new \DateTime($localTime))->format('Y-m-d');
	    $mpdf = new \Mpdf\Mpdf();
	    $stylesheet = file_get_contents('css/playlist_pdf.css');

	    $html = '<h1>Playlist</h1>';
	    $html .= "<h3>" . $today . "</h3>";

	    // Create table for albums sorted by playlist number
	    function sortByPLNum($a, $b) {
		return $a['SmallCode'] > $b['SmallCode'];
	    }
	    usort($playlistAlbums, 'sortByPLNum');
	    $html .= $playlist->createPDFTable($playlistAlbums, 'green');

	    // Create table for albums sorted by artist name
	    function sortByArtistName($a, $b) {
		return $a['library']['artist'] > $b['library']['artist'];
	    }
	    usort($playlistAlbums, 'sortByArtistName');
	    $html .= "<pagebreak />" . $playlist->createPDFTable($playlistAlbums, 'blue');

	    $mpdf->WriteHTML($stylesheet, 1);
	    $mpdf->WriteHTML($html, 2);

	    $mpdf->Output("playlist_" . $today . ".pdf", "D");
	});
    });



        $app->put('/batch', $authenticate($app, array(2)), function () use ($app){

        $playlist = new \TPS\playlist();
        $bulkIds = $app->request->put('bulkEditId');
        $action = $app->request->put('action');
        $attribute = $app->request->put('attribute');

        switch($action) {
        case 'convert': 
            foreach($bulkIds as $key => $refCode)   
            {
                $playlist->moveAlbumToLibrary($refCode);
            }
            break;
        case 'missing':
            foreach($bulkIds as $key => $refCode)   
            {
                $playlist->setToMissing($refCode);
            }
            break;
        case 'found':
            foreach($bulkIds as $key => $refCode)   
            {
                $playlist->setToFound($refCode);
            }
            break;
            
        }
        $app->redirect($app->request->getReferrer());
    });
    $app->get('/batch', $authenticate, function () use ($app){
        $app->redirect('./batch/');
    });

    $app->group('/chartPage', $authenticate, function () use ($app) {
        // inventory management
        $app->get('/', function () use ($app) {
            $app->render('chartPage.twig',array(
                'some'=>'thing'));
        });
    });




    $app->group('/labels', $authenticate, function () use ($app) {
        // inventory management
        $app->get('/', function () use ($app) {
            $app->render('notSupported.twig',array(
                'title'=>'Playlist Label Printing'));
        });
    });
    // This order is important, we need to match routes first, index second
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

		// If no expire date assigned
		if (!array_key_exists('Expire', $result[$key])) {
		     // Assign one based on the library code, or default to 6 months.
		    $libraryCode = $library->getLibraryCodeByRefCode($value['RefCode']);
		    preg_match("/[0-9]*/", $libraryCode, $matches);
		    $genreNum = $matches[0];
		    if ($genreNum != "") {
		        $playlistDuration = $library->listLibraryCodes()[$genreNum]['PlaylistDuration'];
		        $expire = date('Y-m-d', strtotime("+" . $playlistDuration['value'] . " " . $playlistDuration['unit']));
		    } else {
    		        $expire = date('Y-m-d', strtotime("+6 months"));
		    }
		    $result[$key]["Expire"] = $expire;
		}
            }
            $params = array(
		"area"=>"Playlist",
                "title"=>"New",
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



