<?php
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
			xajax.loadingFunction = showProcessingMessage;
			xajax.doneLoadingFunction = hideProcessingMessage;
		}

		function dial(phonenum){
//			alert (phonenum);
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
		
		</script>

	<LINK href="css/style.css" type=text/css rel=stylesheet>
	<meta http-equiv="Content-Language" content="utf-8" />
	</head>
	<body onload="init();">
	<form name="myForm" id="myForm">
		<div id="userMsg" name="userMsg"></div>
		<span id="transfer" name="transfer"></span>
		<div id="formWrapper"></div>
		
		<div id="myevents"></div>
		<div id="status"></div>
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

	<div id="divDialList" name="divDialList"></div>
	<div id="processingMessage" name="processingMessage"></div>
	
	<div id="crm" name="crm"></div>
	<div id="panelDiv" name="panelDiv" class="divPanel"></div>
	<div id="extensionDiv" name="extensionDiv" class="divExtension"></div>
	</body>
</html>
