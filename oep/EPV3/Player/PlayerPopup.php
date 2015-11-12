<?php
	$URL = $_GET['URL'];
	$Desc = $_GET['DESC'];
?>
<!DOCTYPE HTML>
<head>
	<title>jPlayer</title>
	<link type="text/css" href="/js/jQuery.jPlayer.2.2.0/Skin/blue.monday/jplayer.blue.monday.css" rel="stylesheet" />
	<script type="text/javascript" src="/js/jQuery.jplayer.2.2.0/extras/jquery-1.8.2-ajax-deprecated.min.js"></script>
	<script type="text/javascript" src="/js/jQuery.jPlayer.2.2.0/jquery.jplayer.min.js"></script>
	<script type="text/javascript">
    $(document).ready(function(){
      $("#jquery_jplayer_1").jPlayer({
        ready: function () {
          $(this).jPlayer("setMedia", {
            m4a: "http://www.jplayer.org/audio/m4a/Miaow-07-Bubble.m4a",
            oga: "http://www.jplayer.org/audio/ogg/Miaow-07-Bubble.ogg"
          });
        },
        swfPath: "/js",
        supplied: "m4a, oga"
      });
    });
  </script>
</head>
<html>
	<body>
		<div id="Title"><h2>Player</h2></div>
		<div id="jquery_jplayer_1" class="jp-jplayer"></div>
		  <div id="jp_container_1" class="jp-audio">
		    <div class="jp-type-single">
		      <div class="jp-gui jp-interface">
		        <ul class="jp-controls">
		          <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
		          <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
		          <li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
		          <li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
		          <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
		          <li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
		        </ul>
		        <div class="jp-progress">
		          <div class="jp-seek-bar">
		            <div class="jp-play-bar"></div>
		          </div>
		        </div>
		        <div class="jp-volume-bar">
		          <div class="jp-volume-bar-value"></div>
		        </div>
		        <div class="jp-time-holder">
		          <div class="jp-current-time"></div>
		          <div class="jp-duration"></div>
		          <ul class="jp-toggles">
		            <li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
		            <li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
		          </ul>
		        </div>
		      </div>
		      <div class="jp-title">
		        <ul>
		          <li>Bubble</li>
		        </ul>
		      </div>
		      <div class="jp-no-solution">
		        <span>Update Required</span>
		        To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
		      </div>
		    </div>
		  </div>
	</body>
</html>