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
			var timerShowStatus,timerShowChannelsInfo,timerPredictiveDialer;
			function showStatus(){
				xajax_showStatus();
				timerShowStatus = setTimeout("showStatus()", 1000);
			}

			function showAccounts(){
				xajax_showGrid(0,<?=ROWSXPAGE?>,'','','');
			}

			function showChannelsInfo(){
				xajax_showChannelsInfo();
				timerShowChannelsInfo = setTimeout("showChannelsInfo()", 1000);
			}

			function init(){
				xajax_init();
				//clearTimeout(timerShowStatus);
				//clearTimeout(timerShowChannelsInfo);
			}

			function showPredictiveDialer(){
				xajax_showPredictiveDialer(xajax.$('predictiveDialerStatus').value);
				//xajax_preDialer();
			}

			function clearAll(){
				clearTimeout(timerShowStatus);											
				clearTimeout(timerShowChannelsInfo);
				clearTimeout(timerPredictiveDialer);

				xajax.$('formDiv').innerHTML = '';
				xajax.$('grid').innerHTML = '';
				xajax.$('msgZone').innerHTML = '';
				xajax.$('divPredictiveDialer').innerHTML = '';
				xajax.$('channels').innerHTML = '';
				xajax.$('sipChannels').innerHTML = '';
				xajax.$('divPredictiveDialerMsg').innerHTML = '';
				xajax.$('divActiveCalls').innerHTML = '';
				xajax.$('spanTotalRecords').innerHTML = '';
			}

			function btnDialOnClick(){
				if (xajax.$('predictiveDialerStatus').value == 'idle'){
					showChannelsInfo();
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

				xajax_predictiveDialer(maxActiveCalls,totalRecords);
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
		//-->
		</SCRIPT>
	</head>
	<body onload="init();">
		<div id="AMIStatudDiv" name="AMIStatudDiv"></div>
		<div id="panelDiv" name="panelDiv" class="divPanel"></div>
		
		<div id="formDiv" name="formDiv" class="formDiv"></div>

		<div id="grid" name="grid" align="center"> </div>
		<div id="msgZone" name="msgZone" align="left"> </div>
		<span id="spanTotalRecords" name="spanTotalRecords" align="left"></span><!--&nbsp;&nbsp;records left-->
		<div id="divActiveCalls" name="divActiveCalls" align="left"> </div>
		<div id="divPredictiveDialerMsg" name="divPredictiveDialerMsg" align="left"> </div>
		<div id="divPredictiveDialer" name="divPredictiveDialer" align="left"></div>
		<div id="channels" name="channels" align="left"> </div><br><br>
		<div id="sipChannels" name="sipChannels" align="left"></div>
		<input type="hidden" value="" id="msgChannelsInfo" name="msgChannelsInfo">
		<input type="hidden" value="idle" id="predictiveDialerStatus" name="predictiveDialerStatus">
	</body>
</html>
