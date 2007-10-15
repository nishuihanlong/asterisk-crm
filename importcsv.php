<?php
	session_start();
	require_once ('include/Localization.php');
	$GLOBALS['locate']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'csv');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="all" />
<title></title>
<meta name="keywords" content="" />
<meta name="description" content="Power By Phzzy.org" />
<meta name="author" content="Phzzy" />
<link rel="stylesheet" type="text/css" href="template/style.css" />
<script language='javascript'>
	function ddd(){
		if(document.getElementById('myCheckBox').checked == true) 
		{ 
			document.getElementById('mytext').value = "";
			document.getElementById('mytext').disabled = false;
		} 
		else 
		{ 
			document.getElementById('mytext').value = "";
			document.getElementById('mytext').disabled = true;
		}
	}
</script>
</head>
<body onload='iframe1.window.location="show_image.php";'>

<!--<div id="header"></div>
<div id="des">
</div>-->


<div id="mainform">
<form action="upload.php" method="post" enctype="multipart/form-data" name="upload_img" target="iframe1">
<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
<input type="hidden" name="CHECK" value="1" />
<?echo $locate->Translate("file_name");?>：<input type="file" name="image"><br />
<input type="submit" value=" <?echo $locate->Translate("upload");?> ">
</form>
</div>

<div id="message" style="display:none" onclick="this.style.display='none'"></div>

<table id="maintable">
	<tr>
		<td colspan="2" id="title"><?echo $locate->Translate("filemanager");?> &nbsp;&nbsp;<a href="./customer.php" color='red'><?echo $locate->Translate("back");?></a></td>
	</tr>
	<!--<tr>
		<td>
				<form method="post" name="delimage" action="del.php" target="iframe1">
					<a href="#" onclick='javascript:document.delimage.submit();'>[删除所有文件]</a>
				</form>
		</td>
		<td>
				<form method="post" name="showimage" action="show_image.php" target="iframe1">
					<a href="#" onclick="javascript:document.showimage.submit();">[刷新所有文件]</a>
				</form>
		</td>
	</tr>-->
	<tr>
		<td colspan="2"><div id="show_image"></div></td>
	</tr>
</table>

<br>
<iframe name="iframe1" width="0" height="0" scrolling="no"></iframe>
</body>
</html>