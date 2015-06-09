<?php
// ===========================================================================================
//
// File: PScoreboard.php
//
// Description: This provides the content for a score board dialog in html format.
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

$pc = CPageController::getInstance(FALSE);
$payload = $pc->POSTisSetOrSetDefault('payload', '');

$data = json_decode($payload);
$status = "ERROR";

$db = new CDatabaseController();
$mysqli = $db->Connect();

$text = $mysqli->real_escape_string($data -> text);
$tag = $mysqli->real_escape_string($data -> tag);

if ($text && $tag) {
    $result = CNoteManager::addNote($db, $text, $tag);
    $status = empty($result) ? "OK" : $status;
}

$mysqli->close();

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