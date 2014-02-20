<div style="width: inherit;">
    <script>
        $(function () {
            // Handler for .ready() called.
            UpdateCounts();
        });
    </script>
    <table style="text-align: left; width: inherit;">
	<tr><th>Type</th><th>Program</th><th>Sponsor</th><th>Start Time</th><th>Genre</th><th>Date</th><th>Record Date</th>
		<th id="Date" style="text-align:center;">Current Time</th>
		<th style="text-align: center;">CC</th><th style="text-align: center;">PL</th><th style="text-align: center;">ADs</th>
		<th style="text-align: center;">PSA</th><th style="text-align: center;">HITS</th></tr>
<?php
    include_once "../../../../TPSBIN/functions.php";
	sec_session_start();
	$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
	$friends = array();
	if (!$con){
		die("<h2>Error " . mysql_errno() . "</h2><p>Could not establish connection to database. Authentication failed</p>");
	}
	else{
		if(!mysql_select_db("CKXU")){
			die("<h2>Error " . mysql_errno() . "</h2><p>User Access Error. Database refused connection</p>");
		}
	}
    $pgm = addslashes($_SESSION["program"]);
	$date = addslashes($_SESSION['date']);
	$time = addslashes($_SESSION['time']);
	
	$Q_Program = "SELECT * FROM program,genre,episode WHERE program.genre=genre.genreid AND program.programname='".$pgm."' AND program.programname=episode.programname AND episode.date='".$date."' AND episode.starttime='".$time."' ";
	$showarr = mysql_query($Q_Program);
	$show = mysql_fetch_array($showarr);
	if(mysql_error()){
		die("<h2>Error " . mysql_errno() . "</h2><p>SQL Error. Could not fetch program data</p>");
	}
	if($show['Type']=='0'){
		$type = "Live";
	}
	else if($show['Type']){
		$type = "Pre Precord";
	}
	else{
		$type = "Timeless";
	}
	//echo mysql_num_rows($showarr);
	echo "<tr><td>".$type."</td><td>".$show['programname']."</td><td></td><td>".$show['starttime']."</td><td>".$show['genre']."</td><td>".$show['date']."</td><td>".$show['prerecorddate']."</td>"
		
?>
<td class="clock">
	<ul style="margin:0 auto; padding:0px; list-style:none; text-align:center;">
		<li id="hours" style="display:inline; text-align:center; font-family:'BebasNeueRegular', Arial, Helvetica, sans-serif; text-shadow:0 0 1px #00c6ff;">00</li>
		<li id="point" style="display:inline; text-align:center; font-family:'BebasNeueRegular', Arial, Helvetica, sans-serif; text-shadow:0 0 1px #00c6ff;">:</li>
		<li id="min" style="display:inline; text-align:center; font-family:'BebasNeueRegular', Arial, Helvetica, sans-serif; text-shadow:0 0 1px #00c6ff;">00</li>
		<li id="point" style="display:inline; text-align:center; font-family:'BebasNeueRegular', Arial, Helvetica, sans-serif; text-shadow:0 0 1px #00c6ff;">:</li>
		<li id="sec" style="display:inline; text-align:center; font-family:'BebasNeueRegular', Arial, Helvetica, sans-serif; text-shadow:0 0 1px #00c6ff;">00</li>
	</ul>
</td>
  <td style="color: #FF9900; font-weight: 500; text-align: center; text-shadow:0 0 1px" id="cc_c">_/_</td>
  <td style="color: #FF9900; font-weight: 500; text-align: center; text-shadow:0 0 1px" id="pl_c">_/_</td>
  <td style="color: #FF9900; font-weight: 500; text-align: center; text-shadow:0 0 1px" id="ad_c">_/_</td>
  <td style="color: #FF9900; font-weight: 500; text-align: center; text-shadow:0 0 1px" id="psa_c">_/_</td>
  <td style="color: #FF9900; font-weight: 500; text-align: center; text-shadow:0 0 1px" id="hit_c">_/_</td>
</tr>
</table>
    <?php
        if($_SESSION['access']='2'){
            echo "<div style='float:left'><strong>PGM#</strong><span style='color:#FF9900'>".$show['EpNum']."&nbsp;<span></div>";
            echo "<div><strong>DJ(s): </strong>";
            $SEC_PROG=mysql_real_escape_string($show['programname']);
            $DJS = mysql_query("SELECT performs.*,dj.active,dj.djname AS RealName,dj.alias AS DjName FROM performs,dj WHERE programname='$SEC_PROG' and '".$show['date']."' between STdate and ENdate and dj.Alias=performs.Alias");
            $LOOPED = FALSE;
            if(!mysql_errno()){
                while($DJ=mysql_fetch_array($DJS)){
                    if($LOOPED){
                        echo ",&nbsp;";
                    }
                    echo "<span style='color: #FF9900'>".$DJ['RealName']."</span>";
                    $LOOPED=TRUE;
                }
            }
            else{
                echo mysql_error();
            }
            echo "</div>";
        }
 ?>
</div>