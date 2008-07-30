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
	电话挂断
	来电/去电弹屏


* Page elements

* div:							
				divNav				show management function list
				formDiv				show add/edit account form
				grid				show accout grid
				msgZone				show action result
				divCopyright		show copyright
				divMonitor			show monitor button
				divExtension		list extensions
				divPanel			list functions
				divUserMsg			show username and user extension
				divDialList			show if there're calls assigned to the agent]
				divCrm				show 3rd party crm if user dont use internal crm
				myevents			show system status
				click2dial			show input box allow agent enter phone number to dial
				...

* span:
				spanTransfer		show transfer option list when call link
				spanMonitor			show monitor description
				spanMonitorStatus	show system monitor status
				...

* hidden:
				extensionStatus			extension status: idle | link | hangup
				username
				exenstion
				uniqueid				uniqueid if there's a call
				callerid
				mycallerid				store callerid
				curid					current id in events table
				callerChannel
				calleeChannel
				direction				dialout or dialin
				popup					if "yes" then pop-up when there's a call

* javascript function:		

				init							page onload function
				monitor							start/stop monitor
				dial							dial a phone
				showProcessingMessage
				hideProcessingMessage
				btnGetAPhoneNumberOnClick
				updateEvents					check database for asterisk events


* Revision 0.0456  2007/1/16 14:16:00  last modified by solo
* Desc: when there's aleady a call, dial and invite function would be disabled

* Revision 0.0456  2007/10/31 9:46:00  last modified by solo
* Desc: add divHangup

* Revision 0.0456  2007/10/29 21:31:00  last modified by solo
* Desc: add div divSearchContact


* Revision 0.045  2007/10/19 15:05:00  last modified by solo
* Desc: make the following div draggable:
			formDiv
			formCustomerInfo
			formContactInfo
			formNoteInfo
			formEditInfo

* Revision 0.045  2007/10/18 15:05:00  last modified by solo
* Desc: comment added

********************************************************************************/
require_once('portal.common.php');
require_once('config.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8">
		<?php $xajax->printJavascript('include/'); ?>

	<script type="text/javascript" src="js/dragresize.js"></script>
	<script type="text/javascript" src="js/dragresizeInit.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script language="JavaScript" src="js/dhtmlgoodies_calendar.js"></script>
	<LINK href="js/dhtmlgoodies_calendar.css" type=text/css rel=stylesheet>
	<LINK href="skin/default/css/dragresize.css" type=text/css rel=stylesheet>
	<LINK href="skin/default/css/style.css" type=text/css rel=stylesheet>


		<script type="text/javascript">
		var intervalID = 0; //for stop setInterval of autoDial
		function dial(phonenum,first){
			if (document.getElementById("uniqueid").value != '')
				return false;
			xajax.$("divMsg").innerHTML = xajax.$('dialtip').value+" "+phonenum;
			xajax_dial(phonenum,first);
		}

		function hangup(){
			//alert (xajax.$('callerChannel').value);
			//alert (xajax.$('calleeChannel').value);
			xajax.$("divMsg").innerHTML = "Hangup";
			xajax_hangup(xajax.$('callerChannel').value);
			xajax_hangup(xajax.$('calleeChannel').value);
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
				// dont pop new window when there already a window exsits
				if (xajax.$('formDiv') != null){
					if (xajax.$('formDiv').style.visibility == 'visible')
						xajax.$('popup').value = 'no';
					else
						xajax.$('popup').value = 'yes';
				}else{
					xajax.$('popup').value = 'yes';
				}
		}

		function monitor(){
			if (xajax.$('callerChannel').value.indexOf("Local") < 0 )
				channel = xajax.$('callerChannel').value;
			else
				channel = xajax.$('calleeChannel').value;
			callerid = xajax.$('callerid').value;
			if (xajax.$('btnMonitorStatus').value == 'recording')
				xajax_monitor(channel,callerid,'stop');
			else
				xajax_monitor(channel,callerid,'start',document.getElementById("uniqueid").value);

			return false;
		}

		function init(){
			xajax_init();
			updateEvents();

			//make div draggable
			dragresize.apply(document);
//			xajax.loadingFunction = showProcessingMessage;
//			xajax.doneLoadingFunction = hideProcessingMessage;
		}
		
		function invite(){
			if (document.getElementById("uniqueid").value != '')
				return false;
			src = trim(xajax.$('iptSrcNumber').value);
			dest = trim(xajax.$('iptDestNumber').value);
			
			if (src == '' && dest == '')
				return false;
			if (src == ''){
				xajax.$('iptSrcNumber').value = xajax.$('extension').value;
				src = xajax.$('extension').value;
				xajax.$("divMsg").innerHTML = xajax.$('dialtip').value+" "+src;
			}else xajax.$("divMsg").innerHTML = xajax.$('dialtip').value+" "+src;

			if (dest == ''){
				xajax.$('iptDestNumber').value = xajax.$('extension').value;
				dest = xajax.$('extension').value;
			}

			xajax_invite(src,dest);
		}
		
		function transfer(){
			xajax.$("divMsg").innerHTML = xajax.$('trantip').value+" "+xajax.$("sltExten").value;
			xajax_transfer(xajax.getFormValues('myForm'));
		}

		function trim(stringToTrim) {
			return stringToTrim.replace(/^\s+|\s+$/g,"");
		}

		function setCampaign(){
			groupid = document.getElementById("groupid").value;
			if (groupid == '')
				return;
			//清空campaignid
			document.getElementById("campaignid").options.length=0
			xajax_setCampaign(groupid);
		}
		function workctrl(aciton){
			if (aciton == 'stop'){
				xajax_showWorkoff();
			}else{
				xajax.$("divWork").innerHTML = 'dialing';
				xajax_workstart();
			}
		}
		function autoDial(interval){
			if(interval == '') return false;
			xajax.$("divWork").innerHTML = interval;
			intervalID=setInterval("showsec(xajax.$('divWork').innerHTML)",1000);
		}
		function showsec(i)
		{	
			if(xajax.$('btnWorkStatus').value == '') {
				clearInterval(intervalID);
				xajax.$("divWork").innerHTML = '';				
				return false;
			}
			if(i == 0){
				xajax.$("divWork").innerHTML = 'dialing';
				clearInterval(intervalID);
				workctrl('start');
			}else{
				xajax.$("divWork").innerHTML = i-1;
			}
		}
		</script>
<?
if ($config['system']['enable_external_crm'] == false && $config['google-map']['key'] != ''){
?>
	<script src="http://maps.google.com/maps?file=api&v=2&key=<?echo $config['google-map']['key'];?>" type="text/javascript"></script>
<?
}
?>
	</head>
	<body onload="init();" style="PADDING-RIGHT: 20px;PADDING-LEFT: 20px;">
	<form name="myForm" id="myForm">
		<div id="divUserMsg" name="divUserMsg"></div><br>

		<div id="divHangup" name="divHangup">
			<input type="button" value="Hangup" name="btnHangup" id="btnHangup" onclick="hangup();" disabled="true">
			<div id="divTrunkinfo" name="divTrunkinfo"></div>
		</div><br>

		<span id="spanTransfer" name="spanTransfer">
			<SELECT id="sltExten" name="sltExten">
			</SELECT>
			<INPUT TYPE="text" name="iptTtansfer" id="iptTtansfer" size="15">
			<INPUT type="BUTTON" value="Transfer" id="btnTransfer" onclick="transfer();">
		</span>
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
		<input type="hidden" name="callerChannel" id="callerChannel" value=""/>
		<input type="hidden" name="calleeChannel" id="calleeChannel" value=""/>
		<input type="hidden" name="direction" id="direction" value=""/>
		<input type="hidden" name="popup" id="popup" value="yes"/>
		<input type="hidden" name="dialtip" id="dialtip" value="Dialing to"/>
		<input type="hidden" name="trantip" id="trantip" value="Transfering to"/>		
		<input type='hidden' value="" name="btnWorkStatus" id="btnWorkStatus">
	</form>
	<input type="hidden" name="mycallerid" id="mycallerid" value=""/>
	<br>
	<div id="divDialList" name="divDialList"></div><br/>
	<div id="processingMessage" name="processingMessage"></div>
	<div id="misson" name="misson"><input type="button" id="btnWork" name="btnWork" value=""></div><div id="divWork" name="divWork" align="left" style="font-weight:bold;
	"></div><br>
	<div id="divInvite"><input type="text" value="" name="iptSrcNumber" id="iptSrcNumber">&nbsp;->&nbsp;<SELECT id="iptDestNumber" name="iptDestNumber"></SELECT>&nbsp;<input type="button" id="btnDial" name="btnDial" value="Dial" onclick="invite();"></div><br/>
	
		<br/>
		<div id="divSearchContact" name="divSearchContact" class="divSearchContact">
			<input type="text" value="" name="iptCallerid" id="iptCallerid">&nbsp;<input type="button" id="btnSearchContact" name="btnSearchContact" value="" onclick="xajax_getContact(xajax.$('iptCallerid').value)">
		</div>
		<div id="divMsg" name="divMsg" align="center" style="font-weight:bold;
"></div>
		<table width="100%" border="0" style="background: #F9F9F9; padding: 0px;">
			<tr>
				<td style="padding: 0px;">
					<fieldset>
		<div id="formDiv"  class="formDiv drsElement" 
			style="left: 450px; top: 50px;"></div>			
		<div id="formCustomerInfo" class="formDiv drsElement"
			style="left: 20px; top: 50px;width: 600px"></div>
		<div id="formContactInfo" class="formDiv drsElement"
			style="left: 20px; top: 330px;"></div>
		<div id="formCdr" class="formDiv drsElement"
			style="left: 20px; top: 330px; width: 800px"></div>
		<div id="formRecentCdr" class="formDiv drsElement"
			style="left: 20px; top: 50px; width: 400px"></div>		
		<div id="formRecords" class="formDiv drsElement"
			style="left: 20px; top: 330px; width: 800px"></div>
		<div id="formDiallist" class="formDiv drsElement"
			style="left: 20px; top: 330px; width: 800px"></div>
		<div id="formaddDiallistInfo"  class="formDiv drsElement" 
			style="left: 450px; top: 50px;"></div>
		<div id="formeditDiallistInfo"  class="formDiv drsElement" 
			style="left: 450px; top: 50px;"></div>
		<div id="formNoteInfo" class="formDiv  drsElement"
			style="left: 450px; top: 330px;"></div>
		<div id="formWorkoff" class="formDiv  drsElement"
			style="left: 450px; top: 330px;"></div>
		<div id="formEditInfo" class="formDiv drsElement"
			style="left: 450px; top: 50px;"></div>
		<div id="formplaymonitor"  class="formDiv drsElement" 
			style="left: 450px; top: 50px;width: 350px"></div>
		<div id="grid" align="center"></div>
		<div id="msgZone" name="msgZone" align="left"> </div>
					</fieldset>
				</td>
			</tr>
		</table>
	<div id="divCrm" name="divCrm"></div>
	<div id="divPanel" name="divPanel" class="divPanel"></div>

	<div id="divExtension" name="divExtension" 
		class="divExtension drsElement drsMoveHandle" 
		style="left: 750px; top: 20px;	width: 160px;
				position: absolute; 
				z-index:0;
				text-align: center; 
				border: 1px dashed #EAEAEA;    
				color:#006600; "></div>

	<div id="divMap" class="drsElement" 
		style="left: 450px; top: 20px;	width: 300px;height: 340px;
					position: absolute; 
					z-index:0;
					text-align: center; 
					border: 1px dashed #EAEAEA;    
					color:#006600;
					visibility:hidden;">
		<table width="100%" border="1" align="center" class="adminlist" >
			<tr class="drsMoveHandle">
				<th align="right" valign="center" >
					<img src="skin/default/images/close.png" onClick='javascript: document.getElementById("divMap").style.visibility="hidden";return false;' title="Close Window" style="cursor: pointer; height: 16px;">
				</th>
			</tr>
			<tr>
				<td>
					<fieldset><legend>Map</legend>
					<div id="map" style="width: 300px;height: 300px;"></div>
					</fieldset>
				</td>
			</tr>
		</table>
	</div>
	<div id="divCopyright"></div>
	</body>
</html>