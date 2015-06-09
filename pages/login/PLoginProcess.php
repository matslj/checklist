<?php

// ===========================================================================================
//
// PLoginProcess.php
//
// Authorizes or refuses login attempt.
// On successful login it creates a session and store userinfo in the session.
// Destroys the current session before a user is logged on.
//
// @author Mats Ljungquist

// Retrieve configuration
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . 'config.php');

$pagesPath = WS_SITELINK . "pages/login/";
$pc = CPageController::getInstance(FALSE);

// -------------------------------------------------------------------------------------------
//
// Take care of _GET/_POST variables. Store them in a variable (if they are set).
//
$user 		= $pc->POSTisSetOrSetDefault('nameUser', '');
$password 	= $pc->POSTisSetOrSetDefault('passwordUser', '');

$errorRedirect = $pagesPath . "PLogin.php";

// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database, call stored procedure.
//
$db 	= new CDatabaseController();
$mysqli = $db->Connect();

$spLogin = DBSP_AuthenticateUser;
$query = "CALL {$spLogin}('{$user}', '{$password}');";
$res = $db->MultiQuery($query);

// Use results
$results = Array();
$db->RetrieveAndStoreResultsFromMultiQuery($results);

$index = 0;
// Store inserted/updated article id
$row = $results[$index]->fetch_object();

// -------------------------------------------------------------------------------------------
//
// Use the results of the query to populate a session that shows we are logged in
//

// Must be one row in the resultset
if($results[$index]->num_rows === 1) {
        // Authentication / create was successfull.
        // - Destroy current session (logout user), if it exists, and create a new one.
        // - Store new user in session
        require_once(TP_SOURCEPATH . 'FDestroySession.php');
        // Recreate session
        session_start(); 		// Must call it since we destroyed it above.
        session_regenerate_id(); 	// To avoid problems
        // Store user
        // Get user-object
        $uo = CUserData::getInstance();
        $uo -> populateUserData($row->id, $row->account, $row->name, $row->email, $row->avatar, $row->groupid);
        // $log -> debug("id = " . $uo -> getId());
} else {
        $_SESSION['errorMessage']	= "Inloggning misslyckades - felaktigt användarnamn och/eller lösenord.";
        $_POST['redirect'] 		= $errorRedirect;
}


$results[$index]->close();
$mysqli->close();

// -------------------------------------------------------------------------------------------
//
// Redirect to the page set in the post property 'redirect' (which is set in the form in PLogin.php)
//
$pc->RedirectTo($pc->POSTisSetOrSetDefault('redirect'));
exit;

?>