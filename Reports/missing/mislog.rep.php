<?php
    if(!$mysqli){
        die("<p>Error: No Database Connection</p>");
    }
    if(isset($_GET['m'])){
        $message=urldecode($_GET['m']);
    }
    $from = filter_input(INPUT_POST, "from", FILTER_SANITIZE_STRING);
    $to = filter_input(INPUT_POST, "to", FILTER_SANITIZE_STRING);
?>
<h3 class="sub-header">Missing Play sheets</h3>
<?php
    if(isset($message)){
        echo "<div class=\"panel panel-success\">
    <div class=\"panel-heading\">Message Information</div>
        <div class=\"panel-body\">
            <span>$message</span>
        </div>
    </div>";
    }
$pr_name = array();
$pr_length = array();
$pr_genre = array();
$pr_ID = array();

   if($stmt = $mysqli->prepare("SELECT Program.programname, Program.length, Program.genre, Program.ProgramID From Program where active='1' and not exists (select Episode.programname from Episode where date between ? and ? and Episode.programname=Program.programname) order by Program.programname"))
   {
       $stmt->bind_param("ss",[$from,$to]);
       $stmt->execute();
       $stmt->bind_result($pr_name,$pr_length,$pr_genre,$pr_ID);
       $stmt->fetch();
       $stmt->close();
       
       for($i=0;$i<sizeof($pr_name);$i++){
           echo "";
       }
   }
   
   $mysqli->close();
   ?>
