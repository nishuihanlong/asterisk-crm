<?php


session_start();
require_once ("include/xajax.inc.php");

$xajax = new xajax("manager.server.php");

$xajax->registerFunction("showGrid");
$xajax->registerFunction("add");
$xajax->registerFunction("save");
$xajax->registerFunction("edit");
$xajax->registerFunction("update");
$xajax->registerFunction("delete");
?>