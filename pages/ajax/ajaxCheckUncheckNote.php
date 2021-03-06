<?php
// ===========================================================================================
//
// File: ajaxCheckUncheckNote.php
//
// Description: Checks or unchecks the 'checked' attribute in the 'note' table.
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
$id = $pc->GETisSetOrSetDefault('id', '');
$checked = $pc->GETisSetOrSetDefault('checked', 0);

CPageController::IsNumericOrDie($id);
CPageController::IsNumericOrDie($checked);

// Connect to database and check/uncheck note
$db = new CDatabaseController();
$mysqli = $db->Connect();

$result = CNoteManager::checkUncheckNote($db, $id, $checked);
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