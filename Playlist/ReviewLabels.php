<!doctype html>
<?php
    error_reporting(E_ERROR);
    include_once '../TPSBIN/functions.php';
    include_once '../TPSBIN/db_connect.php';
    include_once '../public/lib/libs.php';
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Print Labels</title>
    <link href="CSS_Labels/<?php
    $library = new \TPS\library();
    $reviews = new \TPS\reviews();
    $type = filter_input(INPUT_GET,'type', FILTER_SANITIZE_NUMBER_INT)?:"5160";
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
</head>
<body>
    <div class="no-print">Please use top and bottom margin of 0.5" and 0.0" sides. some printers may vary, adjust as needed</div>
    <?php
    $review = new \TPS\reviews();
    $reviews = $review->getPrintLables();
    for($i=1;$i<$indent;$i++){
        echo "<div class=\"label\" style=\"outline: none;\"></div>";
    }
    foreach ($reviews as $id ) {
        $label = $review->getFullReview($id);
        echo "<div class=\"label\" style='font-size:xx-small;'>";
        $trimArtist = $label['review']['hometown'];
        if(strlen($label['review']['hometown'])>25){
            
            $trimArtist = substr($trimArtist,0, 23);
            $trimArtist = join('', array($trimArtist,'...'));
        }
        $trimAlbum = str_pad($label['RefCode'],11,0,STR_PAD_LEFT);
        $trimLabel = $label['label']['Name'];
        if(strlen($$trimLabel)>17){
            
            $trimLabel = substr($trimLabel,0, 14);
            $trimLabel = join('', array($trimLabel,'...'));
        }
        $title = strtolower($trimArtist.' ('.$trimAlbum.')');
        echo "<small style='float: left'>$title</small><small style=\"float: right\">[".$trimLabel."]</small>";
        echo "<small><br style='clear: both'><span style='float: left; text-align:justify;'>".substr($label['review']['description'],0,220)."</span><br><i style='float:right'>".substr($label['review']['notes'],0,100)."</i><br style='clear: both'><span style='float:right;'>";
        if( sizeof($label['review']['recommendations'])>0){
            echo "RIYL: ";
        }
        echo $label['review']['recommendations']."</span></small><br style='clear: both'/>";
        echo "</div>";
    }
    
    /*if($stmt=$mysqli->prepare("SELECT artist, album, format, genre, CanCon, Locale FROM library WHERE RefCode = ?")){
        for($i=1;$i<$indent;$i++){
            echo "<div class=\"label\" style=\"outline: none;\"></div>";
        }
        foreach($_SESSION['PRINTID'] as $BCD){
            $stmt->bind_param("i",$BCD);
            $stmt->execute();
            $stmt->bind_result($artist,$album,$format,$genre,$CanCon,$locale);
            $stmt->fetch();
            $prefix = 0;
            $padded= join('', array($prefix,str_pad($BCD, 10, "0", STR_PAD_LEFT)));
            
            //echo "<img src='barcode/createBarcode.php?bcd=$BCD'/>";
            echo "<div class=\"label\">";
            echo "</span><br style='clear: both'><strong style='float: left'>".substr($artist,0,20).$artpost."</strong><br><i style='float:left'>".substr($album,0,20).$albpost."</i><span style='float:right;'>$genre</span><br style='clear: both'/></div>";
        }
    }
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
