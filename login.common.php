<?php

require_once ("include/xajax.inc.php");
require_once ('include/Localization.php');

session_start();

$_SESSION['curuser']['country'] = 'cn';
$_SESSION['curuser']['language'] = 'ZH';

$GLOBALS['locate']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'login');

$xajax = new xajax("login.server.php");
$xajax->registerFunction("processForm");
$xajax->registerFunction("init");
?>