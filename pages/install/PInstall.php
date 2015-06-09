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

// -------------------------------------------------------------------------------------------
//
// Page specific code
//
require_once(TP_SQLPATH . 'config.php');

$host		= DB_HOST;
$database 	= DB_DATABASE;
$prefix		= DB_PREFIX;

ob_start();
?>

<h1>Install database</h1>
<p>
Click below link to remove all contents from the database and create new tables and content from
scratch.
</p>
<p>
Database host: '<?=$host?>'
</p>
<p>
Database name: '<?=$database?>'
</p>
<p>
Prefix for tables: '<?=$prefix?>'
</p>
<p>
Update the database config-file (usually sql/config.php) to change the values.
</p>
<p>
&not; <a href='<?=$pagesPath . "PInstallProcess.php"?>'>Destroy current database and create from scratch</a>
</p>

<?php
$shell['html_body'] = ob_get_contents();
ob_end_clean();

// ========================================================================== //
// DRAW SHELL
// ========================================================================== //

draw_shell();

?>