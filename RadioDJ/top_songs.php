<?php
/* ============================================================================= */
// EDIT BELOW
/* ============================================================================= */
$pageTitle = "Most Played Songs This Week";

$resDays		= 7;				// On how many days to build the top?
$resLimit		= 20;				// How many results to display?

/* ============================================================================= */
// END EDIT
/* ============================================================================= */

require_once('serv_inc.php');
require_once('header.php');

?>
<table class="main_table" border="0" cellspacing="0" cellpadding="5">
<?php

db_conn();

$query = "SELECT `artist`, title, count(*) AS tracks FROM `history` WHERE TIMESTAMPDIFF(DAY, `date_played`, NOW()) <= " . $resDays . " AND `song_type`=0 GROUP BY title ORDER BY tracks DESC LIMIT 0," . $resLimit;

$result = mysql_query($query);
	
if (!$result) {
	echo mysql_error();
	exit;
}

if (mysql_num_rows($result) == 0) {
	echo "<div class=\"noticediv\">No results found!</div>";
	require_once('footer.php');
	exit;
}

$inc = 1;

echo " <tr>" . "\n";
echo "   <td class=\"header_no\">#</td>\n";
echo "   <td class=\"header_live\">Artist</td>\n";
echo "   <td class=\"header_live\">Title</td>\n";
echo "   <td class=\"header_no\">Count</td>\n";
echo " </tr>" . "\n";

while($row = mysql_fetch_assoc($result)) {

	echo " <tr>" . "\n";
	echo "  <td class=\"entry_no\">" . $inc . ".</td>\n";
	echo "  <td>" . $row['artist'] . "</td>\n";
	echo "  <td>" . $row['title'] . "</td>\n";
	echo "  <td class=\"entry_no\">" . $row['tracks'] . "</td>\n";
	echo " </tr>" . "\n";

	$inc += 1;
}

@mysql_free_result($result);
db_close($opened_db);
?>
</table>

<?php require_once("footer.php"); ?>

