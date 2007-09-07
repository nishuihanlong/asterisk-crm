<?php


session_start();
define(LOG_ENABLED, 1); // Enable debuggin
define(FILE_LOG, "/tmp/xajaxDebug.log");  // File to debug.
define(ROWSXPAGE, 10); // Number of rows show it per page.
define(MAXROWSXPAGE, 25);  // Total number of rows show it when click on "Show All" button.
require_once ("include/xajax.inc.php");
require_once ('include/Localization.php');
$_SESSION['curuser']['country'] = 'en';
$_SESSION['curuser']['language'] = 'US';
$GLOBALS['locate']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'manager');

$xajax = new xajax("manager.server.php");

$xajax->registerFunction("showGrid");
$xajax->registerFunction("add");
$xajax->registerFunction("save");
$xajax->registerFunction("edit");
$xajax->registerFunction("update");
$xajax->registerFunction("delete");
$xajax->registerFunction("showStatus");
?>
