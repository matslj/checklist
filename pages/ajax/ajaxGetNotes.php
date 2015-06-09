<?php
// ===========================================================================================
//
// File: ajaxGetNotes.php
//
// Description: Gets all the notes. Should be called from an ajax request.
//              Returns json (in text/html content-type).
//
// Author: Mats Ljungquist
//

// Retrieve configuration
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . 'config.php');
$intFilter = new CInterceptionFilter();
$intFilter->UserIsSignedInOrRecirectToSignIn();
// -------------------------------------------------------------------------------------------
//
// Interception Filter, controlling access, authorithy and other checks.
//
//$intFilter = new CInterceptionFilter();
//$intFilter->FrontControllerIsVisitedOrDie();

$db = new CDatabaseController();
$mysqli = $db->Connect();

$notes = new CNoteManager();
$jsonResult = $notes -> getNotesAsJson($db);

$mysqli->close();

// Print the header and page
$charset	= WS_CHARSET;
header("Content-Type: text/html; charset={$charset}");
echo $jsonResult;
exit;

?>