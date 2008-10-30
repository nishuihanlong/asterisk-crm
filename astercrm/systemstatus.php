<?php
/*******************************************************************************
* systemstatus.php
* 系统状态文件
* systerm status interface
* 功能描述
	 显示分机状态和正在进行的通话

* Function Desc


* javascript function:		
						showStatus				show sip extension status
						showChannelsInfo		show asterisk channels information
						init					initialize function after page loaded

* Revision 0.045  2007/10/18 17:55:00  last modified by solo
* Desc: page created
********************************************************************************/

require_once('systemstatus.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<?php $xajax->printJavascript('include/'); ?>
		<meta http-equiv="Content-Language" content="utf-8" />
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			var timerShowStatus,timerShowChannelsInfo;
			function showStatus(){
				xajax_showStatus();
				timerShowStatus = setTimeout("showStatus()", 1000);
			}

			function showChannelsInfo(){
				xajax_showChannelsInfo();
				timerShowChannelsInfo = setTimeout("showChannelsInfo()", 1000);
			}

			function init(){
				xajax_init();
				//xajax_listCommands();
				showStatus();
				showChannelsInfo();
			}

			function hangup(srcChan,dstChan){
				var callerChan = srcChan;
				var calleeChan = dstChan;
				xajax_hangup(callerChan);
				xajax_hangup(calleeChan);
			}

			function dial(phonenum,first){
				dialnum = phonenum;
				firststr = first;
				xajax_dial(dialnum,firststr);
			}

		//-->
		</SCRIPT>

		<script language="JavaScript" src="js/astercrm.js"></script>

	<LINK href="skin/default/css/dragresize.css" type=text/css rel=stylesheet>
	<LINK href="skin/default/css/style.css" type=text/css rel=stylesheet>
	<LINK href="skin/default/css/dialer.css" type="text/css" rel="stylesheet" />

	</head>
	<body onload="init();">
		<div id="divNav"></div>

		<!--
		<div id="divCommandList" name="divCommandList">
			<select id="sltCommandList" name="sltCommandList">
			</select>
			<input type="button" value="Execute" onclick="" id="btnExecuteCommand" name="btnExecuteCommand">
		</div>
		-->

		<div id="AMIStatudDiv" name="AMIStatudDiv"></div>
		<div id="divStatus" align="center"> </div>
		<div id="divActiveCalls" name="divActiveCalls" align="left"> </div>
		<div id="channels" align="left" class="groupsystem_channel"></div>
		<div id="divCopyright"></div>
	</body>
</html>