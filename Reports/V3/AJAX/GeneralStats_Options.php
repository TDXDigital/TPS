<?php
	if(isset($_GET['l'])){
		if($_GET['l']==1){
			$lock = TRUE;
		}
		else{
			$lock = FALSE;
		}
	}
	else{
		$lock = FALSE;
	}
	echo "<span>Weekly Statistics&nbsp;</span>";
	echo "<form action='#' autocomplete='on'>";
	echo "<select>
	<option value='%'>All Programs</option></select>";
	echo "</form>"
?>