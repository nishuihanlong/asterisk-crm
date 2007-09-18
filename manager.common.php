<?php
/*******************************************************************************
* manager.common.php
* manager参数信息文件
* manaer parameter file
* 功能描述
* Function Desc

* Revision 0.0442  2007/09/14 08:55:00  modified by solo
* Desc: modify session scripts to be compatible with trixbox
* 描述: 改进了对session的处理以兼容trixbox2.0

* Revision 0.044  2007/09/10 16:25:00  modified by solo
* Desc: check user popedom 
* 描述: 增加了对管理权限的判断

* Revision 0.044  2007/09/10 15:25:00  modified by solo
* Desc: add some comments
* 描述: 增加了一些注释信息

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


define(LOG_ENABLED, 1); // Enable debuggin
define(FILE_LOG, "/tmp/xajaxDebug.log");  // File to debug.
define(ROWSXPAGE, 10); // Number of rows show it per page.
define(MAXROWSXPAGE, 25);  // Total number of rows show it when click on "Show All" button.
require_once ("include/xajax.inc.php");
require_once ('include/Localization.php');
//$_SESSION['curuser']['country'] = 'en';
//$_SESSION['curuser']['language'] = 'US';
$GLOBALS['locate']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'manager');

$xajax = new xajax("manager.server.php");
//print_r($_SESSION['curuser']['extensions']);

$xajax->registerFunction("showGrid");
$xajax->registerFunction("add");
$xajax->registerFunction("save");
$xajax->registerFunction("edit");
$xajax->registerFunction("update");
$xajax->registerFunction("delete");
$xajax->registerFunction("showStatus");
$xajax->registerFunction("init");
$xajax->registerFunction("preDialer");
?>
