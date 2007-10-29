<?php
/*******************************************************************************
* customer.php
* customer information management interface

* Function Desc
	customer management

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


* Revision 0.045  2007/10/18 14:07:00  modified by solo
* Desc: comment added

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
		<meta http-equiv="Content-Language" content="utf-8" />
		<SCRIPT LANGUAGE="JavaScript">
		<!--
		function init(){
			xajax_init();
			//make div draggable
			dragresize.apply(document);
		}

		function exportCustomer(){
			xajax_export();
		}

		function importCsv(){
			xajax_importCsv();
		}

		function addSearth()
		{
			alert('vvvvvvvvvvvv');
			oldidvalue =frmDownload.oldid.value;
			oldidvalue++;
			str=document.getElementsByName('addSearth')[0].innerHTML;
			str = str.replace('<TBODY>',' ');
			str = str.replace('</TBODY>',' ');
			alert(str);
			i=frmDownload.oldid.value;
			for (i;i<oldidvalue;i++)
			{
				str=str+"<tr><td>searth: &nbsp;<input type='text' size='30' id=searchContent"+i+ "    name=searchContent"+i+">&nbsp;&nbsp;searthby &nbsp;"+
					"<select id=searchField"+i+" name=searchField"+i+">"+
						"<option value=''> </option>"+
						"<option value='customer'>customer name</option>"+
						"<option value='state'>shengfen</option>"+
						"<option value='city'>city</option>"+
						"<option value='phone'>phone</option>"+
						"<option value='contact'>contact</option>"+
						"<option value='website'>www</option>"+
						"<option value='category'>type</option>"+
						"<option value='cretime'>create time</option>"+
						"<option value='creby'>creater</option>"+
					"</select></td></tr>";
			}
			alert(str);
			frmDownload.oldid.value = oldidvalue;
			document.getElementsByName('addSearth')[0].innerHTML = str;
		}
		//-->
		</SCRIPT>

		<script type="text/javascript" src="js/astercrm.js"></script>
		<script type="text/javascript" src="js/dragresize.js"></script>
		<script type="text/javascript" src="js/dragresizeInit.js"></script>

		<script type="text/javascript" src="js/ajax.js"></script>
		<script type="text/javascript" src="js/ajax-dynamic-list.js"></script>

	<LINK href="skin/default/css/dragresize.css" type=text/css rel=stylesheet>
	<LINK href="skin/default/css/style.css" type=text/css rel=stylesheet>

	</head>
	<body onload="init();">
	<div id="divNav"></div>
	<br>
	<div id="divActive" name="divActive">
		<input type="button" value="" id="btnContact" name="btnContact" onClick="window.location='contact.php';" />
		<input type="button" value="" id="btnNote" name="btnNote" onClick="window.location='note.php';" />
	</div>
	<table width="100%" border="0" style="background: #F9F9F9; padding: 0px;">
		<tr>
			<td style="padding: 0px;">
				<fieldset>
		<div id="formDiv"  class="formDiv drsElement" 
			style="left: 450px; top: 50px;"></div>
		<div id="formCustomerInfo" class="formDiv drsElement"
			style="left: 20px; top: 50px;"></div>
		<div id="formContactInfo" class="formDiv drsElement"
			style="left: 20px; top: 330px;"></div>
		<div id="formNoteInfo" class="formDiv  drsElement"
			style="left: 450px; top: 330px;"></div>
		<div id="formEditInfo" class="formDiv drsElement"
			style="left: 450px; top: 50px;"></div>
		<div id="grid" align="center"></div>
		<div id="msgZone" name="msgZone" align="left"> </div>
				</fieldset>
			</td>
		</tr>
	</table>
	<form name="frmDownload" id="frmDownload" action="download.php">
		<input type="hidden" value="" id="type" name="type">
		<!--*********************-->
		<input type="hidden" value="" id="search" name="search" />
		<input type="hidden" value="" id="by" name="by" />
		<INPUT TYPE="hidden" NAME="oldid" value="1"> <!--基数1-->
		<!--*********************-->
	</form>
	<div id="divCopyright"></div>
	</body>
</html>