<?php
header("content-type:text/html;charset=utf-8");

include_once('config.php');

$mime = explode(",", UPLOAD_IMAGE_MIME);
$is_vaild = 0;
$row = 0;
if(isset($_POST['CHECK']) && trim($_POST['CHECK']) == '1'){
	$upload_msg = $_FILES['image']['type'];

	foreach ($mime as $type){
		if($_FILES['image']['type'] == $type){
			$is_vaild = 1;
			//break;
		}
	}

	//if ($is_vaild && $_FILES['image']['size']<=UPLOAD_IMAGE_SIZE && $_FILES['image']['size']>0)
	//{
		if (move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_IMAGE_PATH . $_FILES['image']['name'])) 
		{
			$upload_msg ="文件".$_FILES['image']['name']."上传成功！<br />";
			while($data = fgetcsv(UPLOAD_IMAGE_PATH . $_FILES['image']['name'], 1000, ",")){
				$row = $row + 1;
				$_SESSION['row'] = $row;
			}
			//$upload_msg .= " <font color='red'>共有".$_SESSION['row']."条记录，显示8条记录</font>";
			$upload_msg .= " <font color='red'>只显示8条记录</font>";
			$_SESSION['filename'] = $_FILES['image']['name'];  //新传的文件名做为session
			//$upload_msg =$_SESSION['filename'];
		} 
		else 
		{
			$upload_msg = "上传文件失败";
		}
}
/*}
else
{
	$upload_msg = "上传文件失败，可能是文件超过". UPLOAD_IMAGE_SIZE_KB ."KB、或者文件文件为空、或文件格式不正确";
}*/

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