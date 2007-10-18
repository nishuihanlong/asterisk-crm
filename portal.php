<?php
/*******************************************************************************
* portal.php

* 座席界面文件
* agent portal interface

* Function Desc
	provide agent interface

* 功能描述
	点击呼叫
	主动呼叫
	电话转接
	来电/去电弹屏

* Page elements

* div:							
				divNav				show management function list
				formDiv				show add/edit account form
				grid				show accout grid
				msgZone				show action result
				divCopyright		show copyright
				userMsg				show username and user extension
				divMonitor			show monitor button
				myevents			show system status
				click2dial			show input box allow agent enter phone number to dial
				extensionDiv		list extensions
				...

* span:
				transfer			show transfer option list
				spanMonitor			show monitor description
				spanMonitorStatus	show system monitor status
				...

* input:
				extensionStatus			extension status: idle | link | hangup

* javascript function:		

				init				page onload function
				monitor
				dial
				showProcessingMessage
				hideProcessingMessage
				btnGetAPhoneNumberOnClick
				updateEvents


* Revision 0.045  2007/10/18 15:05:00  last modified by solo
* Desc: comment added

********************************************************************************/
require_once('portal.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8">
		<?php $xajax->printJavascript('include/'); ?>
		<script type="text/javascript">
		function init(){
			xajax_init();
			updateEvents();
//			xajax.loadingFunction = showProcessingMessage;
//			xajax.doneLoadingFunction = hideProcessingMessage;
		}

		function dial(phonenum){
			xajax_dial(phonenum);
		}

		function showProcessingMessage(){
			xajax.$('processingMessage').style.display='block';
		}

		function hideProcessingMessage(){
			xajax.$('processingMessage').style.display = 'none';
		}
		
		function btnGetAPhoneNumberOnClick(){
			xajax_addWithPhoneNumber();
		}

		function updateEvents(){

			myFormValue = xajax.getFormValues("myForm");
			xajax_listenCalls(myFormValue);
				if (xajax.$('formDiv') != null){
					if (xajax.$('formDiv').style.visibility == 'visible')
						xajax.$('popup').value = 'no';
					else
						xajax.$('popup').value = 'yes';
				}else{
					xajax.$('popup').value = 'yes';
				}
			setTimeout("updateEvents()", 1000);
		}

		function monitor(){
			if (xajax.$('callerChannel').value.indexOf("Local") < 0 )
				channel = xajax.$('callerChannel').value;
			else
				channel = xajax.$('calleeChannel').value;

			if (xajax.$('btnMonitorStatus').value == 'recording')
				xajax_monitor(channel,'stop');
			else
				xajax_monitor(channel,'start');

			return false;
		}
		
		</script>

	<LINK href="css/style.css" type=text/css rel=stylesheet>
	<meta http-equiv="Content-Language" content="utf-8" />
	</head>
	<body onload="init();">
	<form name="myForm" id="myForm">
		<div id="userMsg" name="userMsg"></div>
		<span id="transfer" name="transfer"></span>
		<div id="myevents"></div>
		<br>

		<span id="spanMonitor" name="spanMonitor"></span><br>
		<div id="divMonitor">
			<span id="spanMonitorStatus" name="spanMonitorStatus"></span><br>
			<input type='button' value='' name="btnMonitor" id="btnMonitor" onclick="monitor();return false;">
			<input type='hidden' value='' name="btnMonitorStatus" id="btnMonitorStatus">
			<input type='checkbox' name='chkMonitor' id="chkMonitor">
			<span id="spanMonitorSetting" name="spanMonitorSetting"></span>
		</div>

		<input type="hidden" name="extensionStatus" id="extensionStatus" value=""/>
		<input type="hidden" name="username" id="username" value=""/>
		<input type="hidden" name="extension" id="extension" value=""/>
		<input type="hidden" name="uniqueid" id="uniqueid" value=""/>
		<input type="hidden" name="callerid" id="callerid" value=""/>
		<input type="hidden" name="curid" id="curid" value="0"/>
		<input type="hidden" name="extension" id="extension" value=""/>
		<input type="hidden" name="callerChannel" id="callerChannel" value=""/>
		<input type="hidden" name="calleeChannel" id="calleeChannel" value=""/>
		<input type="hidden" name="direction" id="direction" value=""/>
		<input type="hidden" name="popup" id="popup" value="yes"/>
		<div id="debug"></div>
	</form>
	<input type="hidden" name="mycallerid" id="mycallerid" value=""/>
	<br>
	<div id="divDialList" name="divDialList"></div>
	<div id="processingMessage" name="processingMessage"></div>
	<div id="click2dial"><input type="text" value="" name="iptDestnationNumber" id="iptDestnationNumber"><input type="button" id="btnDial" name="btnDial" value="Dial" onclick="dial(xajax.$('iptDestnationNumber').value)"></div>
	
	<div id="crm" name="crm"></div>
	<div id="panelDiv" name="panelDiv" class="divPanel"></div>
	<div id="extensionDiv" name="extensionDiv" class="divExtension"></div>
	<div id="divCopyright"></div>
	</body>
</html>
