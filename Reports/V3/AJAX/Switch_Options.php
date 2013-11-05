<div style="float: left;">
	<input type="hidden" value="SWS" name="RPT_TYPE"/>
	<label for="start_date">Start</label>
	<input id="start_date" type="date" name="start_date" value="<?php
	if(!isset($_POST['start_time'])){
		echo date( "Y-m-d", strtotime("yesterday"));
	}
	else{
		echo addslashes($_POST['start_date']);
	}
	?>"/>
	<input id="start_time" type="time" name="start_time" value="<?php
	if(!isset($_POST['start_time'])){
		echo "00:00";
	}
	else{
		echo addslashes($_POST['start_time']);
	}
	?>"/>
	<label for="end_date">End</label>
	<input id="end_date" type="date" name="end_date" value="<?php
	if(!isset($_POST['start_time'])){
		echo date( "Y-m-d", strtotime("today"));
	}
	else{
		echo addslashes($_POST['end_date']);
	}
	?>"/>
	<input id="end_time" type="time" name="end_time" value="<?php
	if(!isset($_POST['end_time'])){
		echo "00:00";
	}
	else{
		echo addslashes($_POST['end_time']);
	}
	?>"/>
	<input type="submit"/>
</div>