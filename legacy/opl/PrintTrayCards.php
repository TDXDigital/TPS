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
    .traycard {
	position: relative;
	border: 1px dotted black;
	width: 565px;
	height: 442px;
	float: none;
	margin-left:0.22in;
    }

    .left-spine {
        transform: rotate(270deg);
        transform-origin: right top;
        right: 100%;
        width: 442px;
    }

    .right-spine {
        transform: rotate(90deg);
        transform-origin: left top;
        left: 100%;
    }

    .spine {
        position: absolute;
        top: 0;
	height: 25px;
        width: 442px;
	text-align:center;
	outline: 1px dashed black;
    }

    .spine div {
	display: inline-block;
	text-align: center;
	width: 48%;
	margin-top: 3px;
	font-size: 13px;
    }

    .traycard-middle {
	width: 515px;
	height: 100%;
	margin-left: 25px;
    }

    .tracklist {
	padding: 30px 0 0 50px;
    }

    </style>
</head>
<body>
    <div class="no-print">Please use top and bottom margin of 0.5" and 0.0" sides. some printers may vary, adjust as needed</div>
    <?php
	$library = new \TPS\library();
        foreach($_SESSION['PRINTID'] as $i=>$BCD){
            $albums = $library->getAlbumByRefcode($BCD['RefCode'], TRUE);
            if(sizeof($albums)<1)
                continue;
            $albumArr = $albums[0];
            $RefCode = $albumArr['RefCode'];
            $artist = $albumArr['artist'];
            $album = $albumArr['album'];
	    $tracklist = $library->getTracklistByRefCode($RefCode);

	    if (strlen($artist) >= 30)
		$artist = substr($artist, 0, 28) . "...";
	    if (strlen($album) >= 30)
		$album = substr($album, 0, 28) . "...";

            echo "<div class=\"traycard\" style=\"border: 1px dotted black; width: 565px; height: 442px; float: none; margin-left:0.22in;";
	    if ($i % 2 == 0)
		echo "margin-top:0.5in;";
	    echo "\">";
	    // Left spine
	    echo "<span class='spine left-spine'><div>$artist</div><span> - </span><div>$album</div></span>";

	    // Middle section
	    echo "<div class='traycard-middle'>"
			. "<div style='margin-top: 20px; text-align:center;font-size:32px;'>$artist</div>"
			. "<div style='text-align:center;font-size:20px;'>$album</div>"
			. "<div class='tracklist'>";
	    foreach ($tracklist as $j => $track)
		echo           "<div>" . ($j + 1) . " - " . $track . "</div>";
	    echo          "</div>"
		. "</div>";

	    // Right spine
	    echo "<span class='spine right-spine'><div>$artist</div><span> - </span><div>$album</div></span>";

	    echo "</div>";

	    if ($i % 2 == 1)
		echo "<p style='page-break-before: always'></p><div style='height:0.1px'></div>";
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
