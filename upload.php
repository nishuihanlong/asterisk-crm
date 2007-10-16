<?php
header("content-type:text/html;charset=utf-8");
session_start();
require_once ('include/Localization.php');
include_once('config.php');
$GLOBALS['locate']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'csv');
if(isset($_POST['CHECK']) && trim($_POST['CHECK']) == '1'){
	$upload_msg = '';
	$upload_type = $_FILES['image']['type'];
	$is_vaild = 0;
	if ( "application/vnd.ms-excel" == $upload_type)
	{
		if (move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_IMAGE_PATH . $_FILES['image']['name'])) 
		{
			$file = $locate->Translate('file');
			if ($file != mb_convert_encoding($file,"UTF-8","UTF-8"))
				$file=mb_convert_encoding($file,"UTF-8","GB2312");
			$uploadsuccess = $locate->Translate('uploadsuccess');
			if ($uploadsuccess != mb_convert_encoding($uploadsuccess,"UTF-8","UTF-8"))
				$uploadsuccess=mb_convert_encoding($uploadsuccess,"UTF-8","GB2312");
			$upload_msg =$file.$_FILES['image']['name'].$uploadsuccess.$upload_type."！<br />";
			$handleup = fopen(UPLOAD_IMAGE_PATH . $_FILES['image']['name'],"r");
			$row = 0;
			while($data = fgetcsv($handleup, 1000, ",")){
			   $row++;
			}
			$have = $locate->Translate('have');
			if ($have != mb_convert_encoding($have,"UTF-8","UTF-8"))
				$have=mb_convert_encoding($have,"UTF-8","GB2312");
			$default = $locate->Translate('default');
			if ($default != mb_convert_encoding($default,"UTF-8","UTF-8"))
				$default=mb_convert_encoding($default,"UTF-8","GB2312");
			$upload_msg .= " <font color='red'>".$have.$row.$default."</font>";
			//$upload_msg .= '<br />';
			//$upload_msg .= 'vv'.$data;
			$_SESSION['filename'] = $_FILES['image']['name'];  //新传的文件名做为session
			//$upload_msg =$_SESSION['filename'];
		} 
		else 
		{
			$failed = $locate->Translate('failed');
			if ($failed != mb_convert_encoding($failed,"UTF-8","UTF-8"))
				$failed=mb_convert_encoding($failed,"UTF-8","GB2312");
			$upload_msg = $failed;  //失败提示
		}
	}else{
		$cantup = $locate->Translate('cantup');
		if ($cantup != mb_convert_encoding($cantup,"UTF-8","UTF-8"))
			$cantup=mb_convert_encoding($cantup,"UTF-8","GB2312");
		$upload_msg .= $cantup;  //失败提示
	}
}
else
{
	$upload_msg = "failed";
}

if($upload_msg != "")
	$upload_js_function="callbackMessage(\"$upload_msg\");";
else
	$upload_js_function="";


include("template.php");

$t=new Template('./template/');
$t->caching = false;
//$t->unknowns = "keep";
$t->left_delimiter = "[##";
$t->right_delimiter = "##]";

$t->set_file("upload", "upload.tpl");
$t->set_var(array("upload_js_function"=> $upload_js_function));
$t->parse("uploadout","upload");
$t->p("uploadout");
?>