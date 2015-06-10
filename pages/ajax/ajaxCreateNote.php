<?php
// ===========================================================================================
//
// File: ajaxCreateNote.php
//
// Description: Creates a note in the note table.
//            
//              Returns json with content-type text/html. Thought to be used in
//              an ajax call.
//
// Author: Mats Ljungquist
//

// Retrieve configuration
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . 'config.php');
$intFilter = new CInterceptionFilter();
$intFilter->UserIsSignedInOrRecirectToSignIn();

// Get request parameters and validate
$pc = CPageController::getInstance(FALSE);
$payload = $pc->POSTisSetOrSetDefault('payload', '');

$data = json_decode($payload);
$status = "ERROR";

// Connect to db and add the new note
$db = new CDatabaseController();
$mysqli = $db->Connect();

$text = $mysqli->real_escape_string($data -> text);
$tag = $mysqli->real_escape_string($data -> tag);

if ($text && $tag) {
    $result = CNoteManager::addNote($db, $text, $tag);
    $status = empty($result) ? "OK" : $status;
}

$mysqli->close();

// Return status (of how the db operation went) in json format
$jsonResult .= <<< EOD
{
    "status": "{$status}"
}
EOD;

// Print the header and page
$charset	= WS_CHARSET;
header("Content-Type: text/html; charset={$charset}");
echo $jsonResult;
exit;

?>