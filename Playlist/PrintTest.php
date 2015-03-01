<!doctype html>
<?php
    include_once '../TPSBIN/functions.php';
    include_once '../TPSBIN/db_connect.php';
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HTML & CSS Avery Labels (5160) by MM at Boulder Information Services</title>
    <link href="CSS_Labels/<?php
    if(TRUE){
        print "5160";
    }
    elseif($_GET['t']==="5163"){
        print "5163";
    }
    ?>.css" rel="stylesheet" type="text/css" >
    <style type="text/css">
    @media print{
      body{ background-color:none; background-image:none; color:#000000 }
    }
    </style>
</head>
<body>
    <?php
    if($stmt=$mysqli->prepare("SELECT artist, album, format, genre, CanCon, Locale FROM library WHERE RefCode = ?")){
        foreach($_SESSION['PRINTID'] as $BCD){
            $stmt->bind_param("i",$BCD);
            $stmt->execute();
            $stmt->bind_result($artist,$album,$format,$genre,$CanCon,$locale);
            $stmt->fetch();
            $padded=str_pad((int)$BCD, 10, "0", STR_PAD_LEFT);
            //echo "<img src='barcode/createBarcode.php?bcd=$BCD'/>";
            echo "<div class=\"label\"><span class=\"barcode\">$padded</span>";
            if($CanCon==1){
                echo "<img src='./maple.gif' alt='CC'/>";
            }
            echo "<br><strong>$artist</strong><br><i>$album</i><span style='text-align:right'>$genre</span></div>";
        }
    }
    else{
        echo "<div class=\label>ERROR :".$mysqli->error."</div>";
    }
    ?>
<div class="page-break"></div>
<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        window.print();
    });
</script>
</body>
</html>