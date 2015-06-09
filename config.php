<?php

define('WS_SITELINK',   'http://localhost/checklist/');           // Link to site.
define('WS_CHARSET', 	'utf-8');
define('WS_IMAGES',	    WS_SITELINK . 'images/');                    // Relative path to site image folder
define('WS_JAVASCRIPT',	WS_SITELINK . 'js/');	                  // Relative path to site JavaScript code

define("TP_ROOT",	  dirname(__FILE__) . DIRECTORY_SEPARATOR);                                 // The root of installation
define("TP_SOURCEPATH",	  dirname(__FILE__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR);   // Classes, functions, code
define('TP_SQLPATH',	  dirname(__FILE__) . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR);   // SQL code
define("TP_PAGESPATH",	  dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR); // Pagecontrollers and modules

// Namnen på de installerade minifierade jquery och jquery-ui-filerna.
$shell['jquery'] = 'jquery/jquery-1.7.2.min.js';
$shell['jquery-ui'] = 'jquery-ui/jquery-ui-1.9.2.custom.min.js';
$shell['jquery-ui-css'] = 'jquery-ui/cupertino/jquery-ui-1.9.2.custom.min.css';
$shell['handlebars'] = 'handlebars/handlebars-v1.3.0.js'; // For templating. For more information see http://handlebarsjs.com/

//
// Enable autoload for classes. User PEAR naming scheme for classes.
// E.G captcha_CCaptcha as classname.
//
function __autoload($class_name) {
    $path = str_replace('_', DIRECTORY_SEPARATOR, $class_name);
    require_once(TP_SOURCEPATH . "$path.php");
}
session_start();

?>