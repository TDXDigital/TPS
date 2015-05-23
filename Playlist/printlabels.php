<?php
//require('../TPSBIN/fpdf/html2multi.php');
require('../TPSBIN/fpdf/HTML_PDF_Label.php');

/*------------------------------------------------
To create the object, 2 possibilities:
either pass a custom format via an array
or use a built-in AVERY name
------------------------------------------------*/

// Example of custom format
// $pdf = new PDF_Label(array('paper-size'=>'A4', 'metric'=>'mm', 'marginLeft'=>1, 'marginTop'=>1, 'NX'=>2, 'NY'=>7, 'SpaceX'=>0, 'SpaceY'=>0, 'width'=>99, 'height'=>38, 'font-size'=>14));

// Standard format
//$pdf2 = new PDF_HTML();
$pdf = new PDF_Label('5163');

$pdf->AddPage();

// Print labels
for($i=1;$i<=20;$i++) {
    $text = "<strong>Artist</strong><br><i>Album</i><span>CanCon</span>";
    $pdf->WriteHTML($text,$textout);
    $pdf->Add_Label($textout);
    $textout="";
}

$pdf->Output();
?>