<?php
/*******************************************************************************
* customer.php
* 客户信息管理界面
* cutomer information management interface
* 功能描述
	 提供客户信息管理的功能

* Function Desc
	customer management

* Page elements
* div:							
									formDiv			-> add/edit form div in xgrid
									grid				-> main div
									msgZone		-> message from xgrid class
* javascript function:		
									init	


* Revision 0.0443  2007/09/29 12:55:00  modified by solo
* Desc: create page
* 描述: 建立
********************************************************************************/

require_once('customer.common.php');
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
		//-->
		</SCRIPT>
		<script language="JavaScript" src="js/astercrm.js"></script>
	</head>
	<body onload="init();">
	<div id="divPanel" name="divPanel" class="divPanel"></div>
	<br>
	<div id="divActive" name="divActive">
		<input type="button" value="IMPORT" id="btnImport" name="btnImport">
		<input type="button" value="EXPORT" id="btnExport" name="btnExport" onClick="exportCustomer();">
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
	</body>
</html>