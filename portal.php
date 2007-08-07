<?php
require_once('portal.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html;charset=utf-8">
		<?php $xajax->printJavascript('include/'); ?>
		<script type="text/javascript">
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
	<body onload="updateEvents();">
	Welcome <span id="username"><?echo $_SESSION['curuser']['username'];?></span>, your extension is <span id="extension"><?echo $_SESSION['curuser']['extension'];?></span>
	<form name="myForm" id="myForm">

		<div id="formWrapper">
		</div>
		
		<div id="myevents">waiting</div>
		<div id="status">listening</div>
		<input type="hidden" name="uniqueid" id="uniqueid" value=""/>
		<input type="hidden" name="callerid" id="callerid" value=""/>
		<input type="hidden" name="curid" id="curid" value="0"/>
		<input type="hidden" name="extension" id="extension" value=""/>
		<div id="debug"></div>
	</form>

<br><br><br><br><br><br>
<br><br><br><br><br><br>
<br><br><br><br><br><br>


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

	</body>
</html>