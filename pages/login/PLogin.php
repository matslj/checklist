<?php
// ===========================================================================================
//
// File: PLogin.php
//
// Description: Login page for the application. Simple login form; only supports
//              login, not creating of account or password change.
//              
//              On successful login the user will always be directed to index.php.
//              If the user tries to access index.php or any ajax service without
//              being logged in, the user will be redirected to this page.
//
// Author: Mats Ljungquist
//

require_once "../../template.php";

// Set simple page variables that are used by template.php
$shell['title'] = "Inloggning";
$shell['html_head'] = "";

$pagesPath = WS_SITELINK . "pages/login/";
$redirectTo = WS_SITELINK . "index.php";
$imageLink = WS_IMAGES;

// ========================================================================== //
// HTML BODY
// ========================================================================== //

// ****************************************
// ** Sätt headern i html body
ob_start();
?>
<h1>Checklistan</h1>
<?php
$shell['html_header'] = ob_get_contents();
ob_end_clean();
// ** Header i html body är nu satt
// *****************************************

ob_start();
?>

<div id='login'>
    <h1>Inlogging</h1>
    <form action='<?=$pagesPath . "PLoginProcess.php"?>' method="post">
        <input type='hidden' name='redirect' value='<?=$redirectTo?>'>
        <fieldset>
                <label for="nameUser">Användarnamn: <span class="ico"><img src="<?=$imageLink?>/user.png" alt="ikon användarnamn" border="0" /></span></label>
                <input id="nameUser" class="login" type="text" name="nameUser" required autofocus>
                <label for="passwordUser">Lösenord: <span class="ico"><img src="<?=$imageLink?>/pass.png" alt="ikon lösenord" border="0" /></span></label>
                <input id="passwordUser" class="password" type="password" name="passwordUser" required>
        </fieldset>
        <fieldset style='margin-top: 20px;'>
            <button type="submit" name="submit">&gt;&gt;&gt;&nbsp;&nbsp;Logga in</button>
        </fieldset>
    </form>
</div> <!-- #login -->

<?php
$shell['html_body'] = ob_get_contents();
ob_end_clean();

// ========================================================================== //
// DRAW SHELL
// ========================================================================== //

draw_shell();

?>