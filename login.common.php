<?php

require_once ("include/xajax.inc.php");
require_once ('include/Localization.php');

session_start();

if ($_SESSION['curuser']['country'] != '' )
	$GLOBALS['locate']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'login');
else
	$GLOBALS['locate']=new Localization('en','US','login');


$xajax = new xajax("login.server.php");
$xajax->registerFunction("processForm");
$xajax->registerFunction("init");
?>