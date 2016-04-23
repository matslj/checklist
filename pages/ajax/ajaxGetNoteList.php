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

// Get request parameters and validate
$pc = CPageController::getInstance(FALSE);
$listId = $pc->GETisSetOrSetDefault('listId', '');

CPageController::IsNumericOrDie($listId);

// Connect to db and get all the notes
$db = new CDatabaseController();
$mysqli = $db->Connect();

$notes = new CNoteListManager();

// Return notes in json format (array of note objects)
$jsonResult = $notes -> getNoteListAsJson($db, $listId);

$mysqli->close();

// Print the header and page
$charset	= WS_CHARSET;
header("Content-Type: text/html; charset={$charset}");
echo $jsonResult;
exit;

?>