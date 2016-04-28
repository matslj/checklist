<?php
/*
 * Templatefil för applikationen. Definierar grundutseendet/strukturen.
 * shell och base är tänkta att användas som globala variabler genom hela applikationen.
 * shell är en associativ array.
 * 
 * @author Mats Ljungquist
 */
$shell = array();
$base = '';

// Retrieve configuration
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php');

function getErrorMessage() {
    $html = "";

    if(isset($_SESSION['errorMessage'])) {
        $html = <<<EOD
        <div class="ui-widget">
            <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
                <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
                <strong>Fel:</strong> {$_SESSION['errorMessage']}</p>
            </div>
        </div>

EOD;
        unset($_SESSION['errorMessage']);
    }

    return $html;
}

function draw_shell() {
    global $shell, $base;
    
    $javascript = WS_JAVASCRIPT;
    $site = WS_SITELINK;
    $logoutProcess = WS_SITELINK . "pages/login/PLogoutProcess.php";
    $logoutLink = "";
    
    $errorMsg = getErrorMessage();
    
    if(isset($_SESSION['accountUser'])) {
        $logoutLink .= <<<EOD
        Inloggad som: <span id="user">{$_SESSION['accountUser']}</span>
        <a href='{$logoutProcess}'>Logga ut</a>
EOD;
    }
    
    ?><!DOCTYPE html>
    <html lang="sv">
        <head>
            <meta http-equiv="content-type" content="text/html; charset=utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>
                <?= $shell['title'] ?>
            </title>
            <?php
            if ( $shell['jquery'] ) {
            ?><script type="text/javascript" src="<?=$javascript . $shell['jquery'] ?>"></script><?php
            }
            ?>
            <?php
            if ( $shell['jquery-ui'] ) {
            ?>
            <script type="text/javascript" src="<?=$javascript . $shell['jquery-ui'] ?>"></script>
            <link rel="stylesheet" type="text/css" href="<?=$javascript . $shell['jquery-ui-css'] ?>">
            <?php
            }
            ?>
            <?php
            if ( $shell['handlebars'] ) {
            ?><script type="text/javascript" src="<?=$javascript . $shell['handlebars'] ?>"></script><?php
            }
            ?>
            <?php
            if ( $shell['slidepanel'] ) {
            ?>
            <script type="text/javascript" src="<?=$javascript . $shell['slidepanel'] ?>"></script>
            <link rel="stylesheet" type="text/css" href="<?=$javascript . $shell['slidepanel-css'] ?>">
            <?php
            }
            ?>
            <link rel="stylesheet" type="text/css" href="<?= $site ?>style/index.css">

            <?= empty($shell['html_head']) ? "" : $shell['html_head'] ?>
        </head>
        <body>
            <div id="header">
                <div id="logoutLink">
                <?=$logoutLink?>
                </div>
                <?= empty($shell['html_header']) ? "" : $shell['html_header'] ?>
                
            </div>
            <div id="wrapper">
                <div style="clear: both;" id="page">
                    <div id="content" style="position: relative;">
                        <?=$errorMsg?>
                        <?= $shell['html_body'] ?>
                    </div>
                    
                </div>
            </div>
            <div id="footer">
                <p>
                    Mats Ljungquist, 2015
                </p>
            </div>
        </body>
    </html>
<?php
} // end draw_shell() function

//$templateVisited = true;
?>