<?php
// ===========================================================================================
//
// PLogoutProcess.php
//
// Log out by destroying the session. Redirects to loginpage after logout.
//

// Retrieve configuration
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . 'config.php');
$pc = CPageController::getInstance(FALSE);
require_once(TP_SOURCEPATH . 'FDestroySession.php');
$pc->RedirectTo(WS_SITELINK . "pages/login/PLogin.php");
exit;

?>