<?php
namespace TPS;
session_start(); 
include('oep/EPV3/PHP/php-barcode.php');
//require_once("../../TPSBIN/db_connect.php");
require_once("public/lib/libs.php");

class Report extends TPS{
    public function __construct() {
        parent::__construct(TRUE);
    }
    
    public function runReport(){
        $callsign = filter_input(INPUT_POST, "callsign")?:Null;

        $station = new \TPS\station($callsign);

        $ShowStats = FALSE;
        if(isset($_POST['sls'])){
                $ShowStats = TRUE;
        }

        if(isset($_POST['codef'])){
                $codef = addslashes($_POST['codef']);
        }
        else{
                $codef = "code39";//"codabar";
        }

        if($_POST['timef']=="12"){
                $time12 = TRUE;
        }
        else{
                $time12 = FALSE;
        }
        if($_POST['tms']=="1"){
                $Stime = TRUE;
        }
        else{
                $Stime = FALSE;
        }
        if(isset($_POST['ple'])){
                $ple = TRUE;
        }
        else{
                $ple = FALSE;
        }

        if($_POST['sort']=="En"){
                $sort = "EpNum";
        }
        else if($_POST['sort']=="St"){
                $sort = "starttime";
        }
        else{
                $sort = "programname";
        }

        $sortDir = "asc";

        if(isset($_POST['bcd'])){
                $barcode = TRUE;
        }
        else{
                $barcode = FALSE;
        }


                function to12hour($hour1){ 
                        // 24-hour time to 12-hour time 
                        return DATE("g:i A", STRTOTIME($hour1));
                }
                function to24hour($hour2){
                        // 12-hour time to 24-hour time 
                        return DATE("H:i", STRTOTIME($hour2));
                }
        
        $episodes = array();
        $stmt = $this->mysqli->prepare("SELECT EpNum,programname FROM episode "
                . "WHERE date between ? and ? AND programname LIKE ? "
                . "order by `date` asc");
        if(false===$stmt){
            $station->log->error("Could not query server:".
                    $this->mysqli->error);
        }
        $stations = $station->getStations();
        if(is_null($callsign) || !in_array($callsign,$stations)){
            // invalid or missing callsign
            if(!is_null($callsign)){
                $warn = "Error, invalid callsign `$callsign`"
                        . " provided, using default";
                //$app->flashNow('error',$warn);
                $station->log->warn($warn);
            }
            $callsign = key($stations);
        }
        $callsign = $station->setStation($callsign);
        $programParam = sprintf("%%%s%%",  filter_input(INPUT_POST, "program")?:"");
        $dateFrom = filter_input(INPUT_POST, "from")?:"1000-01-01";
        $dateTo = filter_input(INPUT_POST, "to")?:"9999-12-31";
        $stmt->bind_param("sss",$dateFrom,$dateTo,$programParam);
        $episodeID = Null;
        $programName = Null;
        $stmt->bind_result($episodeID,$programName);
        $stmt->execute();
        while($stmt->fetch()){
            $episodes[$episodeID]=$programName;
        }

        foreach ($episodes as $episodeNumber => $programName) {
            $programID = \TPS\program::getId($callsign, $programName);
            $program = new \TPS\program($station, $programID);
            $episode = new \TPS\episode($program, $episodeNumber);
            $djs = [];

            $djStmt = $this->mysqli->prepare("SELECT dj.djname "
                    . "FROM performs, dj, episode "
                    . "WHERE episode.callsign = ? AND performs.programname = ? "
                    . "AND performs.Alias = dj.Alias AND "
                    . "(performs.STdate is null or performs.STdate < ?) and "
                    . "(performs.STdate is null or performs.ENdate > ?) "
                    . "group by dj.djname asc");
            $programData = $program->getValues();
            $episodeData = $episode->getEpisode();
            $stationData = $station->getStation(
                    $programData['callsign'])[$programData['callsign']];
            $djStmt->bind_param("ssss",
                    $programData['callsign'],$programData['name'],
                    $episodeData['date'],$episodeData['date']);
            $djStmt->bind_result($djName);
            $djStmt->execute();
            while($djStmt->fetch()){
                array_push($djs,$djName);
            }
            // Print Format
            print "<table width=\"100%\" border=\"0\" style='font-size: inherit;'>";
            print "<tr><td colspan=\"2\" ><img src=\"../../images/Ckxu_logo_PNG.png"
            . "\" width=\"150px\"></td><td colspan=\"3\">";
            if($barcode){
                print "<img src='Playlist/barcode/barcode.php?bcd="
                . join('', array(str_pad($episodeNumber, 11,
                        "0", STR_PAD_LEFT)))."'/>";
            }
            else{
                print "Episode Number: ".$episodeID;
            }
            print "</td></tr><tr><td width=\"27%\" >"
            . $stationData['frequency'] . " " . $stationData['website'] 
            . "</td><td  colspan=\"2\" width=\"37%\" >"
            . $stationData['address'] . "</td><td  width=\"15%\" >";
            print "Booth Request Ph: <br />" . $stationData['phone']['main']
            . "</td><td  width=\"20%\" >Program & Music Director Ph: <br />"
            . $stationData['phone']['manager'] . "</td></tr>";
            print "<tr><td>Show Name: " . $episodeData['name'];
            print "</td><td>Air Date: " . $episodeData['date'];
            print "</td><td>Start Time:";
            if($time12){
                echo to12hour($episodeData['time']);
            }
            else{
                echo $episodeData['time'];
            }
            echo "</td><td>";
            echo "End Time: ";
            if($time12){
               echo to12hour($episodeData['endTime']);
                        }
            else{
                echo $episodeData['endTime']?:"<i>".
                        date("HH:MM:II", 
                        strtotime($episodeData['time']." + "
                            .$programData['length']." minutes"))
                        ."</i>";
            }
            echo "</td><td>";
            echo "Total Spoken Time: " . $episodeData['totalSpokenTime'];
            echo "</td></tr>";
            echo "<tr><td colspan=\"2\">";
            if(!is_null($episodeData['recordDate']))
                 {
                   if($episodeData['recordDate'] != ""){
                       $PR = "Pre-Record Date:" . 
                               $episodeData['recordDate'];
                   }
                   else{
                     $PR = "Not Pre-Recorded";
                   }
                 }
                 else{
                   $PR = "Not Pre-Recorded";
                }
            echo $PR;
            echo "</td><td>";
                 // needs program info
            if(!is_null($programData['syndicateSource']))
            {
              if($programData['syndicateSource'] != ""){
                echo "Syndication Origin:" . 
                        $programData['syndicatesource'];
              }
              else{
                echo "Not Syndicated";
              }
            }
            else{
              echo "Not Syndicated ";
            }
            echo "</td><td colspan=\"2\">Programmer(s): ";
            print implode(", ",$djs);
            echo "</td></tr></table><table width=\"100%\" border=\"1\" style='font-size: inherit; border-style:dotted solid; border-width: 1px;'>";
            echo "<tr><th width=\"5%\" >Category</th>";
            if($ple){
                  echo "<th width=\"5%\">Playlist</th>";
            }
            if($Stime){
                  echo "<th width=\"5%\">Time</th>";
            }
            echo "<th width=\"20%\">Artist</th><th width=\"20%\">Title</th><th width=\"20%\">Release Title</th><th>Composer</th><th width=\"2%\">CC</th><th width=\"2%\">Hit</th><th width=\"2%\">Ins</th><th width=\"4%\">Language</th></tr>";
            
            // NO SONG OBJECT YET
            $stmt = $episode->mysqli->prepare(
                "SELECT `song`.`songid`, `song`.`time`, `song`.`album`, "
                . "`song`.`artist`, `song`.`title`, `song`.`cancon`, "
                . "playlistnumber, category, instrumental, hit, Spoken, "
                . "composer, note, AdViolationFlag, `song`.`Timestamp`, "
                . "`language`.`languageid` FROM song "
                . "left join `language` on `language`.`songid`=song.songid "
                . "WHERE FIND_IN_SET(category,?)>0 AND song.callsign=? "
                . "and song.programname=? and song.`date`=? and song.starttime=?"
                . " order by `time`,`songid` ");
            if($stmt === false){
                $episode->log->error($episode->mysqli->error);
            }
            $reportType = filter_input(INPUT_POST, 'type')?:"MUO";
            $categories = "";
            
            // MUsicOnly, SPOken, COMmercial, ADMinistrative
            switch (strtoupper($reportType)) {
                case "MUO":
                    $catNumRange = range(20,40);
                    $categories = implode(",", $catNumRange);
                    break;
                case "SPO":
                    $catNumRange = range(0,19);
                    $categories = implode(",", $catNumRange);
                    break;
                case "COM":
                    $catNumRange = range(50,60);
                    $categories = implode(",", $catNumRange);
                    break;
                case "ADM":
                    $catNumRange = range(0,100);
                    $categories = implode(",", $catNumRange);
                    break;
                default:
                    $catNumRange = range(0,100);
                    $categories = implode(",", $catNumRange);
                    break;
            }
            $stmt->bind_param("sssss",$categories,$episodeData['callsign'],
                    $programData['name'],$episodeData['date'],
                    $episodeData['time']);
            $stmt->bind_result($songId,$songTime,$album,$artist,$songTitle,
                    $canCon, $playlist, $category, $instrumental, $hit, $spoken,
                    $composer, $note, $adViolationFlag, $timestamp, $language);
            $stmt->execute();
            
            while($stmt->fetch()){
                if($stmt->num_rows<1 && $stmt->field_count < 1){
                    print "<tr><td colspan=\"100%\" "
                    . "style=\"background-color:yellow;\">"
                    . "no data returned"
                    . "</td></tr>";
                }
                else{
                    if($category == '51'){
                        echo "<tr><td style=\"background-color:#ffff99;\">";
                    }
                    print "<tr><td>$category</td>";
                    if($ple){
                        echo "<td style='text-align: center;'>";
                        echo $playlist?:"&nbsp;";
                    }
                    if($Stime){
                        echo "</td><td style='text-align: center;'>";
                        echo $songTime;
                    }
                    echo "</td><td>";
                    echo $artist?:'&nbsp';
                    echo "</td><td>";
                    echo $songTitle;
                    echo "</td><td>";
                    echo $album?:'&nbsp';
                    echo "</td><td>";
                    echo $composer?:'&nbsp';
                    echo "</td><td style='text-align: center;'>";
                    echo $canCon?"X":"&nbsp";
                    echo "</td><td style='text-align: center;'>";
                    echo $hit?"X":"&nbsp";
                    echo "</td><td style='text-align: center;'>";
                    echo $instrumental?"X":"&nbsp";
                    echo "</td><td>";
                    echo $language?:"N/A";
                    echo "</td></tr>";
                }
                
            }
            echo "</table></br>Times reported in the '".$stationData['timezone'].
                    "' timezone";
            echo '<p style="page-break-before: always;"> </p>';     
        }
        echo "<table width=\"100%\" style=\"background-color:black; color:white\"><tr><td width=\"10%\" rowspan=\"2\"></td><td><h3>End Report</h3><br /></td></tr>";
        echo "<tr><td>   LEGEND</td><td> CC= Canadian Content, Ins = Instrumental, CAT = Category</td></tr></table>";
    }
}

?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="phpstyle.css" />
        <script type='text/javascript' src='https://www.google.com/jsapi'></script>
        <title>Audit Report</title>
    </head>
<body style="background-color:white;">
<?php
$reportHandler = new \TPS\Report();
$reportHandler->runReport();
?>
</body>
</html>
