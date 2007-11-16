<?php
/*******************************************************************************
* export.common.php
* export参数信息文件
* export parameter file

* 功能描述

* Function Desc

registed function:
*	call these function by xajax_ + funcionname
*	such as xajax_init()

	init				init html page
	export

* Revision 0.045  2007/10/22 16:32:00  modified by solo
* Desc: page create
* 描述: 页面建立

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

$GLOBALS['locate']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'astercrm');

$xajax = new xajax("export.server.php");

$xajax->registerFunction("init");
$xajax->registerFunction("export");

define(ROWSXPAGE, 5); // Number of rows show it per page.
define(MAXROWSXPAGE, 25);  // Total number of rows show it when click on "Show All" button.
?>
