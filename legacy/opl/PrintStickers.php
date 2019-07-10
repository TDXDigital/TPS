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

    .parent {
	overflow: auto;
    }

    .label{
	font-size: 10px;
	text-align: left;
        width: 6.64cm; /* plus .6 inches from padding */
        height: 2.54cm; /* plus .125 inches from padding */
        margin-right: 0; 
        padding-left: .1cm;
        padding-right: .1cm;
        padding-top: .15cm;
        padding-bottom: .2cm;
        float: left;
        overflow: hidden;
        border-style: dotted; /* outline doesn't occupy space like border does */
        border-width: 1px;
        page-break-inside:avoid
    }
    </style>
</head>
<body>
    <div class="no-print">Please use top and bottom margin of 0.5" and 0.0" sides. some printers may vary, adjust as needed</div>
    <?php
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
	    $hometowns = $library->getHometownsByRefCode($RefCode);
	    $subgenres = $library->getSubgenresByRefCode($RefCode);
	    $libraryCode = $library->getLibraryCodeByRefCode($RefCode);
	    $recordLabels = $library->getLabelsByRefCode($RefCode);
	    $recordLabelNames = array_map(function($label) {return $label['Name'];}, $recordLabels);

            #$library->createBarcode($RefCode);

	    // Determine leading genre number
	    preg_match("/[0-9]?/", $libraryCode, $matches);
	    $genreNum = $matches[0];
	    if($genreNum == '') // No genre assigned yet
		$genreNum = 'N';

            $padded= join('', array($genreNum, str_pad($BCD['RefCode'], 11 - strlen($genreNum), "0", STR_PAD_LEFT)));


	    if ($i % 3 == 0) {
		if ($i != 0)
		    echo "</div>";
   	        if ($i % 15 == 0) {
		    echo "<p style='page-break-before: always'></p><div style='height:0.1px'></div>";
	        }
		echo "<div class='parent'>";
	    }
            echo "<div class=\"label\"";
	    if ($i % 18 < 3)
		echo "margin-top:0.5in;";
	    echo "\">";
	    $artistAlbumMax = 14;
            if(strlen($artist)>$artistAlbumMax)
                $artpost = "...";
            else
                $artpost = "";

            if(strlen($album)>$artistAlbumMax)
                $albpost = "...";
            else
                $albpost = "";

            echo "<span>" . $libraryCode . "</span>" .
		 "<br />" .
		 "<strong style='float: left; font-size: small'>" . substr($artist,0,$artistAlbumMax) . $artpost . " -</strong>" .
		 "<i style='float:left; font-size: small;'>&nbsp;" . substr($album,0,$artistAlbumMax) . $albpost."</i>" .
		 "<br />" .
		 "<br />" .
		 "<u>Labels:</u> " . implode("|", $recordLabelNames) .
		 "<br />" .
		 "<u>Towns:</u> " . implode("|", $hometowns) .
		 "<br />" .
		 "<u>Subgenres:</u> " . implode("|", $subgenres) .
		 "</div>";
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
