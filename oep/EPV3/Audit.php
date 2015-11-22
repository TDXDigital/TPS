<!DOCTYPE html>
<html style="height: 100%;" lang="en">
	<head>
		<!--<script type="text/javascript" src="https://www.google.com/jsapi"></script>-->
        <script src="../../js/jquery/js/jquery-2.1.1.min.js"></script>
		<script src="../../js/jquery/js/jquery-ui-1.11.0/jquery-ui.min.js"></script>
		<script src="../../js/globalize-master/lib/globalize.js"></script>
		<script src="../../js/modernizr.js"></script>
		<script type="text/javascript" src="../../js/jquery-blockui.js"></script>
		<script type="text/javascript" src="../../js/jquery-jMenu.js"></script>
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="../../phpstyle.css" />
		<link rel="stylesheet" type="text/css" href="../../js/css/jMenu.jquery.css" media="screen" />
		<!--<link rel="stylesheet" type="text/css" href="../../js/jquery/css/smoothness/jquery-ui-1.10.0.custom.css" />-->
        <link rel="stylesheet" type="text/css" href="../../js/jquery/js/jquery-ui-1.11.0/jquery-ui.min.css" />
		<title>Program Audit Configuration</title>
		<meta http-equiv="Content-Type" content="text/html;" charset="UTF-8">
		<script>
		  $(function() {
		    $( "#from" ).datepicker({
		      defaultDate: "-1w",
		      changeMonth: true,
		      changeYear: true,
		      dateFormat: "yy-mm-dd",
		      showButtonPanel: true,
		      numberOfMonths: 2,
		      onClose: function( selectedDate ) {
		        $( "#to" ).datepicker( "option", "minDate", selectedDate );
		      }
		    });
		    $( "#to" ).datepicker({
		      changeMonth: true,
		      changeYear: true,
		      numberOfMonths: 2,
		      dateFormat: "yy-mm-dd",
		      showButtonPanel: true,
		      onClose: function( selectedDate ) {
		        $( "#from" ).datepicker( "option", "maxDate", selectedDate );
		      }
		    });
		    $( "#opts" ).buttonset();
		    $( "#radio" ).buttonset();
		    $( "#radio_fmt" ).buttonset();
		    $( "#prt_opts" ).buttonset();
		    $( "#tms" ).buttonset();
		    $( "#order" ).buttonset();
		  });
		 </script>
	</head>
	<body style="height: inherit;">
		<div id="content" style="margin: auto; width: 1280px; height: inherit; background-color: white;">
			<form action="../../reportInterpreter.php" method="post" target="_blank" accept-charset="utf-8">
			<div id="logo" style="margin-bottom: 25px;"><img style="display: inline;" src="../../images/Ckxu_logo_PNG.png" height="50px" alt="logo"/><h1 style="display: inline; text-align: center; width: 100%">
				Audit Options</h1><br/><hr></div>
			<div id="options" style="float: left;">
				<h2>Date Range</h2>
				<label for="from">From</label>
				<input type="text" id="from" required name="from" placeholder="Start Date"/>
				<label for="to">To</label>
				<input type="text" id="to" required name="to" placeholder="To Date"/>
			</div>
			<div id="program" style="float: left; margin-left: 20px;">
				<h2>Program Options</h2>
				<label for="callsign">Callsign</label>
				<input type="text" id="callsign" name="callsign" placeholder=""/>
				<label for="program">Program Name</label>
				<input type="text" id="program" name="program" placeholder=""/>
			</div>
			<div id="CustRep" style="margin-top: 100px;">
				<div id="CRO">
					<h2>Customized Report</h2>
					<div id="radio" style="float: left">
						<h3>Time Format</h3>
						<input type="radio" id="time12" name="timef" value="12" disabled /><label for="time12">12 Hour</label>
						<input type="radio" id="time24" name="timef" value="24" checked="checked"/><label for="time24">24 Hour</label>
					</div>
					<div id="opts" style="float: left; margin-left: 20px">
						<h3>Printout Inclusions</h3>
						<input type="checkbox" name="ple" id="pl" checked title="Include Playlist Numbers"/><label for="pl">Playlist</label>
						<input type="checkbox" name="bcd" id="bcd" checked title="Create a barcoded EAN13 Report"/><label for="bcd">Barcoded</label>
						<input type="checkbox" name="pbr" id="pbk" checked title="Place a page break after each log"/><label for="pbk">Page Break</label>
						<input type="checkbox" disabled name="sls" id="sls" title="Generate individual Log Statistics"/><label for="sls">Show Stats</label>
						<input type="checkbox" disabled name="rps" id="rps" title="Include Statistics overview for report timeframe"/><label for="rps">Statistics</label>
					</div>
					<div id="tms" style="float: left; margin-left: 20px">
						<h3>Start Times</h3>
						<input type="radio" name="tms" id="tm1" value="1" checked required title="Included"/><label for="tm1">Included</label>
						<input type="radio" name="tms" id="tm0" value="0" title="Excluded"/><label for="tm0">Excluded</label>
					</div>
				</div>
			</div>
			<div id="CustRep" style="margin-top: 100px;">
				<div id="CRO">
					<h2>Audit Settings</h2>
					<div id="radio_fmt" style="float: left">
						<h3>Format</h3>
						<input type="radio" id="fmthtm" name="fmt" checked="checked"/><label for="fmthtm">HTML</label>
						<input type="radio" id="fmtpdf" name="fmt" disabled /><label for="fmtpdf">PDF</label>
                        <input type="radio" id="fmtxls" name="fmt" disabled /><label for="fmtpdf">XLSX</label>
					</div>
					<div id="prt_opts" style="float: left; margin-left: 20px">
						<h3>Audit Type</h3>
						<input type="radio" name="type" value="COM" id="COT" title="Include Playlist Numbers"/><label for="COT">Commercial Only</label>
						<input type="radio" name="type" value="MUO" id="MOT" title="Create a barcoded EAN13 Report"/><label for="MOT">Music Only</label>
						<input type="radio" name="type" value="SPO" id="SOT" title="Place a page break after each log"/><label for="SOT">Spoken Only</label>
						<input type="radio" name="type" value="CMP" id="CMT" checked="checked" title="Generate individual Log Statistics"/><label for="CMT">Complete</label>
                        <input type="radio" name="type" value="ADM" id="ADM" title="Gives So Much Information..."/><label for="ADM">Admin Review (Very Detailed)</label>
					</div>
					<div id="order" style="float: left; margin-left: 20px">
						<h3>Order By</h3>
						<input type="radio" name="sort" value="En" id="EN" checked title="Include Playlist Numbers"/><label for="EN">Episode Number</label>
						<input type="radio" name="sort" value="St" id="ST" title="Include Playlist Numbers"/><label for="ST">Start Time</label>
						<input type="radio" name="sort" value="Pn" id="PN" title="Create a barcoded EAN13 Report"/><label for="PN">Show Name</label>
					</div>
				</div>
			</div>
		</div>
		<div id="foot" style="width: 100%; position: fixed; bottom: 0px; background-color: black; margin-left: 0; padding-left: 0; height: 40px; text-align: center">
				<input id="exit" value="Cancel" type="button" onclick="window.location.href='../../masterpage.php'"/>
				<input id="sub1" value="Submit" type="Submit"/> 
			</div>
		</form>
		
	</body>
</html>