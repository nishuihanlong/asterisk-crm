<?php
/*******************************************************************************
* contact.php
* contact information management interface

* Function Desc
	contact management

* div:							
				divNav				show management function list
				formDiv				show add contact form
				grid				show contact grid
				msgZone				show action result
				divCopyright		show copyright
				formCustomerInfo	show customer detail
				formContactInfo		show contact detail
				formNoteInfo		show note detail
				divActive			show import and export button

* button
				btnImport
				btnExport
* form
				frmDownload			post csv type to download.php
					@type

* javascript function:		

				init				page onload function			 
				exportCustomer		call export script
				importCsv			call import script

* Revision 0.045  2007/10/18 13:24:00  modified by solo
* Desc: comment added

* Revision 0.045  2007/10/9 12:55:00  modified by solo
* Desc: create page
* 描述: 建立
********************************************************************************/

require_once('contact.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<?php $xajax->printJavascript('include/'); ?>
		<LINK href="css/style.css" type=text/css rel=stylesheet>
		<meta http-equiv="Content-Language" content="utf-8" />
		<SCRIPT LANGUAGE="JavaScript">
		<!--
		function init(){
			xajax_init();
		}

		function exportCustomer(){
			xajax_export();
		}

		function importCsv(){
			xajax_importCsv();
		}
		//-->
		</SCRIPT>
		<script language="JavaScript" src="js/astercrm.js"></script>
	</head>
	<body onload="init();">
	<div id="divNav"></div>
	<br>
	<div id="divActive" name="divActive">
		<input type="button" value="IMPORT" id="btnImport" name="btnImport" onClick="importCsv();" />
		<input type="button" value="EXPORT" id="btnExport" name="btnExport" onClick="exportCustomer();" />
	</div>
	<table width="100%" border="0" style="background: #F9F9F9; padding: 0px;">
		<tr>
			<td style="padding: 0px;">
				<fieldset>
					<div id="formDiv" class="formDiv"></div>
					<div id="formCustomerInfo" class="formCustomerInfo"></div>
					<div id="formContactInfo" class="formContactInfo"></div>
					<div id="formNoteInfo" class="formNoteInfo"></div>
					<div id="formEditInfo" class="formEditInfo"></div>
					<div id="grid" align="center"> </div>
					<div id="msgZone" name="msgZone" align="left"> </div>
				</fieldset>
			</td>
		</tr>
	</table>
	<form name="frmDownload" id="frmDownload" action="download.php">
		<input type="hidden" value="" id="type" name="type">
	</form>
	<div id="divCopyright"></div>
	</body>
</html>