<?php
	session_start();
	require_once ('include/Localization.php');
	$GLOBALS['locate']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'csv');
	$file_name = $locate->Translate("file_name");
	if ($file_name != mb_convert_encoding($file_name,"UTF-8","UTF-8"))
			$file_name=mb_convert_encoding($file_name,"UTF-8","GB2312");
	$upload = $locate->Translate("upload");
	if ($upload != mb_convert_encoding($upload,"UTF-8","UTF-8"))
			$upload=mb_convert_encoding($upload,"UTF-8","GB2312");
	$filemanager = $locate->Translate("filemanager");
	if ($filemanager != mb_convert_encoding($filemanager,"UTF-8","UTF-8"))
			$filemanager=mb_convert_encoding($filemanager,"UTF-8","GB2312");
	$back = $locate->Translate("back");
	if ($back != mb_convert_encoding($back,"UTF-8","UTF-8"))
			$back=mb_convert_encoding($back,"UTF-8","GB2312");
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
	function btnonclick(){
		if(document.getElementsByName('myCheckBox')[0].checked == true) 
		{ 
			document.getElementById('mytext').value = "";
			document.getElementById('mytext').disabled = false;
			document.getElementById('mytext').style.border = "1px double #000000";
			document.getElementById('mytext').focus(); 
			document.getElementsByName('myCheckBox2')[0].disabled = false;
		} 
		else 
		{ 
			document.getElementById('mytext').value = "";
			document.getElementById('mytext').disabled = true;
			document.getElementById('mytext').style.border = "1px double #cccccc";
			document.getElementsByName('myCheckBox2')[0].disabled = true;
			document.getElementsByName('myCheckBox2')[0].checked = false;
			document.getElementById('mytext2').value = "";
			document.getElementById('mytext2').disabled = true;
			document.getElementById('mytext2').style.border = "1px double #cccccc";
		}
	}
	function btnonclick2(){
		if(document.getElementsByName('myCheckBox2')[0].checked == true) 
		{ 
			document.getElementById('mytext2').value = "";
			document.getElementById('mytext2').disabled = false;
			document.getElementById('mytext2').style.border = "1px double #000000";
			document.getElementById('mytext2').focus(); 
		} 
		else 
		{ 
			document.getElementById('mytext2').value = "";
			document.getElementById('mytext2').disabled = true;
			document.getElementById('mytext2').style.border = "1px double #cccccc";
		}
	}
</script>
</head>
<body onload='iframe1.window.location="show_image_con.php"'>

<!--<div id="header"></div>
<div id="des">
</div>-->

<div id="mainform">
<form action="upload_con.php" method="post" enctype="multipart/form-data" name="upload_img" target="iframe1">
<input type="hidden" name="MAX_FILE_SIZE" value="300000" />
<input type="hidden" name="CHECK" value="1" />
<?echo $file_name; ?> ：<input type="file" name="image" ><br />
<input type="submit" value=" <?echo $upload ;?> ">
</form>
</div>

<div id="message" style="display:none" onclick="this.style.display='none'"></div>

<table id="maintable">
	<tr>
		<td colspan="2" id="title"><?echo $filemanager;?> &nbsp;&nbsp;<a href="./contact.php" color='red'><?echo $back;?></a></td>
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