<?php
// ===========================================================================================
//
// File: config.php
//
// Description: Configuration file for the application. The moste important thing to set here
//              is the WS_SITELINK which should contain an url to your installation. In dev
//              mode this will probably be localhost.
//              
//              Nothing else has to be changed if you're not altering the structure of the
//              application.
//              
//              OBSERVE that there is another config file: sql/config.php which contains
//              the db connection data. This file has to be changed with the specifics
//              for your database.
//              
// Author: Mats Ljungquist
//

if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
	$url = "https://";
} else {
	$url = "http://";
}
// Append the host(domain name, ip) to the URL.
$url.= $_SERVER['HTTP_HOST'];

define('WS_CONTEXT_ROOT', 	'checklist');

define('WS_SITELINK',   $url . '/' . WS_CONTEXT_ROOT . '/');           // Link to site.
define('WS_CHARSET', 	'utf-8');
define('WS_IMAGES',	 WS_SITELINK . 'images/');                // Relative path to site image folder
define('WS_JAVASCRIPT',	 WS_SITELINK . 'js/');	                  // Relative path to site JavaScript code
define('WS_LOGGER', 'none');

define("TP_ROOT",	  dirname(__FILE__) . DIRECTORY_SEPARATOR);                                 // The root of installation
define("TP_SOURCEPATH",	  dirname(__FILE__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR);   // Classes, functions, code
define('TP_SQLPATH',	  dirname(__FILE__) . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR);   // SQL code
define("TP_PAGESPATH",	  dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR); // Pagecontrollers and modules

// Namnen på de installerade minifierade jquery och jquery-ui-filerna.
$shell['jquery'] = 'jquery/jquery-1.7.2.min.js';
$shell['jquery-ui'] = 'jquery-ui/jquery-ui-1.9.2.custom.min.js';
$shell['jquery-ui-css'] = 'jquery-ui/cupertino/jquery-ui-1.9.2.custom.min.css';
$shell['handlebars'] = 'handlebars/handlebars-v1.3.0.js'; // For templating. For more information see http://handlebarsjs.com/
$shell['slidepanel'] = 'slidepanel/js/jquery.slidepanel.js'; // For slide in panel. For more information see http://codebomber.com/jquery/slidepanel/
$shell['slidepanel-css'] = 'slidepanel/css/jquery.slidepanel.css';

//
// Enable autoload for classes. Using PEAR naming scheme for classes.
// E.G captcha_CCaptcha as classname.
//
function __autoload($class_name) {
    $path = str_replace('_', DIRECTORY_SEPARATOR, $class_name);
    require_once(TP_SOURCEPATH . "$path.php");
}
session_start();

?>