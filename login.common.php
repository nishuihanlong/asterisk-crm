<?php
/*******************************************************************************
* login.common.php
* login参数信息文件
* login parameter file
* 功能描述
	根据用户的原则初始化SESSION, 初始化语言类, 默认使用 en_US
* Function Desc
	set language SESSION, initialize language class, use en_US by default

* Revision 0.044  2007/09/7 17:55:00  last modified by solo
* Desc: add some comments
* 描述: 增加了一些注释信息

********************************************************************************/

require_once ("include/xajax.inc.php");
require_once ('include/Localization.php');

session_start();

if (isset($_SESSION['curuser']['country']) )
	$GLOBALS['locate']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'login');
else
	$GLOBALS['locate']=new Localization('en','US','login');


$xajax = new xajax("login.server.php");
$xajax->registerFunction("processForm");	 //registe xajax_processForm
$xajax->registerFunction("init");				//registe xajax_init
?>