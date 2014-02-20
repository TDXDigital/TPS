<?php
    include_once "../../TPSBIN/functions.php";
	sec_session_start();
	if(!isset($_GET['program'])&&$_GET['p']!=""){
		$_SESSION['program'] = addslashes(urldecode($_GET['p']));
		$_SESSION['time'] = addslashes(urldecode($_GET['t']));
		$_SESSION['date'] = addslashes(urldecode($_GET['d']));
		$_SESSION['callsign'] = addslashes(urldecode($_GET['c']));
	}

    // ALLOW DEBUG LEVEL USERS ONLY
	$DEBUGONLY = TRUE;

    //phpinfo();
    
?>

<!DOCTYPE HTML>
<html style="height: 100%;">
	<head>
		<!-- Javascript Includes -->
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<!--<script src="/js/jquery/js/jquery-1.9.0.js"></script>-->
		<script src="../../js/jquery/js/jquery-1.9.1.min.js"></script>
		<script src="../../js/jquery/js/jquery-ui-1.10.0.custom.min.js"></script>
		<script src="../../js/globalize-master/lib/globalize.js"></script>
		<script src="../../js/modernizr.js"></script>
		<script type="text/javascript" src="../../js/jquery-blockui.js"></script>
		<script type="text/javascript" src="../../js/jquery-jMenu.js"></script>
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="../../phpstyle.css" />
		<link rel="stylesheet" type="text/css" href="../../js/css/jMenu.jquery.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="../../TPSBIN/CSS/Episode/Episode.css" />
 		<title>Digital Program Logs</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<style>
		  .ui-autocomplete-loading {
		    background: white url('images/JSON.gif') right center no-repeat;
		  }
		</style>
		
		<!-- JS -->
<script type="text/javascript">
<?php
	if(isset($_SESSION['program'])){
		if($_SESSION['program']==""||$_SESSION['time']==""||$_SESSION['date']==""){
			unset($_SESSION['program']);
			unset($_SESSION['time']);
			unset($_SESSION['date']);
			echo "/*WARNING: UNSET SESSION VARS, Requirements not met*/";
			echo "var hasset = 'FALSE'";
			/*echo "$.blockUI({
				message: $('#Login')
			});";*/
		}
		else{
			echo "var hasset = 'TRUE'";
		}
		$varphp = "?p=".addslashes($_SESSION['program'])."&t=".addslashes($_SESSION['time'])."&d=".addslashes($_SESSION['date']);
		echo "//".$_SESSION['program'].";".$_SESSION['time'].";".$_SESSION['date']."
		";
		echo "var varphp = '".$varphp."'";
		$NODATA = FALSE;
	}
	else{
		/*echo "alert('WARNINING: No Variables are defined for the program, please assign the values for \$_GET');
		var hasset = 'FALSE';
		";*/
		$NODATA = TRUE;
	}
	if(isset($_GET['disable'])){
		if($_GET['disable']=="true"){
			$DISABLE="TRUE";
		}
		else{
			$DISABLE="FALSE";
		}
	}
	else{
		$DISABLE = "FALSE";
	}
	echo "
	var disable_prompt = '".$DISABLE."';
	";
	if(isset($_GET['argm'])){
	    echo "var argm='".$_GET['argm']."';
        ";
	}
    else{
        echo "var argm='';";
    }
?>

    function CheckDbOnLoad(){
    <?php

  	$login = FALSE;
  	$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
	if(!$con){
		if(!mysql_select_db($_SESSION['DBNAME'])){
			echo"$.blockUI({
					message: $('#Login')
				});
				";
			}
		else{
			echo "//Database Connected
			";
			//$login = true;
		}
	}
	else{
		if(!mysql_select_db($_SESSION['DBNAME'])){
			echo"
			$('#content').hide();
			$.blockUI({
					message: $('#Login')
				});
				";
			}
		else{
			if($NODATA == TRUE){
				echo"
				$('#content').hide();
				$.blockUI({
						message: $('#newLoad')
					});
				";
			}
			else{
				echo "//Established Connection
				$.blockUI({ message: $('#domFetch'), overlayCSS: {backgroundColor:'#C0C0C0'} });
                LOAD_OK = 'TRUE';
				";
			}
			$login=true;
		}
		/*if($login == true && $NODATA = TRUE){
			echo "//Con Good, Nodata observed
			$.blockUI({
				message: $
			})"
		}*/
		mysql_close($con);
	}

        
    if($DEBUGONLY == TRUE && $_SESSION['access']!='2'){
        //header("location: /episode/p2insertEP.php");
        echo "
        $(function() {
            $( \"#dialog-message\" ).show();
            $( \"#dialog-message\" ).dialog({
                    modal: true,
                    close: function() {
                        window.location.href = \"../../Episode/p1update.php\";
                    },
                    buttons: {
                    Ok: function() {
                        $( this ).dialog( \"close\" );
                    }
                    
                 }
                    
            });
        });";
    }
  	?>
    }
</script>
<script type="text/javascript" src="../../TPSBIN/JS/Episode/Counts.js"></script>
<script type="text/javascript" src="../../TPSBIN/JS/Episode/Core.js"></script>
<script type="text/javascript" src="../../TPSBIN/JS/Episode/Interaction.js"></script>
<?php
	if($NODATA == FALSE && $login = TRUE){
		$con = mysql_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
		if (!$con){
			echo '<link rel="stylesheet" type="text/css" href="../../js/jquery/css/blitzer/jquery-ui-1.9.2.custom.css"/>';
			echo '<!--Error DBcon-->';
		}
		else{
			if(!mysql_select_db($_SESSION['DBNAME'])){
				echo '<link rel="stylesheet" type="text/css" href="../../js/jquery/css/blitzer/jquery-ui-1.9.2.custom.css"/>';
			echo '<!--Error Access Error-->';
			}
			else{
				$query = "SELECT * FROM program WHERE callsign='".addslashes($_SESSION['callsign'])."' and programname='".addslashes($_SESSION['program'])."' ";
				if(!$result = mysql_query($query)){
					echo '<link rel="stylesheet" type="text/css" href="../../js/jquery/css/blitzer/jquery-ui-1.9.2.custom.css"/>';
					echo "<!-- ERROR query: ".$query." 
					Error Response: ".mysql_error()."-->";
				}
				else{
					$arr=mysql_fetch_array($result);
					$type=$arr['Theme'];
					switch ($type){
						case 0:
							echo '<link rel="stylesheet" type="text/css" href="../../js/jquery/css/blitzer/jquery-ui-1.9.2.custom.css"/>';
							break;
						case 1:
							echo '<link rel="stylesheet" type="text/css" href="../../js/jquery/css/vader/jquery-ui-1.10.0.custom.css" />';
							break;
						case 2:
							echo '<link rel="stylesheet" type="text/css" href="../../js/jquery/css/ui-darkness/jquery-ui-1.10.0.custom.css" />';
							break;
						case 3:
							echo '<link rel="stylesheet" type="text/css" href="../../js/jquery/css/ui-lightness/jquery-ui-1.10.0.custom.css" />';
							break;
						case 4:
							echo '<link rel="stylesheet" type="text/css" href="../../js/jquery/css/smoothness/jquery-ui-1.10.0.custom.css" />';
							break;
						case 5:
							echo '<link rel="stylesheet" type="text/css" href="../../js/jquery/css/flick/jquery-ui-1.10.0.custom.css" />';
							break;
						case 6:
							echo '<link rel="stylesheet" type="text/css" href="../../js/jquery/css/dot-luv/jquery-ui-1.10.0.custom.css" />';
							break;
						case 7:
							echo '<link rel="stylesheet" type="text/css" href="../../js/jquery/css/excite-bike/jquery-ui-1.10.0.custom.css" />';
							break;
						case 8:
							echo '<link rel="stylesheet" type="text/css" href="../../js/jquery/css/trontastic/jquery-ui-1.10.0.custom.css" />';
							break;
						default:
							echo '<link rel="stylesheet" type="text/css" href="../../js/jquery/css/smoothness/jquery-ui-1.10.0.custom.css" />';
					}
					echo "<!-- COMPLETE SWITCH -->";
				}
			}
			
		}
	}
    else{
        echo '<link rel="stylesheet" type="text/css" href="../../js/jquery/css/smoothness/jquery-ui-1.10.0.custom.css" />';
    }
		
		
		
?>
	</head>
	<body style="height: inherit;">
		<div id="Login" title="Login Form" style="display:none; cursor: default;"> 
			 <h2>Login</h2>
			 <form name="login" action="AJAX/components/Login.php" method="post">
			 	<?php if(isset($_GET['auth'])){
			 		//Authentication Error Response
			 		echo "<p style='color:red'>" . $_GET['auth']."</p>";
			 	} ?>
			 	<span>Username&nbsp;</span><input type="text" id="user" name="name" style="width: inherit"/><br/>
			 	<span>Password&nbsp;&nbsp;</span><input type="password" name="pass" style="width: auto" id="pwd"/></br>
		    	<input type="submit" id="New" value="Login" />
		    </form> 
		</div>
		<div id="domMessage" style="display:none;"> 
    		<h2><img src="../../images/GIF/ajax-loader1.gif" alt="processing" />Processing, Please Wait...</h2> 
		</div>
		<div id="domFetch" style="display:none; cursor: default; text-align: center;"> 
    		<h2 style="width: max-content; text-align: center"><!--<img src="/images/GIF/ajax-loader1.gif" alt="processing" />-->Loading...</h2> 
            <progress style="width: max-content;" id='progressLoad'></progress>
		</div>
		<div id="domScratch" style="display:none; cursor: default;"> 
    		<h2><img src="../../images/GIF/ajax-loader2.gif" alt="processing" />An Error Has Occured, Click to Dismiss</h2> 
		</div>
		<div id="question" style="display:none; cursor: default;"> 
			<h1>This episode has no completion time. would you like to exit and use current time</h1> 
		    <input type="button" id="yes" value="Yes" /> 
		    <input type="button" id="exit" value="Exit without finalizing" />
		    <input type="button" id="no" value="No" /> 
		</div> 
		<div id="newLoad" title="Log Load Settings" style="display:none; cursor: default;"> 
			<h3>There is no defined log, do you want to create a new log or load existing?<br/><br/></h3> 
		    <!--<input type="button" id="New" value="NEW" onclick='$.unblockUI();ajaxLoad("NewLog.php");'/>-->
		    <input type="button" id="New" value="New" onclick='window.location.href="NewLog.php"'/>
		    <input type="button" id="Load" value="Load" onclick="launchload();"/>
            <!--<input type="button" id="Load" value="Load" onclick="$.blockUI({ message: $('#LoadPrompt'), overlayCSS: {backgroundColor:'#f5f5f5'} })"/>-->
            <input type="button" id="Exit" value="Logout" style="float: right" onclick='window.location.href="/logout.php"'/> 
		</div> 
		<div id="LoadPrompt" title="Load Log" style="display:none; cursor: default; ">
			<form method="POST" id="loadform" action="AJAX/LoadLog.php">
				<span id="loadMessage" style="width: 100%"></span>
				<input id="Barcode" style="float: right" class="text ui-widget-content ui-corner-all" type="text" name="barcode" placeholder="Episode Number / Barcode" size="40"/>
                <br/><br/>
                <fieldset>
                    <label for="datepicker">Date</label>
                    <input type="text" class="text ui-widget-content ui-corner-all" id="datepicker" name="datepicker" placeholder="mm/dd/yyyy" />
                    <br/>
                    <label for="name">Program</label>
                    <input type="text" class="text ui-widget-content ui-corner-all" id="name" name="name" />
                    <br/>
                </fieldset>
				<!--<span style="float: right;">
                    <input type="submit" value="load" />
                    <button id="new_from_Load" onclick="$.blockUI({ message: $('#newLoad'), overlayCSS: {backgroundColor:'#f5f5f5'} })">Full Options</button>
                    <button id="new_from_Load" onclick="$.unblockUI();">Cancel</button>
                </span>-->
			</form>
		</div>
		<div id="msg" title="Message" style="display:none; cursor: default;"> 
			<p><?php echo $_GET['msg']; ?></p> 
		    <input type="button" id="New" value="NEW" /> 
		    <input type="button" id="Load" value="LOAD" />
		    <input type="button" id="Exit" value="EXIT" /> 
		</div> 
		<div id="content" style="margin: auto; width:1280px; height: inherit; background-color: white;">
			<!-- NAV -->
			<ul id="jMenu">
				<li><!--<span class="fNiv" style="color:white"><strong><i>&nbsp;CKXU DPL&nbsp;</i></strong></span>-->&nbsp;<img src="../../js/images/logo35.png" height="25" style="vertical-align: middle;" alt="logo"/>&nbsp;</li>
			  <li><a class="fNiv">Log Options</a><!-- Do not forget the "fNiv" class for the first level links !! -->
			    <ul>
			      <li class="arrow"></li>
			      <li><a onclick="ajaxLoad('AJAX/components/NewLog.php')">New Log</a>
			        <!--<ul>
			          <li><a>Live Broadcast</a></li>
			          <li><a>Prerecord</a></li>
			          <li><a>Timeless</a></li>
			        </ul>-->
			      </li>
                  <li><a onclick="launchload();">Load Log</a></li>
			      <!--<li><a onclick="$.blockUI({ message: $('#LoadPrompt'), overlayCSS: {backgroundColor:'#f5f5f5'} });">Load Log</a></li>-->
			      <li><a onclick="Finalize();">Finalize Episode</a></li>
			      <li><a href="AJAX/components/DestroySession.php?dest=../../../../masterpage.php">Exit</a>
			      	<ul>
			              <li><a href="../../logout.php">Logout</a></li>
			        </ul>
			      </li>
			    </ul>
			  </li>
			  <li><a class="fNiv">Settings</a><!-- Do not forget the "fNiv" class for the first level links !! -->
		        <ul>
		          <li><a onclick="ajaxLoad('AJAX/components/ChDesc.php')">Change Description</a></li>
		          <li><a>Change Program</a></li>
		          <li><a>Broadcast Type</a>
		          	<ul>
			          <li><a>Live Broadcast</a></li>
			          <li><a>Prerecord</a></li>
			          <li><a>Timeless</a></li>
			        </ul>
		          </li>
		          <li><a>Manage DJs</a>
		            <ul>
		              <li><a>Set Guests</a></li>
		              <li><a>Edit Host List</a></li>
		              <li><a>Episode Hosts</a></li>
		            </ul>
		          </li>
		        </ul>
		      </li>
			  <!--<li><a class="fNiv">&nbsp;&nbsp;Commercials&nbsp;&nbsp;</a>
			    <ul>
			      <li class="arrow"></li>
			      <li><a onclick="ajaxLoad('AJAX/Commercials/listFriends.php')">Friends Program</a>
			        <ul>
			          <li><a onclick="ajaxLoad('AJAX/Commercials/listFriends.php')">List All Friends</a></li>
			        </ul>
			      </li>
			      <li><a>Commercial List</a></li>
			      <li><a>PSA</a></li>
			    </ul>
			  </li>-->
			  <?php
			  $admin = '
			  <li><a class="fNiv">&nbsp;&nbsp;Administration&nbsp;&nbsp;</a>
			    <ul>
			      <li class="arrow"></li>
			      <li><a>Manual</a></li>
			      <li><a>Contact Support</a></li>
			    </ul>
			  </li>';
			  $debug = '<li><a class="fNiv">Development & Debug</a>
		        <ul>
		          <li class="arrow"></li>
		          <li><a>Errors</a>
	         		<ul>
			          <li><a onclick="$(\'#error\').append(\'<p>Sample Error</p>\');$(\'#error\').show();">Force Error</a></li>
			          <li><a onclick="$(\'#error\').show();">Show Error Field</a></li>
			          <li><a onclick="$(\'#error\').hide();">Hide Error Field</a></li>
			          <li><a onclick="$(\'#error\').html(\'\');">Clear Error Field</a></li>
			          <li><a onclick="javascript:growl(\'<p>This is a Error Growler</p>\');">Force Growl</a></li>
			        </ul>
			      </li<
			      <li><a>Warning</a>
	         		<ul>
			          <li><a onclick="$(\'#info\').append(\'<p>Sample warning</p>\');$(\'#info\').show();">Force Warning</a></li>
			          <li><a onclick="$(\'#info\').show();">Show info Field</a></li>
			          <li><a onclick="$(\'#info\').hide();">Hide info Field</a></li>
			          <li><a onclick="$(\'#info\').html(\'\');">Clear info Field</a></li>
			          <li><a onclick="javascript:growl(\'<p>This is a Warning Growler</p>\');">Force Growl</a></li>
		          	</ul>
		          </li>
		          <li><a onclick="$(\'#collector\').load(\'AJAX/components/Collector.php\');">load Collector (Spinners Disabled)</a></li>
		          <li><a onclick="$(\'#list\').load(\'AJAX/components/list.php\');">load List</a></li>
		          <li><a onclick="javascript:setSpinners();">load JavaScript Interfaces</a></li>
		          <li><a onclick="javascript:UpdateCounts();">Update Counts</a></li>
		        </ul>
		      </li>
			  ';
			  if($_SESSION['access']=='2'){
			  	//Admin Level Access
			  	echo $admin;
				echo $debug;
			  }
			  ?>
			  <li><a class="fNiv">&nbsp;&nbsp;Help / Reference&nbsp;&nbsp;</a><!-- Do not forget the "fNiv" class for the first level links !! -->
			    <ul>
			      <li class="arrow"></li>
			      <li><a>Help</a>
			        <ul>
			          <li><a>Training</a></li>
			          <li><a target="_blank" href="/index.php/report-a-issue">Report Issue</a></li>
			        </ul>
			      </li>
			      <li><a>Manual</a>
                      <ul>
                          <li><a onclick="jQuery('#dialog-types').dialog({ modal: false });">Define Types</a></li>
                      </ul>
                  </li>
			      <li><a>Contact Support</a></li>
			      <li><a >About</a></li>
			    </ul>
			  </li>
			</ul>
			<!-- End NAV -->
			
			<!-- Show Info -->
			<div id="stats" style=" width: inherit;"></div>
			<!--<div id="system" style=" width: inherit;"></div>-->
			<!-- Notifications -->
			<div id="info" class="ui-state-highlight ui-corner-all" style="display: none; width: inherit;"></div>
			<div id="error" class="ui-state-error ui-corner-all" style="display: none; width: inherit;"></div>
			<!-- Player -->
			<div id="player" style="display:none;">
			</div>
			<!-- Interface for Gather and Display -->
			<div id="collector">
				
			</div>
			<div id="limit" style="position: relative; overflow: hidden;">
				<div id="list" style=" width: inherit;"></div>
			</div>
			<div id="foot" class="fade" style="background-color: black; width: inherit; color:white; text-align: right; position:fixed ;bottom:0pt;">
				<span style="float: left;" class="fade">&nbsp;Version Alpha 0.3.60&nbsp;</span>
				<?php
					// listeners 
					if(TRUE){
						echo "<span style=\"float: left; margin-left: 30px;\">Current Online Listeners:&nbsp;</span>";
						echo "<span style=\"float: left; color: white;\" class=\"fade\" id=\"cc_stream_info_listeners\"><img style=\"padding-top: 3px;\" src='Images/BarLoader.gif' alt='...'></span>";
					}
					// Display Warning if finalized
				?>
				<!--<span style="float: left; color: white;" class="fade" id="cc_stream_info_listeners_OLD"><img style="padding-top: 3px;" src='Images/BarLoader.gif' alt='...'></span>-->
				<a href="http://www.tdxdigital.ca/" target="_blank"><img style="margin-right: 5px;" src="../../images/TDX Logo/White_on_black_background_85x23.png" alt="Copyright &copy; James Oliver" title="Copyright TDX Digital" /></a>
				<!--<span class="fade">Copyright &copy; James Oliver 2013&nbsp;</span>-->
			</div>
            <div id="dialog-message" title="User Violation" class="HiddenDialog">
              <p>
                <span class="ui-icon ui-icon-circle-close" style="float: left; margin: 0 7px 50px 0;"></span>
                You are not permitted to access these logs
              </p>
              <p>
                Only <b>Debug</b> users can access these logs.
              </p>
            </div>
            <div id="dialog-types" title="Category Definitions" class="HiddenDialog">
              <p>
                <span class="ui-icon ui-icon-info" style="float: left; margin: 0 7px 50px 0;"></span>
                The Definitions are being corrected and entered in a new format
              </p>
              <p>
                This will be available again soon
              </p>
            </div>
			<div style="height: 20px;"></div>
		</div>
		<?php
			// Stream JS Files location
		
		?>
		<script type="text/javascript" src="../../js/streaminfo.js"></script>
		<script type="text/javascript" src="http://cent4.serverhostingcenter.com/js.php/zuomcwvv/streaminfo/rnd0"></script>
	</body>
</html>