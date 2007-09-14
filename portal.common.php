<?php
/*******************************************************************************
* portal.common.php
* portal参数信息文件
* portal parameter file
* 功能描述
* Function Desc

* Revision 0.0442  2007/09/14 08:55:00  modified by solo
* Desc: modify session scripts to be compatible with trixbox
* 描述: 改进了对session的处理以兼容trixbox2.0

* Revision 0.0441  2007/09/14 08:55:00  modified by solo
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


if ($_SESSION['curuser']['extension'] == '') 
	header("Location: login.php");
if (!isset($_SESSION['curid']) && $_SESSION['curid'] =='' ) $_SESSION['curid']=0;

require_once ("include/xajax.inc.php");
require_once ('include/Localization.php');

define(LOG_ENABLED, 1); // Enable debuggin
define(FILE_LOG, "/tmp/xajaxDebug.log");  // File to debug.
define(ROWSXPAGE, 5); // Number of rows show it per page.
define(MAXROWSXPAGE, 25);  // Total number of rows show it when click on "Show All" button.

//echo "ok";
//echo $_SESSION['curuser']['country'];
//print_r($_SESSION['curuser']);
//exit();
//$_SESSION['curuser']['country'] = 'cn';
//$_SESSION['curuser']['language'] = 'ZH';

//echo $_SESSION['curuser']['country'];
//echo $_SESSION['curuser']['language'];
//exit;
$GLOBALS['locate']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'portal');


$xajax = new xajax("portal.server.php");
//$xajax->debugOn();
$xajax->waitCursorOff();
$xajax->registerFunction("myEvents");
$xajax->registerFunction("listenCalls");
$xajax->registerFunction("dial");
$xajax->registerFunction("transfer");
$xajax->registerFunction("init");

if ($config['system']['enable_external_crm'] == false){
	//crm function
	$xajax->registerFunction("showGrid");
	$xajax->registerFunction("add");
	$xajax->registerFunction("edit");
	$xajax->registerFunction("delete");
	$xajax->registerFunction("save");
	$xajax->registerFunction("update");
	$xajax->registerFunction("editField");
	$xajax->registerFunction("updateField");
	$xajax->registerFunction("confirmCustomer");
	$xajax->registerFunction("confirmContact");
	$xajax->registerFunction("showCustomer");
	$xajax->registerFunction("showContact");
	$xajax->registerFunction("showNote");
}
//$xajax->processRequests();

//$xajax->processRequests();
?>