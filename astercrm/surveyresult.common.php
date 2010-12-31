<?php
/*******************************************************************************
* surveyresult.common.php
* survey
* survey result parameter file

* 功能描述
	检查用户权限
	初始化语言变量
	初始化xajax类
	预定义xajaxGrid中需要使用的一些参数

* Function Desc
	authority
	initialize localization class
	initialize xajax class
	define xajaxGrid parameters

registed function:
*	call these function by xajax_ + funcionname
*	such as xajax_init()


	init
	showGrid
	add
	save
	delete
	edit
	editField
	updateField
	showDetail
	setSurvey



* Revision 0.045  2007/10/18 15:14:00  modified by solo
* Desc: comment added

* Revision 0.045  2007/10/11 15:25:00  modified by solo
* Desc: page create
* 描述: 页面建立

********************************************************************************/

header('Content-Type: text/html; charset=utf-8');
header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: post-check=0, pre-check=0',false);
header('Pragma: no-cache');
session_cache_limiter('public, no-store');

session_set_cookie_params(0);
if (!session_id()) session_start();
setcookie('PHPSESSID', session_id());


if ($_SESSION['curuser']['usertype'] != 'admin' && $_SESSION['curuser']['usertype'] != 'groupadmin' && !is_array($_SESSION['curuser']['privileges']['surveyresult'])) 
	header("Location: portal.php");

require_once ("include/xajax.inc.php");
require_once ('include/localization.class.php');

$GLOBALS['locate']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'survey');

$xajax = new xajax("surveyresult.server.php");

$xajax->registerFunction("init");
$xajax->registerFunction("showGrid");
$xajax->registerFunction("add");
$xajax->registerFunction("save");
$xajax->registerFunction("delete");
$xajax->registerFunction("edit");
$xajax->registerFunction("editField");
$xajax->registerFunction("updateField");
$xajax->registerFunction("showDetail");
$xajax->registerFunction("setSurvey");
$xajax->registerFunction("searchFormSubmit");
$xajax->registerFunction("showCustomer");
$xajax->registerFunction("showContact");

define("ROWSXPAGE", 15); // Number of rows show it per page.
define("MAXROWSXPAGE", 30);  // Total number of rows show it when click on "Show All" button.

?>
