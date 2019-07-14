<!doctype html>

<?php
error_reporting(E_ERROR);
include_once '../../TPSBIN/functions.php';
include_once '../../TPSBIN/db_connect.php';
include_once '../../public/lib/libs.php';
$library = new \TPS\library();
$reviews = new \TPS\reviews();
$type = filter_input(INPUT_GET,'type', FILTER_SANITIZE_NUMBER_INT)?:"5160";
$indent = filter_input(INPUT_GET,'start', FILTER_SANITIZE_NUMBER_INT) ?: 0;
$outline = filter_input(INPUT_GET,'outline',FILTER_SANITIZE_STRING) ?: 'false';


?>


<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Print Labels</title>
    <style>
        body {
            width: 8.5in;

        }
        .label{
            width: 6.64cm; /* plus .6 inches from padding */
            height: 2.54cm; /* plus .125 inches from padding */
            margin-right: .3cm; 
            padding-left: .1cm;
            padding-right: .1cm;
            padding-top: .15cm;
            padding-bottom: .2cm;
            float: left;

            /*text-align: center;*/
            overflow: hidden;

            border-style: dotted; /* outline doesn't occupy space like border does */
            border-width: 1px;
            page-break-inside:avoid
        }
        .page-break  {
            clear: left;
            display:block;
            page-break-after:always;
        }
        @page{
            margin-left: 0.76cm;
            margin-right: 0cm;
            margin-bottom: .25cm;
            margin-top: 1.27cm;
        }


    </style>

</head>
<body>
    <?php
    $review = new \TPS\reviews();
    $genrelist = $library ->getLibraryGenres();
    $reviews = $review->getPrintLables();

    foreach ($reviews as $id ) {

    //get all album info and trim them
      $label = $review->getFullReview($id);

      foreach ($label['labels'] as $rec_label) {
        $trimLabel = $rec_label['Name'];
        if(strlen($$trimLabel)>17){
            $trimLabel = substr($trimLabel,0, 14);
            $trimLabel = join('', array($trimLabel,'...'));
        }
        // echo "<small style=\"float: right\">[".$trimLabel."]</small>";
    }

    $hometown = implode(", ", $label["hometown"]);
    if(strlen($hometown)>30){
        $hometown = substr($hometown,0, 30);
        $hometown = join('', array($hometown,'...'));
    }

    $trimDesc = $label['review']['description'];
    if(strlen($trimDesc)>120){
        $trimDesc = substr($trimDesc,0, 120);
        $trimDesc = join('', array($trimDesc,'...'));
    }

    $trimNote = $label['review']['notes'];
    if(strlen($trimNote)>120){
        $trimNote = substr($trimNote,0, 120);
        $trimNote = join('', array($trimNote,'...'));
    }

    $genre = $genrelist[$label['genre']];
    $subgenres = implode(", ", $label["subgneres"]);

    //print out labels
    echo "<div style=\"font-size: 10px\"; class=\"label\"'>";
    echo "<b><u>". $label['artist']. "</u></b> - " . $label['album'] . '<br>'; // Artist - Album
    echo "(" . $label['year'] . ") " . $trimLabel . '<br>';             // (year) RecordLabel
    echo "<b>" . $genre . "</b>: ". $subgenres . '<br>';         //  Genre: subgenres
    echo "<u>" . $hometown . "</u>, ". $trimDesc . '; '
                . $trimNote . '<br>';                    // Hometown, desc note 
                echo "RIYL: " . $label['review']['recommendations'];


                echo "</div>";
            }
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