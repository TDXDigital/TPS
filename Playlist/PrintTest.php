<!doctype html>
<?php
    error_reporting(E_ALL);
    include_once 'TPSBIN/functions.php';
    include_once 'TPSBIN/db_connect.php';
    require_once "public/lib/station.php";
    require_once "public/lib/library.php";
    //include_once 'barcode/barcode.php';
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Print Labels</title>
    <link href="../Playlist/CSS_Labels/<?php
    $type = filter_input(INPUT_GET,'type', FILTER_SANITIZE_NUMBER_INT)?:5160;
    $indent = filter_input(INPUT_GET,'start', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    $outline = filter_input(INPUT_GET,'outline',FILTER_SANITIZE_STRING) ?: 'false';
    
    
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
            background-color:none;
            background-image:none;
            color:#000000
        }
    }
    
    @page :right{
        margin: 0.0cm;
    }
    
    @page :left{
        margin: 0.0cm;
    }
    @page :top{
        margin: 0.5cm;
    }
    @page :bottom{
        margin: 0.5cm;
    }
    .no-print, .no-print *{
        background-color: orange;
        text-align: center;
    }
    </style>
</head>
<body>
    <div class="no-print">Please use top and bottom margin of 0.5" and 0.0" sides. some printers may vary, adjust as needed</div>
    <?php
    $library = new \TPS\library();
        for($i=1;$i<$indent;$i++){
            echo "<div class=\"label\" style=\"outline: none;\"></div>";
        }
        foreach($_SESSION['PRINTID'] as $BCD){
            $albums = $library->getAlbumByRefcode($BCD['RefCode'], TRUE);
            if(sizeof($albums)<1){
                break;
            }
            $albumArr = $albums[0];
            $RefCode = $albumArr['RefCode'];
            $artist = $albumArr['artist'];
            $album = $albumArr['album'];
            $genre = $albumArr['genre'];
            $format = $albumArr['format'];
            $CanCon = $albumArr['CanCon'];
            $locale = $albumArr['Locale'];
            
            $genreCode = $library->getLibraryCodeByRefCode($RefCode);
            #$library->createBarcode($RefCode);
            $padded= join('', array($genreCode,str_pad($BCD['RefCode'], 10, "0", STR_PAD_LEFT)));
            
            //echo "<img src='barcode/createBarcode.php?bcd=$BCD'/>";
            echo "<div class=\"label\"><span ><img style='float:left; margin:0px;' src='../Playlist/barcode/barcode.php?bcd=$padded' alt='$padded'/>";
            if($locale=="Country"){
                echo "<img style='float: left; margin: 0px;' src='../Playlist/maple.gif' alt='CC'/>";
            }
            else if ($locale=="Province"){
                echo "<img style='float: right; margin: 0px;' width='25px' src='../Playlist/ab_ttm.png' alt='PRO'/>";
            }
            else if ($locale=="Local"){
                echo "<img style='float: right; margin: 0px;' width='25px' src='../Playlist/pointer.png' alt='PRO'/>";
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
            echo "</span><br style='clear: both'><strong style='float: left'>".substr($artist,0,20).$artpost."</strong><br><i style='float:left; font-size: small;'>".substr($album,0,20).$albpost."</i><span style='float:right;'>$genre</span><br style='clear: both'/></div>";
        }
    /*}
    else{
        echo "<div class=\label>ERROR :".$mysqli->error."</div>";
    }*/
    ?>
<div class="page-break"></div>
<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        setTimeout(window.print(),2000);
        //window.print();
    });
</script>
</body>
</html>
