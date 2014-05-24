<?php

/*

VERSION: 2.1
AUTHOR: Marius Vaida
WEBSITE: http://radiodj.ro

*/


/* ============================================================================= */
// EDIT BELOW
/* ============================================================================= */

$pageTitle = "CKXU Radio Society";

/* ============================================================================= */
// END EDIT
/* ============================================================================= */

require_once('serv_inc.php');
require_once('header.php');
?>
<h1>Welcome to RadioDJ Demo script 2.2!</h1>

<div align="left" style="padding:10px;">

<strong>WHAT IS THIS?</strong>
<br />
<ul>
	<li>This script's purpose is to show you how can your website interact with RadioDJ;</li>
	<li>Most of the sections are dynamically generated, so they won't need any maintenance;</li>
</ul>

<hr />
<br />

<strong>HOW TO INSTALL?</strong>
<br />
<ul>
	<li>Open serv_inc.php and enter your MySQL server details;</li>
	<li>Make sure you set the time-zone to your area!</li>
</ul>

<hr />
<br />

<strong>CHANGES IN V2.2:</strong>

<ul>
	<li>Added support for TCP-IP server plugin and demo commands provided;</li>
</ul>

<strong>CHANGES IN V2.1:</strong>

<ul>
	<li>Requested song will not be added to the database if the requested track is already in the queue and not played;</li>
	<li>Request number can be limited for each IP for a 24 hours period.</li>
	<li>Messages and displayed text in Request section can be customized.</li>
</ul>

<hr />
</div>


<?php

require_once('footer.php'); 

?>
