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
				exportForm          记录导出数据的sql语句

* button
				btnImport
				btnExport

* javascript function:		

				init				page onload function			 

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

		function ckbAllOnClick(objCkb){
			var ockb = document.getElementsByName('ckb[]');
			for(i=0;i<ockb.length;i++) {				
				if (ockb[i].checked != objCkb.checked){
					ockb[i].checked = objCkb.checked;
				}
			}			
		}

		function setCampaign(){
			groupid = document.getElementById("groupid").value;
			//if (groupid == '')
			//	return;
			//清空campaignid
			document.getElementById("campaignid").options.length=0
			xajax_setCampaign(groupid);
		}

		function dial(phonenum,first,myvalue,dtmf){
			myFormValue = xajax.getFormValues("myForm");
			dialnum = phonenum;
			firststr = first;

			if(typeof(first) != 'undefined'){
				firststr = first;
			}else{
				firststr = '';
			}
	
			if(typeof(dtmf) != 'undefined'){
				dtmfstr = dtmf;
			}else{
				dtmfstr = '';
			}
			
			xajax_dial(dialnum,firststr,myFormValue,dtmfstr);
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
	<br><br>
	<div id="divActive" name="divActive">
		<input type="button" value="" id="btnContact" name="btnContact" onClick="window.location='contact.php';" />
		<input type="button" value="" id="btnNote" name="btnNote" onClick="window.location='note.php';" />
	</div>
	<table width="100%" border="0" style="background: #F9F9F9; padding: 0px;">
		<tr>
			<td style="padding: 0px;">
				<fieldset>
		<div id="surveyDiv"  class="formDiv drsElement" 
			style="left: 20px; top: 20px;width:700px;"></div>			
		<div id="formDiv"  class="formDiv drsElement" 
			style="left: 450px; top: 50px;width:500px;"></div>
		<div id="formCustomerInfo" class="formDiv drsElement"
			style="left: 20px; top: 50px; width: 550px"></div>
		<div id="formContactInfo" class="formDiv drsElement"
			style="left: 20px; top: 330px;width: 600px"></div>
		<div id="formCdr" class="formDiv drsElement"
			style="left: 20px; top: 330px; width: 850px"></div>
		<div id="formDiallist" class="formDiv drsElement"
			style="left: 20px; top: 330px; width: 800px"></div>
		<div id="formRecords" class="formDiv drsElement"
			style="left: 20px; top: 330px; width: 800px"></div>
		<div id="formNoteInfo" class="formDiv  drsElement"
			style="left: 450px; top: 330px;"></div>
		<div id="formEditInfo" class="formDiv drsElement"
			style="left: 450px; top: 50px;width: 500px"></div>
		<div id="grid" align="center"></div>
		<div id="msgZone" name="msgZone" align="left"> </div>
		<div id="formDiallist" class="formDiv drsElement"
			style="left: 20px; top: 330px; width: 800px"></div>
		<div id="formaddDiallistInfo"  class="formDiv drsElement" 
			style="left: 450px; top: 50px;width: 500px"></div>
		<div id="formeditDiallistInfo"  class="formDiv drsElement" 
			style="left: 450px; top: 50px;width: 500px"></div>
				</fieldset>
			</td>
		</tr>
	</table>
	<form name="exportForm" id="exportForm" action="dataexport.php" >
		<input type="hidden" value="" id="hidSql" name="hidSql" />
	</form>
	<div id="divCopyright"></div>
	</body>
</html>