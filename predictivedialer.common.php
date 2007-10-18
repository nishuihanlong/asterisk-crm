<?php
/*******************************************************************************
* predictivedialer.common.php
* predictivedialer参数信息文件
* predictivedialer parameter file
* 功能描述
* Function Desc

* Revision 0.045  2007/10/18 15:25:00  modified by solo
* Desc: page created

********************************************************************************/

header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: post-check=0, pre-check=0',false);
header('Pragma: no-cache');
session_cache_limiter('public, no-store');

session_set_cookie_params(0);
if (!session_id()) session_start();
setcookie('PHPSESSID', session_id());


if ($_SESSION['curuser']['extension'] == '' or  $_SESSION['curuser']['usertype'] != 'admin') 
	header("Location: portal.php");


require_once ("include/xajax.inc.php");
require_once ('include/localization.class.php');

$GLOBALS['locate']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'predictivedialer');

$xajax = new xajax("predictivedialer.server.php");
$xajax->waitCursorOff();

$xajax->registerFunction("init");
$xajax->registerFunction("predictiveDialer");
$xajax->registerFunction("showPredictiveDialer");
$xajax->registerFunction("showChannelsInfo");

define(LOG_ENABLED, $config['system']['log_enabled']); // Enable debuggin
define(FILE_LOG, $config['system']['log_file_path']);  // File to debug.
define(ROWSXPAGE, 5); // Number of rows show it per page.
define(MAXROWSXPAGE, 25);  // Total number of rows show it when click on "Show All" button.
?>
