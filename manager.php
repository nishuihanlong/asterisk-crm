<?php
/*******************************************************************************
* manager.php
* 管理员界面文件
* administrator interface
* 功能描述
	 提供帐户管理和系统状态查看的功能

* Function Desc
	account managment and extensions status monitor

* Page elements
* div:							
									panel
									formDiv			-> add/edit form div in xgrid
									grid				-> main div
									msgZone		-> message from xgrid class
* javascript function:		
									showStatus	
									showAccounts					 

* Revision 0.045  2007/09/24 17:55:00  last modified by solo
* Desc: add div to show channel infomation, addfunction showPredictiveDialer,showChannelsInfo
* 描述: 增加了显示channel信息的div

* Revision 0.044  2007/09/7 17:55:00  last modified by solo
* Desc: add some comments, and function showAccounts()
* 描述: 增加了一些注释信息, 增加了 showAccounts() 函数, 当进行分机管理的时候, 自动中止显示分机状态的函数
********************************************************************************/

require_once('manager.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<?php $xajax->printJavascript('include/'); ?>
		<LINK href="css/style.css" type=text/css rel=stylesheet>
		<meta http-equiv="Content-Language" content="utf-8" />
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			var timer;
			function showStatus(){
				xajax_showStatus();
				timer = setTimeout("showStatus()", 1000);
			}

			function showAccounts(){
				clearTimeout(timer);											//disable showStauts() function
				xajax_showGrid(0,<?=ROWSXPAGE?>,'','','');
			}

			function showChannelsInfo(){
				alert (xajax.$('msgChannelsInfo').value);
				xajax_showChannelsInfo();
			}

			function init(){
				xajax_init();
			}

			function showPredictiveDialer(){
				xajax_preDialer();
			}
		//-->
		</SCRIPT>
	</head>
	<body onload="init();">

		<div id="panelDiv" name="panelDiv" class="divPanel"></div>
		
		<div id="formDiv" name="formDiv" class="formDiv"></div>

		<div id="grid" name="grid" align="center"> </div>
		<div id="msgZone" name="msgZone" align="left"> </div>
		Channels
		<div id="channels" name="channels" align="left"> </div><br><br>
		Sip Channels
		<div id="sipChannels" name="sipChannels" align="left"> </div>
		<input type="hidden" value="" id="msgChannelsInfo" name="msgChannelsInfo">
	</body>
</html>
