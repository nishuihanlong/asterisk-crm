<?php
/*******************************************************************************
* customer.php
* excel文件管理界面//
* excel file management interface
* 功能描述
	 提供excel文件信息管理的功能

* Function Desc
	csv,xls management
* Page elements
* div:							
									mainform			-> uploade excel file
									divMessage			-> show upload message
									divShowExcel		-> show uploade excel file
* javascript function:		
									init
									selectTable
									chkAddOnClick
									chkAssignOnClick
									confirmMsg


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
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		<script language='javascript'>
			function init(){
				xajax_init();
			}

			function selectTable(tablename){
				if(tablename != ''){
					xajax_selectTable(tablename);
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
				if(document.getElementById('assign').value == "")
				{
					alert(document.getElementById('alertmsg').value);
				}
				xajax_submitForm(xajax.getFormValues('formImport'));
			}

		</script>
	</head>
	<body onload="init();">
		<center>
			<div id="mainform">
				<form action="upload.php" method="post" enctype="multipart/form-data" name="upload_excel" target="iframeShowExcel">
					<input type="hidden" name="MAX_FILE_SIZE" value="300000" />
					<input type="hidden" name="CHECK" value="1" />
					<span id="divFileName"></span>:<input type="file" name="excel"><br />
					<input type="submit" value="" id="upload" name="upload" />
					<input type="hidden" value="" id="alertmsg" />
				</form>
			</div>

			<div id="divMessage"></div>

			<table id="maintable">
				<tr>
					<td colspan="2" id="title" align='center'><span id="spanFileManager"></span></td>
				</tr>
			</table>

			<br>
			<iframe name="iframeShowExcel" id="iframeShowExcel" width="0" height="0" scrolling="no"></iframe>
			<div name="divShowExcel" id="divShowExcel"></div>
		</center>
	</body>
</html>