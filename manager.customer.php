<?php
/*******************************************************************************
* manager.customer.php
* 客户信息管理界面
* cutomer information management interface
* 功能描述
	 提供客户信息管理的功能

* Function Desc
	customer management

* Page elements
* div:							
									panel
									formDiv			-> add/edit form div in xgrid
									grid				-> main div
									msgZone		-> message from xgrid class
* javascript function:		
									showStatus	
									showAccounts					 

* Revision 0.0442  2007/09/14 12:55:00  modified by solo
* Desc: create page
* 描述: 建立
********************************************************************************/

require_once('manager.customer.common.php');
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

	</body>
</html>
