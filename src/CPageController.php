<?php
// ===========================================================================================
//
// Class CPagecontroller
//
// Nice to have utility for common methods useful in most pagecontrollers.
//
class CPageController {
    
    private static $instance;

    // ------------------------------------------------------------------------------------
    //
    // Internal variables
    //
    public $lang = Array();


    // ------------------------------------------------------------------------------------
    //
    // Constructor
    // @param historize if true -> mark page in history
    //        BEWARE of multiple constructs of CPageController if trying to disable history (solved - singelton).
    private function __construct($historize) {
        if ($historize === TRUE) {
            $_SESSION['history2'] = self::SESSIONisSetOrSetDefault('history1', 'home');
            $_SESSION['history1'] = self::CurrentURL();
            // print_r($_SESSION);
        }
        // print_r($_SESSION);
    }

    // ------------------------------------------------------------------------------------
    //
    // Destructor
    //
    public function __destruct() {
            ;
    }

    public static function getInstance($historize = TRUE) {
        if (empty(self::$instance)) {
            self::$instance = new self($historize);
        }
        return self::$instance;
    }

    // ------------------------------------------------------------------------------------
    //
    // Check if corresponding $_GET[''] is set, then use it or return the default value.
    //
    public static function GETisSetOrSetDefault($aEntry, $aDefault = '') {

            return isset($_GET["$aEntry"]) && !empty($_GET["$aEntry"]) ? $_GET["$aEntry"] : $aDefault;
    }

    // ------------------------------------------------------------------------------------
    //
    // Check if corresponding $_POST[''] is set, then use it or return the default value.
    //
    public static function POSTisSetOrSetDefault($aEntry, $aDefault = '') {

            return isset($_POST["$aEntry"]) && !empty($_POST["$aEntry"]) ? $_POST["$aEntry"] : $aDefault;
    }

    // ------------------------------------------------------------------------------------
    //
    // Check if corresponding $_POST[''] is set, then use it or return the default value.
    //
    public static function REQUESTisSetOrSetDefault($aEntry, $aDefault = '') {

            return isset($_REQUEST["$aEntry"]) && !empty($_REQUEST["$aEntry"]) ? $_REQUEST["$aEntry"] : $aDefault;
    }

    // ------------------------------------------------------------------------------------
    //
    // Check if corresponding $_SESSION[''] is set, then use it or return the default value.
    //
    public static function SESSIONisSetOrSetDefault($aEntry, $aDefault = '') {

            return isset($_SESSION["$aEntry"]) && !empty($_SESSION["$aEntry"]) ? $_SESSION["$aEntry"] : $aDefault;
    }
    
    public static function VariableIsSetOrSetDefault($aEntry, $aDefault = '') {

            return isset($aEntry) && !empty($aEntry) ? $aEntry : $aDefault;
    }

    // ------------------------------------------------------------------------------------
    //
    // Sets a session attribute and return the value.
    //
    public static function SESSIONSet($aEntry, $value) {
        $_SESSION[$aEntry] = $value;
        return $value;
    }

    // ------------------------------------------------------------------------------------
    //
    // Check if the value is numeric and optional in the range.
    //
    public static function IsNumericOrDie($aVar, $aRangeLow = '', $aRangeHigh = "") {

        $inRangeH = empty($aRangeHigh) ? TRUE : ($aVar <= $aRangeHigh);
        $inRangeL = empty($aRangeLow)  ? TRUE : ($aVar >= $aRangeLow);
        if(!(is_numeric($aVar) && $inRangeH && $inRangeL)) {
                die(sprintf("The variable value '$s' is not numeric or it is out of range.", $aVar));
        }
    }

    // ------------------------------------------------------------------------------------
    //
    // Check if the value is a string.
    //
    public static function IsStringOrDie($aVar) {

        if(!is_string($aVar)) {
                die(sprintf("The variable value '$s' is not a string.", $aVar));
        }
    }

    // ------------------------------------------------------------------------------------
    //
    // Static function
    // Create a URL to the current page.
    //
    public static function CurrentURL() {

        // Create link to current page
        $refToThisPage = "http";
        $refToThisPage .= (@$_SERVER["HTTPS"] == "on") ? 's' : '';
        $refToThisPage .= "://";
        $serverPort = ($_SERVER["SERVER_PORT"] == "80") ? '' : ":{$_SERVER['SERVER_PORT']}";
        $refToThisPage .= $serverPort . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

        return $refToThisPage;
    }
    
    public static function RedirectTo($aUri) {
        if (empty($aUri)) {
            $aUri = WS_SITELINK . "PLogin.php";
        }

        header("Location: {$aUri}");
        exit;
    }
        
} // End of Of Class

?>