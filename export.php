<?php
/*******************************************************************************
* export.php
* export datas

* Function Desc


* div:							

* button
* form
				frmDownload			post csv type to download.php
					@type

* javascript function:		

				init				page onload function			 
				exportCustomer		call export script
				exportContact		call export script
				exportNote		call export script

* Revision 0.045  2007/10/22 16:30:00  modified by solo
* Desc: comment added

********************************************************************************/

require_once('export.common.php');
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
		}

		function exportCustomer(){
			xajax.$('hidType').value = 'customer';
			xajax_export();
		}

		function exportContact(){
			xajax.$('hidType').value = 'contact';
			xajax_export();
		}

		function exportNote(){
			xajax.$('hidType').value = 'note';
			xajax_export();
		}

		//-->
		</SCRIPT>

		<script language="JavaScript" src="js/astercrm.js"></script>
		<LINK href="skin/default/css/style.css" type=text/css rel=stylesheet>

	</head>
	<body onload="init();">
	<div id="divNav"></div>
	<br>
	<div id="divActive" name="divActive">
		<input type="button" value="customer" id="btnCustomer" name="btnCustomer" onClick="exportCustomer();" />
		<input type="button" value="contact" id="btnContact" name="btnContact" onClick="exportContact();" />
		<input type="button" value="note" id="btnNote" name="btnNote" onClick="exportNote();" />
	</div>
	<form name="frmDownload" id="frmDownload" action="download.php">
		<input type="hidden" value="" id="hidType" name="hidType">
	</form>
	<div id="divCopyright"></div>
	</body>
</html>