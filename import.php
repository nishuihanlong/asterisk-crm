<?php
/*******************************************************************************
* import.php
* 导入excel表格数据界面//
* excel data
* 功能描述
	 导入excel表格数据

* 

* 
* 
						


* 
* 
* 
********************************************************************************/
	require_once('import.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php $xajax->printJavascript('include/'); ?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="template/style.css" />
<script language='javascript'>
	function init(){
			xajax_init();
			iframeShowExcel.window.location="show_excel.php";
	}

	function selectTable(tablename){
		if(tablename == ''){
			//alert(tablename);
		}else{
			xajax_selectTable(tablename);
		}
	}

	function btnOnClick(){
		if(document.getElementsByName('myCheckBox')[0].checked == true) 
		{ 
			document.getElementById('dialListField').value = "";
			document.getElementById('dialListField').disabled = false;
			document.getElementById('dialListField').style.border = "1px double #000000";
			document.getElementById('dialListField').focus(); 
			document.getElementsByName('myCheckBox2')[0].disabled = false;
		} 
		else 
		{ 
			document.getElementById('dialListField').value = "";
			document.getElementById('dialListField').disabled = true;
			document.getElementById('dialListField').style.border = "1px double #cccccc";
			document.getElementsByName('myCheckBox2')[0].disabled = true;
			document.getElementsByName('myCheckBox2')[0].checked = false;
			document.getElementById('assign').value = "";
			document.getElementById('assign').disabled = true;
			document.getElementById('assign').style.border = "1px double #cccccc";
		}
	}
	function btnOnClick2(){
		if(document.getElementsByName('myCheckBox2')[0].checked == true) 
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

	function confirmMsg(){
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
<form action="upload.php" method="post" enctype="multipart/form-data" name="upload_excel" target="iframeShowExcel"  >
<input type="hidden" name="MAX_FILE_SIZE" value="300000" />
<input type="hidden" name="CHECK" value="1" />
<span id="file_name"></span>:<input type="file" name="excel"><br />
<input type="submit" value="" id="upload" name="upload" />
<input type="hidden" value="" id="alertmsg" />
</form>
</div>

<div id="divMessage"></div>

<table id="maintable">
	<tr>
		<td colspan="2" id="title" align='center'><span id="spanFileManager"></span></td>
	</tr>
	<tr>
		<td colspan="2"><div id="show_excel"></div></td>
	</tr>
</table>
<br>
<iframe name="iframeShowExcel" id="iframeShowExcel" width="0" height="0" scrolling="no"></iframe>

</center>
</body>
</html>