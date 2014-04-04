<?php
session_start();
if($_SESSION['access']!=2)
{
  header('location: /djhome.php');
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />

		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

		<title>index</title>
		<meta name="description" content="" />
		<meta name="author" content="j.oliver" />

		<meta name="viewport" content="width=device-width; initial-scale=1.0" />

		<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
		<link rel="shortcut icon" href="../favicon.ico" />
		<link rel="apple-touch-icon" href="../apple-touch-icon.png" />
		<link rel="stylesheet" href="../TPSBIN/CSS/EPISODE/Episode.css" />
        <link rel="stylesheet" href="../js/jquery/css/ui-lightness/jquery-ui-1.10.0.custom.min.css"/>
		<script src="../js/jquery/js/jquery-2.0.3.min.js"></script>
        <script src="../js/jquery/js/jquery-ui-1.10.0.custom.min.js"></script>
		<script src="../TPSBIN/JS/Remote/control.js"></script>
	</head>

	<body>
		<div>
			<header>
				<h1>Switch Control Suite</h1>
			</header>
			<!--<ul>
				<li>
					<a href="../masterpage.php">Home</a>
				</li>
				<li>
					<a href="../logout.php">Logout</a>
				</li>
			</li>
            </ul>-->
            <!--
            <button onclick="Get_Switch_Poll('EM24');">Execute 24 Hour Emergency Over Ride</button>
                <button onclick="Get_Switch_Poll('lock');">Lockout Front Panel (Lockdown)</button>
                <button onclick="Get_Switch_Poll('Unlock');">Unlock Front Panel (Restore)</button>
			<button onclick="Get_Switch_Poll('0U');">Switch Settings</button>
            <button onclick="Get_Switch_Poll('0SL');">POLL Switch</button>
            -->
			<div class="ui-state-error" style="display: none" id="error">
			</div>
			<pre class="ui-state-highlight" style="display: none" id="bay">
			</pre>
		</div>
        <div style="height: 50px;">
        </div>
        <div style="bottom: 0; position: fixed; height: 40px; width: 100%; color: white; background-color: #808080;">
            <span style="margin-left: 10px;">Switch Control Suite</span>
        <select id="control_select" name="control">
            <?php
                // Perform access verification;

            ?>
            <optgroup label="Controls">
                <option value="EM24">24 Hour Only</option>
                <option value="B1A">Booth 1 Air</option>
                <option value="B2A">Booth 2 Air</option>
                <option value="BBA">Both Booths Air</option>
            </optgroup>
            <optgroup label="Query">
                <option value="0U">Settings</option>
                <option value="0SL" selected>Current Status</option>
            </optgroup>
            <optgroup label="Security">
                <option value="lock">Lock Switch</option>
                <option value="unlock">Unlock Switch</option>
            </optgroup>
        </select>
            <button onclick="Get_Control()">Execute</button>
            <button onclick='window.location.href="../masterpage"'>Menu</button>
        </div>
        <div id="dialog-confirm" title="Confirm Execute" style="display:none;">
          <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This will change a active switch status, If a program is on air you might hard cut it!</p>
        </div>
	</body>
</html>
