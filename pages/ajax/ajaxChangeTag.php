<?php
// ===========================================================================================
//
// File: ajaxChangeTag.php
//
// Description: Bulk change of tagNote of table note. All the notes with a tagNote
//              <old value> will have their tagNotes changed to <new value>.
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

// Retrive request parameters
$pc = CPageController::getInstance(FALSE);
$old = $pc->POSTisSetOrSetDefault('old', '');
$new = $pc->POSTisSetOrSetDefault('new', '');

// Connect to db and perform bulk change
$db = new CDatabaseController();
$mysqli = $db->Connect();

$result = CNoteManager::changeTag($db, $mysqli, $old, $new);
$status = empty($result) ? "OK" : "ERROR";

$mysqli->close();

// Return status (of how the db operation went) in json format
$jsonResult = <<< EOD
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