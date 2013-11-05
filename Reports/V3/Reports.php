<?php
    session_start();
	$DEBUG = FALSE;
	include_once("AJAX/ChangeDB.php");
	
    if(isset($_POST['server'])){
		/*if(!isset($_SESSION['SRVPOST'])){
			if(isset($_POST['server'])){
				ChangeDB($_POST['server']);
				$_SESSION['SRVPOST']=$_POST['server'];
			}
			else{
				$_SESSION['SRVPOST']='NDEF000';
			}
			
		}
		else if($_SESSION['SRVPOST']!=$_POST['server']){
			
			if(ChangeDB($_POST['server'])){
				$SRVPOST = addslashes($_POST['server']);
			}
			else{
				$SRVPOST = addslashes($_POST['server']);
			}
		}*/
		ChangeDB($_POST['server']);
		$SRVPOST = $_SESSION['SRVPOST'];
    }
    elseif(isset($_SESSION['SRVPOST'])){
        $SRVPOST = $_SESSION['SRVPOST'];
    }
    else{
        $SRVPOST = "NDEF000";
    }
	$dbxml = simplexml_load_file("../../TPSBIN/XML/DBSETTINGS.xml");
	
    if(isset($_POST['RPT_TYPE']))
    {
        $REPORT_TYPE = $_POST['RPT_TYPE'];
    }
    //$SRVPOST = $_POST['']
    if($DEBUG){
    	echo "SRV:".$SRVPOST;
		echo "POST:".$_POST['server'];
	}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>TPS Reports</title>
        <link rel="stylesheet" href="../../altstyle.css" type="text/css"/>
        <link href="../../js/jquery/css/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" rel="stylesheet"/>
        <link href="../../TPSBIN/CSS/GLOBAL/drop_menu.css" type="text/css" rel="stylesheet"/>
        <script src="../../js/jquery/js/jquery-2.0.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery/js/jquery-ui-1.10.0.custom.min.js" type="text/javascript"></script>
        <script src="../../TPSBIN/JS/Reports/Reports.js" type="text/javascript"></script>
    </head>
    <body class="hasstatictop">
        <div class="statictop" id="topbar">
            <form id="settings" method="post" action="Reports.php">
            <div style="float: left">
                <!--<ul id="menu" class='has-sub' style="float:left;">
                	<li>
                		<a>Menu</a>
                		<ul>
                			<li><a href="#"><span class="ui-icon ui-icon-disk"></span>Save</a></li>
						  <li><a href="#"><span class="ui-icon ui-icon-zoomin"></span>Zoom In</a></li>
						  <li><a href="#"><span class="ui-icon ui-icon-zoomout"></span>Zoom Out</a></li>
						  <li class="ui-state-disabled"><a href="#"><span class="ui-icon ui-icon-print"></span>Print...</a></li>
						  <li>
						    <a href="#">Playback</a>
						    <ul>
						      <li><a href="#"><span class="ui-icon ui-icon-seek-start"></span>Prev</a></li>
						      <li><a href="#"><span class="ui-icon ui-icon-stop"></span>Stop</a></li>
						      <li><a href="#"><span class="ui-icon ui-icon-play"></span>Play</a></li>
						      <li><a href="#"><span class="ui-icon ui-icon-seek-end"></span>Next</a></li>
						    </ul>
						  </li>
				  		</ul>
				  </li>
				</ul>-->
				<ul id="statictop-menu" >
					<li><span>Menu</span>
						<ul>
							<li><a>&nbsp;</a></li>
							<!--<li><a href="">Reports</a>-->
							<li><a href="?t=WSA">Weekly Statistics</a></li>
							<li><a href="?t=MLR">Missing Log Report</a></li>
							<li><a href="?t=COM">Commercial Audit</a></li>
							<li><a href="?t=SWS">Switch Status</a></li>
								<!--<ul class="subnav">
									
								</ul>-->
							</li>
							<li><a>&nbsp;</a></li>
							<!--<li><a href="/masterpage.php">Refresh</a></li>-->
							<li><a href="../../masterpage.php">Exit</a></li>
						</ul>
					</li>
				</ul>
                <span class="statictop-leftbar">Server&nbsp;</span>
                <progress id="check_db" style="float:left;  display: none;"></progress>
                <select name="server" id="servers" onchange="this.form.submit();" style="float:left;">
                	<option value="NDEF000">Select Server</option>
                    <?php
                        foreach( $dbxml->SERVER as $convars):
                            if($convars->ACTIVE==1){
                                echo "<option value='".($convars->ID)."'";
                                if($convars->ID==$SRVPOST){
                                    echo " selected ";
                                }
                                echo ">".($convars->NAME)."</option>";
                            }
                        endforeach;

                    ?>
                </select>
                <span id="alert_icon" class="ui-icon ui-icon-alert" style="float: left; display: none; background-image: url('../../js/jquery/css/ui-lightness/images/ui-icons_228EF1_256x240.png');" title="Connection Test Result"></span>
            </div>
            <div id="top_options" class="statictop-leftbar">
                <?php
                if(isset($_GET['t']) || isset($REPORT_TYPE)){
	                if($_GET['t']=='WSA' || $REPORT_TYPE == 'WSA'){
	                	include("AJAX/GeneralStats_Options.php");
	                	//echo "Defined WSA";
	                }
					elseif($_GET['t']=="COM" || $REPORT_TYPE == 'COM'){
						include("AJAX/Commercial_Options.php");
					}
					elseif($_GET['t']=="SWS" || $REPORT_TYPE == 'SWS'){
						include("AJAX/Switch_Options.php");
					}
					else{
						echo "None Defined";
					}
				}
				else{
					echo "Select report type from menu";
				}
                ?>
            </div>
            </form>
        </div>
        	<!-- GOOGLE API TABLE -->
        	<script type='text/javascript' src='https://www.google.com/jsapi'></script>
		    <script type='text/javascript'>
		      google.load('visualization', '1', {packages:['table']});
		      google.setOnLoadCallback(drawTable);
		      function drawTable() {
		        var data = new google.visualization.DataTable();
		        //ADD HEADERS
		        data.addColumn('boolean','Status');
		        data.addColumn('number', 'Category')
		        data.addColumn('string', 'Advertisement');
		        data.addColumn('string', 'Program');
		        data.addColumn('string', 'Date');
		        data.addColumn('string', 'Time');
		        data.addColumn('number', 'ID');
		        data.addColumn('string', 'Timestamp');
		        	        
        	<?php
        		if($_POST['RPT_TYPE']="COM"){
        			if($_POST['ADID']!='REFINE' && $_POST['ADID'] != null){
        				/*if($_POST['ADID']=="CURRENT"){
        					echo "<h3>Awaiting Implementation</h3>";
        				}
						else{*/
							//echo "<h3>ADID:".$_POST['ADID']."</h3>";
							
							// CONNECT
							$linkRPT01 = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'], $_SESSION['DBNAME']);
							if(mysqli_connect_error()){
								echo "- CONNECTION ERROR -";
							}
							else{
								if($_POST['ADID']=="CURRENT"){
		        					$SQL2 = "SELECT * FROM (song,episode) JOIN adverts ON (song.title = adverts.AdName) WHERE ";
									if($FRIEND){
										$SQL2 .= " `Friend` = '1' ";
									}
									else{
										$SQL2 .= " `Friend` = '0' ";
									}
									// APPEND ACTIVE BOOL
									if($ACTIVE){
										$SQL2 .= "AND `Active` = '1' ";
									}
									else{
										$SQL2 .= "AND `Active` = '0' ";
									}
									// APPEND NAME RESTRICTION
									$SQL2 .= " AND `AdName` LIKE '%".addslashes($_POST['AD_NAME'])."%'";
		        				}
								else{
									$SQL2 = "SELECT * FROM `song`,`episode` WHERE `title` = (SELECT `AdName` FROM `adverts` WHERE `AdId` = '"
									. addslashes($_POST['ADID']) . "') and episode.programname = song.programname
									AND episode.date = song.date AND episode.starttime = song.starttime";
								}
								// APPEND OPTIONS
								// APPEND FRIEND BOOL
								
								$result = mysqli_query($linkRPT01,$SQL2);
								for($x=0; $x < mysqli_num_rows($result); $x++)
								{
									$row = mysqli_fetch_array($result);
									/*echo "<h1>".$row['title'] . " - " . $row['programname']."</h1>";*/
									echo "data.addRow([ ";
									if($row['AdViolationFlag']){
										echo "false";
									}
									else{
										echo "true";
									}
									echo " , ".$row['category'];
									echo " ,'".$row['title']."','";
									echo "<a target=\"_blank\" href=\"../../Episode/quickview.php?args=".urlencode($row['programname'])."@".$row['date']."@".$row['starttime']."@".$row['callsign']."\">";
									echo addslashes($row['programname'])."</a>','";
									echo $row['date']."','".$row['time']."', ";
									echo $row['songid']." ,'";
									echo $row['Timestamp']."']);
									";
								}
								//echo $SQL2;
								//echo "alert('$SQL2');
								//";
							}
							echo "var table = new google.visualization.Table(document.getElementById('table_div'));
	        						table.draw(data, {showRowNumber: false, allowHtml:true});
	        						// set the width of the column with the title 'Name' to 100px
								    /* var title = 'ID';
								     var width = '35px';
								     $('.google-visualization-table-th:contains(' + title + ')').css('width', width);
								     var title = 'Timestamp';
								     var width = '150px';
								     $('.google-visualization-table-th:contains(' + title + ')').css('width', width);
									 var title = 'Status';
								     var width = '35px';
								     $('.google-visualization-table-th:contains(' + title + ')').css('width', width);*/";
						}
        			}
				//}
        	?>
	        }
        </script>
        <script type='text/javascript'>
	      google.load('visualization', '1', {packages:['table']});
	      google.setOnLoadCallback(drawTable);
	      function drawTable() {
	        var data = new google.visualization.DataTable();
	        data.addColumn('string', 'Name');
	        data.addColumn('number', 'Salary');
	        data.addColumn('boolean', 'Full Time Employee');
	        data.addRows([
	          ['Mike',  {v: 10000, f: '$10,000'}, true],
	          ['Jim',   {v:8000,   f: '$8,000'},  false],
	          ['Alice', {v: 12500, f: '$12,500'}, true],
	          ['Bob',   {v: 7000,  f: '$7,000'},  true]
	        ]);
			<?php
			if($REPORT_TYPE == 'SWS' && !isset($_POST['clear']) ){
		        echo "var table = new google.visualization.Table(document.getElementById('table_div'));
		        table.draw(data, {showRowNumber: false});";
	        }
	        ?>
	      }
	    </script>
        <div id='stats'>
        	
        </div>
        <div id='table_div'>
        </div>
        <div id="db-error-dialog" title="Connection Error" style="display: none;">
          <p>
            <span class="ui-icon ui-icon-transferthick-e-w" style="float: left; margin: 0 7px 50px 0;"></span>
            There was a database connection error, The following result was returned
          </p>
          <p id="dberror_notify" class="ui-state-error ui-corner-all">
            Error to go here
          </p>
        </div>
    </body>
</html>
