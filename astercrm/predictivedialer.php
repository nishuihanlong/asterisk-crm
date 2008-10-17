<?php
/*******************************************************************************
* predictivedialer.php
* 预拨号器界面文件
* predictivedialer interface

* Function Desc
		拨号器控制: 开始/停止
		最大通道控制

* div
				divNav
				divAMIStatus					show error message if AMI is error
				divActiveCalls					show active calls number
				divPredictiveDialerMsg			
				divPredictiveDialer				show predictive dialer
				channels						show asterisk channels
				divCopyright
* span
				spanTotalRecords				records in diallist

* hidden
				predictiveDialerStatus			dialer status: idle | busy


* javascript functions

				init
				showChannelsInfo
				showPredictiveDialer
				btnDialOnClick
				startDial
				stopDial
				trim
				isNumber

* Revision 0.045  2007/10/18 20:12:00  last modified by solo
* Desc: change div id from AMIStatusDiv to divAMIStatus 

* Revision 0.045  2007/10/18 17:55:00  last modified by solo
* Desc: page created
********************************************************************************/

require_once('predictivedialer.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<?php $xajax->printJavascript('include/'); ?>
		<meta http-equiv="Content-Language" content="utf-8" />
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			var timerShowChannelsInfo,timerPredictiveDialer;

			function showChannelsInfo(){
				xajax_showChannelsInfo();
				timerShowChannelsInfo = setTimeout("showChannelsInfo()", 1000);
			}

			function init(){
				xajax_init();
				showPredictiveDialer();
				showChannelsInfo();
			}

			function showPredictiveDialer(){
				xajax_showPredictiveDialer(xajax.$('predictiveDialerStatus').value,xajax.$('groupid').value,xajax.$('campaignid').value);
			}

			function btnDialOnClick(){
				if (xajax.$('predictiveDialerStatus').value == 'idle'){
//					showChannelsInfo();
					startDial();
				}else{
					stopDial();
				}
			}

			function startDial(){

				maxActiveCalls = xajax.$('fldMaxActiveCalls').value;
				if (!isNumber(maxActiveCalls)){
					alert(xajax.$('btnNumberOnlyMsg').value);
					stopDial();
					return;
				}
					
				xajax.$('btnDial').value = xajax.$('btnStopMsg').value;

				totalRecordsHTML = trim(xajax.$('spanTotalRecords').innerHTML);
				if (totalRecordsHTML == '')
					totalRecords = -1;
				else{
					totalRecordsHTMLArray = totalRecordsHTML.split(" ");
					totalRecords = totalRecordsHTMLArray[0];
				}
				var groupid = document.getElementById('groupid').value;
				var campaignid = document.getElementById('campaignid').value;
				xajax_predictiveDialer(maxActiveCalls,totalRecords,groupid,campaignid);
				xajax.$('predictiveDialerStatus').value = "dialing";
				timerPredictiveDialer = setTimeout("startDial()", 2000);

			}
			
			function stopDial(){
				clearTimeout(timerPredictiveDialer);
				xajax.$('predictiveDialerStatus').value = "idle";
				xajax.$('divPredictiveDialerMsg').innerHTML = xajax.$('btnDialerStoppedMsg').value;
				xajax.$('btnDial').value = xajax.$('btnDialMsg').value;	
			}

			function trim(stringToTrim) {
				return stringToTrim.replace(/^\s+|\s+$/g,"");
			}

		function  addOption(objId,optionVal,optionText)  {
			objSelect = document.getElementById(objId);
			var _o = document.createElement("OPTION");
			_o.text = optionText;
			_o.value = optionVal;
			objSelect.options.add(_o);
		} 
	
		   function isNumber(oNum){
				if(!oNum) return false;
				var strP=/^\d+(\.\d+)?$/;
				if(!strP.test(oNum)) return false;
				try{
					if(parseFloat(oNum)!=oNum) return false;
				}
				catch(ex)
				{
					return false;
				}
				return true;
			}

			function setCampaign(){
				var groupid = xajax.$('groupid').value;
				if (groupid == '')
					return;
				//清空campaignid
				document.getElementById("campaignid").options.length = 0;
				if (groupid != 0)
					xajax_setCampaign(groupid);
			}

		//-->
		</SCRIPT>
		<script language="JavaScript" src="js/astercrm.js"></script>

	<LINK href="skin/default/css/dragresize.css" type=text/css rel=stylesheet>
	<LINK href="skin/default/css/style.css" type=text/css rel=stylesheet>

	</head>
	<body onload="init();">
		<div id="divNav"></div>
		<br><br><br><br>
		<div id="divAMIStatus" name="divAMIStatus"></div>

		<div id="divGroup" name="divGroup">
			<SELECT id="groupid" name="groupid" onchange="setCampaign();showPredictiveDialer();">
			</SELECT>
			<SELECT id="campaignid" name="campaignid" onchange="showPredictiveDialer();">
			</SELECT>
		</div>
		<span id="spanTotalRecords" name="spanTotalRecords" align="left"></span><!--&nbsp;&nbsp;records left-->
		<div id="divPredictiveDialerMsg" name="divPredictiveDialerMsg" align="left"> </div>
	
		<div id="divPredictiveDialer" name="divPredictiveDialer" align="left" style="display:none;">
			<div id="divActiveCalls" name="divActiveCalls" align="left"> </div>
			<input type="button" value="Dial" id="btnDial" name="btnDial" onClick="btnDialOnClick();">&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="text" size="10" value="5" maxlength="3" id="fldMaxActiveCalls" name="fldMaxActiveCalls">

			<input type="hidden" value="Dial" id="btnDialMsg" name="btnDialMsg">
			<input type="hidden" value="Stop" id="btnStopMsg" name="btnStopMsg">
			<input type="hidden" value="Number Only" id="btnNumberOnlyMsg" name="btnNumberOnlyMsg">
			<input type="hidden" value="Dialer Stopped" id="btnDialerStoppedMsg" name="btnDialerStoppedMsg">
		</div>

		<div id="channels" name="channels" align="left"> </div><br><br>

		<input type="hidden" value="" id="msgChannelsInfo" name="msgChannelsInfo">
		<input type="hidden" value="idle" id="predictiveDialerStatus" name="predictiveDialerStatus">
		<div id="divCopyright"></div>
	</body>
</html>
