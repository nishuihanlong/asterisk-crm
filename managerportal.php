<?php
/*******************************************************************************
* managerportal.php
* 管理员界面文件
* administrator interface
* 功能描述
	 提供各个管理功能的入口

* Function Desc
	provide an interface to enter different functions

* Revision 0.045  2007/10/17 20:40:00  
********************************************************************************/

require_once('managerportal.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<?php $xajax->printJavascript('include/'); ?>
		<LINK href="css/style.css" type=text/css rel=stylesheet>
		<meta http-equiv="Content-Language" content="utf-8" />
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			function init(){

			}
		//-->
		</SCRIPT>
	</head>
	<body onload="init();">
	<div id="divNav">
		<?include "portalinterface.php";?>
	</div>
	</body>
</html>
