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

$pc = CPageController::getInstance(FALSE);
$old = $pc->POSTisSetOrSetDefault('old', '');
$new = $pc->POSTisSetOrSetDefault('new', '');

$db = new CDatabaseController();
$mysqli = $db->Connect();

$result = CNoteManager::changeTag($db, $mysqli, $old, $new);
$status = empty($result) ? "OK" : "ERROR";

$mysqli->close();

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