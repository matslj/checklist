<?php

require_once "../../template.php";

// Set simple page variables that are used by template.php
$shell['title'] = "Kaninboets checklista";
$shell['html_head'] = "";

$pagesPath = WS_SITELINK . "pages/install/";

// ========================================================================== //
// HTML BODY
// ========================================================================== //

// ****************************************
// ** Sätt headern i html body
ob_start();
?>
<h1>Kaninboets checklista</h1>
<div id="promotext">   
<p style='font-style: italic'>
    En reseplanerare för kaniner
</p>
</div>
<?php
$shell['html_header'] = ob_get_contents();
ob_end_clean();
// ** Header i html body är nu satt
// *****************************************

$htmlMain = "";
$db 	= new CDatabaseController();
$mysqli = $db->Connect();

// -------------------------------------------------------------------------------------------
//
// Execute several queries and print out the result.
//
$queries = Array('SQLCreateUserAndGroupTables.php');

foreach($queries as $val) {

	$query 	= $db->LoadSQL($val);
	$res 	= $db->MultiQuery($query);
	$no	= $db->RetrieveAndIgnoreResultsFromMultiQuery();
        
        $errorStyle = $mysqli->errno == 0 ? "green" : "red";

	$htmlMain .= <<< EOD
<h3>SQL Query '{$val}'</h3>
<p>
<div class="sourcecode">
<pre>{$query}</pre>
</div>
</p>
<p style="font-weight: bold; color:{$errorStyle};">**** Statements that succeeded: {$no} ****</p>
<p style="font-weight: bold; color:{$errorStyle};">**** Error code: {$mysqli->errno} ({$mysqli->error}) ****</p>
EOD;
}

$mysqli->close();

ob_start();
?>

<h1>Database installed</h1>
<?=$htmlMain?>

<?php
$shell['html_body'] = ob_get_contents();
ob_end_clean();

// ========================================================================== //
// DRAW SHELL
// ========================================================================== //

draw_shell();

?>