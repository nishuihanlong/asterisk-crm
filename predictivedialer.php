<?php
/*******************************************************************************
* predictivedialer.php
* 预拨号器界面文件
* predictivedialer interface

* Function Desc

* Revision 0.045  2007/10/18 17:55:00  last modified by solo
* Desc: page created
********************************************************************************/

require_once('predictivedialer.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<?php $xajax->printJavascript('include/'); ?>
		<LINK href="css/style.css" type=text/css rel=stylesheet>
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
				xajax_showPredictiveDialer(xajax.$('predictiveDialerStatus').value);
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
		<div id="divNav"></div>
		<div id="AMIStatudDiv" name="AMIStatudDiv"></div>
		<span id="spanTotalRecords" name="spanTotalRecords" align="left"></span><!--&nbsp;&nbsp;records left-->
		<div id="divActiveCalls" name="divActiveCalls" align="left"> </div>
		<div id="divPredictiveDialerMsg" name="divPredictiveDialerMsg" align="left"> </div>
		<div id="divPredictiveDialer" name="divPredictiveDialer" align="left"></div>
		<div id="channels" name="channels" align="left"> </div><br><br>
		<input type="hidden" value="" id="msgChannelsInfo" name="msgChannelsInfo">
		<input type="hidden" value="idle" id="predictiveDialerStatus" name="predictiveDialerStatus">
		<div id="divCopyright"></div>
	</body>
</html>
