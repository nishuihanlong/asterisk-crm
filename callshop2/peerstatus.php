<?php
/*******************************************************************************
* peerstatus.php
* 系统状态文件
* systerm status interface
* 功能描述
	 显示分机状态和正在进行的通话

TO-DO

1.增加 print invoice 的button
2.asterrc 增加一级计费引擎
3.每个booth可以自定义名称
4.callshop 可以显示自己的信息

* Function Desc


* javascript function:		
						showStatus				show sip extension status
						showChannelsInfo		show asterisk channels information
						init					initialize function after page loaded

* Revision 0.045  2007/10/18 17:55:00  last modified by solo
* Desc: page created
********************************************************************************/

require_once('peerstatus.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<?php $xajax->printJavascript('include/'); ?>
		<meta http-equiv="Content-Language" content="utf-8" />
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			function init(){
				xajax_init();
				showStatus();
				dragresize.apply(document);
			}
			function showCallshopStatus(){
				var myDiv = document.getElementById("divAmount");
				if (myDiv.style.display == 'block')
					myDiv.style.display = 'none';
				else
					myDiv.style.display = 'block';
				return false;
			}
		//-->
		</SCRIPT>
		<script language="JavaScript" src="js/astercrm.js"></script>
		<LINK href="skin/default/css/layout.css" type=text/css rel=stylesheet>
		<LINK href="skin/default/css/dragresize.css" type=text/css rel=stylesheet>

		<script type="text/javascript" src="js/dragresize.js"></script>
		<script type="text/javascript" src="js/dragresizeInit.js"></script>

	</head>
	<body onload="init();">
		<div id="divNav"></div>
		<div id="AMIStatudDiv" name="AMIStatudDiv"></div>
		<form method="post" id="peerStatus">
			<div class="container" id="divMainContainer">
			</div>
		</form>

		<input type="hidden" name="curid" id="curid" value="0"/>
		<div id="divCopyright"></div>
	</body>
</html>
