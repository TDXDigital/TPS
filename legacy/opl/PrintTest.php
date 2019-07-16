<!doctype html>
<?php
    error_reporting(E_ALL);
    include_once 'TPSBIN/functions.php';
    include_once 'TPSBIN/db_connect.php';
    require_once "public/lib/station.php";
    require_once "public/lib/library.php";

$type = filter_input(INPUT_GET,'type', FILTER_SANITIZE_NUMBER_INT)?:5160;
$indent = filter_input(INPUT_GET,'start', FILTER_SANITIZE_NUMBER_INT) ?: 0;
$outline = filter_input(INPUT_GET,'outline',FILTER_SANITIZE_STRING) ?: 'false';
$library = new \TPS\library();
$reviews = new \TPS\reviews();
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Print Labels</title>
    <link href="../legacy/opl/CSS_Labels/<?php
    if($type==="5160"){
        print "5160";
    }
    elseif($type==="5163"){
        print "5163";
    }
    ?>.css" rel="stylesheet" type="text/css" >
    <?php

    if(strtolower($outline)==='true'){
        echo "<style type='text/css'>\xA.label{\xAoutline: 1px dashed;\xA}\xA</style>";
    }
    elseif(strtolower($outline)==='true'){
        echo "<style type='text/css'>\xA.label{\xAoutline: none;\xA}\xA</style>";
    }

    ?>
    <style type="text/css">
    @media print{
        .no-print, .no-print *{
            display: none !important;
        }
        body{
            background-image: none;
            color:#000000
        }
        -webkit-print-color-adjust: exact;
    }

    @page :right{
        margin: 0.0cm;
    }

    @page :left{
        margin: 0.0cm;
    }
    .no-print, .no-print *{
        background-color: orange;
        text-align: center;
    }

    .cd_cover_row {
        display: flex;
        margin-top: 10px;
        font-size: 9px;
    }

    .cd_cover_tile {
        border: 1px solid darkgrey;
        flex: 1;
        margin: 0 5px;
    }

    .cd_cover_tile_header {
        color: white;
        background-color: grey;
	padding: 1px;
	font-weight: bold;
    }

    .cd_cover_tile_body {
        text-align: left;
	margin: 5px;
    }

    </style>
</head>
<body>
    <div class="no-print">Please use top and bottom margin of 0.5" and 0.0" sides. some printers may vary, adjust as needed</div>
    <?php
	function buildTile($title, $info) {
	    $tile = "<div class='cd_cover_tile'>" .
			"<div class='cd_cover_tile_header'>" . $title . "</div>" .
			"<div class='cd_cover_tile_body'>";
	    if (is_array($info))
		$tile .= implode("", array_map(function($i) {if ($i != "") return '-'.$i.'<br />';}, $info));
	    else
		$tile .= $info;
	    $tile .=    "</div>" .
		    "</div>";
	    return $tile;
	}

        for($i=1;$i<$indent;$i++){
            echo "<div class=\"label\" style=\"border: 1px dotted black;\"></div>";
        }
        foreach($_SESSION['PRINTID'] as $i=>$BCD){
            $albums = $library->getAlbumByRefcode($BCD['RefCode'], TRUE);
            if(sizeof($albums)<1){
                continue;
            }
            $albumArr = $albums[0];
            $RefCode = $albumArr['RefCode'];
            $artist = $albumArr['artist'];
            $album = $albumArr['album'];
            $genre = $albumArr['genre'];
            $format = $albumArr['format'];
            $CanCon = $albumArr['CanCon'];
            $locale = $albumArr['Locale'];
	    $hometowns = $library->getHometownsByRefCode($RefCode);
	    $subgenres = $library->getSubgenresByRefCode($RefCode);
	    $libraryCode = $library->getLibraryCodeByRefCode($RefCode);
	    $recordLabels = $library->getLabelsByRefCode($RefCode);
	    $tags = $library->getTagsByRefCode($RefCode);

	    $reviewIds = $reviews->getReviewIdsByRefCode($RefCode);
	    $recommendations = "";
	    $descriptions = [];
	    $notes = [];
	    foreach ($reviewIds as $j=>$reviewId) {
		$fullReview = $reviews->getFullReview($reviewId);
		if ($j != 0)
		    $recommendations .= ", ";
		$recommendations .= $fullReview['review']['recommendations'];
		array_push($descriptions, $fullReview['review']['description']);
		array_push($notes, $fullReview['review']['notes']);
	    }

	    // Convert the string of recommendations into an array
	    $recommendations = explode(",", $recommendations);
	    foreach ($recommendations as &$rec)
		$rec = trim($rec);

	    $recordLabelNames = array_map(function($label) {return $label['Name'];}, $recordLabels);

            #$library->createBarcode($RefCode);

	    // Determine leading genre number
	    preg_match("/[0-9]?/", $libraryCode, $matches);
	    $genreNum = $matches[0];
	    if($genreNum == '') // No genre assigned yet
		$genreNum = 'N';

            $padded= join('', array($genreNum, str_pad($BCD['RefCode'], 11 - strlen($genreNum), "0", STR_PAD_LEFT)));

            //echo "<img src='barcode/createBarcode.php?bcd=$BCD'/>";
            echo "<div class=\"label\" style=\"border: 1px dotted black; width: 418px; height: 436px; float: none; margin-left:0.22in;";
	    if ($i % 2 == 0)
		echo "margin-top:0.5in;";
	    echo "\">";
            echo "<span><img style='float:left; margin:0px;' src='../legacy/opl/barcode/barcode.php?bcd=$padded' alt='$padded'/>";
            if($locale=="Country"){
                echo "<img style='float: right; margin: 0px;' src='../legacy/opl/maple.gif' alt='CC'/>";
            }
            else if ($locale=="Province"){
                echo "<img style='float: right; margin: 0px;' width='25px' src='../legacy/opl/ab_ttm.png' alt='PRO'/>";
            }
            else if ($locale=="Local"){
                echo "<img style='float: right; margin: 0px;' width='25px' src='../legacy/opl/pointer.png' alt='PRO'/>";
            }
            substr("abcdef", -1);
            if(strlen($artist)>20){
                $artpost = "...";
            }
            else{
                $artpost = "";
            }
            if(strlen($album)>20){
                $albpost = "...";
            }
            else{
                $albpost = "";
            }
            echo "</span>" . 
		 "<br style='clear: both'>" .
		 "<strong style='float: left'>" . substr($artist,0,20) . $artpost . "</strong>" .
		 "<br>" . 
		 "<i style='float:left; font-size: small;'>" . substr($album,0,20) . $albpost."</i>" .
		 "<span style='float:right;'>" . $genre . "</span>" . 
		 "<br style='clear: both'/>" .
		 "<div class='cd_cover_row'>" .
			buildTile('Library Code', $libraryCode) .
			buildTile('Record Labels', $recordLabelNames) .
			buildTile('Hometowns', $hometowns) .
		 "</div>" .
		 "<div class='cd_cover_row'>" .
			buildTile('Subgenres', $subgenres) .
			buildTile('RIYL', $recommendations) .
			buildTile('Tags', $tags) .
		 "</div>" .
		 "<div class='cd_cover_row'>" .
			buildTile('Descriptions', $descriptions) .
		 "</div>" .
		 "<div class='cd_cover_row'>" .
			buildTile('Notes', $notes) .
		 "</div>" .
		 "</div>";
	    if ($i % 2 == 1) {
		echo "<p style='page-break-before: always'></p><div style='height:0.1px'></div>";
	    }
        }
    ?>
<div class="page-break"></div>
<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        setTimeout(window.print(),2000);
    });
</script>
</body>
</html>
