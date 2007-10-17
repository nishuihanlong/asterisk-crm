<?php
/*******************************************************************************
* account.php
* 账户管理界面文件
* account management interface
* 功能描述
	 提供帐户管理


* Revision 0.045  2007/10/17 17:55:00  last modified by solo
* Desc: created

********************************************************************************/

require_once('account.common.php');
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
				xajax_init();
			}

		//-->
		</SCRIPT>
	</head>
	<body onload="init();">
		<div id="divPanel" name="divPanel" class="divPanel"></div>
		
		<div id="formDiv" name="formDiv" class="formDiv"></div>

		<div id="grid" name="grid" align="center"> </div>
		<div id="msgZone" name="msgZone" align="left"> </div>
	</body>
</html>
