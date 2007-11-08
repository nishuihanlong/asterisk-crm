<?php
/*******************************************************************************
* import.php
* 上传、导出数据界面
* upload / import management interface
* 功能描述
	 提供上传 导出数据的功能

* Function Desc
	csv,xls file upload and import management
* Page elements
* div:							
									mainform			-> uploade excel file
									divMessage			-> show upload message
									divShowExcel		-> show uploade excel file
									mainDiv
									divShowTable
									divMainRight
									divSubmitForm
* javascript function:		
									init
									selectTable
									chkAddOnClick
									chkAssignOnClick
									confirmMsg
									showDivMainRight


* Revision 0.0456  2007/11/6 14:17:00  modified by solo
* Desc: modified function uploadFile

* Revision 0.045  2007/10/22 13:02:00  modified by yunshida
* Desc: modified some element id
		upload -> btnUpload
		upload_excel -> formUpload

* Revision 0.045  2007/10/22 11:35:00  modified by yunshida
* Desc: create page
* 描述: 建立
********************************************************************************/
	require_once('import.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<?php $xajax->printJavascript('include/'); ?>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<script language='javascript'>
			function init(){
				xajax_init();
			}

			function selectTable(tablename){
				if(tablename != ''){
					xajax_selectTable(tablename);
				}else{
					init();
				}
			}

			function chkAddOnClick(){
				if(document.getElementsByName('chkAdd')[0].checked == true) 
				{ 
					document.getElementById('dialListField').value = "";
					document.getElementById('dialListField').disabled = false;
					document.getElementById('dialListField').style.border = "1px double #000000";
					document.getElementById('dialListField').focus(); 
					document.getElementsByName('chkAssign')[0].disabled = false;
				} 
				else 
				{ 
					document.getElementById('dialListField').value = "";
					document.getElementById('dialListField').disabled = true;
					document.getElementById('dialListField').style.border = "1px double #cccccc";
					document.getElementsByName('chkAssign')[0].disabled = true;
					document.getElementsByName('chkAssign')[0].checked = false;
					document.getElementById('assign').value = "";
					document.getElementById('assign').disabled = true;
					document.getElementById('assign').style.border = "1px double #cccccc";
				}
			}
			function chkAssignOnClick(){
				if(document.getElementsByName('chkAssign')[0].checked == true) 
				{ 
					document.getElementById('assign').value = "";
					document.getElementById('assign').disabled = false;
					document.getElementById('assign').style.border = "1px double #000000";
					document.getElementById('assign').focus(); 
				} 
				else 
				{ 
					document.getElementById('assign').value = "";
					document.getElementById('assign').disabled = true;
					document.getElementById('assign').style.border = "1px double #cccccc";
				}
			}

			function submitFormOnSubmit(){
				xajax.$('submitButton').disabled=true;
				xajax.$('submitButton').value=xajax.$('onsubmitMsg').value;
				if(document.getElementsByName('chkAdd')[0].checked == true){
					if(document.getElementsByName('chkAssign')[0].checked == true){
						if(document.getElementById('assign').value == "")
						{
							alert(document.getElementById('hidAssignAlertMsg').value);
						}
					}
				}
				xajax_submitForm(xajax.getFormValues('formImport'));
			}

			function showDivMainRight(filename){
				xajax_showDivMainRight(filename);
			}
			
			function uploadFile()
			{
				if (document.getElementById('excel').value == '')
					return false;

				xajax.$('btnUpload').disabled = true;
				xajax.$('btnUpload').value=xajax.$('hidOnUploadMsg').value;
				formUpload.submit();
				return false;
			}
		

		</script>
		<script language="JavaScript" src="js/astercrm.js"></script>

	<LINK href="skin/default/css/dragresize.css" type=text/css rel=stylesheet>
	<LINK href="skin/default/css/style.css" type=text/css rel=stylesheet>

	</head>
	<body onload="init();">
		<div id="divNav"></div>
		<center>
			<div id="mainform">
				<form action="upload.php" method="post" enctype="multipart/form-data" name="formUpload" target="iframeShowExcel" onsubmit="uploadFile();return false;">
					<input type="hidden" name="CHECK" value="1" />
					<span id="divFileName"></span>:<input type="file" name="excel" id="excel"/>
					<br />
					<input type="submit" value="" id="btnUpload" name="btnUpload" style="width:150px;"/>
					<input id="hidOnUploadMsg" name="hidOnUploadMsg" type="hidden" value=""/>
					<input id="hidAssignAlertMsg" type="hidden" value=""/>
					
				</form>
			</div>

			<div id="divMessage"></div>

			<table id="maintable">
				<tr>
					<td colspan="2" id="title" align='center'>
						<span id="spanFileManager"></span>
					</td>
				</tr>
			</table>

			<br>
			<table id="mainDiv" name="mainDiv">
				<tr>
					<td width="20%" valign="top">
						<div id="divShowTable" name="divShowTable"></div>
					</td>
					<td width="80%" valign="top">
						<form method='post' name='formImport' id='formImport' action="javascript:void(null);" onsubmit='submitFormOnSubmit();'>
						<input id="onsubmitMsg" name="onsubmitMsg" type="hidden" value=""/>
							<div name="divShowExcel" id="divShowExcel"></div>
							<div name="divSubmitForm" id="divSubmitForm"></div>
							<input type='hidden' value='' name='FILE_NAME' id='FILE_NAME' />
							<input type='hidden' value='' name='TABLE_NAME' id='TABLE_NAME' />
							<input type='hidden' value='' name='MAX_NUM' id='MAX_NUM' />
						</form>
					</td>
				</tr>
			</table>

			<!--
				use a hidden iframe to handle upload
			-->
			<iframe name="iframeShowExcel" id="iframeShowExcel" width="0" height="0" scrolling="no"></iframe>
			
		</center>
		<br />
		<br />
		<br />
		<p><div id="divCopyright"></div></p>
	</body>
</html>