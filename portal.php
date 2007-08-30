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

		function showProcessingMessage(){
			xajax.$('processingMessage').style.display='block';
		}
		function hideProcessingMessage(){
			xajax.$('processingMessage').style.display = 'none';
		}


		function updateEvents(){
//			alert (document.getElementById('myevents').innerHTML);
//			xajax_myEvents(document.getElementById('myevents').innerHTML);
			xajax_listenCalls(xajax.getFormValues("myForm"));
//			xajax_myEvents();
			setTimeout("updateEvents()", 1000);
		}
		
		function btnConfirmCustomerOnClick(){
			if (xajax.$('btnConfirmCustomer').value == 'Cancel')
			{
				xajax_add(xajax.$('callerid').value);
			}else{
				xajax_confirmCustomer(xajax.$('customer').value,xajax.$('callerid').value);
			}
		}

		function openWindow(url){
			window.open(url);
		}

		function btnConfirmContactOnClick(){
			if (xajax.$('customerid').value == '')
				return false;
			if (xajax.$('btnConfirmContact').value == 'Cancel')
			{
				xajax_add(xajax.$('callerid').value,xajax.$('customerid').value);
			}else{
				xajax_confirmContact(xajax.$('contact').value,xajax.$('customerid').value,xajax.$('callerid').value);
			}
		}
		</script>

	<LINK href="css/style.css" type=text/css rel=stylesheet>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/ajax-dynamic-list.js"></script>
	<meta http-equiv="Content-Language" content="utf-8" />
	</head>
	<body onload="init();">
	<form name="myForm" id="myForm">
		<div id="userMsg" name="userMsg">
		</div>
		<span id="transfer" name="transfer"></span>
		<div id="formWrapper">
		</div>
		
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
		<div id="debug"></div>
	</form>

<br><br><br><br><br><br>
<br><br><br><br><br><br>
<br><br><br><br><br><br>
<div id="processingMessage" name="processingMessage"></div>

		<table width="95%" border="0" style="background: #F9F9F9; padding: 0px;">
			<tr>
				<td style="padding: 0px;">
					<fieldset>
					<div id="formDiv" class="formDiv"></div>
					<div id="formCustomerInfo" class="formCustomerInfo"></div>
					<div id="formContactInfo" class="formContactInfo"></div>
					<div id="formNoteInfo" class="formNoteInfo"></div>
					<div id="formEditInfo" class="formEditInfo"></div>
					<div id="grid" align="center"> </div>
					<script type="text/javascript">
						xajax_showGrid(0,<?=ROWSXPAGE?>,'','','');
					</script>
					</fieldset>
				</td>
			</tr>
		</table>
		<div id="panelDiv" name="panelDiv" class="divPanel"></div>
	</body>
</html>